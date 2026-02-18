<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Validator;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Services\PusherService;
use App\Models\Crypto;
use App\Models\P2PMerchant;
use App\Models\WalletDeposit;
use App\Models\WalletWithdraw;
use App\Models\ClientWallets;
use App\Models\ClientBankDetails;
use App\Models\P2POrders;
use App\Models\P2POrdershistory;
use App\Models\PaymentLog;

class P2PController extends Controller
{
	protected $settings;
    protected $pusherService;

    public function __construct(PusherService $pusherService)
    {
        $this->settings = settings();
        $this->pusherService = $pusherService;
    }
	
	public function generateMerchantCode() {
		$prefix = "LMP2P";
		$base = 10000;
		$userId = auth()->id();

		return $prefix . ($base + $userId);
	}
	
	public function generateOrderNo()
	{
		$date = now()->format('Ymd');
		$lastOrder = P2POrders::whereDate('created_at', today())
			->orderBy('id', 'desc')
			->first();
		$sequence = $lastOrder
			? str_pad(((int) substr($lastOrder->order_no, -6)) + 1, 6, '0', STR_PAD_LEFT)
			: '000001';
		return "ORD-{$date}-{$sequence}";
	}	
	
	public function getWalletBalance($email)
    {
        $totalDeposit = WalletDeposit::where('email', $email)
            ->where('status', 1)
            ->sum('deposit_amount');
        $totalWithdraw = WalletWithdraw::where('email', $email)
            ->where('status', 1)
            ->sum('withdraw_amount');
        $walletBalance = $totalDeposit - $totalWithdraw;
			addIpLog(' WalletBalance',  $walletBalance);
        return $walletBalance;
    }
	
	public function exchangeRate(Request $request)
	{
		$symbol = $request->asset . "/" . $request->fiat;

		$response = Http::get("https://api.twelvedata.com/exchange_rate", [
			'symbol' => $symbol,
			'apikey' => env('TWELVEDATA_API_KEY')
		]);

		if ($response->failed()) {
			return response()->json(['rate' => 0]);
		}

		return response()->json([
			'rate' => (float) ($response->json()['rate'] ?? 0)
		]);
	}
	
    public function p2pmarketplace(Request $request)
    {
		 $user = auth()->user();
		$email = auth()->user()->email;
        $pageTitle = "P2P Marketplace";
		$cryptolist = Crypto::where('status', 1)->orderBy('id', 'ASC')->get();
		$currency = DB::table('currencies')->where('status', 1)->get();		
		$providerlist = P2PMerchant::where('status', 1)->whereNotIn('email', [$email])->orderBy('id', 'ASC')->get();
		//$providerlist = P2PMerchant::where('status', 1)->orderBy('id', 'ASC')->get();

		addIpLog(' View p2p MarketPlace', $email);
        return view('p2p.p2p_marketplace', compact('user','pageTitle', 'providerlist', 'cryptolist', 'currency'));
    }
	
	public function p2pmerchant(Request $request)
    {
        $pageTitle = "P2P Merchant";
		$cryptolist = Crypto::with('latestHistory')->where('status', 1)->orderBy('id', 'ASC')->get();
		$currency = DB::table('currencies')->where('status', 1)->orderBy('id', 'ASC')->get();
		/*Logged User Country Currency*/
		$country = auth()->user()->country;
		
        return view('p2p.p2p_merchant', compact('pageTitle', 'cryptolist', 'currency'));
    }
	
	public function p2pmerchantstore(Request $request){
		$email = auth()->user()->email;
		$request->validate([
            'merchantcompany' => 'required',
            'wanttype'      => 'required',
            'cryptoval'     => 'required',
            'currency_code'   => 'required',
            'pricetype'     => 'required',
            'quoteprice'    => 'nullable|numeric',
            'total_amount'  => 'required|numeric',
            'min_limit'     => 'required|numeric',
            'max_limit'     => 'required|numeric',
            'time_limit'    => 'required|numeric',
            'transferstatus'=> 'required'
        ]);
$datalog = [
    'merchantcompany'  => $request->merchantcompany,
    'merchantid'       => $this->generateMerchantCode(),
    'email'            => $email,
    'wanttype'         => $request->wanttype,
    'cryptoval'        => $request->cryptoval,
    'currency_code'    => $request->currency_code,
    'pricetype'        => $request->pricetype,
    'cryptoquoteprice' => $request->cryptoquoteprice,
    'quoteprice'       => $request->quoteprice,
    'total_amount'     => $request->total_amount,
    'min_limit'        => $request->min_limit,
    'max_limit'        => $request->max_limit,
    'time_limit'       => $request->time_limit,
    'payment_method'   => $request->payment_method ? json_encode($request->payment_method) : null,
    'tags'             => $request->tags ? json_encode($request->tags) : null,
    'remarks'          => $request->remarks,
    'autoreply'        => $request->autoreply,
    'transferstatus'   => $request->transferstatus,
    'status'           => 1,
];


        $merchant = new P2PMerchant();
        $merchant->merchantcompany     = $request->merchantcompany;
        $merchant->merchantid     = $this->generateMerchantCode();
        $merchant->email      	  = $email;
        $merchant->wanttype       = $request->wanttype;
        $merchant->cryptoval      = $request->cryptoval;
        $merchant->currency_code  = $request->currency_code;
        $merchant->pricetype      = $request->pricetype;
        $merchant->cryptoquoteprice     = $request->cryptoquoteprice;
        $merchant->quoteprice     = $request->quoteprice;
        $merchant->total_amount   = $request->total_amount;
        $merchant->min_limit      = $request->min_limit;
        $merchant->max_limit      = $request->max_limit;
        $merchant->time_limit     = $request->time_limit;
		$merchant->payment_method = $request->payment_method ? json_encode($request->payment_method) : null;
        $merchant->tags           = $request->tags ? json_encode($request->tags) : null;
        $merchant->remarks        = $request->remarks;
        $merchant->autoreply      = $request->autoreply;
        $merchant->transferstatus = $request->transferstatus;
        $merchant->status = 1;
		
        $merchant->save();

		addIpLog(' Create p2p MarketPlace', $datalog);
        return redirect()->route('p2pmyadslist')->with('success', 'Merchant post created successfully. Our admin team verify your post approve shortly!');
	}
	
