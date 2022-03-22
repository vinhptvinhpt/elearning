<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        '/sso/authentoken',
        '/bgtlogout',
        '/sso/checklogin',
        '/bgtresetpassword',
        '/bgtdoadmin',
        '/loginfirst/executelogin',
        '/elearning/v1/sync-organization',
        '/elearning/v1/sync-user',
        '/elearning/v1/test-user',
        '/api/protect/azure'
    ];
}
