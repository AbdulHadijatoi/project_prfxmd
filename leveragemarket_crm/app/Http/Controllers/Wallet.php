<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\ClientBankDetails;
use Illuminate\Http\Request;
use App\Models\WalletDeposit;
use App\Models\WalletWithdraw;
use App\Models\ClientWallets;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LiveAccount;
use App\Models\PaymentLog;
use App\Models\TotalBalance;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Payment;
use Carbon\Carbon;
use App\Models\UserGroup;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Services\PusherService;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransfer;

class Wallet extends Controller
{
    protected $settings;
    protected $paymentController;
    protected $pusherService;

    public function __construct(Payment $paymentController, PusherService $pusherService)
    {
        $this->settings = settings();
        $this->paymentController = $paymentController;
        $this->pusherService = $pusherService;
    }
    public function index()
    {
        $email = auth()->user()->email;
        $wallet_history = $this->getWalletHistory($email);
        $wallet_balance = $this->getWalletBalance($email);
        addIpLog('Wallet_view', $email);
        return view('wallet', compact('wallet_balance', 'wallet_history'));
    }
	
	public function activateWallet(Request $request)
	{
		$action = $request->query('activate_wallet');
		$email = auth()->user()->email;
		$user = User::where('email', $email)->first();
		if (!$user) {
			return response()->json(false);
		}
		// Update wallet (enable/disable)
		$user->wallet_enabled = ($action === 'enable') ? 1 : 0;
		$user->save();
		// Refresh user session
		$user->refresh(); // simple, clean
		Auth::setUser($user); 
		return response()->json(true);
	}
	