	public function p2pmyadslist()
	{
		 $user = auth()->user();
		$email = auth()->user()->email;
		$list = P2PMerchant::where('email', $email)->get();
		addIpLog(' p2p Listing Page', $email);
		return view('p2p.p2p_myads', compact('user','list'));
	}
	
	public function p2pmerchantedit($id)
	{
		$data = P2PMerchant::findOrFail($id);
		$cryptolist = Crypto::where('status', 1)->orderBy('id', 'ASC')->get();
		$currency = DB::table('currencies')->where('status', 1)->get();	
		$pageTitle = "P2P Merchant Edit";		
		return view('p2p.p2p_merchant', compact('data', 'cryptolist', 'currency', 'pageTitle'));
	}
	
	public function p2pmerchantupdate(Request $request, $id)
	{
		$request->validate([
			'wanttype'      => 'required',
			'cryptoval'     => 'required',
			'currencyval'   => 'required',
			'pricetype'     => 'required',
			'total_amount'  => 'required|numeric',
			'min_limit'     => 'required|numeric',
			'max_limit'     => 'required|numeric',
			'time_limit'    => 'required|numeric',
			'transferstatus'=> 'required'
		]);

		$merchant = P2PMerchant::findOrFail($id);

		$datalog = [
			'wanttype'      => $request->wanttype,
			'cryptoval'     => $request->cryptoval,
			'currencyval'   => $request->currencyval,
			'pricetype'     => $request->pricetype,
			'cryptoquoteprice'    => $request->cryptoquoteprice,
			'quoteprice'    => $request->quoteprice,
			'total_amount'  => $request->total_amount,
			'min_limit'     => $request->min_limit,
			'max_limit'     => $request->max_limit,
			'time_limit'    => $request->time_limit,
			'payment_method'=> $request->payment_method ? json_encode($request->payment_method) : null,
			'tags'          => $request->tags ? json_encode($request->tags) : null,
			'remarks'       => $request->remarks,
			'autoreply'     => $request->autoreply,
			'transferstatus'=> $request->transferstatus,
			'status'		=> $request->status,
		];

		$merchant->update([
			'wanttype'      => $request->wanttype,
			'cryptoval'     => $request->cryptoval,
			'currencyval'   => $request->currencyval,
			'pricetype'     => $request->pricetype,
			'cryptoquoteprice'    => $request->cryptoquoteprice,
			'quoteprice'    => $request->quoteprice,
			'total_amount'  => $request->total_amount,
			'min_limit'     => $request->min_limit,
			'max_limit'     => $request->max_limit,
			'time_limit'    => $request->time_limit,
			'payment_method'=> $request->payment_method ? json_encode($request->payment_method) : null,
			'tags'          => $request->tags ? json_encode($request->tags) : null,
			'remarks'       => $request->remarks,
			'autoreply'     => $request->autoreply,
			'transferstatus'=> $request->transferstatus,
			'status'		=> $request->status,
		]);
addIpLog(' p2p Merchant Update', $datalog);
		return redirect()->route('p2pmyadslist')->with('success', 'Updated successfully!');
	}
	
	public function p2pmerchantdelete($id)
	{
		$merchant = P2PMerchant::findOrFail($id);
		$merchant->update([
			'status' => 3
		]);
		return redirect()->route('p2pmyadslist')->with('success', 'Deleted successfully!');
	}
	
