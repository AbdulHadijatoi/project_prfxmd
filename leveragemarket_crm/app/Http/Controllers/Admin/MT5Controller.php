<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AccountHelper;
use App\Http\Controllers\Controller;
use App\Models\BonusTrans;
use App\Models\LiveAccount;
use App\Models\TradeDeposits;
use App\Models\TradeWithdrawals;
use App\MT5\MTEnDealAction;
use App\MT5\MTProtocolConsts;
use App\MT5\MTRetCode;
use Illuminate\Http\Request;
use App\Models\TotalBalance;
use App\Models\WalletWithdraw;
use DB;
use Mail;
use App\Models\EmployeeList;
use Illuminate\Support\Facades\Session;
use App\Models\WalletDeposit;
use Illuminate\Support\Facades\Auth;
use App\MT5\MTWebAPI;
use App\Services\MT5Service;
use App\Services\MailService as MailService;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;

class MT5Controller extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MailService $mailService, MT5Service $mt5Service, MTWebAPI $api)
    {
        $this->mt5Service = $mt5Service;
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
        $this->mailService = $mailService;
        // $this->api = $api;

    }
    public function index(Request $request)
    {
        // Get the activeType and activeGroup from the request
        $activeType = $request->query('activeType');
        $activeGroup = $request->query('activeGroup');

        // Retrieve MT5 group categories of type 'type' with account type counts
        $results = DB::table('mt5_group_categories')
            ->leftJoin('account_types', 'account_types.ac_category', '=', 'mt5_group_categories.mt5_grp_cat_id')
            ->select('mt5_group_categories.*', DB::raw('SUM(IF(account_types.ac_index IS NOT NULL, 1, 0)) as count'))
            ->where('mt5_group_categories.mt5_grp_cat_type', 'type')
            ->groupBy('mt5_group_categories.mt5_grp_cat_id')
            ->orderBy('mt5_group_categories.mt5_grp_cat_id')
            ->get();

        // Retrieve MT5 group categories of type 'book'
        $grp_books = DB::table('mt5_group_categories')
            ->where('mt5_grp_cat_type', 'book')
            ->orderBy('mt5_grp_cat_id')
            ->get();

        // Retrieve all MT5 groups
        $mt5_groups = DB::table('mt5_groups')->get();

        // Retrieve account types with display priority
        $acc_priority = DB::table('account_types')
            ->whereNotNull('display_priority')
            ->get();
        // $idArray = session('userData')['user_group_id'];
        // $idArray = json_decode($idArray, true);
        // $idArray = array_map('intval', $idArray);
        $user_groups = UserGroup::
            where('status', 1)
            // ->whereIn('user_group_id',$idArray)
            ->get();

               // ✅ Proper Data Logs
    $datalogs = [
        'action'           => 'MT5 Index Page Viewed',
        'admin_id'         => session('userData.client_index') ?? null,
        'role_id'          => session('userData.role_id') ?? null,
        'active_type'      => $activeType,
        'active_group'     => $activeGroup,
        'mt5_categories'   => $results->count(),
        'grp_books_count'  => $grp_books->count(),
        'mt5_groups_count' => $mt5_groups->count(),
        'user_groups_count'=> $user_groups->count(),
        'ip_address'       => $request->ip(),
        'user_agent'       => $request->userAgent(),
        'timestamp'        => now(),
    ];
    addIpLog('View MT5 Index Page', $datalogs);
        // Return data to the view
        return view('admin.mt5.index', [
            'results' => $results,
            'grp_books' => $grp_books,
            'mt5_groups' => $mt5_groups,
            'acc_priority' => $acc_priority,
            'activeType' => $activeType,
            'activeGroup' => $activeGroup,
            'user_groups' => $user_groups
        ]);
    }
    public function updateAccountDetails(Request $request)
    {
        if ($request->has(['trade_id', 'account_type'])) {
            $trade_id = $request->input('trade_id');
            $account_type = $request->input('account_type');
            $leverage = $request->input('leverage');

            // Fetch user data from API (assume the API method and classes are available)
            // $trade_user = NULL;/
            // $this->api->UserGet($trade_id,$trade_user);
            // dd($trade_id);
            if (($error_code = $this->api->UserGet($trade_id, $trade_user)) != MTRetCode::MT_RET_OK) {
                //dd(MTRetCode::GetError($error_code));
                // return response()->json([
                //     'status' => 'warning',
                //     'message' => 'Something went wrong on Updating details',
                //     'error' => MTRetCode::GetError($error_code)
                // ], 400);
                return redirect()->back()->with('error', 'Something went wrong on Updating details' . MTRetCode::GetError($error_code));
            }
            // Fetch account type details
            $acc = DB::table('account_types')
                ->where('ac_index', $account_type)
                ->first();

            $trade_user->Group = $acc->ac_group;
            $trade_user->Leverage = $leverage;

            // Update user data via API
            $updated_user = "";
            if (($error_code = $this->api->UserUpdate($trade_user, $updated_user)) != MTRetCode::MT_RET_OK) {
                return redirect()->back()->with("error", "Something went wrong on Updating details" . MTRetCode::GetError($error_code));
            } else {
                // Update leverage and account type in the database
                DB::table('liveaccount')
                    ->where('trade_id', $trade_id)
                    ->update([
                        'leverage' => $leverage,
                        'account_type' => $account_type
                    ]);

                     $datalogs = [
            'action'      => 'MT5 Account Update',
            'trade_id'    => $trade_id,
            'admin_id'    => session('alogin'),
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'timestamp'   => now(),
        ];
        addIpLog('MT5 Account Update', $datalogs);
                return redirect()->back()->with("success", "MT5 Account Details Successfully Updated");
            }
        }
    }

    public function updatePassword(Request $request)
    {
        if ($request->has(['trade_id', 'password_type'])) {
            $login = $request->input('trade_id');
            $pass_type = $request->input('password_type');
            $new_password = $request->input('password');
            $type = $request->input('type', 'live'); // default to 'live' if 'type' is not provided
            // Change main password
            if ($pass_type == 'main') {
                if (($error_code = $this->api->UserPasswordChange($login, $new_password, MTProtocolConsts::WEB_VAL_USER_PASS_MAIN)) != MTRetCode::MT_RET_OK) {
                    return redirect()->back()->with("error", 'Something went wrong on fetching details' . MTRetCode::GetError($error_code));
                } else {
                    $table = $type == 'demo' ? 'demoaccount' : 'liveaccount';
                    DB::table($table)
                        ->where('trade_id', $login)
                        ->update(['trader_pwd' => $new_password]);
                    return redirect()->back()->with("success", 'Your Master Password Successfully Updated');
                }
            }

            // Change investor password
            if ($pass_type == 'investor') {
                if (($error_code = $this->api->UserPasswordChange($login, $new_password, MTProtocolConsts::WEB_VAL_USER_PASS_INVESTOR)) != MTRetCode::MT_RET_OK) {
                    return redirect()->back()->with("error", 'Something went wrong on fetching details' . MTRetCode::GetError($error_code));
                } else {
                    $table = $type == 'demo' ? 'demoaccount' : 'liveaccount';
                    DB::table($table)
                        ->where('trade_id', $login)
                        ->update(['invester_pwd' => $new_password]);
                           $datalogs = [
            'action'        => 'MT5 Password Update',
            'trade_id'      => $login,
            'password_type' => $pass_type,
            'status'        => 'success',
            'admin_id'      => session('alogin'),
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'timestamp'     => now(),
        ];
        addIpLog('MT5 Password Update', $datalogs);
                    return redirect()->back()->with('success', 'Your Investor Password Successfully Updated');
                }
            }
        }
    }

    public function depositToAccount(Request $request)
    {
        //  echo'<pre>';print_r($request->all());exit;

          $user = EmployeeList::where('client_index', Auth::user()->id)->first();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // Check OTP validity
    $otpValid = ($user->adminotp == $request->otp);

    if (!$otpValid) {
        return response()->json(['message' => 'Invalid OTP'], 422);
    }
    
        $eid = $request->input('email');
        $user = User::where('email', $eid)->first();
        $trade_id = $request->input('trade_id');
        if ($request->has('deposit_to_account')) {
            $amount = $request->input('amount');
            $description = $request->input('description');
            $deposit_type = 'Trade Deposit (Admin)';
            $email = $eid;
            $deposit_currency = 'USD';
            $login = $trade_id;
            $comment = 'CRM Deposited';
            $ticket = null;

            if (($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, true)) !== MTRetCode::MT_RET_OK) {
                return redirect()->back()->with('error', MTRetCode::GetError($error_code));
            } else {

            $datalogs = [
                'email' => $email,
                    'trade_id' => $trade_id,
                    'deposit_amount' => $amount,
                    'deposit_type' => $deposit_type,
                    'Status' => 1,
                    'AdminRemark' => $description,
                    'Js_Admin_Remark_Date' => date('Y-m-d H:i:s'),
                    'deposit_currency' => $deposit_currency,
                    'created_by' => session('alogin'),
                    'admin_email' => session('alogin')
            ];
                $tradeDeposit = TradeDeposits::create([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'deposit_amount' => $amount,
                    'deposit_type' => $deposit_type,
                    'Status' => 1,
                    'AdminRemark' => $description,
                    'Js_Admin_Remark_Date' => date('Y-m-d H:i:s'),
                    'deposit_currency' => $deposit_currency,
                    'created_by' => session('alogin'),
                    'admin_email' => session('alogin')
                ]);
                $transid = "TDID" . str_pad($tradeDeposit->id, 4, '0', STR_PAD_LEFT);

                // Store in total_balance table
                DB::table('total_balance')->insert([
                    'email' => $email,
                    'trading_deposited' => $amount
                ]);
                $settings = settings();
                $emailSubject = $settings['admin_title'] . ' - Fund Deposit';
                $content = '<div>We are pleased to inform you that funds have been successfully deposited into your account.</div>
          <div><b>Transaction Details</b></div>
          <div><b>Amount: </b>$' . $amount . '</div>
          <div><b>Account ID: </b>' . $trade_id . '</div>
          <div><b>Transaction ID: </b>' . $transid . '</div>
          <div><b>Deposited Date: </b>' . date("Y-m-d H:i:s") . '</div>
          <div><b>Deposit Type </b>' . $deposit_type . '</div>';
                $templateVars = [
                    'name' => $user->fullname,
                    'site_link' => settings()['copyright_site_name_text'],
                    "btn_text" => "Go To Dashboard",
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Fund",
                    "subtitle_right" => "Deposit"
                ];

                 addIpLog('Deposit To Account Update', $datalogs);
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                // return redirect()->back()->with('success', 'Trade Deposit Successful');
                //  return response()->json([
                //         'status' => 'success',
                //         'message' => 'Trade Deposit Successful'
                //     ]);
                  return response()->json(['status' => 'success', 'message' => 'Trade Deposit Successful']);
            }
        }
    }

    public function bonusToAccount(Request $request)
    {

    $users = EmployeeList::where('client_index', Auth::user()->id)->first();

    if (!$users) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // Check OTP validity
    $otpValid = ($users->adminotp == $request->otp);

    if (!$otpValid) {
        return response()->json(['message' => 'Invalid OTP'], 422);
    }
        $eid = $request->input('email');
        $user = User::where('email', $eid)->first();
        $trade_id = $request->input('trade_id');
        if ($request->has('bonus_to_account')) {

            $amount = $request->input('amount_bonus');
            $description = $request->input('descriptionbonus');
            $type = $request->input('type');
            $deposit_type = $type === 'in' ? 'Bonus In' : 'Bonus Out';
            $amount = $type === 'in' ? $amount : -1 * $amount;
            $email = $eid;
            $deposit_currency = 'USD';
            $login = $trade_id;
            $comment = $description;
            $ticket = null;
            if (($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BONUS, $amount, $comment, $ticket, true)) !== MTRetCode::MT_RET_OK) {
                return redirect()->back()->with('error', MTRetCode::GetError($error_code));
            } else {
                $datalogs = [
                     'email' => $email,
                    'trade_id' => $trade_id,
                    'bonus_amount' => $amount,
                    'bonus_type' => $deposit_type,
                    'status' => 1,
                    'admin_remark' => $description,
                    'bonus_currency' => $deposit_currency,
                    'created_by' => session('alogin')
                ];
                $deposit_details = BonusTrans::create([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'bonus_amount' => $amount,
                    'bonus_type' => $deposit_type,
                    'status' => 1,
                    'admin_remark' => $description,
                    'bonus_currency' => $deposit_currency,
                    'created_by' => session('alogin')
                ]);

                $toEmail = $email;
                $from = settings()['email_from_address'];
                $transid = "BTID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
                $emailSubject = settings()['admin_title'] . ' - Bonus Transaction';
                if ($type == "in") {
                    $content = '<div>We are pleased to inform your that Bonus have been successfully deposited into your account.</div>';
                } else {
                    $content = '<div>This email to inform you, that Bonus credited out from your account.</div>';
                }

                $content .= '<div><b>Transaction Details</b></div>
          <div><b>Amount: </b>$' . $deposit_details->bonus_amount . '</div>
          <div><b>Account ID: </b>' . $deposit_details->trade_id . '</div>
          <div><b>Transaction ID: </b>' . $transid . '</div>
          <div><b>Bonus Date: </b>' . date("Y-m-d H:i:s") . '</div>';

                $templateVars = [
                    'name' => $user->fullname,
                    'site_link' => settings()['copyright_site_name_text'],
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Bonus",
                    "subtitle_right" => "Credit Out",
                    "btn_text" => "Go To Dashboard",
                ];
                 addIpLog('Bonus To Account ', $datalogs);
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);

                           return response()->json([
                            'status' => 'success',
                            'message' => 'Bonus ' . ($type === 'in' ? 'Credited' : 'Debited') . ' Successfully'
                        ]);

                //  return redirect()->back()->with('success', 'Bonus ' . ($type === 'in' ? 'Credited' : 'Debited') . ' Successfully');
            }
        }
    }

    public function withdrawFromAccount(Request $request)
    {
        //  echo'<pre>';print_r($request->all());exit;

          $users = EmployeeList::where('client_index', Auth::user()->id)->first();

    if (!$users) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // Check OTP validity
    $otpValid = ($users->adminotp == $request->otp);

    if (!$otpValid) {
        return response()->json(['message' => 'Invalid OTP'], 422);
    }
    
        $eid = $request->input('email');
        $user = User::where('email', $eid)->first();

        //  echo'<pre>';print_r($user);exit;
        $trade_id = $request->input('trade_id');
        if ($request->has('withdraw_from_account')) {
            $amount = $request->input('amount_withdrawal');
            $tw_amount = abs($request->input('amount_withdrawal')) * -1;
            $description = $request->input('descriptionwithdrawal');
            $withdraw_type = 'Trade Withdrawal (Admin)';
            $email = $eid;
            $login = $trade_id;
            $comment = 'CRM Withdrawal';
            $ticket = null;
            if (($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BALANCE, $tw_amount, $comment, $ticket, true)) !== MTRetCode::MT_RET_OK) {
                return redirect()->back()->with("error", MTRetCode::GetError($error_code));
            } else {
                $datalogs = [
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'withdrawal_amount' => $amount,
                    'withdraw_type' => $withdraw_type,
                    'AdminRemark' => $description,
                    'Js_Admin_Remark_Date' => date('Y-m-d H:i:s'),
                    'created_by' => session('alogin'),
                    'Status' => 1,
                    'admin_email' => session('alogin')
                ];
                $deposit_details = TradeWithdrawals::create([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'withdrawal_amount' => $amount,
                    'withdraw_type' => $withdraw_type,
                    'AdminRemark' => $description,
                    'Js_Admin_Remark_Date' => date('Y-m-d H:i:s'),
                    'created_by' => session('alogin'),
                    'Status' => 1,
                    'admin_email' => session('alogin')
                ]);

                // Update total_balance table
                // DB::table('total_balance')->insert([
                //     'email' => $email,
                //     'withdrawal_amount' => $amount
                // ]);

                // Send Email
                $transid = "TWID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
                $settings = settings();
                $emailSubject = $settings['admin_title'] . ' - Fund Withdrawal';
                $content = '<div>We are pleased to inform you that funds have been successfully withdrawn from your account.</div>
                <div><b>Withdrawal Details</b></div>
                <div><b>Amount: </b>$' . $deposit_details->withdrawal_amount . '</div>
                <div><b>Account ID: </b>' . $deposit_details->trade_id . '</div>
                <div><b>Transaction ID: </b>' . $transid . '</div>
                <div><b>Withdraw Date: </b>' . date("Y-m-d H:i:s") . '</div>
                <div><b>Withdraw Type </b>' . $deposit_details->withdraw_type . '</div>';
                $templateVars = [
                    'name' => $user->fullname,
                    'site_link' => settings()['copyright_site_name_text'],
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Fund",
                    "subtitle_right" => "Withdrawal",
                    "btn_text" => "Go To Dashboard",
                ];
                addIpLog(' Withdraw From Account ', $datalogs);
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                // return redirect()->back()->with("success", "Withdrawal Successful");

                  return response()->json(['status' => 'success', 'message' => 'Withdrawal Successful']);
            }
        }
    }

    private function sendEmail($toEmail, $subject, $content, $transaction)
    {
        $transid = strtoupper(substr($subject, 0, 2)) . 'ID' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
       
       $datalogs = [
         'name' => $transaction->user->fullname ?? 'User',
            'site_link' => env('APP_URL'),
            'email' => env('MAIL_FROM_ADDRESS'),
            'content' => $content,
            'transid' => $transid,
            'amount' => $transaction->amount,
            'trade_id' => $transaction->trade_id,
            'date' => $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i:s') : date("Y-m-d H:i:s"),
       ];
        $templateVars = [
            'name' => $transaction->user->fullname ?? 'User',
            'site_link' => env('APP_URL'),
            'email' => env('MAIL_FROM_ADDRESS'),
            'content' => $content,
            'transid' => $transid,
            'amount' => $transaction->amount,
            'trade_id' => $transaction->trade_id,
            'date' => $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i:s') : date("Y-m-d H:i:s"),
        ];
 addIpLog('Mt5 sendEmail', $datalogs);
        Mail::send('emails.transaction', $templateVars, function ($message) use ($toEmail, $subject) {
            $message->to($toEmail)
                ->subject($subject)
                ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }


    public function view(Request $request)
    {
        $trade_id = $request->query('id');
        AccountHelper::updateLiveAndDemoAccounts($trade_id);
        $type = "live";

        $sql = "select liveaccount.*,aspnetusers.fullname,aspnetusers.group_id,account_types.ac_group from liveaccount
left join account_types on account_types.ac_index = liveaccount.account_type
left join aspnetusers on aspnetusers.email = liveaccount.email where md5(liveaccount.trade_id)='" . $trade_id . "'";
        $query = DB::select($sql);
        $getUser = isset($query[0]) ? $query[0] : [];
        if (!$getUser) {
            alert()->error("The MT5 account does not exist or has been deleted. Please try again.");
            return redirect("/admin/dashboard");
        }

        // Total approved deposits
        $total_deposit = DB::table('trade_deposit')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', 1)
            ->sum('deposit_amount');

        // Total unapproved deposits
        $unapproved_deposit = DB::table('trade_deposit')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', '!=', 1)
            ->sum('deposit_amount');

        // Total approved withdrawals
        $total_withdrawal = DB::table('trade_withdrawal')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', 1)
            ->sum('withdrawal_amount');

        // Total unapproved withdrawals
        $unapproved_withdrawal = DB::table('trade_withdrawal')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', '!=', 1)
            ->sum('withdrawal_amount');

        // $bonus_trans = BonusTrans::where('status', 1)
        //     ->where(DB::raw('MD5(trade_id)'), $trade_id)
        //     ->get();
        $bonus_trans = [];
        $account_types = DB::table('account_types')
            ->join('mt5_groups', 'mt5_groups.mt5_group_id', '=', 'account_types.ac_type')
            ->where('mt5_groups.is_active', 1)
            ->where('status', 1)
            ->where('account_types.user_group_id', $getUser->group_id)
            ->get();
        $account = AccountHelper::getAccount($trade_id);
        return view("admin.mt5.view", [
            "id" => $trade_id,
            "getUser" => $getUser,
            "account" => $account,
            'total_deposit' => $total_deposit,
            'unapprove_deposit' => $unapproved_deposit,
            'total_withdrawl' => $total_withdrawal,
            'unapprove_withdrawl' => $unapproved_withdrawal,
            'bonus_trans' => $bonus_trans,
            'account_types' => $account_types,
            'type' => $type,
            'title' => 'MT5 Account Details'
        ]);
    }
public function admingetOtp(Request $request)
{
    //   echo'<pre>';print_r($request->all());exit;
    $settings = settings();
    $id = Auth::user()->id;

    $user = EmployeeList::where('client_index', $id)->first();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Save OTP in database column 'adminotp'
    $user->adminotp = $otp;
    // $user->otp_generated_at = now(); // optional: track timestamp
    $user->save();

    $email = $user->email;
    $fullname = ucfirst($user->fullname);

    $emailSubject = $settings['admin_title'] . ' - OTP Verification';
    $content = "<p>Your OTP for Admin Wallet Deposit is <b>$otp</b></p>
                <p>If you did not request this, please reset your password or contact support.</p>";
$datalogs = [
    'name' => $user->username,
        'email' => $settings['email_from_address'],
        "content" => $content,
        "title_right" => "",
        "subtitle_right" => "",
        "img_hidden" => true,
];
    $templateVars = [
        'name' => $user->username,
        'email' => $settings['email_from_address'],
        "content" => $content,
        "title_right" => "",
        "subtitle_right" => "",
        "img_hidden" => true,
    ];
addIpLog('admin get Otp', $datalogs);
     $mailSent = $this->mailService->sendEmail($email, $emailSubject, '', 'emails.otp', $templateVars);

    if ($mailSent) {
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false, 'message' => 'Please try again later or contact support.']);
    }
}
public function updatewalletwithdrawal(Request $request)
    {
    //    echo'<pre>';print_r($request->all());exit;
        $settings = settings();
        // $validatedData = $request->validate([
        //     'description' => 'required|string|max:255',
        //     'status' => 'required|integer',
        //     'email' => 'required|email',
        //     'amount' => 'required|numeric',
        // ]);
        $user = EmployeeList::where('client_index', Auth::user()->id)->first();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // Check OTP validity
    $otpValid = ($user->adminotp == $request->otp);
    if (!$otpValid) {
        return response()->json(['message' => 'Invalid OTP'], 422);
    }
        $description = $request->descriptionwithdraw;
        $status = 1;
        $email = $request->email;
        $depositAmount = $request->amountwithdraw;

        $datalogs = [
            'email' => $email,
                    'withdraw_amount' => $depositAmount,
                    'AdminRemark' => $description,
                    'admin_email' => Auth::user()->email, // logged in admin
                    'withdraw_type' => 'Wallet Withdrawal (Admin)',
                    'withdraw_date' => now(),
                    'Status' => 1,
        ];
                $transaction = WalletWithdraw::create([
                    'email' => $email,
                    'withdraw_amount' => $depositAmount,
                    'AdminRemark' => $description,
                    'admin_email' => Auth::user()->email, // logged in admin
                    'withdraw_type' => 'Wallet Withdrawal (Admin)',
                    'withdraw_date' => now(),
                    'Status' => 1,
                ]);
       
       
                TotalBalance::create([
                    'email' => $email,
                    'withdraw_amount' => $depositAmount,
                ]);
                $deposit_details = WalletWithdraw::with('user')->find($transaction->id);
                $from = $settings['email_from_address'];
                $transid = "WWID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
                $emailSubject = $settings['admin_title'] . ' - Wallet Withdrawal';
                $content = '<div>We are pleased to inform you that your withdrawal has been successfully approved.</div>
                            <div><b>Transaction Details</b></div>
                            <div><b>Approved Amount: </b>$' . $deposit_details->withdraw_amount . '</div>
                            <div><b>Transaction ID: </b>' . $transid . '</div>
                            <div><b>Withdrawal Date: </b>' . $deposit_details->withdraw_date . '</div>
                            <div><b>Withdrawal Type: </b>' . $deposit_details->withdraw_type . '</div>';
                $templateVars = [
                    'name' => $deposit_details->user->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    'email' => $settings['email_from_address'],
                    'content' => $content,
                    'title_right' => 'Wallet',
                    'subtitle_right' => 'Withdrawal',
                    'btn_text' => 'Go To Dashboard',
                ];
                addIpLog('update wallet withdrawal', $datalogs);
                $this->mailService->sendEmail($email, $emailSubject, $headers, '', $templateVars);
                return response()->json(['status' => 'success', 'message' => 'Withdrawal Approved Successfully']);
             
           
       
    }
