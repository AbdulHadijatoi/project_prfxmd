<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClientWallets;
use App\Models\ClientBankDetails;
use App\Models\BonusModel;
use App\Models\KycUpdate;
use App\Models\Promotation;
use App\Models\WalletDeposit;
use App\Models\WalletWithdraw;
use App\Models\UserGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\PusherService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Services\MailService as MailService;
use App\Models\LiveAccount;
use App\Models\AccountType;
use App\Models\Leverage;
use App\Models\TradeDeposits;
use App\Models\BonusTransaction;
use App\Models\PaymentLog;
use App\Services\MT5Service;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;

class Users extends Controller
{
    protected $pusherService;
	protected $api;
    protected $mailService;
    protected $mt5Service;

    public function __construct(PusherService $pusherService, MailService $mailService, MT5Service $mt5Service, MTWebAPI $api)
    {
        $this->pusherService = $pusherService;
        $this->mailService = $mailService;
        $this->settings = settings();
		
		$this->mt5Service = $mt5Service;
		$this->mt5Service->connect();
		$this->api = $this->mt5Service->getApi();
    }
    public function profile()
    {
        session()->forget('Bank_Details_Otp');
        session()->forget('Wallet_Creation_Otp');
        session()->forget('Bank_Delete_Otp');
        session()->forget('Wallet_Update_Otp');
        $email = auth()->user()->email;
        $wallet_accounts = ClientWallets::where('user_id', $email)->get();
        $bank_accounts = ClientBankDetails::where('userId', $email)->get();
        $user = User::where('email', $email)->first();

          $idProof = KycUpdate::where('email', $email)
            ->where('kyc_type', 'ID Proof')
            ->orderBy('id', 'desc')
            ->first();

            $addressProof = KycUpdate::where('email', $email)
                ->where('kyc_type', 'Address Proof')
                ->orderBy('id', 'desc')
                ->first();
        $verf_docs = KycUpdate::where('email', $email)->orderBy('id', 'desc')->get();

        $customerMfaEnabled = isset($user->mfa_enable) && (int) $user->mfa_enable === 1;
        $customerHasMfaSecret = isset($user->mfa_secret) && $user->mfa_secret !== '' && $user->mfa_secret !== null;

        return view('profile', compact('bank_accounts', 'user', 'verf_docs', 'wallet_accounts', 'idProof', 'addressProof', 'customerMfaEnabled', 'customerHasMfaSecret'));

    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        $email = auth()->user()->email;
        $user = DB::table('aspnetusers')->where('email', $email)->first();
        $datalog = [
            'email' => auth()->user()->email,
             'current_password' => $request->current_password,
            'new_password' => $request->new_password,
        ];

         addIpLog('Change Password', $datalog);
        if ($user && $user->password === $request->current_password) {
            DB::table('aspnetusers')->where('email', $email)->update(['password' => $request->new_password]);
            return response()->json(['success' => 'Password Successfully Changed']);
        } else {
            return response()->json(['message' => 'Current Password is not matched'], 422);
        }
    }
	
	public function profileUpdate(Request $request){
		$email = auth()->user()->email;
        $user = DB::table('aspnetusers')->where('email', $email)->first();
		
	}
	
      public function sumsub()
{
    $userId = session('clogin');
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Session expired. Please login again.');
    }
//  echo'<pre>';print_r($userId);exit;
    // Check if user is already KYC verified
    $user = DB::table('aspnetusers')->where('email', $userId)->first();
    if ($user && $user->kyc_verify == 1) {
        return redirect()->route('user-profile')->with('success', 'You have already verified your KYC.');
    }

    // Continue with Sumsub token generation
    // $secretKey = '70x9HmTmX1F1YhqZLPGI8M0vi7vjFX3X';
    $secretKey = '0S4qDM6qM3kozjkd3vXiS37H4mZtLV2U';
    // $appToken = 'prd:w35EjuwyBT4sBcFHz6fMBxdm.reHwfPw126L299BoLsc7pCf7FLOEEmLz'; '&levelName=id-and-liveness'
    // $appToken = 'sbx:nD08dyISlPoy7XFQDK9iaPJG.7Ursn7PhqEOE8Z0jY2UXnhBLBIG5c9z3';
    $appToken ='prd:Z8Ql5TNEtFu0bPmV4WJDgUL8.AlcN9gzPlGOUtybwBKofktFBPuE8bSTN';
    $timestamp = time();
    $apiUrl = '/resources/accessTokens?userId=' . urlencode($userId) . '&levelName=id-and-liveness';
    $requestMethod = 'POST';
    $requestBody = '';

