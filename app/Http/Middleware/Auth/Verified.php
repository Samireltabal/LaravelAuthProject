<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;

class Verified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(\Auth::guard($guard)->user()->email_verified_at) {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'not verified'
            ], 401);
        }
    }
}
