<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CourseSendMail;
use App\Mail\EmailDeviationGrading;
use App\MdlUser;
use App\StudentCertificate;
use App\TmsNotification;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Mockery\Exception;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showFormlogin()
    {
        return redirect('/');
        //return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $apiKey = $request->input('apiKey');
        $callback = $request->input('callback');
        return view('auth.index', ['api_key' => $apiKey, 'callback' => $callback]);
    }

    public function authenticateToken(Request $request)
    {
        $AuthenticationOperation = [];
        $token = $request->input('jwt');

        $param = [
            'jwt' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            Auth::logout();
            return response()->json();
        }


        $user = MdlUser::with('detail')->where('token', $token)->first();
        if (!$user) {
            return response()->json(['status' => 'FAIL']);
        }
        $AuthenticationOperation['token'] = $token;
        $AuthenticationOperation['fullname'] = $user['detail']['fullname'];
        $AuthenticationOperation['eIdentifier'] = $user['username'];
        $AuthenticationOperation['email'] = $user['detail']['email'];
        $AuthenticationOperation['status'] = 'SUCCESS';
        return response()->json($AuthenticationOperation);
    }

    public function checklogin(Request $request)
    {
        $token = $request->input('jwt');

        $param = [
            'jwt' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            Auth::logout();
            return response()->json(['status' => 'LOGOUT']);
        }

        //$callback = $request->input('callback');
        if (!$token) {
            Auth::logout();
            return response()->json(['status' => 'LOGOUT']);
        }
        $user = MdlUser::with('detail')->where('token', $token)->first();
        if (!$user) {
            Auth::logout();
            return response()->json(['status' => 'LOGOUT']);
        }
        if (isset(Auth::user()->id)) {
            if (Auth::user()->id == $user['id']) {
                return response()->json(['status' => 'LOGIN']);
            } else {
                Auth::logout();
                Auth::login($user);
                return response()->json(['status' => 'LOGIN']);
            }

        }
        Auth::login($user);
        return response()->json(['status' => 'LOGIN']);
    }

    public function login(Request $request)
    {
        try {
            $username = $request->input('username');
            $password = $request->input('password');
            $remember = $request->input('remember');
            $code = $request->input('code');
            $status = $request->input('status');
            $order = $request->input('order');
            $confirm_time = $request->input('confirm_time');

            $param = [
                'username' => 'code',
                //'password'      => 'password',
                'remember' => 'number',
                'status' => 'number',
                'order' => 'number',
                'confirm_time' => 'date'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(['status' => $validator]);
            }

            $checkCertificate = StudentCertificate::where('code', $username)->first();
            if (isset($checkCertificate)) {
                $checkUser = MdlUser::where('id', $checkCertificate->userid)->first();
            } else {
                $checkUser = MdlUser::where(DB::raw('LOWER(`username`)'), strtolower($username))->first();
            }

            if (!$checkUser) {
                return response()->json(['status' => 'FAILUSER']);
            }

            if ($checkUser->deleted == 1) {
                return response()->json(['status' => 'FAILBANNED']);
            }
            if (!password_verify($password, $checkUser->password)) {
                return response()->json(['status' => 'FAILPASSWORD']);
            }

            // grab credentials from the request
            $credentials = $request->only('username', 'password');
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => 'invalid_credentials'], 401);
            }

            $token = compact('token');
            $checkUser->token = $token['token'];
            $checkUser->save();

            Auth::login($checkUser, $remember);

            $response['username'] = $checkUser->username;
            $response['avatar'] = Auth::user()->detail['avatar'];
            $response['jwt'] = $token['token'];
            $response['status'] = 'SUCCESS';
            // [VinhPT]
            // Get description for user check
            $response['redirect_type'] = $checkUser->redirect_type;

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

    public function logout(Request $request)
    {
        try {
            $token = $request->input('token');
            if ($token) {
                JWTAuth::invalidate($token);
            }
            Auth::logout();
        } catch (\Exception $e) {
            return response()->json(['status' => 'FAILED']);
        }

        return response()->json(['status' => 'SUCCESS']);
    }


    /**
     * @param Request $request
     * @return false|string
     */
    public function reset(Request $request)
    {
        try {
            $username = $request->input('username');
            $email = $request->input('email');

            $param = [
                'username' => 'text',
                'email' => 'email'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(['status' => false, 'message' => 'Định dạng dữ liệu không hợp lệ']);
            }

            $checkUser = MdlUser::select('id', 'deleted')->where([
                'username' => $username,
                'email' => $email
            ])->first();

            if (!$checkUser) {
                return response()->json(['status' => false, 'message' => 'Tài khoản hoặc Email chưa chính xác']);
            }

            if ($checkUser->deleted == 1) {
                return response()->json(['status' => false, 'message' => 'Tài khoản đang bị khóa, vui lòng liên hệ với quản trị viên để mở khóa tài khoản']);
            }

            $normal_chars = range('a', 'z');
            $special_chars = array('!', '@', '$', '#', '%', '?', '&');
            $uppercase_chars = range('A', 'Z');
            $number_chars = range(0, 9);

            shuffle($normal_chars);
            $password_arr = array_slice($normal_chars, 0, 8);

            $password_arr[0] = $special_chars[array_rand($special_chars, 1)];
            $password_arr[1] = $uppercase_chars[array_rand($uppercase_chars, 1)];
            $password_arr[2] = $number_chars[array_rand($number_chars, 1)];

            shuffle($password_arr);
            $password = implode("", $password_arr);

            // Begin Transaction
            DB::beginTransaction();

            try {

                MdlUser::findOrFail($checkUser['id'])->update([
                    'password' => bcrypt($password)
                ]);

//            Mail::send('email.recover_password', [
//                'username' => $username,
//                'password' => $password
//            ], function ($message) use ($email) {
//                $message->to($email)->subject('Recover Password');
//            });

                //sendmail to user for get new password
                Mail::to($email)->send(new CourseSendMail(
                    TmsNotification::FORGOT_PASSWORD,
                    $username,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $password
                ));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => false, 'message' => 'Reset mật khẩu thất bại do lỗi hệ thống, vui lòng thử lại sau']);
            }
            return response()->json(['status' => true, 'message' => 'Mật khẩu mới đã được gửi về email của bạn. Vui lòng kiểm tra email để nhận mật khẩu']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function loginSSO(Request $request)
    {
        try {
            $username = $request->input('username');

            $param = [
                'username' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(['status' => 'FAIL']);
            }

            if (!$username)
                return response()->json(['status' => 'FAIL']);

            $checkUser = MdlUser::where('username', $username)->first();

            $token = createJWT($username, 'data');
            $checkUser->token = $token;
            $checkUser->save();
            Auth::login($checkUser);
            $response['jwt'] = $token;
            $response['status'] = 'SUCCESS';
            // [VinhPT]
            // Get description for user check
            $response['redirect_type'] = $checkUser->redirect_type;
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

    public function updatePassword()
    {
        try {
            $lstData = MdlUser::select('id')->where('id', '>', 5)->pluck('id');

            $count_data = count($lstData);
            $pass = '123456789';
            if ($count_data > 0) {
                \DB::beginTransaction();

                \DB::table('mdl_user')->whereIn('id', $lstData)->update(['password' => bcrypt($pass)]);

                \DB::commit();
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()]);
        }
    }


}
