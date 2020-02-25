<?php

namespace App\Http\Middleware;

use Closure;

class CustomCKFinderAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        config(['ckfinder.authentication' => function() use ($request) {
            if (\Auth::check()) {
                return true;
            } else {
                config(['ckfinder.authentication' => function() use ($request) {
                    return false;
                }] );
            }
        }] );

        return $next($request);
    }
}