    $valueToSign = $timestamp . $requestMethod . $apiUrl;
    if (!empty($requestBody)) {
        $valueToSign .= $requestBody;
    }

    $signature = hash_hmac('sha256', $valueToSign, $secretKey, true);
    $signatureHex = bin2hex($signature);

    // $curl = curl_init();
    // curl_setopt_array($curl, [
    //     CURLOPT_URL => 'https://api.sumsub.com' . $apiUrl,
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => $requestMethod,
    //     CURLOPT_POSTFIELDS => $requestBody,
    //     CURLOPT_HTTPHEADER => [
    //         'X-App-Token: ' . $appToken,
    //         'X-App-Access-Ts: ' . $timestamp,
    //         'X-App-Access-Sig: ' . $signatureHex,
    //     ],
    // ]);

    // $response = curl_exec($curl);

    // echo'<pre>';print_r($response);exit;
    $curlOptions = [
    CURLOPT_URL => 'https://api.sumsub.com' . $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => $requestMethod,
    CURLOPT_HTTPHEADER => [
        'X-App-Token: ' . $appToken,
        'X-App-Access-Ts: ' . $timestamp,
        'X-App-Access-Sig: ' . $signatureHex,
    ],
];

// Only add POSTFIELDS if not empty
if (!empty($requestBody)) {
    $curlOptions[CURLOPT_POSTFIELDS] = $requestBody;
}

$curl = curl_init();
curl_setopt_array($curl, $curlOptions);

$response = curl_exec($curl);


   
    if (curl_errno($curl)) {
        return response()->json(['error' => curl_error($curl)], 500);
    }

    $auth = json_decode($response);
    curl_close($curl);

 
    $token = $auth->token ?? null;

    if (!$token) {
        return redirect()->route('user-profile')->with('error', 'Failed to generate Sumsub access token.');
    }

