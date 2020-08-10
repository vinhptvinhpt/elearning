<?php

namespace App\Http\Controllers\Api;

use App\CourseCompletion;
use App\CourseFinal;
use App\Exports\ImportResultSheet;
use App\Exports\ListMismatchData;
use App\Http\Controllers\Controller;
use App\Imports\DataImport;
use App\MdlEnrol;
use App\MdlGradeGrade;
use App\MdlGradeItem;
use App\MdlRole;
use App\MdlRoleAssignments;
use App\MdlUser;
use App\MdlUserEnrolments;
use App\ModelHasRole;
use App\Role;
use App\StudentCertificate;
use App\TmsBranch;
use App\TmsBranchMaster;
use App\TmsBranchSaleRoom;
use App\TmsCity;
use App\TmsCityBranch;
use App\TmsDepartments;
use App\TmsDevice;
use App\TmsInvitation;
use App\TmsLog;
use App\TmsNotification;
use App\TmsNotificationLog;
use App\TmsOrganization;
use App\TmsOrganizationEmployee;
use App\TmsRoleCourse;
use App\TmsRoleOrganization;
use App\TmsRoleOrganize;
use App\TmsSaleRooms;
use App\TmsSaleRoomUser;
use App\TmsSurveyUser;
use App\TmsSurveyUserView;
use App\TmsTrainningCategory;
use App\TmsTrainningUser;
use App\TmsUserDetail;
use App\TmsUserSaleDetail;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\Request;

set_time_limit(0);

class BackgroundController extends Controller
{
    public function test()
    {
//        path to files folder contain excel files
//        $dir = storage_path() . DIRECTORY_SEPARATOR . "import";
//        //return files or folders in directory above
//        $files = scandir($dir);
//        $files = array_diff($files, array('.', '..'));
//        return response()->json($files);
    }

