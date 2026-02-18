<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiveAccount;
use App\Models\User;
use App\Models\ClientBankDetails;
use App\Models\TotalBalance;
use App\Models\WalletDeposit;
use App\Models\TradeWithdrawals;
use App\Models\BonusModel;
use App\Models\BonusTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Helpers\AccountHelper;
use App\Services\MailService as MailService;
use App\Models\UserGroup;
use GuzzleHttp\Client;
use App\Models\ClientWallets;
use App\Models\PaymentLog;
use Validator;
use App\Services\PusherService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TradeWithdrawal extends Controller
{
    protected $api;
    protected $mailService;
    protected $pusherService;

    public function __construct(MailService $mailService, MTWebAPI $api,PusherService $pusherService)
    {
        $this->api = $api;
        $this->mailService = $mailService;
        $email = session('clogin');
        AccountHelper::updateLiveAndDemoAccounts($email, $api);
        $this->settings = settings();
        $this->pusherService = $pusherService;
    }
    public function index($type = null)
    {

         $user = auth()->user();
        $email = auth()->user()->email;
        session()->forget('Wallet_Transfer_Otp');
        session()->forget('USDT_Withdrawal_Otp');
        session()->forget('Bank_Withdrawal_Otp');
        $is_kyc_verified = User::where('email', $email)->value('kyc_verify');

        /*if ($is_kyc_verified == 0) {
            alert()->info("Please complete the KYC Verification to proceed Withdrawal process.", "Please contact support, if your KYC already verified.");
            return redirect("/user-profile");
        }*/

        $user_groups = UserGroup::find(session('user')['group_id']);
        $client_banks = ClientWallets::where('user_id', $email)
            ->where('status', 1)
            ->get();
        AccountHelper::updateLiveAndDemoAccounts($email, $this->api);
        $liveaccount_details = LiveAccount::with('accountType')
            ->where('email', $email)
			->where('status', 'active')	
            ->get();
        $walletenabled = User::where('email', $email)->value('wallet_enabled') ?? false;
        $bank_details = ClientBankDetails::where('userId', $email)->get() ?? [];
        $totals = LiveAccount::where('email', $email)->where('status', 'active')	
            ->selectRaw('SUM(equity) as equity, SUM(credit) as credit, SUM(balance) as balance')
            ->first();
        $existing = TradeWithdrawals::where('email', session('clogin'))
            ->where('status', 0)
            ->first();
        return view('trade_withdrawal', compact('user','liveaccount_details', 'walletenabled', 'bank_details', 'totals', 'user_groups', 'client_banks', 'type', 'existing'));
    }
    public function withdraw(Request $request)
    {
        $otp_type = str_replace(' ', '_', trim($request->input('withdraw_type'))).'_Otp';
        if (!session()->has($otp_type) || !request()->has('otp')) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify with OTP and proceed.',
            ], 400);
        } elseif (request('otp') != session($otp_type)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP, Please try again.',
            ], 400);
        }

        $settings = settings();
        $this->api->SetLoggerWriteDebug(config('constants.IS_WRITE_DEBUG_LOG'));
        $this->api->Connect(
            $settings['mt5_server_ip'],
            $settings['mt5_server_port'],
            300,
            $settings['mt5_server_web_login'],
            $settings['mt5_server_web_password']
        );
		$request->validate([
            'withdraw_amount' => 'required|numeric|min:10',
            'trade_id' => 'required'
        ]);
		
        $email = session('clogin');        
        $trade_id = $request->input('trade_id');
        $withdraw_type = $request->input('withdraw_type');
        $amount = $request->input('withdraw_amount');
        $withdraw_to = $request->input('withdraw_to');
        $agent_account = $request->input('agent_account');
		$liveaccount_details = LiveAccount::with('accountType')
            ->where('email', $email)
            ->where('trade_id', $trade_id)
            ->first();      
		
        // Check for sufficient balance
        if (isset($liveaccount_details) && $amount > $liveaccount_details->Balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
            ], 400);
        }
		
		/* Bonus Detection */
		$bonusdetect = $this->bonusWithdrawLogic($trade_id, $amount, $liveaccount_details->credit, $liveaccount_details->Balance);
				
        if ($withdraw_type == "Wallet Withdrawal"){
			
            $balance = abs((float) $amount) * -1;
            $comment = 'Withdraw';
            $ticket = NULL;
            $login = $trade_id;
            $withdraw_date = date("Y-m-d H:i:s");
            $errorCode = $this->api->TradeBalance($login, $type = MTEnDealAction::DEAL_BALANCE, $balance, $comment, $ticket, $margin_check = true);
            if ($errorCode != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($errorCode);
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!! ' . $error,
                    'error' => $error,
                ], 400);
            } else {
                DB::beginTransaction();
                try {
                    $tradeWithdrawals = TradeWithdrawals::create([
                        'email' => $email,
                        'trade_id' => $trade_id,
                        'withdrawal_amount' => $amount,
                        'withdraw_type' => 'A2W withdraw',
                        'withdraw_date' => $withdraw_date,
                        'withdraw_to' => $withdraw_to,
                        'wallet_qr' => '',
                        'Status' => 1
                    ]);

                    $datalog = [
                     'email' => $email,
                        'trade_id' => $trade_id,
                        'withdrawal_amount' => $amount,
                        'withdraw_type' => 'A2W withdraw',
                        'withdraw_date' => $withdraw_date,
                        'withdraw_to' => $withdraw_to,
                        'wallet_qr' => '',
                        'Status' => 1
                    ];
                    $withdrawTransId = $tradeWithdrawals->id;
                    TotalBalance::create([
                        'email' => $email,
                        'deposit_amount' => $amount,
                    ]);
                    WalletDeposit::create([
                        'email' => $email,
                        'deposit_amount' => $amount,
                        'deposit_type' => 'A2W withdraw',
                        'Status' => 1,
                    ]);					
					
                    DB::commit();
                    $emailSubject = $settings['admin_title'] . ' - Fund Withdrawal';
                    $transid = "TDID" . str_pad($withdrawTransId, 4, '0', STR_PAD_LEFT);
                    $content = '<div>We are pleased to inform you that funds have been successfully withdrawn from your account.</div>
					<div><b>Withdrawal Details</b></div>
					<div><b>Amount: </b>$' . $amount . '</div>
					<div><b>Account ID: </b>' . $trade_id . '</div>
					<div><b>Transaction ID: </b>' . $transid . '</div>
					<div><b>Withdraw Date: </b>' . $withdraw_date . '</div>
					<div><b>Withdraw Type </b>' . $withdraw_type . '</div>';
                    $templateVars = [
                        'name' => session('user')['fullname'],
                        'site_link' => $settings['copyright_site_name_text'],
                        "btn_text" => "Go To Dashboard",
                        'email' => $settings['email_from_address'],
                        "content" => $content,
                        "title_right" => "Fund",
                        "subtitle_right" => "Withdrawal",
                        "img_hidden"=>"true",
                    ];
                    $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                    // addIpLog('Trade Withdrawal Request - A2W Wallet Withdrawal');
                      addIpLog('Trader withdraw response ', $datalog);
                    return response()->json(['success' => "Your wallet is credited with $" . $amount]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    echo "<pre>";
                    print_r($e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Something Went Wrong !!! Please Try Again'
                    ], 400);
                }
            }
        } elseif ($withdraw_type == "Agent Withdrawal") {
            $tradeWithdrawals = TradeWithdrawals::create([
                'email' => $email,
                'trade_id' => $trade_id,
                'withdrawal_amount' => $amount,
                'withdraw_type' => $withdraw_type,
                'withdraw_date' => date('Y-m-d H:i:s'),
                'withdraw_to' => $withdraw_to,
                'wallet_qr' => '',
                'Status' => 0,
                'agent_account' => $agent_account
            ]);

            $pusherData = [
                'type' => 'Trade Withdrawal',
                'message' => 'A Trade Withdrawal request of $' . $amount . ' has been received from ' . session('user')['fullname'],
                'link' => "/admin/trading_withdrawal_details?id=" . md5($tradeWithdrawals->id),
                'enc_id'=>md5($tradeWithdrawals->id)
            ];
            $datalog = [
                 'email' => $email,
                'trade_id' => $trade_id,
                'withdrawal_amount' => $amount,
                'withdraw_type' => $withdraw_type,
                'withdraw_date' => date('Y-m-d H:i:s'),
                'withdraw_to' => $withdraw_to,
                'wallet_qr' => '',
                'Status' => 0,
                'agent_account' => $agent_account
            ];
            // addIpLog('Trade Withdrawal Request - Agent Withdrawal');
             addIpLog('Trader withdraw Agent response ', $datalog);
            $this->pusherService->sendPusherMessage($pusherData);
            return response()->json(['success' => "Your Trade Withdrawal Request Submitted"]);
        } elseif ($withdraw_type == 'Crypto Payment') {
            $email = session('clogin');
            $amount = $request->input('withdraw_amount');
            $trade_id = $request->input('trade_id');
            $wallet_id = $request->input('client_wallet');
            $client_wallets = ClientWallets::where('client_wallet_id', $wallet_id)->first();
            $wallet_address = $client_wallets->wallet_address ?? '';
            $data = [
                "payment_amount" => $amount,
                "payment_type" => "Crypto Payment",
                "payment_reference_id" => "Trade Withdrawal",
                "payment_status" => "Initiated",
                "initiated_by" => $email,
                "trade_id" => $trade_id,
            ];
            $paymentLog = PaymentLog::create($data);
            $request = (object) ["email" => $email, "payment_id" => $paymentLog->payment_id, "amount" => $amount, "wallet_address" => $wallet_address];
            $payment = $this->sendCryptoWithdrawalRequest($request);
            $payment = json_decode($payment, true);
            if ($payment['status'] == 'PaymentError') {
                PaymentLog::where('payment_id', $paymentLog->payment_id)->update([
                    'payment_res' => $payment['response'],
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => $payment['response'],
                    'request' => $payment['request'],
                    'remarks' => $payment['remarks']
                ]);
            } else {
                $payment_response = json_decode($payment['response'], true);
                PaymentLog::where('payment_id', $paymentLog->payment_id)->update([
                    'payment_req' => json_encode($payment['request']),
                    'payment_res' => $payment['response'],
                    'remarks' => $payment['remarks'],
                ]);
                // $tradeWithdrawals = TradeWithdrawals::create([
                //     'email' => $email,
                //     'trade_id' => $trade_id,
                //     'withdrawal_amount' => $amount,
                //     'withdraw_type' => $withdraw_type,
                //     'withdraw_date' => date('Y-m-d H:i:s'),
                //     'withdraw_to' => '',
                //     'wallet_qr' => '',
                //     'Status' => 0,
                // ]);
                addIpLog('Trader withdraw Crypto Payment ', $data);
                return response()->json(['success' => 'Your Trade Withdrawal Request Submitted']);
            }
        } else if ($withdraw_type == 'Bank Withdrawal') {
            $adj_amount = $_POST['adjustment_inr'] ?: 0;
            $amount_in_other_currency = $_POST['amount_in_other_currency'] ?: 0;
            $withdrawal_currency = $_POST['withdrawal_currency'] ?: '';
            $trade_id = $request->input('trade_id');
            $withdraw_type = $request->input('withdraw_type');
            $amount = $request->input('withdraw_amount');
            $withdraw_to = $request->input('withdraw_to');
            $agent_account = $request->input('agent_account');
            $tradeWithdrawals = TradeWithdrawals::create([
                'email' => $email,
                'trade_id' => $trade_id,
                'withdrawal_amount' => $amount,
                'withdraw_type' => $withdraw_type,
                'adj_amount' => $adj_amount,
                'withdraw_to' => $withdraw_to,
                'amount_in_other_currency' => $amount_in_other_currency,
                'withdrawal_currency' => $withdrawal_currency,
            ]);
            $datalogo = [
                'email' => $email,
                'trade_id' => $trade_id,
                'withdrawal_amount' => $amount,
                'withdraw_type' => $withdraw_type,
                'adj_amount' => $adj_amount,
                'withdraw_to' => $withdraw_to,
                'amount_in_other_currency' => $amount_in_other_currency,
                'withdrawal_currency' => $withdrawal_currency,
            ];
            // addIpLog('Trade Withdrawal Request - Bank Withdrawal');
            $pusherData = [
                'type' => 'Trade Withdrawal',
                'message' => 'A Trade Withdrawal request of $' . $amount . ' has been received from ' . session('user')['fullname'],
                'link' => "/admin/trading_withdrawal_details?id=" . md5($tradeWithdrawals->id),
                'enc_id'=>md5($tradeWithdrawals->id)
            ];
            $this->pusherService->sendPusherMessage($pusherData);
            addIpLog('Bank Withdrawa Crypto Payment ', $datalogo);
            return response()->json(['success' => 'Your Trade Withdrawal Request Submitted']);
        } else if ($withdraw_type == 'USDT Withdrawal') {
            $wallet_qr = null;
            if ($request->hasFile('wallet_qr')) {
                $validator = Validator::make($request->all(), [
                    'wallet_qr' => 'required|file|mimes:pdf,png,jpeg|max:2048',
                ]);
                if ($validator->fails()) {
                    $firstError = $validator->errors()->first();
                    return redirect()->back()->with('error', $firstError);
                }
                $wallet_qr = $request->file('wallet_qr')->store('wallet_qr_code', 'public');
            }
            $trade_id = $request->input('trade_id');
            $withdraw_type = $request->input('withdraw_type');
            $amount = $request->input('withdraw_amount');
            $withdraw_to = $request->input('withdraw_to');
            $tradeWithdrawals = TradeWithdrawals::create([
                'email' => $email,
                'trade_id' => $trade_id,
                'withdrawal_amount' => $amount,
                'withdraw_type' => $withdraw_type,
                'wallet_qr' => $wallet_qr,
                'withdraw_to' => $withdraw_to
            ]);
            addIpLog('Trade Withdrawal Request - USDT Withdrawal');
            $pusherData = [
                'type' => 'Trade Withdrawal',
                'message' => 'A Trade Withdrawal request of $' . $amount . ' has been received from ' . session('user')['fullname'],
                'link' => "/admin/trading_withdrawal_details?id=" . md5($tradeWithdrawals->id),
                'enc_id'=>md5($tradeWithdrawals->id)
            ];
            $this->pusherService->sendPusherMessage($pusherData);
            return response()->json(['success' => 'Your Trade Withdrawal Request Submitted']);
        }
    }
    public function sendCryptoWithdrawalRequest($request)
    {
        $amount = $request->amount;
        $email = $request->email;
        $wallet_address = $request->wallet_address;
        $payment_id = $request->payment_id;
        $user_groups = UserGroup::find(session('user')['group_id']);
        $apiToken = null;
        if (isset($user_groups['crypto_payment_api']) && !empty($user_groups['crypto_payment_api'])) {
            $apiToken = $user_groups['crypto_payment_api'];
        }
        $apiSecret = null;
        if (isset($user_groups['crypto_payment_security']) && !empty($user_groups['crypto_payment_security'])) {
            $apiSecret = $user_groups['crypto_payment_security'];
        }
        // $apiToken='Zdcz2VbKSEZLB1XzCHUhBD5qwpo5EWV2BXit';
        // $apiSecret='QgRifBvd4STHS3HN7Dx0txTugDceBoA3nxDL';

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $callback_url = $this->settings['copyright_site_name_text'] . "/cryptopayment-response?payment_id=" . md5($payment_id);
        $body = [
            "address" => '0x2757fa0eaadc0733127e200869d8529eb7724c2d',
            "amount" => 1,
            "apiToken" => $apiToken,
            "callbackUrl" => $callback_url,
            "currency" => "USD",
            "paymentGatewayName" => "USDT TRC20",
            "withdrawCurrency" => "USX",
            // "tradingAccountLogin" => "",
            "timestamp" => time()
        ];
        ksort($body);
        $signarture = hash("sha384", implode('', $body) . $apiSecret);
        $body['signature'] = $signarture;
        try {
            $response = $client->post(
                // 'https://pp-staging.fx-edge.com/api/v2/withdraw/crypto_agent',
                'https://wallet.fe-prime.com/api/v2/withdraw/crypto_agent',
                [
                    'headers' => $headers,
                    'json' => $body,
                ]
            );
            return json_encode([
                'status' => 'success',
                'response' => $response->getBody()->getContents(),
                'request' => $body,
                'remarks' => $callback_url
            ]);
        } catch (\Exception $e) {
            return json_encode(
                [
                    'status' => 'PaymentError',
                    'response' => $e->getMessage(),
                    'request' => $body,
                    'remarks' => $callback_url
                ]
            );
        }
    }
	
	public function bonusWithdrawLogic($tradeId, $withdrawAmt, $creditBalance, $currentBalance)
	{
		if ($withdrawAmt <= 0 || $currentBalance <= 0) {
			return;
		}

		DB::transaction(function () use ($tradeId, $withdrawAmt, $creditBalance, $currentBalance) {
			
			$bonusToRemove = min($withdrawAmt, $creditBalance);
			if ($bonusToRemove <= 0) {
				return;
			}			
			$bonuses = BonusTransaction::where('trade_id', $tradeId)
				->where('bonus_withdrawstatus', 0)
				->orderBy('bonus_date', 'asc')
				->lockForUpdate()
				->get();
			
			$remaining = $bonusToRemove;

			foreach ($bonuses as $bonus) {
				if ($remaining <= 0) {
					break;
				}
				$availableBonus = $bonus->bonus_amount - $bonus->bonus_withdrawamount;

				if ($availableBonus <= 0) {
					$bonus->bonus_withdrawstatus = 1;
					$bonus->save();
					continue;
				}

				// ✅ Deduct only once
				$deduct = min($availableBonus, $remaining);

				// ✅ Update DB bonus transaction
				$bonus->bonus_withdrawamount += $deduct;

				if ($bonus->bonus_withdrawamount >= $bonus->bonus_amount) {
					$bonus->bonus_withdrawstatus = 1;
				}

				$bonus->save();

				// ✅ Reduce remaining amount
				$remaining -= $deduct;
				
				$ticket = null;
				$bonusamount = -abs($deduct);
				$comment = 'Bonus Out';
				if (($error_code = $this->api->TradeBalance($tradeId, MTEnDealAction::DEAL_BONUS, $bonusamount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
					Log::info('MT5: Bonus Reduce ', [
						'login' => $login,
						'error' => MTRetCode::GetError($error_code)
					]);
				}
				
				DB::table('bonus_withdrawlog')->insert([
					'bonus_transid'  => $bonus->id,
					'bonus_id'       => $bonus->bonus_id,
					'email'        	 => $bonus->email,
					'tradeid'		 => $tradeId,
					'withdraw_amount'=> $deduct,
					'status'         => 1,
					'created_at'     => now(),
					'updated_at'     => now(),
				]);
			}
		});			
	}
}