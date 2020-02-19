<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('authenticate.login',['apiKey' => 'bd629ce2de47436e3a9cdd2673e97b17','callback' => $request->fullUrl()]);
            // return route('goadmin.login');
        }
    }
}
