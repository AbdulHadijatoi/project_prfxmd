<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KycUpdate;

class Kyc extends Controller
{
    public function updateKyc(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'email' => 'required|email',
            'description' => 'required|string',
            'status' => 'required|string',
        ]);
        
        $kyc = KycUpdate::find($validatedData['id']);
        if (!$kyc) {
            return response()->json(['message' => 'KYC record not found'], 404);
        }
        $kyc->Admin_Remark = $validatedData['description'];
        $kyc->Status = $validatedData['status'];
        $kyc->approved_by = session('userData')['client_index'];
 
        $kyc->save();
       // ✅ Proper Data Logs
    $datalogs = [
        'action'        => 'KYC Status Updated',
        'kyc_id'        => $kyc->id,
        'user_email'    => $kyc->email,
  
        'new_status'    => $kyc->Status,
     
        'new_remark'    => $kyc->Admin_Remark,
        'approved_by'   => $kyc->approved_by,
        'ip_address'    => $request->ip(),
        'user_agent'    => $request->userAgent(),
        'timestamp'     => now(),
    ];

    addIpLog('KYC Status Update in Admin', $datalogs);
        return redirect()->back()->with("success","KYC Status Updated Successfully");
    }
}
