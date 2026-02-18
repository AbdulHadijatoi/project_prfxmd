<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TournamentLiveAccount;
use Illuminate\Http\Request;
use App\Models\LiveAccount;
use App\Models\DemoAccount;
use App\Models\AccountType;
use App\Models\Leverage;
use App\Models\User;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\MT5\MTProtocolConsts;
use App\Services\MT5Service;
use Illuminate\Support\Facades\DB;
use App\Services\MailService as MailService;
use App\Models\DemoDeposit;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class MT5Accounts extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MT5Service $mt5Service, MailService $mailService)
    {
        $this->mailService = $mailService;
        $this->mt5Service = $mt5Service;
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
    }
    public function liveAccounts()
    {
        $user = auth()->user();
        $email = auth()->user()->email;
        $results = LiveAccount::with('accountType')
            ->where('email', $email)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();
             addIpLog('Live Accounts view', $email);
        return view('live_accounts', compact('results','user'));
    }
    public function demoAccounts()
    {
         $user = auth()->user();
        $email = $email = auth()->user()->email;
        $results = DemoAccount::with('accountType')->where('email', $email)
			->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();
            addIpLog('Demo Accounts view', $email);
        return view('demo_accounts', compact('user','results'));
    }
    public function viewAccountDetails()
    {
        session()->remove('error');
        $email = $email = auth()->user()->email;
        $trade_id = $_GET['id'];
        $type = $_GET['type'];
        $settings = settings();
        // dd($settings['mt5_server_ip'],
        // $settings['mt5_server_port'],
        // 300,
        // $settings['mt5_server_web_login'],
        // $settings['mt5_server_web_password']);
        $results = [];
        // $this->api->SetLoggerWriteDebug(config('constants.IS_WRITE_DEBUG_LOG'));
        // $this->api->Connect(
        //     $settings['mt5_server_ip'],
        //     $settings['mt5_server_port'],
        //     300,
        //     $settings['mt5_server_web_login'],
        //     $settings['mt5_server_web_password']
        // );
        $getUser = [];
        $equity = '';
        $margin = '';
        $marginlevel = '';
        $accountSwap = '';
        $freemargin = '';
        $profit = '';
        try {
            $login = $trade_id;
            if (($error_code = $this->api->UserAccountGet($login, $accounts)) != MTRetCode::MT_RET_OK) {
                session()->flash('error', value: 'MT5 ' . $login . ': ' . MTRetCode::GetError($error_code));
            }
            if (($error_code = $this->api->UserGet($login, $account)) != MTRetCode::MT_RET_OK) {
                session()->flash('error', value: 'MT5 ' . $login . ': ' . MTRetCode::GetError($error_code));
            }
            // Fetch positions
            if (($error_code = $this->api->PositionGetTotal($login, $total)) != MTRetCode::MT_RET_OK) {
                session()->flash('error', 'MT5 ' . $login . ': ' . MTRetCode::GetError($error_code));
            }
            $open_order_history = $total;
            $offset = 0;
            $positions = [];
            // Fetch position pages
            if (($error_code = $this->api->PositionGetPage($login, $offset, $total, $positions)) != MTRetCode::MT_RET_OK) {
                session()->flash('error', 'MT5 ' . $login . ': ' . MTRetCode::GetError($error_code));
            }

            // Fetch user account details
            if ($account) {

                // account login get
                $account->Login;
                // balance get
                $balance = $account->Balance;
                // balance get
                $balance = $account->Balance;
                // Credit get
                $credit = $account->Credit;
                // profit get
                $profit = $accounts->Floating;
                // Free Margin get
                $freemargin = $accounts->MarginFree;
                // credit get
                $credit = $account->Credit;
                // equity --  $balance + $Credit+$Profit
                $equity = ($balance + $credit + $profit);
                // margin level get
                // $margin = $account->Margin;
                $marginlevel = round((($balance - $freemargin) / (1000)), 2);
                // Update live account with new data
                $liveAccount = LiveAccount::where('trade_id', $trade_id)->first();
                if ($liveAccount) {
                    $liveAccount->update([
                        'balance' => $account->Balance,
                        'credit' => $account->Credit,
                        'MarginFree' => $accounts->MarginFree,
                        'MarginLevel' => $accounts->MarginLevel,
                        'equity' => $accounts->Equity
                    ]);
                }
            }
            // Fetch order history
            $from = 'March 01,2016';
            $to = 'March 31,2080';
            if (($error_code = $this->api->HistoryGetTotal($login, $from, $to, $total)) != MTRetCode::MT_RET_OK) {
                session()->flash('error', 'MT5 ' . $login . ': ' . MTRetCode::GetError($error_code));
            }
            $closed_order_history = $total;
            // Fetch order pages
            if (($error_code = $this->api->HistoryGetPage($login, $from, $to, $offset, $total, $orders)) != MTRetCode::MT_RET_OK) {
                session()->flash('error', 'MT5 ' . MTRetCode::GetError($error_code));
            }
            if ($type == "demo") {
                $getUser = DemoAccount::with('accountType')
                    ->where('trade_id', $trade_id)
                    ->first();
            } else if ($type == "live") {
                $getUser = LiveAccount::with('accountType')
                    ->where('trade_id', $trade_id)
                    ->first();
            } else {
                $getUser = TournamentLiveAccount::with('accountType')
                    ->where('trade_id', $trade_id)
                    ->first();
            }
            $accountSwap = $getUser->accountType ? $getUser->accountType->ac_swap : null;
            // Process orders
            if (!empty($orders)) {
                foreach ($orders as $item) {
                    $volume = $item->VolumeInitial * 0.00001;
                    $time_closed = gmdate("Y-m-d H:i:s", $item->TimeDone);
                    // Insert commission data into DB
                    DB::table('ib1_commission')->insert([
                        'user_id' => session('clogin'),
                        'order_id' => $item->Order,
                        'login' => $item->Login,
                        'volume' => $volume,
                        'time_closed' => $time_closed
                    ]);
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', '');
        }
        return view('view-account-details', compact('results', 'trade_id', 'type', 'settings', 'account', 'getUser', 'equity', 'margin', 'marginlevel', 'accountSwap', 'freemargin', 'profit'));

    }
    public function showLiveAccountForm()
    {
        $email = $email = auth()->user()->email;
        $user = User::where('email', $email)->first();
        $acc_count = LiveAccount::where('email', $email)->where('status', 'active')->count();
        // $results = AccountType::whereHas('mt5Group', function ($query) {
        //     $query->where('mt5_group_type', 'live')
        //         ->orWhere('mt5_group_type', 'real');
        // })->where('is_client_group', 1)
        //     ->orderBy('display_priority', 'DESC')
        //     ->with('mt5Group:mt5_group_id,mt5_group_type')
        //     ->get();

$results = DB::table('account_types')
    ->join('mt5_groups', 'mt5_groups.mt5_group_id', '=', 'account_types.ac_type')
    ->where(function ($query) {
        $query->where('mt5_groups.mt5_group_type', 'live')
              ->orWhere('mt5_groups.mt5_group_type', 'real');
    })
    ->where('account_types.status', 1)
    ->where('mt5_groups.is_active', 1)
    ->where('account_types.is_client_group', 1)
    ->where('account_types.user_group_id', $user->group_id)
    ->select(
        'account_types.*',
        'mt5_groups.mt5_group_type',
        DB::raw("CONCAT(UPPER(LEFT(account_types.ac_swap,1)), LOWER(SUBSTRING(account_types.ac_swap,2))) as ac_swap")
    )
    ->orderBy('account_types.display_priority', 'asc')
    ->get();
        // $results = DB::table('account_types')
        //     ->join('mt5_groups', 'mt5_groups.mt5_group_id', '=', 'account_types.ac_type')
        //     ->where(function ($query) {
        //         $query->where('mt5_groups.mt5_group_type', 'live')
        //             ->orWhere('mt5_groups.mt5_group_type', 'real');
        //     })
        //     ->where('account_types.status', 1)
        //     ->where('mt5_groups.is_active', 1)
        //     ->where('account_types.is_client_group', 1)
        //     ->where('account_types.user_group_id', $user->group_id)
        //     // ->select('account_types.*', 'mt5_groups.mt5_group_type')
		// 	->orderBy('display_priority', 'asc')
        //     ->get();

            //   echo'<pre>';print_r($results);exit;
            addIpLog('Live Accounts Form ', $email);
        return view('create-live-account', compact('user', 'results', 'acc_count'));
    }
    public function showDemoAccountForm()
    {
        $email = $email = auth()->user()->email;
        $user = User::where('email', $email)->first();

        $results = AccountType::with('mt5Group')
            ->whereHas('mt5Group', function ($query) {
                $query->where('mt5_group_type', 'demo')
                    ->where('is_active', 1);
            })
            ->where('status', 1)
            ->where('is_client_group', 1)
            ->where('user_group_id', $user->group_id)
            ->orderBy('display_priority', 'asc')
            ->get();
            addIpLog('Demo Accounts Form ', $email);
        return view('create-demo-account', compact('user', 'results'));
    }
    public function getLeverage(Request $request)
    {
        $accountTypeId = $request->query('id');
        $leverage = Leverage::where('account_type_id', $accountTypeId)->get();
        return response()->json($leverage);
    }
    // public function createLiveAccount(Request $request)
    // {
    //     $settings = settings();
    //     $validatedData = $request->validate([
    //         'options' => 'required|string',
    //         'leverage' => 'required|string',
    //     ]);
    //     $user = User::where('email', session('clogin'))->firstOrFail();
    //     $group = AccountType::where('ac_index', $validatedData['options'])->firstOrFail();
		
	// 	$new_user = $this->api->UserCreate();
    //     $new_user->MainPassword = $this->generatePassword();
    //     $new_user->Group = $group->ac_group;
    //     $new_user->Leverage = $validatedData['leverage'];
    //     $new_user->ZipCode = $user->zipcode;
    //     $new_user->Country = $user->country;
    //     $new_user->State = $user->state;
    //     $new_user->City = $user->city;
    //     $new_user->Address = $user->address;
    //     $new_user->Phone = $user->number;
    //     $new_user->Currency = 'USD';
    //     $new_user->Status = 1;
    //     $new_user->Company = $settings['mt5_company_name'];
    //     $new_user->Name = $user->fullname;
    //     $new_user->Email = session('clogin');
    //     $new_user->LeadSource = ($user->ib1 == 'noIB') ? "" : $user->ib1;
    //     $new_user->PhonePassword = $this->generatePassword();
    //     $new_user->InvestPassword = $this->generatePassword();
    //     $new_user->Login = $this->generateRandomNumber();
    //     $response = $this->CreateAccount($new_user, $user_server, 'Live');
    //     if ($response['status']) {
	// 		$promoid = 0;
	// 		if(!empty($request->input('promoid'))){
	// 			$promoid = $request->input('promoid');
	// 		}
    //         LiveAccount::create([
    //             'email' => $new_user->Email,
    //             'name' => $new_user->Name,
    //             'trade_id' => $new_user->Login,
    //             'account_type' => $validatedData['options'],
    //             'leverage' => $new_user->Leverage,
    //             'currency' => $new_user->Currency,
    //             'trader_pwd' => $new_user->MainPassword,
    //             'invester_pwd' => $new_user->InvestPassword,
    //             'phone_pwd' => $new_user->PhonePassword,
    //             'ib1' => $new_user->LeadSource,
    //             'ib2' => $user->ib2,
    //             'ib3' => $user->ib3,
    //             'ib4' => $user->ib4,
    //             'ib5' => $user->ib5,
    //             'ib6' => $user->ib6,
    //             'ib7' => $user->ib7,
    //             'ib8' => $user->ib8,
    //             'ib9' => $user->ib9,
    //             'ib10' => $user->ib10,
    //             'ib11' => $user->ib11,
    //             'ib12' => $user->ib12,
    //             'ib13' => $user->ib13,
    //             'ib14' => $user->ib14,
    //             'ib15' => $user->ib15,
	// 			'promoid' => $promoid
    //         ]);
    //         $this->sendMail($new_user, 'Live');
    //         return redirect()->back()->with('success', $response['message']);
    //     } else {
    //         return redirect()->back()->with('error', $response['message']);
    //     }
    // }
	public function createLiveAccount(Request $request)
	{
		$settings = settings();

		$validatedData = $request->validate([
			'options' => 'required|string',
			'leverage' => 'required|string',
		]);

		$user = User::where('email', session('clogin'))->firstOrFail();

		// Count how many live accounts the user already has
		$createdAccounts = LiveAccount::where('email', $user->email)->where('status', 'active')->count();

		// Check if limit reached
		if ($createdAccounts >= $user->acc_limit){
			return redirect()->back()->withErrors([
				'limit' => 'You have reached the maximum number of accounts allowed.'
			]);
		}

		// Get account type
		$group = AccountType::where('ac_index', $validatedData['options'])->firstOrFail();

		// Create MT5 user object
		$new_user = $this->api->UserCreate();
		$new_user->MainPassword = $this->generatePassword();
		$new_user->Group = $group->ac_group;
		$new_user->Leverage = $validatedData['leverage'];
		$new_user->ZipCode = $user->zipcode;
		$new_user->Country = $user->country;
		$new_user->State = $user->state;
		$new_user->City = $user->city;
		$new_user->Address = $user->address;
		$new_user->Phone = $user->number;
		$new_user->Currency = 'USD';
		$new_user->Status = 1;
		$new_user->Company = $settings['mt5_company_name'];
		$new_user->Name = $user->fullname;
		$new_user->Email = session('clogin');
		$new_user->LeadSource = ($user->ib1 == 'noIB') ? "" : $user->ib1;
		$new_user->PhonePassword = $this->generatePassword();
		$new_user->InvestPassword = $this->generatePassword();
		$new_user->Login = $this->generateRandomNumber();

		// Create account via API
		$response = $this->CreateAccount($new_user, $user_server, 'Live');

		if ($response['status']) {
			$promoid = $request->input('promoid', 0);

            $datalog =[
                'email' => $new_user->Email,
				'name' => $new_user->Name,
				'trade_id' => $new_user->Login,
				'account_type' => $validatedData['options'],
				'leverage' => $new_user->Leverage,
				'currency' => $new_user->Currency,
				'trader_pwd' => $new_user->MainPassword,
				'invester_pwd' => $new_user->InvestPassword,
				'phone_pwd' => $new_user->PhonePassword,
				'ib1' => $new_user->LeadSource,
				'ib2' => $user->ib2,
				'ib3' => $user->ib3,
				'ib4' => $user->ib4,
				'ib5' => $user->ib5,
				'ib6' => $user->ib6,
				'ib7' => $user->ib7,
				'ib8' => $user->ib8,
				'ib9' => $user->ib9,
				'ib10' => $user->ib10,
				'ib11' => $user->ib11,
				'ib12' => $user->ib12,
				'ib13' => $user->ib13,
				'ib14' => $user->ib14,
				'ib15' => $user->ib15,
				'promoid' => $promoid
            ];
			// Save to DB
			LiveAccount::create([
				'email' => $new_user->Email,
				'name' => $new_user->Name,
				'trade_id' => $new_user->Login,
				'account_type' => $validatedData['options'],
				'leverage' => $new_user->Leverage,
				'currency' => $new_user->Currency,
				'trader_pwd' => $new_user->MainPassword,
				'invester_pwd' => $new_user->InvestPassword,
				'phone_pwd' => $new_user->PhonePassword,
				'ib1' => $new_user->LeadSource,
				'ib2' => $user->ib2,
				'ib3' => $user->ib3,
				'ib4' => $user->ib4,
				'ib5' => $user->ib5,
				'ib6' => $user->ib6,
				'ib7' => $user->ib7,
				'ib8' => $user->ib8,
				'ib9' => $user->ib9,
				'ib10' => $user->ib10,
				'ib11' => $user->ib11,
				'ib12' => $user->ib12,
				'ib13' => $user->ib13,
				'ib14' => $user->ib14,
				'ib15' => $user->ib15,
				'promoid' => $promoid
			]);
addIpLog('Create Live Account ', $datalog);
			// Send email
			$this->sendMail($new_user, 'Live');

			return redirect()->back()->with('success', $response['message']);
		} else {
			return redirect()->back()->with('error', $response['message']);
		}
	}

	public function updateLiveAccount(Request $request)
    {
        $settings = settings();
		$trade_id = $request->input('trade_id');
		$promoid = $request->input('promoid');
		
		LiveAccount::where('trade_id', $trade_id)->update([
			'promoid' => $promoid
		]);
        addIpLog('Update Live Account ', $promoid);
		return redirect()->back()->with('success', 'Promo code updated, the choosen live account successfully!');
		
	}
	
    public function createDemoAccount(Request $request)
    {
        $settings = settings();
        $validatedData = $request->validate([
            'options' => 'required|string',
            'leverage' => 'required|string',
            'demo_deposit' => 'required'
        ]);
        $user = User::where('email', session('clogin'))->firstOrFail();
        $group = AccountType::where('ac_index', $validatedData['options'])->firstOrFail();
        $new_user = $this->api->UserCreate();
        $new_user->MainPassword = $this->generatePassword();
        $new_user->Group = $group->ac_group;
        $new_user->Leverage = $validatedData['leverage'];
        $new_user->ZipCode = $user->zipcode;
        $new_user->Country = $user->country;
        $new_user->State = $user->state;
        $new_user->City = $user->city;
        $new_user->Address = $user->address;
        $new_user->Phone = $user->number;
        $new_user->Currency = 'USD';
        $new_user->Company = $settings['mt5_company_name'];
        $new_user->Name = $user->fullname;
        $new_user->Email = session('clogin');
        $new_user->LeadSource = ($user->ib1 == 'noIB') ? "" : $user->ib1;
        $new_user->PhonePassword = $this->generatePassword();
        $new_user->InvestPassword = $this->generatePassword();
        $new_user->Login = $this->generateRandomNumber();
        $response = $this->CreateAccount($new_user, $user_server, 'Demo');
        
        $datalog = ['email' => $new_user->Email,
                'trade_id' => $new_user->Login,
                'account_type' => $group->ac_group,
                'leverage' => $new_user->Leverage,
                'currency' => $new_user->Currency,
                'trader_pwd' => $new_user->MainPassword,
                'invester_pwd' => $new_user->InvestPassword,
                'phone_pwd' => $new_user->PhonePassword,
                'balance' => $validatedData['demo_deposit']];
        
        if ($response['status']) {
            DemoAccount::create([
                'email' => $new_user->Email,
                'trade_id' => $new_user->Login,
                'account_type' => $group->ac_group,
                'leverage' => $new_user->Leverage,
                'currency' => $new_user->Currency,
                'trader_pwd' => $new_user->MainPassword,
                'invester_pwd' => $new_user->InvestPassword,
                'phone_pwd' => $new_user->PhonePassword,
                'balance' => $validatedData['demo_deposit']
            ]);
            $ticket = NULL;
            $errorCode = $this->api->TradeBalance($new_user->Login, $type = MTEnDealAction::DEAL_BALANCE, $validatedData['demo_deposit'], 'Deposit', $ticket, $margin_check = true);
            if ($errorCode != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($errorCode);
                return redirect()->back()->with('error', $error);
            } else {
                $data = [
                    'email' => $new_user->Email,
                    'trade_id' => $new_user->Login,
                    'deposit_amount' => $validatedData['demo_deposit'],
                    'Status' => 1
                ];
                DemoDeposit::create($data);
            }
            addIpLog('Create Demo Account ',  $datalog);
            $this->sendMail($new_user, 'Demo');
            return redirect()->back()->with('success', $response['message']);
        } else {
            return redirect()->back()->with('error', $response['message']);
        }
    }
    public function sendMail($new_user, $type)
    {
        $settings = settings();
        $toEmail = $new_user->Email;
        $from = $settings['email_from_address'];
        $emailSubject = $settings['admin_title'] . ' - ' . $type . ' Account Details';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";

        $datalog =[
            'name' => $new_user->Name,
            'type' => $type,
            'trade_id' => $new_user->Login,
            'trader_pwd' => $new_user->MainPassword,
            'investor_pwd' => $new_user->InvestPassword,
            'leverage' => "1:" . $new_user->Leverage,
            'server_name' => $settings['mt5_company_name'],
            'email' => $settings['email_from_address'],
            "title_right" => "Get Started With",
            "subtitle_right" => "New " . $type . " MT5 Account"
        ];
        $templateVars = [
            'name' => $new_user->Name,
            'type' => $type,
            'trade_id' => $new_user->Login,
            'trader_pwd' => $new_user->MainPassword,
            'investor_pwd' => $new_user->InvestPassword,
            'leverage' => "1:" . $new_user->Leverage,
            'server_name' => $settings['mt5_company_name'],
            'email' => $settings['email_from_address'],
            "title_right" => "Get Started With",
            "subtitle_right" => "New " . $type . " MT5 Account"
        ];
         addIpLog('Email MT5 Send Account ',  $datalog);
        $this->mailService->sendEmail($toEmail, $emailSubject, $headers, '', $templateVars);

    }
    public function generatePassword($length = 9)
    {
        // Define character pools
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!';
        // Ensure at least one character from each pool is included
        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $specialChars[rand(0, strlen($specialChars) - 1)];
        // Combine all pools for the remaining characters
        $allCharacters = $uppercase . $lowercase . $numbers . $specialChars;
        // Generate the remaining characters
        for ($i = 4; $i < $length; $i++) {
            $password .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
        }
        // Shuffle the password to avoid predictable patterns
        $password = str_shuffle($password);
        return $password;
    }
    function generateRandomNumber($length = 6)
    {
        $min = pow(10, $length - 1); // Minimum value for an 8-digit number (10000000)
        $max = pow(10, $length) - 1;  // Maximum value for an 8-digit number (99999999)
        return rand($min, $max);
    }
    function CreateAccount($user, &$user_server, $type)
    {
        $settings = settings();
        if (!$this->api->IsConnected()) {
            $errorCode = $this->api->Connect(
                $settings['mt5_server_ip'],
                $settings['mt5_server_port'],
                300,
                $settings['mt5_server_web_login'],
                $settings['mt5_server_web_password']
            );
            if ($errorCode != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($errorCode);
                return ["status" => false, "message" => $error];
            }
        }
        if (($error_code = $this->api->UserAdd($user, $user_server)) != MTRetCode::MT_RET_OK) {
            $error = MTRetCode::GetError($error_code);
            return ["status" => false, "message" => $error];
        } else {
            return ["status" => true, "message" => $type . " Account Created Successfully"];
        }
    }
    public function changeMt5Password(Request $request)
    {
        $request->validate([
            'trade_id' => 'required',
            'password_type' => 'required|in:main,investor',
            'password' => 'required|min:6',
        ]);
        $login = $request->input('trade_id');
        $pass_type = $request->input('password_type');
        $new_password = $request->input('password');
        $type = $request->input('type', 'live');
          
        if ($pass_type == 'main') {
            $error_code = $this->api->UserPasswordChange($login, $new_password, MTProtocolConsts::WEB_VAL_USER_PASS_MAIN);
        } else {
            $error_code = $this->api->UserPasswordChange($login, $new_password, MTProtocolConsts::WEB_VAL_USER_PASS_INVESTOR);
        }

        // Check if the password change was successful
        if ($error_code != MTRetCode::MT_RET_OK) {
            return redirect()->back()->with('error', 'MT5: ' . MTRetCode::GetError($error_code));
        }

        // Update the password in the database
        $model = $type === 'demo' ? new DemoAccount() : new LiveAccount();
        if ($pass_type == 'main') {
            $model->where('trade_id', $login)->update(['trader_pwd' => $new_password]);
        } else {
            $model->where('trade_id', $login)->update(['invester_pwd' => $new_password]);
        }
 $datalog =[
     $login = $request->input('trade_id'),
        $pass_type = $request->input('password_type'),
        $new_password = $request->input('password'),
        $type = $request->input('type', 'live'),
 ];
     

         addIpLog('changeMt5Password ',  $datalog);
        // Display success message
        $message = $pass_type == 'main' ? 'Your Master Password Successfully Updated' : 'Your Investor Password Successfully Updated';
        return redirect()->back()->with('success', $message);
    }
	
	public function generateToken(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string'
        ]);

        $apiBase = rtrim(config('services.mt5.base'), '/');
        $path    = config('services.mt5.token_path');

        try {
            $response = Http::withHeaders($this->brokerHeaders())
                ->post("{$apiBase}{$path}", [
                    'login'    => $request->login,
                    'password' => $request->password,
                ]);

            if ($response->failed()) {
                // Log minimal info but NOT passwords
                Log::warning('MT5 token request failed', [
                    'user_id' => auth()->id(),
                    'login'   => $request->login,
                    'status'  => $response->status(),
                ]);

                return response()->json(['error' => 'MT5 login failed'], 401);
            }

            $datalog = [
                 'user_id' => auth()->id(),
                    'login'   => $request->login,
                    'status'  => $response->status(),
            ];
            $body = $response->json();

            if (empty($body['token'])) {
                return response()->json(['error' => 'Invalid response from MT5 API'], 502);
            }

            // Optionally compute expires_at
            $expiresIn = isset($body['expires_in']) ? intval($body['expires_in']) : 60;
            $buffer = intval(config('services.mt5.exp_buffer', 5));
            $expiresAt = Carbon::now()->addSeconds(max(5, $expiresIn - $buffer));
 addIpLog('Mt5generateToken ',  $datalog);
            return response()->json([
                'token'      => $body['token'],
                'expires_in' => $expiresIn,
                'expires_at' => $expiresAt->toIso8601String(),
                // optionally include terminal url base so frontend doesn't have to hardcode
                'terminal_url' => rtrim(config('services.mt5.terminal_url'), '/'),
            ]);

        } catch (\Exception $e) {
            Log::error('MT5 token request exception', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'MT5 service error'], 500);
        }
    }

    protected function brokerHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];
        if ($key = config('services.mt5.api_key')) {
            $headers['Authorization'] = 'Bearer ' . $key;
        }
        return $headers;
    }
	
	public function tradeonline(Request $request){
		$login = $request->query('login');
		$password = $request->query('password');
		return view('tradeonline', compact('login', 'password'));
	}
}