public function verifyAdminOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:6'
    ]);

    $user = EmployeeList::where('client_index', Auth::user()->id)->first();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // Check OTP validity
    $otpValid = ($user->adminotp == $request->otp);

    // Optional: check OTP expiration (5-10 min)
    // $otpTimeout = now()->diffInMinutes($user->otp_generated_at) > 10;

    if (!$otpValid) {
        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }

    // if ($otpTimeout) {
    //     return response()->json(['success' => false, 'message' => 'OTP expired. Please request a new one.']);
    // }

    // Clear OTP after verification
    $user->adminotp = null;
    // $user->otp_generated_at = null;
    $user->save();

    return response()->json(['success' => true, 'message' => 'OTP verified successfully']);
}
public function adminwalletupdate(Request $request)
{
    $settings = settings();

    // Validate required fields
    // $validatedData = $request->validate([
    //     'description' => 'required|string|max:255',
    //     'email' => 'required|email',
    //     'amount' => 'required|numeric',
    //     'otp' => 'required|digits:6',
    // ]);
     $user = EmployeeList::where('client_index', Auth::user()->id)->first();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    // Check OTP validity
    $otpValid = ($user->adminotp == $request->otp);
    if (!$otpValid) {
        return response()->json(['message' => 'Invalid OTP'], 422);
    }

    // OTP is correct → process wallet deposit
    $depositAmount = $request->amount;
    $email = $request->email;
    $description = $request->description;

    $datalog = [
        'email' => $email,
        'deposit_amount' => $depositAmount,
        'AdminRemark' => $description,
        'admin_email' => Auth::user()->email, // logged in admin
        'deposit_type' => 'Wallet Deposit (Admin)',
        'deposted_date' => now(),
        'Status' => 1,
    ];

    $transaction = WalletDeposit::create([
        'email' => $email,
        'deposit_amount' => $depositAmount,
        'AdminRemark' => $description,
        'admin_email' => Auth::user()->email, // logged in admin
        'deposit_type' => 'Wallet Deposit (Admin)',
        'deposted_date' => now(),
        'Status' => 1,
    ]);
    

    TotalBalance::create([
        'email' => $email,
        'withdraw_amount' => $depositAmount,
    ]);
   addIpLog('Admin Wallet Deposit', $datalog);
    // Send email to user
    $deposit_details = WalletDeposit::with('user')->find($transaction->id);
    $transid = "WDID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);

    $emailSubject = $settings['admin_title'] . ' - Wallet Deposit';
    $content = "<div>We are pleased to inform you that your deposit has been successfully approved.</div>
                <div><b>Approved Amount: </b>$" . $deposit_details->deposit_amount . "</div>
                <div><b>Transaction ID: </b>$transid</div>
                <div><b>Deposit Date: </b>" . $deposit_details->deposted_date . "</div>
                <div><b>Deposit Type: </b>" . $deposit_details->deposit_type . "</div>";

    $templateVars = [
        'name' => $deposit_details->user->fullname,
        'site_link' => $settings['copyright_site_name_text'],
        'email' => $settings['email_from_address'],
        'content' => $content,
        'title_right' => 'Wallet',
        'subtitle_right' => 'Deposit',
        'btn_text' => 'Go To Dashboard',
    ];

    $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);

    

    return response()->json(['status' => 'success', 'message' => 'Wallet Deposit Successfully']);
}

