<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Session;
use App\Models\RelationshipManager;
use App\Services\MailService;
use App\Models\IbClientList;
use App\Models\TicketModel;
use Illuminate\Support\Str;
use App\Models\WalletDeposit;
use App\Models\WalletWithdraw;
use App\Models\ClientWallets;
use App\Models\WalletTransfer;
use Carbon\Carbon;

class ClientController extends Controller
{
    protected $mailService;
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    public function index()
    {

        // Fetch IB details
        $ib_details = DB::table('ib1')
            ->select('name', 'email')
            ->orderBy('name')
            ->get();

        // Fetch RM details
        $rm_details = DB::table('emplist as emp')
            ->select('emp.client_index', 'emp.email', 'emp.username')
            ->where('emp.role_id', 2)
            ->get();

        // Fetch Countries
        $countries = Country::all();

        // Fetch Deposits and Withdrawals
        $trade_deposit = DB::table('trade_deposit')
            ->where('status', 1)
            ->whereNotIn('deposit_type', ['Wallet Transfer'])
            ->sum('deposit_amount');

        $wallet_deposit = DB::table('wallet_deposit')
            ->where('status', 1)
            ->sum('deposit_amount');

        $trade_withdrawal = DB::table('trade_withdrawal')
            ->where('status', 1)
            ->whereNotIn('withdraw_type', ['Wallet Withdrawal'])
            ->sum('withdrawal_amount');

        $wallet_withdrawal = DB::table('wallet_withdraw')
            ->where('status', 1)
            ->sum('withdraw_amount');

        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1)";
        $sql = "SELECT COUNT(*) AS count FROM aspnetusers trs " . $rmCondition;
        $total_clients = DB::select($sql);


        $total_ib = DB::table('ib1')
            ->leftJoin('relationship_manager as rm', 'rm.user_id', '=', 'ib1.email')
            ->when(Session::get('userData.role_id') == 2, function ($query) {
                $query->where('rm.rm_id', Session::get('alogin'));
            })
            ->count();

        // Total Balance Details
        $total_balance = DB::table('total_balance')
            ->select(
                DB::raw('COALESCE(SUM(deposit_amount), 0) as deposit_amount'),
                DB::raw('COALESCE(SUM(withdraw_amount), 0) as withdraw_amount'),
                DB::raw('COALESCE(SUM(trading_deposited), 0) as trading_deposited'),
                DB::raw('COALESCE(SUM(trading_withdrawal), 0) as trading_withdrawal')
            )
            ->leftJoin('relationship_manager as rm', 'rm.user_id', '=', 'total_balance.email')
            ->when(Session::get('userData.role_id') == 2, function ($query) {
                $query->where('rm.rm_id', Session::get('alogin'));
            })
            ->first();

        // Account Groups
        $acc_groups = DB::table('ib_plan_details')
            ->join('ib_categories', 'ib_categories.ib_cat_id', '=', 'ib_plan_details.ib_plan_id')
            ->where('ib_plan_details.status', 1)
            ->select(DB::raw('ib_categories.ib_cat_name,ib_plan_details.ib_plan_id'))
            ->groupBy('ib_plan_details.ib_plan_id')
            ->get();
        $user_groups = DB::table('user_groups')
            ->where('status', 1)
            ->get()->toArray();
 
        return view("admin.client_list", compact(
            'ib_details',
            'rm_details',
            'countries',
            'trade_deposit',
            'wallet_deposit',
            'trade_withdrawal',
            'wallet_withdrawal',
            'total_clients',
            'total_ib',
            'total_balance',
            'acc_groups',
            'user_groups'
        ));
    }
