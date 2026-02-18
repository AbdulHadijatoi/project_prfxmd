<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\Page;
use App\Models\Permissions;
use App\Models\EmployeeList;
use App\Models\ActionOtp;
use App\Models\AccountType;
use App\Models\UserGroup;
use App\Models\RmMapping;
use App\Models\LiveAccount;
use Validator;

class StaffManagement extends Controller
{

    public function roles()
    {
        return view('admin.roles');
    }
    public function rolePermissions()
    {
        $roles = Roles::all();
        return view('admin.role_permissions', compact('roles'));
    }
    public function adminUsers()
    {
        $roles = Roles::where('is_active', 1)->get();
        $user_groups = [];
        $mt5_groups = [];
        $rm_list = [];
        if (session('userData.user_group_id')) {
            $adminUserGroups = json_decode(session('userData.user_group_id'), true);
            $userGroupsList = implode(',', $adminUserGroups);
            $user_groups = UserGroup::where('status', 1);
            if (session('userData.client_index') != 1) {
                $user_groups = $user_groups->whereIn('user_group_id', $adminUserGroups);
            }
            $user_groups = $user_groups->get();
            // $mt5_groups = AccountType:: where('status', 1)
            //     ->whereIn('user_group_id', $adminUserGroups)
            //     ->get();


            $mt5_groups = AccountType::with('mt5Group')
                ->whereHas('mt5Group', function ($query) {
                    $query->where('is_active', 1);
                })
                ->where('status', 1)
                ->whereIn('user_group_id', $adminUserGroups)
                ->get();

            $rm_list = EmployeeList::where('role_id', 2)
                ->where(function ($query) use ($adminUserGroups) {
                    foreach ($adminUserGroups as $value) {
                        $query->orWhereRaw('JSON_CONTAINS(user_group_id, ?)', [json_encode((string) $value)]);
                    }
                })
                ->select('client_index', 'email', 'username', 'user_group_id')
                ->get();
        }
         addIpLog('Permissions List Viewed', [
        'admin_id' => session('userData.client_index'),
        'allowed_user_groups' => $adminUserGroups,
        'roles_count' => $roles->count(),
        'user_groups_count' => is_array($user_groups) ? 0 : $user_groups->count(),
        'mt5_groups_count' => is_array($mt5_groups) ? 0 : $mt5_groups->count(),
        'rm_list_count' => is_array($rm_list) ? 0 : $rm_list->count(),
        'ip' => request()->ip(),
    ]);
        return view('admin.admin_users', compact('roles', 'user_groups', 'mt5_groups', 'rm_list'));
    }
    public function permissionsList(Request $request)
    {
        $id = $request->id;
        $roles = Roles::where('role_id', $id)->first();
        $pages = Page::orderBy('dup_id', 'asc')->get();
        $permissions = Permissions::where('role_id', $id)->get()->toArray();
        $rolePermissions = array_values(array_column($permissions, 'page_id'));
        $menu = [];
        foreach ($pages as $page) {
            if ($page['is_submenu'] == 0) {
                $datalogs = ['page_name' => $page['pagename'],
                    'page_id' => $page['page_id'],
                    'submenu' => []];
                $menu[$page['page_id']] = [
                    'page_name' => $page['pagename'],
                    'page_id' => $page['page_id'],
                    'submenu' => []
                ];
            } else {
                $menu[$page['is_submenu']]['submenu'][] = [
                    'page_id' => $page['page_id'],
                    'page_name' => $page['pagename']
                ];
            }
        }
         addIpLog('permissionsList  ', $datalogs);
        return view('admin.permissions_list', compact('menu', 'rolePermissions', 'pages', 'roles'));
    }
    public function addRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'role_desc' => 'nullable|string',
        ]);
        $role = new Roles();
        $role->role_name = $request->input('role_name');
        $role->role_desc = $request->input('role_desc');
        $role->created_by = session('userData')['client_index'];
        $role->is_active = $request->has('is_active') ? 1 : 0;
        $datalogs = [
               $role = new Roles(),
        $role->role_name = $request->input('role_name'),
        $role->role_desc = $request->input('role_desc'),
        $role->created_by = session('userData')['client_index'],
        $role->is_active = $request->has('is_active') ? 1 : 0,
        ];
        addIpLog('Add Role Status  ', $datalogs);
        if ($role->save()) {
            return redirect()->back()->with("success", "New Role Added");
        }
        return redirect()->back()->with("error", "Failed to add role");
    }
    public function updateRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'role_desc' => 'nullable|string',
        ]);
        $id = $request->input('role_id');
        $role = Roles::where('role_id', $id)->firstOrFail();
        $role->role_name = $request->input('role_name');
        $role->role_desc = $request->input('role_desc');
        $role->is_active = $request->has('is_active') ? 1 : 0;

        $datalogs = [
              $id = $request->input('role_id'),
        $role = Roles::where('role_id', $id)->firstOrFail(),
        $role->role_name = $request->input('role_name'),
        $role->role_desc = $request->input('role_desc'),
        $role->is_active = $request->has('is_active') ? 1 : 0,
        ];
         addIpLog('update Role  ', $datalogs);
        if ($role->save()) {
            return redirect()->back()->with("success", "Role Details Updated");
        }
        return redirect()->back()->with("error", "Failed to update role");
    }
    public function updateRoleStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);
        $id = $request->input('role_id');
        $role = Roles::where('role_id', $id)->firstOrFail();
        $role->is_active = $request->input('status');
        $datalogs = [
               $id = $request->input('role_id'),
        $role = Roles::where('role_id', $id)->firstOrFail(),
        $role->is_active = $request->input('status'),
        ];
         addIpLog('update Role Status  ', $datalogs);
        if ($role->save()) {

            return redirect()->back()->with("success", "Status Updated Successfully");
        }
        return redirect()->back()->with("error", "Failed to update role");
    }
    public function updateRolePermissions(Request $request)
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*' => 'integer',
        ]);
        $roleId = $request->input('role_id');
        Permissions::where('role_id', $roleId)->delete();

        $pages = $request->input('pages');
        $createdBy = session('userData')['client_index'];
        $permissions = [];
        foreach ($pages as $pageId) {
            $datalogs = [ 'page_id' => $pageId,
                'role_id' => $roleId,
                'created_by' => $createdBy,];
            $permissions[] = [
                'page_id' => $pageId,
                'role_id' => $roleId,
                'created_by' => $createdBy,
            ];
        }
         addIpLog('update Role Permissions  ', $datalogs);
        Permissions::insert($permissions);
        return redirect()->back()->with('permissions', 'Permissions updated successfully');
    }
    public function saveUser(Request $request, $userId = null)
    {

    // echo'<pre>';print_r( $grp_ids);exit;
        $userId = request()->input('user_id');
        $request->validate([
            'username' => 'required|string|max:255',
            'role_id' => 'required|integer',
            'email' => 'required|email|max:255|unique:emplist,email,' . $userId . ',client_index',
            'number' => 'required|string|max:15',
            'password' => $userId ? 'nullable|string' : 'required|string',
            'company_name' => 'required|string|max:255',
        ]);
        if ($userId) {
            $user = EmployeeList::findOrFail($userId);
        } else {
            echo'<pre>';print_r( 'else');
            $user = new EmployeeList();
            $user->uid = '';
            $user->profile_pic = '';
            $user->empId = '';
            $user->userDepartment = '';
            $user->emailToken = '';
            $user->country = '';
        }
        $user->username = $request->input('username');
        $user->role_id = $request->input('role_id');
        $user->email = $request->input('email');
        $user->number = $request->input('number');
        if ($request->filled('password')) {
            $user->password = $request->input('password');
        }
        $user->company_name = $request->input('company_name');
        $user->userRole = $this->getRoleName($request->input('role_id'));
        $user->status = $request->has('is_active') ? 1 : 0;

                    $rm_users = $request->input('rm_list');
                    // $grp_ids = $request->input('group_id') ?? ["0"]; //old
                    
                    $grp_ids = $request->input('group_id', []);

            // remove empty values
            $grp_ids = array_filter($grp_ids);

            // if empty after filtering, set default
            if (empty($grp_ids)) {
                $grp_ids = ['1'];
            }

        $user->user_group_id = json_encode($grp_ids);
        $user->mt5_group_id = $request->input('mt5_group_id') ? json_encode($request->input('mt5_group_id')) : NULL;


        RmMapping::where('supervisor_id', $user->email)->delete();
        if ($user->save()) {
            if ($request->input('role_id') == 9) {
                $rmUsers = $request->input('rm_list');
                $data = [];
                $datalogs = [];
                foreach ($rmUsers as $rm) {
                    $datalogs = [ 'rm_id' => $rm,
                        'supervisor_id' => $request->input('email'),
                        'created_by' => session('alogin')];
                    $data[] = [
                        'rm_id' => $rm,
                        'supervisor_id' => $request->input('email'),
                        'created_by' => session('alogin')
                    ];
                }
                RmMapping::insert($data);
                addIpLog('New User Added  ', $datalogs);
            }
               
            $respMsg = $userId ? 'User Updated' : 'New User Added';
            return redirect()->back()->with('success', $respMsg);
        }
        return redirect()->back()->with('error', "Failed to save user");
    }
    protected function getRoleName($roleId)
    {
        $role = Roles::find($roleId);
        return $role ? $role->role_name : 'Unknown';
    }
    public function rmDashboard(Request $request)
    {
        $clientIndex = $request->query('id');
        $rm_details = EmployeeList::whereRaw("MD5(client_index) = ?", [$clientIndex])->first();
        if (!$rm_details) {
            return redirect('/admin/admin_users')->with('error', 'Invalid Account / Account Not Found');
        } else {
            $rm_id = $rm_details->email;
            $rm_email_enc = md5($rm_id);
        }
        $inactiveUsers = \DB::table('aspnetusers as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->selectRaw('SUM(CASE WHEN trs.status = 0 THEN 1 ELSE 0 END) AS inactive_users,
                  SUM(CASE WHEN trs.status = 1 THEN 1 ELSE 0 END) AS active_users')
            ->first();

        $total_clients = [
            'inactive_users' => $inactiveUsers->inactive_users,
            'active_users' => $inactiveUsers->active_users,
        ];
        $trade_deposit = \DB::table('trade_deposit as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.status', 1)
            ->whereNotIn('trs.deposit_type', ['Wallet Transfer'])
            ->sum('trs.deposit_amount');

        $wallet_deposit = \DB::table('wallet_deposit as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.status', 1)
            ->sum('trs.deposit_amount');

        $trade_withdrawal = \DB::table('trade_withdrawal as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.status', 1)
            ->whereNotIn('trs.withdraw_type', ['Wallet Withdrawal'])
            ->sum('trs.withdrawal_amount');

        $wallet_withdrawal = \DB::table('wallet_withdraw as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.status', 1)
            ->sum('trs.withdraw_amount');

        $pending_wd = \DB::table('wallet_deposits as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.Status', 0)
            ->count();

        $pending_td = \DB::table('trade_deposit as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.Status', 0)
            ->count();

        $pending_ww = \DB::table('wallet_withdraw as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.Status', 0)
            ->count();

        $pending_tw = \DB::table('trade_withdrawal as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.Status', 0)
            ->count();

        $pending_ib = \DB::table('ib1 as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.status', 0)
            ->count();

        $wallet_users = \DB::table('aspnetusers as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.wallet_enabled', 1)
            ->count();
        $latest_pending_deposit = \DB::table('wallet_deposits as trs')
            ->select('trs.*')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.status', 0)
            ->orderBy('trs.raw_id', 'desc')
            ->limit(10)
            ->get();
        $latest_pending_withdrawal = \DB::table('wallet_withdraws as trs')
            ->Join('relationship_manager as rm', 'rm.user_id', '=', 'trs.email')
            ->where('rm.rm_id', $rm_id)
            ->where('trs.status', 0)
            ->orderBy('trs.raw_id', 'desc')
            ->limit(10)
            ->get();
        return view('admin.rm_dashboard', compact('rm_email_enc', 'latest_pending_deposit', 'latest_pending_withdrawal', 'rm_details', 'inactiveUsers', 'wallet_users', 'pending_ib', 'pending_td', 'pending_wd', 'pending_tw', 'pending_ww', 'wallet_withdrawal', 'trade_withdrawal', 'wallet_deposit', 'trade_deposit', 'total_clients'));
    }
    public function userGroups()
    {
        $settings = settings()['copyright_site_name_text'];
        $live_accounts = LiveAccount::all();
        return view('admin.user_groups', compact('live_accounts'));
    }
    public function saveUserGroup(Request $request, $userId = null)
    {
        $userGroupId = request()->input('user_group_id');

        if ($userGroupId) {
            $actionOtp = $request->input('action_otp');
            if (!$actionOtp) {
                return redirect()->back()->with('error', 'OTP is required to update user group.');
            }
            $record = ActionOtp::where('client_index', session('userID'))
                ->where('module', ActionOtp::MODULE_USERGROUP)
                ->where('action', ActionOtp::ACTION_UPDATE)
                ->orderBy('id', 'desc')
                ->first();
            if (!$record || $record->isExpired() || (string) $record->otp_code !== (string) $actionOtp) {
                return redirect()->back()->with('error', 'Invalid or expired OTP. Please request a new OTP and try again.');
            }
            ActionOtp::where('client_index', session('userID'))
            ->where('module', ActionOtp::MODULE_USERGROUP)
            ->where('action', ActionOtp::ACTION_UPDATE)
            ->delete();
        }

        $request->validate([
            'group_name' => 'required|string|max:255',
            'group_code' => 'required|string|max:255|unique:user_groups,group_code,' . $userGroupId . ',user_group_id',
            // 'description' => 'required',
        ]);
        if ($userGroupId) {
            $userGroup = UserGroup::find($userGroupId);
        } else {
            $userGroup = new UserGroup();
        }
        $userGroup->group_name = $request->input('group_name');
        $userGroup->group_code = $request->input('group_code');
        $userGroup->description = $request->input('description');
        $userGroup->status = $request->has('status') ? 1 : 0;
        $userGroup->is_visible = $request->has('is_visible') ? 1 : 0;
        $userGroup->bankwire = $request->input('bankwire');
        $userGroup->bankwire_status = $request->has('bankwire_status') ? 1 : 0;
        $userGroup->agent_account = $request->input('agent_account');
        $userGroup->agent_status = $request->has('agent_status') ? 1 : 0;


        $userGroup->now_payment_api = $request->input('now_payment_api');
        $userGroup->now_payment_security = $request->input('now_payment_security');
        $userGroup->now_payment_status = $request->has('now_payment_status') ? 1 : 0;

        $userGroup->crypto_payment_api = $request->input('crypto_payment_api');
        $userGroup->crypto_payment_security = $request->input('crypto_payment_security');
        $userGroup->crypto_payment_status = $request->has('crypto_payment_status') ? 1 : 0;
        $userGroup->crypto_payment_withdraw = $request->has('crypto_payment_withdraw') ? 1 : 0;

        $userGroup->payissa_wallet = $request->input('payissa_wallet');
        $userGroup->payissa_status = $request->has('payissa_status') ? 1 : 0;

        $userGroup->bank_account_details = $request->input('bank_account_details');
        $userGroup->bank_deposit_status = $request->has('bank_deposit_status') ? 1 : 0;

        $userGroup->international_bank_account_details = $request->input('international_bank_account_details');
        $userGroup->international_bank_status = $request->has('international_bank_status') ? 1 : 0;


        if ($request->hasFile('usdt_wallet_qr')) {
            $validator = Validator::make($request->all(), [
                'usdt_wallet_qr' => 'required|file|mimes:png,jpeg|max:2048',
            ]);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return redirect()->back()->with('error', $firstError);
            }
            $usdtWalletQR = $request->file('usdt_wallet_qr')->store('usdt_wallet', 'public');
            $userGroup->usdt_wallet_qr = $usdtWalletQR;
        }

        $userGroup->usdt_wallet_id = $request->input('usdt_wallet_id');
        $userGroup->usdt_status = $request->has('usdt_status') ? 1 : 0;


        if ($userGroup->save()) {
              addIpLog("User Group Save", [
            'user_group_id' => $userGroup->user_group_id,
            'group_name' => $userGroup->group_name,
            'group_code' => $userGroup->group_code,
            'updated_by' => session('userID'),
            'ip' => request()->ip(),
        ]);
            $respMsg = $userGroupId ? 'User Group Updated' : 'New User Group Added';
            return redirect()->back()->with('success', $respMsg);
        }
       

        return redirect()->back()->with('error', "Failed to save details");
    }
}
