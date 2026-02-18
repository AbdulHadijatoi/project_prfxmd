<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IbPlanDetails;
use Illuminate\Http\Request;
use App\Models\Leverage;
use App\Models\Ib1;
use App\Models\Mt5GroupCategory;
use App\Models\Mt5Group;
use App\Models\IBCategory;
use App\Models\AccountType;
use App\Models\User;
use DB;
use Exception;
use App\MT5\MTWebAPI;
use App\Services\MT5Service;
use App\MT5\MTRetCode;
use App\Services\MailService as MailService;

// use Illuminate\Support\Facades\Hash;

class ApiAjaxController extends Controller
{
    protected $api;
    protected $mailService;

    protected $mt5Service;
    public function __construct(MT5Service $mt5Service, MTWebAPI $api, MailService $mailService)
    {
        $this->mailService = $mailService;

        $this->mt5Service = $mt5Service;
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
    }
    public function handleRequest(Request $request)
    {
        // dd($request->all());

       
        if ($request->has('type') && $request->type == 'leverage') {
            $leverage = Leverage::where('account_type_id', $request->id)->get();
            return response()->json($leverage);
        }

        if ($request->has('client_id')) {
            return $this->handleClientRequest($request);
        }

        if ($request->has('get_groupcat') && $request->has('id')) {
            $groupCat = DB::table("mt5_group_categories")->where(DB::raw('md5(mt5_grp_cat_id)'), $request->id)->first();
            return $groupCat ? response()->json($groupCat) : response()->json(false);
        }

        if ($request->has('get_groupMains') && $request->has('id')) {
            $groupMain = Mt5Group::where(DB::raw('md5(mt5_group_id)'), $request->id)->first();
            return $groupMain ? response()->json($groupMain) : response()->json(false);
        }

        if ($request->has('get_ibplan') && $request->has('id')) {
            $ibPlan = IbCategory::where(DB::raw('md5(ib_cat_id)'), $request->id)->first();
            return $ibPlan ? response()->json($ibPlan) : response()->json(false);
        }

        if ($request->has('group_update') && $request->id) {
            // dd("Update");
            $updated = DB::table("mt5_group_categories")->where(DB::raw('md5(mt5_grp_cat_id)'), $request->id)
                ->update([
                    'mt5_grp_cat_type' => $request->mt5_grp_cat_type,
                    'mt5_grp_cat_name' => $request->mt5_grp_cat_name,
                    'mt5_grp_cat_desc' => $request->mt5_grp_cat_desc,
                    'is_active' => $request->is_active
                ]);
            return response()->json($updated ? 'true' : 'false');
        }

        if ($request->has('ib_plan_update')) {
            return $request->id ? $this->updateIbPlan($request) : $this->createIbPlan($request);
        }

        if ($request->has('groupMain_update')) {
            return $request->id ? $this->updateGroupMain($request) : $this->createGroupMain($request);
        }

        if ($request->has('group_update')) {
            $updated = DB::table("mt5_group_categories")->insert([
                'mt5_grp_cat_type' => $request->mt5_grp_cat_type,
                'mt5_grp_cat_name' => $request->mt5_grp_cat_name,
                'mt5_grp_cat_desc' => $request->mt5_grp_cat_desc,
                'is_active' => $request->is_active
            ]);
            return response()->json($updated ? 'true' : 'false');
            // return $this->createGroupCategory($request);
        }

        if ($request->has('groupCreation')) {

            // echo'<pre>';print_r($request->all());exit;
            return $this->createGroup($request);
        }

        if ($request->has('groupUpdation')) {
            return $this->updateGroup($request);
        }

        if ($request->has('ibPlanUpdate')) {
            return $this->updateIbPlanData($request);
        }

        return response()->json('false');
    }

    private function handleClientRequest($request)
    {
        $clientId = $request->client_id;
        $ibStatus = $request->ib_status;
        $ibGroup = $request->ib_group;

        $ib = Ib1::where(DB::raw('md5(email)'), $clientId)->first();

        if (!$ib) {
            $user = User::where(DB::raw('md5(email)'), $clientId)->first();
            if ($user) {
                $ib = new Ib1();
                $ib->uid = $user->uid;
                $ib->email = $user->email;
                $ib->password = $user->password;
                $ib->number = $user->number;
                $ib->username = $user->email;
                $ib->name = $user->fullname;
                $ib->country = $user->country;
                $ib->emailToken = $user->emailToken;
                $ib->status = 1;
                $ib->save();
            }
        }

        $ibUpdate = Ib1::where(DB::raw('md5(email)'), $clientId)
            ->update(['status' => $ibStatus, 'acc_type' => $ibGroup]);

               $datalogs = [
        'client_id' => $clientId,
        'ib_status' => $ibStatus,
        'ib_group'  => $ibGroup,
        'updated'   => $ibUpdate,
        'ip'        => request()->ip(),
        'time'      => now()
    ];

    addIpLog('handleClientRequest', $datalogs);

        return response()->json($ibUpdate ? 'true' : 'false');
    }

