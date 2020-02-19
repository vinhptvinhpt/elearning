<?php

namespace App\Http\Middleware;

use Closure;

class Localization
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

        if ($request->session()->has('lang') ) {
            $locale = $request->session()->get('lang');
            app()->setLocale($locale);

        }

        if($request->segment(1) == 'en') {
            $locale = $request->segment(1);
            $request->session()->put('lang', $locale);
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
