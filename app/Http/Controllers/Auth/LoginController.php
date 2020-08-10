<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CourseSendMail;
use App\MdlUser;
use App\Role;
use App\StudentCertificate;
use App\TmsNotification;
use App\TmsUserDetail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
                //'username' => 'email',
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

            //Cuonghq
            //Check role and update redirect type
            $sru = DB::table('model_has_roles as mhr')
                ->join('roles', 'roles.id', '=', 'mhr.role_id')
                ->leftJoin('permission_slug_role as psr', 'psr.role_id', '=', 'mhr.role_id')
                ->join('mdl_user as mu', 'mu.id', '=', 'mhr.model_id')
                ->where('mhr.model_id', $checkUser->id)
                ->where('mhr.model_type', 'App/MdlUser')
                ->get();

            $current_redirect_type = $checkUser->redirect_type;

            $redirect_type = 'lms';
            if (count($sru) != 0) {
                foreach ($sru as $role) {
                    if (!in_array($role->name, [Role::STUDENT, Role::ROLE_EMPLOYEE])) {
                        $redirect_type = "default";
                        break;
                    }
//                    if (in_array($role->name, [
//                            Role::ROLE_MANAGER,
//                            Role::ROLE_LEADER,
//                            Role::ROOT,
//                            Role::ADMIN,
//                            Role::TEACHER
//                        ])) {
//                        $redirect_type = "default";
//                        break;
//                    }
                }
            }

            $updated = false;
            //Update redirect type
            if ($redirect_type != $current_redirect_type) {
                $checkUser->redirect_type = $redirect_type;
                $updated = true;
            }

            // [VinhPT]
            // Get description for user check
            $response['redirect_type'] = $redirect_type;

            if (empty($checkUser->token)) {
                $token = compact('token');
                $checkUser->token = $token['token'];
                $updated = true;
            }

            if ($updated) { //Luu thong tin moi
                $checkUser->save();
            }

            $token = $checkUser->token;

            Auth::login($checkUser, $remember);

            $response['id'] = $checkUser->id;
            $response['username'] = $checkUser->username;
            $response['avatar'] = Auth::user()->detail['avatar'];
            $response['fullname'] = Auth::user()->detail['fullname'];
            $response['domain'] = Config::get('constants.domain.TMS');
            $response['jwt'] = $token;
            $response['status'] = 'SUCCESS';

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
        $token = $request->input('token');
        try {
            if ($token) {
                JWTAuth::invalidate($token);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'FAILED']);
        }
        Auth::logout();
        return response()->json(['status' => 'SUCCESS']);
    }


    /**
     * @param Request $request
     * @return false|string
     */
    public function reset(Request $request)
    {
        try {
            $email = $request->input('username');

            $param = [
                'username' => 'email'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(['status' => false, 'message' => 'Invalid data format']);
            }

            $checkUser = MdlUser::select('id', 'deleted')->where([
                'username' => $email
            ])->first();

            if (!$checkUser) {
                return response()->json(['status' => false, 'message' => 'Email incorrect']);
            }

            if ($checkUser->deleted == 1) {
                return response()->json(['status' => false, 'message' => 'The account is locked, please contact the administrator to open lock up
                    account']);
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

                //sendmail to user for get new password
                Mail::to($email)->send(new CourseSendMail(
                    TmsNotification::FORGOT_PASSWORD,
                    $email,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $password
                ));
				
				// check for failures
    if (Mail::failures()) {	
        // return response showing failed emails
		 return response()->json(['status' => false, 'message' => 'Password reset failed due to a system error, please try again later1']);
    }

                DB::commit();

            } catch (\Exception $e) {

                DB::rollback();
                return response()->json(['status' => false, 'message' => 'Password reset failed due to a system error, please try again later']);
            }
            return response()->json(['status' => true, 'message' => 'New password has been sent to your email. Please check your email for password']);
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

            if ($checkUser) {

                //Cuonghq
                //Check role and update redirect type
                $sru = DB::table('model_has_roles as mhr')
                    ->join('roles', 'roles.id', '=', 'mhr.role_id')
                    ->leftJoin('permission_slug_role as psr', 'psr.role_id', '=', 'mhr.role_id')
                    ->join('mdl_user as mu', 'mu.id', '=', 'mhr.model_id')
                    ->where('mhr.model_id', $checkUser->id)
                    ->where('mhr.model_type', 'App/MdlUser')
                    ->get();

                $current_redirect_type = $checkUser->redirect_type;

                $redirect_type = 'lms';
                if (count($sru) != 0) {
                    foreach ($sru as $role) {
                        if (!in_array($role->name, [Role::STUDENT, Role::ROLE_EMPLOYEE])) {
                            $redirect_type = "default";
                            break;
                        }
//                    if (in_array($role->name, [
//                            Role::ROLE_MANAGER,
//                            Role::ROLE_LEADER,
//                            Role::ROOT,
//                            Role::ADMIN,
//                            Role::TEACHER
//                        ])) {
//                        $redirect_type = "default";
//                        break;
//                    }
                    }
                }

                $updated = false;
                //Update redirect type
                if ($redirect_type != $current_redirect_type) {
                    $checkUser->redirect_type = $redirect_type;
                    $updated = true;
                }

                // [VinhPT]
                // Get description for user check
                $response['redirect_type'] = $redirect_type;

                if (empty($checkUser->token)) {
                    $token = compact('token');
                    $checkUser->token = $token['token'];
                    $updated = true;
                }

                if ($updated) { //Luu thong tin moi
                    $checkUser->save();
                }

                $token = $checkUser->token;


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

            }

//            $token = createJWT($username, 'data');
//            $checkUser->token = $token;
//            $checkUser->save();
//
//            $response['jwt'] = $token;
//            $response['status'] = 'SUCCESS';
//            // [VinhPT]
//            // Get description for user check
//            $response['redirect_type'] = $checkUser->redirect_type;
//            //encrypt for login lms
//            $app_name = Config::get('constants.domain.APP_NAME');
//
//            $key_app = encrypt_key($app_name);
//
//            $data_lms = array(
//                'user_id' => $checkUser->id,
//                'app_key' => $key_app
//            );
//
//            $data_lms = createJWT($data_lms, 'data');
//
//            $response['data'] = $data_lms;
//            return response()->json($response);
            return response()->json(['status' => 'FAIL']);

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
