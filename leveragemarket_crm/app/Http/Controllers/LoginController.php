<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Vectorface\GoogleAuthenticator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Services\MailService as MailService;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;
use App\Models\UserGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Mews\Purifier\Facades\Purifier;

class LoginController extends Controller
{
    protected $mailService;
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    // Show login form
    public function showLoginForm(Request $request)
    {
        if (Auth::check() && session('clogin')) {
            return redirect()->route('dashboard');
        }
        if ($request->query('cancel_2fa')) {
            session()->forget('customer_2fa_pending');
            return redirect()->route('login');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        /*Clear if any existing account logged in or admin logged in*/
		Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget(['userData', 'alogin']);
        unset($_SESSION['alogin']);
        unset($_SESSION['userData']);
		
		// Validate form inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        
        // Prepare credentials
        $credentials = $request->only('email', 'password');
        // Check if the email is confirmed
        $user = User::where('email', $credentials['email'])
            ->where('email_confirmed', 1)
            ->first();
        // Check if user exists
        if ($user) {
           
            if ($user->password === $credentials['password']) {
                $userHasMfa = isset($user->mfa_enable) && (int) $user->mfa_enable === 1 && !empty($user->mfa_secret ?? null);
                if ($userHasMfa) {
                    session(['customer_2fa_pending' => $user->email]);
                    return redirect()->route('login')->with('step', '2fa');
                }
                // Log the user in
                Auth::login($user);
                $_SESSION['clogin'] = $credentials['email'];
                $_SESSION['user'] = $user->toArray();
                session(['user' => $user, 'clogin' => $credentials['email']]);
            } else {
                // Password doesn't match
                return back()->with('error', 'Your login details are invalid or your email is not verified.');
            }
            // Regenerate the session to prevent session fixation
            $request->session()->regenerate();
            // Get IP and country information
            // $response = Http::get('https://api.ipgeolocation.io/ipgeo', [
            //     'apikey' => '77ac63f823cd4a6d891562102dec49bb',
            //     'ip' => $request->ip() // Use client's real IP
            // ]);
            // $geoData = $response->json();
            // Insert login history into the database
            // DB::table('login_history')->insert([
            //     'email' => $user->email,
            //     'ip' => $request->ip(),
            //     'country' =>NULL,
            //     'action' => 'login',
            //     'created_date_js' => Carbon::now(),
            //     'status' => 1
            // ]);
			
            addIpLog('login', $credentials);
            // Redirect to the dashboard
            return redirect()->route('dashboard');
        }
        // If login fails, redirect back with error message
        return back()->with('error', 'Your login details are invalid or your email is not verified.');
    }

    public function verify2FaAndLogin(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        $code = $request->input('code');
        $email = session('customer_2fa_pending');
        $user = User::where('email', $email)->where('email_confirmed', 1)->first();

        if (!$user || empty($user->mfa_secret)) {
            session()->forget('customer_2fa_pending');
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Session expired. Please log in again.']);
            }
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        $ga = new GoogleAuthenticator();
        $secret = base64_decode($user->mfa_secret);
        $valid = $ga->verifyCode($secret, $code, 1);

        if (!$valid) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
            }
            return back()->with('error', 'Invalid or expired code.');
        }