	public function p2pbuy($marketid){
		$pageTitle = "Place Your Order";
		$user = auth()->user();		
		$email = $user->email;
		$walletBalance = $this->getWalletBalance($email);
		$wallet_accounts = ClientWallets::where('user_id', $email)->get();
        $bank_accounts = ClientBankDetails::where('userId', $email)->get();
		$merchant = P2PMerchant::whereRaw('MD5(id) = ?', [$marketid])->first();
		addIpLog(' p2p User Buy', $email);
		return view('p2p.p2p_orderplace', compact('pageTitle', 'merchant', 'walletBalance', 'wallet_accounts', 'bank_accounts'));
	}
	
	public function p2porderstore(Request $request){
		$user = auth()->user();		
		$email = $user->email;
		
		$request->validate([
            'orderamount'          => 'required|numeric|min:1',
            'orderconvertcrypto'   => 'required|numeric|min:0.0001',
            'orderpaymentmethod'   => 'required|in:cryptotransfer,banktransfer',
            'orderpaymentproof'    => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);
		
		$merchant = P2PMerchant::findOrFail($request->merchantaccid);
        
        if($request->orderamount < $merchant->min_limit || $request->orderamount > $merchant->max_limit){
            return back()->withErrors([
                'orderamount' => 'Order amount outside allowed limits.'
            ]);
        }
		
		DB::beginTransaction();

		try {
			
			$proofPath = $request->file('orderpaymentproof')->store('p2porderpaymentproofs', 'public');
									
			$orders = new P2POrders();
			$orders->orderId     	= $this->generateOrderNo();
			$orders->userid     	= $user->id;
			$orders->email      	= $email;
			$orders->merchantaccid  = $request->merchantaccid;
			$orders->orderprice  = $request->orderprice;
			$orders->orderusdval  = $request->orderusdval;
			$orders->orderpayamount  = $request->orderamount;
			$orders->orderpaycurrency  = $request->orderpaycurrency;
			$orders->orderreceiveamount  = $request->orderconvertcrypto;
			$orders->orderreceivecurrency  = $request->orderreceivecurrency;
			$orders->orderpaymentmethod  = $request->orderpaymentmethod;
			$orders->orderpaymentproof  = $proofPath;
			$orders->status = 'pending';
			$orders->save();
			
			if(isset($orders)){
				$ordershistory = new P2POrdershistory();
				$ordershistory->orderid      = $orders->id;
				$ordershistory->orderremarks   = 'Orders have been initiated and are pending approval.';
				$ordershistory->status = 'pending';
				$ordershistory->created_by = $email;
				$ordershistory->save();
				
				$data = [
					"payment_amount" => $request->orderusdval,
					"payment_type" => "Wallet to P2P",
					"payment_reference_id" => 'P2P',
					"payment_status" => "Initiated",
					"initiated_by" => $email
				];
				$paymentLog = PaymentLog::create($data);
			    addIpLog(' p2p Merchant Payment Buy', $data);
				$data_wallet = [
					"email" => $email,
					"withdraw_amount" => $request->orderusdval,
					"withdraw_type" => 'P2P',
					"Status" => 0
				];
				$paymentWallet = WalletWithdraw::create($data_wallet);
			}
		
			DB::commit();
			addIpLog(' p2p Merchant Buy', $data);
			return redirect()->route('p2pmyorders')->with('success', 'P2P Your Order created Successfully. Kindly wait for admin approval!');
			
		} catch (\Exception $e) {

			DB::rollBack();
			Log::error('P2P Order Failed', ['error' => $e->getMessage()]);

			return back()
				->withInput()
				->with('error', 'Order failed. Please try again.');
		}
	}
	
	public function p2pmyorders(Request $request)
    {
		$user = auth()->user();		
		$email = $user->email;
		
        $pageTitle = "P2P My Orders";	 
		$orderlist = DB::table('p2p_orders')
			->join('p2p_merchantacc', 'p2p_orders.merchantaccid', '=', 'p2p_merchantacc.id')
			->where('p2p_orders.email', $email)
			->select(
				'p2p_orders.*',
				'p2p_merchantacc.id as adsid', 'p2p_merchantacc.merchantcompany', 'p2p_merchantacc.wanttype',
			)
			->orderBy('p2p_orders.id', 'desc')
			->get();
		addIpLog('p2p apply order', $email);
        return view('p2p.p2p_myorders', compact('pageTitle', 'orderlist','user'));
    }
	
	public function p2preceiveorders(Request $request)
    {
		$user = auth()->user();		
		$email = $user->email;
		
        $pageTitle = "P2P Receive Orders";	 
		$orderlist = DB::table('p2p_orders')
			->join('p2p_merchantacc', 'p2p_orders.merchantaccid', '=', 'p2p_merchantacc.id')
			->where('p2p_merchantacc.email', $email)
			->select(
				'p2p_orders.*',
				'p2p_merchantacc.id as adsid', 'p2p_merchantacc.wanttype',
			)
			->orderBy('p2p_orders.id', 'desc')
			->get();
		addIpLog('p2p receive order', $email);
        return view('p2p.p2p_orders', compact('pageTitle', 'orderlist'));
    }
	
}