//     public function adminwalletupdate(Request $request){

// // echo'<pre>';print_r($request->all());exit;
//       $settings = settings();
//         $validatedData = $request->validate([
//             'description' => 'required|string|max:255',
//             'email' => 'required|email',
//             'amount' => 'required|numeric',
//         ]);
//         $description = $validatedData['description'];
       
//         $email = $validatedData['email'];
//         $depositAmount = $validatedData['amount'];
//         $did = $request->input('id');
//        $transaction = WalletDeposit::create([
//                 'email' => $email,
//                 'deposit_amount' => $depositAmount,
//                 'AdminRemark' => $description,
//                 'admin_email' => session('alogin'),
//                 'deposit_type' => 'Admin Wallet Deposit', // or 'Bonus', etc
//                 'deposted_date' => now(),
//                 'Status' => 1,
//             ]);
//                 TotalBalance::create([
//                     'email' => $email,
//                     'withdraw_amount' => $depositAmount,
//                 ]);
                
//              $deposit_details = WalletDeposit::with('user')->find($transaction->id);
//             //   echo'<pre>';print_r($deposit_details);exit;
//                 $from = $settings['email_from_address'];
//                 $transid = "WDID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
//                 $headers = "MIME-Version: 1.0" . "\r\n";
//                 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//                 $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
//                 $emailSubject = $settings['admin_title'] . ' - Wallet Deposit';
//                 $content = '<div>We are pleased to inform you that your deposit has been successfully approved.</div>
//                             <div><b>Transaction Details</b></div>
//                             <div><b>Approved Amount: </b>$' . $deposit_details->deposit_amount . '</div>
//                             <div><b>Transaction ID: </b>' . $transid . '</div>
//                             <div><b>Deposit Date: </b>' . $deposit_details->deposted_date . '</div>
//                             <div><b>Deposit Type: </b>' . $deposit_details->deposit_type . '</div>';
//                 $templateVars = [
//                     'name' => $deposit_details->user->fullname,
//                     'site_link' => $settings['copyright_site_name_text'],
//                     'email' => $settings['email_from_address'],
//                     'content' => $content,
//                     'title_right' => 'Wallet',
//                     'subtitle_right' => 'Deposit',
//                     'btn_text' => 'Go To Dashboard',
//                 ];
//                  $this->mailService->sendEmail($email, $emailSubject, $headers, '', $templateVars);
//                  return response()->json(['status' => 'success', 'message' => 'Wallet Deposit Successfully']);
          
