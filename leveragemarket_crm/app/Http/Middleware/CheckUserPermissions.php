<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        $userRoleID = session('userData')['role_id'];
        if ($userRoleID == 1) {
            return $next($request);
        }
        $requestUri = $request->path();
        $rolePermissions = DB::table('permissions as p')
            ->leftJoin('pages as pg', 'p.page_id', '=', 'pg.page_id')
            ->where('p.role_id', $userRoleID)
            ->pluck('pg.filename')
            ->toArray();
        // dd($rolePermissions);
        $part_req = explode("/", $requestUri);
        $part_req = array_slice($part_req, 0, 2);
        $part_req = implode("/", $part_req);
        $part_req = '/'.$part_req;
        // print_r($part_req);
        if ((!in_array($requestUri, $rolePermissions) && !in_array($part_req, $rolePermissions) && !in_array('/'.$requestUri, $rolePermissions)) && $userRoleID != 2) {
            // dd($requestUri);
            return response()->view('errors.401', [], 401);
        }
        return $next($request);
    }
}
