<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\EmployeeList;

class SettingsController extends Controller
{
    public function index()
    {
        $admin = null;
        $adminMfaEnabled = false;
        $adminHasMfaSecret = false;
        if (session()->has('alogin')) {
            $admin = EmployeeList::where('email', session('alogin'))->first();
            if ($admin) {
                $adminMfaEnabled = !empty($admin->mfa_enable);
                $adminHasMfaSecret = !empty($admin->mfa_secret);
            }
        }
        return view("admin.ui_settings", compact('adminMfaEnabled', 'adminHasMfaSecret'));
    }

    public function store(Request $request)
    {

        // echo'<pre>';print_r('yes');exit;
        $req = $request->except(["_token", "update"]);
        foreach ($request->file() as $key => $file) {
            if ($request->hasFile($key)) {
                $file = $request->file($key); // Retrieve the uploaded file from the request
                $filename = time() . '_' . $file->getClientOriginalName(); // Retrieve the original filename
                Storage::disk('local')->put('public/files/' . $filename, file_get_contents($file));
                $file_path = "storage/files/" . $filename;
                $req[$key] = $file_path;
            }
        }

        foreach ($req as $key => $value) {
            Setting::updateOrCreate(
               
                ['name' => $key],
                ['value' => $value]
            );
        }
      
        alert()->success("Settings Successfully Updated");
        return redirect()->back();
    }
    
    public function update_password()
    {
        return view("admin.update_password");
    }
    public function store_password(Request $request)
    {
        $request->validate([
            'oldpassword' => 'required',
            'newpassword' => 'required|confirmed',
        ]);
        $user = EmployeeList::where('email', session('alogin'))->first();
        if ($request->oldpassword!=$user->password){
            return redirect()->back()->with('error','Old password you entered is invalid');
        }
        $user->password = $request->newpassword;
        $user->save();

        $datalogs = [
              $user->password = $request->newpassword,
                $user->oldpassword = $request->oldpassword,
        ];
      
        addIpLog('update admin password', $datalogs);

        return redirect()->back()->with('success','Password Updated Successfully');
    }

}
