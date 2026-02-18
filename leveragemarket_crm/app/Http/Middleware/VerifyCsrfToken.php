<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;
use Illuminate\Http\Request;
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
    ];
    public function handle($request, Closure $next)
    {
        $bypassIps = ['40.89.154.129', '52.143.160.251', '51.83.255.235','91.73.60.155'];
        if ($request->is('cryptopayment-response')) {
            if (in_array($request->ip(), $bypassIps)) {
                return $next($request);
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'message' => "Your request is being blocked due to not being in the whitelist.",
                        'reference' => json_encode($request->all())
                    ]
                );
            }
        }
        return $next($request);
    }

}
