<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use Vectorface\GoogleAuthenticator;

class MFAController extends Controller
{
    public function index(Request $request)
    {
        return redirect('/admin/ui_settings#two-factor');
    }

    public function getQrCode()
    {
        if (!session()->has('alogin')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $settings = settings();
        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeUrl("Admin", $secret, $settings["admin_title"]);
        EmployeeList::where("email", session("alogin"))->update([
            "mfa_secret" => base64_encode($secret)
        ]);

        addIpLog('Admin MFA QR Code Attempt',  $qrCodeUrl);
        return response()->json(['qrCodeUrl' => $qrCodeUrl]);
    }

    public function verify(Request $request)
    {
        if (!session()->has('alogin')) {
            return "sessionOut";
        }
        if ($request->ajax()) {
            $ga = new GoogleAuthenticator();
            $admin = EmployeeList::where("email", session("alogin"))->first();
            $checkResult = $ga->verifyCode(base64_decode($admin->mfa_secret), $request->code, 0.5);

            EmployeeList::where("email", session("alogin"))->update([
                "mfa_enable" => 1
            ]);

            return "true";
        } else {
            return "Invalid Request";
        }
    }

    public function disable()
    {
        if (!session()->has('alogin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        EmployeeList::where("email", session("alogin"))->update(['mfa_enable' => 0]);
           $datalogs = [
        'action'     => 'Admin MFA Disabled',
        'status'     => 'success',
        'admin_id'   => session('alogin'),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'timestamp'  => now(),
    ];
    addIpLog('Admin MFA Disabled', $datalogs);
        return response()->json(['success' => true]);
    }

    public function reenable(Request $request)
    {
        if (!session()->has('alogin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $request->validate(['code' => 'required|string|size:6']);
        $admin = EmployeeList::where("email", session("alogin"))->first();
        if (!$admin || empty($admin->mfa_secret)) {
            return response()->json(['success' => false, 'message' => 'No existing 2FA setup found.']);
        }
        $ga = new GoogleAuthenticator();
        $valid = $ga->verifyCode(base64_decode($admin->mfa_secret), $request->code, 1);
        if (!$valid) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
        }
        EmployeeList::where("email", session("alogin"))->update(['mfa_enable' => 1]);
         $datalogs = [
            'action'     => 'Admin MFA Reenable Attempt',
            'status'     => 'invalid_code',
            'admin_id'   => session('alogin'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp'  => now(),
        ];
        addIpLog('Admin MFA Reenable Attempt', $datalogs);
        return response()->json(['success' => true]);
    }
}
