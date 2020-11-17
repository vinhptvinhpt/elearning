<?php


namespace App\Http\Controllers\Backend\HiStaff;


use App\MdlRoleAssignments;
use App\MdlUser;
use App\ModelHasRole;
use App\Role;
use App\TmsOrganization;
use App\TmsOrganizationEmployee;
use App\TmsUserDetail;
use App\ViewModel\ResultModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JWTAuth;
use Mockery\Exception;

class SyncDataController
{
    public function index(Request $request)
    {
        $username = $request->input('username');
        $token = $request->input('token');
        return view('histaff.index', ['username' => $username, 'token' => $token]);
    }

    public function login(Request $request)
    {
        try {
            $username = $request->input('username');
            $token_histaff = $request->input('token_histaff');
            $password = $request->input('password');

            //xac thuc token voi histaff
            $url = Config::get('constants.domain.HISTAFF-API') . 'CheckToken';

            $result_api = callAPIHiStaff('POST', $url, $username, $token_histaff, Config::get('constants.domain.HISTAFF-KEY'));
            $result_api = json_decode($result_api, true);

            if ($result_api['Code'] != '200') {
                return response()->json(['status' => 'FAILTOKEN']);
            }


            $checkUser = MdlUser::where(DB::raw('LOWER(`username`)'), strtolower($username))->first();


            if (!$checkUser) {
                return response()->json(['status' => 'FAILUSER']);
            }

            if ($checkUser->active == 0) {
                return response()->json(['status' => 'FAILBANNED']);
            }

            if (strpos($username, 'admin') !== false) {
            } else {
                //lấy url hiện tại
                $host = Config::get('constants.domain.TMS');

                //lấy ra mã tổ chức
                $query_cctc = "(SELECT f.id, f.level, f.code
            FROM (SELECT @id AS _id, (SELECT @id := parent_id FROM tms_organization WHERE id = _id)
            FROM (SELECT @id := (select toe.organization_id from tms_organization_employee toe join mdl_user mu on mu.id = toe.user_id
            where mu.username= '" . $username . "')) tmp1
            JOIN tms_organization ON @id IS NOT NULL) tmp2
            JOIN tms_organization f ON tmp2._id = f.id
            where f.level = 2 or f.level = 1 limit 1)";


                $query_cctc = DB::raw($query_cctc);

                $results = DB::select($query_cctc);
                $rs = "";
                foreach ($results as $rs) {
                    $rs = $rs->code;
                    break;
                };

                //Nếu có tên - thuộc tổ chức
                $partOfUrl = "phh";
                if ($rs) {
                    if (Str::contains(strtolower($rs), ['ea']) == 1) {
                        $partOfUrl = "easia";
                    } else if (Str::contains(strtolower($rs), ['bg']) == 1) {
                        $partOfUrl = "begodi";
                    } else if (Str::contains(strtolower($rs), ['av']) == 1) {
                        $partOfUrl = "avana";
                    } else if (Str::contains(strtolower($rs), ['ev']) == 1) {
                        $partOfUrl = "exotic";
                    }
                }


                if (strpos(strtolower($host), $partOfUrl) !== false || Str::contains(strtolower($host), ['localhost']) == 1 || Str::contains(strtolower($host), ['dev']) == 1) {

                } else {
                    return response()->json(['status' => 'FAILORGANIZATION']);
                }
            }


            // grab credentials from the request
            $credentials = $request->only('username', 'password');
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => 'invalid_credentials'], 401);
            }

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
                }
            }

            $updated = false;
            //Update redirect type
            if ($redirect_type != $current_redirect_type) {
                $checkUser->redirect_type = $redirect_type;
                $updated = true;
            }

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

            Auth::login($checkUser, 1);

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

    //api sync cctc voi histaff
    public function apiSaveOrganization(Request $request)
    {
        $result = new ResultModel();
        try {
            $header = $request->header('X-App-Token');
            $token = $request->header('Authorization');

            $json = $request->input('data');
            $username = $request->input('username');

            if (empty($header)) {
                $result->code = 'ERR01';
                $result->status = false;
                $result->message = 'Application token not found';
                return response()->json($result);
            }

            if ($header != Config::get('constants.domain.APP-TOKEN-SYSTEM')) {
                $result->code = 'ERR02';
                $result->status = false;
                $result->message = 'Application token invalid';
                return response()->json($result);
            }

            if (empty($json)) {
                $result->code = 'ERR03';
                $result->status = false;
                $result->message = 'Data value is empty';
                return response()->json($result);
            }


            if (empty($token)) {
                $result->code = 'ERR05';
                $result->status = false;
                $result->message = 'User token not found';
                return response()->json($result);
            }

            //xac thuc token voi histaff
            $token = str_replace('', 'Bearer ', $token);

            $url = Config::get('constants.domain.HISTAFF-API') . 'CheckToken';

            $result_api = callAPIHiStaff('POST', $url, $username, $token, Config::get('constants.domain.HISTAFF-KEY'));
            $result_api = json_decode($result_api, true);

            if ($result_api['Code'] != '200') {
                $result->code = 'ERR06';
                $result->status = false;
                $result->message = $result_api['Msg'];
                return response()->json($result);
            }


            ////// xu ly flow insert vao elearning
            $data = json_decode($json, true);

            if (empty($data['code']) || empty($data['name'])) {
                $result->code = 'ORG02';
                $result->status = false;
                $result->message = 'Code or name is empty';
                return response()->json($result);
            }

            $parent_id = 0;
            $level = 0;

            if (!empty($data['parent_code'])) {
                $org_parent = TmsOrganization::where('code', $data['parent_code'])->first();

                if ($org_parent) {
                    $parent_id = $org_parent->id;
                    $level = $org_parent->level;
                }
            }

            $org_check = TmsOrganization::where('code', $data['code'])->first();


            if ($org_check) {
                $org_check->code = $data['code'];
                $org_check->name = $data['name'];
                $org_check->description = $data['description'];
                $org_check->parent_id = $parent_id;
                $org_check->level = $level + 1;

                $org_check->save();

                $result->message = 'Success update organization';
            } else {

                TmsOrganization::firstOrCreate([
                    'code' => $data['code'],
                    'parent_id' => $parent_id
                ],
                    [
                        'name' => $data['name'],
                        'description' => $data['description'],
                        'level' => $level + 1,
                        'enabled' => 1
                    ]);

                $result->message = 'Success insert organization';
            }

            $result->code = 'ORG01';
            $result->status = true;

        } catch (\Exception $e) {
            $result->status = false;
            $result->code = $e->getCode();
            $result->message = 'ERR04: ' . $e->getMessage();
        }
        return response()->json($result);
    }


    //api sync user voi histaff
    public function apiSaveUser(Request $request)
    {
        $result = new ResultModel();
        try {
            $header = $request->header('X-App-Token');

            $token = $request->header('Authorization');

            $json = $request->input('data');
            $username = $request->input('username');

            if (!$header) {
                $result->code = 'ERR01';
                $result->status = false;
                $result->message = 'Application token not found';
                return response()->json($result);
            }

            if ($header != Config::get('constants.domain.APP-TOKEN-SYSTEM')) {
                $result->code = 'ERR02';
                $result->status = false;
                $result->message = 'Application token invalid';
                return response()->json($result);
            }

            if (empty($json)) {
                $result->code = 'ERR03';
                $result->status = false;
                $result->message = 'Data value is empty';
                return response()->json($result);
            }

            if (empty($token)) {
                $result->code = 'ERR05';
                $result->status = false;
                $result->message = 'User token not found';
                return response()->json($result);
            }

            if (!$json) {
                $result->code = 'ERR03';
                $result->status = false;
                $result->message = 'Data value is empty';
                return response()->json($result);
            }

            $data = json_decode($json, true);

            if (empty($data['username']) || empty($data['password']) || empty($data['email']) || empty($data['fullname'])) {
                $result->code = 'US02';
                $result->status = false;
                $result->message = 'username or password or email or fullname is empty';
                return response()->json($result);
            }


            $validator = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

            if (!$validator) {
                $result->code = 'US03';
                $result->status = false;
                $result->message = 'The email format is invalid';
                return response()->json($result);
            }


            if (empty($data['organization_code'])) {
                $result->code = 'US04';
                $result->status = false;
                $result->message = 'organization_code is empty';
                return response()->json($result);
            }

            //xac thuc token voi histaff
            $token = str_replace('', 'Bearer ', $token);

            $url = Config::get('constants.domain.HISTAFF-API') . 'CheckToken';

            $result_api = callAPIHiStaff('POST', $url, $username, $token, Config::get('constants.domain.HISTAFF-KEY'));
            $result_api = json_decode($result_api, true);

            if ($result_api['Code'] != '200') {
                $result->code = 'ERR06';
                $result->status = false;
                $result->message = $result_api['Msg'];
                return response()->json($result);
            }

            ////// xu ly flow insert vao elearning
            //region flow insert or update data vao elearning
            $org_check = TmsOrganization::where('code', $data['organization_code'])->first();

            if (empty($org_check)) {
                $result->code = 'US05';
                $result->status = false;
                $result->message = 'Organization is not exists';
                return response()->json($result);
            }

            //hard code, default user sync tu histaff co quyen student va excutive
            $inputRole = array(
                array(//student
                    "id" => 5,
                    "mdl_role_id" => 5
                ),
                array(//excutive
                    "id" => 29,
                    "mdl_role_id" => 80
                )
            );

            DB::beginTransaction();

            $convert_name = convert_name($data['fullname']);

            $user = MdlUser::where('username', $data['username'])->first();

            if ($user) {
                //update user in mdl_user
                $user->username = $data['username'];
                $user->firstname = $convert_name['firstname'];
                $user->lastname = $convert_name['lastname'];
                $user->email = $data['email'];
                $user->password = bcrypt($data['password']);
                $user->save();

                //update user in tms_user_detail
                $tms_user = TmsUserDetail::where('user_id', $user->id)->first();
                $tms_user->cmtnd = $data['cmtnd'];
                $tms_user->fullname = $data['fullname'];
                $tms_user->email = $data['email'];
                $tms_user->phone = $data['phone'];
                $tms_user->address = $data['address'];
                $tms_user->city = $data['office'];
                $tms_user->country = 'vi';

                $tms_user->sex = $data['sex'] ? 1 : 0;
                $tms_user->working_status = $data['working_status'] ? 0 : 1;

                $tms_user->save();

                $result->message = 'Success update user';

            } else {
                $user = MdlUser::firstOrCreate([
                    'username' => $data['username'],
                    'email' => $data['email'],
                ],
                    [
                        'firstname' => $convert_name['firstname'],
                        'lastname' => $convert_name['lastname'],
                        'password' => bcrypt($data['password']),
                        'confirmed' => 1,
                        'address' => $data['address'],
                        'city' => $data['office'],
                        'country' => 'vi',
                        'active' => 1,
                        'redirect_type' => 'lms'
                    ]);

                //add user to organization
                TmsOrganizationEmployee::firstOrCreate([
                    'user_id' => $user->id,
                    'organization_id' => $org_check->id,
                    'position' => Role::ROLE_EMPLOYEE,
                    'enabled' => 1
                ]);

                //update role for user
                $arr_data = [];
                $data_item = [];

                $arr_data_enrol = [];
                $data_item_enrol = [];

                foreach ($inputRole as $role) {

                    $data_item['role_id'] = $role["id"];
                    $data_item['model_id'] = $user->id;
                    $data_item['model_type'] = 'App/MdlUser';
                    array_push($arr_data, $data_item);

                    bulk_enrol_lms($user->id, $role['mdl_role_id'], $arr_data_enrol, $data_item_enrol);
                }

                ModelHasRole::insert($arr_data);
                MdlRoleAssignments::insert($arr_data_enrol);

                //update info to tms_user_detail
                TmsUserDetail::firstOrCreate([
                    'user_id' => $user->id,
                ],
                    [
                        'fullname' => $data['fullname'],
                        'cmtnd' => $data['cmtnd'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'address' => $data['address'],
                        'city' => $data['office'],
                        'country' => 'vi',
                        'sex' => $data['sex'] ? 1 : 0,
                        'working_status' => $data['working_status'] ? 0 : 1,
                        'avatar' => '/userfiles/files/avatar.png'
                    ]);

                $result->message = 'Success insert user';

            }
            DB::commit();
            //endregion

            $result->code = 'US01';
            $result->status = true;

        } catch (\Exception $e) {
            DB::rollBack();
            $result->status = false;
            $result->code = 'ERR04';
            $result->message = $e->getMessage();
        }
        return response()->json($result);
    }
}
