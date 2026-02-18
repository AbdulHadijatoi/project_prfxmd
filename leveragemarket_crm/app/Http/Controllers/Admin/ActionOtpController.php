<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionOtp;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ActionOtpController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function requestActionOtp(Request $request)
    {
        if (!Session::has('userID')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'module' => 'required|string',
            'action' => 'required|string',
        ]);

        $module = $request->input('module');
        $action = $request->input('action');

        $allowedModules = [ActionOtp::MODULE_USERGROUP];
        $allowedActions = [ActionOtp::ACTION_VIEW, ActionOtp::ACTION_UPDATE];
        if (!in_array($module, $allowedModules) || !in_array($action, $allowedActions)) {
            return response()->json(['success' => false, 'message' => 'Invalid module or action']);
        }

        $clientIndex = Session::get('userID');
        $otp = (string) rand(100000, 999999);

        ActionOtp::where('client_index', $clientIndex)
            ->where('module', $module)
            ->where('action', $action)
            ->delete();
$datalogs = [
     'client_index' => $clientIndex,
            'module' => $module,
            'action' => $action,
            'otp_code' => $otp,
];
        ActionOtp::create([
            'client_index' => $clientIndex,
            'module' => $module,
            'action' => $action,
            'otp_code' => $otp,
        ]);

        $settings = settings();
        $email = Session::get('alogin');
        $userData = Session::get('userData', []);
        $name = is_array($userData) ? ($userData['fullname'] ?? $userData['username'] ?? 'Staff') : 'Staff';
        $emailSubject = $settings['admin_title'] . ' - OTP Verification';
        $actionLabel = $action === ActionOtp::ACTION_UPDATE ? 'Update User Group' : 'Edit User Group';
        $content = "<p>Your OTP for " . $actionLabel . " is <b>{$otp}</b></p><p>If you did not request this, please contact support.</p>";

        $templateVars = [
            'name' => $name,
            'email' => $settings['email_from_address'],
            'content' => $content,
            'title_right' => '',
            'subtitle_right' => '',
            'img_hidden' => true,
        ];
 addIpLog('Request Action Otp ', $datalogs);
        $mailSent = $this->mailService->sendEmail($email, $emailSubject, '', 'emails.otp', $templateVars);

        if ($mailSent) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Please try again later or contact support.']);
    }

    public function verifyActionOtp(Request $request)
    {
        if (!Session::has('userID')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'module' => 'required|string',
            'action' => 'required|string',
            'otp' => 'required|string',
        ]);

        $module = $request->input('module');
        $action = $request->input('action');
        $otp = $request->input('otp');
$datalogs = [
          $module = $request->input('module'),
        $action = $request->input('action'),
        $otp = $request->input('otp'),
];
        $record = ActionOtp::where('client_index', Session::get('userID'))
            ->where('module', $module)
            ->where('action', $action)
            ->orderBy('id', 'desc')
            ->first();

        if (!$record || $record->isExpired() || (string) $record->otp_code !== (string) $otp) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP']);
        }

        ActionOtp::where('client_index', Session::get('userID'))
            ->where('module', $module)
            ->where('action', $action)
            ->delete();
 addIpLog('Verify Action Otp ', $datalogs);
        return response()->json(['success' => true]);
    }
}