    public function getWalletHistory($email)
    {
        // Fetch deposit history
        $deposit_history = WalletDeposit::where('email', $email)
            ->select('id as raw_id', 'transaction_id', 'deposit_type as transfer_type', 'status', 'deposit_amount as amount', \DB::raw("'deposit' as type"), 'deposted_date as date_added')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
        // Fetch withdrawal history
        $withdrawal_history = WalletWithdraw::where('email', $email)
            ->select('id as raw_id', 'transaction_id', 'withdraw_type as transfer_type', 'status', 'withdraw_amount as amount', \DB::raw("'withdrawal' as type"), 'withdraw_date as date_added')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
        // Merge and sort
        $wallethistory = $deposit_history->concat($withdrawal_history)->sortByDesc('date_added')->take(10);

        return $wallethistory;
    }
    public function getWalletBalance($email)
    {
        $totalDeposit = WalletDeposit::where('email', $email)->where('status', 1)->sum('deposit_amount');
        $totalWithdraw = WalletWithdraw::where('email', $email)->whereIn('status', [0, 1])->sum('withdraw_amount');

        $walletBalance = (float) $totalDeposit - (float) $totalWithdraw;
        return $walletBalance;
    }
    public function storeClientWallet(Request $request)
    {
        $otp_type = 'Wallet_Creation_Otp';
        if (!session()->has($otp_type) || !request()->has('otp')) {
            return response()->json(['success' => false,'message'=>'Please verify with OTP and proceed.']);
        } elseif (request('otp') != session($otp_type)) {
           return response()->json(['success' => false,'message'=>'Invalid OTP, Please try again.']);
        }
        $request->validate([
            'wallet_name' => 'required|string|max:255',
            'wallet_currency' => 'required|string|max:10',
            'wallet_network' => 'required|string|max:255',
            'wallet_address' => 'required|string|max:255',
            'status' => 'required',
        ]);
        $datalog = [
                     'wallet_name' => $request->wallet_name,
            'wallet_currency' => $request->wallet_currency,
            'wallet_network' => $request->wallet_network,
            'wallet_address' => $request->wallet_address,
            'created_by' => session('clogin'),
            'user_id' => session('clogin'),
            'status' => $request->status,
                ];
        ClientWallets::create([
            'wallet_name' => $request->wallet_name,
            'wallet_currency' => $request->wallet_currency,
            'wallet_network' => $request->wallet_network,
            'wallet_address' => $request->wallet_address,
            'created_by' => session('clogin'),
            'user_id' => session('clogin'),
            'status' => $request->status,
        ]);
         addIpLog('Wallet_create',$datalog );
       
        return response()->json(['success' => true]);
    }
    public function updateStatus(Request $request)
    {
        $otp_type = 'Wallet_Update_Otp';
        if (!session()->has($otp_type) || !request()->has('otp')) {
            return response()->json(['success' => false,'message'=>'Please verify with OTP and proceed.']);
        } elseif (request('otp') != session($otp_type)) {
           return response()->json(['success' => false,'message'=>'Invalid OTP, Please try again.']);
        }
        $request->validate([
            'toggle_wallet' => 'required',
            'id' => 'required|string',
        ]);
         $datalog = [
                   'id' => $request->id,
          
                ];
        $wallet = ClientWallets::where(DB::raw('md5(client_wallet_id)'), $request->id)->first();
        if ($wallet) {
            $wallet->status = $wallet->status == 0 ? 1 : 0;
            $wallet->save();
            addIpLog('Walet Update',$datalog);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Wallet not found.'], 404);
    }
    public function showDepositForm()
    {
        $user = auth()->user();	
        $email = auth()->user()->email;
        $user_groups = UserGroup::find(session('user')['group_id']);
        $kyc_user = User::where('email', $email)->first();
        $settings = $this->settings;
        $liveaccount_details = LiveAccount::with('accountType')
			->where('status', 'active')
            ->where('email', $email)
            ->get();
        $totals = LiveAccount::where('email', $email)->where('status', 'active')
            ->select(DB::raw('SUM(equity) as equity'), DB::raw('SUM(balance) as balance'))
            ->first();
		addIpLog('Wallet Deposit', $email);
        return view('wallet_deposit', compact('user','kyc_user', 'settings', 'liveaccount_details', 'totals', 'user_groups'));
    }
    public function showWithdrawalForm()
    {
          $user = auth()->user();	
        session()->forget('getPOotp');
        session()->forget('Wallet_Withdrawal_Otp');
        session()->forget('USDT_Withdrawal_Otp');
        session()->forget('Bank_Withdrawal_Otp');
        session()->forget('Other_Withdrawal_Otp');
        session()->forget('Bank_Details_Otp');
        session()->forget('Wallet_Creation_Otp');
        $email = auth()->user()->email;
        $client_wallets = ClientWallets::where('user_id', $email)
            ->where('status', 1)
            ->get();
        $client_banks = ClientBankDetails::where('userId', $email)
            ->where('status', 'success')
            ->get();
        $settings = $this->settings;
        $liveaccount_details = LiveAccount::with('accountType')
			->where('status', 'active')
            ->where('email', $email)
            ->get();
        $totals = LiveAccount::where('email', $email)->where('status', 'active')
            ->select(DB::raw('SUM(equity) as equity'), DB::raw('SUM(balance) as balance'))
            ->first();
        $total_wd = WalletDeposit::where('email', $email)
            ->where('status', 1)
            ->sum('deposit_amount');
        $total_ww = WalletWithdraw::where('email', $email)  
			->whereIn('status', [0, 1])
            ->sum('withdraw_amount');
        $wallet_balance = (float) $total_wd - (float) $total_ww;
				
        $walletBalance = (float) $total_wd - (float) $total_ww;
			
        return view('wallet_withdrawal', compact('client_wallets','user', 'client_banks', 'settings', 'liveaccount_details', 'totals', 'wallet_balance', 'walletBalance'));
    }
    public function deposit(Request $request)
    {
        $email = session('clogin');
        try {
            $trading_deposited1 = $request->input('deposit');
            // $email = $request->input('email');
            $deposit_type = $request->input('deposit_type');
            $date = date("Y-m-d");

            $existing = DB::table('wallet_deposit')
                ->where('email', $email)
                ->where('deposit_amount', $trading_deposited1)
                ->where('deposit_type', $deposit_type)
                ->whereDate('deposted_date', $date)
                ->where('Status', 0)
                ->count();
            if ($existing > 0) {
                return redirect()->back()->with('error', "There is already a pending deposit for this amount today. Please wait for confirmation or reach out to support");
            }
            if ($deposit_type == "Paytiko"){
				$data = [
					"payment_amount" => $trading_deposited1,
					"payment_type" => "Paytiko",
					"payment_reference_id" => 'Wallet',
					"payment_status" => "Initiated",
					"initiated_by" => $email
				];
				$paymentLog = PaymentLog::create($data);
				$orderId = 'payTiko'. $paymentLog->payment_id;
				$currency = 'USD';
				$payment = $this->createPaytikoPayment($trading_deposited1, $currency, $orderId, $paymentLog->payment_id);
				addIpLog('Wallet Deposit Submit', $data);
				if ($payment) {
					return redirect()->away("https://cashier.paytiko.com/?sessionToken={$payment}");
				} else {
					return redirect()->back()->with('error', 'Something went wrong in Paytiko. Please try again other Payment methods or try again later.');
				}			
			} else if ($deposit_type == "Now Payment") {
                $data = [
                    "payment_amount" => $trading_deposited1,
                    "payment_type" => "NowPayment",
                    "payment_reference_id" => "Wallet",
                    "payment_status" => "Initiated",
                    "initiated_by" => $email
                ];
                $paymentLog = PaymentLog::create($data);
                $orderId = 'nowPay' . $paymentLog->id;
                $currency = 'USD';
                $payment = $this->createPayment($trading_deposited1, $currency, $orderId, $paymentLog->payment_id);
				addIpLog('Wallet Deposit Submit', $data);
                if ($payment) {
                    return redirect($payment['invoice_url']);
                } else {
                    return redirect()->back()->with('error', 'Something went wrong in NowPayment. Please try again other Payment methods or try again later.');
                }
            } else if ($deposit_type == 'match2pay') {
                $data = [
                    "payment_amount" => $request->deposit,
                    "payment_type" => "Crypto Payment",
                    "payment_reference_id" => "Wallet",
                    "payment_status" => "Initiated",
                    "initiated_by" => $email
                ];
                $paymentLog = PaymentLog::create($data);
                $request->payment_id = $paymentLog->payment_id;
                $payment = $this->sendCryptoDepositRequest($request);
                $payment = json_decode($payment, true);
                if ($payment['status'] == 'PaymentError') {
                    PaymentLog::where('payment_id', $paymentLog->payment_id)->update([
                        'remarks' => $payment['response'],
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => $payment['response']
                    ]);
                } else {
                    $payment_response = json_decode($payment['response'], true);
                    PaymentLog::where('payment_id', $paymentLog->payment_id)->update([
                        'payment_req' => json_encode($payment['request']),
                        'payment_url' => $payment_response['checkoutUrl'],
                        'remarks' => $payment['response']
                    ]);
                    return response()->json([
                        'status' => 'success',
                        'checkoutUrl' => $payment_response['checkoutUrl'],
                        'paymentId' => md5($paymentLog->payment_id)
                    ]);
                }
            } else if ($deposit_type == 'USDT Deposit') {
                $depositProofPath = null;
                if ($request->hasFile('deposit_proof')) {
                    $validator = Validator::make($request->all(), [
                        'deposit_proof' => 'required|file|mimes:pdf,png,jpeg|max:2048',
                    ]);
                    if ($validator->fails()) {
                        $firstError = $validator->errors()->first();
                        return redirect()->back()->with('error', $firstError);
                    }
                    $depositProofPath = $request->file('deposit_proof')->store('deposit_proofs', 'public');
                }
                $walletDeposit = WalletDeposit::create([
                    'email' => $email,
                    'deposit_amount' => $trading_deposited1,
                    'deposit_type' => $deposit_type,
                    'currency_type' => "USD",
                    'Status' => 0,
                    'deposit_proof' => $depositProofPath,
                    'usdt_wallet_id' => $request->get('usdt_wallet_id'),
                    'usdt_wallet_qr' => $request->get('usdt_wallet_qr'),

                ]);
                $pusherData = [
                    'type' => 'Wallet Deposit',
                    'message' => 'A wallet deposit request of $' . $trading_deposited1 . ' has been received from ' . session('user')['fullname'],
                    'link' => "/admin/wallet_deposit_details?id=" . md5($walletDeposit->id),
                    'enc_id' => md5($walletDeposit->id)
                ];
                $this->pusherService->sendPusherMessage($pusherData);
                return redirect()->back()->with('success', 'Deposited $' . $trading_deposited1 . ' is awaiting approval. Once approved, it will be credited to your wallet.');
            } else if ($deposit_type == 'usdc-polygon') {
                $data = [
                    "payment_amount" => $request->wallet_amount,
                    "payment_type" => "payissa",
                    "payment_status" => "Initiated",
                    "initiated_by" => session('clogin'),
                ];
                $paymentLog = PaymentLog::create($data);
                $payment_id = $paymentLog->payment_id;


                $url = 'https://api2.payissa.com/control/wallet.php';
                $user_groups = UserGroup::find(session('user')['group_id']);
                $address = $user_groups['payissa_wallet'];
                $callback = $this->settings['copyright_site_name_text'] . '/payment-confirmation?payment_id=' . md5($payment_id);
                $fullUrl = $url . '?address=' . $address . '&callback=' . urlencode($callback);
                $payment_reference_id = "wallet";
                $payment_req = json_encode(['address' => $address, 'callback' => $callback]);


                $response = Http::get($fullUrl);
                if ($response->failed()) {
                    $errorResponse = $response->body();
                    DB::table('payment_logs')->where('payment_id', $payment_id)->update([
                        'payment_res' => $errorResponse,
                    ]);
                    return redirect()->back()->with('error', $errorResponse);
                }
                $resp = $response->json();
                $redirect_url = "https://checkout2.payissa.com/process-payment.php?address=" . $resp['address_in'] . "&amount=" . $request->input('wallet_amount') . "&provider=wert&email=" . urlencode(auth()->user()->email) . "&currency=USD";
                DB::table('payment_logs')->where('payment_id', $payment_id)->update([
                    'payment_url' => $redirect_url,
                    'remarks' => $response->body(),
                    'payment_req' => $payment_req,
                    'payment_reference_id' => $payment_reference_id,
                ]);
                return redirect()->away($redirect_url);
            } else if ($deposit_type == 'xyrapay') {
				$data = [
                    "payment_amount" => $request->wallet_amount,
                    "payment_type" => "xyrapay",
                    "payment_status" => "Initiated",
                    "initiated_by" => session('clogin'),
                ];				
            } else if ($deposit_type == 'paygate') {
				$data = [
                    "payment_amount" => $request->wallet_amount,
                    "payment_type" => "paygate",
                    "payment_status" => "Initiated",
                    "initiated_by" => session('clogin'),
                ];				
			} else {
				$datalog = [
                    'email' => $email,
                    'deposit_amount' => $request->wallet_amount,
                    'deposit_type' => $deposit_type,
                    'currency_type' => "USD",
                    'Status' => 0,
                ];
                $walletDeposit = WalletDeposit::create([
                    'email' => $email,
                    'deposit_amount' => $request->wallet_amount,
                    'deposit_type' => $deposit_type,
                    'currency_type' => "USD",
                    'Status' => 0,
                ]);
                $pusherData = [
                    'type' => 'Wallet Deposit',
                    'message' => 'A wallet deposit request of $' . $request->wallet_amount . ' has been received from ' . session('user')['fullname'],
                    'link' => "/admin/wallet_deposit_details?id=" . md5($walletDeposit->id),
                    'enc_id' => md5($walletDeposit->id)
                ];
                $this->pusherService->sendPusherMessage($pusherData);
				addIpLog('Wallet Deposit Submit', $datalog);
                return redirect()->back()->with('success', 'Deposited $' . $request->wallet_amount . ' is awaiting approval. Once approved, it will be credited to your wallet.');
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return redirect()->back()->with('error', $error);
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
        if ($response->successful()) {
            PaymentLog::where('payment_id', $paymentId)->update([
                'payment_req' => json_encode($data),
                'payment_url' => $response['invoice_url'],
                'remarks' => $success_url,
            ]);
            addIpLog('Wallet Deposit Submit', $data);
            return $response->json();
        }
        return null;
    }
    //function for cryptochill payment
    public function processPayment(Request $request)
    {
        if ($request->has('paymentGateway')) {
            $depositTo = $request->input('deposit_to');
            if (!$depositTo) {
                return response()->json(['message' => 'Deposit designation missing..!'], 400);
            }
            $amount = $request->input('amount');
            $tradeId = $request->input('trade_id');
            $time = $request->input('time');
            $comment = "Deposit";
            $depositType = $request->input('deposit_type');
            $email = auth()->user()->email;
            try {
                if ($depositTo == "wallet") {
                    $callbackData = json_encode($request->input('data'));
                    $callbackCode = json_encode($request->input('code'));
                    $walletDeposit = new WalletDeposit();
                    $walletDeposit->email = $email;
                    $walletDeposit->deposit_type = $depositType;
                    $walletDeposit->deposit_amount = $amount;
                    $walletDeposit->company_bank = $depositType;
                    $walletDeposit->transaction_id = $time;
                    $walletDeposit->Status = 1;
                    $walletDeposit->currency_type = 'USD';
                    $walletDeposit->callback_data = $callbackData;
                    $walletDeposit->callback_code = $callbackCode;
                    $walletDeposit->save();
                    $totalBalance = TotalBalance::Create(
                        [
                            'email' => $email,
                            'deposit_amount' => $amount
                        ]
                    );
                    $mailData = new \stdClass();
                    $mailData->payment_amount = $amount;
                    $mailData->fullname = session('user')['fullname'];
                    $mailData->payment_type = $depositType;
                    $mailData->created_at = $formattedDate = Carbon::parse($walletDeposit->created_at)->format('Y-m-d H:i:s');
                    $mailData->payment_reference_id = $time;
                    $this->paymentController->sendSuccessEmail($email, $amount, $mailData, $walletDeposit->id);
                    return response()->json(['status' => true, 'message' => 'Deposit successful!'], 200);
                }
            } catch (Exception $e) {
                return response()->json(['status' => false, 'message' => 'Something went wrong...!'], 500);
            }
        }
    }
    public function withdrawal(Request $request)
    {
        $otp_type = str_replace(' ', '_', trim($request->input('withdraw_type'))) . '_Otp';
        if (!session()->has($otp_type) || !request()->has('otp')) {
            return redirect()->back()->with('error', 'Please verify with OTP and proceed.');
        } elseif (request('otp') != session($otp_type)) {
            return redirect()->back()->with('error', 'Invalid OTP, Please try again.');
        }
        $validator = Validator::make($request->all(), [
            'withdraw_amount' => 'required|numeric|min:10',
            'withdraw_type' => 'required|string',
            'client_bank' => 'required'
        ]);
        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('error', $firstError);
        }

        $userEmail = auth()->user()->email;
        $withdrawAmount = $request->input('withdraw_amount');
        $withdrawType = str_replace('_', ' ', $request->input('withdraw_type'));
        $clientBank = $request->input('client_bank');
        $totalDeposits = WalletDeposit::where('email', $userEmail)
            ->where('status', 1)
            ->sum('deposit_amount');

        $totalWithdrawals = WalletWithdraw::where('email', $userEmail)
			->whereIn('status', [0, 1])
            ->sum('withdraw_amount');

        $walletBalance = (float) $totalDeposits - (float) $totalWithdrawals;
        if ($withdrawAmount > $walletBalance) {
            return redirect()->back()->with('error', 'Insufficient balance in your wallet.');
        }
         $datalog = [
                     'email' => $userEmail,
            'withdraw_amount' => $withdrawAmount,
            'withdraw_type' => $withdrawType,
            'client_bank' => $withdrawType == 'Bank Withdrawal' ? $clientBank : '',
            'wallet_id' => $withdrawType == 'Wallet Withdrawal' ? $clientBank : '',
            'status' => 0
                ];
        $walletWithdraw = WalletWithdraw::create([
            'email' => $userEmail,
            'withdraw_amount' => $withdrawAmount,
            'withdraw_type' => $withdrawType,
            'client_bank' => $withdrawType == 'Bank Withdrawal' ? $clientBank : '',
            'wallet_id' => $withdrawType == 'Wallet Withdrawal' ? $clientBank : '',
            'status' => 0
        ]);
        $pusherData = [
            'type' => 'Wallet Withdrawal',
            'message' => 'A wallet withdrawal request of $' . $withdrawAmount . ' has been received from ' . session('user')['fullname'],
            'link' => "/admin/wallet_withdrawal_details?id=" . md5($walletWithdraw->id),
            'enc_id' => md5($walletWithdraw->id)
        ];
        $this->pusherService->sendPusherMessage($pusherData);
        
        addIpLog('Wallet Deposit Request', $datalog);
        return redirect()->back()->with('success', 'Successfully requested $' . $withdrawAmount . ' from your wallet. You will get an email notification once approved.');
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
	
	public function walletTransfer(Request $request){

         $user = auth()->user();	
		$email = auth()->user()->email;
        $user_groups = UserGroup::find(session('user')['group_id']);
        $kyc_user = User::where('email', $email)->first();
        $settings = $this->settings;
        $totalDeposit = WalletDeposit::where('email', $email)
            ->where('status', 1)
            ->sum('deposit_amount');
        $totalWithdraw = WalletWithdraw::where('email', $email)
			->whereIn('status', [0, 1])
            ->sum('withdraw_amount');

        $walletBalance = $totalDeposit - $totalWithdraw;
         $datalogs = [
            "totalDeposit" => $totalDeposit,
            "totalWithdraw" => $totalWithdraw,
            "walletBalance" => $walletBalance,
        ];
        //$walletBalance = 2500; 
         addIpLog('Wallet Transfer Request', $datalogs);
        return view('wallet_transfer', compact('user','kyc_user', 'settings', 'user_groups', 'walletBalance'));
	}
	
	public function wallettransferto(Request $request){
		$email = auth()->user()->email;		
		$request->validate([
			'transfer_emailto' => 'required|email',
			'wallet_amount' => 'required|numeric|min:1',
		]);
		$user = auth()->user();
		$toUser = User::where('email', $request->transfer_emailto)->first();

		if (!$toUser) {
			return response()->json([
				'status' => 'error',
				'message' => 'Receiver email not found.'
			]);
		}
		
		/*Deposit to client wallet*/
		$walletDeposit = WalletDeposit::create([
			'email' => $request->transfer_emailto,
			'deposit_amount' => $request->wallet_amount,
			'deposit_type' => 'External Deposit',
			'currency_type' => $request->currency,
			'Status' => 1
		]);
		
		/*Withdraw to client wallet*/
		$walletWithdraw = WalletWithdraw::create([
            'email' => $email,
            'withdraw_amount' => $request->wallet_amount,
            'withdraw_type' => 'External Withdrawal',
			'withdrawal_currency' => $request->currency,
			'client_note' => 'Transfer to '.$request->transfer_emailto,
            'Status' => 1
        ]);
		
        $datalogs = [
         'wallet_from' => $email,
			'wallet_to' => $request->transfer_emailto,
			'transfer_currency' => $request->currency,
			'wallet_balance' => $request->walletBalance,
			'transfer_amount' => $request->wallet_amount,
			'transfer_date' => now(),
			'transfer_note' => $email.' Transfer to '.$request->transfer_emailto.' amount as $'.$request->wallet_amount,
			'status' => 'Success'
        ];
		/*Wallet Transfer*/
		WalletTransfer::create([
			'wallet_from' => $email,
			'wallet_to' => $request->transfer_emailto,
			'transfer_currency' => $request->currency,
			'wallet_balance' => $request->walletBalance,
			'transfer_amount' => $request->wallet_amount,
			'transfer_date' => now(),
			'transfer_note' => $email.' Transfer to '.$request->transfer_emailto.' amount as $'.$request->wallet_amount,
			'status' => 'Success'
		]);
		addIpLog('Wallet Transfer request', $datalogs);
		return response()->json([
			'status' => 'success',
			'message' => 'Transfer request submitted.'
		]);
	}
	
	public function wallettranscation(Request $request)
	{
		$user  = auth()->user();
		$email = $user->email;

		$from = $request->from;
		$to   = $request->to;
		$type = $request->filter_type;
		$paymode = $request->filter_paymode;

		/* ---------------- DATE FILTER ---------------- */
		if ($request->filter_duration == 'today') {
			$from = now()->startOfDay();
			$to   = now()->endOfDay();
		}

		if ($request->filter_duration == 'yesterday') {
			$from = now()->subDay()->startOfDay();
			$to   = now()->subDay()->endOfDay();
		}

		if ($request->filter_duration == 'week') {
			$from = now()->subDays(6)->startOfDay(); // last 7 days
			$to   = now()->endOfDay();
		}

		if ($request->filter_duration == 'month') {
			$from = now()->subDays(29)->startOfDay(); // last 30 days
			$to   = now()->endOfDay();
		}
		
		if ($request->filter_duration == 'customrange') {
			$from = $request->from;
			$to   = $request->to;
		}

		/* =========================================================
		   DEPOSIT (trade + wallet)
		========================================================== */
		$tradeDeposit = DB::table('trade_deposit')
			->select([
				'id',
				'status as Status',
				DB::raw("'Deposit' as transtype"),
				DB::raw('deposted_date as created_at'),
				DB::raw('deposit_type as particulars'),
				DB::raw('deposit_amount as valamount')
			])
			->whereNotIn('deposit_type', ['Wallet Transfer', 'Wallet Payments', 'W2A Deposit', 'A2A Transfer', 'Bonus Deposit'])
			->where('email', $email);

		$walletDeposit = DB::table('wallet_deposit')
			->select([
				'id',
				'Status',
				DB::raw("'Deposit' as transtype"),
				DB::raw('deposted_date as created_at'),
				DB::raw('deposit_type as particulars'),
				DB::raw('deposit_amount as valamount')
			])
			->whereNotIn('deposit_type', ['Wallet Transfer','A2A Transfer','A2W withdraw','A2W Deposit'])
			->where('email', $email);

		/* =========================================================
		   WITHDRAW (trade + wallet)
		========================================================== */
		$withdraw = DB::table('wallet_withdraw')
			->select([
				'id',
				'Status',
				DB::raw("'Withdrawal' as transtype"),
				DB::raw('withdraw_date as created_at'),
				DB::raw('withdraw_type as particulars'),
				DB::raw('withdraw_amount as valamount')
			])
			->whereIn('withdraw_type', ['Wallet Withdrawal', 'External Withdrawal', 'Wallet Withdrawal (Admin)'])
			->where('email', $email);
			
		$tradewithdraw = DB::table('trade_withdrawal')
			->select([
				'id',
				'Status',
				DB::raw("'Withdrawal' as transtype"),
				DB::raw('withdraw_date as created_at'),
				DB::raw('withdraw_type as particulars'),
				DB::raw('withdrawal_amount as valamount')
			])
			->whereIn('withdraw_type', ['Trade Withdrawal (Admin)'])
			->where('email', $email);

		/* =========================================================
		   TRANSFER
		========================================================== */
		$tradeDeposittrans = DB::table('trade_deposit')
			->select([
				'id',
				'status as Status',
				DB::raw("'Internal Transfer' as transtype"),
				DB::raw('deposted_date as created_at'),
				DB::raw('deposit_type as particulars'),
				DB::raw('deposit_amount as valamount')
			])
			->whereIn('deposit_type', ['Wallet Transfer', 'Wallet Payments', 'W2A Deposit', 'A2A Transfer'])
			->where('email', $email);

		$walletDeposittrans = DB::table('wallet_deposit')
			->select([
				'id',
				'Status',
				DB::raw("'Internal Transfer' as transtype"),
				DB::raw('deposted_date as created_at'),
				DB::raw('deposit_type as particulars'),
				DB::raw('deposit_amount as valamount')
			])
			->whereIn('deposit_type', ['Wallet Transfer','A2A Transfer','A2W withdraw','A2W Deposit'])
			->where('email', $email);
		

		/* =========================================================
		   APPLY DATE FILTER BEFORE UNION (FAST)
		========================================================== */
		if ($from && $to) {
			$tradeDeposit->whereBetween('deposted_date', [$from, $to]);
			$walletDeposit->whereBetween('deposted_date', [$from, $to]);
			$withdraw->whereBetween('withdraw_date', [$from, $to]);
			$tradewithdraw->whereBetween('withdraw_date', [$from, $to]);
			$tradeDeposittrans->whereBetween('deposted_date', [$from, $to]);
			$walletDeposittrans->whereBetween('deposted_date', [$from, $to]);
		}
		
		/* =========================================================
		   UNION BUILD
		========================================================== */
		
		if ($paymode == 'Wallet Deposit') {
			$union = $walletDeposit;
		} elseif ($paymode == 'Trade Deposit'){
			$union = $tradeDeposit;
		} else {
			$union = $tradeDeposit
				->unionAll($walletDeposit)
				->unionAll($withdraw)
				->unionAll($tradewithdraw)
				//->unionAll($transfer)
				->unionAll($walletDeposittrans)
				->unionAll($tradeDeposittrans);
		}	

		$ledgerQuery = DB::query()->fromSub($union, 'ledger');		
		if ($type) {
			$ledgerQuery->where('transtype', $type);
		}

		/* PAYMODE FILTER */
		if ($paymode && !in_array($paymode, ['Wallet Deposit','Trade Deposit'])) {
			$ledgerQuery->where('particulars', $paymode);
		}		

		/* =========================================================
		   PAGINATION
		========================================================== */
		$ledger = $ledgerQuery
			->orderByDesc('created_at')
			->paginate(10)
			->withQueryString();

		/* =========================================================
		   TOTALS
		========================================================== */
		$totalCredit = DB::table('trade_deposit')
			->where('email', $email)
			->whereNotIn('deposit_type', ['Wallet Transfer', 'Wallet Payments', 'W2A Deposit', 'A2A Transfer'])
			->sum('deposit_amount')
			+ DB::table('wallet_deposit')
			->where('email', $email)
			->whereNotIn('deposit_type', ['Wallet Transfer','A2A Transfer','A2W withdraw','A2W Deposit'])
			->sum('deposit_amount');

		$totalDebit = DB::table('wallet_withdraw')
			->where('email', $email)
			->whereIn('status', [1])
			->whereIn('withdraw_type', ['Wallet Withdrawal', 'External Withdrawal', 'Wallet Withdrawal (Admin)'])
			->sum('withdraw_amount');

		$totalTransferCredit = DB::table('trade_withdrawal')
			->where('email', $email)
			->whereIn('withdraw_type', ['A2A Transfer', 'A2W withdraw', 'Trade Withdrawal (Admin)'])
			->sum('withdrawal_amount')
			+ DB::table('wallet_withdraw')
			->where('email', $email)
			->whereIn('withdraw_type', ['W2A Deposit'])
			->sum('withdraw_amount');	
		
		return view('wallet_transactions', compact(
			'user',
			'ledger',
			'totalCredit',
			'totalDebit',
			'totalTransferCredit'
		));
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
			"countryCode"  => $countrydata->country_alpha ?? "",
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

		$result = $response->json();
		if($response->json() && isset($result['cashierSessionToken'])) {			
            PaymentLog::where('payment_id', $paymentId)->update([
                'payment_req' => json_encode($data),
                'payment_url' => $result['cashierSessionToken'],
                'remarks' => 'paytiko_redirect',
            ]);
            addIpLog('Paytiko Payment', $data);
            return $result['cashierSessionToken'];
        }
		return null;
	}

}
