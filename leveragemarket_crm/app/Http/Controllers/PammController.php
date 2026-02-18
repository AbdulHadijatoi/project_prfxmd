<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiveAccount;

class PammController extends Controller
{
    public function investments(){
         $user = auth()->user();	
         $email =	auth()->user();
        $liveaccounts = LiveAccount::where('email', session('clogin'))->get();
addIpLog('View investments', $email);
        return view('pamm.investments',compact('user','liveaccounts'));
    }
    public function investment_list(){
         $user = auth()->user();
    	
         $email =	auth()->user();
         addIpLog('investment_list', $email);
        return view('pamm.investment_list',[],compact('user'));
    }
}
