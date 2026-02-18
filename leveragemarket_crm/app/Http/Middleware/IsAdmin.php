<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // dd("Checking...",session("alogin"),Session::all());
        if (!session('alogin')) {
            // return response()->view('admin.login')->with('msg','Please Login To Proceed');
            return redirect('/admin/login')->with('msg','Please Login To Proceed');
        }
        return $next($request);
    }
}
