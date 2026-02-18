<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientBankDetails;
use Illuminate\Http\Request;
use DB;
use App\Models\ClientWallets;

class ClientResourceController extends Controller
{
    //
    public function kycHistory()
    {
        return view('admin.kyc_history');
    }
    public function bankDetails()
    {
        return view('admin.bank_details');
    }
    public function walletDetails()
    {
        return view('admin.wallet_details');
    }
    public function kycDetails(Request $request)
    {
        $userId = $request->id;
        $user = DB::table('kyc_update as kyc')
            ->selectRaw('
        kyc.*,
        MAX(registered_date_js) as date,
        GROUP_CONCAT(DISTINCT kyc.kyc_type) as kyc_type,
        GROUP_CONCAT(CONCAT(kyc.kyc_type, "=", kyc.Status) SEPARATOR "#") as summary,
        kyc.email as email,
        SUM(kyc.Status) as status,
        aspnetusers.fullname,
        MD5(kyc.email) as enc_id
    ')
            ->leftJoin('aspnetusers', 'aspnetusers.email', '=', 'kyc.email')
            ->where(DB::raw('MD5(kyc.email)'), '=', $userId)
            ->groupBy('kyc.email')
            ->first();
        $details_all = DB::table('kyc_update as kyc')
            ->where(DB::raw('MD5(kyc.email)'), '=', $userId)
            ->orderBy('Status', 'asc')
            ->get();
        $details_all = DB::table('kyc_update as kyc')
            ->where(DB::raw('MD5(kyc.email)'), '=', $userId)
            ->orderBy('Status', 'asc')
            ->get();
            if ($user) {

    $datalogs = [
        'action'        => 'KYC Details Viewed',
        'user_email'    => $user->email,
        'enc_id'        => $user->enc_id,
        'kyc_types'     => $user->kyc_type,
        'kyc_status_sum'=> $user->status,
        'viewed_by'     => session('alogin'),
        'ip_address'    => $request->ip(),
        'user_agent'    => $request->userAgent(),
        'timestamp'     => now(),
    ];

    addIpLog('View KYC Details in Admin', $datalogs);
}
        return view('admin.kyc_details', compact('user', 'details_all'));
    }
    public function viewBankDetails(Request $request)
    {
        $id = request()->get('id');
        $details = ClientBankDetails::whereRaw('MD5(id) = ?', [$id])->first();
         if ($details) {

        $datalogs = [
            'action'      => 'Bank Details Viewed Admin',
            'bank_id'     => $details->id,
            'client_id'   => $details->client_id ?? null,
            'bank_name'   => $details->bank_name ?? null,
            'viewed_by'   => session('alogin'),
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'timestamp'   => now(),
        ];

        addIpLog('View Bank Details in Admin', $datalogs);
    }

        return view('admin.view_bank_details',compact('details'));
    }
    public function viewWalletDetails(Request $request)
    {
        $id = request()->get('id');
        $details = ClientWallets::whereRaw('MD5(client_wallet_id) = ?', [$id])->first();
         if ($details) {

        $datalogs = [
            'action'      => 'Wallet Details Viewed Admin',
            'wallet_id'   => $details->client_wallet_id,
            'client_id'   => $details->client_id ?? null,
            'wallet_type' => $details->wallet_type ?? null,
            'viewed_by'   => session('alogin'),
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'timestamp'   => now(),
        ];

        addIpLog('View Wallet Details in Admin', $datalogs);
    }
        return view('admin.view_wallet_details',compact('details'));
    }

}
