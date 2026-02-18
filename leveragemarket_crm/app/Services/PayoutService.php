<?php

namespace App\Services;

use App\Models\ClientWallets;
use App\Models\PaymentLog;
use App\Models\TotalBalance;
use App\Models\User;
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

        if ($paymentLog->payment_type === 'BinancePay') {
            $clientWallet = null;
            if ($paymentTo && is_numeric($paymentTo)) {
                $clientWallet = ClientWallets::where('client_wallet_id', $paymentTo)->first();
            }
            if (!$clientWallet || !$clientWallet->wallet_address || !$clientWallet->wallet_currency) {
                throw new \Exception('Wallet address and currency could not be resolved for this withdrawal. Ensure payment_to is a valid client wallet.');
            }
            $address = $clientWallet->wallet_address;
            $coin = strtoupper(trim($clientWallet->wallet_currency ?? ''));
            $network = trim($clientWallet->wallet_network ?? '');
            $apiKey = config('services.binance.api_key');
            $secret = config('services.binance.secret');
            if (empty($apiKey) || empty($secret)) {
                throw new \Exception('Binance API key and secret are not configured. Set BINANCE_API_KEY and BINANCE_SECRET in .env.');
            }
            $payoutResponse = $this->createBinanceWithdraw($paymentLog, $address, $coin, $amount, $network);
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
     * Create a single withdrawal to an external wallet address via Binance Capital Withdraw API.
     *
     * @param PaymentLog $paymentLog
     * @param string $address Recipient wallet address
     * @param string $coin Uppercase currency (e.g. USDT, BNB)
     * @param float $amount Amount to withdraw
     * @param string $network Optional network (e.g. TRC20, ERC20); omit or empty for default
     * @return array Decoded JSON response
     * @throws \Exception On missing config, HTTP error, or API error
     */
    protected function createBinanceWithdraw(PaymentLog $paymentLog, string $address, string $coin, float $amount, string $network = ''): array
    {
        $apiKey = config('services.binance.api_key');
        $secret = config('services.binance.secret');
        if (empty($apiKey) || empty($secret)) {
            throw new \Exception('Binance API key and secret are not configured.');
        }

        $recvWindow = 5000;
        $params = [
            'coin' => $coin,
            'address' => $address,
            'amount' => $amount,
            'timestamp' => round(microtime(true) * 1000),
            'recvWindow' => $recvWindow,
            'withdrawOrderId' => 'wd_' . $paymentLog->payment_id,
        ];
        if ($network !== '') {
            $params['network'] = $network;
        }
        ksort($params);
        $queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $signature = hash_hmac('sha256', $queryString, $secret);
        $url = 'https://api.binance.com/sapi/v1/capital/withdraw/apply?' . $queryString . '&signature=' . $signature;

        $response = Http::withHeaders([
            'X-MBX-APIKEY' => $apiKey,
        ])->post($url);

        if (!$response->successful()) {
            $body = $response->json();
            $message = $body['msg'] ?? $body['message'] ?? $response->body();
            throw new \Exception('Binance withdraw failed: ' . (is_string($message) ? $message : json_encode($message)));
        }
        $data = $response->json();
        if (isset($data['code']) && $data['code'] != 0) {
            $message = $data['msg'] ?? json_encode($data);
            throw new \Exception('Binance API error: ' . $message);
        }
        return $data;
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
