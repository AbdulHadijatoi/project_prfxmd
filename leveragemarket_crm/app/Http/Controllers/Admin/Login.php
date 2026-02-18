<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeList;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Permissions;
use Vectorface\GoogleAuthenticator;



class Login extends Controller
{
    public function index()
    {
        return view('admin.login');
    }
    public function showLoginForm()
    {
        // dd(session());
        return view('admin.login');
        if (session()->has('alogin')) {
            return redirect('/admin/dashboard');
        }
        // dd("Hellow");

        return view('admin.login');
    }
    public function adminLogin(Request $request)
    {
		/*Clear if any existing account logged in or admin logged in*/
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget(['user', 'clogin']);
        unset($_SESSION['clogin']);
        unset($_SESSION['user']);
		
		$request->validate([
            'username' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('username', 'password');
        // Attempt to log the user in
        $user = EmployeeList::where('email', $credentials['username'])
            ->first();
        if ($user && ($credentials['password'] == $user->password)) {
            
			
			
			
			
			if ($user->status == '1') {
                // Store user details in session
                Auth::login($user);
                Session::put('alogin', $user->email);
                Session::put('userRoleID', $user->role_id);
                Session::put('userRole', $user->userRole);
                Session::put('userID', $user->client_index);
                Session::put('userData', $user->toArray());
                // Fetch permissions
                $permissions = DB::table('permissions as p')
                    ->join('pages as pg', 'p.page_id', '=', 'pg.page_id')
                    ->where('p.role_id', $user->role_id)
                    // ->where('pg.is_submenu', 0)
                    ->orderBy('pg.page_order', 'asc')
                    ->get(['p.page_id', 'pg.filename', 'pg.is_submenu']);

                $current_permissions = [];
                foreach ($permissions as $permission) {
                    $currentpermissions[] = $permission->filename;
                    if ($permission->is_submenu == 0) {
                        $current_permissions[] = $permission->filename;
                        $submenus = DB::table('pages')
                            ->where('is_submenu', $permission->page_id)
                            ->orderBy('page_order', 'asc')
                            ->get();

                        foreach ($submenus as $submenu) {
                            $current_permissions[] = $submenu->filename;
                        }
                    }
                }
                 $datalogs = [
                'action'      => 'Admin Login Attempt',
                'username'    => $credentials['username'],
               
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'timestamp'   => now(),
            ];
            addIpLog('Admin Login Attempt', $datalogs);
                Session::put('current_permissions', $currentpermissions);


                // Log user in
                if (strtolower($user->userRole) == 'super admin' || $user->userRole == 'Relationship Manager') {
                    $this->logLoginHistory($user->email);
                    // dd($user,$current_permissions);
                    return redirect('admin/dashboard');
                }
                if (in_array('/admin/dashboard', $current_permissions)) {
                    $this->logLoginHistory($user->email);
                    return redirect('admin/dashboard');
                } else {
                    $first_php_page = '';
                    foreach ($current_permissions as $permission) {
                        if (strpos($permission, '.php') !== false) {
                            $first_php_page = $permission;
                            break;
                        }
                    }
                    if (!empty($first_php_page)) {
                        $this->logLoginHistory($user->email);
                        return redirect($first_php_page);
                    } else {
                        return back()->with('error', 'You do not have any page permissions. Please contact the administrator.');
                    }
                }
            } else {
                return back()->with('error', 'Your account is inactive. Please contact the administrator.');
            }
			
        } else {
            return back()->with('error', 'Login Details are Invalid');
        }
    }

    public function validateAndRequestOtp(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('username', 'password');
        $user = EmployeeList::where('email', $credentials['username'])->first();
        if (!$user || $credentials['password'] != $user->password) {
            return response()->json(['success' => false, 'message' => 'Login Details are Invalid']);
        }
        if ($user->status != '1') {
            return response()->json(['success' => false, 'message' => 'Your account is inactive. Please contact the administrator.']);
        }
        Session::put('admin_otp_pending', $user->email);
        Session::put('admin_pending_user', $user->toArray());

           $datalogs = [
            'action'      => 'Admin OTP Request',
            'username'    => $credentials['username'],
          
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'timestamp'   => now(),
        ];
        addIpLog('Admin OTP Request', $datalogs);
        return response()->json(['success' => true]);
    }

    public function verifyOtpAndLogin(Request $request)
    {
        $request->validate(['otp' => 'required']);
        $otp = $request->input('otp');
        $email = Session::get('admin_otp_pending');
        $user = EmployeeList::where('email', $email)->first();
        if (!$user) {
            Session::forget(['admin_otp_pending', 'admin_pending_user', 'Admin_Verification_Otp', 'Admin_Verification_Otp_created_at']);
            return response()->json(['success' => false, 'message' => 'Session expired. Please try again.']);
        }
        if ($user->admin_login_otp === null || (string) $user->admin_login_otp !== (string) $otp) {
            // Session::forget(['admin_otp_pending', 'admin_pending_user', 'Admin_Verification_Otp', 'Admin_Verification_Otp_created_at']);
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP']);
        }
        if ($user->admin_login_otp_created_at !== null && (time() - (int) $user->admin_login_otp_created_at) > 60) {
            $user->update(['admin_login_otp' => null, 'admin_login_otp_created_at' => null]);
            Session::forget(['admin_otp_pending', 'admin_pending_user', 'Admin_Verification_Otp', 'Admin_Verification_Otp_created_at']);
            return response()->json(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
        }
        $user->update(['admin_login_otp' => null, 'admin_login_otp_created_at' => null]);
        Session::forget(['admin_otp_pending', 'admin_pending_user', 'Admin_Verification_Otp', 'Admin_Verification_Otp_created_at']);

        $userHasMfa = (int) $user->mfa_enable === 1 && !empty($user->mfa_secret);

        if ($userHasMfa) {
            Session::put('admin_2fa_pending', $user->email);
            addIpLog('Admin_Verification', [], $user->email);
            return response()->json(['success' => true, 'require_2fa' => true]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget(['user', 'clogin']);
        if (isset($_SESSION['clogin'])) {
            unset($_SESSION['clogin']);
        }
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        addIpLog('Admin_Verification', [], $user->email);
        $redirectUrl = $this->performAdminLogin($user);
        return response()->json(['success' => true, 'redirect' => $redirectUrl]);
    }

    public function verify2FaAndLogin(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        $code = $request->input('code');
        $email = Session::get('admin_2fa_pending');
        $user = EmployeeList::where('email', $email)->first();

        if (!$user || empty($user->mfa_secret)) {
            Session::forget(['admin_2fa_pending']);
            return response()->json(['success' => false, 'message' => 'Session expired. Please log in again.']);
        }

        $ga = new GoogleAuthenticator();
        $secret = base64_decode($user->mfa_secret);
        $valid = $ga->verifyCode($secret, $code, 1);

        if (!$valid) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
        }

        Session::forget(['admin_2fa_pending']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget(['user', 'clogin']);
        if (isset($_SESSION['clogin'])) {
            unset($_SESSION['clogin']);
        }
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        addIpLog('Admin_Verification', [], $user->email);
        $redirectUrl = $this->performAdminLogin($user);
        return response()->json(['success' => true, 'redirect' => $redirectUrl]);
    }

    private function performAdminLogin($user)
    {
        Auth::login($user);
        Session::put('alogin', $user->email);
        Session::put('userRoleID', $user->role_id);
        Session::put('userRole', $user->userRole);
        Session::put('userID', $user->client_index);
        Session::put('userData', $user->toArray());
        $permissions = DB::table('permissions as p')
            ->join('pages as pg', 'p.page_id', '=', 'pg.page_id')
            ->where('p.role_id', $user->role_id)
            ->orderBy('pg.page_order', 'asc')
            ->get(['p.page_id', 'pg.filename', 'pg.is_submenu']);
        $current_permissions = [];
        $currentpermissions = [];
        foreach ($permissions as $permission) {
            $currentpermissions[] = $permission->filename;
            if ($permission->is_submenu == 0) {
                $current_permissions[] = $permission->filename;
                $submenus = DB::table('pages')
                    ->where('is_submenu', $permission->page_id)
                    ->orderBy('page_order', 'asc')
                    ->get();
                foreach ($submenus as $submenu) {
                    $current_permissions[] = $submenu->filename;
                }
            }
        }
        Session::put('current_permissions', $currentpermissions);
        if (strtolower($user->userRole) == 'super admin' || $user->userRole == 'Relationship Manager') {
            return url('admin/dashboard');
        }
        if (in_array('/admin/dashboard', $current_permissions)) {
            return url('admin/dashboard');
        }
        $first_php_page = '';
        foreach ($current_permissions as $permission) {
            if (strpos($permission, '.php') !== false) {
                $first_php_page = $permission;
                break;
            }
        }
        if (!empty($first_php_page)) {
            return url($first_php_page);
        }
        return url('admin/dashboard');
    }

    
    private function logLoginHistory($email)
    {
        $country = '';
        $ip = request()->ip();
        LoginHistory::create([
            'email' => $email,
            'ip' => $ip,
            'country' => $country,
            'action' => 'Admin Login',
            'status' => 1
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();
        session()->forget(['userData', 'alogin']);
        unset($_SESSION['alogin']);
        unset($_SESSION['userData']);
        return redirect('/admin/login');
    }

    public function ib_migration()
    {
        $live_accs = LiveAccount::select(DB::raw("liveaccount.trade_id,aspnetusers.*"))
            ->join("aspnetusers", "aspnetusers.email", "=", "liveaccount.email")
            ->get();
        foreach ($live_accs as $live_acc) {
            LiveAccount::where("trade_id", $live_acc->trade_id)->update([
                "ib1" => $live_acc->ib1,
                "ib2" => $live_acc->ib2,
                "ib3" => $live_acc->ib3,
                "ib4" => $live_acc->ib4,
                "ib5" => $live_acc->ib5,
                "ib6" => $live_acc->ib6,
                "ib7" => $live_acc->ib7,
                "ib8" => $live_acc->ib8,
                "ib9" => $live_acc->ib9,
                "ib10" => $live_acc->ib10,
                "ib11" => $live_acc->ib11,
                "ib12" => $live_acc->ib12,
                "ib13" => $live_acc->ib13,
                "ib14" => $live_acc->ib14,
                "ib15" => $live_acc->ib15
            ]);
        }
        dd("Completed");
    }

}