    private function createIbPlan($request)
    {

    $datalogs = [
        'ib_cat_name' => $request->ib_cat_name,
            'ib_cat_desc' => $request->ib_cat_desc,
            'is_active' => $request->is_active
    ];
        $ibPlan = IBCategory::create([
            'ib_cat_name' => $request->ib_cat_name,
            'ib_cat_desc' => $request->ib_cat_desc,
            'is_active' => $request->is_active
        ]);
 addIpLog('createIbPlan', $datalogs);
        return response()->json($ibPlan ? 'true' : 'false');
    }

    private function updateGroupMain($request)
    {

    $datalogs = [ 'mt5_group_name' => $request->mt5_group_name,
                'mt5_group_desc' => $request->mt5_group_desc,
                'is_active' => $request->is_active,
                'updated_by' => session('alogin'),
                'user_group_id' => $request->user_group_id];
        $updated = Mt5Group::where(DB::raw('md5(mt5_group_id)'), $request->id)
            ->update([
                'mt5_group_name' => $request->mt5_group_name,
                'mt5_group_desc' => $request->mt5_group_desc,
                'is_active' => $request->is_active,
                'updated_by' => session('alogin'),
                'user_group_id' => $request->user_group_id
            ]);
 addIpLog('updateGroupMain', $datalogs);
        return response()->json($updated ? 'true' : 'false');
    }

    private function createGroupMain($request)
    {
        $datalogs = [
            'mt5_group_name' => $request->mt5_group_name,
            'mt5_group_type' => $request->mt5_group_type,
            'mt5_group_desc' => $request->mt5_group_desc,
            'is_active' => $request->is_active,
            'updated_by' => session('alogin'),
            'user_group_id' => $request->user_group_id
        ];
        $group = Mt5Group::create([
            'mt5_group_name' => $request->mt5_group_name,
            'mt5_group_type' => $request->mt5_group_type,
            'mt5_group_desc' => $request->mt5_group_desc,
            'is_active' => $request->is_active,
            'updated_by' => session('alogin'),
            'user_group_id' => $request->user_group_id
        ]);
 addIpLog('createGroupMain', $datalogs);
        return response()->json($group ? 'true' : 'false');
    }

