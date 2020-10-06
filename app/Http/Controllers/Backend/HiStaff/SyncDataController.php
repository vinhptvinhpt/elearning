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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Validator;

class SyncDataController
{
    //api sync cctc voi histaff
    public function apiSaveOrganization(Request $request)
    {
        $result = new ResultModel();
        try {
            $header = $request->header('X-App-Token');

            $json = $request->input('data');

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

            $json = $request->input('data');

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

            $org_check = TmsOrganization::where('code', $data['organization_code'])->first();

            if (empty($org_check)) {
                $result->code = 'US05';
                $result->status = false;
                $result->message = 'Organization is not exists';
                return response()->json($result);
            }

            $inputRole = array(
                array(
                    "id" => 5,
                    "mdl_role_id" => 5
                ),
                array(
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
