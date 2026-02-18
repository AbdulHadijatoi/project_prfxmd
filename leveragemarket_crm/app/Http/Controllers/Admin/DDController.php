<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DDController extends Controller
{
    public function users(Request $request){
        // dd();
        $userGroups = json_decode( session("userData")["user_group_id"]);
        $users = User::whereIn("group_id",$userGroups);
        if($request->term){
            $users = $users->whereAny([
                'email',
                'fullname'
            ], 'like', '%' . $request->term . '%');
        }
       $datalogs = [
        'action'        => 'User List Viewed',
        'search_term'   => $request->term ?? null,
        'group_filter'  => $userGroups,
        
        'total_results' => $users->count(),
        'viewed_by'     => session('alogin'),
        'ip_address'    => $request->ip(),
        'user_agent'    => $request->userAgent(),
        'timestamp'     => now(),
    ];

    addIpLog('View Users in Admin', $datalogs);
        $users = $users->get()->toArray();
        return $users;
    }
}