    private function createGroup1($request)
    {
        try {
            DB::beginTransaction();

            $accountType = new AccountType();
            $accountType->ac_type = $request->ac_type;
            $accountType->ac_name = $request->ac_name;
            $accountType->ac_group = $request->ac_group;
            $accountType->ac_min_deposit = $request->ac_min_deposit;
            $accountType->ac_max_leverage = $request->ac_max_leverage;
            $accountType->ac_spread = $request->ac_spread;
            $accountType->ac_swap = $request->ac_swap;
            $accountType->status = $request->status;
            $accountType->ib_enabled = $request->ib_enabled;
            $accountType->ac_category = $request->ac_category;
            $accountType->ac_book_type = $request->ac_book_type;
            $accountType->is_client_group = $request->is_client_group;
            $accountType->inquiry_status = $request->inquiry_status;
            $accountType->display_priority = $request->display_priority ?? 0;
            $accountType->save();

            foreach (explode(",", $request->ac_max_leverage) as $lev) {
                Leverage::create([
                    'account_type_id' => $accountType->ac_index,
                    'account_leverage' => $lev
                ]);
            }

            DB::commit();
            return response()->json('true');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["status" => 'false', "message" => $e->getMessage()]);
        }
    }
    private function createGroup($request)
    {
      
             
        if ($request->has('groupCreation')) {
            if (empty($request->input('ac_index'))) {
                try {
                    $userGroup = DB::table('mt5_groups')
                        ->where('mt5_group_id', $request->input('ac_type'))
                  
                        ->select('user_group_id')
                        ->first();
                    if (!$userGroup) {
                        return response()->json(['error' => 'User group not found'], 400);
                    }
                    $displaypriority = DB::table('account_types')
                        // ->where('mt5_group_id', $request->input('ac_type'))
                        ->where('display_priority', $request->input('display_priority'))
                        ->select('display_priority')
                        ->first();
                       
                        if ($displaypriority) {
                        return response()->json(['error' => 'display priority already used'], 400);
                    }
                    // $ac_group = str_replace('\', '\\', $request->input('ac_group'));
                    $ac_group = $request->input('ac_group');
                    $is_exist = DB::table('account_types')
                        ->where('ac_group', $ac_group)
                        ->exists();
                    if ($is_exist) {
                        return response()->json(['error' => 'Group name already exists.'], 409);
                    }
                    $newGroup = $this->api->GroupCreate();
                    // $symb=$this->api->SymbolGet("*",$test);
                    // echo MTRetCode::GetError($symb);
                    // exit();
                    $symbol = $this->api->SymbolCreate();
                    $symbol->Symbol = '*';
                    $newGroup->Group = $ac_group;
                    $newGroup->Commissions = 0;
                    $newGroup->Symbols = [$symbol];
                    $newGroup->Company = settings()['mt5_company_name'];
                    $newGroup->Server = 1;
                    $newGroup->MarginMode = 2;
                    $newGroup->LimitPositions = 0;

                    $error_code = $this->api->GroupAdd($newGroup, $new_group);
                    if ($error_code != MTRetCode::MT_RET_OK) {
                        return response()->json([
                            'error' => "Something went wrong. Please try again later. Code: $error_code [" . MTRetCode::GetError($error_code) . "]"
                        ], 500);
                    }
                    // Insert group details into the database
                    $inquiry_status=$request->input('inquiry_status');
                    $is_client_group=$inquiry_status==2?0:$request->input('is_client_group');
                     $imageName = null;
                        $folder = 'uploads/groupimg';

                        if ($request->hasFile('image')) {

                            $file = $request->file('image');

                            // Optional: unique name to avoid overwrite
                            $imageName = time() . '_' . $file->getClientOriginalName();

                            // Store image in public disk
                            $file->storeAs($folder, $imageName, 'public');
                        }
                        $datalogs = [
                            'ac_type' => $request->input('ac_type'),
                        'ac_name' => $request->input('ac_name'),
                        'ac_group' => $ac_group,
                        'ac_min_deposit' => $request->input('ac_min_deposit'),
                        'ac_max_leverage' => $request->input('ac_max_leverage'),
                        'ac_spread' => $request->input('ac_spread'),
                        'ac_swap' => $request->input('ac_swap'),
                        'status' => $request->input('status'),
                        'ib_enabled' => $request->input('ib_enabled'),
                        'ac_category' => $request->input('ac_category'),
                        'ac_book_type' => $request->input('ac_book_type'),
                        'is_client_group' => $is_client_group,
                        'user_group_id' => $userGroup->user_group_id,
                        'display_priority'=>$request->input('display_priority'),
                        'inquiry_status'=>$inquiry_status,
                        'image'=> $imageName,
                        'ac_description' =>$request->input('ac_description')
                        ];
                    $accountTypeId = DB::table('account_types')->insertGetId([
                        'ac_type' => $request->input('ac_type'),
                        'ac_name' => $request->input('ac_name'),
                        'ac_group' => $ac_group,
                        'ac_min_deposit' => $request->input('ac_min_deposit'),
                        'ac_max_leverage' => $request->input('ac_max_leverage'),
                        'ac_spread' => $request->input('ac_spread'),
                        'ac_swap' => $request->input('ac_swap'),
                        'status' => $request->input('status'),
                        'ib_enabled' => $request->input('ib_enabled'),
                        'ac_category' => $request->input('ac_category'),
                        'ac_book_type' => $request->input('ac_book_type'),
                        'is_client_group' => $is_client_group,
                        'user_group_id' => $userGroup->user_group_id,
                        'display_priority'=>$request->input('display_priority'),
                        'inquiry_status'=>$inquiry_status,
                        'image'=> $imageName,
                        'ac_description' =>$request->input('ac_description')
                    ]);
                    foreach (explode(",", $request->ac_max_leverage) as $lev) {
                        Leverage::create([
                            'account_type_id' => $accountTypeId,
                            'account_leverage' => $lev
                        ]);
                    }
                    addIpLog('createGroup', $datalogs);
                    return response()->json(['success' => true]);
                } catch (Exception $e) {
                    dd($e);
                    return response()->json(['error' => 'An error occurred.' . $e->getMessage()], 500);
                }
            } else {
                return response()->json(['error' => 'Invalid input.'], 400);
            }
        }
        return response()->json(['error' => 'No group creation requested.'], 400);
    }

    private function updateGroup($request)
    {
        try {
           $current = DB::table('account_types')
                        ->whereRaw('md5(ac_index) = ?', [$request->ac_index])
                        ->select('ac_index', 'display_priority')
                        ->first();

                    if (!$current) {
                        return response()->json([
                            'error' => 'Invalid account type',
                            'status' => false
                        ], 400);
                    }
                    if ($current->display_priority != $request->display_priority) {

                        $exists = DB::table('account_types')
                            ->where('display_priority', $request->display_priority)
                            ->where('ac_index', '!=', $current->ac_index)
                            ->exists();

                        if ($exists) {
                            return response()->json([
                                'error' => 'Display priority already used by another account.',
                                'status' => false
                            ], 400);
                        }
                    }
            $accountType = AccountType::where(DB::raw("md5(ac_index)"), $request->ac_index)->first();
            if ($accountType) {
                $imageName = $accountType->image ?? null;

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $imageName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('uploads/groupimg', $imageName, 'public');
                }

                $accountType->ac_name = $request->ac_name;
                $accountType->image = $imageName ;
                $accountType->ac_min_deposit = $request->ac_min_deposit;
                $accountType->ac_max_leverage = $request->ac_max_leverage;
                $accountType->ac_swap = $request->ac_swap;
                $accountType->ac_spread = $request->ac_spread;
                $accountType->user_group_id = $request->user_group_id;
                $accountType->is_client_group = $request->inquiry_status==2?0:$request->is_client_group;;
                $accountType->status = $request->status;
                $accountType->ib_enabled = $request->ib_enabled;
                $accountType->inquiry_status = $request->inquiry_status;
                $accountType->display_priority = $request->display_priority ?? 0;
                 $accountType->ac_description = $request->ac_description;
                $accountType->save();
                Leverage::where('account_type_id', $accountType->ac_index)->delete();

                foreach (explode(",", $request->ac_max_leverage) as $lev) {
                    Leverage::create([
                        'account_type_id' => $accountType->ac_index,
                        'account_leverage' => $lev
                    ]);
                }
  $datalogs = [
                'ac_index_md5'     => $request->ac_index,
                'ac_index'         => $accountType->ac_index,
                'ac_name'          => $accountType->ac_name,
                'display_priority' => $accountType->display_priority,
                'status'           => $accountType->status,
                'user_group_id'    => $accountType->user_group_id,
                'leverage_values'  => $request->ac_max_leverage,
                'updated_by_ip'    => request()->ip(),
                'updated_at'       => now()
            ];

            addIpLog('updateGroup', $datalogs);
                return response()->json('true');
            }
            return response()->json(["status" => 'false', "message" => "Group is not Exist"]);
        } catch (Exception $e) {
            return response()->json(["status" => 'false', "message" => $e->getMessage()]);
        }
    }

    private function updateIbPlanData($request)
    {
        try {
            DB::beginTransaction();

            IbPlanDetails::where('ib_plan_cat_id', $request->ib_plan_cat_id)
                ->where('ib_acc_type_id', $request->ib_acc_type_id)
                ->update(['status' => 0, 'updated_by' => session('alogin'), 'deleted_at' => now()]);
$datalogs = [
    'ib_acc_type_id' => $request->ib_acc_type_id,
                'ib_plan_cat_id' => $request->ib_plan_cat_id,
                'ib_plan_code' => $request->ib_plan_code,
                'ib_plan_amount' => $request->ib_plan_amount,
                'ib_plan_type' => $request->ib_plan_type,
                'ib_plan_desc' => $request->ib_plan_desc,
                'updated_by' => session('alogin')
];
            IbPlanDetails::create([
                'ib_acc_type_id' => $request->ib_acc_type_id,
                'ib_plan_cat_id' => $request->ib_plan_cat_id,
                'ib_plan_code' => $request->ib_plan_code,
                'ib_plan_amount' => $request->ib_plan_amount,
                'ib_plan_type' => $request->ib_plan_type,
                'ib_plan_desc' => $request->ib_plan_desc,
                'updated_by' => session('alogin')
            ]);

            DB::commit();
             addIpLog('updateGroup', $datalogs);
            return response()->json('true');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('false');
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
        $datalogs = [
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
        addIpLog('sendMail', $datalogs);
        $this->mailService->sendEmail($toEmail, $emailSubject, $headers, '', $templateVars);
    }
    public function liveaccountCreation(Request $request)
    {
        // dd($request->all());
        $settings = settings();
        $email = $request->input('client_id');
        $accType = $request->input('acc-types');
        $leverage = $request->input('leverage');

        // Fetch user details
        $user = DB::table('aspnetusers')
            ->where(DB::raw('md5(lower(email))'), $email)
            ->first();

        if (!$user) {
            return "Invalid Client. Please create MT5 with proper client";
        }

        // Fetch account type details
        $group = DB::table('account_types')
            ->where('ac_index', $accType)
            ->first();

        if (!$group) {
            return "Invalid Account Group. Please create MT5 with actual Account Group";
        }

        // Initialize MT5 API and create user
        // $api = resolve('MT5ApiService'); // Assuming a service for MT5 API is resolved here

        // if (!$this->api->isConnected()) {
        //     $connectResult = $this->api->connect(config('mt5.server_ip'), config('mt5.server_port'), 300, config('mt5.web_login'), config('mt5.web_password'));

        //     if ($connectResult !== MTRetCode::MT_RET_OK) {
        //         return back()->with('error', 'Unable to connect to MT5 API: ' . MTRetCode::getError($connectResult));
        //     }
        // }

        $newUser = $this->api->userCreate();
        $newUser->MainPassword = $this->generatePassword();
        $newUser->Group = $group->ac_group;
        $newUser->Leverage = $leverage;
        $newUser->ZipCode = $user->zipcode;
        $newUser->Country = $user->country;
        $newUser->State = $user->state;
        $newUser->City = $user->city;
        $newUser->Address = $user->address;
        $newUser->Phone = ""; // Add if phone is available
        $newUser->Currency = 'USD';
        $newUser->Company = $settings['mt5_company_name'];
        $newUser->Name = $user->fullname;
        $newUser->Email = ""; // Optionally set email if needed
        $newUser->LeadSource = $user->ib1 == 'noIB' ? "" : $user->ib1;
        $newUser->PhonePassword = $this->generatePassword();
        $newUser->InvestPassword = $this->generatePassword();
        $newUser->Login = $this->generateRandomNumber();

        // $this->api->userAdd($newUser,$createResult);
        $user_server = NULL;
        if (($error_code = $this->api->UserAdd($newUser, $user_server)) != MTRetCode::MT_RET_OK) {
            alert()->warning('Error', 'Error creating user: ' . MTRetCode::getError($error_code));
            return "Error Occured. " . MTRetCode::getError($error_code);
        }

        // Save live account details to the database
        $datalogs = [
             'email' => $user->email,
            'name' => $newUser->Name,
            'trade_id' => $newUser->Login,
            'account_type' => $accType,
            'leverage' => $leverage,
            'currency' => "USD",
            'trader_pwd' => $newUser->MainPassword,
            'invester_pwd' => $newUser->InvestPassword,
            'phone_pwd' => $newUser->PhonePassword,
            'ib1' => $newUser->LeadSource,
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
            'ib15' => $user->ib15
        ];
        DB::table('liveaccount')->insert([
            'email' => $user->email,
            'name' => $newUser->Name,
            'trade_id' => $newUser->Login,
            'account_type' => $accType,
            'leverage' => $leverage,
            'currency' => "USD",
            'trader_pwd' => $newUser->MainPassword,
            'invester_pwd' => $newUser->InvestPassword,
            'phone_pwd' => $newUser->PhonePassword,
            'ib1' => $newUser->LeadSource,
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
            'ib15' => $user->ib15
        ]);
 addIpLog('liveaccountCreation', $datalogs);
        try {
            $this->sendMail($newUser, 'Live');
            return "true";
        } catch (\Exception $e) {
            return "Exception Occured. Please contact support." . $e->getMessage();
        }
    }

    public function generatePassword($length = 9)
    {
        // Define character pools
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#';
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
         $datalogs = [
        'action' => 'password_generated',
        'length' => $length,
        'ip'     => request()->ip(),
        'time'   => now()
    ];

    addIpLog('liveaccountCreation', $datalogs);
        $password = str_shuffle($password);
        return $password;
    }
    function generateRandomNumber($length = 6)
    {
        $min = pow(10, $length - 1); // Minimum value for an 8-digit number (10000000)
        $max = pow(10, $length) - 1;  // Maximum value for an 8-digit number (99999999)
        return rand($min, $max);
    }
}