// }
    function updateTransaction(Request $request)
    {


        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $settings = settings();
        $data = $request->all();		
        $description = $data['description'];
        $status = $data['status'];
        $did = $data['did'];
        $email = $data['email'];
        $amount = $data['amount'];
        $login = $data['tradeId'];
        $bonus_amount = $data['bonus_amount'] ?? 0;
        $comment = "Deposit";

        if ($status == 1) {
            $error = null;
            $ticket = null;
            if (($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);				
            } elseif ($bonus_amount > 0 && ($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BONUS, $bonus_amount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);
            } else {
				
                DB::table('trade_deposit')
                    ->whereRaw('md5(id) = ?', [$did])
                    ->update(['AdminRemark' => $description, 'Status' => $status, 'admin_email' => session('alogin')]);
                DB::table('total_balance')->insert([
                    'email' => $email,
                    'trading_deposited' => $amount,
                ]);
                $depositDetails = DB::table('trade_deposit as td')
                    ->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
                    ->select('td.id', 'ap.fullname', 'td.email', 'td.trade_id', 'td.deposit_amount as amount', 'td.bonus_amount', 'td.bonus_trans_id', 'td.deposted_date as date', 'td.deposit_type as type')
                    ->whereRaw('md5(td.id) = ?', [$did])
                    ->first();
                if ($depositDetails->bonus_trans_id) {
                    DB::table('bonus_trans')
                        ->where('id', $depositDetails->bonus_trans_id)
                        ->update(['status' => 1]);
                }

                $emailSubject = $settings['admin_title'] . ' - Transaction Approved';
                $transid = "TDID" . str_pad($depositDetails->id, 4, '0', STR_PAD_LEFT);
                $content = '<div>We are pleased to inform you that your transaction has been successfully approvedss. </div>
        <div>The approved amount has been deposited into your account.</div>
        <div><b>Transaction Details</b></div>
        <div><b>Approved Amount: </b>$' . $amount . '</div>';
                if ($bonus_amount > 0) {
                    $content .= '<div><b>Bonus Amount: </b>$' . $bonus_amount . '</div>';
                }
                $content .= '<div><b>Account ID: </b>' . $login . '</div>
        <div><b>Transaction ID: </b>' . $transid . '</div>
        <div><b>Deposited Date: </b>' . $depositDetails->date . '</div>
        <div><b>Deposit Type </b>' . $depositDetails->type . '</div>';
        $datalog = [
            'name' => $depositDetails->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    "btn_text" => "Go To Dashboard",
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Transaction",
                    "subtitle_right" => "Approved"
        ];
                $templateVars = [
                    'name' => $depositDetails->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    "btn_text" => "Go To Dashboard",
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Transaction",
                    "subtitle_right" => "Approved"
                ];
                  addIpLog('update Transaction', $datalog);
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                return redirect()->back()->with('success', 'Transaction Approved Successfully');
            }
        } else {
            DB::table('trade_deposit')
                ->whereRaw('md5(id) = ?', [$did])
                ->update(['AdminRemark' => $description, 'Status' => $status, 'admin_email' => session('alogin')]);
            $depositDetails = DB::table('trade_deposit as td')
                ->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
                ->select('td.id', 'ap.fullname', 'td.email', 'td.trade_id', 'td.deposit_amount as amount', 'td.deposted_date as date', 'td.deposit_type as type')
                ->whereRaw('md5(td.id) = ?', [$did])
                ->first();
            $transid = "TDID" . str_pad($depositDetails->id, 4, '0', STR_PAD_LEFT);
            $emailSubject = $settings['admin_title'] . ' - Transaction Rejected';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $content = '<div>This email to inform you that your transaction has been Rejected. </div>
                    <div><b>Transaction Details</b></div>
                    <div><b>Rejected Amount: </b>$' . $depositDetails->amount . '</div>
                    <div><b>Account ID: </b>' . $depositDetails->trade_id . '</div>
                    <div><b>Transaction ID: </b>' . $transid . '</div>
                    <div><b>Date: </b>' . $depositDetails->date . '</div>
                    <div><b>Deposit Type </b>' . $depositDetails->type . '</div>';
                    $datalog = [
                            'name' => $depositDetails->fullname,
                'site_link' => $settings['copyright_site_name_text'],
                'email' => $settings['email_from_address'],
                "content" => $content,
                "title_right" => "Transaction",
                "subtitle_right" => "Rejected",
                "btn_text" => "Go To Dashboard",
                    ];
            $templateVars = [
                'name' => $depositDetails->fullname,
                'site_link' => $settings['copyright_site_name_text'],
                'email' => $settings['email_from_address'],
                "content" => $content,
                "title_right" => "Transaction",
                "subtitle_right" => "Rejected",
                "btn_text" => "Go To Dashboard",
            ];
             addIpLog('trade deposit', $datalog);
            $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
            return redirect()->back()->with('success', 'Transaction Rejected');
        }
    }
    function updateWithdrawal(Request $request)
    {
        $settings = settings();
        $data = request()->all();
        $description = $data['description'];
        $status = $data['status'];
        $did = $data['did'];
        $email = $data['email'];
        $agent_amount = $data['amount'];
        $amount = ((float) $data['amount']) * -1;
        $login = $data['tradeId'];
        $comment = "Withdrawal";

        $trade = DB::table('trade_withdrawal as td')
            ->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
            ->leftJoin('liveaccount as la', 'td.agent_account', '=', 'la.trade_id')
            ->select(
                'td.id',
                'ap.fullname',
                'td.email',
                'td.trade_id',
                'td.withdrawal_amount as amount',
                'td.withdraw_date as date',
                'td.withdraw_type as type',
                'td.agent_account',
                'la.email as agent_email',
                'la.name as agent_name'
            )
            ->whereRaw('md5(td.id) = ?', [$did])
            ->first();

        if ($status == 1) {
            if (($error_code = $this->api->TradeBalance($login, $type = MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, $margin_check = true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);
                return redirect()->back()->with('error', $error);
            }
            DB::table('trade_withdrawal')
                ->whereRaw('md5(id) = ?', [$did])
                ->update(['AdminRemark' => $description, 'Status' => $status, 'admin_email' => session('alogin')]);
            if ($trade->type == 'Agent Withdrawal') {
                $errorCode = $this->api->TradeBalance($trade->agent_account, $type = MTEnDealAction::DEAL_BALANCE, $agent_amount, 'deposit', $ticket, true);
                if ($errorCode != MTRetCode::MT_RET_OK) {
                    $error = MTRetCode::GetError($errorCode);
                    return redirect()->back()->with('error', 'Deposit Failed.');
                } else {
                    $agent_deposit = TradeDeposits::create([
                        'email' => $trade->agent_email,
                        'trade_id' => $trade->agent_account,
                        'deposit_amount' => $agent_amount,
                        'deposit_type' => 'Internal Transfer',
                        'deposit_from' => $trade->trade_id,
                        'status' => 1,
                        'AdminRemark' => "Agent Withdrawal - TWID" . str_pad($trade->id, 4, '0', STR_PAD_LEFT)
                    ]);

                    $from = $settings['email_from_address'];
                    $transid = "TDID" . str_pad($agent_deposit->id, 4, '0', STR_PAD_LEFT);
                    $emailSubject = $settings['admin_title'] . ' - Fund Deposit';
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";

                    $content = '<div>We are pleased to inform you that funds have been successfully deposited into your account.</div>
        <div><b>Transaction Details</b></div>
        <div><b>Amount: </b>$' . $agent_amount . '</div>
        <div><b>Account ID: </b>' . $trade->agent_account . '</div>
        <div><b>From Account: </b>' . $trade->trade_id . '</div>
        <div><b>Transaction ID: </b>' . $transid . '</div>
        <div><b>Deposited Date: </b>' . date("Y-m-d H:i:s") . '</div>
        <div><b>Deposit Type </b>' . $trade->type . '</div>';

                    $templateVars = [
                        'name' => $trade->agent_name,
                        'site_link' => $settings['copyright_site_name_text'],
                        'email' => $settings['email_from_address'],
                        "content" => $content,
                        "title_right" => "Fund",
                        "subtitle_right" => "Deposit",
                        "btn_text" => "Go To Dashboard",
                    ];
                    $this->mailService->sendEmail($trade->agent_email, $emailSubject, '', '', $templateVars);

                }
            }
            $from = $settings['email_from_address'];
            $transid = "TWID" . str_pad($trade->id, 4, '0', STR_PAD_LEFT);
            $emailSubject = $settings['admin_title'] . ' - Withdrawal Approved';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
            $content = '<div>We are pleased to inform you that your withdrawal has been successfully approveds. </div>
        <div><b>Transaction Details</b></div>
        <div><b>Approved Amount: </b>$' . $trade->amount . '</div>
        <div><b>Account ID: </b>' . $trade->trade_id . '</div>';
            if ($trade->type == 'Agent Withdrawal') {
                $content .= '<div><b>To Account: </b>' . $trade->agent_account . '</div>';
            }
            $content .= '<div><b>Transaction ID: </b>' . $transid . '</div>
        <div><b>Withdraw Date: </b>' . $trade->date . '</div>
        <div><b>Withdraw Type </b>' . $trade->type . '</div>';
            $templateVars = [
                'name' => $trade->fullname,
                'site_link' => $settings['copyright_site_name_text'],
                'email' => $settings['email_from_address'],
                "content" => $content,
                "title_right" => "Withdrawal",
                "subtitle_right" => "Approved",
                "btn_text" => "Go To Dashboard",
            ];
            $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
            return redirect()->back()->with('success', 'Withdrawal Approved Successfully');
        } else {
            DB::table('trade_withdrawal')
                ->whereRaw('md5(id) = ?', [$did])
                ->update(['AdminRemark' => $description, 'Status' => $status, 'admin_email' => session('alogin')]);
            $from = $settings['email_from_address'];
            $transid = "TWID" . str_pad($trade->id, 4, '0', STR_PAD_LEFT);
            $emailSubject = $settings['admin_title'] . ' - Withdrawal Rejected';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
            $content = '<div>This email to inform you that your transaction has been Rejected. </div>
        <div><b>Transaction Details</b></div>
        <div><b>Rejected Amount: </b>$' . $trade->amount . '</div>
        <div><b>Account ID: </b>' . $trade->trade_id . '</div>
        <div><b>Transaction ID: </b>' . $transid . '</div>
        <div><b>Withdraw Date: </b>' . $trade->date . '</div>
        <div><b>Withdraw Type </b>' . $trade->type . '</div>';
            $templateVars = [
                'name' => $trade->fullname,
                'site_link' => $settings['copyright_site_name_text'],
                'email' => $settings['email_from_address'],
                "content" => $content,
                "title_right" => "Withdrawal",
                "subtitle_right" => "Rejected",
                "btn_text" => "Go To Dashboard",
            ];
            $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
            return redirect()->back()->with('success', 'Withdrawal Rejected Successfully');
        }
    }

    public function live_acc_excluded()
    {
        $requestData = $_GET;
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and date(liveaccount.Registered_Date) >= '" . $requestData['startdate'] . "' AND date(liveaccount.Registered_Date) <= '" . $requestData['enddate'] . "'  ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and date(liveaccount.Registered_Date) <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and date(liveaccount.Registered_Date) >= '" . $requestData['startdate'] . "' ";
            }
        }

        $roleId = session('userData')['role_id'];
        $alogin = session('alogin');
        $userGroups = explode(',', session('user_groups'));
        // Select the fields and execute the query
        $rmCondition = app('permission')->appendRolePermissionsQry('liveaccount', 'email') . " (1=1)";
        $query = "select liveaccount.*,md5(aspnetusers.id) as enc_id,account_types.ac_group,sum(ib1_commission.volume) as total_lots from liveaccount
        left join ib1_commission on ib1_commission.login = liveaccount.trade_id
        left join aspnetusers on aspnetusers.email = liveaccount.email