        session()->forget('customer_2fa_pending');
        Auth::login($user);
        $request->session()->regenerate();
        $_SESSION['clogin'] = $user->email;
        $_SESSION['user'] = $user->toArray();
        session(['user' => $user, 'clogin' => $user->email]);
        addIpLog('login', ['email' => $user->email]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'redirect' => route('dashboard')]);
        }
        return redirect()->route('dashboard');
    }

    // Logout user
    public function logout(Request $request)
    {
		addIpLog('logout', session('clogin'));
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();
        session()->forget(['user', 'clogin']);
        unset($_SESSION['clogin']);
        unset($_SESSION['user']);
        return redirect('/login');
    }
    public function forgot_password()
    {
        return view('auth.forgot-password');
    }
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'txtemail' => 'required|email',
        ]);
        $email = $request->input('txtemail');
        $user = User::where('email', $email)->first();
        if ($user) {
            $code = md5(uniqid(rand()));
            User::where('email', $email)->update(['emailToken' => $code]);
            $settings = settings();
            $from = $settings['email_from_address'];
            $emailSubject = $settings['admin_title'] . ' Password Reset';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
            $content =
                '<div>Welcome to ' . htmlspecialchars($settings['admin_title'], ENT_QUOTES, 'UTF-8') . '!</div>' .
                '<div>We received a request to reset your password. If you made this request, click the link below to reset your password. If you did not request a password reset, you can ignore this email.
        </div>';
            $id = $user['id'];
            $templateVars = [
                'name' => $user['fullname'],
                'site_link' => $settings['copyright_site_name_text'] . "/reset-password?id=$id&code=$code",
                'btn_text' => "Reset Password",
                'email' => $settings['email_from_address'],
                "content" => $content,
                "title_right" => "Reset",
                "subtitle_right" => "Your Password"
            ];
            $this->mailService->sendEmail($email, $emailSubject, $headers, '', $templateVars);
			addIpLog('Reset Password Link', $email);
            return redirect()->back()->with('success', "We have sent an email to $email. Please click on the password reset link in the email to generate a new password.");
        } else {
            return redirect()->back()->with('error', "Sorry! This email was not found.");
        }
    }
    public function resetPassword(Request $request)
    {
        $id = $request->query('id');
        $code = $request->query('code');
        // Check user exists
        $user = User::where('id', $id)->where('emailToken', $code)->first();
        if ($user) {
            if ($request->isMethod('post')) {
                // Validate
                $request->validate([
                    'password' => 'required|string|confirmed'
                ]);
                $password = $request->input('password');
                DB::table('aspnetusers')
                    ->where('email', $user->email)
                    ->update(['password' => $password]);
                // Send the email notification
                $this->sendPasswordResetSuccessEmail($user);
				addIpLog('Reset Password Update', $user->email);
                return redirect()->route('login')->with('status', 'Password has been reset successfully. You can now login.');
            }
            return view('auth.reset-password', ['user' => $user]); // Return view
        } else {
            return redirect()->route('login')->with('error', 'No account found with the given ID and token.');
        }
    }

    protected function sendPasswordResetSuccessEmail($user)
    {
        $settings = settings();
        $from = $settings['email_from_address'];
        $toEmail = $user->email;
        $emailSubject = $settings['admin_title'] . ' - Client Portal Password Reset Success!';
        $htmlContent = "";
        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // Additional headers
        $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
        $content =
            '<div>Welcome to ' . htmlspecialchars($settings['admin_title'], ENT_QUOTES, 'UTF-8') . '!</div>' .
            '<div>You have successfully reset your password. Thank you for being with us.</div>';
        // Send email
        $templateVars = [
            'name' => $user->fullname,
            'site_link' => $settings['copyright_site_name_text'],
            'btn_text' => "Login",
            'email' => $settings['email_from_address'],
            "content" => $content,
            "title_right" => "Password Reset",
            "subtitle_right" => "Successful"
        ];

        $datalogs = [
             'name' => $user->fullname,
            'site_link' => $settings['copyright_site_name_text'],
            'btn_text' => "Login",
            'email' => $settings['email_from_address'],
            "content" => $content,
            "title_right" => "Password Reset",
            "subtitle_right" => "Successful"
        ];
        addIpLog('send Password Reset Success Email', $datalogs);
        $this->mailService->sendEmail($toEmail, $emailSubject, $headers, '', $templateVars);

    }
    public function register($params = null)
    {
        $queryParams = request()->all();
        $countries = Country::all();
        $user_groups = UserGroup::where("is_visible", 1)->get();
        return view('auth.register', compact('countries', 'user_groups', 'queryParams', 'params'));
    }
    public function addUser(Request $request, $group_code = null)
    {
		$blockedDomains = [
			'yopmail.com',
			'guerrillamail.com',
			'10minutemail.com',
			'tempmail.com',
		];

		Validator::extend('not_disposable', function ($attribute, $value) use ($blockedDomains) {
			if (!str_contains($value, '@')) {
				return false;
			}
			$domain = strtolower(trim(substr(strrchr($value, '@'), 1)));
			return !in_array($domain, $blockedDomains);
		});
		
		/*Input Validation Server side*/
        $validated = $request->validate([
			'fullname' => [
				'required',
				'string',
				'max:100',
				'regex:/^[a-zA-Z\s.\'-]+$/'
			],
			'email' => [
				'required',
				'email:rfc,dns',
				'max:255',
				'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
				'unique:aspnetusers,username',
				'not_disposable'
			],
			'password' => 'required|min:8|confirmed',
			'telephone' => 'required|digits_between:6,15',
			'country' => 'required|string|max:100',
			'country_code' => 'required|string|max:10',
			'group_id' => 'required|integer'
		]);
		
		/*Captcha Verify*/
		$verifyCaptcha = $this->verifyCaptcha($request);
		if (!isset($verifyCaptcha['success']) || $verifyCaptcha['success'] !== true) {
			return back()->withErrors([
				'error' => $verifyCaptcha['message'] ?? 'Captcha verification failed'
			])->withInput();
		}
		
		$group_id = $validated['group_id'];
		$ibEncoded = [];
		
		if ($request->filled('refercode')) {
			$decodedEmail = base64_decode($request->query('refercode'));
			$refUser = DB::table('aspnetusers')->where('email', $decodedEmail)->first();

			if ($refUser) {
				for ($i = 1; $i <= 15; $i++) {
					$field = $i === 1 ? 'email' : 'ib' . ($i - 1);
					$ibEncoded['ib' . $i] = $refUser->$field ?? null;
				}
				$group_id = $refUser->group_id;
			}
		}
		
		if ($group_code) {
			$group = DB::table('user_groups')->where('group_code', $group_code)->first();
			if (!$group) {
				return back()->withErrors(['error' => 'Invalid group code'])->withInput();
			}
			$group_id = $group->user_group_id;
		}
		
		$emailToken = md5(uniqid(rand(), true));
        $datalogs = [
            'email'         => strtolower($validated['email']),
			'username'      => strtolower($validated['email']),
			'fullname'      => trim(strip_tags($validated['fullname'])),
			'password'      => $validated['password'],
			'password_hash' => Hash::make($validated['password']),
			'country_code'  => trim(strip_tags($validated['country_code'])),
			'number'        => trim(strip_tags($validated['telephone'])),
			'country'       => trim(strip_tags($validated['country'])),
			'group_id'      => $group_id,
			'referral'      => '',
			'emailToken'    => $emailToken,
			'request_ip'    => $request->ip(),
			'created_at'    => now(),
			'updated_at'    => now()
        ];

		$insertData = [
			'email'         => strtolower($validated['email']),
			'username'      => strtolower($validated['email']),
			'fullname'      => trim(strip_tags($validated['fullname'])),
			'password'      => $validated['password'],
			'password_hash' => Hash::make($validated['password']),
			'country_code'  => trim(strip_tags($validated['country_code'])),
			'number'        => trim(strip_tags($validated['telephone'])),
			'country'       => trim(strip_tags($validated['country'])),
			'group_id'      => $group_id,
			'referral'      => '',
			'emailToken'    => $emailToken,
			'request_ip'    => $request->ip(),
			'created_at'    => now(),
			'updated_at'    => now()
		];
		
		$insertData = array_merge($insertData, $ibEncoded);
		$lastInsertId = DB::table('aspnetusers')->insertGetId($insertData);

		if (!$lastInsertId) {
			return back()->withErrors([
				'error' => 'Registration failed. Please try again.'
			])->withInput();
		}
		
		$uid = 10000 + $lastInsertId;
		DB::table('aspnetusers')
			->where('id', $lastInsertId)
			->update(['uid' => $uid]);
		
		$settings = settings();
		$from = $settings['email_from_address'];
		$toEmail = $validated['email'];
		$emailSubject = $settings['admin_title'] . ' - Email Address Verfication';
		$htmlContent = "";
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
		$content =
			'<div>Welcome to ' . htmlspecialchars($settings['admin_title'], ENT_QUOTES, 'UTF-8') . '!</div>' .
			'<div>You are receiving this email because you have registered for a Trading Account.</div>' .
			'<div>Click the link below to activate your Trading Account</div>';
		$templateVars = [
			'name' => Str::before($request->email, '@'),
			'server_name' => $settings['mt5_company_name'],
			'site_link' => $settings['copyright_site_name_text'] . "/email_verify?id=$lastInsertId&code=$emailToken",
			'email' => $settings['email_from_address'],
			"content" => '<div>Welcome to ' . htmlspecialchars($settings['admin_title'], ENT_QUOTES, 'UTF-8') . '!</div>'
						. '<div>You are receiving this email because you have registered for a Trading Account.</div>'
						. '<div>Click the link below to activate your Trading Account</div>',
			"title_right" => "Activate",
			"subtitle_right" => "Your Account"
		];
		$this->mailService->sendEmail($toEmail, $emailSubject, $headers, '', $templateVars);
         addIpLog('Register In Client Page', $datalogs);
		return redirect()->route('register')->with('status', 'We have sent a verification email to ' . $toEmail . '. Please confirm to activate your account.');
    }
	
    public function verifyEmail(Request $request)
    {
        $settings = settings();
        $id = $request->query('id');
        $code = $request->query('code');
        $user = User::where('id', $id)
            ->where('emailToken', $code)
            ->first();
        if ($user) {
            if ($user->status == 0) {
                $user->status = 1;
                $user->email_confirmed = 1;
                $user->save();
                $from = $settings['email_from_address'];
                $emailSubject = $settings['admin_title'] . ' - Thank You for Confirming Your Email Address';
                $htmlContent = "";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
                $content =
                    '<div>Welcome to ' . htmlspecialchars($settings['admin_title'], ENT_QUOTES, 'UTF-8') . '!</div>' .
                    '<div>Your email address has been successfully confirmed, and you’re all set to start exploring everything we have to offer.</div>' .
                    '<div><b>Here are your login credentials:</b></div>
          <div><b>Username: </b>' . $user->email . '</div>
          <div><b>Password: </b>' . $user->password . '</div>';
          $datalogs = [
            'name' => $user->fullname,
                    'server_name' => $settings['mt5_company_name'],
                    'site_link' => $settings['copyright_site_name_text'] . "/login",
                    'email' => $settings['email_from_address'],
                    "content" => $content,
                    "title_right" => "Email Verification",
                    "subtitle_right" => "Successful",
                    "btn_text" => "Login"
          ];
                $templateVars = [
                    'name' => $user->fullname,
                    'server_name' => $settings['mt5_company_name'],
                    'site_link' => $settings['copyright_site_name_text'] . "/login",
                    'email' => $settings['email_from_address'],
                    "content" => $content,
                    "title_right" => "Email Verification",
                    "subtitle_right" => "Successful",
                    "btn_text" => "Login"
                ];
                $this->mailService->sendEmail($user->email, $emailSubject, $headers, '', $templateVars);
                 addIpLog('Register verifyEmail', $datalogs);
                return redirect()->route('login')->with('status', 'Your Account has been Activated.');
            } else {
                return redirect()->route('login')->with('error', 'Sorry! Your Account is already Activated');
            }
        } else {
            return redirect()->route('register')->with('error', 'Sorry! No Account Found. Signup here');
        }
    }
    public function clientProfile($clientId)
    {
        $user = User::whereRaw('md5(email) = ?', [$clientId])->where('email_confirmed', 1)->first();
        if ($user) {
            Auth::logout();
            session()->forget(['user', 'clogin']);
            unset($_SESSION['clogin']);
            unset($_SESSION['user']);

            Auth::login($user);
            $_SESSION['clogin'] = $user->email;
            $_SESSION['user'] = $user->toArray();
            session(['user' => $user, 'clogin' => $user->email]);
            return redirect()->route('dashboard');
        } else {
            return "";
        }
    }
    public function verifyCaptcha(Request $request)
    {
        $secretKey = settings()['hcaptcha_secret_key'];
        $token = $request->input('h-captcha-response');
        $verifyUrl = "https://api.hcaptcha.com/siteverify";
        if (!$token) {
            return ['success' => false, 'message' => 'Captcha token missing.'];
        }
        $response = Http::asForm()->post($verifyUrl, [
            'secret' => $secretKey,
            'response' => $token,
        ]);
        $responseBody = $response->json();
        if (isset($responseBody['success']) && $responseBody['success']) {
            return ['success' => true, 'message' => 'Captcha verified successfully.'];
        } else {
            return [
                'success' => false,
                'message' => 'Captcha verification failed.',
                'error-codes' => $responseBody['error-codes'] ?? null,
            ];
        }
    }

}
