<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientBankDetails;
use App\Models\TotalBalance;
use App\Models\TradeDeposits;
use Illuminate\Http\Request;
use App\Models\LiveAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\WalletDeposit;
use App\Models\WalletWithdraw;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Helpers\AccountHelper;
use App\Models\BonusModel;
use App\Models\BonusTransaction;
use App\Services\MailService as MailService;
use App\Models\UserGroup;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Services\PusherService;
use App\Http\Controllers\Payment;
class TradeDeposit extends Controller
{
    protected $api;
    protected $mailService;
    protected $settings;
    protected $pusherService;

    public function __construct(MailService $mailService, MTWebAPI $api,PusherService $pusherService,Payment$payment)
    {

        $this->api = $api;
        $this->mailService = $mailService;
        $this->settings = settings();
        $this->pusherService = $pusherService;
        $this->Payment = $payment;
    }
    public function index(Request $request)
    {
		$bonuscode = null;
		if ($request->filled('bonusid')) {
			$bonuscode = BonusModel::whereRaw(
				"MD5(bonus_id) = ?",
				[$request->bonusid]
			)->first();
			if ($bonuscode) {
				// Show only same bonus_accessable type
				$bonuslist = BonusModel::where('status', 1)
					->where('bonus_accessable', $bonuscode->bonus_accessable)
					->get();
			} else {
				// Invalid bonusid → fallback to all bonuses
				$bonuslist = BonusModel::where('status', 1)
					->where(function ($q) {
						$q->whereNotIn('bonus_accessable', ['referred_users', 'direct_users'])
						  ->orWhereNull('bonus_accessable');
					})
					->get();
			}
		} else {
			// No bonus selected → show all bonuses
			$bonuslist = BonusModel::where('status', 1)
				->where(function ($q) {
					$q->whereNotIn('bonus_accessable', ['referred_users', 'direct_users'])
					  ->orWhereNull('bonus_accessable');
				})
				->get();
		}
		
        $email = auth()->user()->email;
        $totalDeposit = WalletDeposit::where('email', $email)
            ->where('status', 1)
            ->sum('deposit_amount');
        $totalWithdraw = WalletWithdraw::where('email', $email)
            ->whereIn('status', [0,1])
            ->sum('withdraw_amount');
        $walletBalance = $totalDeposit - $totalWithdraw;

		$user = User::where('email', $email)->first();
        $user_groups = UserGroup::find(session('user')['group_id']);
        AccountHelper::updateLiveAndDemoAccounts($email, $this->api);
        
		$liveaccount_details = LiveAccount::with('accountType')
			->leftJoin('trade_deposit as td', 'liveaccount.trade_id', '=', 'td.trade_id')
			->where('liveaccount.email', $email)
			->where('liveaccount.status', 'active')
			->groupBy('liveaccount.id', 'liveaccount.trade_id')
			->select(
				'liveaccount.*',
				DB::raw('COUNT(td.id) as deposit_count')
			)
			->when($bonuscode && $bonuscode->bonus_accessable === 'welcome_bouns', function ($q) {
				$q->havingRaw('COUNT(td.id) = 0');
			})
			->when($bonuscode && $bonuscode->bonus_accessable === 'regular_bouns', function ($q) {
				$q->havingRaw('COUNT(td.id) > 0');
			})
			->get();
			
        $walletenabled = User::where('email', $email)->value('wallet_enabled') ?? false;
        $bank_details = ClientBankDetails::where('email', $email)->first() ?? [];
        $totals = LiveAccount::where('email', $email)
            ->selectRaw('SUM(equity) as equity, SUM(credit) as credit, SUM(balance) as balance')
            ->first();

		$bankdepositcount = DB::table('trade_deposit')
			->where('email', $email)
			->where('deposit_type', 'Bank Deposit')
			->where('status', 0)
			->count();
		
		return view('trade_deposit', compact('liveaccount_details', 'walletenabled', 'bank_details', 'totals', 'user_groups', 'user','bankdepositcount','walletBalance', 'bonuscode', 'bonuslist'));
    }
    public function deposit(Request $request)
    {        
		$request->validate(
            [
                'user.trade_id' => 'required',
                'user.deposit' => 'required|numeric',
                'user.deposit_type' => 'required',
                'deposit_proof' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            ],
            [
                'user.trade_id.required' => 'You have to select an account to proceed.'
            ]
        );
        $settings = settings();
        $this->api->SetLoggerWriteDebug(config('constants.IS_WRITE_DEBUG_LOG'));
        $this->api->Connect(
            $settings['mt5_server_ip'],
            $settings['mt5_server_port'],
            300,
            $settings['mt5_server_web_login'],
            $settings['mt5_server_web_password']
        );
        $user = $request->input('user');
        $email = session('clogin');
        $trading_deposited1 = $user['deposit'];
        $email = $user['email'];
        $trade_id = $user['trade_id'];
        $deposit_type = $user['deposit_type'];
        $deposit_from = NULL;
        $comment = "Deposit";
        $bonusComment = "Bonus Deposit";
        $ticket = NULL;
		
		$bonus_id = $user['bonus_id'] ?? null;
        $bonusValue = 0;
        $bonusTransId = NULL;
		
		if (!empty($bonus_id)){
            $bonusDetails = BonusModel::where('bonus_id', $bonus_id)->first();
			
            if ($bonusDetails) {
                if ($bonusDetails->bonus_type === 'percentage') {
                    $bonusValue = ($user['deposit'] * $bonusDetails->bonus_value) / 100;
                } else {
                    $bonusValue = $bonusDetails->bonus_value;
                }
            } else {
                $bonusValue = 0;
            }
			
			/*Check the logic based on bonus terms*/
			if ($bonusDetails->bonus_accessable == "first_deposit" || $bonusDetails->bonus_accessable == "welcome_bouns") {
				$checkexistdep = TradeDeposits::where('email', $email)
					->count();
				if ($checkexistdep == 0) {		
					echo "ok";
				} else {
					return redirect()->back()->with('error', 'This bonus is only applicable for the first deposit. Please apply for a different bonus code!'); 
				}
			} else if ($bonusDetails->bonus_accessable == "regular_bouns") {
				$checkexistdep = TradeDeposits::where('email', $email)
					->where('trade_id', $trade_id)
					->count();
				if ($checkexistdep > 0) {
					echo "ok";
				} else {
					return redirect()->back()->with('error', 'Regular bonus not applicable this account.');
				}
			} else if ($bonusDetails->bonus_accessable == "direct_users") {
				$checkexistdep = TradeDeposits::where('email', $email)
					->where('trade_id', $trade_id)
					->count();
				if ($checkexistdep == 0) {
					echo "ok";
				} else {
					echo "No"; 
				}
			} else {
				echo "No Issues";
			}	
        }
		
        // Calculate wallet balance
        $totalWd = WalletDeposit::where('email', $email)->where('status', 1)->sum('deposit_amount');
        $totalWw = WalletWithdraw::where('email', $email)->whereIn('status', [0,1])->sum('withdraw_amount');
        $walletBalance = $totalWd - $totalWw;
        // Check if there's enough balance
        if (($user['deposit_type'] === 'Wallet Transfer' || $user['deposit_type'] === 'Wallet Payments') && $walletBalance < $user['deposit']) {
            /*return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => "Insufficient wallet balance!!!!",
            ], 402);*/
			
			return redirect()->back()->with('error', 'Insufficient wallet balance. Your current balance:$'.$walletBalance);
        }
        // Handle file upload for deposit proof
        $depositProofPath = null;
        if ($request->hasFile('deposit_proof')) {
            $depositProofPath = $request->file('deposit_proof')->store('deposit_proofs', 'public');
        }
		
		if (!empty($bonus_id)) {
			$bonusDepositCurrency = "USD";
			$bonusDepositType = "Bonus In";
			$bonusDescription = '';
			$bonusTransaction = BonusTransaction::create([
				'email' => $email,
				'trade_id' => $user['trade_id'],
				'bonus_amount' => $bonusValue,
				'bonus_type' => $bonusDepositType,
				'bonus_id' => $bonus_id,
				'status' => 1,
				'adminRemark' => $bonusDescription,
				'bonus_currency' => $bonusDepositCurrency,
				'created_by' => session('clogin'),
			]);
			$bonusTransId = $bonusTransaction->id;
		} else {
			$bonusTransId = 0;
		}
		
		if($user['deposit_type'] === 'Wallet Payments'){
			
			$TradeDeposits = TradeDeposits::create([
				'email' => $email,
				'trade_id' => $trade_id,
				'deposit_currency_amount' => $user['deposit'],
				'deposit_type' => 'W2A Deposit',
				'deposit_currency' => "USD",
				'Status' => 1,
				'deposit_amount' => $user['deposit'],
				'bonus_amount' => $bonusValue,
				'bonus_trans_id' => $bonusTransId
			]);
			
			/*If bonus value there adding the deposit entry*/
            $did = $TradeDeposits->id;
			 //payment logs
			$PaymentLog = PaymentLog::create([
				'email' => $email,
				'payment_amount' => $user['deposit'],
				'payment_type' => 'Wallet to Trade Account',
				'trade_id' => $trade_id,
				'payment_reference_id' => $trade_id,
				'initiated_by' => $email,
				'remarks' => "Wallet to Trade Deposit From Users",
				'payment_status' => 1,
				'bonus_amount' => $bonusValue,
				'bonus_trans_id' => $bonusTransId
			]);
         //wallet withdraw 
			$WalletWithdraw = WalletWithdraw::create([
				'email' => $email,
				'withdraw_amount' => $user['deposit'],
				'withdraw_type' => 'W2A Deposit',
				'transaction_id' => $trade_id,
				'Status' => 1,
			]);
         //mt5 update
			$data = [
                'email' => $email,
                'tradeId' => $trade_id,
                'amount' => $user['deposit'],
                'did' => $did,
                'status'=> 1,
                'description'=>'W2A Deposit',
                'bonus_amount' => $bonusValue
            ];
            
            $requestObject = new Request($data);
            $paymentController = app()->make(Payment::class);
          addIpLog('deposit_type:Wallet Payment', $data);
            $result = $paymentController->walletTransactionupdate($requestObject);
			if ($result) {
				return redirect()->back()->with('success', 'Transaction Approved Successfully');
			}
        } else if ($user['deposit_type'] == 'match2pay') {

            $data = [
                "payment_amount" => $trading_deposited1,
                "payment_type" => "Crypto Payment",
                "payment_reference_id" => "Trade Deposit",
                "payment_status" => "Initiated",
                "initiated_by" => $email,
                "bonus_id"=>$bonus_id,
                "trade_id"=>$trade_id,
				'bonus_amount' => $bonusValue,
				'bonus_trans_id' => $bonusTransId
            ];
            $paymentLog = PaymentLog::create($data);
            $request=(object)["email" => $email, "payment_id" => $paymentLog->payment_id,"deposit"=>$trading_deposited1];
            $payment = $this->sendCryptoDepositRequest($request);
            $payment = json_decode($payment, true);
            if ($payment['status'] == 'PaymentError') {
                PaymentLog::where('payment_id', $paymentLog->payment_id)->update([
                    'remarks' => $payment['response'],
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong.Please try again other payment methods or try again later.',
                ]);
            } else {
                $payment_response = json_decode($payment['response'], true);
                PaymentLog::where('payment_id', $paymentLog->payment_id)->update([
                    'payment_req' => json_encode($payment['request']),
                    'payment_url' => $payment_response['checkoutUrl'],
                    'remarks' => $payment['response'],
                ]);
                return response()->json([
                    'status' => 'success',
                    'checkoutUrl' => $payment_response['checkoutUrl'],
                    'paymentId'=>md5($paymentLog->payment_id)
                ]);
            }

        } else if($user['deposit_type'] == "Now Payment"){
            $data = [
                "payment_amount" => $trading_deposited1,
                "payment_type" => "NowPayment",
                "payment_reference_id" => $trade_id,
                "payment_status" => "Initiated",
                "initiated_by" => $email,
				'bonus_amount' => $bonusValue,
				'bonus_trans_id' => $bonusTransId
            ];
            $paymentLog = PaymentLog::create($data);
            $orderId = 'nowPay' . $paymentLog->id;
            $currency = 'USD';
            $payment = $this->createPayment($trading_deposited1, $currency, $orderId, $paymentLog->payment_id);
             addIpLog('deposit_type:Now Payment', $data);
            if ($payment) {
                return redirect($payment['invoice_url']);
            } else {
                return redirect()->back()->with('error', 'Something went wrong in NowPayment. Please try again other Payment methods or try again later.');
            }
			
        } else if($user['deposit_type'] == 'USDT Deposit'){
			
            $tradeDeposits = TradeDeposits::create([
                'email' => $email,
                'trade_id' => $user['trade_id'],
                'deposit_amount' => $user['deposit'],
                'deposit_type' => $user['deposit_type'],
                'deposted_date' => date("Y-m-d H:i:s"),
                'deposit_from' => null,
                'deposit_proof' => $depositProofPath,
                'Status' => 0,
                'usdt_wallet_qr'=>$user['usdt_wallet_qr'],
                'usdt_wallet_id'=>$user['usdt_wallet_id'],
				'bonus_amount' => $bonusValue,
				'bonus_trans_id' => $bonusTransId
            ]);
            $pusherData = [
                'type' => 'Trade Deposit',
                'message' => 'A Trade Deposit request of $' . $user['deposit'] . ' has been received from ' . session('user')['fullname'],
                'link' => "/admin/trading_deposit_details?id=" . md5($tradeDeposits->id),
                'enc_id'=>md5($tradeDeposits->id)
            ];
            $this->pusherService->sendPusherMessage($pusherData);
            return redirect()->back()->with('success', 'Your Trade Deposit Request Submitted. Account Will Get Deposited Once Approved');
        
		} 
        else if($user['deposit_type'] == 'International Deposit'){
			$deposit_currency = $user["deposit_currency"];
            $deposit_currency_amount = $user["deposit"];
            $deposit_currency_in_usd = $user["deposit_currency_in_usd"];
            $trading_deposited1 = $deposit_currency_in_usd;
            $adj_amount = NULL;
			if ($deposit_currency == 'INR') {
                $adj_amount = $user['adjustment_inrbank'];
            }
            $deposit_account_details = NULL;
            if (isset($_POST["deposit_account_details"])) {
              $deposit_account_details = $_POST["deposit_account_details"];
            }
            $tradeDeposits = TradeDeposits::create([
                'email' => $email,
                'trade_id' => $user['trade_id'],
				'deposit_amount' => $deposit_currency_in_usd,                
                'deposit_type' => $user['deposit_type'],
                'deposted_date' => date("Y-m-d H:i:s"),
                'deposit_from' => null,
                'deposit_proof' => $depositProofPath,
                'status' => 0,
                'deposit_account_details'=>$deposit_account_details,
				'deposit_currency' => $deposit_currency,
				'deposit_currency_amount' => $deposit_currency_amount,
				'deposit_currency_in_usd' => $deposit_currency_in_usd,
				'adj_amount' => $adj_amount
            ]);
			
            $pusherData = [
                'type' => 'Trade Deposit',
                'message' => 'A Trade Deposit request of $' . $user['deposit'] . ' has been received from ' . session('user')['fullname'],
                'link' => "/admin/trading_deposit_details?id=" . md5($tradeDeposits->id),
                'enc_id'=>md5($tradeDeposits->id)
            ];
            $this->pusherService->sendPusherMessage($pusherData);
            return redirect()->back()->with('success', 'Your Trade Deposit Request Submitted. Account Will Get Deposited Once Approved');
			
        } 
        else if($user['deposit_type'] == 'Bank Deposit'){
			$deposit_currency = $user["deposit_currency"];
            $deposit_currency_amount = $user["deposit"];
            $deposit_currency_in_usd = $user["deposit_currency_in_usd"];
            $trading_deposited1 = $deposit_currency_in_usd;
            $adj_amount = NULL;
			if ($deposit_currency == 'INR') {
                $adj_amount = $user['adjustment_inrbank'];
            }
            $deposit_account_details = NULL;
            if (isset($_POST["deposit_account_details"])) {
              $deposit_account_details = $_POST["deposit_account_details"];
            }

            $datalog=[
            'email' => $email,
                'trade_id' => $user['trade_id'],
				'deposit_amount' => $deposit_currency_in_usd,                
                'deposit_type' => $user['deposit_type'],
                'deposted_date' => date("Y-m-d H:i:s"),
                'deposit_from' => null,
                'deposit_proof' => $depositProofPath,
                'Status' => 0,
                'deposit_account_details'=>$deposit_account_details,
				'deposit_currency' => $deposit_currency,
				'deposit_currency_amount' => $deposit_currency_amount,
				'deposit_currency_in_usd' => $deposit_currency_in_usd,
				'adj_amount' => $adj_amount
            ];
            $tradeDeposits = TradeDeposits::create([
                'email' => $email,
                'trade_id' => $user['trade_id'],
				'deposit_amount' => $deposit_currency_in_usd,                
                'deposit_type' => $user['deposit_type'],
                'deposted_date' => date("Y-m-d H:i:s"),
                'deposit_from' => null,
                'deposit_proof' => $depositProofPath,
                'Status' => 0,
                'deposit_account_details'=>$deposit_account_details,
				'deposit_currency' => $deposit_currency,
				'deposit_currency_amount' => $deposit_currency_amount,
				'deposit_currency_in_usd' => $deposit_currency_in_usd,
				'adj_amount' => $adj_amount
            ]);
			
            $pusherData = [
                'type' => 'Trade Deposit',
                'message' => 'A Trade Deposit request of $' . $user['deposit'] . ' has been received from ' . session('user')['fullname'],
                'link' => "/admin/trading_deposit_details?id=" . md5($tradeDeposits->id),
                'enc_id'=>md5($tradeDeposits->id)
            ];
             addIpLog('deposit_type:Bank', $datalog);
            $this->pusherService->sendPusherMessage($pusherData);
            return redirect()->back()->with('success', 'Your Trade Deposit Request Submitted. Account Will Get Deposited Once Approved');
			
        }else if($user['deposit_type'] == 'usdc-polygon'){
            $payment = DB::table('payment_logs')->insertGetId([
                'payment_amount' => $trading_deposited1,
                'payment_type' => 'payissa',
                'payment_req' => '',
                'payment_reference_id' => $trade_id,
                'payment_url' => '',
                'payment_status' => 'Initiated',
                'payment_res' => '',
                'initiated_by' => $email
            ]);
            $url = 'https://api2.payissa.com/control/wallet.php';
            $user_groups = UserGroup::find(session('user')['group_id']);
            $address = $user_groups['payissa_wallet'];
            $callback = url('/payment-confirmation?payment_id=' . md5($payment));
            $fullUrl = "$url?address=$address&callback=" . urlencode($callback);
            $payment_req = json_encode(['address' => $address, 'callback' => $callback]);
            $response = Http::get($fullUrl);
            $redirect_url = '/';
            if ($response->failed()) {
                $errorResponse = $response->body();
                DB::table('payment_logs')
                    ->where('payment_id', $payment)
                    ->update(['payment_res' => $errorResponse]);
            } else {
                $resp = $response->json();
                $redirect_url = "https://checkout2.payissa.com/process-payment.php?address=" . $resp['address_in'] . "&amount=$trading_deposited1&provider=wert&email=" . urlencode($email) . "&currency=USD";
                DB::table('payment_logs')
                    ->where('payment_id', $payment)
                    ->update([
                        'payment_url' => $redirect_url,
                        'remarks' => $response->body(),
                        'payment_req' => $payment_req
                    ]);
            }
            return redirect($redirect_url);
			
		} else if ($deposit_type == 'paygate') {
			$payment = DB::table('payment_logs')->insertGetId([
                'payment_amount' => $trading_deposited1,
                'payment_type' => 'paygate',
                'payment_req' => '',
                'payment_reference_id' => $trade_id,
                'payment_url' => '',
                'payment_status' => 'Initiated',
                'payment_res' => '',
                'initiated_by' => $email
            ]);
						
			$url = 'https://api.paygate.to/crypto/btc/wallet.php';
            $user_groups = UserGroup::find(session('user')['group_id']);
            $address = 'bc1qx9t2l3pyny2spqpqlye8svce70nppwtaxwdrp4';
            $callback = url('/payment-confirmation?payment_id=' . md5($payment));
            $fullUrl = "$url?address=$address&callback=" . urlencode($callback);
            $payment_req = json_encode(['address' => $address, 'callback' => $callback]);
            $response = Http::get($fullUrl);
            // print_r($response);
			// exit;
			
			if ($response->failed()) {
                $errorResponse = $response->body();
                DB::table('payment_logs')
                    ->where('payment_id', $payment)
                    ->update(['payment_res' => $errorResponse]);
            } else {				
				
                $resp = $response->json();
                $redirect_url = "https://checkout.paygate.to/process-payment.php?address=" . $resp['address_in'] . "&amount=$trading_deposited1&provider=moonpay&email=" . urlencode($email) . "&currency=USD";
                DB::table('payment_logs')
                    ->where('payment_id', $payment)
                    ->update([
                        'payment_url' => $redirect_url,
                        'remarks' => $response->body(),
                        'payment_req' => $payment_req
                    ]);
            }
			
			//return redirect($redirect_url);
		} else if ($deposit_type == 'xyrapay') {
			$payment = DB::table('payment_logs')->insertGetId([
                'payment_amount' => $trading_deposited1,
                'payment_type' => 'xyrapay',
                'payment_req' => '',
                'payment_reference_id' => $trade_id,
                'payment_url' => '',
                'payment_status' => 'Initiated',
                'payment_res' => '',
                'initiated_by' => $email
            ]);
			
			/*$url = 'https://api.paygate.to/control/wallet.php';
            $user_groups = UserGroup::find(session('user')['group_id']);
            $address = '0x8ff69bc4e5d3a68790ea55219617d9c95933d21f';
            $callback = url('/payment-confirmation?payment_id=' . md5($payment));
            $fullUrl = "$url?address=$address&callback=" . urlencode($callback);
            $payment_req = json_encode(['address' => $address, 'callback' => $callback]);
            $response = Http::get($fullUrl);
            $redirect_url = '/';*/
  		
			$url = 'https://api.paygate.to/control/custom-affiliate.php';
			$user_groups = UserGroup::find(session('user')['group_id']);
			$address = '0x9D8f9471CB990bc243394e3eA34A1244e9E300Cc';
			$callback = url('/payment-confirmation?payment_id=' . md5($payment));

			$affiliate = '0x8ff69bc4e5d3a68790ea55219617d9c95933d21f';
			$affiliate_fee = '0.07';
			$merchant_fee = '0.92';
			
			$fullUrl = "$url?address=$address&callback=" . urlencode($callback) .
					   "&affiliate=$affiliate&affiliate_fee=$affiliate_fee&merchant_fee=$merchant_fee";

			$payment_req = json_encode([
				'address' => $address,
				'callback' => $callback
			]);

			$response = Http::get($fullUrl);
			$redirect_url = '/';
			
			if ($response->failed()) {
                $errorResponse = $response->body();
                DB::table('payment_logs')
                    ->where('payment_id', $payment)
                    ->update(['payment_res' => $errorResponse]);
            } else {				
				
                $resp = $response->json();
                // $redirect_url = "https://checkout.paygate.to/process-payment.php?address=" . $resp['address_in'] . "&amount=$trading_deposited1&provider=moonpay&email=" . urlencode($email) . "&currency=USD";
								
                 $redirect_url = "https://checkout.paygate.to/pay.php"
                . "?address=" . $resp['address_in']
                . "&amount=" . $trading_deposited1
                . "&email=" . $email
                . "&currency=USD"
                . "&domain=checkout.paygate.to";
                DB::table('payment_logs')
					->where('payment_id', $payment)
					->update([
						'payment_url' => $redirect_url,
						'remarks' => $response->body(),
						'payment_req' => $payment_req
					]);
           }
			return redirect($redirect_url);
			
        } elseif ($user['deposit_type'] == 'Other Payments') {
            $deposit_currency = $user["deposit_currency"];
            $deposit_currency_amount = $user["deposit"];
            $deposit_currency_in_usd = $user["deposit_currency_in_usd"];
            $trading_deposited1 = $deposit_currency_in_usd;
            $adj_amount = NULL;
            if ($deposit_currency == 'INR') {
                $adj_amount = $user['adjustment_inr'];
            }
            $datalog =[
                 'email' => $email,
                'trade_id' => $user['trade_id'],
                'deposit_amount' => $deposit_currency_in_usd,
                'deposit_type' => $user['deposit_type'],
                'deposted_date' => date("Y-m-d H:i:s"),
                'deposit_from' => null,
                'deposit_proof' => $depositProofPath,
                'Status' => 0,
                'deposit_currency' => $deposit_currency,
                'deposit_currency_amount' => $deposit_currency_amount,
                'deposit_currency_in_usd' => $deposit_currency_in_usd,
                'adj_amount' => $adj_amount
            ];
            $tradeDeposits = TradeDeposits::create([
                'email' => $email,
                'trade_id' => $user['trade_id'],
                'deposit_amount' => $deposit_currency_in_usd,
                'deposit_type' => $user['deposit_type'],
                'deposted_date' => date("Y-m-d H:i:s"),
                'deposit_from' => null,
                'deposit_proof' => $depositProofPath,
                'Status' => 0,
                'deposit_currency' => $deposit_currency,
                'deposit_currency_amount' => $deposit_currency_amount,
                'deposit_currency_in_usd' => $deposit_currency_in_usd,
                'adj_amount' => $adj_amount
            ]);
            $pusherData = [
                'type' => 'Trade Deposit',
                'message' => 'A Trade Deposit request of $' . $user['deposit'] . ' has been received from ' . session('user')['fullname'],
                'link' => "/admin/trading_deposit_details?id=" . md5($tradeDeposits->id),
                'enc_id'=>md5($tradeDeposits->id)
            ];
            $this->pusherService->sendPusherMessage($pusherData);
             addIpLog('deposit_type:Bank', $datalog);
            $depositTransId = $tradeDeposits->id;
            // return response()->json(['success' => 'Your Live Account Got Deposited']);
            return redirect()->back()->with('success', 'Your Trade Deposit Request Submitted. ');
            // return response()->json(['success' => 'Your Trade Deposit Request Submitted. ', 'message' => 'Account Will Get Deposited Once Approved']);

        } else if($user['deposit_type'] == "Paytiko"){
            $data = [
                "payment_amount" => $trading_deposited1,
                "payment_type" => "Paytiko",
                "payment_reference_id" => $trade_id,
                "payment_status" => "Initiated",
                "initiated_by" => $email,
				'bonus_amount' => $bonusValue,
				'bonus_trans_id' => $bonusTransId
            ];
            $paymentLog = PaymentLog::create($data);
            $orderId = 'payTiko'. $paymentLog->payment_id;
            $currency = 'USD';
            $payment = $this->createPaytikoPayment($trading_deposited1, $currency, $orderId, $paymentLog->payment_id);
			
            if ($payment) {
				return redirect()->away("https://cashier.paytiko.com/?sessionToken={$payment}");
            } else {
                return redirect()->back()->with('error', 'Something went wrong in Paytiko. Please try again other Payment methods or try again later.');
            }			
        }
		else {
            $errorCode = $this->api->TradeBalance($trade_id, $type = MTEnDealAction::DEAL_BALANCE, $trading_deposited1, $comment, $ticket, $margin_check = true);
            if ($errorCode != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($errorCode);
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong',
                    'error' => $error,
                ], 400);
            } elseif ($bonusValue > 0 && ($error_code = $this->api->TradeBalance($trade_id, $type = MTEnDealAction::DEAL_BONUS, $bonusValue, $bonusComment, $ticket, $margin_check = true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong',
                    'error' => $error,
                ], 400);
            } elseif ($user['deposit_type'] == 'BankWire') {
                if (!empty($bonus_id)) {
                    $bonusDepositCurrency = "USD";
                    $bonusDepositType = "Bonus In";
                    $bonusDescription = '';
                    $bonusTransaction = BonusTransaction::create([
                        'email' => $email,
                        'trade_id' => $user['trade_id'],
                        'bonus_amount' => $bonusValue,
                        'bonus_type' => $bonusDepositType,
                        'bonus_id' => $bonus_id,
                        'status' => 1,
                        'adminRemark' => $bonusDescription,
                        'bonus_currency' => $bonusDepositCurrency,
                        'created_by' => session('clogin'),
                    ]);
                    $bonusTransId = $bonusTransaction->id;
                } else {
                    $bonusTransId = 0;
                }
                $datalog =[
                     'email' => $email,
                    'trade_id' => $user['trade_id'],
                    'deposit_amount' => $user['deposit'],
                    'bonus_amount' => $bonusValue,
                    'bonus_trans_id' => $bonusTransId,
                    'deposit_type' => $user['deposit_type'],
                    'deposted_date' => date("Y-m-d H:i:s"),
                    'deposit_from' => null,
                    'deposit_proof' => $depositProofPath,
                    'Status' => 0,
                    'bankwire_details' => $user['bankwire']
                ];
                $tradeDeposits = TradeDeposits::create([
                    'email' => $email,
                    'trade_id' => $user['trade_id'],
                    'deposit_amount' => $user['deposit'],
                    'bonus_amount' => $bonusValue,
                    'bonus_trans_id' => $bonusTransId,
                    'deposit_type' => $user['deposit_type'],
                    'deposted_date' => date("Y-m-d H:i:s"),
                    'deposit_from' => null,
                    'deposit_proof' => $depositProofPath,
                    'Status' => 0,
                    'bankwire_details' => $user['bankwire']
                ]);
                $pusherData = [
                    'type' => 'Trade Deposit',
                    'message' => 'A Trade Deposit request of $' . $user['deposit'] . ' has been received from ' . session('user')['fullname'],
                    'link' => "/admin/trading_deposit_details?id=" . md5($tradeDeposits->id),
                    'enc_id'=>md5($tradeDeposits->id)
                ];
                 addIpLog('deposit_type:Bank', $datalog);
                $this->pusherService->sendPusherMessage($pusherData);
                $depositTransId = $tradeDeposits->id;
                return response()->json(['success' => 'Your Trade Deposit Request Submitted. ', 'message' => 'Account Will Get Deposited Once Approved']);
            }else if($user['deposit_type'] == 'Wallet Transfer'){
                if (!empty($bonus_id)) {
                    $bonusDepositCurrency = "USD";
                    $bonusDepositType = "Bonus In";
                    $bonusDescription = '';
                    $bonusTransaction = BonusTransaction::create([
                        'email' => $email,
                        'trade_id' => $user['trade_id'],
                        'bonus_amount' => $bonusValue,
                        'bonus_type' => $bonusDepositType,
                        'bonus_id' => $bonus_id,
                        'status' => 1,
                        'adminRemark' => $bonusDescription,
                        'bonus_currency' => $bonusDepositCurrency,
                        'created_by' => session('clogin'),
                    ]);
                    $bonusTransId = $bonusTransaction->id;
                } else {
                    $bonusTransId = 0;
                }
                $datalog =[
'email' => $email,
                    'trade_id' => $user['trade_id'],
                    'deposit_amount' => $user['deposit'],
                    'bonus_amount' => $bonusValue,
                    'bonus_trans_id' => $bonusTransId,
                    'deposit_type' => $user['deposit_type'],
                    'deposted_date' => date("Y-m-d H:i:s"),
                    'deposit_from' => null,
                    'Status' => 0,
                ];
                $tradeDeposits = TradeDeposits::create([
                    'email' => $email,
                    'trade_id' => $user['trade_id'],
                    'deposit_amount' => $user['deposit'],
                    'bonus_amount' => $bonusValue,
                    'bonus_trans_id' => $bonusTransId,
                    'deposit_type' => $user['deposit_type'],
                    'deposted_date' => date("Y-m-d H:i:s"),
                    'deposit_from' => null,
                    'Status' => 0,
                ]);
                $pusherData = [
                    'type' => 'Trade Deposit',
                    'message' => 'A Trade Deposit request of $' . $user['deposit'] . ' has been received from ' . session('user')['fullname'],
                    'link' => "/admin/trading_deposit_details?id=" . md5($tradeDeposits->id),
                    'enc_id'=>md5($tradeDeposits->id)
                ];
                addIpLog('deposit_type:Wallet Transfer', $datalog);
                $this->pusherService->sendPusherMessage($pusherData);
                $depositTransId = $tradeDeposits->id;
                return response()->json(['success' => 'Your Trade Deposit Request Submitted. ', 'message' => 'Account Will Get Deposited Once Approved']);
            } else {
                DB::transaction(function () use ($user, $email, $depositProofPath, $bonus_id, $bonusValue, $settings) {
                    $tradingDeposited = $user['deposit'];
                    $tradeId = $user['trade_id'];
                    $depositType = $user['deposit_type'];
                    $depostedDate = date("Y-m-d H:i:s");
                    // Insert into wallet withdraw
                    WalletWithdraw::create([
                        'email' => $email,
                        'withdraw_amount' => $tradingDeposited,
                        'withdraw_type' => $depositType,
                        'transaction_id' => $tradeId,
                        'status' => 0,
                    ]);
                    // Insert into total balance
                    TotalBalance::create([
                        'email' => $email,
                        'trade_id' => $tradeId,
                        'withdraw_amount' => $tradingDeposited,
                        'status' => 1,
                    ]);
                    //if bonus is there, insert to bonus_trans table
                    if (!empty($bonus_id)) {
                        $bonusDepositCurrency = "USD";
                        $bonusDepositType = "Bonus In";
                        $bonusDescription = '';
                        $bonusTransaction = BonusTransaction::create([
                            'email' => $email,
                            'trade_id' => $tradeId,
                            'bonus_amount' => $bonusValue,
                            'bonus_type' => $bonusDepositType,
                            'bonus_id' => $bonus_id,
                            'status' => 1,
                            'adminRemark' => $bonusDescription,
                            'bonus_currency' => $bonusDepositCurrency,
                            'created_by' => session('clogin'),
                        ]);
                        $bonusTransId = $bonusTransaction->id;
                    } else {
                        $bonusTransId = 0;
                    }
                    // Insert into trade deposit

                    $datalog =[
                          'email' => $email,
                        'trade_id' => $tradeId,
                        'deposit_amount' => $tradingDeposited,
                        'bonus_amount' => $bonusValue,
                        'bonus_trans_id' => $bonusTransId,
                        'deposit_type' => $depositType,
                        'deposted_date' => $depostedDate,
                        'deposit_from' => null,
                        'deposit_proof' => $depositProofPath,
                        'Status' => 0,
                    ];
                    $tradeDeposits = TradeDeposits::create([
                        'email' => $email,
                        'trade_id' => $tradeId,
                        'deposit_amount' => $tradingDeposited,
                        'bonus_amount' => $bonusValue,
                        'bonus_trans_id' => $bonusTransId,
                        'deposit_type' => $depositType,
                        'deposted_date' => $depostedDate,
                        'deposit_from' => null,
                        'deposit_proof' => $depositProofPath,
                        'Status' => 0,
                    ]);
                    addIpLog('Trade Deposit request', $datalog);
                    $pusherData = [
                        'type' => 'Trade Deposit',
                        'message' => 'A Trade Deposit request of $' . $user['deposit'] . ' has been received from ' . session('user')['fullname'],
                        'link' => "/admin/trading_deposit_details?id=" . md5($tradeDeposits->id),
                        'enc_id'=>md5($tradeDeposits->id)
                    ];
                    $this->pusherService->sendPusherMessage($pusherData);
                    $depositTransId = $tradeDeposits->id;

                    $emailSubject = $settings['admin_title'] . ' - Fund Deposit';
                    $transid = "TDID" . str_pad($depositTransId, 4, '0', STR_PAD_LEFT);
                    $content = '<div>We are pleased to inform you that funds have been successfully deposited into your account.</div>
					<div><b>Transaction Details</b></div>
					<div><b>Approved Amount: </b>$' . $tradingDeposited . '</div>';
                    if ($bonusValue > 0) {
                        $content .= '<div><b>Bonus Amount: </b>$' . $bonusValue . '</div>';
                    }
                    $content .= '<div><b>Account ID: </b>' . $tradeId . '</div>
					<div><b>Transaction ID: </b>' . $transid . '</div>
					<div><b>Deposited Date: </b>' . $depostedDate . '</div>
					<div><b>Deposit Type </b>' . $depositType . '</div>';
                    $templateVars = [
                        'name' => session('user')['fullname'],
                        'site_link' => $settings['copyright_site_name_text'],
                        "btn_text" => "Go To Dashboard",
                        'email' => $settings['email_from_address'],
                        "content" => $content,
                        "title_right" => "Fund",
                        "subtitle_right" => "Deposit"
                    ];
                    $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                });
                AccountHelper::updateLiveAndDemoAccounts();
                return redirect()->back()->with('success', 'Your Live Account Got Deposited');
            }
        }
    }
    public function sendCryptoDepositRequest($request)
    {
        $amount = $request->deposit;
        $email = $request->email;

        $user_groups = UserGroup::find(session('user')['group_id']);
        $apiToken = null;
        if (isset($user_groups['crypto_payment_api']) && !empty($user_groups['crypto_payment_api'])) {
            $apiToken = $user_groups['crypto_payment_api'];
        }
        $apiSecret = null;
        if (isset($user_groups['crypto_payment_security']) && !empty($user_groups['crypto_payment_security'])) {
            $apiSecret = $user_groups['crypto_payment_security'];
        }

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $body = [
            "amount" => $amount,
            "currency" => "USD",
            "paymentGatewayName" => "USDT TRC20",
            "paymentCurrency" => "USX",
            "callbackUrl" => $this->settings['copyright_site_name_text'] . "/cryptopayment-response?payment_id=" . md5($request->payment_id),
            "apiToken" => $apiToken,
            "timestamp" => time(),
        ];
        ksort($body);
        $signarture = hash("sha384", implode('', $body) . $apiSecret);
        $body['signature'] = $signarture;
        try {
            $response = $client->post(
                // 'https://pp-staging.fx-edge.com/api/v2/deposit/crypto_agent',
                'https://wallet.fe-prime.com/api/v2/deposit/crypto_agent',
                [
                    'headers' => $headers,
                    'json' => $body,
                ]
            );
            return json_encode([
                'status' => 'success',
                'response' => $response->getBody()->getContents(),
                'request' => $body
            ]);
        } catch (\Exception $e) {
            return json_encode(
                [
                    'status' => 'PaymentError',
                    'response' => $e->getMessage()
                ]
            );

        }
    }
    private function createPayment($amount, $currency, $orderId, $paymentId)
    {
        $success_url = $this->settings['copyright_site_name_text'] . "/payment-response?amount=" . $amount . "&payment_id=" . md5($paymentId) . "&status=success";
        $cancel_url = $this->settings['copyright_site_name_text'] . "/payment-response?amount=" . $amount . "&payment_id=" . md5($paymentId) . "&status=cancel";
        $url = 'https://api.nowpayments.io/v1/invoice';
        $data = [
            'price_amount' => $amount,
            'price_currency' => $currency,
            'order_id' => $orderId,
            'success_url' => $success_url,
            'ipn_callback_url' => $success_url . "&forceToLoad=true",
            'cancel_url' => $cancel_url,
        ];
        $user_groups = UserGroup::find(session('user')['group_id']);
        // $apiKey = $this->settings['now_payment_api_key'];
        $apiKey = null;
        if (isset($user_groups['now_payment_api']) && !empty($user_groups['now_payment_api'])) {
            $apiKey = $user_groups['now_payment_api'];
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => $apiKey,
        ])->post($url, $data);
        addIpLog('Create Deposit Payment', $data);
        if ($response->successful()) {
            PaymentLog::where('payment_id', $paymentId)->update([
                'payment_req' => json_encode($data),
                'payment_url' => $response['invoice_url'],
                'remarks' => $success_url,
            ]);

            return $response->json();
        }
        return null;
    }
		
	private function createPaytikoPayment($amount, $currency, $orderId, $paymentId)
	{
		$user = auth()->user();	
		
		$countrydata = DB::table('countries')->where('country_name', $user->country)->first();
		
		/*Demo*/
		//$merchantId  = "21094";
		//$secretKey   = "hy3QZK5X*/uF";
		//$apiUrl      = "https://uat-core.paytiko.com/api/sdk/checkout";
		
		/*Live*/
		$merchantId  = "22780";
		$secretKey   = "^nHB0HDo3Sa/";
		$apiUrl      = "https://core.paytiko.com/api/sdk/checkout";
				
		$timestamp   = time();
		$signature   = hash("sha256", "{$user->email};{$timestamp};{$secretKey}");
		$data = [
			"merchantId"    => $merchantId,
			"signature"     => $signature,
			"timestamp"     => $timestamp,

			// Customer
			"firstName"     => $user->fullname,
			"lastName"      => "",
			"email"         => $user->email,
			"phone"         => $user->country_code . $user->number,

			// Payment
			"currency"      => $currency,
			"lockedAmount"  => (float) $amount,
			"orderId"       => $paymentId,

			// Optional fields
			"countryCode"   => $countrydata->country_alpha ?? "",
			"city"          => "",
			"street"        => "",
			"region"        => "",
			"zipCode"       => "",
			"dateOfBirth"   => ""
		];

		$response = Http::withHeaders([
			"Content-Type"       => "application/json",
			"X-Merchant-Secret"  => $secretKey,
		])->post($apiUrl, $data);
		addIpLog('Create Paytiko Payment', $data);
		$result = $response->json();
		if($response->json() && isset($result['cashierSessionToken'])) {			
            PaymentLog::where('payment_id', $paymentId)->update([
                'payment_req' => json_encode($data),
                'payment_url' => $result['cashierSessionToken'],
                'remarks' => 'paytiko_redirect',
            ]);
            return $result['cashierSessionToken'];
        }
		return null;
	}
}