public function resendemail(Request $request){


    $user = User::select("*")->where('email', $request->email)->first();
        $settings = settings();
                $from = $settings['email_from_address'];
                $toEmail = $request->email;
                $uid = uniqid();
                $emailSubject = $settings['admin_title'] . ' - Email Address Verfication';
                $htmlContent = "";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
                $content =
                    '<div>Welcome to ' . htmlspecialchars($settings['admin_title'], ENT_QUOTES, 'UTF-8') . '!</div>' .
                    '<div>You are receiving this email because you have registered for a Trading Account.</div>' .
                    '<div>Click the link below to activate your Trading Account</div>';
$datalogs = ['name' => Str::before($request->email, '@'),
                    'server_name' => $settings['mt5_company_name'],
                    'site_link' => $settings['copyright_site_name_text'] . "/email_verify?id=$user->id&code=$user->emailToken",
                    'email' => $settings['email_from_address'],
                    "content" => $content,
                    "title_right" => "Activate",
                    "subtitle_right" => "Your Account"];
                $templateVars = [
                    'name' => Str::before($request->email, '@'),
                    'server_name' => $settings['mt5_company_name'],
                    'site_link' => $settings['copyright_site_name_text'] . "/email_verify?id=$user->id&code=$user->emailToken",
                    'email' => $settings['email_from_address'],
                    "content" => $content,
                    "title_right" => "Activate",
                    "subtitle_right" => "Your Account"
                ];
                                addIpLog('admin send mail ', $datalogs);

                $this->mailService->sendEmail($toEmail, $emailSubject, $headers, '', $templateVars);

	
		 return response()->json(1);
	}

    public function updateRM(Request $request)
    {
        if ($request->has('rmUpdate')) {
            $email = $request->input('user_id');
             $user_id = $request->input('user_id');
            $rm_id = $request->input('rm_id');
            $exists = RelationshipManager::where('user_id', $email)->count();
            if ($exists > 0) {
                RelationshipManager::where('user_id', $email)->update(['rm_id' => $rm_id]);
            } else {
                RelationshipManager::create(['user_id' => $email, 'rm_id' => $rm_id]);
                $old_rm_id = null;
            $action = 'RM Created';
            }
            $datalogs = [
            'action'      => $action,
            'user_id'     => $user_id,
            'old_rm_id'   => $old_rm_id,
            'new_rm_id'   => $rm_id,
            'ip_address'  => $request->ip(),
            'updated_by'  => auth()->id(),
            'timestamp'   => now(),
        ];
             addIpLog('update RM ', $datalogs);
            return redirect()->back()->with('success', 'RM Details Updated');
        }
    }

    public function updateIB(Request $request)
    {
        if ($request->has('ibUpdate')) {
            try {
                $ibFields = [];
                $email = $request->input('client_id');

                for ($i = 1; $i <= 15; $i++) {
                    $value = $request->input("ib$i");
                    if (!empty($value)) {
                        $ibFields[] = $value;
                    }
                }

                if (count($ibFields) !== count(array_unique($ibFields))) {
                    return redirect()->back()->withErrors('Some IB fields contain duplicate values.');
                } else {
                    $currentValues = DB::table('aspnetusers')
                        ->whereRaw('md5(email) = ?', [$email])
                        ->select('ib1', 'ib2', 'ib3', 'ib4', 'ib5', 'ib6', 'ib7', 'ib8', 'ib9', 'ib10', 'ib11', 'ib12', 'ib13', 'ib14', 'ib15')
                        ->first();

                    $updateFields = [];
                    $logdata = [];

                    for ($i = 1; $i <= 15; $i++) {
                        $fieldName = "ib$i";
                        $newValue = $request->input($fieldName);
                        if ($newValue !== $currentValues->$fieldName) {
                            $updateFields[$fieldName] = $newValue;
                            $logdata[$fieldName] = $newValue;
                        }
                    }

                    if (!empty($updateFields)) {
                        DB::table('aspnetusers')
                            ->whereRaw('md5(email) = ?', [$email])
                            ->update($updateFields);
                            $datalogs = [
                                'email' => $email,
                            'type' => 'ib',
                            'value' => json_encode($logdata)
                            ];
                        $this->addToUserLog([
                            'email' => $email,
                            'type' => 'ib',
                            'value' => json_encode($logdata)
                        ]);
                          addIpLog('update IB ', $datalogs);
                        return redirect()->back()->with('success', 'Client IB Details Updated Successfully');
                    } else {
                        return redirect()->back()->with('success', 'No changes were made. Everything is up to date!');
                    }
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('success', $e->getMessage());
            }
        }
    }

    public function addUser(Request $request)
    {
        if ($request->has('addUser')) {
            $fullname = $request->input('fullname');
            $email = $request->input('email');
            $password = $request->input('password');
            $confirmPassword = $request->input('confirm_password');
            $country = $request->input('country');
            $country_code = $request->input('country_code');
            $number = $request->input('telephone');
            $referral = '';
            $group_id = $request->input('group_id');
            $code = md5(uniqid(rand()));

            // Check if passwords match
            if ($password !== $confirmPassword) {
                return redirect()->back()->with('error', 'Passwords do not match');
            }

            // Check if the user already exists
            $userExist = DB::table('aspnetusers')->where('email', $email)->exists();

            if ($userExist) {
                return redirect()->back()->with('error', 'Email already exists');
            } else {
                $status = 1;
                $emailConfirmed = 1;
                try {
                    // Insert new user into the database

                    $datalogs = [
                        'email' => $email,
                        'fullname' => $fullname,
                        'password' => $password,
                        'country_code' => $country_code,
                        'number' => $number,
                        'username' => $email,
                        'referral' => $referral,
                        'emailToken' => $code,
                        'country' => $country,
                        'status' => $status,
                        'email_confirmed' => $emailConfirmed,
                        'group_id' => $group_id
                    ];
                    $lastInsertId = DB::table('aspnetusers')->insertGetId([
                        'email' => $email,
                        'fullname' => $fullname,
                        'password' => $password,
                        'country_code' => $country_code,
                        'number' => $number,
                        'username' => $email,
                        'referral' => $referral,
                        'emailToken' => $code,
                        'country' => $country,
                        'status' => $status,
                        'email_confirmed' => $emailConfirmed,
                        'group_id' => $group_id
                    ]);

                    if ($lastInsertId) {
                        $logData = [
                            'email' => $email,
                            'type' => 'client_add',
                            'value' => json_encode($request->except(['addUser', 'password', 'confirm_password']))
                        ];
                        $this->addToUserLog($logData);
                        $from = settings()['email_from_address'];
                        $emailSubject = settings()['admin_title'] . ' - Welcome Email';
                        $templateVars = [
                            'name' => $fullname,
                            'site_link' => settings()['copyright_site_name_text'],
                            'email' => $from,
                            'content' => $this->buildWelcomeContent($fullname, $email, $password),
                            'title_right' => 'Welcome',
                            'subtitle_right' => 'Aboard!',
                            'btn_text' => 'Login'
                        ];
                        addIpLog('create user in admin ', $datalogs);
                        $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                        return redirect()->back()->with('success', 'User created successfully');
                    } else {
                        return redirect()->back()->with('error', 'User creation failed');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
                }
            }
        }
    }

    private function buildWelcomeContent($fullname, $email, $password)
    {
        return "
            <div>Welcome to " . htmlspecialchars(settings()['admin_title'], ENT_QUOTES, 'UTF-8') . "! We're excited to have you on board.</div>
            <div>Your account has been successfully created, and you're now part of our growing community.</div>
            <div><b>Here are your login credentials:</b></div>
            <div><b>Username: </b>{$email}</div>
            <div><b>Password: </b>{$password}</div>
            <div>If you have any queries, please contact our support team. We’re here to help!</div>
        ";
    }
    public function updateUser(Request $request)
    {
        if ($request->has('updateUser')) {
            $email = $request->input('email');
            $fullname = $request->input('fullname');
            $password = $request->input('password');
            $confirmPassword = $request->input('confirm_password');
            $country = $request->input('country');
            $country_code = $request->input('country_code');
            $number = $request->input('telephone');
            $code = $request->input('id');
            $group_id = $request->input('group_id');
            $emailNotification = $request->input('email_notification');
            if ($password !== $confirmPassword) {
                return redirect()->back()->with('error', 'Passwords do not match');
            }
            $status = 1;
            $emailConfirmed = 1;

            try {
                // Update user in the database
                $datalogs = [
                    'fullname' => $fullname,
                        'password' => $password,
                        'number' => $number,
                        'country_code' => $country_code,
                        'country' => $country,
                        'group_id' => $group_id
                ];
                $affectedRows = DB::table('aspnetusers')
                    ->where(DB::raw('md5(email)'), $code)
                    ->update([
                        'fullname' => $fullname,
                        'password' => $password,
                        'number' => $number,
                        'country_code' => $country_code,
                        'country' => $country,
                        'group_id' => $group_id
                    ]);

                // If update is successful
                if ($affectedRows > 0) {
                    $updateData = [
                        'email' => $email,
                        'type' => 'client_update',
                        'value' => json_encode($request->except(['updateUser', 'password', 'confirm_password']))
                    ];
                    // Log the update (you need to implement this function if not already available)
                    $this->addToUserLog($updateData);

                    // Send email notification if required
                    if ($emailNotification) {
                        $from = settings()['email_from_address'];
                        $emailSubject = settings()['admin_title'] . ' - Your Account Details Have Been Updated';
                        $templateVars = [
                            'name' => $fullname,
                            'site_link' => settings()['copyright_site_name_text'],
                            'email' => $from,
                            'content' => $this->buildEmailContent($fullname, $password, $country_code . $number, $country),
                            'title_right' => 'Account',
                            'subtitle_right' => 'Updation',
                            'btn_text' => 'Dashboard'
                        ];
                        addIpLog('update user in admin ', $datalogs);
                        $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                    }
                    return redirect()->back()->with('success', 'Details updated successfully');
                } else {
                    return redirect()->back()->with('error', 'Update failed! No changes were made.');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
    }

    private function buildEmailContent($fullname, $password, $number, $country)
    {
        return "
        <div>We hope this message finds you well!</div>
        <div>We wanted to inform you that your account details have been successfully updated.</div>
        <div><b>Latest Details:</b></div>
        <div><b>Name: </b>{$fullname}</div>
        <div><b>Password: </b>{$password}</div>
        <div><b>Telephone: </b>{$number}</div>
        <div><b>Country: </b>{$country}</div>
        <div>If you have any queries, please contact our support team. We’re here to help!</div>
        <div>Thank you for being a valued member of our community!</div>
    ";
    }

    private function addToUserLog($data)
    {
        DB::table('aspnetusers_log')->insert([
            'email' => $data['email'],
            'admin_email' => Session::get('alogin'),
            'type' => $data['type'],
            'value' => $data['value']
        ]);
    }
    function add_to_user_log($data)
    {
        User::create([
            'email' => $data['email'],
            'admin_email' => session('alogin'),
            'type' => $data['type'],
            'value' => json_encode($data['value'])
        ]);
    }
    public function clientDetails(Request $request)
    {
        $id = request('id');
        $user = DB::table('aspnetusers as ap')
            ->leftJoin('ib1', 'ib1.email', '=', 'ap.email')
            ->leftJoin('user_groups', 'user_groups.user_group_id', '=', 'ap.group_id')
            ->select('ap.*', 'ib1.status as ib_status', 'ib1.acc_type as ib_group', 'user_groups.group_name as user_group_name')
            ->where(DB::raw('md5(ap.id)'), $id)
            ->orWhere(DB::raw('md5(ap.email)'), $id)
            ->first();
        $acc_groups = DB::table('ib_plan_details')
            ->join('ib_categories', 'ib_categories.ib_cat_id', '=', 'ib_plan_details.ib_plan_id')
            ->where('ib_plan_details.status', 1)
            ->select(DB::raw('ib_categories.ib_cat_name,ib_plan_details.ib_plan_id'))
            ->groupBy('ib_plan_details.ib_plan_id')
            ->get();
        $userGroups = json_decode(session("userData")["user_group_id"]);
        // dd($userGroups);
        $acc_types = DB::table('account_types as ac')
            ->leftJoin('mt5_groups as m', 'ac.ac_type', '=', 'm.mt5_group_id')
            ->select('ac.*')
            ->where('m.mt5_group_type', 'live')
            ->where('ac.status', 1)
            ->where('m.is_active', 1)
            ->whereIn("ac.user_group_id", $userGroups)
            ->get();

        if (!empty($user)) {
            $eid = $user->email;
			$email = $user->email;
            $clients = [];
            // for ($i = 1; $i <= 15; $i++) {
            //     $foundClients = IbClientList::where("ib$i", $user->email)->get();
            //     $clients[$i] = $foundClients;
            // }
            for ($i = 1; $i <= 15; $i++) {
                $foundClients = IbClientList::where("ib$i", $user->email)->get();
                foreach ($foundClients as $client) {
                    $ibExists = DB::table('ib1')
                        ->where('email', $client->email)
                        ->where('status', 1)
                        ->count();
                    $client->ib_exists = $ibExists > 0 ? true : false;
                }
                $clients[$i] = $foundClients;
            }

            $total_wd = DB::table('wallet_deposit')
                ->where('email', $eid)
                ->where('status', 1)
                ->selectRaw('SUM(deposit_amount) as amount')
                ->first();
				
			$sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs WHERE trs.status=1 and trs.email='" . $eid . "' and trs.deposit_type NOT IN('Wallet Transfer', 'Wallet Payments', 'W2A Deposit', 'A2A Transfer')";
			$trade_deposit = DB::select($sql)[0];

			$sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from wallet_deposit trs WHERE trs. status=1 and trs.email='" . $eid . "' and trs.deposit_type NOT IN('Wallet Transfer', 'A2A Transfer', 'A2W withdraw', 'A2W Deposit')";
			$wallet_deposit = DB::select($sql)[0];
			$total_wd = $trade_deposit->deposit + $wallet_deposit->deposit;
			
			$total_witdraw = DB::table('wallet_withdraw as trs')
				->select(DB::raw('COALESCE(SUM(trs.withdraw_amount),0) as withdraw'))
				->where('trs.email', $eid)
				->whereIn('trs.status', [1])
				->whereIn('trs.withdraw_type', ['Wallet Withdrawal', 'External Withdrawal', 'Wallet Withdrawal (Admin)'])
				->get();
			$total_ww = $total_witdraw[0]->withdraw;				
            
            $wallet_balance = (float) $total_wd - (float) $total_ww;
			
			$a2a_transferrs = DB::table('trade_withdrawal as trs')
			->select(DB::raw('COALESCE(SUM(trs.withdrawal_amount),0) as a2awithdraw'))
			->whereIn('trs.withdraw_type', ['A2A Transfer', 'W2A Deposit', 'A2W withdraw'])
			->where('trs.email', $eid)
			->where('trs.status', 1)
			->get();
			$a2a_transfer = $a2a_transferrs[0]->a2awithdraw;		
			
            $total_balance = DB::table('total_balance')
                ->where('email', $eid)
                ->selectRaw('
            SUM(deposit_amount) as deposit_amount,
            SUM(trading_deposited) as trading_deposited,
            SUM(trading_withdrawal) as trading_withdrawal,
            SUM(withdraw_amount) as withdraw_amount
        ')
                ->first();
            $live_accounts = DB::table('liveaccount')
                ->leftJoin('account_types', 'account_types.ac_index', '=', 'liveaccount.account_type')
                ->where('email', $eid)
				->where('liveaccount.status', 'active')
                ->select('liveaccount.*', 'account_types.ac_name', 'account_types.ac_group')
                ->orderByDesc('id')
                ->get();
            $bank_details = DB::table('clientbankdetails')
                ->where('userId', $eid)
                ->first();
            $kyc_details = DB::table('kyc_update')
                ->where('email', $eid)
                ->get();
            $ib_details = DB::table('ib1')
                ->leftJoin('ib_wallet', 'ib1.email', '=', 'ib_wallet.email')
                ->leftJoin('account_types as ac', 'ac.ac_index', '=', 'ib1.acc_type')
                ->select('ib1.*', DB::raw('SUM(ib_wallet.ib_wallet) as deposit'), DB::raw('SUM(ib_wallet.ib_withdraw) as withdraw'), 'ac.ac_name')
                ->where('ib1.status', 1)
                ->where('ib1.email', $eid)
                ->groupBy('ib1.email')
                ->havingRaw('COUNT(ib1.email) > 0')
                ->first();
        $rm_details = DB::table('relationship_manager as rm')
    ->leftJoin('emplist as emp', 'rm.rm_id', '=', 'emp.email')
    ->select('emp.username')
    ->where('rm.user_id', $eid)
    ->first();


            $superadmin_details = DB::table('emplist')
                ->where('role_id', 1)
                ->first();
            $country_code = DB::table('countries')
                ->where('country_name', $user->country)
                ->first();
        }
        $ticket_status_obj = DB::table('ticket_status')->get()->toArray();
        $ticket_status = json_decode(json_encode($ticket_status_obj), true);
        $ticket_types_obj = DB::table('ticket_types')->get()->toArray();
        $ticket_types = json_decode(json_encode($ticket_types_obj), true);


        $rmCondition = app('permission')->appendRolePermissionsQry('t', 'email_id') . " (1=1) ORDER BY t.created_at DESC";
        $tickets = DB::table('tickets as t')
            ->leftJoin('ticket_status as ts', 't.ticket_status_id', '=', 'ts.id')
            ->leftJoin('ticket_types as tt', 't.ticket_type_id', '=', 'tt.id')
            ->leftJoin('aspnetusers as u', 't.email_id', '=', 'u.email')
            ->leftJoin('emplist as e', 't.created_by', '=', 'e.client_index')
            ->leftJoin('aspnetusers as c', 't.created_user', '=', 'c.id')
            ->leftJoin(
                DB::raw('(SELECT tf1.* FROM ticket_followup tf1 INNER JOIN (
                                 SELECT ticket_id, MAX(added_at) as latest_followup
                                 FROM ticket_followup
                                 GROUP BY ticket_id) tf2
                                 ON tf1.ticket_id = tf2.ticket_id AND tf1.added_at = tf2.latest_followup) tf'),
                't.id',
                '=',
                'tf.ticket_id'
            )
            ->leftJoin('aspnetusers as fu', 'tf.user_id', '=', 'fu.id')
            ->leftJoin('emplist as fa', 'tf.admin_id', '=', 'fa.client_index')
            ->select(
                't.id as ticket_id',
                't.subject_name',
                't.discription',
                't.created_at',
                't.email_id',
                'ts.ticket_status',
                'ts.ticket_label',
                'tt.ticket_type',
                'u.fullname',
                DB::raw('IF(t.created_by IS NULL, c.fullname, e.username) as created_user'),
                'tf.added_at as last_followup',
                'tf.user_type as followup_type',
                'fu.fullname as followup_user',
                'fa.username as followup_admin'
            )->where('t.email_id', $eid)->get();

        // $query = $ticketsQuery->toSql() . ' ' . $rmCondition;
        // $tickets = DB::select($query);


        $ibdetails = DB::table('ib1')
            ->select('name', 'email')
            ->orderBy('name')
            ->get();
        $rmdetails = DB::table('emplist as emp')
            ->select('emp.client_index', 'emp.email', 'emp.username')
            ->where('emp.role_id', 13)
            ->get();
        $countries = Country::all();
        $user_groups = DB::table('user_groups')
            ->where('status', 1)
            ->get()->toArray();
        $create_new_acc_types = DB::table('account_types')
            ->join('mt5_groups', 'mt5_groups.mt5_group_id', '=', 'account_types.ac_type')
            ->whereIn('mt5_groups.mt5_group_type', ['live', 'real'])
            ->where('account_types.status', 1)
            ->where('account_types.is_client_group', 1)
            ->select('account_types.*', 'mt5_groups.mt5_group_type')
            ->get();
           $eid = $user->email;
        $totalDeposit = WalletDeposit::where('email', $eid)
            ->where('status', 1)
            ->sum('deposit_amount');
        $totalWithdraw = WalletWithdraw::where('email', $eid)
            ->whereIn('status', [0,1])
            ->sum('withdraw_amount');
        $walletBalance = $totalDeposit - $totalWithdraw;
        $wallet_accounts = ClientWallets::where('user_id', $eid)
            ->when($request->filled('wallet_search'), function ($q) use ($request) {
                $term = $request->wallet_search;
                $q->where(function ($q2) use ($term) {
                    $q2->where('wallet_name', 'like', '%' . $term . '%')
                        ->orWhere('wallet_currency', 'like', '%' . $term . '%')
                        ->orWhere('wallet_network', 'like', '%' . $term . '%')
                        ->orWhere('wallet_address', 'like', '%' . $term . '%');
                });
            })
            ->paginate(10)
            ->withQueryString();
        // echo'<pre>';print_r($walletBalance);exit;
		
		/*Wallet transcation summary*/
		$from = $request->from;
		$to   = $request->to;
		$type = $request->filter_type;
		$paymode = $request->filter_paymode; 
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
		
		
        if ($request->get('partial') == '1') {
            return response()->view('admin.partials.client_wallet_details', compact('wallet_accounts'));
        }
        return view('admin.client_details', compact(
            'ticket_status',
            'ticket_types',
            'user',
            'acc_groups',
            'acc_types',
            'wallet_balance',
            'total_balance',
            'live_accounts',
            'bank_details',
            'kyc_details',
            'ib_details',
            'rm_details',
            'superadmin_details',
            'country_code',
            'total_wd',
            'total_ww',
            'clients',
            'tickets',
            'ibdetails',
            'rmdetails',
            'countries',
            'user_groups',
            'create_new_acc_types','walletBalance','wallet_accounts', 'a2a_transfer', 'ledger'
        ));
    }
    public function sendPasswordResetLink(Request $request)
    {
        $email = $request->txtemail;
        $user = User::where('email', $email)->first();
        if ($user) {
            $code = md5(uniqid(rand()));
            $user->update(['emailToken' => $code]);
            $content =
                '<div>Welcome to ' . htmlspecialchars(settings()['admin_title'], ENT_QUOTES, 'UTF-8') . '!</div>' .
                '<div>We received a request to reset your password. If you made this request, click the link below to reset your password. If you did not request a password reset, you can ignore this email.
      </div>';
            $from = settings()['email_from_address'];
            $emailSubject = settings()['admin_title'] . ' - Password Reset';
            $datalogs = [
                'name' => $user->fullname,
                'site_link' => settings()['copyright_site_name_text'] . "/reset-password?id=$user->id&code=$code",
                'btn_text' => "Reset Password",
                'email' => $from,
                "content" => $content,
                "title_right" => "Reset",
                "subtitle_right" => "Your Password"
            ];
            $templateVars = [
                'name' => $user->fullname,
                'site_link' => settings()['copyright_site_name_text'] . "/reset-password?id=$user->id&code=$code",
                'btn_text' => "Reset Password",
                'email' => $from,
                "content" => $content,
                "title_right" => "Reset",
                "subtitle_right" => "Your Password"
            ];
            addIpLog('reset password  in admin ', $datalogs);
            $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
            return redirect()->back()->with("success", "An email has been sent to $email with the password reset link.");
        } else {
            return redirect()->back()->with("error", "User not found.");
        }
    }
public function updateAccLimit(Request $request)
{
    $email_hash = $request->email;   // this is md5(email)
    $acc_limit  = $request->acc_limit;

    // Get existing user record
    $user = DB::table('aspnetusers')
        ->where(DB::raw('md5(email)'), $email_hash)
        ->first();

    if (!$user) {
        return redirect()->back()->with("error", "User not found.");
    }

    $old_limit = $user->acc_limit;

    // Update limit
    DB::table('aspnetusers')
        ->where(DB::raw('md5(email)'), $email_hash)
        ->update([
            'acc_limit' => $acc_limit
        ]);

    // ✅ Proper Data Logs
    $datalogs = [
        'action'        => 'Account Limit Updated',
        'user_id'       => $user->id ?? null,
        'email'         => $user->email ?? null,
        'old_limit'     => $old_limit,
        'new_limit'     => $acc_limit,
        'ip_address'    => $request->ip(),
        'updated_by'    => auth()->id(),
        'user_agent'    => $request->userAgent(),
        'timestamp'     => now(),
    ];

    addIpLog('Update Acc Limit', $datalogs);

    return redirect()->back()->with("success", "Account limit updated.");
}

    public function activityLog()
    {
        return view("admin.ip_activity");
    }
public function activityLogview($id)
{
    $log = DB::table('login_history')
        ->leftJoin('aspnetusers as user', 'login_history.email', '=', 'user.email')
        ->leftJoin('emplist as emp', 'login_history.email', '=', 'emp.email')
        ->select(
            DB::raw('COALESCE(user.fullname, emp.username) as display_name'),
            'login_history.*'
        )
        ->where('login_history.id', $id)   // make sure 'id' exists in login_history
        ->first();

    if (!$log) {
        abort(404, 'Activity log not found.');
    }

    $log->datalog = json_decode($log->datalog, true) ?? [];

    return view('admin.ip_activityview', ['log' => $log]);
}


}
