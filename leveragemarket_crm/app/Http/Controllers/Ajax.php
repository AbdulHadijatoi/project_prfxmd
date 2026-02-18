<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeList;
use Illuminate\Support\Facades\Session;
use App\Services\MailService as MailService;

class Ajax extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    
    public function getOtp(Request $request)
    {
        $settings = settings();
        $otp_type = $request->input('type');

        if ($otp_type == 'Admin_Verification') {
            if (!Session::has('admin_otp_pending')) {
                return response()->json(['success' => false, 'message' => 'No Active Session']);
            }
            $email = session('admin_otp_pending');
            $pendingUser = session('admin_pending_user');
            $name = 'Staff';
            if ($pendingUser) {
                if (is_object($pendingUser)) {
                    $name = $pendingUser->fullname ?? $pendingUser->name ?? $pendingUser->email ?? $name;
                } else {
                    $name = $pendingUser['fullname'] ?? $pendingUser['name'] ?? $pendingUser['email'] ?? $name;
                }
            }
        } else {
            if (!Session::has('clogin')) {
                return response()->json(['success' => false, 'message' => 'No Active Session']);
            }
            $email = session('clogin');
            $name = ucfirst(Session::get('user')->fullname);
        }

        $otp = rand(100000, 999999);
        $action = $request->input('action');
        $type = str_replace(' ', '_', trim($request->input('type'))) . "_Otp";
        $email_type = str_replace('_', ' ', $request->input('type'));
        $emailSubject = $settings['admin_title'] . ' - OTP Verification';
        if ($otp_type == 'Wallet_Creation') {
            $email_type = 'Wallet Creation';
        } elseif ($otp_type == 'Bank_Details') {
            $email_type = 'Adding Bank Details';
        } elseif ($otp_type == 'Wallet_Update') {
            $email_type = 'Wallet Updation';
        } elseif ($otp_type == 'Bank_Delete') {
            $email_type = 'Deleting Bank Details';
        } elseif ($otp_type == 'Admin_Verification') {
            $email_type = 'Admin Verification';
        } else {
            $email_type = "Withdrawal (" . $email_type . ") ";
        }
        $content = "<p>Your OTP for  " . $email_type . " is <b>$otp</b></p><p>If you did not request this, please reset your password or raise a support ticket .</p>";
        if ($otp_type == 'Admin_Verification') {
            EmployeeList::where('email', $email)->update([
                'admin_login_otp' => $otp,
                'admin_login_otp_created_at' => time(),
            ]);
        } else {
            Session::put($type, $otp);
        }
        $datalogs = [ 'name' => $name,
            'email' => $settings['email_from_address'],
            "content" => $content,
            "title_right" => "",
            "subtitle_right" => "",
            "img_hidden" => true,];
        $templateVars = [
            'name' => $name,
            'email' => $settings['email_from_address'],
            "content" => $content,
            "title_right" => "",
            "subtitle_right" => "",
            "img_hidden" => true,
        ];
        addIpLog('getOtp', $datalogs);
        $mailSent = $this->mailService->sendEmail($email, $emailSubject, '', 'emails.otp', $templateVars);
        if ($mailSent) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Please try again later or contact support.']);
        }
    }
}
