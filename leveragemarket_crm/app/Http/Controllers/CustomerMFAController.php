<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Vectorface\GoogleAuthenticator;

class CustomerMFAController extends Controller
{
    public function getQrCode()
    {
        $email = session('clogin') ?? auth()->user()?->email;
        if (!$email) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $settings = settings();
        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeUrl('Customer', $secret, $settings['admin_title'] ?? 'Customer Portal');
        User::where('email', $email)->update([
            'mfa_secret' => base64_encode($secret),
        ]);
        return response()->json(['qrCodeUrl' => $qrCodeUrl]);
    }

    public function verify(Request $request)
    {
        $email = session('clogin') ?? auth()->user()?->email;
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Invalid Request'], 400);
        }
        $user = User::where('email', $email)->first();
        if (!$user || empty($user->mfa_secret)) {
            return response()->json(['success' => false, 'message' => 'No 2FA setup found.']);
        }
        $ga = new GoogleAuthenticator();
        $valid = $ga->verifyCode(base64_decode($user->mfa_secret), $request->code, 0.5);
        if (!$valid) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
        }
        User::where('email', $email)->update(['mfa_enable' => 1]);
        return response()->json(['success' => true]);
    }

    public function disable()
    {
        $email = session('clogin') ?? auth()->user()?->email;
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        User::where('email', $email)->update(['mfa_enable' => 0]);
        return response()->json(['success' => true]);
    }

    public function reenable(Request $request)
    {
        $email = session('clogin') ?? auth()->user()?->email;
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $request->validate(['code' => 'required|string|size:6']);
        $user = User::where('email', $email)->first();
        if (!$user || empty($user->mfa_secret)) {
            return response()->json(['success' => false, 'message' => 'No existing 2FA setup found.']);
        }
        $ga = new GoogleAuthenticator();
        $valid = $ga->verifyCode(base64_decode($user->mfa_secret), $request->code, 1);
        if (!$valid) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
        }
        User::where('email', $email)->update(['mfa_enable' => 1]);
        return response()->json(['success' => true]);
    }
}
