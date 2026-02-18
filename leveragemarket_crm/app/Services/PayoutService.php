<?php

namespace App\Services;

use App\Models\ClientWallets;
use App\Models\PaymentLog;
use App\Models\TotalBalance;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\WalletWithdraw;
use Illuminate\Support\Facades\Http;

class PayoutService
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Create a withdrawal request record in payment_logs (e.g. NowPayment).
     *
     * @param string $email
     * @param float $amount
     * @param string $paymentTo  Destination (wallet address or client_wallet_id)
     * @param string $paymentMethod
     * @return PaymentLog
     */
    public function createWithdrawalRequest(string $email, float $amount, string $paymentTo, string $paymentMethod = 'NowPayment'): PaymentLog
    {
        $paymentLog = PaymentLog::create([
            'payment_amount' => $amount,
            'payment_type' => $paymentMethod,
            'payment_reference_id' => 'Wallet Withdrawal',
            'payment_status' => 'Pending',
            'initiated_by' => $email,
            'payment_to' => $paymentTo,
        ]);
        return $paymentLog;
    }

    /**
     * Approve a withdrawal: for NowPayment call payout API, then create wallet_withdraw, update payment_log, send email.
     *
     * @param int $paymentLogId  payment_logs.payment_id
     * @param bool $approvedByAdmin  If true, withdraw_type = "Wallet Withdrawal (Admin)", else "Wallet Withdrawal"
     * @param string|null $transactionId  Optional reference ID from admin
     * @param string|null $adminRemark  Optional admin description
     * @return WalletWithdraw
     * @throws \Exception When NowPayment payout API fails or required data is missing
     */
    public function approveWithdrawal(int $paymentLogId, bool $approvedByAdmin = false, ?string $transactionId = null, ?string $adminRemark = null): WalletWithdraw
    {
        $paymentLog = PaymentLog::findOrFail($paymentLogId);
        $email = $paymentLog->initiated_by;
        $amount = (float) $paymentLog->payment_amount;
        $paymentTo = $paymentLog->payment_to ?? '';

        $withdrawType = $approvedByAdmin ? 'Wallet Withdrawal (Admin)' : 'Wallet Withdrawal';

        $walletId = '';
        if ($paymentTo && is_numeric($paymentTo)) {
            $walletId = $paymentTo;
        } else {
            $walletId = $paymentTo;
        }

        if ($paymentLog->payment_type === 'NowPayment') {
            $clientWallet = null;
            if ($paymentTo && is_numeric($paymentTo)) {
                $clientWallet = ClientWallets::where('client_wallet_id', $paymentTo)->first();
            }
            if (!$clientWallet || !$clientWallet->wallet_address) {
                throw new \Exception('Wallet address could not be resolved for this withdrawal. Ensure payment_to is a valid client wallet.');
            }
            $currency = $this->mapToNowPaymentsCurrency($clientWallet->wallet_currency ?? '', $clientWallet->wallet_network ?? '');
            if (!$currency) {
                throw new \Exception('Unsupported wallet currency/network for NowPayments. Supported examples: USDT+TRC20, USDT+ERC20.');
            }
            $user = User::where('email', $email)->first();
            $groupId = $user->group_id ?? null;
            if (!$groupId) {
                throw new \Exception('NowPayments is not configured for this user\'s group.');
            }
            $userGroup = UserGroup::find($groupId);
            $apiKey = $userGroup->now_payment_api ?? null;
            if (empty($apiKey)) {
                throw new \Exception('NowPayments API key is not configured for this user\'s group.');
            }
            $payoutResponse = $this->createNowPaymentsPayout($paymentLog, $clientWallet->wallet_address, $currency, $apiKey);
            $paymentLog->update([
                'payment_res' => is_array($payoutResponse) ? json_encode($payoutResponse) : (string) $payoutResponse,
            ]);
        }

        $walletWithdraw = WalletWithdraw::create([
            'email' => $email,
            'withdraw_amount' => $amount,
            'withdraw_type' => $withdrawType,
            'transaction_id' => $transactionId ?? '',
            'Status' => 1,
            'wallet_id' => $walletId,
            'payment_log_id' => $paymentLogId,
            'client_bank' => '',
            'admin_email' => $approvedByAdmin ? (session('alogin') ?? null) : null,
            'AdminRemark' => $adminRemark ?? '',
            'withdraw_date' => now()->format('Y-m-d H:i:s'),
        ]);

        TotalBalance::create([
            'email' => $email,
            'withdraw_amount' => $amount,
        ]);

        $paymentLog->update(['payment_status' => 'Success']);

        $this->sendApprovalEmail($email, $amount, $withdrawType, $walletWithdraw->id);

        return $walletWithdraw;
    }

    /**
     * Map wallet_currency + wallet_network to NowPayments payout currency code.
     */
    protected function mapToNowPaymentsCurrency(string $walletCurrency, string $walletNetwork): ?string
    {
        $currency = strtolower(trim($walletCurrency));
        $network = strtolower(trim(str_replace([' ', '-'], '', $walletNetwork)));
        if (!$currency || !$network) {
            return null;
        }
        return $currency . $network;
    }

    /**
     * Create a payout via NowPayments API (batch format with a single withdrawal).
     *
     * @return array Decoded JSON response (contains id and withdrawals array)
     * @throws \Exception On HTTP or API error
     */
    protected function createNowPaymentsPayout(PaymentLog $paymentLog, string $address, string $currency, string $apiKey): array
    {
        $settings = settings();
        $baseUrl = $settings['copyright_site_name_text'] ?? '';
        $withdrawal = [
            'address' => $address,
            'currency' => $currency,
            'fiat_amount' => (float) $paymentLog->payment_amount,
            'fiat_currency' => 'usd',
            'unique_external_id' => (string) $paymentLog->payment_id,
            'payout_description' => 'Wallet Withdrawal #' . $paymentLog->payment_id,
        ];
        if (!empty($baseUrl) && (str_contains($baseUrl, 'http://') || str_contains($baseUrl, 'https://'))) {
            $callbackUrl = rtrim($baseUrl, '/') . '/payment-response?payout=1&payment_id=' . $paymentLog->payment_id;
            $withdrawal['ipn_callback_url'] = $callbackUrl;
        }
        $payload = [
            'withdrawals' => [$withdrawal],
        ];
        if (!empty($withdrawal['ipn_callback_url'] ?? null)) {
            $payload['ipn_callback_url'] = $withdrawal['ipn_callback_url'];
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => $apiKey,
        ])->post('https://api.nowpayments.io/v1/payout', $payload);

        if (!$response->successful()) {
            $body = $response->json();
            $message = $body['message'] ?? $body['err'] ?? $response->body();
            throw new \Exception(is_string($message) ? $message : json_encode($message));
        }
        return $response->json();
    }

    /**
     * Reject a withdrawal: update payment_log, send rejection email.
     *
     * @param int $paymentLogId
     * @param string|null $adminRemark
     */
    public function rejectWithdrawal(int $paymentLogId, ?string $adminRemark = null): void
    {
        $paymentLog = PaymentLog::findOrFail($paymentLogId);
        $paymentLog->update(['payment_status' => 'Rejected']);
        $this->sendRejectionEmail($paymentLog->initiated_by, (float) $paymentLog->payment_amount, $adminRemark);
    }

    protected function sendApprovalEmail(string $email, float $amount, string $withdrawType, int $withdrawId): void
    {
        $settings = settings();
        $from = $settings['email_from_address'] ?? '';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From:' . ($settings['admin_title'] ?? '') . '<' . $from . '>' . "\r\n";
        $emailSubject = ($settings['admin_title'] ?? '') . ' - Wallet Withdrawal';
        $transid = 'WWID' . str_pad($withdrawId, 4, '0', STR_PAD_LEFT);
        $content = '<div>We are pleased to inform you that your withdrawal has been successfully approved.</div>
            <div><b>Transaction Details</b></div>
            <div><b>Approved Amount: </b>$' . number_format($amount, 2) . '</div>
            <div><b>Transaction ID: </b>' . $transid . '</div>
            <div><b>Withdrawal Type: </b>' . $withdrawType . '</div>';
        $user = \App\Models\User::where('email', $email)->first();
        $templateVars = [
            'name' => $user ? $user->fullname : $email,
            'site_link' => $settings['copyright_site_name_text'] ?? '',
            'email' => $from,
            'content' => $content,
            'title_right' => 'Wallet',
            'subtitle_right' => 'Withdrawal',
            'btn_text' => 'Go To Dashboard',
        ];
        $this->mailService->sendEmail($email, $emailSubject, $headers, '', $templateVars);
    }

    protected function sendRejectionEmail(string $email, float $amount, ?string $adminRemark = null): void
    {
        $settings = settings();
        $from = $settings['email_from_address'] ?? '';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From:' . ($settings['admin_title'] ?? '') . '<' . $from . '>' . "\r\n";
        $emailSubject = ($settings['admin_title'] ?? '') . ' - Wallet Withdrawal';
        $content = '<div>Your wallet withdrawal request has been rejected.</div>
            <div><b>Requested Amount: </b>$' . number_format($amount, 2) . '</div>';
        if ($adminRemark) {
            $content .= '<div><b>Remark: </b>' . htmlspecialchars($adminRemark) . '</div>';
        }
        $user = \App\Models\User::where('email', $email)->first();
        $templateVars = [
            'name' => $user ? $user->fullname : $email,
            'site_link' => $settings['copyright_site_name_text'] ?? '',
            'email' => $from,
            'content' => $content,
            'title_right' => 'Wallet',
            'subtitle_right' => 'Withdrawal',
            'btn_text' => 'Go To Dashboard',
        ];
        $this->mailService->sendEmail($email, $emailSubject, $headers, '', $templateVars);
    }
}
