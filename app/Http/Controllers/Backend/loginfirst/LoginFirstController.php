<?php

// [VinhPT] Login first
namespace App\Http\Controllers\Backend\loginfirst;

// Import
use App\Http\Controllers\Controller;
use Mockery\Exception;
use Illuminate\Http\Request;
use App\MdlUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class LoginFirstController extends Controller
{
    public function viewLoginFirst()
    {
        return view('loginfirst.index');
    }

    public function executeLogin(Request $request)
    {
        try {
            $remember = true;
            // Check exist user
            $checkUser = MdlUser::where('id', 6)->first();
            if (!$checkUser) {
                return response()->json(['status' => 'FAILUSER']);
            }

            $token = createJWT($checkUser->username, 'data');
            $checkUser->token = $token;
            $checkUser->save();

            Auth::login($checkUser, $remember);

            $response['jwt'] = $token;
            $response['status'] = 'SUCCESS';
            $response['url'] = '/lms/loginfirst.php';

            //encrypt for login lms
            $app_name = Config::get('constants.domain.APP_NAME');

            $key_app = encrypt_key($app_name);

            $data_lms = array(
                'user_id' => $checkUser->id,
                'app_key' => $key_app
            );

            $data_lms = createJWT($data_lms, 'data');

            $response['data'] = $data_lms;

            return response()->json($response);

        } catch (Exception $e) {
            return response()->json(['status' => 'FAIL']);
        }
    }
}