join account_types on account_types.ac_index = liveaccount.account_type " . $rmCondition . $dateCondition . " and liveaccount.is_excluded = 1 group by liveaccount.trade_id order by id desc;
";
        $accounts = DB::select($query);
        // dd($accounts);
        return view("admin.client_accounts.excluded_live_accounts", compact("accounts"));
    }

    public function live_acc_excluded_store(Request $request)
    {
        // dd($request->all());
        if (!$request->trade_id) {
            alert()->warning("Trade ID not Specified", "Invalid Request. Please try again");
            return redirect()->back();
        }
        if ($request->addUser) {
            $live_acc = LiveAccount::where("trade_id", $request->trade_id)->first();
        } else {
            $live_acc = LiveAccount::where(DB::raw("md5(trade_id)"), $request->trade_id)->first();
        }
        if ($live_acc) {
            // dd();
            if ($request->addUser) {
                if ($live_acc->is_excluded == 1) {
                    alert()->info("Trade ID Already Excluded");
                    return redirect()->back();
                } else {
                    $live_acc->is_excluded = 1;
                    $live_acc->excluded_by = session("alogin");
                    $live_acc->excluded_at = Carbon::now();
                    $live_acc->save();
                    alert()->success("Trade ID Successfully Excluded");
                    return redirect()->back();
                }
            } else {
                if ($live_acc->is_excluded == 0) {
                    return response()->json(["status" => true, "message" => "Trade ID Not In Excluded"]);
                } else {
                    $live_acc->is_excluded = 0;
                    $live_acc->excluded_by = session("alogin");
                    // $live_acc->excluded_at = Carbon::now();
                    $live_acc->save();
                    return response()->json(["status" => true, "message" => "Trade ID Successfully Excluded"]);
                }
            }
        } else {
            alert()->warning("Trade ID not Specified", "Invalid TradeID. Please try again or contact support");
            return redirect()->back();
        }
    }
    public function mapMt5Account(Request $request)
    {
        $emailHash = $request->input('client_id');
        $accType = $request->input('acc_type');
        $leverage = $request->input('leverage');
        $login = $request->input('trade_id');
        $user = DB::table('aspnetusers')->whereRaw('MD5(email) = ?', [$emailHash])->first();
        if (!$user) {
            return response()->json(["status" => "warning", "message" => "User not found"]);
        }
        $name = $user->fullname;
        $email = $user->email;
        $errorCode = $this->api->UserAccountGet($login, $account);
        if ($errorCode != MTRetCode::MT_RET_OK) {
            return response()->json(["status" => "error", "message" => 'Something went wrong on fetching details: ' . MTRetCode::GetError($errorCode)]);
        }
        $recordExists = DB::table('liveaccount')->where('trade_id', $login)->exists();
        if ($recordExists) {
            return response()->json(["status" => "info", "message" => 'Account already exists and mapped']);
        }

        $datalog = [
             'email' => $email,
            'name' => $name,
            'trade_id' => $login,
            'account_type' => $accType,
            'leverage' => $leverage,
            'ib1' => $user->ib1,
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
            'email' => $email,
            'name' => $name,
            'trade_id' => $login,
            'account_type' => $accType,
            'leverage' => $leverage,
            'ib1' => $user->ib1,
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

         addIpLog('map Mt5 Account', $datalog);
        return response()->json(["status" => "success", "message" => 'Account mapped successfully', "redirect_url" => 'view_account_details?id=' . md5($login)]);
    }
    public function userAccountGet(Request $request)
    {
        $login = $request->input('trade_id');
        if (($error_code = $this->api->UserGet($login, $trade_user)) != MTRetCode::MT_RET_OK) {
            return "";
        } else {
            return response()->json(["data" => $trade_user]);
        }
    }
	
	/*MT5 Groups Delete functionality*/	
	public function maingroups($id){	
		$maingroup = DB::table('mt5_groups')
			->where('mt5_group_id', $id)
			->first();

		if (!$maingroup) {
			return response()->json([
				'status' => 'error',
				'message' => 'Main Group not found.'
			]);
		}		
		$exists = DB::table('account_types')
			->where('ac_type', $maingroup->mt5_group_id)
			->exists();

		if ($exists) {
			return response()->json([
				'status' => 'error',
				'message' => 'This group is linked with account groups. Cannot delete.'
			]);
		}
		DB::table('mt5_groups')->where('mt5_group_id', $id)->delete();
		//DB::table('mt5_groups')->where('mt5_group_id', $id)->update(['is_active' => 4]);

		return response()->json([
			'status' => 'success',
			'message' => 'Main Group deleted successfully.'
		]);
	}
	
	public function maincategory($id){	
		$maincategory = DB::table('mt5_group_categories')
			->where('mt5_grp_cat_id', $id)
			->first();

		if (!$maincategory) {
			return response()->json([
				'status' => 'error',
				'message' => 'Main Category not found.'
			]);
		}		
		$exists = DB::table('account_types')
			->where('ac_category', $maincategory->mt5_grp_cat_id)
			->exists();

		if ($exists) {
			return response()->json([
				'status' => 'error',
				'message' => 'This category is linked with account groups. Cannot delete.'
			]);
		}
		DB::table('mt5_group_categories')->where('mt5_grp_cat_id', $id)->delete();
		//DB::table('mt5_group_categories')->where('mt5_grp_cat_id', $id)->update(['is_active' => 4]);

		return response()->json([
			'status' => 'success',
			'message' => 'Main Category deleted successfully.'
		]);
	}
	
	public function maintype($id){	
		$maintype = DB::table('mt5_group_categories')
			->where('mt5_grp_cat_id', $id)
			->first();

		if (!$maintype) {
			return response()->json([
				'status' => 'error',
				'message' => 'Main Type not found.'
			]);
		}		
		$exists = DB::table('account_types')
			->where('ac_book_type', $maintype->mt5_grp_cat_id)
			->exists();

		if ($exists) {
			return response()->json([
				'status' => 'error',
				'message' => 'This type is linked with account groups. Cannot delete.'
			]);
		}
		DB::table('mt5_group_categories')->where('mt5_grp_cat_id', $id)->delete();
		//DB::table('mt5_group_categories')->where('mt5_grp_cat_id', $id)->update(['is_active' => 4]);
    $datalogs = [
        'action'      => 'Maintype Group Delete',
        'group_id_md5'=> $id,
        'status'      => 'success',
       
        'admin_id'    => session('alogin'),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('MT5 Maintype', $datalogs);
		return response()->json([
			'status' => 'success',
			'message' => 'Main Type deleted successfully.'
		]);
	}
	
	public function groupdelete($id){	
		$checkgroup = DB::table('account_types')->whereRaw("MD5(ac_index) = ?", [$id])->first();
		if (!$checkgroup) {
			return response()->json([
				'status' => 'error',
				'message' => 'Account Group not found.'
			]);
		}	
		if($checkgroup->ac_type == 1){
			/*Check Live account*/
			$exists = DB::table('liveaccount')
			->where('account_type', $checkgroup->ac_index)
			->exists();
	    } else {
			/*Check Demo account*/
			$exists = DB::table('demoaccount')
			->where('account_type', $checkgroup->ac_group)
			->exists();
		}

		if ($exists) {
			return response()->json([
				'status' => 'error',
				'message' => 'This account groups is linked with live/demo accounts. Cannot delete. Kindly delete that accounts!'
			]);
		}
		DB::table('account_types')->whereRaw("MD5(ac_index) = ?", [$id])->delete();
		//DB::table('account_types')->where('ac_index', $id)->update(['is_active' => 4]);
 $datalogs = [
        'action'      => 'MT5 Account Group Delete',
        'group_id_md5'=> $id,
        'status'      => 'success',
        
        'admin_id'    => session('alogin'),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('MT5 Account Group Delete', $datalogs);
		return response()->json([
			'status' => 'success',
			'message' => 'Account Group deleted successfully.'
		]);
	}
	
	public function userAccountdelete($id){		
		$account = DB::table('liveaccount')->where('trade_id', $id)->first();
		if (!$account) {
			return response()->json([
				'status' => 'error',
				'message' => $id . ' Account Not Found!!'
			]);
		}		
		if (($error_code = $this->api->UserDelete($id)) != MTRetCode::MT_RET_OK) {
			return redirect()->back()->with("error", "Something went wrong on deleted the account " . MTRetCode::GetError($error_code));
		} 
		DB::beginTransaction();
		try {

			// Soft delete liveaccount
			DB::table('liveaccount')
				->where('trade_id', $id)
				->update([
					'status'       => 'deleted',
					'deleteRemark' => 'Admin has deleted this live account.',
					'deletedby'    => session('alogin'),
					'deleted_at'   => now()
				]);

			// Reduce account limit
			/*$user = DB::table('aspnetusers')
				->where('email', $account->email)
				->first();

			if ($user && $user->acc_limit > 0) {
				DB::table('aspnetusers')
					->where('email', $account->email)
					->update([
						'acc_limit' => $user->acc_limit - 1
					]);
			}*/
$datalogs = [
        'action'      => 'user Account delete',
        'group_id_md5'=> $id,
        'status'      => 'success',
        
        'admin_id'    => session('alogin'),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('user Account delete', $datalogs);
			DB::commit();
			return response()->json([
				'status' => 'success',
				'message' => $id . ' MT5 Account Successfully Deleted'
			]);

		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'status' => 'error',
				'message' => 'DB Error: ' . $e->getMessage()
			]);
		}		
	}
	
	public function userdemoAccountdelete($id){
		$exists = DB::table('demoaccount')
			->where('trade_id', $id)
			->exists();
		if ($exists) {
			if (($error_code = $this->api->UserDelete($id)) != MTRetCode::MT_RET_OK) {
                return redirect()->back()->with("error", "Something went wrong on deleted the account " . MTRetCode::GetError($error_code));
            } else {
                DB::table('demoaccount')
                    ->where('trade_id', $id)
                    ->update([
						'status'       => 'deleted',
						'deleteRemark' => 'Admin has deleted this demo account.',
						'deletedby'    => session('alogin'),
						'deleted_at'   => now()
                    ]);
                      $datalogs = [
        'action'      => 'Demo MT5 Account Delete',
        'trade_id'    => $id,
        'status'      => 'success',
        
        'admin_id'    => session('alogin'),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('Demo MT5 Account Delete', $datalogs);
				return response()->json([
					'status' => 'success',
					'message' => $id.' MT5 Account Details Successfully Deleted'
				]);
            }
		} else {
			return response()->json([
				'status' => 'error',
				'message' => $id.' Account Not Found!!'
			]);
		}
	}
}