    public function importEmployee(Request $request)
    {Log::info('71');
        set_time_limit(0);

        $env = "background";

        $manager_keys = array(
            'manager',
            'officer',
            'director',
            'controller',
            'chief'
        );

        $base_level_orgs = array(
            'EA' => 'Easia Travel',
            'EV' => 'Exotic Voyages',
            'BG' => 'Begodi',
            'AV' => 'Avana',
            'TVE' => 'TVE'
        );

        $countries = TmsUserDetail::country;

        $from = $request->input('from');

        if (strlen($from) != 0) { //Import from cms
            $env = 'cms';
            if (!$request->file('file')) {
                return response()->json(self::status_message('error', "File missing"));
            } else {
                //check file is xlsx, xls
                $extension = $request->file('file')->getClientOriginalExtension();
                if ($extension != 'xls' && $extension != 'xlsx') {
                    return response()->json(self::status_message('error', "File type mismatch. Allow XLS, XLSX file only"));
                }
                $files = array($request->file('file'));
            }
        } else { //background scan folder import
            $dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "import";
            //return files or folders in directory above
            $files = scandir($dir);
            $files = array_diff($files, array('.', '..'));
            $files = array_slice($files, 0, 1);
        }
Log::info('107');

        foreach ($files as $file_path) {

            if ($env == 'background') {
                //check file is xlsx, xls
                $extension = pathinfo($file_path, PATHINFO_EXTENSION);
                if ($extension != 'xls' && $extension != 'xlsx') {
                    return response()->json([
                        'extension' => 'error'
                    ]);
                }
                $file_path = "import" . DIRECTORY_SEPARATOR . $file_path;
                $file_name = pathinfo($file_path, PATHINFO_FILENAME);
            } else {
                /* @var $file_path UploadedFile */
                $full_file_name = $file_path->getClientOriginalName();
                $file_name = pathinfo($full_file_name, PATHINFO_FILENAME);
            }

            $base_level = 1;

            $list_uploaded = (new DataImport())->toArray($file_path, '', '');

            $response = array();

            //Lấy dữ liệu từ tab Staff List <= Required
            $list_employee = $list_uploaded['Staff List'];
Log::info('157');
            foreach ($list_employee as $user) {

                $content = array();
                $status = true;
                $base_level_id = 0;

                //Fetch data
                //Skip 2 first row and department name row, check first column is numeric or not
                $stt = $user[0];
                if (!is_numeric($stt) || $stt == 0) {
                    continue;
                }

                $base_level_organization_code = $user[7];

                if (strlen($base_level_organization_code) != 0 && array_key_exists($base_level_organization_code, $base_level_orgs)) {
                    $base_organization = TmsOrganization::firstOrCreate([
                        'code' => $base_level_organization_code,
                        'name' => $base_level_orgs[$base_level_organization_code],
                        'level' => $base_level
                    ]);
                    self::createPQDL($base_organization);
                    $base_level_id = $base_organization->id;
                } else {
                    $content[] = 'Organization mismatch';
                }

                $position_name = $user[8];
                $city =  $user[9]; //office name
                $country = $user[10]; //country name
                if (strlen($country) == 0) {//Set default country vi
                    $country_code = array_search('Vietnam', $countries,true);
                    $country = $country_code;
                } else {
                    $country_code = array_search($country, $countries,true);
                    if ($country_code === false) {
                        $content[] = 'Country name does not exist. Supported countries: Vietnam, Laos, Cambodia, Thailand, Myanmar';
                    } else {
                        $country = $country_code;
                    }
                }


                if (strlen($position_name) == 0) {
                    $content[] = 'Position is missing';
                }

                if (str_replace($manager_keys, '', strtolower($position_name)) != strtolower($position_name)) {
                    $role = Role::ROLE_MANAGER;
                } elseif (strpos(strtolower($position_name), Role::ROLE_LEADER) !== false) {
                    $role = Role::ROLE_LEADER;
                } else {
                    $role = Role::ROLE_EMPLOYEE;
                }

                //Check / create department
                $department_name = $user[5];
                $department_code = $user[6];

                if (strlen($department_name) == 0 || strlen($department_code) == 0) {
                    $content[] = 'Department info is missing';
                } else {
                    if (empty($content) && $base_level_id != 0) {
                        $organization = TmsOrganization::updateOrCreate([
                            'code' => strtoupper($base_level_organization_code . "-" . $department_code), //$department_code,//strtoupper($base_level_organization . "-" . $department_name),
                            'parent_id' => $base_level_id,
                            'level' => $base_level + 1
                        ], [
                            'name' => $department_name,//ucwords($base_level_organization) . "-" . $department_name,
                        ]);
                        self::createPQDL($organization);
                    }
                }

                //Validate required fields
                //email
                $email = trim($user[30]);

                if (strlen($email) == 0) {
                    $content[] = 'Email is missing';
                } else {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        $content[] = "Email is wrong format";
                    }
                }
                //name
                $full_name = trim($user[1]);
                $first_name = trim($user[2]);
                $middle_name = trim($user[3]);
                $last_name = trim($user[4]);

                if (strlen($full_name) == 0) {
                    $content[] = 'Full name is missing';
                }

                if (strlen($first_name) == 0) {
                    $content[] = 'First name is missing';
                }

                if (strlen($last_name) == 0) {
                    $content[] = 'Last name is missing';
                }
                //cmtnd
                $personal_id = $user[22];
                //Skip check missing
//                if (strlen($personal_id) == 0) {
//                    $content[] = 'Personal id is missing';
//                }

                $address = $user[21];
                $phone = self::preparePhoneNo($user[28]);
                $phone2 = self::preparePhoneNo($user[29]);

                $skype = $user[31];

                $gender = $user[16];
                if (strtolower($gender) == 'nam') {
                    $gender = 1;
                } elseif (strtolower($gender) == 'nữ') {
                    $gender = 0;
                } else {
                    $gender = -1;
                }

                $dob = "";
                if (strlen($user[12]) == 0 || strlen($user[13]) == 0 || strlen($user[14]) == 0) {
                    $content[] = 'Dob is missing';
                } else {
                    $dob_date = str_pad($user[12], 2, '0', STR_PAD_LEFT);
                    $dob_month = str_pad($user[13], 2, '0', STR_PAD_LEFT);
                    $dob_year = $user[14];

                    $dob_string = $dob_year . "-" . $dob_month . "-" . $dob_date;
                    $dob = strtotime($dob_string);
                }

                $start_time = "";
                if (strlen($user[18]) == 0 || strlen($user[19]) == 0 || strlen($user[20]) == 0) {
                    $start_date = str_pad($user[18], 2, '0', STR_PAD_LEFT);
                    $start_month = str_pad($user[19], 2, '0', STR_PAD_LEFT);
                    $start_year = $user[20];
                    $start_time_string = $start_year . "-" . $start_month . "-" . $start_date;
                    $start_time = strtotime($start_time_string);
                }

                $response_item = array(
                    $stt,
                    $full_name,
                    '',
                    ''
                );

                $full_name = self::prepareName($full_name);
                $first_name = self::prepareName($first_name);
                $middle_name = self::prepareName($middle_name);
                $last_name = self::prepareName($last_name);

                if (empty($content)) {
                    $createEmployeeResponse = self::createEmployee(
                        $role,
                        $email,
                        $email,
                        $full_name,
                        $first_name,
                        $middle_name,
                        $last_name,
                        $personal_id,
                        $phone,
                        $phone2,
                        $skype,
                        $address,
                        $city,
                        $country,
                        $gender,
                        $dob,
                        $start_time
                    );

                    if ($createEmployeeResponse['id'] != 0) { //tạo user thành công
                        $user_id = $createEmployeeResponse['id'];
                        $employee = self::createOrganizationEmployee($organization->id, $user_id, $role, $position_name);
                        if (!is_numeric($employee)) {
                            $content[] = $employee;
                        }
                    } else {
                        $status = false;
                        $content[] = $createEmployeeResponse['message']; //Tạo thất bại -> log lỗi
                    }
                } else { //validate fail
                    $status = false;
                }

                if ($status ==  false) {
                    $response_item[2] = 'error';
                } else {
                    $response_item[2] = 'success';
                }
                $response_item[3] = implode("\n", $content);

                $response[] = $response_item;
            }
Log::info('337');
            $result_file_name = "bg_import_error_" . $file_name . ".xlsx";

            //xóa file cũ
            if (Storage::exists($result_file_name)) {
                Storage::delete($result_file_name);
            }

            //ghi file vào thư mục storage, không được mở file khi đang lưu nếu k sẽ lỗi k lưu được
            $exportExcel = new ImportResultSheet('Import Result', $response);
            $exportExcel->store($result_file_name, '', Excel::XLSX);
Log::info('348');
            if ($env == 'cms') {
                return response()->json(self::status_message('success', __('nhap_du_lieu_thanh_cong'), ['result_file' => $result_file_name]));
            }
        }
    }

    function createPQDL($organization) {
        if (!$organization->roleOrganization) { //Tạo role nếu chưa có
            $lastRole = MdlRole::latest()->first();
            $checkRole = Role::where('name', $organization->name)->first();
            if ($checkRole) {
                return response()->json(status_message('error', __('quen_da_ton_tai_khong_the_them')));
            }

            //Tạo quyền bên LMS
            $mdlRole = new MdlRole;
            $mdlRole->shortname = $organization->code;
            $mdlRole->description = $organization->name;
            $mdlRole->sortorder = $lastRole['sortorder'] + 1;
            $mdlRole->archetype = 'user';
            $mdlRole->save();

            $role = new Role();
            $role->mdl_role_id = $mdlRole->id;
            $role->name = $organization->code;
            $role->description = $organization->name;
            $role->guard_name = 'web';
            $role->status = 1;
            $role->save();

            $new_role_organization = new TmsRoleOrganization();
            $new_role_organization->organization_id = $organization->id;
            $new_role_organization->role_id = $role->id;
            $new_role_organization->save();
        } else {
            $check = TmsRoleOrganization::where('organization_id', $organization->id)->first();
            if ($check->role) { //Cập nhật role
                $check->role->name = $organization->code;
                $check->role->description = $organization->name;
                $check->role->save();
            }
        }
    }

    function createOrganizationEmployee($organization_id, $user_id, $role, $description) {
        $check = TmsOrganizationEmployee::with('organization')->where('user_id', $user_id)->first();
        if (isset($check)) {
//            if ($check->organization_id != $organization_id) {
//                return __('nhan_vien_da_tham_gia_phong_ban_khac') . ": " . $check->organization->name;
//            }
            //overwrite chức vụ
            $check->position = $role;
            $check->organization_id = $organization_id;
            $check->save();
        } else {
            //tạo mới nếu chưa có
            $check = new TmsOrganizationEmployee();
            $check->user_id = $user_id;
            $check->organization_id = $organization_id;
            $check->position = $role;
            $check->description = $description;
            $check->save();
        }
        return $check->id;
    }

    function prepareName($name) {
        $name = mb_strtolower($name, 'UTF-8');
        return mb_convert_case($name, MB_CASE_TITLE);
    }

    function preparePhoneNo($phone) {
        $plus = '';
        if (strpos($phone, '+') === 0) {
            $plus = '+';
        }
        return $plus . preg_replace("/[^0-9]/", "", $phone);
    }

    public function createEmployee(
        $role,
        $username,
        $email,
        $full_name,
        $first_name,
        $middle_name,
        $last_name,
        $personal_id,
        $phone1,
        $phone2,
        $skype,
        $address,
        $city,
        $country,
        $gender,
        $dob,
        $working_start_at
    ) {
        $check = MdlUser::query()->where('email', $email)->first();

        $response = [
            'id'=> 0,
            'message' => ''
        ];

        if (isset($check)) { //Cập nhật
            try {
                $check->redirect_type = 'lms';
                $check->firstname = $first_name;
                $check->middlename = $middle_name;
                $check->lastname = $last_name;
                $check->email = $email;
                $check->phone1 = $phone1;
                $check->phone2 = $phone2;
                $check->skype = $skype;
                $check->city = $city;
                $check->country = $country;
                $check->save();

                //Xóa detail thừa
                //TmsUserDetail::query()->where('email', $email)->delete();

                //cập nhật thông tin chi tiết user
                $user_detail = TmsUserDetail::query()
                    ->where('email', $email)
                    //->orWhere('cmtnd', $personal_id) //Bỏ check personal_id do trường họp BOM có nhiều account ở các phòng ban khác nhau
                    ->first();
                if (!isset($user_detail)) {
                    $user_detail = new TmsUserDetail();
                    $exist = false;
                } else {
                    $exist = true;
                }

                //check cmtnd
                //if ($user_detail->user_id != $check->id && $exist == true) {
                //    $response['message'] = 'Skip update because Personal ID is used by another user ' . $user_detail->fullname;
                //} else {
                    $user_detail->user_id = $check->id;
                    $user_detail->fullname = $full_name;
                    $user_detail->cmtnd = $personal_id;
                    $user_detail->email = $email;
                    $user_detail->phone = $phone1;
                    $user_detail->address = $address;
                    $user_detail->city = $city;
                    $user_detail->country = $country;
                    $user_detail->sex = $gender;
                    $user_detail->confirm = 0;
                    $user_detail->dob = $dob;
                    $user_detail->working_status = 0;
                    $user_detail->start_time = $working_start_at;
                    $user_detail->save();

                    self::assignRoles($check->id, $role, true);

                    $response['id'] = $check->id;
                    if (strlen($response['message']) == 0) {
                        $response['message'] = 'Update successfully';
                    }
                //}
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
        } else { //Tạo mới
            $check = self::createUserNew(
                $role,
                $username,
                $email,
                $full_name,
                $first_name,
                $middle_name,
                $last_name,
                $personal_id,
                $phone1,
                $phone2,
                $skype,
                $address,
                $city,
                $country,
                $gender,
                $dob,
                $working_start_at
            );

            if (!is_object($check)) { //lỗi
                $response['message'] = $check;
            } else {
                $response['id'] = $check->id;
                $response['message'] = 'Create successfully';
            }
        }

        return $response;
    }

    /**
     * @param $role
     * @param $username
     * @param $email
     * @param $full_name
     * @param $first_name
     * @param $middle_name
     * @param $last_name
     * @param $personal_id
     * @param $phone1
     * @param $phone2
     * @param $skype
     * @param $address
     * @param $gender
     * @param $dob
     * @param $working_start_at
     * @return mixed
     */
    public function createUserNew(
        $role,
        $username,
        $email,
        $full_name,
        $first_name,
        $middle_name,
        $last_name,
        $personal_id,
        $phone1,
        $phone2,
        $skype,
        $address,
        $city,
        $country,
        $gender,
        $dob,
        $working_start_at
    ) {
        try {

            //Check cmtnd
//            $user_detail = TmsUserDetail::query()
//                ->where('cmtnd', $personal_id)
//                ->first();
//            if (isset($user_detail)) {
//                return 'Can not create user because Personal ID is used by another user ' . $user_detail->fullname;
//            }

            $check = new MdlUser;
            $check->username = $username;
            $check->password = bcrypt('123456a@');
            $check->redirect_type = 'lms';
            $check->firstname = $first_name;
            $check->middlename = $middle_name;
            $check->lastname = $last_name;
            $check->email = $email;
            $check->city = $city;
            $check->country = $country;
            if (strlen($phone1) != 0) {
                $check->phone1 = $phone1;
            }
            if (strlen($phone2) != 0) {
                $check->phone2 = $phone2;
            }
            if (strlen($skype) != 0) {
                $check->skype = $skype;
            }
            $check->active = 1;
            $check->save();

            self::assignRoles($check->id, $role);

            $user_detail = new TmsUserDetail;
            $user_detail->cmtnd = $personal_id;
            $user_detail->fullname = $full_name;
            $user_detail->email = $email;
            $user_detail->phone = $phone1;
            $user_detail->address = $address;
            $user_detail->city = $city;
            $user_detail->country = $country;
            $user_detail->sex = $gender;
            $user_detail->confirm = 0;
            $user_detail->dob = $dob;
            $user_detail->working_status = 0;
            $user_detail->start_time = $working_start_at;
            $user_detail->user_id = $check->id;
            $user_detail->save();

        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $check;
    }

    /**
     * @param $user_id
     * @param $role
     * @param bool $update
     */
    function assignRoles($user_id, $role, $update = false) {
        $role = Role::query()->select('id', 'name', 'mdl_role_id')->where('name', $role)->first();
        //Remove old roles when update
        if ($update) {
            $this->clearRole($user_id);
        }
        //Auto thêm quyền học viên nếu chưa có
        self::add_user_by_role($user_id, 5);
        if ($role) {
            //Thêm quyền mới nếu chưa có
            self::add_user_by_role($user_id, $role['id']);
            //self::enrole_lms($check->id, $role['mdl_role_id'], 1);
        }
    }

    function clearRole($user_id) {
        ModelHasRole::where('model_id', $user_id)->delete();
        //remove role of user from table mdl_role_assignments for lms
        MdlRoleAssignments::where('userid', $user_id)->delete();
    }

    function enrole_lms($user_id, $role_id, $confirm)
    {
        $mdl_role = MdlRole::findOrFail($role_id);
        $context_id = 1;
        if ($mdl_role['shortname'] == 'student') {
            /*if ($confirm == 0) {
                $courses = DB::table('mdl_course_categories as cate')
                    ->join('mdl_course as course', 'course.category', '=', 'cate.id')
                    ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'course.id')
                    ->select('course.id as course_id')
                    ->where('cate.id', '=', 3)
                    ->get();
                if ($courses) {
                    foreach ($courses as $course) {
    //                    $enrole = DB::table('mdl_enrol')
    //                        ->where('enrol', '=', 'manual')
    //                        ->where('courseid', '=', $course->course_id)
    //                        ->first();
    //                    if (!$enrole) {

                        $enrole = MdlEnrol::firstOrCreate(

                            [
                                'enrol' => 'manual',
                                'courseid' => $course->course_id,
                                'roleid' => $role_id
                            ],
                            [
                                'sortorder' => 0,
                                'status' => 0,
                                'expirythreshold' => 86400,
                                'timecreated' => strtotime(Carbon::now()),
                                'timemodified' => strtotime(Carbon::now()),
                            ]
                        );

    //                        MdlEnrol::firstOrCreate(
    //                            [
    //                                'enrol' => 'guest',
    //                                'courseid' => $course->course_id,
    //                                'roleid' => $role_id,
    //                                'sortorder' => 1
    //                            ],
    //                            [
    //                                'expirythreshold' => 86400,
    //                                'timecreated' => strtotime(Carbon::now()),
    //                                'timemodified' => strtotime(Carbon::now())
    //                            ]
    //                        );
    //                        MdlEnrol::firstOrCreate(
    //                            [
    //                                'enrol' => 'self',
    //                                'courseid' => $course->course_id,
    //                                'roleid' => $role_id,
    //                                'sortorder' => 2
    //                            ],
    //                            [
    //                                'expirythreshold' => 86400,
    //                                'timecreated' => strtotime(Carbon::now()),
    //                                'timemodified' => strtotime(Carbon::now())
    //                            ]
    //                        );
                        // }
                        $checkEnrole = DB::table('mdl_user_enrolments')
                            ->where('enrolid', '=', $enrole->id)
                            ->where('userid', '=', $user_id)
                            ->first();
                        if (!$checkEnrole) {
                            DB::table('mdl_user_enrolments')->insert([
                                'enrolid' => $enrole->id,
                                'userid' => $user_id
                            ]);
                        }

                        $context = DB::table('mdl_context')
                            ->where('instanceid', '=', $course->course_id)
                            ->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSE)
                            ->first();
                        $context_id = $context ? $context->id : 1;
                        $mdlRoleAssignment = MdlRoleAssignments::where([
                            'roleid' => $role_id,
                            'userid' => $user_id,
                            'contextid' => $context_id
                        ])->first();
                        if (!$mdlRoleAssignment) {
                            $roleAssign = new MdlRoleAssignments;
                            $roleAssign->roleid = $role_id;
                            $roleAssign->userid = $user_id;
                            $roleAssign->contextid = $context_id;
                            $roleAssign->save();
                        }

                        //lay gia trị trong bang mdl_grade_items
                        $mdl_grade_item = MdlGradeItem::where('courseid', $course->course_id)->first();

                        if ($mdl_grade_item) {
                            //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                            MdlGradeGrade::firstOrCreate([
                                'userid' => $user_id,
                                'itemid' => $mdl_grade_item->id
                            ]);
                        }

                        //write log to notifications
                        self::insert_single_notification(\App\TmsNotification::MAIL, $user_id, \App\TmsNotification::ENROL, $course->course_id);
                    }
                }
            } else {
                $mdlRoleAssignment = MdlRoleAssignments::where([
                    'roleid' => $role_id,
                    'userid' => $user_id,
                    'contextid' => $context_id
                ])->first();
                if (!$mdlRoleAssignment) {
                    $roleAssign = new MdlRoleAssignments;
                    $roleAssign->roleid = $role_id;
                    $roleAssign->userid = $user_id;
                    $roleAssign->contextid = $context_id;
                    $roleAssign->save();
                }
            }*/
        } else {
            /*if ($mdl_role['shortname'] == 'manager') {
                $context_id = 1;
            }*/
            $mdlRoleAssignment = MdlRoleAssignments::where([
                'roleid' => $role_id,
                'userid' => $user_id,
                'contextid' => $context_id
            ])->first();
            if (!$mdlRoleAssignment) {
                $roleAssign = new MdlRoleAssignments;
                $roleAssign->roleid = $role_id;
                $roleAssign->userid = $user_id;
                $roleAssign->contextid = $context_id;
                $roleAssign->save();
            }
        }
    }

    function add_user_by_role($user_id, $role_id)
    {
        //Assign TMS
        /*$modelHasRole = ModelHasRole::where([
            'role_id' => $role_id,
            'model_id' => $user_id
        ])->first();
        if (!$modelHasRole) {*/
        ModelHasRole::firstOrCreate([
            'role_id' => $role_id,
            'model_id' => $user_id,
            'model_type' => 'App/MdlUser',
        ]);
        /*$userRole = new ModelHasRole;
        $userRole->role_id = $role_id;
        $userRole->model_id = $user_id;
        $userRole->model_type = 'App/MdlUser';
        $userRole->save();*/
        //}

        //Assign LMS
        // $role = \App\Role::select('name','mdl_role_id')->findOrFail($role_id);
        // if(
        //     $role['name'] != 'coursecreator' &&
        //     $role['name'] != 'editingteacher' &&
        //     $role['name'] != 'teacher' &&
        //     $role['name'] != 'student'
        // ){

        //     $mdlRoleAssignment = MdlRoleAssignments::where([
        //         'roleid' => $role['mdl_role_id'],
        //         'userid' => $user_id,
        //         'contextid' => 1
        //     ])->first();
        //     if (!$mdlRoleAssignment) {
        //         $roleAssign = new MdlRoleAssignments;
        //         $roleAssign->roleid = $role['mdl_role_id'];
        //         $roleAssign->userid = $user_id;
        //         $roleAssign->contextid = 0;
        //         $roleAssign->save();
        //     }
        // }
    }

    function training_enrole($user_id, $category_id = null)
    {
        $role = Role::select('mdl_role_id')->where('name', Role::STUDENT)->first();
        if (!$category_id) {
            $category_id = TmsTrainningUser::with('category')->where('user_id', $user_id)->first();
            $category_id = $category_id['category']['category_id'];
        }
        $courses = DB::table('mdl_course_categories as cate')
            ->join('mdl_course as course', 'course.category', '=', 'cate.id')
            ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'course.id')
            ->select('course.id as course_id')
            ->where('cate.id', '=', $category_id)
            ->get();

        /*
         * Đóng chức năng theo follow hiện tại :
         * Khi đổi khung năng lực thì enrole học viên vào các khóa học thuộc khung năng lực hiện tại
         * Và giữ lại những khóa học đã enrole của khung năng lực trước
         * Thực hiện unenrole bằng tay khóa học k mong muốn
         * */

        //delete enrole
        /*$context_del = DB::table('mdl_context as mc')
            ->select('mc.id')
            ->join('mdl_course as course', 'course.id', '=', 'mc.instanceid')
            ->join('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'course.id')
            ->where('course.category', '=', $category_id)
            ->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSE)
            ->pluck('mc.id');

        if($context_del){
            //Xóa user được assign vào khóa học vs vai trò là học viên
            MdlRoleAssignments::where([
                'roleid'    => $role['mdl_role_id'],
                'userid'    => $user_id
            ])->whereIn('contextid', $context_del)
                ->delete();
        }

        $enrole_del = DB::table('mdl_course_categories as cate')
            ->select('me.id')
            ->join('mdl_course as course', 'course.category', '=', 'cate.id')
            ->join('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'course.id')
            ->join('mdl_enrol as me', 'me.courseid', '=', 'course.id')
            //->where('cate.id', '=', $category_id)
            ->where('me.roleid', '=', $role['mdl_role_id'])
            ->where('me.enrol', '=', 'manual')
            ->pluck('me.id');
        if (count($enrole_del) > 0) {
            //Xóa user được enrole vào các khóa học tự động vs vai trò học viên
            DB::table('mdl_user_enrolments')->where('userid' , $user_id)
                ->whereIn('enrolid', $enrole_del)
                ->delete();
        }*/
        //end delete enrole

        if ($courses) {
            foreach ($courses as $course) {
                $enrole = MdlEnrol::firstOrCreate(
                    [
                        'enrol' => 'manual',
                        'courseid' => $course->course_id,
                        'roleid' => $role['mdl_role_id']
                    ],
                    [
                        'sortorder' => 0,
                        'status' => 0,
                        'expirythreshold' => 86400,
                        'timecreated' => strtotime(Carbon::now()),
                        'timemodified' => strtotime(Carbon::now()),
                    ]
                );
                $checkEnrole = DB::table('mdl_user_enrolments')
                    ->where('enrolid', '=', $enrole->id)
                    ->where('userid', '=', $user_id)
                    ->first();
                if (!$checkEnrole) {
                    DB::table('mdl_user_enrolments')->insert([
                        'enrolid' => $enrole->id,
                        'userid' => $user_id
                    ]);
                }

                $context = DB::table('mdl_context')
                    ->where('instanceid', '=', $course->course_id)
                    ->where('contextlevel', '=', MdlUser::CONTEXT_COURSE)
                    ->first();
                $context_id = $context ? $context->id : 0;
                MdlRoleAssignments::firstOrCreate([
                    'roleid' => $role['mdl_role_id'],
                    'userid' => $user_id,
                    'contextid' => $context_id
                ]);

                //lay gia trị trong bang mdl_grade_items
                $mdl_grade_item = MdlGradeItem::where('courseid', $course->course_id)->first();

                if ($mdl_grade_item) {
                    //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                    MdlGradeGrade::firstOrCreate([
                        'userid' => $user_id,
                        'itemid' => $mdl_grade_item->id
                    ]);
                }

                //write log to notifications
                self::insert_single_notification(TmsNotification::MAIL, $user_id, TmsNotification::ENROL, $course->course_id);
            }
        }
    }

    function insert_single_notification($type, $sendto, $target, $course_id)
    {
        $tms_notif = new TmsNotification();
        $tms_notif->type = $type;
        $tms_notif->sendto = $sendto;
        $tms_notif->target = $target;
        $tms_notif->status_send = TmsNotification::UN_SENT;
        $tms_notif->course_id = $course_id;
        if (!empty(Auth::user())) {
            $tms_notif->createdby = Auth::user()->id;
        }

        $tms_notif->save();

        self::insert_single_notification_log($tms_notif, TmsNotificationLog::CREATE_NOTIF);
    }

    function insert_single_notification_log($tmsNotif, $action)  //action bao gồm create, update, delete lấy trong bảng TmsNotificationLog
    {
        $tms_notifLog = new TmsNotificationLog();
        $tms_notifLog->type = $tmsNotif->type;
        $tms_notifLog->target = $tmsNotif->target;
        $tms_notifLog->content = json_encode($tmsNotif);
        $tms_notifLog->sendto = $tmsNotif->sendto;
        $tms_notifLog->status_send = $tmsNotif->status_send;
        $tms_notifLog->createdby = $tmsNotif->createdby;
        $tms_notifLog->course_id = $tmsNotif->course_id;
        $tms_notifLog->action = $action;
        $tms_notifLog->save();
    }

    function status_message($status, $message, $additional_data = [])
    {
        $data = [];
        $data['status'] = $status;
        $data['message'] = $message;
        $data['data'] = $additional_data;
        return $data;
    }

    //Kiểm tra Account có phải là nhân viên giám sát thị trường
    function has_user_market($user_id = null)
    {
        if (!$user_id)
            $user_id = Auth::id();
        $check = false;
        $sru = DB::table('model_has_roles as mhr')
            ->join('roles', 'roles.id', '=', 'mhr.role_id')
            ->where('roles.name', '=', Role::MANAGE_MARKET)
            ->where('mhr.model_id', '=', $user_id)
            ->count();
        if ($sru > 0)
            $check = true;
        return $check;
    }

    //gán quyền cho chủ đại lý
    function add_role_for_user($user_id)
    {
        $role = Role::where('name', Role::MANAGE_AGENTS)->first();
        $check = ModelHasRole::where([
            'role_id' => $role['id'],
            'model_id' => $user_id
        ])->count();
        if ($check == 0) {
            $mhr = new ModelHasRole;
            $mhr->role_id = $role['id'];
            $mhr->model_id = $user_id;
            $mhr->model_type = 'App/MdlUser';
            $mhr->save();
        }
    }

    //gán quyền cho chủ điểm bán
    function add_managepos_for_user($user_id)
    {
        $role = Role::where('name', Role::MANAGE_POS)->first();
        $check = ModelHasRole::where([
            'role_id' => $role['id'],
            'model_id' => $user_id
        ])->count();
        if ($check == 0) {
            $mhr = new ModelHasRole;
            $mhr->role_id = $role['id'];
            $mhr->model_id = $user_id;
            $mhr->model_type = 'App/MdlUser';
            $mhr->save();
        }
    }

    //Xóa user & các dữ liệu liên quan khỏi hệ thống
    public function removeUsers() {
        $excludes = [
            //system
            'easiaeditor01@gmail.com',
            'easiaeditor02@gmail.com',
            'easiaeditor03@gmail.com',
            'easiaeditor04@gmail.com',
            'easiaeditor05@gmail.com',
            'easiaeditor06@gmail.com',
            'easiaeditor07@gmail.com',
            'easiaeditor08@gmail.com',
            'easiaeditor09@gmail.com',
            'easiaeditor10@gmail.com',
            'easiaeditor11@gmail.com',
            'easiaadmin@gmail.com',
            'easia_editor_03',
            'easia_editor_02',
            'easia_editor_01',
            'easia',
            'thuylinh',
            'linhnt',
            'admin',
            'tvadmin_01@gmail.com',
            'tvtrainee_01@gmail.com',

            //easia
            'thuyhoa@easia-travel.com','vuhuy@easia-travel.com','trancong@phh-group.com','thubac@easia-travel.com','thanhhuong@easia-travel.com','vuchung@easia-travel.com','tuyetthanh@easia-travel.com','Vananh@easia-travel.com','xuanchien@easia-travel.com','Trannga@easia-travel.com','duonglinh@easia-travel.com','bichphuong@easia-travel.com','bichdiep@easia-travel.com','lanhuong@easia-travel.com','hongmai@easia-travel.com','quynhtrang@easia-travel.com','haiyen@easia-travel.com','nguyengiang@easia-travel.com','thanhthuy@easia-travel.com','nguyetnt@easia-travel.com','dohien@easia-travel.com','thanhloan@easia-travel.com','huonggiang@easia-travel.com','quanghuy@easia-travel.com','myngoc@easia-travel.com','luongnam@easia-travel.com','bichthao@easia-travel.com','phuongha@easia-travel.com','nguyenphuong@easia-travel.com','thaontp@easia-travel.com','thanhhang@easia-travel.com','nguyenhuyen@easia-travel.com','phuonghoa@easia-travel.com','hongtham@easia-travel.com','nguyenvan@easia-travel.com','maihuong@easia-travel.com','vuthihuong@easia-travel.com','dieulinh@easia-travel.com','hoaly@easia-travel.com','bichquyen@easia-travel.com','thuannguyen@easia-travel.com','hoaithu@easia-travel.com','lethuy@easia-travel.com','nguyenthuphuong@easia-travel.com','nguyenthinhung@easia-travel.com','thutrang@easia-travel.com','hongnhung@easia-travel.com','phanhong@easia-travel.com','nguyentrang@easia-travel.com','tranquan@easia-travel.com','minhtrang@easia-travel.com','taluyen@easia-travel.com','thuylinh@easia-travel.com','thuananh@easia-travel.com','ngoclinh@easia-travel.com','ngoctram@easia-travel.com','minhhang@easia-travel.com','anhtho@easia-travel.com','haianh@easia-travel.com','nguyenngoc@easia-travel.com','doanhoanganh@easia-travel.com','lehoa@easia-travel.com','ngocthang@easia-travel.com','ngocanh@easia-travel.com','trang@easia-travel.com','kimdat@easia-travel.com','hongduc@easia-travel.com','tiengiang@easia-travel.com','thanhquang@easia-travel.com','tienquyet@easia-travel.com','trungkien@easia-travel.com','thanhtung@easia-travel.com','tienduc@easia-travel.com','nguyenthao@easia-travel.com','quachlinh@easia-travel.com','buiduong@easia-travel.com','thuhuong@easia-travel.com','trungdung@easia-travel.com','thucanh@easia-travel.com','lanphuong@easia-travel.com','huyenltt@easia-travel.com','dieuly@easia-travel.com','dominh@easia-travel.com','tavan@easia-travel.com','tienhung@easia-travel.com','haison@easia-travel.com','adam@easia-travel.com','myhanh@easia-travel.com','quangdung@easia-travel.com','maitrang@easia-travel.com','doducanh@easia-travel.com','vuhuong@easia-travel.com','thuhao@easia-travel.com','thuydung@easia-travel.com','phamyen@easia-travel.com','tuyetdung@easia-travel.com','buinhuan@easia-travel.com','thanhcuong@easia-travel.com','manhcuong@easia-travel.com','daovan@easia-travel.com','hoann@easia-travel.com','thuymien@phh-group.com','thuhien@phh-group.com','touyen@phh-group.com','receptionist@phh-group.com','admin@phh-group.com','veronique@easia-travel.com','maianh@easia-travel.com','brice@easia-travel.com','diemmy@easia-travel.com','dean@easia-travel.com','ngongoc@phh-group.com','hoangoanh@easia-travel.com','phuongthao@easia-travel.com','caoson@easia-travel.com','bichtuyen@easia-travel.com','trantrang@easia-travel.com','camly@easia-travel.com','anhthu@easia-travel.com','nhatanh@easia-travel.com','trangnm@easia-travel.com','ngochien@easia-travel.com','dongsang@easia-travel.com','quockhanh@easia-travel.com','kimthao@easia-travel.com','phamhoang@easia-travel.com','baothu@easia-travel.com','kimthu@easia-travel.com','quechau@easia-travel.com','minhphuc@easia-travel.com','quangthang@easia-travel.com','khaiquang@easia-travel.com','hoangyen@easia-travel.com','diemtri@easia-travel.com','minhhoa@easia-travel.com','letho@easia-travel.com','duybao@easia-travel.com','lenhan@easia-travel.com','myvan@easia-travel.com','tuphuong@easia-travel.com','thuytrinh@easia-travel.com','nguyenoanh@easia-travel.com',
            //exotic
            'assistant@exoticvoyages.com','tranthuy@exoticvoyages.com','thuhang@exoticvoyages.com','tranhoa@exoticvoyages.com','huyentrang@exoticvoyages.com','buichi@exoticvoyages.com','quynhhoa@exoticvoyages.com','maisen@exoticvoyages.com','hongyen@easia-travel.com','thuthuy@exoticvoyages.com','thanhhung@exoticvoyages.com','hongtham@exoticvoyages.com','thuynguyenvn1996@gmail.com','lehoang@exoticvoyages.com','tuyethang@exoticvoyages.com','trungkien@exoticvoyages.com','khanhlinh@exoticvoyages.com','hongtrang@exoticvoyages.com','nhatruc@exoticvoyages.com','phuongquynh@exoticvoyages.com',
            //begodi
            'vuhuy@easia-travel.com','damquynh@exoticvoyages.com','huyhoang@begodi.com','hienluong@begodi.com','contentwriter@begodi.com','vananh@begodi.com','haohiep@begodi.com','tatquang@begodi.com','nguyentrinhthuytien1998@gmail.com','nhungnth@begodi.com','intern.begodi@gmail.com','dungdt@begodi.com','lanhuong@begodi.com','thuphuong@begodi.com','contentwriter02@begodi.com','thuytien@begodi.com','anhntv@begodi.com','xuanquang@begodi.com',
        ];

        $exclude_email = [
            'easia',
            'phh'
        ];


        DB::beginTransaction();
         try {
//Xóa lần lượt

//        $users = MdlUser::query()
//            ->whereNotIn('username', $excludes)
//            ->select('id')
//            ->limit(500)
//            ->get();
//
//        foreach ($users as $user) {
//            //Gọi hàm xóa tms user, trong hàm có gọi sang lms để xóa
//            TmsUserDetail::clearUser($user->id);
//        }

//Xóa cùng lúc
        //Xóa khỏi bảng TmsUserDetail
        TmsUserDetail::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        //Xóa dữ liệu liên quan
        //Tms tables
        ModelHasRole::query()
            ->whereIn('model_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('model_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        TmsRoleOrganize::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        TmsSaleRoomUser::query()
//            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
//                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
//            })
//            ->orWhereNotIn('user_id', function ($q)  {
//                self::buildSubQueryForUser2($q);
//            })
            ->delete();

        TmsSurveyUserView::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        TmsSurveyUser::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        TmsTrainningUser::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        TmsUserSaleDetail::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        TmsDevice::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        TmsLog::query()
            ->whereIn('user', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        //Old organize system
        TmsCity::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->update(['user_id' => 0]);

        TmsBranchMaster::query()
//            ->whereIn('master_id', function ($q) use ($excludes, $exclude_email) {
//                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
//            })
//            ->orWhereNotIn('master_id', function ($q)  {
//                self::buildSubQueryForUser2($q);
//            })
//            ->update(['master_id' => 0]);
            ->delete();

        TmsCityBranch::query()->delete();

        TmsBranch::query()
//            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
//                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
//            })
//            ->orWhereNotIn('user_id', function ($q)  {
//                self::buildSubQueryForUser2($q);
//            })
//            ->update(['user_id' => 0]);
            ->delete();

        TmsBranchSaleRoom::query()->delete();

        TmsSaleRooms::query()
//            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
//                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
//            })
//            ->orWhereNotIn('user_id', function ($q)  {
//                self::buildSubQueryForUser2($q);
//            })
//            ->update(['user_id' => 0]);
            ->delete();

        //new tables
        TmsInvitation::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();
        TmsOrganizationEmployee::query()
            ->whereIn('user_id', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('user_id', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        //Not has tms in name tables by dat09
        CourseFinal::query()
            ->whereIn('userid', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('userid', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();
        CourseCompletion::query()
            ->whereIn('userid', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('userid', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();
        StudentCertificate::query()
            ->whereIn('userid', function ($q) use ($excludes, $exclude_email) {
                self::buildSubQueryForUser1($q, $excludes, $exclude_email);
            })
            ->orWhereNotIn('userid', function ($q)  {
                self::buildSubQueryForUser2($q);
            })
            ->delete();

        //Temporary
        //Delete mdl_user_enrolments data
        MdlUserEnrolments::query()->whereIn('userid', function ($q) use ($excludes, $exclude_email) {
            self::buildSubQueryForUser1($q, $excludes, $exclude_email);
        })->delete();

        //Delete from

        //Xóa user trong bảng mdl_user
        //Server error General error: 1093 You can't specify target table 'mdl_user' for update in FROM clause - mặc dù đã đặt alias??
//        MdlUser::query()
//            ->whereNotIn('username', $excludes)
//            ->whereNotIn('email', function ($q1) use ($exclude_email)  {
//                $q1->select('mu.email')->from('mdl_user AS mu');
//                foreach ($exclude_email as $key => $exclude_email_item) {
//                    if ($key == 0) {
//                        $q1->where('mu.email', 'like', '%@'. $exclude_email_item .'%');
//                    } else {
//                        $q1->orWhere('mu.email', 'like', '%@'. $exclude_email_item .'%');
//                    }
//                }
//            })->delete();
        $excluded_emails = MdlUser::query();
        foreach ($exclude_email as $key => $exclude_email_item) {
            if ($key == 0) {
                $excluded_emails->where('email', 'like', '%@'. $exclude_email_item .'%');
            } else {
                $excluded_emails->orWhere('email', 'like', '%@'. $exclude_email_item .'%');
            }
        };
        $excluded_emails = $excluded_emails->pluck('email');
         MdlUser::query()
        ->whereNotIn('username', $excludes)
        ->whereNotIn('email', $excluded_emails)->delete();


        //Xóa user trong bảng mdl mà không có trong bảng tms_user_detail
        MdlUser::query()->whereNotIn('id', function ($q2)  {
                    $q2->select('user_id')->from('tms_user_detail');
                })
            ->delete();
             DB::commit();
         } catch (\Exception $e) {
             Log::error($e);
             DB::rollBack();
         }

         //Clear organization and role
//         $cleanOrganizations = TmsOrganization::query()->where('name', '<>', 'TVE')->get()->toArray();
//         foreach ($cleanOrganizations as $cleanOrganization) {
//             TmsOrganizationEmployee::query()->where('organization_id', $cleanOrganization['id'])->delete();
//             $check = TmsRoleOrganization::query()->where('organization_id', $cleanOrganization['id'])->first();
//             if ($check) {
//                 $role_id = $check->role_id;
//                 TmsRoleCourse::query()->where('role_id', $role_id)->delete();
//             }
//             TmsRoleOrganization::query()->where('organization_id', $cleanOrganization['id'])->delete();
//             Role::query()->where('description', $cleanOrganization['name'])->delete();
//             MdlRole::query()->where('description', $cleanOrganization['name'])->delete();
//         }
//        TmsOrganization::query()->where('name', '<>', 'TVE')->delete();
    }

    function buildSubQueryForUser1(&$q, $excludes, $exclude_email) {
        /**
         * @var $q Builder
         */
        $q->select('id')
            ->from('mdl_user')
            ->whereNotIn('username', $excludes);
        foreach ($exclude_email as $exclude_email_item) {
            $q->whereNotIn('email', function ($q1) use ($exclude_email_item)  {
                $q1->select('email')
                    ->from('mdl_user')
                    ->where('email', 'like', '%@'. $exclude_email_item .'%');
            });
        }
    }

    function buildSubQueryForUser2(&$q) {
        /**
         * @var $q Builder
         */
        $q->select('id')->from('mdl_user');
    }

    function deleteLeftoverData() {
        MdlUserEnrolments::query()->whereNotIn('userid', function ($q2)  {
            $q2->select('user_id')->from('tms_user_detail');
        })->delete();
        //updating
    }

    public function resetOrganizationEmployeePassword() {
        $users = MdlUser::query()->where('username', '<>', 'admin')->get();
        foreach ($users as $user) {
            $user->password = bcrypt('123456a@');
            $user->save();
        }
        echo "Successfully!";
    }
}

