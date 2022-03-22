<?php

namespace App\Http\Middleware;

use Closure;

class CheckToken
{
    const SERVER_CALL_TOKEN = "AAAAYhOtjQY:APA91bGdGxnRsUf21tbZ4KHguRfVPybbw5urjpXEOTrnpMkiUiWGmCy_QDduYwc1uk-40GcZFmUhyDSxErOY1OiXiIlSbBqLfHlKcrXnrrty6DSWBjhRwsVLZjWt0EAUJ0BjPj7IHhNQ";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->token;

        if (strlen($token) == 0) {
            return response()->json(['status' => false, 'message' => 'Token missing']);
        }

//        $param = [
//            'username' => 'text',
//            'email' => 'email'
//        ];
//        $validator = validate_fails($request, $param);
//        if (!empty($validator)) {
//            return response()->json(['status' => false, 'message' => 'Định dạng dữ liệu không hợp lệ']);
//        }

        if ($token != self::SERVER_CALL_TOKEN) {
            return response()->json(['status' => false, 'message' => 'Token mismatch']);
        }

        return $next($request);
    }
}
