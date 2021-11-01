<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard)
    {
        if(\Auth::guard($guard)->user()) {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'not authenticated'
            ], 401);
        }
    }
}