    return view('sumsub', compact('token'));
}
    public function documentUpload()
    {
        return view('documentUpload');
    }
 public function uploadDocument(Request $request)
{
    $time = time() . "_";
    $email = session('clogin');
    $folder = 'public/_docs/';
    $dist = '/storage/_docs/';
    $allowed = ['jpeg', 'png', 'jpg'];

    // Validate all 3 files
    $validator = Validator::make($request->all(), [
        'image'  => 'required|file|mimes:' . implode(',', $allowed) . '|max:2048',
        'image1' => 'required|file|mimes:' . implode(',', $allowed) . '|max:2048',
        'image2' => 'required|file|mimes:' . implode(',', $allowed) . '|max:2048',
    ], [
        'image.max'  => 'The front side image must not be greater than 2MB.',
        'image1.max' => 'The back side image must not be greater than 2MB.',
        'image2.max' => 'The address proof image must not be greater than 2MB.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->with("error", $validator->errors()->first());
    }

    try {
        // ID Proof - Front
        $imagePath = $request->file('image')->storeAs($folder, $time . $request->file('image')->getClientOriginalName());
        // ID Proof - Back
        $image1Path = $request->file('image1')->storeAs($folder, $time . $request->file('image1')->getClientOriginalName());
        // Address Proof
        $image2Path = $request->file('image2')->storeAs($folder, $time . $request->file('image2')->getClientOriginalName());

        // Confirm all file paths
        if (!$imagePath || !$image1Path || !$image2Path) {
            return redirect()->back()->with("error", "File upload failed. Please try again.");
        }

        // Save ID Proof
        // KycUpdate::create([
        //     'email' => $email,
        //     'kyc_type' => 'ID Proof',
        //     'kyc_frontside' => $dist . basename($imagePath),
        //     'kyc_backside' => $dist . basename($image1Path),
        // ]);

        $this->pusherService->sendPusherMessage([
            'type' => 'Document Upload',
            'message' => 'A new KYC Document - ID Proof has been uploaded by ' . session('user')['fullname'],
            'link' => "/admin/kyc_details?id=" . md5($email),
            'enc_id' => md5($email)
        ]);

        // Save Address Proof
        // KycUpdate::create([
        //     'email' => $email,
        //     'kyc_type' => 'Address Proof',
        //     'front_image' => $dist . basename($image2Path),
        // ]);

        $idProof = KycUpdate::create([
                'email' => $email,
                'kyc_type' => 'ID Proof',
                'kyc_frontside' => $dist . basename($imagePath),
                'kyc_backside' => $dist . basename($image1Path),
            ]);

            // Save Address Proof
            $addressProof = KycUpdate::create([
                'email' => $email,
                'kyc_type' => 'Address Proof',
                'front_image' => $dist . basename($image2Path),
            ]);

            // Get the inserted IDs
            $latestIds = [$idProof->id, $addressProof->id];
// echo'<pre>';print_r($latestIds);exit;
          DB::table('aspnetusers')
            ->where('email', $email)
            ->update(['kycdocumentRequest' => 1]);
       KycUpdate::where('email', $email)
         ->whereNotIn('id', $latestIds)
         ->forceDelete();


        $this->pusherService->sendPusherMessage([
            'type' => 'Document Upload',
            'message' => 'A new KYC Document - Address Proof has been uploaded by ' . session('user')['fullname'],
            'link' => "/admin/kyc_details?id=" . md5($email),
            'enc_id' => md5($email)
        ]);
        

        $settings = settings();
            $emailSubject = $settings['admin_title'] . ' - KYC ';
                    $content = '<div>We are pleased to inform you that your KYC has been successfully submitted your account..</div>';
$datalog = [
             'name' => session('user')['fullname'],
                        'site_link' => $settings['copyright_site_name_text'],
                        "btn_text" => "Go To Dashboard",
                        'email' => $settings['email_from_address'],
                        "content" => $content,
                        "title_right" => "KYC",
                        "subtitle_right" => "KYC Document"
        ];
                    $templateVars = [
                        'name' => session('user')['fullname'],
                        'site_link' => $settings['copyright_site_name_text'],
                        "btn_text" => "Go To Dashboard",
                        'email' => $settings['email_from_address'],
                        "content" => $content,
                        "title_right" => "KYC",
                        "subtitle_right" => "KYC Document"
                    ];
                    $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);

                    addIpLog('KYC Documents  uploaded', $datalog);
               
        return redirect()->to('/user-profile')->with("success", "KYC Documents successfully uploaded.");

    } catch (\Exception $e) {
        return redirect()->back()->with("error", "Upload failed: " . $e->getMessage());
    }
}
 public function sumsub_verify(Request $request)
    {

      
        if (Session::has('clogin') && $request->has(['sumsub', 'type', 'payload'])) {
            $email = Session::get('clogin');
            $type = $request->input('type');
            $payload = $request->input('payload');
  
            // $type='idCheck.onApplicantStatusChanged';
            // $payload=['reviewStatus'=>'completed','reviewResult'=>["reviewAnswer"=>"GREEN"]];
            if ($type == 'idCheck.onApplicantStatusChanged') {
                // Store callback log in the database
                $datalog = [
                     'client_id' => $email,
                    'callback_code' => json_encode($type),
                    'callback_payload' => json_encode($payload),
                ];
                DB::table('kyc_logs')->insert([
                    'client_id' => $email,
                    'callback_code' => json_encode($type),
                    'callback_payload' => json_encode($payload),
                ]);
                
                // Check if review status is completed
                if (isset($payload['reviewStatus']) && $payload['reviewStatus'] == 'completed') {
                    // Check review result

  
                    if (isset($payload['reviewResult']['reviewAnswer']) && $payload['reviewResult']['reviewAnswer'] == 'GREEN') {
                        // Find the user in the database
                        $user = DB::table('aspnetusers')->where('email', $email)->first();

                        // Check if the user's KYC is already verified
                        if ($user && $user->kyc_verify == 1) {
                            return response()->json(['status' => 'true', 'message' => 'Your KYC Already Verifieds']);
                        }

                        // Update user's KYC status to verified
                        DB::table('aspnetusers')
                            ->where('email', $email)
                            ->update(['kyc_verify' => 1,'sumsub_verify' => 1]);

                        return response()->json(['status' => 'true', 'message' => 'KYC Verified']);
                    }
                    if (isset($payload['reviewResult']['reviewAnswer']) && $payload['reviewResult']['reviewAnswer'] == 'RED') {
                        // Find the user in the database
                        $user = DB::table('aspnetusers')->where('email', $email)->first();

                       
                        // Update user's KYC status to verified
                        DB::table('aspnetusers')
                            ->where('email', $email)
                            ->update(['sumsub_verify' => 2]);
 addIpLog('KYC sumsub_verify', $datalog);
                        return response()->json(['status' => 'false_not', 'message' => 'KYC Not Verified']);
                    } 
                    // else {     
                        
                    //     return response()->json(['status' => 'false', 'message' => 'Something went wrong. Please try again or Create a Support Ticket']);
                    // }
                    
                } else {
                    return response()->json(['status' => 'false', 'message' => 'Status in progress..']);
                }
            } else {
                return response()->json(['status' => 'false', 'message' => 'Status in progress...']);
            }
        }

        // Return a default response if session or parameters are missing
        return response()->json(['status' => 'false', 'message' => 'Invalid request.']);
    }
    public function storeBankDetails(Request $request)
    {
        $otp_type = 'Bank_Details_Otp';
        if (!session()->has($otp_type) || !request()->has('otp')) {
            return response()->json(['success' => false, 'message' => 'Please verify with OTP and proceed.']);
        } elseif (request('otp') != session($otp_type)) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP, Please try again.']);
        }
        $validatedData = $request->validate([
            'account_holder_name' => 'required|string',
            'bank_account_no' => 'required|string',
            'ifsc_code' => 'required|string',
            'swift_code' => 'required|string',
            'bank_name' => 'required|string',
        ]);
        $datalog = [
            'ClientName' => $validatedData['account_holder_name'],
            'accountNumber' => $validatedData['bank_account_no'],
            'code' => $validatedData['ifsc_code'],
            'swift_code' => $validatedData['swift_code'],
            'bankName' => $validatedData['bank_name'],
            'userId' => session('clogin'),
            'status' => 'success',
        ];
        ClientBankDetails::create([
            'ClientName' => $validatedData['account_holder_name'],
            'accountNumber' => $validatedData['bank_account_no'],
            'code' => $validatedData['ifsc_code'],
            'swift_code' => $validatedData['swift_code'],
            'bankName' => $validatedData['bank_name'],
            'userId' => session('clogin'),
            'status' => 'success',
        ]);
         addIpLog('KYC sumsub_verify', $datalog);
        addIpLog('Bank Details-Add');
        return response()->json(['success' => true]);
    }


    public function deleteBankDetails(Request $request, $enc, $otp = null)
    {
        $otp_type = 'Bank_Delete_Otp';
        if (!session()->has($otp_type) || !$otp) {
            return redirect()->route("user-profile")->with("error", "Please verify with OTP and proceed.");
        } elseif ($otp != session($otp_type)) {
            return redirect()->route("user-profile")->with("error", "Invalid OTP, Please try again.");
        }
        $bankDetail = ClientBankDetails::whereRaw('MD5(id) = ?', [$enc])->first();
        if ($bankDetail) {
            $bankDetail->delete();
             addIpLog('Bank Details-Delete', $bankDetail);
            addIpLog('Bank Details-Delete');
            return redirect()->route("user-profile")->with("success", "Your bank detail successfully removed.");
        } else {
            return redirect()->route("user-profile")->with("error", "Bank details not found.");
        }
    }
	
	public function getOffers(Request $request)
    {
		$user = auth()->user();		
		$email = $user->email;
		$group_id = $user->group_id;  // make sure your user table has group_id
		
        $datalog = [
          	$user = auth()->user(),		
		$email = $user->email,
		$group_id = $user->group_id, 
        ];

		$accountTypes = DB::table('account_types')
			->where('user_group_id', $group_id)
			->pluck('ac_index');

		$acIndexes = $accountTypes->unique()->toArray();

		$bonuses = BonusModel::where('status', 1)
			->where(function ($q) use ($email, $acIndexes) {   // <-- FIXED

			// 1️⃣ All Users
			$q->where('bonus_shows_on', 'all')

			// 2️⃣ User Email Based Bonus
			->orWhere(function ($q2) use ($email) {
				$q2->where('bonus_shows_on', 'users')
				   ->whereRaw("FIND_IN_SET(?, bonus_show_list)", [$email]);
			})

			// 3️⃣ Group / Account-Type Based Bonus
			->orWhere(function ($q3) use ($acIndexes) {
				if (!empty($acIndexes)) {
					foreach ($acIndexes as $idx) {
						$q3->orWhereRaw("FIND_IN_SET(?, bonus_show_list)", [$idx]);
					}
				}
			});
		})
		->whereDate('bonus_starts_at', '<=', now())
		->whereDate('bonus_ends_at', '>=', now())
		->orderBy('bonus_id', 'DESC')
		->get();
        
         $datalog = [
          	$user = auth()->user(),		
		$email = $user->email,
		$group_id = $user->group_id, 
       
        ];
        addIpLog('View Offers', $datalog);	
		return view('offers', compact('user','bonuses'));  
	}
	
	public function getPromotions(Request $request)
    {
		$user = auth()->user();		
		$email = $user->email;
		$promotionsdata = Promotation::where('status', 1)
			->whereDate('promo_starts_at', '<=', now())
			->whereDate('promo_ends_at', '>=', now())
			->orderBy('promo_id', 'DESC')
			->get();
            addIpLog('view Promotions', $email);
		return view('promotions', compact('user','promotionsdata')); 
	}
	public function promoenroll(Request $request)
    {
		$email = auth()->user()->email;
		$promoselgroup = collect(); // default empty
		$promoselapplyacc = collect(); // default empty
		$leveragegroup = collect(); // default empty
		$promo = Promotation::whereRaw("MD5(promo_id) = ?", [$request->promoid])->first();
		$groupIds = [];
		if (!empty($promo->promo_groups)) {
			$groupIds = explode(',', $promo->promo_groups);
			$firstGroupId = trim($groupIds[0]);
			$leveragegroup = Leverage::where('account_type_id', $firstGroupId)->get();
		}
		
		if (!empty($groupIds)) {
			$promoselgroup = AccountType::whereIn('ac_index', $groupIds)->get();
		}

		if ($promo->promo_apply_for == 'exist') {
			$promoselapplyacc = LiveAccount::with('accountType')
				->where('email', $email)
				->where('status', 'active')
				->whereIn('account_type', $groupIds) // match ac_index
				->orderBy('id', 'desc')
				->get();
			$linkaccount = 'update-live-account';
			$btntext = 'Update Account';
		} else {
			$linkaccount = 'create-live-account';
			$btntext = 'Create Account';
		}		
         addIpLog('PromoEnroll Promotions', $promo);
		return view('promoenroll', compact('promo', 'promoselgroup', 'promoselapplyacc', 'leveragegroup', 'linkaccount', 'btntext')); 
	}
	
	public function getbonusenroll(Request $request)
    {
		$bonuscode = '';
		if ($request->filled('bonusid')) {
			$bonuscode = BonusModel::whereRaw("MD5(bonus_id) = ?", [$request->bonusid])->first();
		}
		$email = auth()->user()->email;        

		$user = User::where('email', $email)->first();
        $user_groups = UserGroup::find(session('user')['group_id']);
        
        /*$liveaccount_details = LiveAccount::with('accountType')
            ->where('email', $email)
            ->where('status', 'active')			
            ->get();*/	

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
			
		$bonusname = $bonuscode->bonus_name.' - Claim Now ('.$bonuscode->bonus_code.')';	
         addIpLog('Bonusenroll Promotions', $liveaccount_details);
        return view('bonus_claim', compact('liveaccount_details', 'user_groups', 'user', 'bonusname', 'bonuscode'));		
	}
	
	public function bonuspreview(Request $request){
		
		$account = LiveAccount::select(
			'liveaccount.*',
				DB::raw('COALESCE(SUM(td.deposit_amount), 0) as five_day_deposit')
			)
			->where('liveaccount.trade_id', $request->trade_id)
			->where('liveaccount.email', auth()->user()->email)
			->leftJoin('trade_deposit as td', function ($join) {
				$join->on('liveaccount.trade_id', '=', 'td.trade_id')
					 ->whereRaw("
						td.deposted_date <= DATE_ADD(
							(
								SELECT MIN(td2.deposted_date)
								FROM trade_deposit td2
								WHERE td2.trade_id = liveaccount.trade_id
							),
							INTERVAL 5 DAY
						)
					 ");
			})
			->groupBy('liveaccount.trade_id')
			->first();
		$depositAmount = $account->five_day_deposit;
		if (!$account) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid account selected'
			]);
		}

		$bonus = BonusModel::whereRaw("MD5(bonus_id) = ?", [$request->bonus_id])->first();
		if (!$bonus) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid bonus'
			]);
		}
		if($bonus->bonus_type == 'percentage'){
			$bonusvalue = number_format($bonus->bonus_value, 2).'%';
			$bonusAmount = ($depositAmount * $bonus->bonus_value) / 100;
		} else {
			$bonusvalue = number_format($bonus->bonus_value, 2).'USD';
			$bonusAmount = $bonus->bonus_value;
		}
        $datalog = [
            'status' => true,
			'trade_id' => $account->trade_id,
			'deposit' => number_format($depositAmount, 2),
			'bonus_percent' => $bonusvalue,
			'bonus_amount' => number_format($bonusAmount, 2)
        ];
        addIpLog('Bonusenroll Promotions', $datalog);
		return response()->json([
			'status' => true,
			'trade_id' => $account->trade_id,
			'deposit' => number_format($depositAmount, 2),
			'bonus_percent' => $bonusvalue,
			'bonus_amount' => number_format($bonusAmount, 2)
		]);
	}
	
	public function applybonus(Request $request){		
		$email = session('clogin');
		DB::beginTransaction();
		
		try {
			$bonusDetails = BonusModel::whereRaw("MD5(bonus_id) = ?", [$request->bonus_id])->first();
			//Check the logic based on bonus terms*/
			if ($bonusDetails->bonus_accessable == "first_deposit" || $bonusDetails->bonus_accessable == "welcome_bouns") {
				$checkexistdep = TradeDeposits::where('email', $email)
					->where('trade_id', $request->trade_id)
					->count();
				if ($checkexistdep != 0) {					
					return response()->json([
						'status' => false,
						'message' => 'This Bouns code Not applicable first Deposit. Already you are did deposited.'
					]);
				}
			} else if ($bonusDetails->bonus_accessable == "regular_bouns") {
				$checkexistdep = TradeDeposits::where('email', $email)
					->where('trade_id', $request->trade_id)
					->count();
				if ($checkexistdep > 0) {
					echo "ok";
				} else {
					return redirect()->back()->with('error', 'Regular bonus not applicable this account.');
				}
			} else if ($bonusDetails->bonus_accessable == "direct_users") {
				$checkexistdep = TradeDeposits::where('email', $email)
					->where('trade_id', $request->trade_id)
					->count();
				if ($checkexistdep == 0) {
					echo "ok";
				} else {
					echo "No"; 
				}
			} else {
				echo "No Issues";
			}					
			$account = LiveAccount::select(
				'liveaccount.*',
					DB::raw('COALESCE(SUM(td.deposit_amount), 0) as five_day_deposit')
				)
				->where('liveaccount.trade_id', $request->trade_id)
				->where('liveaccount.email', auth()->user()->email)
				->leftJoin('trade_deposit as td', function ($join) {
					$join->on('liveaccount.trade_id', '=', 'td.trade_id')
						 ->whereRaw("
							td.deposted_date <= DATE_ADD(
								(
									SELECT MIN(td2.deposted_date)
									FROM trade_deposit td2
									WHERE td2.trade_id = liveaccount.trade_id
								),
								INTERVAL 5 DAY
							)
						 ");
				})
				->groupBy('liveaccount.trade_id')
				->first();
			$depositAmount = $account->five_day_deposit;	
			
			$bonusValue = 0;
			if($bonusDetails->bonus_type == 'percentage'){
				$bonusvalue = number_format($bonusDetails->bonus_value, 2).'%';
				$bonusAmount = ($depositAmount * $bonusDetails->bonus_value) / 100;
			} else {
				$bonusvalue = number_format($bonusDetails->bonus_value, 2).'USD';
				$bonusAmount = $bonusDetails->bonus_value;
			}
			
			if (!empty($request->bonus_id)) {
			
				$bonusDepositCurrency = "USD";
				$bonusDepositType = "Bonus In";
				$bonusDescription = '';
				$bonusTransaction = BonusTransaction::create([
					'email' => $email,
					'trade_id' => $request->trade_id,
					'bonus_amount' => $bonusAmount,
					'bonus_type' => 'Bonus In',
					'bonus_id' => $bonusDetails->bonus_id,
					'status' => 1,
					'adminRemark' => $bonusDescription,
					'bonus_currency' => $bonusDepositCurrency,
					'created_by' => session('clogin'),
				]);
				$bonusTransId = $bonusTransaction->id;
			} else {
				$bonusTransId = 0;
			}
			
            $datalog = [
                'email' => $email,
				'trade_id' => $request->trade_id,
				'deposit_currency_amount' => 0,
				'deposit_type' => 'Deposit',
				'deposit_currency' => "USD",
				'Status' => 1,
				'deposit_amount' => 0,
				'bonus_amount' => $bonusAmount,
				'bonus_trans_id' => $bonusTransId
            ];
			$TradeDeposits = TradeDeposits::create([
				'email' => $email,
				'trade_id' => $request->trade_id,
				'deposit_currency_amount' => 0,
				'deposit_type' => 'Bonus Deposit',
				'deposit_currency' => "USD",
				'Status' => 1,
				'deposit_amount' => 0,
				'bonus_amount' => $bonusAmount,
				'bonus_trans_id' => $bonusTransId
			]);
			
            addIpLog('Apply Bonus Promotions', $datalog);
			$settings = settings();	
			$comment = "Bonus";
			
			$error = null;
            $ticket = null;
            if ($bonusAmount > 0 && ($error_code = $this->api->TradeBalance($request->trade_id, MTEnDealAction::DEAL_BONUS, $bonusAmount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);
            } else {
				DB::table('trade_deposit as td')
				->where('td.id', '=', $TradeDeposits->id)
				->update([
					'AdminRemark' => 'Bonus amount applied',
					'Status' => 1,
					'admin_email' => 'support@leveragemarkets.com'
				]);
                DB::table('total_balance')->insert([
                    'email' => $email,
                    'trading_deposited' => $bonusAmount,
                ]);
				
				$depositDetails = DB::table('trade_deposit as td')
                    ->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
                    ->select('td.id', 'ap.fullname', 'td.email', 'td.trade_id', 'td.deposit_amount as amount', 'td.bonus_amount', 'td.bonus_trans_id', 'td.deposted_date as date', 'td.deposit_type as type')
                    ->where('td.id', '=', $TradeDeposits->id)
                    ->first();
				
				$emailSubject = $settings['admin_title'] . ' - Transaction Approved';
                $transid = "TDID" . str_pad($depositDetails->id, 4, '0', STR_PAD_LEFT);
                $content = '<div>We are pleased to inform you that your bonus has been successfully applied. </div>
        <div>The applied amount has been deposited into your account.</div>
        <div><b>Transaction Details</b></div>
        <div><b>Applied Amount: </b>$' . $bonusAmount . '</div>';
                
                $content .= '<div><b>Account ID: </b>' . $request->trade_id . '</div>
				<div><b>Transaction ID: </b>' . $transid . '</div>
				<div><b>Deposited Date: </b>' . $depositDetails->date . '</div>
				<div><b>Deposit Type </b>' . $depositDetails->type . '</div>';
                $templateVars = [
                    'name' => $depositDetails->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    "btn_text" => "Go To Dashboard",
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Transaction",
                    "subtitle_right" => "Applied"
                ];
                
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                //return redirect()->back()->with('success', 'Bonus Applied Successfully');                		
			}
			DB::commit();
			return redirect()->back()->with('success', 'Bonus Applied Successfully');      

		} catch (\Exception $e) {
			
			DB::rollBack();
			return redirect()->back()->with('error', 'Bonus Not Applied Successfully! Contact Site Admin.');
		}
	}

	public function profileupate(Request $request)
	{
		$email = auth()->user()->email;
		$user = DB::table('aspnetusers')->where('email', $email)->first();
		$updateData = [
			'gender' => $request->gender ?? 'Others',
		];
		if ($request->hasFile('profile_image')) {
			$folder = 'uploads/profile';
			// $time = time() . '_';
			$imageName =  $request->file('profile_image')->getClientOriginalName();

			// Store image in public disk
			$request->file('profile_image')->storeAs($folder, $imageName, 'public');

			// Delete old image
			if (!empty($user->profile_image) && Storage::disk('public')->exists($folder . '/' . $user->profile_image)) {
				Storage::disk('public')->delete($folder . '/' . $user->profile_image);
			}

			$updateData['profile_image'] = $imageName;
		}
		DB::table('aspnetusers')->where('email', $email)->update($updateData);
		$user = DB::table('aspnetusers')->where('email', $email)->first();
          addIpLog('Update Bonusen Promotions', $updateData);
		return response()->json(['success' => 'Profile Updated Successfully']);
	}
}
