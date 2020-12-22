<?php


namespace App\Repositories;


use App\Imports\QuestionImport;
use App\Imports\UsersImport;
use App\MdlContext;
use App\MdlCourse;
use App\MdlCourseCompletionCriteria;
use App\MdlEnrol;
use App\MdlGradeCategory;
use App\MdlGradeItem;
use App\MdlLogstoreStandardLog;
use App\MdlQuestion;
use App\MdlQuestionAnswer;
use App\MdlRole;
use App\MdlUser;
use App\Role;
use App\TmsInvitation;
use App\TmsOptionalCourse;
use App\TmsOrganization;
use App\TmsOrganizationEmployee;
use App\TmsRoleCourse;
use App\TmsRoleOrganization;
use App\TmsTrainningCourse;
use App\TmsTrainningProgram;
use App\TmsUserCourseException;
use App\TmsUserOrganizationCourseException;
use App\TmsUserOrganizationException;
use App\ViewModel\ImportModel;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Horde\Socket\Client\Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MdlCourseRepository implements IMdlCourseInterface, ICommonInterface
{

    //api xóa khóa học
    public function apiDeleteCourse(Request $request)
    {
        $response = new ResponseModel();
        try {

            $id = $request->input('course_id');

            $param = [
                'course_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }

            //Nếu trạng thái là visible thì có thể xóa

            $course = MdlCourse::where('id', '=', $id)
                ->where('startdate', '<=', time())
                ->where('visible', '=', '1')
                ->where('enddate', '>=', time())->first();

            //nếu tồn tại khóa học trong thời gian học và không phải khóa học mẫu
            if ($course && $course->category != 2) {
                //Đang trong thời gian học, không xóa được
                $result = 10;
            } else {

//                $app_name = Config::get('constants.domain.APP_NAME');
//
//                $key_app = encrypt_key($app_name);
//
//                $data = array(
//                    'courseid' => $id,
//                    'app_key' => $key_app
//                );
//
//                $data = createJWT($data, 'data');
//
//                $data_del = array(
//                    'data' => $data
//                );
//
//                $url = Config::get('constants.domain.LMS') . '/course/delete_course.php';
//                //call api write log
//                $result = callAPI('POST', $url, $data_del, false, '');
                $course = MdlCourse::findOrFail($id);
                $course->deleted = 1;
                // update an khoa hoc neu khoa hoc xoa dang nam trong khung nang luc => để không vênh dữ liệu
//                if($course->category == 2){
                TmsTrainningCourse::where('course_id', '=', $id)->update(['deleted' => '1']);
//                }
                $course->save();

                $result = 1;
            }

            if ($result == 1) {

                //write log to mdl_logstore_standard_log
                /*                $app_name = Config::get('constants.domain.APP_NAME');

                                $key_app = encrypt_key($app_name);
                                $user_id = Auth::id();
                                $dataLog = array(
                                    'app_key' => $key_app,
                                    'courseid' => $course->id,
                                    'action' => 'delete',
                                    //'description' => json_encode($course->toArray()), //Cause error when decode jwt by using html editor here
                                    'userid' => $user_id
                                );
                                $dataLog = createJWT($dataLog, 'data');
                                $data_write = array(
                                    'data' => $dataLog,
                                );
                                $url = Config::get('constants.domain.LMS') . '/course/write_log.php';
                                $checkUser = MdlUser::where('id', $user_id)->first();
                                $token = '';
                                if (isset($checkUser)) {
                                    $token = strlen($checkUser->token) != 0 ? $checkUser->token : '';
                                }
                                //call api write log
                                callAPI('POST', $url, $data_write, false, $token);*/

                devcpt_log_system('course', 'lms/course/view.php?id=' . $course->id, 'delete', 'Delete course: ' . $course->shortname);
                updateLastModification('delete', $course->id);


                $response->status = true;
                $response->message = __('xoa_khoa_hoc');
            } else if ($result == 10) {
                $response->status = false;
                $response->message = __('dang_trong_thoi_gian_hoc');
            } else {
                $response->status = false;
                $response->message = __('xoa_khoa_hoc_khong_thanh_cong');
            }

            //            MdlCourseCompletionCriteria::where('course', '=', $id)->delete();
            //
            //            MdlCourse::where('id', $id)->delete();


        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //api lấy danh sách khóa học mẫu
    public function apiGetListCourseSample()
    {

        if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {
            $listCourses = DB::table('mdl_course')
                ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
                ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                ->select(
                    'mdl_course.id',
                    'mdl_course.fullname',
                    'mdl_course.shortname',
                    'mdl_course.summary as description',
                    'mdl_course.course_avatar as avatar',
                    'mdl_course.allow_register',
                    'mdl_course.total_date_course',
                    'mdl_course.is_end_quiz',
                    'mdl_course_completion_criteria.gradepass as pass_score'
                );

        } else {
//            $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
            $listCourses = DB::table('mdl_course')
                ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
                ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                ->whereIn('mdl_course.id', function ($q) { //organization
                    /* @var $q Builder */
                    $q->select('mdl_course.id')
                        ->from('tms_organization_employee')
                        ->join('tms_role_organization', 'tms_organization_employee.organization_id', '=', 'tms_role_organization.organization_id')
                        ->join('tms_role_course', 'tms_role_organization.role_id', '=', 'tms_role_course.role_id')
                        ->join('mdl_course', 'tms_role_course.course_id', '=', 'mdl_course.id')
                        ->where('tms_organization_employee.user_id', '=', \Auth::user()->id);
                })
                ->select(
                    'mdl_course.id',
                    'mdl_course.fullname',
                    'mdl_course.shortname',
                    'mdl_course.summary as description',
                    'mdl_course.course_avatar as avatar',
                    'mdl_course.allow_register',
                    'mdl_course.total_date_course',
                    'mdl_course.is_end_quiz',
                    'mdl_course_completion_criteria.gradepass as pass_score'
                );
        }


        $listCourses = $listCourses
            ->where('mdl_course.category', '=', 2)
            ->where('mdl_course.deleted', '=', 0); //2 là khóa học mẫu

//        $listCourses = $listCourses->orderBy('id', 'desc')->get();
        $listCourses = $listCourses->orderBy('mdl_course.shortname', 'desc')->get();

        return response()->json($listCourses);
    }


    //api clone khóa học
    public function apiCloneCourse(Request $request)
    {
        $response = new ResponseModel();
        try {

            $avatar = $request->file('file');
            $course_avatar = $request->input('course_avatar');
            $fullname = $request->input('fullname');
            $shortname = $request->input('shortname');
            $startdate = $request->input('startdate');
            $enddate = $request->input('enddate');
            $pass_score = $request->input('pass_score');
            $description = $request->input('description');
            $category_id = $request->input('category_id');
            $allow_register = $request->input('allow_register');
            $total_date_course = $request->input('total_date_course');
            $is_end_quiz = $request->input('is_end_quiz');
            $estimate_duration = $request->input('estimate_duration');
            $course_budget = $request->input('course_budget');
            $access_ip_string = $request->input('access_ip');
            $is_toeic = $request->input('is_toeic');
            $org_code = $request->input('org_code');

            $param = [
                'course_avatar' => 'text',
                'fullname' => 'text',
                'shortname' => 'code',
                'description' => 'longtext',
                'pass_score' => 'number',
                'category_id' => 'number',
                'course_place' => 'text',
                'allow_register' => 'number',
                'total_date_course' => 'number',
                'is_end_quiz' => 'number',
                'estimate_duration' => 'number',
                'course_budget' => 'number',
                'access_ip' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }


            //check course info exist
            $courseInfo = MdlCourse::select('id')->where('shortname', $shortname)->where('deleted', 0)->first();

            if ($courseInfo) {
                $response->status = false;
                $response->message = __('ma_khoa_hoc_da_ton_tai');
                return response()->json($response);
            }

            //thực hiện insert dữ liệu

            if ($avatar) {
                $name_file = str_replace(' ', '', $shortname);
                $name_file = str_replace('/', '', $name_file);
                $name_file = str_replace('\\', '', $name_file);
                $name_file = utf8convert($name_file);
                $name = $name_file . '.' . $avatar->getClientOriginalExtension();

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/course',
                    $avatar,
                    $name
                );

                $path_avatar = '/storage/upload/course/' . $name;
            } else if ($course_avatar) {
                $path_avatar = $course_avatar;
            } else {
                $path_avatar = '/storage/upload/course/default_course.jpg';
            }

            $course = new MdlCourse(); //khởi tạo theo cách này để tránh trường hợp insert startdate và endate bị set về 0
            $course->category = $category_id;
            $course->shortname = $shortname;
            $course->fullname = $fullname;
            $course->summary = $description;
            $course->course_avatar = $path_avatar;
            $course->estimate_duration = $estimate_duration;
            $course->course_budget = $course_budget;

            if ($category_id == 3) {
                $course->is_certificate = 1;
                $course->is_end_quiz = $is_end_quiz;
            }

            if ($category_id != 2) { //nếu là thư viện khóa học thì không check thời gian
                $stdate = strtotime($startdate);
                $eddate = !is_null($enddate) ? strtotime($enddate) : 0;
                if ($enddate && $stdate > $eddate) {
                    $response->status = false;
                    $response->message = __('thoi_gian_bat_dau_khong_lon_hon_ket_thuc');
                    return response()->json($response);
                }

                $course->startdate = $stdate;
                $course->enddate = $eddate;
            }


            $course->total_date_course = $total_date_course;
            $course->allow_register = $allow_register;
            $course->visible = 0;

            //access_ip
            $access_ip = $this->spitIP($access_ip_string);
            $course->access_ip = $access_ip;

            //toeic
            $course->is_toeic = $is_toeic;
            $course->save();

            //insert dữ liệu điểm qua môn
            MdlCourseCompletionCriteria::create(array(
                'course' => $course->id,
                'criteriatype' => 6, //default là 6 trong trường hợp này
                'gradepass' => $pass_score
            ));


            $context_cate = MdlContext::where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSECAT)
                ->where('instanceid', '=', $category_id)->first();

            if ($context_cate) {
                //insert dữ liệu vào bảng mdl_context
                $mdl_context = MdlContext::firstOrCreate([
                    'contextlevel' => \App\MdlUser::CONTEXT_COURSE,
                    'instanceid' => $course->id,
                    'depth' => 3,
                    'locked' => 0
                ]);

                //cập nhật path
                $mdl_context->path = '/1/' . $context_cate->id . '/' . $mdl_context->id;
                $mdl_context->save();
            }

            //write data to table mdl_grade_categories -> muc dich phuc vu cham diem, Vinh PT yeu cau
            $mdl_grade_cate = MdlGradeCategory::firstOrCreate([
                'courseid' => $course->id,
                'depth' => 1,
                'aggregation' => 13,
                'aggregateonlygraded' => 1,
                'timecreated' => strtotime(Carbon::now()),
                'timemodified' => strtotime(Carbon::now())
            ]);

            $mdl_grade_cate->path = '/' . $mdl_grade_cate->id . '/';
            $mdl_grade_cate->save();

            //write data to table mdl_grade_items
            MdlGradeItem::firstOrCreate([
                'courseid' => $course->id,
                'itemname' => $course->fullname,
                'itemtype' => 'course',
                'iteminstance' => $mdl_grade_cate->id,
                'gradepass' => $pass_score
            ]);

            //insert dữ liệu vào bảng mdl_enrol, yêu cầu của VinhPT phục vụ mục đích đăng ký học của học viên bên LMS
            $enrol = DB::table('mdl_enrol')
                ->where('enrol', '=', 'manual')
                ->where('courseid', '=', $course->id)
                ->where('enrol', '=', 'self')
                ->where('roleid', '=', 5)//quyền học viên
                ->first();

            if ($enrol) {
                $enrol->status = 0;
                $enrol->save();
            } else {
                MdlEnrol::create([
                    'enrol' => 'self',
                    'courseid' => $course->id,
                    'roleid' => 5,
                    'sortorder' => 0,
                    'status' => 0
                ]);
            }

            if ($course->category != 2) { //ko phai thu vien khoa hoc
                $tms_trainning = TmsTrainningProgram::firstOrCreate([
                    'code' => $course->shortname . $course->id,
                    'name' => $course->fullname,
                    'style' => 1,
                    'run_cron' => 1,
                    'time_start' => 0,
                    'time_end' => 0,
                    'auto_certificate' => 1,
                    'auto_badge' => 1,
                    'deleted' => 2 //KNL ko hien thi tren he thong
                ]);

                if ($tms_trainning) {
                    TmsTrainningCourse::firstOrCreate([
                        'trainning_id' => $tms_trainning->id,
                        'sample_id' => $course->id,
                        'course_id' => $course->id
                    ]);
                }
            }


            $user_id = Auth::id();
            //Check role teacher and enrol for creator of course
            $current_user_roles_and_slugs = checkRole();
            //If user ís not a teacher, assign as teacher
            $role_teacher = Role::select('id', 'name', 'mdl_role_id', 'status')->where('name', Role::TEACHER)->first();
            if (!$current_user_roles_and_slugs['roles']->has_role_teacher) {
                add_user_by_role($user_id, $role_teacher->id);
                enrole_lms($user_id, $role_teacher->mdl_role_id, 1);
            }
            //Enrol user to newly created course as teacher
            enrole_user_to_course_multiple(array($user_id), $role_teacher->mdl_role_id, $course->id, true);


            //Add newly course to phân quyền dữ liệu
            if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {
                //admin do nothing
            } else {
                $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
                if ($checkRoleOrg != 0) {
                    $org_role = TmsRoleOrganization::query()->where('organization_id', $checkRoleOrg)->first();
                    if (isset($org_role)) {
                        $new_relation = new TmsRoleCourse();
                        $new_relation->role_id = $org_role->role_id;
                        $new_relation->course_id = $course->id;
                        $new_relation->save();
                    }

                    //lay tat ca user ngoai le duoc quyen quan ly course cua cctc
                    $lstUserExcept = TmsUserOrganizationException::where('organization_id', $checkRoleOrg)->pluck('user_id');

                    if ($lstUserExcept) {
                        $arr_data = [];
                        $data_course = [];

                        foreach ($lstUserExcept as $user) {
                            $data_course['user_id'] = $user;
                            $data_course['organization_id'] = $checkRoleOrg;
                            $data_course['course_id'] = $course->id;
                            $data_course['created_at'] = Carbon::now();
                            $data_course['updated_at'] = Carbon::now();

                            array_push($arr_data, $data_course);
                        }

                        //gan course cho user ngoai le quan ly
                        TmsUserOrganizationCourseException::insert($arr_data);
                    }

                }
            }

            //add user except, apply when admin or user not in organization select course code
            if ($org_code) {

                $userExcept = DB::table('tms_organization as tor')
                    ->join('tms_user_organization_exceptions as tuoe', 'tuoe.organization_id', '=', 'tor.id')
                    ->where('tor.code', '=', $org_code)
                    ->select('tuoe.user_id', 'tor.id as org_id')->get();

                if ($userExcept) {
                    $arr_data = [];
                    $data_course = [];

                    foreach ($userExcept as $user) {
                        $data_course['user_id'] = $user->user_id;
                        $data_course['organization_id'] = $user->org_id;
                        $data_course['course_id'] = $course->id;
                        $data_course['created_at'] = Carbon::now();
                        $data_course['updated_at'] = Carbon::now();

                        array_push($arr_data, $data_course);
                    }

                    //gan course cho user ngoai le quan ly
                    TmsUserOrganizationCourseException::insert($arr_data);
                }
            }

            //write log to mdl_logstore_standard_log
            /*            $app_name = Config::get('constants.domain.APP_NAME');
                        $key_app = encrypt_key($app_name);
                        $dataLog = array(
                            'app_key' => $key_app,
                            'courseid' => $course->id,
                            'action' => 'create',
                            'description' => json_encode($course),
                        );
                        $dataLog = createJWT($dataLog, 'data');
                        $data_write = array(
                            'data' => $dataLog,
                        );
                        $url = Config::get('constants.domain.LMS') . '/course/write_log.php';
                        //call api write log
                        callAPI('POST', $url, $data_write, false, '');*/

            devcpt_log_system('course', 'lms/course/view.php?id=' . $course->id, 'create', 'Create course: ' . $course->shortname);
            updateLastModification('create', $course->id);

            $response->status = true;
            $response->message = __('nhan_ban_khoa_hoc');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }


    //api lấy danh sách khóa học tập trung
    public function apiGetListCourseConcen(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $category_id = $request->input('category_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $status_course = $request->input('status_course');
//        $sample = $request->input('sample'); //field xác định giá trị là khóa học mẫu hay không

        if (strlen($category_id) == 0) {
            $category_id = 5;
        }

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'category_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $select = [];
        //Nếu có quyền admin hoặc root hoặc có quyền System administrator thì được phép xem tất cả
        //if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {

//        if (true) { //hack show all courses offline
        $listCourses = DB::table('mdl_course')
            ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
            ->where('mdl_course.category', '=', $category_id);
        $select = [
            'mdl_course.id',
            'mdl_course.fullname',
            'mdl_course.shortname',
            'mdl_course.startdate',
            'mdl_course.enddate',
            'mdl_course.visible',
            'mdl_course_completion_criteria.gradepass as pass_score'
        ];

        //region comment code by require easia show all course offline
//        }
//        else {
//            //check xem người dùng có thuộc bộ 3 quyền: leader, employee, manager hay không?
//            //$checkRole = tvHasRoles(\Auth::user()->id, ["manager", "leader", "employee"]);
//            //if ($checkRole === true) {
//            $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
//            if ($checkRoleOrg != 0) {
//                $listCourses = DB::table('mdl_course')
//                    ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'mdl_course.id')
//                    ->where('mdl_course.category', '=', $category_id)
//                    ->where(function ($query) {
//                        /* @var $query Builder */
//                        $query
//                            ->whereIn('mdl_course.id', function ($q1) { //enrol
//                                /* @var $q1 Builder */
//                                $q1->select('mdl_course.id')
//                                    ->from('mdl_user_enrolments as mue')
//                                    ->join('mdl_enrol as e', 'mue.enrolid', '=', 'e.id')
//                                    ->join('mdl_course', 'e.courseid', '=', 'mdl_course.id')
//                                    ->where('mue.userid', '=', \Auth::user()->id);
//                            })
//                            ->orWhereIn('mdl_course.id', function ($q2) { //organization
//                                /* @var $q2 Builder */
//                                $q2->select('mdl_course.id')
//                                    ->from('tms_organization_employee')
//                                    ->join('tms_role_organization', 'tms_organization_employee.organization_id', '=', 'tms_role_organization.organization_id')
//                                    ->join('tms_role_course', 'tms_role_organization.role_id', '=', 'tms_role_course.role_id')
//                                    ->join('mdl_course', 'tms_role_course.course_id', '=', 'mdl_course.id')
//                                    ->where('tms_organization_employee.user_id', '=', \Auth::user()->id);
//                            });
//                    });
//                $select = [
//                    'mdl_course.id',
//                    'mdl_course.fullname',
//                    'mdl_course.shortname',
//                    'mdl_course.startdate',
//                    'mdl_course.enddate',
//                    'mdl_course.visible',
//                    'mdl_course.category',
//                    'mdl_course.deleted',
//                    'mccc.gradepass as pass_score'
//                ];
//            } else {
//                //Kiểm tra xem có phải role teacher hay không
//                $checkRole = tvHasRole(\Auth::user()->id, "teacher");
//                if ($checkRole == true) {
//                    $listCourses = DB::table('mdl_user_enrolments')
//                        ->where('mdl_user_enrolments.userid', '=', \Auth::user()->id)
//                        ->join('mdl_enrol', 'mdl_user_enrolments.enrolid', '=', 'mdl_enrol.id')
//                        ->join('mdl_course', 'mdl_enrol.courseid', '=', 'mdl_course.id')
//                        ->where('mdl_course.category', '=', $category_id)
//                        ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id');
//                    $select = [
//                        'mdl_course.id',
//                        'mdl_course.fullname',
//                        'mdl_course.shortname',
//                        'mdl_course.startdate',
//                        'mdl_course.enddate',
//                        'mdl_course.visible',
//                        'mdl_course_completion_criteria.gradepass as pass_score'
//                    ];
//                }
//            }
//        }
        //endregion

        $listCourses->leftJoin('mdl_user', 'mdl_course.last_modify_user', '=', 'mdl_user.id');
        //$select[] = 'mdl_course.last_modify_user';
        $select[] = 'mdl_user.username';
        $select[] = DB::raw("DATE_FORMAT(FROM_UNIXTIME(`mdl_course`.`last_modify_time`), '%Y-%m-%d %H:%i:%s') as last_modify_time");
        $select[] = 'mdl_course.last_modify_action';
        $listCourses->select($select);

        //là khóa học mẫu
        //        if ($sample == 1) {
        //            $listCourses = $listCourses->where('mdl_course.category', '=', 2); //2 là khóa học mẫu
        //        } else {
        //            $listCourses = $listCourses->where('mdl_course.category', '!=', 2);
        //        }

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $listCourses = $listCourses->whereRaw('( mdl_course.fullname like "%' . $keyword . '%" OR mdl_course.shortname like "%' . $keyword . '%" )');
        }

        //        if ($category_id) {
        //            $listCourses = $listCourses->where('mdl_course.category', '=', $category_id);
        //        }

        if ($startdate) {
            $cv_startDate = strtotime($startdate);
            $listCourses = $listCourses->where('mdl_course.startdate', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate);
            $listCourses = $listCourses->where('mdl_course.enddate', '<=', $cv_endDate);
        }
        $listCourses = $listCourses->where('mdl_course.deleted', '=', 0);

        if ($status_course) {
            $unix_now = strtotime(Carbon::now());
            if ($status_course == 1) { //các khóa sắp diễn ra
                $listCourses = $listCourses->where('mdl_course.startdate', '>', $unix_now);
            } else if ($status_course == 2) { //các khóa đang diễn ra
                $listCourses = $listCourses->where('mdl_course.startdate', '<=', $unix_now);
//                $listCourses = $listCourses->where('mdl_course.enddate', '>=', $unix_now);
                $listCourses = $listCourses->where(function ($query) use ($unix_now) {
                    $query->where('mdl_course.enddate', '>=', $unix_now)
                        ->orWhere('mdl_course.enddate', '=', 0);
                });
            } else if ($status_course == 3) { //các khóa đã diễn ra
                $listCourses = $listCourses->where('mdl_course.enddate', '<=', $unix_now)
                    ->where('mdl_course.enddate', '>', 0);
            }
        }


        if ($keyword) {
            $listCourses = $listCourses->orderBy('mdl_course.shortname', 'desc');
        } else {
            $listCourses = $listCourses->orderBy('mdl_course.shortname', 'desc');
            //$listCourses = $listCourses->orderBy('mdl_course.id', 'desc');
        }

        if ($row == 0) {
            return $listCourses->get();
        }

        $totalCourse = count($listCourses->get()); //lấy tổng số khóa học hiện tại

        $listCourses = $listCourses->paginate($row);
        $total = ceil($listCourses->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listCourses->currentPage(),
            ],
            'data' => $listCourses,
            'total_course' => $totalCourse
        ];


        return response()->json($response);
    }

    //api lấy danh sách khóa học cần restore
    public function apiGetListCourseRestore(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $category_id = $request->input('category_id');
        $timecreated = $request->input('timecreated');
        $enddate = $request->input('enddate');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'category_id' => 'number',
            'timecreated' => 'text',
            'enddate' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listCourses = DB::table('mdl_course')
            ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
            ->select(
                'mdl_course.id',
                'mdl_course.fullname',
                'mdl_course.shortname',
                'mdl_course.updated_at as timecreated',
                'mdl_course_categories.name as category_name',
                'mdl_course_categories.id as categoryid'
            );

        if ($category_id) {
            $listCourses = $listCourses->where('mdl_course.category', '=', $category_id);
        }

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $listCourses = $listCourses->whereRaw('( mdl_course.fullname like "%' . $keyword . '%" OR mdl_course.shortname like "%' . $keyword . '%" )');
        }

        if ($timecreated) {
            $cv_startDate = strtotime($timecreated);
            $listCourses = $listCourses->where('mdl_course.updated_at', '>=', date("Y-m-d H:i:s", $cv_startDate));
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate . " 23:59:59");
            $listCourses = $listCourses->where('mdl_course.updated_at', '<=', date("Y-m-d H:i:s", $cv_endDate));
        }

        $listCourses = $listCourses->where('mdl_course.deleted', '=', 1);


        $totalCourse = count($listCourses->get()); //lấy tổng số khóa học hiện tại

        //$listCourses = $listCourses->orderBy('id', 'desc');
        $listCourses = $listCourses->orderBy('mdl_course.shortname', 'desc');

        $listCourses = $listCourses->paginate($row);
        $total = ceil($listCourses->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listCourses->currentPage(),
            ],
            'data' => $listCourses,
            'total_course' => $totalCourse
        ];


        return response()->json($response);
    }

    //api restore khóa học
    public function apiRestoreCourse(Request $request)
    {
        $response = new ResponseModel();
        try {

            $id = $request->input('course_id');
            $instance_id = $request->input('instance_id');
            $action = $request->input('action');

            $param = [
                'course_id' => 'number',
                'instance_id' => 'number',
                'action' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }


//            $contextData = MdlContext::query();
//            $contextData = $contextData->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSECAT);
//            $contextData = $contextData->where('instanceid', '=', $instance_id);
//            $contextData = $contextData->orderBy('id', 'desc')->first();
//
//            if (!$contextData) {
//                $response->status = false;
//                $response->message = __('khoi_phuc_khoa_hoc');
//                return response()->json($response);
//            }

//            $app_name = Config::get('constants.domain.APP_NAME');
//
//            $key_app = encrypt_key($app_name);
//
//            $data = array(
//                'contextid' => $contextData->id,
//                'itemid' => $id,
//                'action' => $action,
//                'app_key' => $key_app
//            );
//
//            $data = createJWT($data, 'data');
//
//            $data_res = array(
//                'data' => $data
//            );
//
//            $url = Config::get('constants.domain.LMS') . '/admin/tool/recyclebin/recyclebin.php';
//
//            //call api write log
//            $result = callAPI('POST', $url, $data_res, false, '');


            $course = MdlCourse::findOrFail($id);
            $course->deleted = 0;
            //nếu là thư viện khóa học => Cập nhật cả trong khung năng lực tms_trainning_courses vì
            // khi xóa thư viện khóa học thì chuyển trạng thái khóa học đó trong knl = 1

            TmsTrainningCourse::where('course_id', '=', $id)->update(['deleted' => '0']);

            $course->save();

            $result = 1;

            if ($result == 1) {


                /*                $contextData = MdlContext::query();
                                $contextData = $contextData->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSE);
                                $contextData = $contextData->where('instanceid', '=', $id);
                                $contextData = $contextData->orderBy('id', 'desc')->first();

                                if ($contextData) {
                                    //Write log to mdl_logstore_standard_log
                                    $new_event = new MdlLogstoreStandardLog();
                                    $new_event->eventname = '\core\event\course_restored';
                                    $new_event->component = 'core';
                                    $new_event->action = 'restored';
                                    $new_event->target = 'course';
                                    $new_event->objecttable = 'course';
                                    $new_event->objectid = $id;
                                    $new_event->crud = 'c';
                                    $new_event->edulevel = 1;
                                    $new_event->contextid = $contextData->id;
                                    $new_event->contextlevel = \App\MdlUser::CONTEXT_COURSE;
                                    $new_event->contextinstanceid = $id;
                                    $new_event->userid = Auth::id();
                                    $new_event->courseid = $id;
                                    $new_event->other = json_encode([
                                        "type"=>"course",
                                        "target"=> 1,
                                        "mode"=> 20,
                                        "operation" => "restore",
                                        "samesite" => true,
                                        "originalcourseid"=> "596"
                                    ]);
                                    $new_event->timecreated = time();
                                    $new_event->origin = "restore";
                                    $new_event->ip = '192.168.1.1';
                                    $new_event->save();
                                }*/
                devcpt_log_system('course', 'lms/course/view.php?id=' . $course->id, 'restore', 'Restore course: ' . $course->shortname);
                updateLastModification('restore', $course->id);

                $response->status = true;
                $response->message = __('thao_tac_thanh_cong');
            } else {
                $response->status = false;
                $response->message = __('thao_tac_khong_thanh_cong');
            }
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    //api lọc thông tin người dùng đang được ghi danh vào khóa học
    public function apiUserCurrentEnrol(Request $request)
    {

        $course_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $role_id = $request->input('role_id');
        $organization_id = $request->input('organization_id');


        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'role_id' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }


        //lấy danh sách học viên/giáo viên đang được enrol vào khóa học hiện tại
        $currentUserEnrol = DB::table('mdl_user_enrolments')
            ->join('mdl_user', 'mdl_user.id', '=', 'mdl_user_enrolments.userid')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
            ->where('mdl_course.id', '=', $course_id)
            ->where('mdl_enrol.enrol', '<>', 'self')
            ->select('mdl_user.id', 'mdl_user.username', 'tms_user_detail.fullname', 'mdl_user.firstname', 'mdl_user.lastname', 'mdl_enrol.id as enrol_id');
        if ($keyword) {
            $currentUserEnrol = $currentUserEnrol->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

//        if (strlen($organization_id) != 0 && $organization_id != 0) {
//            $currentUserEnrol = $currentUserEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
//            $currentUserEnrol = $currentUserEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
//        }

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_organization tor on tor.id = toe.organization_id
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';

            $org_query = DB::raw($org_query);

            $currentUserEnrol = $currentUserEnrol->join($org_query, 'org_tp.org_uid', '=', 'mdl_user.id');

//            $currentUserEnrol = $currentUserEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
//            $currentUserEnrol = $currentUserEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
        }

        if ($role_id) {
            $currentUserEnrol = $currentUserEnrol->where('mdl_enrol.roleid', '=', $role_id);
        }


        $currentUserEnrol = $currentUserEnrol->orderBy('mdl_enrol.id', 'desc');

        $currentUserEnrol = $currentUserEnrol->paginate($row);
        $total = ceil($currentUserEnrol->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $currentUserEnrol->currentPage(),
            ],
            'data' => $currentUserEnrol
        ];

        return response()->json($response);
    }

    public function apiUserCurrentInvite(Request $request)
    {
        $course_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $organization_id = $request->input('organization_id');
        $invite_status = $request->input('invite_status');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'role_id' => 'number',
            'keyword' => 'text',
            'invite_status' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên đang được enrol vào khóa học hiện tại
        $currentUserEnrol = DB::table('tms_invitation')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_invitation.user_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->where('tms_invitation.course_id', '=', $course_id)
            ->select(
                'mdl_user.id',
                'mdl_user.username',
                'mdl_user.firstname',
                'mdl_user.lastname',
                'tms_user_detail.fullname',
                'tms_invitation.replied',
                'tms_invitation.accepted',
                'tms_invitation.reason'
            );
        if ($keyword) {
            $currentUserEnrol = $currentUserEnrol->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        if ($invite_status == 'noreply') {
            $currentUserEnrol = $currentUserEnrol->where('tms_invitation.replied', 0);
        } elseif ($invite_status == 'accepted') {
            $currentUserEnrol = $currentUserEnrol->where('tms_invitation.replied', 1)->where('tms_invitation.accepted', 1);
        } elseif ($invite_status == 'denied') {
            $currentUserEnrol = $currentUserEnrol->where('tms_invitation.replied', 1)->where('tms_invitation.accepted', 0);
        }

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $currentUserEnrol = $currentUserEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
            $currentUserEnrol = $currentUserEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
        }

        $currentUserEnrol = $currentUserEnrol->paginate($row);
        $total = ceil($currentUserEnrol->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $currentUserEnrol->currentPage(),
            ],
            'data' => $currentUserEnrol
        ];

        return response()->json($response);
    }


    //api lấy danh sách người dùng ngoại lệ được vào khóa học
    public function apiUserCourseException(Request $request)
    {
        $course_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $organization_id = $request->input('organization_id');
//        $invite_status = $request->input('invite_status');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'role_id' => 'number',
            'keyword' => 'text',
//            'invite_status' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên đang được enrol vào khóa học hiện tại
        $currentUserCourseException = DB::table('tms_user_course_exception')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_course_exception.user_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->where('tms_user_course_exception.course_id', '=', $course_id)
            ->select(
                'mdl_user.id',
                'mdl_user.username',
                'mdl_user.firstname',
                'mdl_user.lastname',
                'tms_user_detail.fullname'
            );
        if ($keyword) {
            $currentUserCourseException = $currentUserCourseException->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $currentUserCourseException = $currentUserCourseException->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
            $currentUserCourseException = $currentUserCourseException->where('tms_organization_employee.organization_id', '=', $organization_id);
        }

        $currentUserCourseException = $currentUserCourseException->paginate($row);
        $total = ceil($currentUserCourseException->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $currentUserCourseException->currentPage(),
            ],
            'data' => $currentUserCourseException
        ];

        return response()->json($response);
    }


    //api lấy danh sách người dùng cần ghi danh
    //ThoLD 14/09/2019
    public $courseCurrent_id;
    public $category_id;

    public function apiUserNeedEnrol(Request $request)
    {
        $this->courseCurrent_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $role_id = $request->input('role_id');
        $organization_id = $request->input('organization_id');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'role_id' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên chưa được enrol vào khóa học hiện tại
        $userNeedEnrol = DB::table('model_has_roles')
            ->join('mdl_user', 'mdl_user.id', '=', 'model_has_roles.model_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('mdl_user.deleted', '=', 0)
            ->select('mdl_user.id', 'mdl_user.username', 'tms_user_detail.fullname', 'mdl_user.firstname', 'mdl_user.lastname', 'roles.name as rolename')
            ->whereNotExists(function ($query) use ($role_id) {
                $query->select(DB::raw(1))
                    ->from('mdl_user_enrolments')
                    ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                    ->where('mdl_course.id', '=', $this->courseCurrent_id)
                    ->whereRaw('mdl_user_enrolments.userid = mdl_user.id');

//                if ($role_id == 4) { //Instructor => cho phép lấy ra, để có thể overwrite vào role học viên đã từng tạo
//                    $query->where('mdl_enrol.roleid', '=', $role_id);
//                }

            });

        if ($keyword) {
            $userNeedEnrol = $userNeedEnrol->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        //Lấy user trực thuộc tổ chức
//        if (strlen($organization_id) != 0 && $organization_id != 0) {
//            $userNeedEnrol = $userNeedEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
//            $userNeedEnrol = $userNeedEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
//        }

        //Lấy user thuộc tổ chức và các tổ chức đệ quy của nó
        if (strlen($organization_id) != 0 && $organization_id != 0) {

            $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                     join tms_organization tor on tor.id = toe.organization_id
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $organization_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $organization_id . '
                            ) as org_tp';

            $org_query = DB::raw($org_query);

            $userNeedEnrol = $userNeedEnrol->join($org_query, 'org_tp.org_uid', '=', 'mdl_user.id');

//            $userNeedEnrol = $userNeedEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
//            $userNeedEnrol = $userNeedEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
        }


        if ($role_id) {
            $userNeedEnrol = $userNeedEnrol->where('roles.id', '=', $role_id);
        }

        $userNeedEnrol = $userNeedEnrol->orderBy('mdl_user.id', 'desc');

        $userNeedEnrol = $userNeedEnrol->paginate($row);
        $total = ceil($userNeedEnrol->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $userNeedEnrol->currentPage(),
            ],
            'data' => $userNeedEnrol
        ];

        return response()->json($response);
    }

    public function apiUserNeedInvite(Request $request)
    {
        $this->courseCurrent_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $role_id = $request->input('role_id');
        $organization_id = $request->input('organization_id');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'role_id' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //xử lý lấy dữ liệu người dùng bên diva
        //        if ($keyword) {
        //            \DB::beginTransaction();
        //            $checkUser = MdlUser::where('username', $keyword);
        //            $checkUser = $checkUser->where('deleted', 0)->first();
        //            if (!$checkUser) {
        //
        //                $url = Config::get('constants.domain.DIVA') . 'user/search?term=' . $keyword;
        //
        //                $result = callAPI('GET', $url, '', true, '');
        //
        //                $result = json_decode($result, true);
        //
        //                if ($result['code'] == 0 && !empty($result['data'])) {
        //
        //                    $mdl_user = new MdlUser();
        //                    insert_user_search($result, $mdl_user, 'Bgt@2109');
        //
        //                    //Assign TMS
        //                    //mặc định add tài khoản từ diva có quyền học viên
        //                    $modelHasRole = ModelHasRole::where([
        //                        'role_id' => 5,
        //                        'model_id' => $mdl_user->id
        //                    ])->first();
        //                    if (!$modelHasRole) {
        //                        $userRole = new ModelHasRole;
        //                        $userRole->role_id = 5;
        //                        $userRole->model_id = $mdl_user->id;
        //                        $userRole->model_type = 'App/MdlUser';
        //                        $userRole->save();
        //
        //                        enrole_lms($mdl_user->id, 5, 0); //mặc định set người dùng diva có quyền học viên
        //                    }
        //                }
        //            }
        //            \DB::commit();
        //        }


        //lấy danh sách học viên chưa được enrol vào khóa học hiện tại
        $userNeedEnrol = DB::table('model_has_roles')
            ->join('mdl_user', 'mdl_user.id', '=', 'model_has_roles.model_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('mdl_user.deleted', '=', 0)
            ->select('mdl_user.id', 'mdl_user.username', 'tms_user_detail.fullname', 'mdl_user.firstname', 'mdl_user.lastname', 'roles.name as rolename')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('mdl_user_enrolments')
                    ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                    ->where('mdl_course.id', '=', $this->courseCurrent_id)
                    ->whereRaw('mdl_user_enrolments.userid = mdl_user.id');
            })->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_invitation')
                    ->where('course_id', '=', $this->courseCurrent_id)
                    ->whereRaw('tms_invitation.user_id = mdl_user.id');
            });

        if ($keyword) {
            $userNeedEnrol = $userNeedEnrol->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $userNeedEnrol = $userNeedEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
            $userNeedEnrol = $userNeedEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
        }

        if ($role_id) {
            $userNeedEnrol = $userNeedEnrol->where('roles.id', '=', $role_id);
        }

        $userNeedEnrol = $userNeedEnrol->orderBy('mdl_user.id', 'desc');

        $userNeedEnrol = $userNeedEnrol->paginate($row);
        $total = ceil($userNeedEnrol->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $userNeedEnrol->currentPage(),
            ],
            'data' => $userNeedEnrol
        ];

        return response()->json($response);
    }

    public function apiUserNeedInviteToException(Request $request)
    {
        $this->courseCurrent_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $role_id = $request->input('role_id');
        $organization_id = $request->input('organization_id');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'role_id' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //lấy danh sách học viên chưa được enrol vào khóa học hiện tại
        $userNeedEnrol = DB::table('model_has_roles')
            ->join('mdl_user', 'mdl_user.id', '=', 'model_has_roles.model_id')
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('mdl_user.deleted', '=', 0)
            ->select('mdl_user.id', 'mdl_user.username', 'tms_user_detail.fullname', 'mdl_user.firstname', 'mdl_user.lastname', 'roles.name as rolename')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_user_course_exception')
                    ->where('course_id', '=', $this->courseCurrent_id)
                    ->whereRaw('tms_user_course_exception.user_id = mdl_user.id');
            });
        if ($keyword) {
            $userNeedEnrol = $userNeedEnrol->where(function ($query) use ($keyword) {
                $query->where('mdl_user.username', 'like', '%' . $keyword . '%')
                    ->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%");
            });
        }

        if (strlen($organization_id) != 0 && $organization_id != 0) {
            $userNeedEnrol = $userNeedEnrol->join('tms_organization_employee', 'mdl_user.id', '=', 'tms_organization_employee.user_id');
            $userNeedEnrol = $userNeedEnrol->where('tms_organization_employee.organization_id', '=', $organization_id);
        }

        if ($role_id) {
            $userNeedEnrol = $userNeedEnrol->where('roles.id', '=', $role_id);
        }

        $userNeedEnrol = $userNeedEnrol->orderBy('mdl_user.id', 'desc');

        $userNeedEnrol = $userNeedEnrol->paginate($row);
        $total = ceil($userNeedEnrol->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $userNeedEnrol->currentPage(),
            ],
            'data' => $userNeedEnrol
        ];

        return response()->json($response);
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    public function apiGetListCategory()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->where('mdl_course_categories.id', '!=', 2)
            //->where('mdl_course_categories.id', '!=', 3)
            ->where('mdl_course_categories.id', '!=', 5)
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    public function apiGetListCategoryForClone()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->whereNotIn('mdl_course_categories.id', [2, 5])
//            ->where('mdl_course_categories.id', '!=', 5) //ko lay danh muc khoa
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }


    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    public function apiGetListCategoryForEdit()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->where('mdl_course_categories.id', '!=', 2)
            ->where('mdl_course_categories.id', '!=', 5)
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }


    //api lấy danh sách danh mục khóa học cho chức năng restore
    //hiển hị dưới view create và edit course
    public function apiGetListCategoryRestore()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }

    //api enrol học viên vào khóa học
    //ThoLD 15/09/2019
    public function apiEnrolUser(Request $request)
    {
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $role_id = $request->input('role_id');
            $lstUserIDs = $request->input('Users');

            $param = [
                'course_id' => 'number',
                'role_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            $course = MdlCourse::findOrFail($course_id);

            if (empty($course)) {
                $response->status = false;
                $response->message = __('khong_tim_thay_khoa_hoc');
                return json_encode($response);
            }

            //Update performance 02/03/2020 by cuonghq
            enrole_user_to_course_multiple($lstUserIDs, $role_id, $course_id, true);

//            $count_user = count($lstUserIDs);
//            if ($count_user > 0) {
//                \DB::beginTransaction();
//                for ($i = 0; $i < $count_user; $i++) {
//
//                    enrole_user_to_course($lstUserIDs[$i], $role_id, $course_id, $course->category);
//                    sleep(0.01);
//
//                    insert_single_notification(\App\TmsNotification::MAIL, $lstUserIDs[$i], \App\TmsNotification::ENROL, $course_id);
//                }
//                \DB::commit();
//            }

            $response->status = true;
            $response->message = __('ghi_danh_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    public function apiInviteUser(Request $request)
    {
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $lstUserIDs = $request->input('users');

            $param = [
                'course_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            $course = MdlCourse::find($course_id);

            if (empty($course)) {
                $response->status = false;
                $response->message = __('khong_tim_thay_khoa_hoc');
                return json_encode($response);
            }

            $insert_data = [];
            $dt = Carbon::now();

            $send = false;

            if (count($lstUserIDs) == 1 && TmsInvitation::where('user_id', '=', $lstUserIDs[0])
                    ->where('course_id', '=', $course_id)->first()) {
                $send = true;
            }

            foreach ($lstUserIDs as $user_id) {
                $checkInvitation = TmsInvitation::where('user_id', '=', $user_id)
                    ->where('course_id', '=', $course_id)->first();
                if (!$checkInvitation) {
                    $insert_data[] = [
                        'course_id' => $course_id,
                        'user_id' => $user_id,
                        'replied' => 0,
                        'accepted' => 0,
                        'created_at' => $dt->toDateTimeString()
                    ];
                }
            }
            if (!empty($insert_data)) {
                TmsInvitation::insert($insert_data);
            }

            $response->status = true;
            $response->send = $send;
            $response->message = __('them_vao_danh_sach_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->send = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    //api enrol học viên vào khóa học
    //ThoLD 15/09/2019
    public function apiRemoveEnrolUser(Request $request)
    {
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $role_id = $request->input('role_id');
            $lstUserIDs = $request->input('Users');
            $param = [
                'course_id' => 'number',
                'role_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }


            $count_user = count($lstUserIDs);
            if ($count_user > 0) {
                \DB::beginTransaction();
                for ($i = 0; $i < $count_user; $i++) {
                    remove_enrole_user_to_course($lstUserIDs[$i], $role_id, $course_id);
                    sleep(0.01);
                }
                \DB::commit();
            }

            $response->status = true;
            $response->message = __('huy_ghi_danh_khoa_hoc');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    public function apiRemoveInviteUser(Request $request)
    {
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $lstUserIDs = $request->input('users');
            $param = [
                'course_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            TmsInvitation::whereIn('user_id', $lstUserIDs)->where('course_id', $course_id)->delete();

            $response->status = true;
            $response->message = __('xoa_khoi_danh_sach_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    public function apiEnrolUserException(Request $request)
    {
        self::apiInviteUser($request);
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $lstUserIDs = $request->input('users');

            $param = [
                'course_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            $course = MdlCourse::find($course_id);

            if (empty($course)) {
                $response->status = false;
                $response->message = __('khong_tim_thay_khoa_hoc');
                return json_encode($response);
            }

            $insert_data = [];
            $dt = Carbon::now();

            $send = false;

            if (count($lstUserIDs) == 1 && TmsUserCourseException::where('user_id', '=', $lstUserIDs[0])
                    ->where('course_id', '=', $course_id)->first()) {
                $send = true;
            }

            foreach ($lstUserIDs as $user_id) {
                $checkInvitation = TmsUserCourseException::where('user_id', '=', $user_id)
                    ->where('course_id', '=', $course_id)->first();
                if (!$checkInvitation) {
                    $insert_data[] = [
                        'course_id' => $course_id,
                        'user_id' => $user_id,
                        'created_at' => $dt->toDateTimeString()
                    ];
                }
            }
            if (!empty($insert_data)) {
                TmsUserCourseException::insert($insert_data);
            }

            $response->status = true;
            $response->send = $send;
            $response->message = __('them_vao_danh_sach_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->send = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    public function apiRemoveUserException(Request $request)
    {
        self::apiRemoveInviteUser($request);
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $lstUserIDs = $request->input('users');
            $param = [
                'course_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            TmsUserCourseException::whereIn('user_id', $lstUserIDs)->where('course_id', $course_id)->delete();

            $response->status = true;
            $response->message = __('xoa_khoi_danh_sach_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    public function apiInvitationDetail($id)
    {
        $invitation = TmsInvitation::with('course')->where('id', $id)->first();

        return response()->json($invitation);
    }

    public function apiInvitationConfirm(Request $request)
    {
        $id = $request->input('id');
        $accepted = $request->input('accepted');
        $reason = $request->input('reason');
        DB::beginTransaction();
        try {
            $invitation = TmsInvitation::query()->where('id', $id)->first();
            if (isset($invitation)) {
                $invitation->replied = 1;
                if ($accepted == 'false') {
                    $invitation->accepted = 0;
                    $invitation->reason = $reason;
                    $accepted_bool = false;
                } else {
                    $invitation->accepted = 1;
                    $accepted_bool = true;
                }
                $invitation->save();
                if ($accepted_bool) {
                    //Enrol user vào khóa
                    enrole_user_to_course_multiple([$invitation->user_id], Role::ROLE_STUDENT, $invitation->course_id, true);
                }
            }
            DB::commit();
            $data['status'] = 'success';
            $data['message'] = __('xac_nhan_thanh_cong');
        } catch (\Exception $e) {
            DB::rollBack();
            $data['status'] = 'error';
            $data['message'] = __('xac_nhan_that_bai');
        }
        return response()->json($data);

    }

    //api lấy danh sách khóa học
    public function getall(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $category_id = $request->input('category_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $status_course = $request->input('status_course');
        $sample = $request->input('sample'); //field xác định giá trị là khóa học mẫu hay không
        //Tích hợp phân quyền dữ liệu => Move to apiGetListCoursePermissionData
        //$role_id = $request->input('role_id'); //quyền hệ thống
        //$is_excluded = $request->input('is_excluded'); //đã gán vào quyền hay chưa

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'category_id' => 'number',
            'sample' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $ready = false;
//        if (strlen($role_id) != 0) {
//            $listCourses = DB::table('mdl_course as c')
//                ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'c.id')
//                ->join('mdl_course_categories as mc', 'mc.id', '=', 'c.category')
//                ->select(
//                    'c.id',
//                    'c.fullname',
//                    'c.shortname',
//                    'c.startdate',
//                    'c.enddate',
//                    'c.visible',
//                    'mccc.gradepass as pass_score'
//                );
//            if (strlen($is_excluded) != 0) {
//                if ($is_excluded == 1) { //List khóa học chưa phân quyền cho role này
//                    $listCourses = $listCourses->whereNotIn('c.id', function ($query) use ($role_id) {
//                        $query->select('course_id')
//                            ->from('tms_role_course')
//                            ->where('role_id', $role_id);
//                    });
//                } else { //List khóa học đã phân quyền cho role này
//                    $listCourses = $listCourses->whereIn('c.id', function ($query) use ($role_id) {
//                        $query->select('course_id')
//                            ->from('tms_role_course')
//                            ->where('role_id', $role_id);
//                    });
//                }
//            }
//            $ready =  true;
//        }
//        else {


        $select = [];

        //Nếu có quyền admin hoặc root hoặc có quyền System administrator thì được phép xem tất cả
        if (tvHasRoles(\Auth::user()->id, ["admin", "root"]) or slug_can('tms-system-administrator-grant')) {
            //Không thuộc các trường hợp trên
            $listCourses = DB::table('mdl_course as c')
                ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'c.id')
                ->join('mdl_course_categories as mc', 'mc.id', '=', 'c.category');

            $select = [
                'c.id',
                'c.fullname',
                'c.shortname',
                'c.startdate',
                'c.enddate',
                'c.visible',
                'c.category',
                'c.deleted',
                'mccc.gradepass as pass_score'
            ];
        } else {
            //Kiểm tra xem có phải role thuộc organization hay không
//            $checkRoleOrg = tvHasRoles(\Auth::user()->id, ["manager", "leader", "employee"]);
//            if ($checkRoleOrg === true) {
            $checkRoleOrg = tvHasOrganization(\Auth::user()->id);
            if ($checkRoleOrg != 0) {
                $listCourses = DB::table('mdl_course as c')
                    ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'c.id')
                    ->where(function ($query) {
                        /* @var $query Builder */
                        $query
                            ->whereIn('c.id', function ($q2) { //organization
                                /* @var $q2 Builder */
                                $q2->select('mdl_course.id')
                                    ->from('tms_organization_employee')
                                    ->join('tms_role_organization', 'tms_organization_employee.organization_id', '=', 'tms_role_organization.organization_id')
                                    ->join('tms_role_course', 'tms_role_organization.role_id', '=', 'tms_role_course.role_id')
                                    ->join('mdl_course', 'tms_role_course.course_id', '=', 'mdl_course.id')
                                    ->where('tms_organization_employee.user_id', '=', \Auth::user()->id);
                            })
                            ->orWhereIn('c.id', function ($q1) { //được enrol
                                /* @var $q1 Builder */
                                $q1->select('mdl_course.id')
                                    ->from('mdl_user_enrolments as mue')
                                    //->join('mdl_enrol as e', 'mue.enrolid', '=', 'e.id')
                                    ->join('mdl_enrol as e', function ($join) {
                                        $join->on('mue.enrolid', '=', 'e.id');
                                        $join->where('e.roleid', '=', Role::ROLE_TEACHER); //role teacher
                                    })
                                    ->join('mdl_course', 'e.courseid', '=', 'mdl_course.id')
                                    ->where('mue.userid', '=', \Auth::user()->id);
                            })
                            ->orWhereIn('c.id', function ($q3) { //cac khoa nam trong to chuc khac neu co
                                /* @var $q1 Builder */
                                $q3->select('mdl_course.id')
                                    ->from('tms_user_organization_course_exceptions as tuoce')
                                    ->join('mdl_course', 'tuoce.course_id', '=', 'mdl_course.id')
                                    ->where('tuoce.user_id', '=', \Auth::user()->id);
                            });
                    });
                $select = [
                    'c.id',
                    'c.fullname',
                    'c.shortname',
                    'c.startdate',
                    'c.enddate',
                    'c.visible',
                    'c.category',
                    'c.deleted',
                    'mccc.gradepass as pass_score'
                ];
            } else {
                //Kiểm tra xem có phải role teacher hay không
                $checkRole = tvHasRole(\Auth::user()->id, "teacher");
                if ($checkRole === TRUE) {
                    $listCourses = DB::table('mdl_user_enrolments as mue')
                        ->where('mue.userid', '=', \Auth::user()->id)
                        ->join('mdl_enrol as e', 'mue.enrolid', '=', 'e.id')
                        ->join('mdl_course as c', 'e.courseid', '=', 'c.id')
                        ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'c.id');
                    $select = [
                        'c.id',
                        'c.fullname',
                        'c.shortname',
                        'c.startdate',
                        'c.enddate',
                        'c.visible',
                        'c.category',
                        'c.deleted',
                        'mccc.gradepass as pass_score'
                    ];
//                    $ready = true;
                }
            }
        }

        //Get last modify => Quá chậm so với query thuần, mặc dù đã paginated, do dữ liệu bảnh log quá nhiều => sử dụng trigger mdl_logstore_standard_log để cập nhật bảng mdl_course
//        $listCourses->leftJoin('mdl_logstore_standard_log', function($q) {
//            $q->on('c.id', '=', 'mdl_logstore_standard_log.contextinstanceid');
//            $q->whereIn('mdl_logstore_standard_log.id', function($query) {
//                $query
//                    //->max('mdl_logstore_standard_log.id') //Eloquent only
//                    ->select(DB::raw('MAX(mlsl.id)')) //Query builder normal
//                    ->from('mdl_logstore_standard_log as mlsl')
//                    ->join('mdl_course as mc', 'mlsl.contextinstanceid', '=', 'mc.id')
//                    ->where('mlsl.action', '<>', 'viewed')
//                    ->groupBy('mc.id');
//            });
//        });
//        $select[] = 'mdl_logstore_standard_log.timecreated';
//        $select[] = 'mdl_logstore_standard_log.userid';
        //Get last modify query raw
        /*        select
                `c`.`id`,
                `c`.`fullname`,
                `c`.`shortname`,
                `c`.`startdate`,
                `c`.`enddate`,
                `c`.`visible`,
                `c`.`category`,
                `c`.`deleted`,
                `mccc`.`gradepass` as `pass_score`,
                `mdl_logstore_standard_log`.`timecreated`,
                `mdl_logstore_standard_log`.`userid`
                from `mdl_course` as `c`
                left join `mdl_course_completion_criteria` as `mccc` on `mccc`.`course` = `c`.`id`
                inner join `mdl_course_categories` as `mc` on `mc`.`id` = `c`.`category`
                left join `mdl_logstore_standard_log` on `c`.`id` = `mdl_logstore_standard_log`.`contextinstanceid` and `mdl_logstore_standard_log`.`id` in (
                        select MAX(mlsl.id)
                from `mdl_logstore_standard_log` as `mlsl`
                inner join `mdl_course` as `mc` on `mlsl`.`contextinstanceid` = `mc`.`id`
                where `mlsl`.`action` <> 'viewed'
                group by `mc`.`id`
                )
                limit 10*/

        //Get last modification info by trigger
        $listCourses->leftJoin('mdl_user', 'c.last_modify_user', '=', 'mdl_user.id');
        //$select[] = 'mdl_course.last_modify_user';
        $select[] = 'mdl_user.username';
        $select[] = DB::raw("DATE_FORMAT(FROM_UNIXTIME(`c`.`last_modify_time`), '%Y-%m-%d %H:%i:%s') as last_modify_time");
        $select[] = 'c.last_modify_action';
        $listCourses->select($select);

        //}
//        if (!$ready) {
//            //Không thuộc các trường hợp trên
//            $listCourses = DB::table('mdl_course as c')
//                ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'c.id')
//                ->join('mdl_course_categories as mc', 'mc.id', '=', 'c.category')
//                ->select(
//                    'c.id',
//                    'c.fullname',
//                    'c.shortname',
//                    'c.startdate',
//                    'c.enddate',
//                    'c.visible',
//                    'c.category',
//                    'c.deleted',
//                    'mccc.gradepass as pass_score'
//                );
//        }

        //là khóa học mẫu
        if ($sample == 1) {
            $listCourses = $listCourses->where('c.category', '=', 2); //2 là khóa học mẫu
        } else { // là khóa online
            $listCourses = $listCourses->where('c.category', '!=', 2);
            $listCourses = $listCourses->where('c.category', '!=', 5);
        }
        $listCourses = $listCourses->where('c.deleted', '=', 0);


        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $listCourses = $listCourses->whereRaw('( c.fullname like "%' . $keyword . '%" OR c.shortname like "%' . $keyword . '%" )');
        }

        if ($category_id) {
            $listCourses = $listCourses->where('c.category', '=', $category_id);
        }

        if ($startdate) {
            $cv_startDate = strtotime($startdate);
            $listCourses = $listCourses->where('c.startdate', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate);
            $listCourses = $listCourses->where('c.enddate', '<=', $cv_endDate);
        }


        if ($status_course) {
            $unix_now = strtotime(Carbon::now());
            if ($status_course == 1) { //các khóa sắp diễn ra
                $listCourses = $listCourses->where('c.startdate', '>', $unix_now);
            } else if ($status_course == 2) { //các khóa đang diễn ra
                $listCourses = $listCourses->where('c.startdate', '<=', $unix_now);
                $listCourses = $listCourses->where(function ($query) use ($unix_now) {
                    $query->where('c.enddate', '>=', $unix_now)
                        ->orWhere('c.enddate', '=', 0);
                });
//                $listCourses = $listCourses->where('c.enddate', '>=', $unix_now);
            } else if ($status_course == 3) { //các khóa đã diễn ra
                $listCourses = $listCourses->where('c.enddate', '<', $unix_now)
                    ->where('c.enddate', '>', 0);
            }
        }

        $totalCourse = count($listCourses->get()); //lấy tổng số khóa học hiện tại
        // if ($keyword) {
        //     $listCourses = $listCourses->orderBy('c.shortname', 'desc');
        // } else {
        //     $listCourses = $listCourses->orderBy('c.id', 'desc');
        // }
        // $listCourses = $listCourses->orderBy('c.id', 'desc');
        // Get list course order by code course (shortname) <= Ngongoc request [18.12.2020]
        $listCourses = $listCourses->orderBy('c.shortname', 'desc');

        if ($row == 0) {
            return $listCourses->get();
        }

        $listCourses = $listCourses->paginate($row);
        $total = ceil($listCourses->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listCourses->currentPage(),
            ],
            'data' => $listCourses,
            'total_course' => $totalCourse
        ];


        return response()->json($response);
    }

    public function getAllPermissionData(Request $request)
    {
        // TODO: Implement getall() method.
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $category_id = $request->input('category_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $status_course = $request->input('status_course');
        $sample = $request->input('sample'); //field xác định giá trị là khóa học mẫu hay không
        //Tích hợp phân quyền dữ liệu
        $role_id = $request->input('role_id'); //quyền hệ thống
        $is_excluded = $request->input('is_excluded'); //đã gán vào quyền hay chưa

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'category_id' => 'number',
            'sample' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listCourses = DB::table('mdl_course as c')
            ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'c.id')
            ->join('mdl_course_categories as mc', 'mc.id', '=', 'c.category')
            ->select(
                'c.id',
                'c.fullname',
                'c.shortname',
                'c.startdate',
                'c.enddate',
                'c.visible',
                'mccc.gradepass as pass_score'
            );
        if (strlen($is_excluded) != 0) {
            if ($is_excluded == 1) { //List khóa học chưa phân quyền cho role này
                $listCourses = $listCourses->whereNotIn('c.id', function ($query) use ($role_id) {
                    $query->select('course_id')
                        ->from('tms_role_course')
                        ->where('role_id', $role_id);
                });
            } else { //List khóa học đã phân quyền cho role này
                $listCourses = $listCourses->whereIn('c.id', function ($query) use ($role_id) {
                    $query->select('course_id')
                        ->from('tms_role_course')
                        ->where('role_id', $role_id);
                });
            }
        }

        //là khóa học mẫu
        if ($sample == 1) {
            $listCourses = $listCourses->where('c.category', '=', 2); //2 là khóa học mẫu
        } else {
            $listCourses = $listCourses->where('c.category', '!=', 2);
//            $listCourses = $listCourses->where('c.category', '!=', 5);
        }
        $listCourses = $listCourses->where('c.deleted', '=', 0);

        $totalCourse = count($listCourses->get()); //lấy tổng số khóa học hiện tại

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $listCourses = $listCourses->whereRaw('( c.fullname like "%' . $keyword . '%" OR c.shortname like "%' . $keyword . '%" )');
        }

        if ($category_id) {
            $listCourses = $listCourses->where('c.category', '=', $category_id);
        }

        if ($startdate) {
            $cv_startDate = strtotime($startdate);
            $listCourses = $listCourses->where('c.startdate', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate);
            $listCourses = $listCourses->where('c.enddate', '<=', $cv_endDate);
        }

        if ($status_course) {
            $unix_now = strtotime(Carbon::now());
            if ($status_course == 1) { //các khóa sắp diễn ra
                $listCourses = $listCourses->where('c.startdate', '>', $unix_now);
            } else if ($status_course == 2) { //các khóa đang diễn ra
                $listCourses = $listCourses->where('c.startdate', '<=', $unix_now);
                $listCourses = $listCourses->where('c.enddate', '>=', $unix_now);
            } else if ($status_course == 3) { //các khóa đã diễn ra
                $listCourses = $listCourses->where('c.enddate', '<', $unix_now);
            }
        }


        $listCourses = $listCourses->orderBy('id', 'desc');

        $listCourses = $listCourses->paginate($row);
        $total = ceil($listCourses->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listCourses->currentPage(),
            ],
            'data' => $listCourses,
            'total_course' => $totalCourse
        ];


        return response()->json($response);
    }

    public function store(Request $request)
    {
        $avatar = $request->file('file');
        $fullname = $request->input('fullname');
        $shortname = $request->input('shortname');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $pass_score = $request->input('pass_score');
        $description = $request->input('description');
        $category_id = $request->input('category_id');
        $sample = $request->input('sample');
        $course_place = $request->input('course_place');
        $allow_register = $request->input('allow_register');
        $total_date_course = $request->input('total_date_course');
        $is_end_quiz = $request->input('is_end_quiz');
        $estimate_duration = $request->input('estimate_duration');
        $course_budget = $request->input('course_budget');
        $access_ip_string = $request->input('access_ip');
        $is_toeic = $request->input('is_toeic');
        $library_code = $request->input('selected_org');

        //thực hiện insert dữ liệu
        if ($avatar) {
            $name_file = str_replace(' ', '', $shortname);
            $name_file = str_replace('/', '', $name_file);
            $name_file = str_replace('\\', '', $name_file);
            $name_file = utf8convert($name_file);
            $name = $name_file . '.' . $avatar->getClientOriginalExtension();

            // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
            Storage::putFileAs(
                'public/upload/course',
                $avatar,
                $name
            );

            $path_avatar = '/storage/upload/course/' . $name;
        } else {
            $path_avatar = '/storage/upload/course/default_course.jpg';
        }

        $course = new MdlCourse(); //khởi tạo theo cách này để tránh trường hợp insert startdate và endate bị set về 0
        $course->category = $category_id;
        $course->shortname = $shortname;
        $course->fullname = $fullname;
        $course->summary = $description;
        $course->is_toeic = $is_toeic;
        $course->course_avatar = $path_avatar;
        if ($sample == 1) {
            $course->startdate = strtotime(Carbon::now());
            $course->enddate = strtotime(Carbon::now()->addYear(100)); // gia hạn thời gian cho khóa học mẫu là 100 năm
            $course->visible = 1;  //luôn hiển thị khi là khóa học mẫu
        } else {
            $stdate = strtotime($startdate);
            $eddate = !is_null($enddate) ? strtotime($enddate) : 0;
            $course->course_place = $course_place;
            $course->startdate = $stdate;
            $course->enddate = $eddate;
            $course->visible = 0;
        }

        if ($category_id == 3) {
            $course->is_certificate = 1;
            $course->is_end_quiz = $is_end_quiz;
        }

        $course->total_date_course = $total_date_course;

        $course->allow_register = $allow_register;
        $course->enablecompletion = 1;
        $course->estimate_duration = $estimate_duration;
        $course->course_budget = $course_budget;

        //access_ip
        $access_ip = $this->spitIP($access_ip_string);

        $course->access_ip = $access_ip;

        $course->save();
        //nếu manager hoặc leader tạo khóa
        $checkRole = tvHasRoles(\Auth::user()->id, ["manager", "leader", "employee"]);
        if ($checkRole == true) {
            $organization_employee = TmsOrganizationEmployee::query()->where('user_id', '=', \Auth::user()->id)->first();
            if (isset($organization_employee)) {
                if ($organization_employee->organization) {

                    $role_organization = TmsRoleOrganization::query()->where('organization_id', $organization_employee->organization_id)->first();
                    if (isset($role_organization)) { //Map course to that roles
//                        $role_course = new TmsRoleCourse();
//                        $role_course->role_id = $role_organization->role_id;
//                        $role_course->course_id = $course->id;
//                        $role_course->save();
                        TmsRoleCourse::firstOrCreate([
                            'role_id' => $role_organization->role_id,
                            'course_id' => $course->id
                        ]);
                    } else { //Enable use organization as role and map course to that role

                        $lastRole = MdlRole::query()->orderBy('sortorder', 'desc')->first();
                        //Tạo quyền bên LMS
                        if (isset($lastRole)) {
                            $sortorder = $lastRole['sortorder'] + 1;
                        } else {
                            $sortorder = 1;
                        }

                        $mdlRole = MdlRole::firstOrCreate([
                            'shortname' => $organization_employee->organization->code,
                            'archetype' => 'user'
                        ], [
                            'description' => $organization_employee->organization->name,
                            'sortorder' => $sortorder
                        ]);

                        $role = Role::firstOrCreate([
                            'mdl_role_id' => $mdlRole->id,
                            'name' => $organization_employee->organization->code,
                            'guard_name' => 'web',
                            'status' => 1
                        ], [
                            'description' => $organization_employee->organization->name
                        ]);

                        $role_organization = TmsRoleOrganization::firstOrCreate([
                            'role_id' => $role->id,
                            'organization_id' => $organization_employee->organization_id
                        ]);

                        TmsRoleCourse::firstOrCreate([
                            'role_id' => $role_organization->role_id,
                            'course_id' => $course->id
                        ]);
                    }
                }
            }
        }

        if ($library_code){
            $userExcept = DB::table('tms_organization as tor')
                ->join('tms_user_organization_exceptions as tuoe', 'tuoe.organization_id', '=', 'tor.id')
                ->where('tor.code', '=', $library_code)
                ->select('tuoe.user_id', 'tor.id as org_id')->get();

            if ($userExcept) {
                $arr_data = [];
                $data_course = [];

                foreach ($userExcept as $user) {
                    $data_course['user_id'] = $user->user_id;
                    $data_course['organization_id'] = $user->org_id;
                    $data_course['course_id'] = $course->id;
                    $data_course['created_at'] = Carbon::now();
                    $data_course['updated_at'] = Carbon::now();

                    array_push($arr_data, $data_course);
                }

                //gan course cho user ngoai le quan ly
                TmsUserOrganizationCourseException::insert($arr_data);
            }
        }

        //insert dữ liệu điểm qua môn
        MdlCourseCompletionCriteria::create(array(
            'course' => $course->id,
            'criteriatype' => 6, //default là 6 trong trường hợp này
            'gradepass' => $pass_score
        ));

        $context_cate = MdlContext::where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSECAT)
            ->where('instanceid', '=', $category_id)->first();

        if ($context_cate) {
            //insert dữ liệu vào bảng mdl_context
            $mdl_context = MdlContext::firstOrCreate([
                'contextlevel' => \App\MdlUser::CONTEXT_COURSE,
                'instanceid' => $course->id,
                'depth' => 3,
                'locked' => 0
            ]);

            //cập nhật path
            $mdl_context->path = '/1/' . $context_cate->id . '/' . $mdl_context->id;
            $mdl_context->save();
        }


        if ($allow_register == 1) {
            MdlEnrol::firstOrCreate(
                [
                    'enrol' => 'self',
                    'courseid' => $course->id,
                    'roleid' => 5,
                    'sortorder' => 2,
                    'customint6' => 1
                ],
                [
                    'expirythreshold' => 86400,
                    'timecreated' => strtotime(Carbon::now()),
                    'timemodified' => strtotime(Carbon::now())
                ]
            );
        }

        //get info of role teacher
        $role_teacher = MdlRole::where('shortname', 'teacher')->first();
        //call function auto enrol to show list courses for teacher when teacher create a course
        enrole_user_to_course(Auth::user()->id, $role_teacher->id, $course->id, $course->category);

        //write data to table mdl_grade_categories -> muc dich phuc vu cham diem, Vinh PT yeu cau
        $mdl_grade_cate = MdlGradeCategory::firstOrCreate([
            'courseid' => $course->id,
            'depth' => 1,
            'aggregation' => 13,
            'aggregateonlygraded' => 1,
            'timecreated' => strtotime(Carbon::now()),
            'timemodified' => strtotime(Carbon::now())
        ]);

        $mdl_grade_cate->path = '/' . $mdl_grade_cate->id . '/';
        $mdl_grade_cate->save();

        //write data to table mdl_grade_items
        MdlGradeItem::firstOrCreate([
            'courseid' => $course->id,
            'itemname' => $course->fullname,
            'itemtype' => 'course',
            'iteminstance' => $mdl_grade_cate->id,
            'gradepass' => $pass_score
        ]);

        return $course;
    }

    public function spitIP($ip)
    {
        $access_ip = '{"list_access_ip":[';
        $splitAccessIP = "";
        if ($ip)
            $splitAccessIP = explode(',', $ip);
        if ($splitAccessIP) {
            foreach ($splitAccessIP as $ip) {
                $access_ip .= '"' . str_replace(' ', '', $ip) . '",';
            }
            $access_ip = rtrim($access_ip, ",");
        }
        $access_ip .= ']}';
        return $access_ip;
    }

    public function update(Request $request)
    {
        // TODO: Implement update() method.
    }

    public function updateCourse($id, Request $request)
    {
        // TODO: Implement updateCourse() method.

        $response = new ResponseModel();
        try {
            $avatar = $request->file('file');
            $fullname = $request->input('fullname');
            $shortname = $request->input('shortname');
            $startdate = $request->input('startdate');
            $enddate = $request->input('enddate');
            $pass_score = $request->input('pass_score');
            $description = $request->input('description');
            $category_id = $request->input('category_id');
            $course_place = $request->input('course_place');
            $offline = $request->input('offline');
            $allow_register = $request->input('allow_register');
            $total_date_course = $request->input('total_date_course');
            $is_end_quiz = $request->input('is_end_quiz');
            $estimate_duration = $request->input('estimate_duration');
            $course_budget = $request->input('course_budget');
            $access_ip_string = $request->input('access_ip');
            $is_toeic = $request->input('is_toeic');

            //thực hiện insert dữ liệu
            $param = [
                'shortname' => 'code',
                'description' => 'longtext',
                'pass_score' => 'positivenumber',
                'category_id' => 'number',
                'offline' => 'number',
                'course_place' => 'text',
                'allow_register' => 'number',
                'total_date_course' => 'number',
                'is_end_quiz' => 'number',
                'fullname' => 'text',
                'estimate_duration' => 'positivenumber',
                'course_budget' => 'positivenumber',
                'access_ip' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                $response->otherData = $validator;
                $response->error = $description;
                return response()->json($response);
            }


            $course = MdlCourse::findOrFail($id);

            if (!$course) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }


            //Check course code
            $checkCourse = MdlCourse::select('id')->whereNotIn('id', [$course->id])->where('shortname', $shortname)->first();

            if ($checkCourse) {
                $response->status = false;
                $response->message = __('ma_khoa_hoc_da_ton_tai');
                return response()->json($response);
            }


            //            $path_avatar = '';
            if ($avatar) {
                $name_file = str_replace(' ', '', $shortname);
                $name_file = str_replace('/', '', $name_file);
                $name_file = str_replace('\\', '', $name_file);
                $name_file = utf8convert($name_file);
                $name = $name_file . '.' . $avatar->getClientOriginalExtension();

                if (file_exists(storage_path('app/public/upload/course/' . $name))) {
                    Storage::delete('app/public/upload/course/' . $name);
                }

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/course',
                    $avatar,
                    $name
                );

                $path_avatar = '/storage/upload/course/' . $name;

                $course->course_avatar = $path_avatar;
            }

            if ($offline == 1) {
                $startdate = Carbon::createFromFormat('d-m-Y h:i A', $startdate);
                $startdate = $startdate->format('Y-m-d H:i:s');
                //$startdate = new Carbon($startdate);
                //$startdate = $startdate->subHour(7);

                if (!is_null($enddate)) {
                    $enddate = Carbon::createFromFormat('d-m-Y h:i A', $enddate);
                    $enddate = $enddate->format('Y-m-d H:i:s');
                    //$enddate = new Carbon($enddate);
                    //$enddate = $enddate->subHour(7);
                }
            }

            if ($category_id == 3) {
                $course->is_certificate = 1;
                $course->is_end_quiz = $is_end_quiz;
            }


            if ($category_id != 2) { //nếu là thư viện khóa học thì không check thời gian
                $stdate = strtotime($startdate);
                $eddate = !is_null($enddate) ? strtotime($enddate) : 0;

                if ($enddate && $stdate > $eddate) {
                    $response->status = false;
                    $response->message = __('thoi_gian_bat_dau_khong_lon_hon_ket_thuc');
                    return response()->json($response);
                }

                $course->startdate = $stdate;
                $course->enddate = $eddate;
            }

            $course->total_date_course = $total_date_course;
            $course->course_place = $course_place;
            $course->category = $category_id;
            $course->shortname = $shortname;
            $course->fullname = $fullname;
            $course->summary = $description;
            $course->estimate_duration = $estimate_duration;
            $course->course_budget = $course_budget;
            $course->is_toeic = $is_toeic;
            $course->allow_register = $allow_register;
            $access_ip = $this->spitIP($access_ip_string);
            $course->access_ip = $access_ip;

            $course->save();


            MdlCourseCompletionCriteria::where('course', '=', $id)->delete();

            //insert dữ liệu điểm qua môn
            MdlCourseCompletionCriteria::create(array(
                'course' => $course->id,
                'criteriatype' => 6, //default là 6 trong trường hợp này
                'gradepass' => $pass_score
            ));


            if ($allow_register == 1) {
                MdlEnrol::firstOrCreate(
                    [
                        'enrol' => 'self',
                        'courseid' => $course->id,
                        'roleid' => 5,
                        'sortorder' => 2,
                        'customint6' => 1
                    ],
                    [
                        'expirythreshold' => 86400,
                        'timecreated' => strtotime(Carbon::now()),
                        'timemodified' => strtotime(Carbon::now())
                    ]
                );
                //                }
            } else {
                //xóa dữ liệu enrol của course
                DB::table('mdl_enrol')
                    ->where('courseid', '=', $course->id)
                    ->where('enrol', '=', 'self')
                    ->where('roleid', '=', 5)//quyền học viên
                    ->delete();
            }

            //write log to mdl_logstore_standard_log
            /*            $app_name = Config::get('constants.domain.APP_NAME');

                        $key_app = encrypt_key($app_name);
                        $user_id = Auth::id();
                        $dataLog = array(
                            'app_key' => $key_app,
                            'courseid' => $course->id,
                            'action' => 'edit',
                            'description' => json_encode($course),
                            'userid' => $user_id
                        );

                        $dataLog = createJWT($dataLog, 'data');

                        $data_write = array(
                            'data' => $dataLog,
                        );

                        $url = Config::get('constants.domain.LMS') . '/course/write_log.php';
                        $checkUser = MdlUser::where('id', $user_id)->first();
                        $token = '';
                        if (isset($checkUser)) {
                            $token = strlen($checkUser->token) != 0 ? $checkUser->token : '';
                        }
                        //call api write log
                        callAPI('POST', $url, $data_write, false, $token);*/
            devcpt_log_system('course', 'lms/course/view.php?id=' . $course->id, 'update', 'Update course: ' . $course->shortname);
            updateLastModification('update', $course->id);

            $response->status = true;
            $response->message = __('sua_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
            //$response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function changestatuscourse(Request $request)
    {
        // TODO: Implement changestatuscourse() method.
        $response = new ResponseModel();
        try {

            $id = $request->input('course_id');
            $current_status = $request->input('current_status');

            $param = [
                'course_id' => 'number',
                'current_status' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = $validator['message'];
                return response()->json($response);
            }


            $course = MdlCourse::findOrFail($id);

            if (!$course) {
                $response->status = false;
                $response->message = __('khong_tim_thay_khoa_hoc');
                return response()->json($response);
            }

            if ($current_status == 1) {
                $current_status = 0;
            } else {
                $current_status = 1;
            }

            $course->update(array(
                'visible' => $current_status,
            ));

            $response->status = true;
            $response->message = __('phe_duyet_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }


    public function apiGetTotalActivityCourse(Request $request)
    {
        $course_id = $request->input('course_id');
        $param = [
            'course_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }


        $totalActivity = DB::table('mdl_course_modules as cm')
            ->join("mdl_course_sections as cs", function ($join) {
                $join->on("cs.course", "=", "cm.course")
                    ->on("cs.id", "=", "cm.section");
            })
            ->where('cs.section', '<>', 0)
            ->where('cm.course', '=', $course_id)
            ->count();

        return response()->json($totalActivity);
    }

    public function apiStatisticUserInCourse(Request $request)
    {
        $course_id = $request->input('course_id');
        $row = $request->input('row');
        $keyword = $request->input('keyword');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }


        $totalActivity = DB::table('mdl_course_modules as cm')
            ->join("mdl_course_sections as cs", function ($join) {
                $join->on("cs.course", "=", "cm.course")
                    ->on("cs.id", "=", "cm.section");
            })
            ->where('cs.section', '<>', 0)
            ->where('cm.course', '=', $course_id)
            ->where('cm.completion', '<>', 0)
            ->count();

        $lstUserCourse = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'u.id')
            ->join('model_has_roles', 'u.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->where('c.id', '=', $course_id)
            //->where('roles.id', 5)//hoc vien only
            ->where('e.roleid', '=', Role::ROLE_STUDENT)
            ->select(
                'u.id as user_id',
                'u.username',
                'u.firstname',
                'u.lastname',
                'tud.fullname',
                DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm inner join
                mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on
                cm.course = cs.course and cm.section = cs.id inner join mdl_course cc on cm.course = cc.id where
                cs.section <> 0 and cmc.completionstate in (1,2) and cm.course = c.id and cmc.userid = u.id and cm.completion <> 0) as user_course_learn'),

                DB::raw('(select `g`.`finalgrade`
  				from mdl_grade_items as gi
				join mdl_grade_grades as g
				on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = u.id ) as finalgrade')
            );
        if ($keyword) {
            $lstUserCourse = $lstUserCourse->whereRaw('( u.username like "%' . $keyword . '%" OR tud.fullname like "%' . $keyword . '%" )');
        }

        $lstUserCourse = $lstUserCourse->orderBy('u.id', 'desc')->groupBy('u.id');

        $total_Data = $lstUserCourse->get()->count();

        $lstUserCourse = $lstUserCourse->paginate($row);
        $total = ceil($total_Data / $row);

        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstUserCourse->currentPage(),
            ],
            'data' => $lstUserCourse,
            'total_course' => $totalActivity
        ];


        return response()->json($response);
    }


    //Lấy danh sách học viên điểm danh theo lớp
    public function apiListAttendanceUsers(Request $request)
    {
        $course_id = $request->input('course_id');
        $row = $request->input('row');
        $keyword = $request->input('keyword');

        $param = [
            'course_id' => 'number',
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        //Lấy danh sách học viên trong khóa học và leftjoin vào table điểm danh
        $lstUserAttendance = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
//            ->join('model_has_roles', 'u.id', '=', 'model_has_roles.model_id')
//            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->leftJoin('mdl_attendance as mat', 'mat.userid', '=', 'mu.userid')
            ->where('c.id', '=', $course_id)
            ->where('e.roleid', 5)//hoc vien only
//            ->where('roles.id', 5)//hoc vien only
            ->select(
                'u.id as user_id',
                'u.username',
                'u.firstname',
                'u.lastname',
                'c.total_date_course',
                DB::raw('(SELECT count(att.id) FROM `mdl_attendance` att
                                    where att.courseid = c.id and att.userid = u.id) as count_attendance')
            );

        //search
        if ($keyword) {
            $lstUserAttendance = $lstUserAttendance->where('u.username', 'like', '%' . $keyword . '%');
        }
        //order by
        $lstUserAttendance = $lstUserAttendance->orderBy('u.id', 'desc')->groupBy('u.id');

        $total_Data = $lstUserAttendance->get()->count();
        $lstUserAttendance = $lstUserAttendance->paginate($row);
        $total = ceil($total_Data / $row);

        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstUserAttendance->currentPage(),
            ],
            'data' => $lstUserAttendance
        ];


        return response()->json($response);
    }

    public function apiDeleteEnrolNotUse()
    {
        $responseModel = new ResponseModel();
        try {

            //lấy danh sách học viên chưa được enrol vào khóa học hiện tại

            $lstData = DB::table('mdl_user_enrolments')
                ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                ->join('mdl_user', 'mdl_user.id', '=', 'mdl_user_enrolments.userid')
                ->select('mdl_enrol.id')
                ->groupBy('mdl_enrol.id')->pluck('mdl_enrol.id');

            //lay danh sach cac ban ghi enrol khong phu hop, loai bo cac ban ghi co enrol la self
            $lstNormal = $lstData = DB::table('mdl_enrol')
                ->where('mdl_enrol.enrol', '!=', 'self')
                ->select('mdl_enrol.id')->whereNotIn('mdl_enrol.id', $lstData)->pluck('mdl_enrol.id');


            $count_data = count($lstNormal);
            if ($count_data > 0) {
                DB::table('mdl_enrol')->whereIn('id', $lstNormal)->delete();
            }

            $responseModel->status = true;
            $responseModel->message = 'success';
        } catch (Exception $e) {
            $responseModel->status = false;
            $responseModel->message = $e->getMessage();
        }
        return response()->json($responseModel);
    }

    public function apiGetCourseDetail($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $course_info = MdlCourse::query()
            //->with('lastEdit')
            ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
            //->join('mdl_logstore_standard_log', 'mdl_course_categories.id', '=', 'mdl_course.category')
            ->leftJoin('mdl_user', 'mdl_course.last_modify_user', '=', 'mdl_user.id')
            ->select(
                'mdl_course.id as id',
                'mdl_course.fullname as fullname',
                'mdl_course.shortname as shortname',
                'mdl_course.category as category',
                'mdl_course_categories.name as category_name',
                'mdl_course.startdate as startdate',
                'mdl_course.enddate as enddate',
                'mdl_course_completion_criteria.gradepass as pass_score',
                'mdl_course.course_avatar as avatar',
                'mdl_course.allow_register',
                'mdl_course.total_date_course',
                'mdl_course.is_end_quiz',
                'mdl_course.summary',
                'mdl_course.course_place',
                'mdl_course.estimate_duration',
                'mdl_course.course_budget',
                'mdl_course.is_toeic',
                'mdl_course.access_ip',
                'mdl_user.username',
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`mdl_course`.`last_modify_time`), '%Y-%m-%d %H:%i:%s') as last_modify_time"),
                'mdl_course.last_modify_action',
                'mdl_course.last_modify_user'
            )
            ->where('mdl_course.id', '=', $id)->first();

        return response()->json($course_info);
    }


    public function apiGetCourseLastUpdate($id)
    {
        $id = is_numeric($id) ? $id : 0;
        $last_update = null;
        if ($id != 0) {
            $log_latest = MdlLogstoreStandardLog::with('userDetail')
                //->where('mdl_logstore_standard_log.target', 'course') //course only, comment out for module and section fetch
                ->select(
                    DB::raw('"education" as type'),
                    'mdl_logstore_standard_log.action',
                    DB::raw('FROM_UNIXTIME(mdl_logstore_standard_log.timecreated) as created_at'),
                    'mdl_course.shortname as course_name',
                    'mdl_logstore_standard_log.contextinstanceid as course_id',
                    'mdl_logstore_standard_log.userid',
                    'mdl_logstore_standard_log.target'
                )
                ->whereHas('userDetail')
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_logstore_standard_log.contextinstanceid')
                //->where('mdl_logstore_standard_log.contextlevel', 50) //course only
                //->where('mdl_logstore_standard_log.contextinstanceid', $id) //course only

                //new, get all activity for course and its modules, sections etc
                ->where('courseid', $id)
                ->where('mdl_logstore_standard_log.action', "<>", 'viewed')//update nên k tính viewed
                ->orderBy('mdl_logstore_standard_log.timecreated', 'desc')
                ->first();

            if (isset($log_latest)) {
                $last_update = $log_latest;
//                $last_update['user_id'] = $log_latest->userid;
//                $last_update['user_fullname'] = $log_latest->user_detail ? $log_latest->user_detail->fullname : '';
//                $last_update['updated_at'] = $log_latest->created_at;
            }
        }
        return response()->json(['last' => $last_update]);
    }


    //api enrol học viên vào khóa học tập trung
    public function apiEnrolUserCourseConcent(Request $request)
    {
        $response = new ResponseModel();
        try {
            $course_id = $request->input('course_id');
            $role_id = $request->input('role_id');
            $lstUserIDs = $request->input('Users');

            $param = [
                'course_id' => 'number',
                'role_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            $course = MdlCourse::findOrFail($course_id);

            if (empty($course)) {
                $response->status = false;
                $response->message = __('khong_tim_thay_khoa_hoc');
                return json_encode($response);
            }

            $start_date = $course->startdate;
            $end_date = $course->enddate;

            $ids = array();
            $ids_error = '';
            // End_date rỗng insert bình thường

            //id category
            $category = 5;

//            if (empty($end_date)) {
//                $ids = $lstUserIDs;
//            } else {
            foreach ($lstUserIDs as $user_id) {
                if (checkUserEnrol($user_id, $start_date, $end_date, $category)) {
                    array_push($ids, $user_id);
                } else {
                    $ids_error .= MdlUser::find($user_id)->username . ', ';
                }
            }
//            }

            enrole_user_to_course_multiple($ids, $role_id, $course_id, true);

            $response->otherData = $ids_error;
            $response->status = true;
            $response->message = __('ghi_danh_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }


    //api import câu hỏi vào ngân hàng câu hỏi
    public function apiImportQuestion(Request $request)
    {
        $responseModel = new ResponseModel();
        try {

            if (!$request->hasFile('import_file')) {
                $responseModel->status = false;
                $responseModel->message = 'Bạn chưa chọn file nào';
                return response()->json($responseModel);
            }

            $file = $request->file('import_file');

            $extension = $file->getClientOriginalExtension();
            if ($extension != 'xls' && $extension != 'xlsx') {
                $responseModel->status = false;
                $responseModel->message = __('dinh_dang_file');
                return response()->json($responseModel);
            }

            $this->category_id = $request->input('category');

            $param = [
                'category' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $responseModel->status = false;
                $responseModel->message = __('dinh_dang_du_lieu_danh_muc_khong_hop_le');
                return response()->json($responseModel);
            }

            if (empty($this->category_id)) {
                $responseModel->status = false;
                $responseModel->message = __('ban_chua_chon_danh_muc_cao_hoi');
                return response()->json($responseModel);
            }

            //import dữ liệu
            $array = (new QuestionImport())->toArray($file, '', '');
            $count_arr = count($array);
            if ($count_arr > 0 && !empty($array[0])) {
                $count_array = count($array[0]);
                for ($i = 1; $i < $count_array; $i++) {
                    $result = array_values(array_diff($array[0][$i], array("null", "")));
                    $count_item = count($result);
                    $count_ans = $count_item - 8; //lấy số lượng đáp án của câu hỏi

                    $question = MdlQuestion::create([
                        'category' => $this->category_id,
                        'name' => $result[1],
                        'questiontext' => $result[4],
                        'questiontextformat' => 1,
                        'generalfeedback' => 'nothing data',
                        'stamp' => 'online.bgt.com.vn',
                        'version' => 'online.bgt.com.vn',
                        'timecreated' => strtotime(Carbon::now()),
                        'timemodified' => strtotime(Carbon::now()),
                        'createdby' => Auth::user()->id,
                        'modifiedby' => Auth::user()->id,
                        'qtype' => MdlQuestion::TYPE_MULTIPLE_CHOICE,
                    ]);

                    if ($question) {
                        if ($count_ans > 0) {
                            $anwser_right = $result[7];
                            $arr_ans = explode(',', $anwser_right);
                            $count_r = count($arr_ans);
                            for ($j = 0; $j < $count_ans; $j++) {
                                for ($k = 0; $k < $count_r; $k++) {
                                    MdlQuestionAnswer::where('question', '=', $question->id)
                                        ->where('answer', '=', $result[8 + $j])
                                        ->where('fraction', '=', 0)->delete();
                                    if ($arr_ans[$k] == ($j + 1)) {

                                        MdlQuestionAnswer::firstOrCreate([
                                            'question' => $question->id,
                                            'answer' => $result[8 + $j],
                                            'fraction' => 1,
                                            'feedback' => 'nothing'
                                        ]);
                                    } else {

                                        $check = MdlQuestionAnswer::where('question', '=', $question->id)
                                            ->where('answer', '=', $result[8 + $j])
                                            ->where('fraction', '=', 1)->first();

                                        if (!$check) {
                                            MdlQuestionAnswer::firstOrCreate([
                                                'question' => $question->id,
                                                'answer' => $result[8 + $j],
                                                'fraction' => 0,
                                                'feedback' => 'nothing'
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $responseModel->status = true;
            $responseModel->message = 'success';
        } catch (Exception $e) {
            $responseModel->status = false;
            $responseModel->message = $e->getMessage();
        }
        return response()->json($responseModel);
    }

    public $arrError = [];

    //api import enrol học viên vào khóa học
    public function importExcelEnrol(Request $request)
    {
        $response = new ResponseModel();
        try {
            $this->courseCurrent_id = $request->input('course_id');
            $file_excel = $request->file('file');

            $param = [
                'course_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            if (!$file_excel) {
                $response->status = false;
                $response->message = __('vui_long_nhap_excel');
                return json_encode($response);
            }

            $course = MdlCourse::findOrFail($this->courseCurrent_id);

            if (empty($course)) {
                $response->status = false;
                $response->message = __('khong_tim_thay_khoa_hoc');
                return json_encode($response);
            }

            $this->category_id = $course->category;

            $path = $file_excel->getRealPath();
            $extension = $file_excel->getClientOriginalExtension();
            if ($extension != 'xls' && $extension != 'xlsx') {
                $response->status = false;
                $response->message = __('dinh_dang_file');
                return json_encode($response);
            }

            \DB::beginTransaction();
            $array = (new UsersImport)->toArray($request->file('file'), '', '');
            foreach ($array as $key => $rows) {
                if (!empty($rows) && count($rows) > 1) {
                    //loại bỏ hàng đầu tiên
                    $first_element = array_shift($rows);
                    //                    unset($rows[count($rows)-1]);
                    foreach ($rows as $user) {

                        $row = array_combine($first_element, $user);
                        //
                        $importModel = new ImportModel();
                        $username = $row['username'];

                        $user = MdlUser::select('id')->where('username', $username)->first();

                        if ($user) {
                            $isStudent = $row['isstudent'];
                            if ($isStudent == 1) {
                                $role_id = 5; //user có quyền học sinh
                            } else {
                                $role_id = 4; //user có quyền giáo viên
                            }

                            $checkUserAndRole = DB::table('model_has_roles')
                                ->join('mdl_user', 'mdl_user.id', '=', 'model_has_roles.model_id')
                                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                                ->where('mdl_user.username', '=', $username)
                                ->where('roles.id', '=', $role_id)
                                ->select('mdl_user.id')
                                ->first();


                            if ($checkUserAndRole) {

                                enrole_user_to_course($user['id'], $role_id, $this->courseCurrent_id, $this->category_id);

                                $importModel->username = $username;
                                $importModel->status = 'success';
                                $response->message = __('ghi_danh_khoa_hoc_thanh_cong');

                                array_push($this->arrError, $importModel);
                            } else {
                                $importModel->username = $username;
                                $importModel->status = 'error';
                                $importModel->message = __('tai_khoan_khong_co_quyen');

                                array_push($this->arrError, $importModel);
                            }

                            sleep(0.01);
                        }
                    }
                }
            }
            \DB::commit();

            $response->error = $this->arrError;
            $response->status = true;
            $response->message = __('ghi_danh_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return json_encode($response);
    }

    public function detail($id)
    {
        // TODO: Implement detail() method.
    }

    public function apiAttendanceList(Request $request)
    { //Skipped
        // TODO: Implement getall() method.
        $row = $request->input('row');
        $course_id = $request->input('course_id');
        $date = $request->input('date');

        $param = [
            'row' => 'number',
            'course_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $list = DB::table('mdl_user_enrolments')
            ->join('mdl_user', 'mdl_user.id', '=', 'mdl_user_enrolments.userid')
            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
            ->leftJoin('mdl_attendance', function ($join) {
                $join->on('mdl_course.id', '=', 'mdl_attendance.courseid')
                    ->andOn('mdl_user.id', '=', 'mdl_attendance.userid');
            })
            ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->where('mdl_attendance.attendance', '=', $date)
            ->where('mdl_course.id', '=', $course_id)
            ->where('mdl_enrol.roleid', '=', Role::ROLE_STUDENT)
            ->select(
                'mdl_user.id',
                'mdl_user.username',
                'tms_user_detail.fullname',
                'mdl_attendance.attendance',
                'mdl_attendance.note',
                'mdl_attendance.present'
            );

        $total_items = count($list->get()); //lấy tổng số khóa học hiện tại

        $list = $list->paginate($row);
        $total = ceil($list->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $list->currentPage(),
            ],
            'data' => $list,
            'total_items' => $total_items
        ];

        return response()->json($response);
    }

    public function apiGetListModule($course_id)
    {
        $modules = DB::table('mdl_course_modules as cm')
            ->join('mdl_modules as m', 'm.id', '=', 'cm.module')
            ->where('cm.course', '=', $course_id)
            ->select('m.id', 'm.name')
            ->distinct()
            ->get();
        return response()->json($modules);
    }

    public function apiGetListDocument(Request $request)
    {
        $course_id = $request->input('course_id');
        $module_id = $request->input('module_id');
        $keyword = $request->input('keyword');
        $page = $request->input('page');
        $pageSize = $request->input('row');
        $action = $request->input('action');

        // Ghi log của course
        // * lấy log trong thùng rác của course
        $docDel = DB::table('mdl_logstore_standard_log as lsl')
            ->join('mdl_user as u', 'u.id', '=', 'lsl.userid')
            ->where('lsl.contextlevel', '=', MdlUser::CONTEXT_MODULE)
            ->where('lsl.courseid', '=', $course_id)->join('mdl_tool_recyclebin_course as mtrc', 'mtrc.courseid', '=', 'lsl.courseid')
            ->where('lsl.action', '=', 'deleted')
            ->select('lsl.other', 'mtrc.name', 'u.username', 'lsl.action', 'mtrc.module', 'lsl.timecreated');

        // * lấy log khác thùng rác
        $docDifDel = DB::table('mdl_logstore_standard_log as lsl')
            ->join('mdl_user as u', 'u.id', '=', 'lsl.userid')
            ->where('lsl.contextlevel', '=', MdlUser::CONTEXT_MODULE)
            ->where('lsl.courseid', '=', $course_id)->join('mdl_course_modules as cm', 'cm.id', '=', 'lsl.objectid')
            ->select('lsl.other', DB::raw('"" as name'), 'u.username', 'lsl.action', 'cm.module', 'lsl.timecreated');


        if ($keyword) {
            //lsl.other is a json => encode string input
            $encodeKeyword = json_encode($keyword);
            //replace " to remove " in string
            $encodeKeyword = str_replace('"', '', $encodeKeyword);
            //string input take the form 'v\u1ecb' so replace \ -> \\\\ will search in mysql right
            $encodeKeyword = str_replace('\\', '\\\\\\\\', $encodeKeyword);
            //
            $docDel = $docDel->whereRaw('( lsl.other like "%' . $keyword . '%" OR lsl.other like "%' . $encodeKeyword . '%" OR mtrc.name like "%' . $keyword . '%" OR u.username like "%' . $keyword . '%" OR lsl.action like "%' . $keyword . '%" )');
            //
            $docDifDel = $docDifDel->whereRaw('( lsl.other like "%' . $keyword . '%" OR lsl.other like "%' . $encodeKeyword . '%" OR u.username like "%' . $keyword . '%" OR lsl.action like "%' . $keyword . '%" )');
        }

        if ($action) {
            $docDel = $docDel->where('lsl.action', '=', $action);
            $docDifDel = $docDifDel->where('lsl.action', '=', $action);
        }

        if ($module_id > 0) {
            $docDel = $docDel->where('mtrc.module', '=', $module_id);
            $docDifDel = $docDifDel->where('cm.module', '=', $module_id);
        }

        // * union all log
        $documents = $docDel->unionAll($docDifDel);

        $total_Data = $documents->count();

        $documents = $documents
            ->orderBy('timecreated', 'desc')
            ->skip(($page - 1) * $pageSize)->take($pageSize)
            ->get();

        $total = ceil($total_Data / $pageSize);

        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $page,
            ],
            'data' => $documents,
            'total_course' => $total_Data
        ];

        return response()->json($response);
    }

    public function apiHintCourseCode(Request $request)
    {
        $type = $request->input('type');
        $response = new ResponseModel();

        $join_string = "_";
        if ($type == 'sample') {
            $join_string = "_";
        } elseif ($type == 'online') {
            $join_string = '_ONL';
        } elseif ($type == 'offline') {
            $join_string = '_OFFL';
        }

        try {
            $code_org = DB::table('mdl_user as mu')
                ->join('tms_organization_employee as toe', 'toe.user_id', '=', 'mu.id')
                ->join('tms_organization as tor', 'tor.id', '=', 'toe.organization_id')
                ->where('mu.id', '=', Auth::id())
                ->select('tor.code')->first();
            $num = 0;
            if ($code_org) {
                $prefix = str_replace('-', '_', $code_org->code);
                $courses = MdlCourse::where('shortname', 'like', $prefix . '%')
                    ->select('shortname');
                if ($type == 'library') {
                    $courses->where('category', 2);
                } elseif ($type == 'online') {
                    $courses->where('category', '<>', 2);
                    $courses->where('category', '<>', 5);
                } elseif ($type == 'online') {
                    $courses->where('category', 5);
                }
                $courses = $courses->orderBy('id', 'desc')->get();
                $arr_code = [];
                foreach ($courses as $course) {
                    $arr_data = explode($join_string, $course->shortname);
                    $count_dt = count($arr_data);
                    if ($count_dt > 0) {
                        if (is_numeric($arr_data[$count_dt - 1])) {
                            array_push($arr_code, (int)$arr_data[$count_dt - 1]);
                        }
                    }
                }
                $count_code = count($arr_code);
                if ($count_code > 0) {
                    $num = max($arr_code);
                    if ($num == 0) {
                        $num = 1;
                    } else {
                        $num = $num + 1;
                    }
                } else {
                    $num = 1;
                }
                $code_hint = $prefix . $join_string . self::composeAppend($num);
            }

            if ($num > 0) {
                $response->status = true;
                $response->otherData = $code_hint;
            } else {
                $response->status = false;
            }

            $response->message = '';

        } catch (\Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function apiGetListLibrary()
    {
        $libraries = MdlCourse::query()
            ->where('category', 2)
            //->where('deleted', 0)
            //->whereRaw('ROUND((CHAR_LENGTH(shortname) - CHAR_LENGTH(REPLACE(shortname, "_", ""))) / CHAR_LENGTH("_")) = 2')
            ->get()
            ->toArray();
        return response()->json($libraries);
    }

    public function apiGetListLibraryCodes()
    {
        $codes = TmsOrganization::query()
            ->orderBy('level')
            ->select('id', 'code')
            ->get();
        return response()->json($codes);
    }

    public function apiGetExistedCodes()
    {
        $codes = MdlCourse::query()
            ->select('id', 'shortname')
            ->get();
        return response()->json($codes);
    }

    public function apiGetExistedCodeLibraries()
    {
        $codes = MdlCourse::query()
            ->select('id', 'shortname')
            ->where('category', 2)
            ->get();
        return response()->json($codes);
    }


    /**
     * @param $num
     * @return string
     */
    public function composeAppend($num)
    {
        $length = 3;
        if (strlen($num) >= $length) {
            return $num;
        } else {
            return str_repeat('0', $length - strlen($num)) . $num;
        }
    }

    //#region optonal courses
    public function getOptionalCourses(Request $request)
    {
        // TODO: Implement getOptionalCourses() method.
        $org_id = $request->input('org_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $is_excluded = $request->input('is_excluded'); //đã gán vào quyền hay chưa

        $param = [
            'keyword' => 'text',
            'row' => 'number'
        ];

        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listCourses = DB::table('mdl_course as c')
            ->where('c.category', '!=', 2)
            ->select(
                'c.id',
                'c.fullname',
                'c.shortname'
            );

        if (strlen($is_excluded) != 0) {
            if ($is_excluded == 1) { //List khóa học chưa phân quyền cho role này
                $listCourses = $listCourses->whereNotIn('c.id', function ($query) use ($org_id) {
                    $query->select('course_id')
                        ->from('tms_optional_courses')
                        ->where('organization_id', $org_id);
                });
            } else { //List khóa học đã phân quyền cho role này
                $listCourses = $listCourses->whereIn('c.id', function ($query) use ($org_id) {
                    $query->select('course_id')
                        ->from('tms_optional_courses')
                        ->where('organization_id', $org_id);
                });
            }
        }


        $listCourses = $listCourses->where('c.deleted', '=', 0);
        $listCourses = $listCourses->where('c.visible', '=', 1);

        $totalCourse = count($listCourses->get()); //lấy tổng số khóa học hiện tại

        if ($keyword) {
            //lỗi query của mysql, không search được kết quả khi keyword bắt đầu với kỳ tự d or D
            // code xử lý remove ký tự đầu tiên của keyword đi
            if (substr($keyword, 0, 1) === 'd' || substr($keyword, 0, 1) === 'D') {
                $total_len = strlen($keyword);
                if ($total_len > 2) {
                    $keyword = substr($keyword, 1, $total_len - 1);
                }
            }

            $listCourses = $listCourses->whereRaw('( c.fullname like "%' . $keyword . '%" OR c.shortname like "%' . $keyword . '%" )');
        }

        $listCourses = $listCourses->orderBy('c.id', 'desc');

        $listCourses = $listCourses->paginate($row);
        $total = ceil($listCourses->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listCourses->currentPage(),
            ],
            'data' => $listCourses,
            'total_course' => $totalCourse
        ];


        return response()->json($response);
    }

    public function assignOptionalCourse(Request $request)
    {
        // TODO: Implement assignOptionalCourse() method.
        $response = new ResponseModel();
        try {
            $lstCourseId = $request->input('lstCourse');
            $org_id = $request->input('org_id');

            $param = [
                'org_id' => 'number'
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            $arrData = [];
            $data_item = [];

            foreach ($lstCourseId as $course_id) {
                $data_item['course_id'] = $course_id;
                $data_item['organization_id'] = $org_id;
                $data_item['created_at'] = Carbon::now();
                $data_item['updated_at'] = Carbon::now();

                array_push($arrData, $data_item);
            }

            TmsOptionalCourse::insert($arrData);

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');;
        }
        return response()->json($response);
    }

    public function removeAssignOptionalCourse(Request $request)
    {
        // TODO: Implement removeAssignOptionalCourse() method.
        $response = new ResponseModel();
        try {
            $lstCourseId = $request->input('lstCourse');
            $org_id = $request->input('org_id');

            $param = [
                'org_id' => 'number'
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return json_encode($response);
            }

            TmsOptionalCourse::where('organization_id', '=', $org_id)->whereIn('course_id', $lstCourseId)->delete();

            $response->status = true;
            $response->message = __('thao_tac_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = __('loi_he_thong_thao_tac_that_bai');;
        }
        return response()->json($response);
    }

    //#endregion

    public function cloneCourseLibrary(Request $request)
    {
        $avatar = $request->input('course_avatar');
        $fullname = $request->input('fullname');
        $shortname = $request->input('shortname');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $pass_score = $request->input('pass_score');
        $description = $request->input('description');
        $category_id = $request->input('category_id');
        $sample = $request->input('sample');
        $course_place = $request->input('course_place');
        $allow_register = $request->input('allow_register');
        $total_date_course = $request->input('total_date_course');
        $is_end_quiz = $request->input('is_end_quiz');
        $estimate_duration = $request->input('estimate_duration');
        $course_budget = $request->input('course_budget');
        $access_ip_string = $request->input('access_ip');
        $is_toeic = $request->input('is_toeic');
        $org_code = $request->input('org_code');

        $course = new MdlCourse(); //khởi tạo theo cách này để tránh trường hợp insert startdate và endate bị set về 0
        $course->category = $category_id;
        $course->shortname = $shortname;
        $course->fullname = $fullname;
        $course->summary = $description;
        $course->is_toeic = $is_toeic;
        $course->course_avatar = $avatar;
        if ($sample == 1) {
            $course->startdate = strtotime(Carbon::now());
            $course->enddate = strtotime(Carbon::now()->addYear(100)); // gia hạn thời gian cho khóa học mẫu là 100 năm
            $course->visible = 1;  //luôn hiển thị khi là khóa học mẫu
        } else {
            $stdate = strtotime($startdate);
            $eddate = !is_null($enddate) ? strtotime($enddate) : 0;
            $course->course_place = $course_place;
            $course->startdate = $stdate;
            $course->enddate = $eddate;
            $course->visible = 0;
        }

        if ($category_id == 3) {
            $course->is_certificate = 1;
            $course->is_end_quiz = $is_end_quiz;
        }

        $course->total_date_course = $total_date_course;

        $course->allow_register = $allow_register;
        $course->enablecompletion = 1;
        $course->estimate_duration = $estimate_duration;
        $course->course_budget = $course_budget;

        //access_ip
        $access_ip = $this->spitIP($access_ip_string);

        $course->access_ip = $access_ip;

        $course->save();
        //nếu manager hoặc leader tạo khóa
        $checkRole = tvHasRoles(\Auth::user()->id, ["manager", "leader", "employee"]);
        if ($checkRole == true) {
            $organization_employee = TmsOrganizationEmployee::query()->where('user_id', '=', \Auth::user()->id)->first();
            if (isset($organization_employee)) {
                if ($organization_employee->organization) {

                    //lay tat ca user ngoai le duoc quyen quan ly course cua cctc
                    $lstUserExcept = TmsUserOrganizationException::where('organization_id', $organization_employee->organization_id)->pluck('user_id');

                    if ($lstUserExcept) {
                        $arr_data = [];
                        $data_course = [];

                        foreach ($lstUserExcept as $user) {
                            $data_course['user_id'] = $user;
                            $data_course['organization_id'] = $organization_employee->organization_id;
                            $data_course['course_id'] = $course->id;
                            $data_course['created_at'] = Carbon::now();
                            $data_course['updated_at'] = Carbon::now();

                            array_push($arr_data, $data_course);
                        }

                        //gan course cho user ngoai le quan ly
                        TmsUserOrganizationCourseException::insert($arr_data);
                    }

                    $role_organization = TmsRoleOrganization::query()->where('organization_id', $organization_employee->organization_id)->first();
                    if (isset($role_organization)) { //Map course to that roles
//                        $role_course = new TmsRoleCourse();
//                        $role_course->role_id = $role_organization->role_id;
//                        $role_course->course_id = $course->id;
//                        $role_course->save();
                        TmsRoleCourse::firstOrCreate([
                            'role_id' => $role_organization->role_id,
                            'course_id' => $course->id
                        ]);
                    } else { //Enable use organization as role and map course to that role

                        $lastRole = MdlRole::query()->orderBy('sortorder', 'desc')->first();
                        //Tạo quyền bên LMS
                        if (isset($lastRole)) {
                            $sortorder = $lastRole['sortorder'] + 1;
                        } else {
                            $sortorder = 1;
                        }

                        $mdlRole = MdlRole::firstOrCreate([
                            'shortname' => $organization_employee->organization->code,
                            'archetype' => 'user'
                        ], [
                            'description' => $organization_employee->organization->name,
                            'sortorder' => $sortorder
                        ]);

                        $role = Role::firstOrCreate([
                            'mdl_role_id' => $mdlRole->id,
                            'name' => $organization_employee->organization->code,
                            'guard_name' => 'web',
                            'status' => 1
                        ], [
                            'description' => $organization_employee->organization->name
                        ]);

                        $role_organization = TmsRoleOrganization::firstOrCreate([
                            'role_id' => $role->id,
                            'organization_id' => $organization_employee->organization_id
                        ]);

                        TmsRoleCourse::firstOrCreate([
                            'role_id' => $role_organization->role_id,
                            'course_id' => $course->id
                        ]);
                    }
                }
            }
        }

        if ($org_code) {

            $userExcept = DB::table('tms_organization as tor')
                ->join('tms_user_organization_exceptions as tuoe', 'tuoe.organization_id', '=', 'tor.id')
                ->where('tor.code', '=', $org_code)
                ->select('tuoe.user_id', 'tor.id as org_id')->get();

            if ($userExcept) {
                $arr_data = [];
                $data_course = [];

                foreach ($userExcept as $user) {
                    $data_course['user_id'] = $user->user_id;
                    $data_course['organization_id'] = $user->org_id;
                    $data_course['course_id'] = $course->id;
                    $data_course['created_at'] = Carbon::now();
                    $data_course['updated_at'] = Carbon::now();

                    array_push($arr_data, $data_course);
                }

                //gan course cho user ngoai le quan ly
                TmsUserOrganizationCourseException::insert($arr_data);
            }
        }


        //insert dữ liệu điểm qua môn
        MdlCourseCompletionCriteria::create(array(
            'course' => $course->id,
            'criteriatype' => 6, //default là 6 trong trường hợp này
            'gradepass' => $pass_score
        ));

        $context_cate = MdlContext::where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSECAT)
            ->where('instanceid', '=', $category_id)->first();

        if ($context_cate) {
            //insert dữ liệu vào bảng mdl_context
            $mdl_context = MdlContext::firstOrCreate([
                'contextlevel' => \App\MdlUser::CONTEXT_COURSE,
                'instanceid' => $course->id,
                'depth' => 3,
                'locked' => 0
            ]);

            //cập nhật path
            $mdl_context->path = '/1/' . $context_cate->id . '/' . $mdl_context->id;
            $mdl_context->save();
        }


        if ($allow_register == 1) {
            MdlEnrol::firstOrCreate(
                [
                    'enrol' => 'self',
                    'courseid' => $course->id,
                    'roleid' => 5,
                    'sortorder' => 2,
                    'customint6' => 1
                ],
                [
                    'expirythreshold' => 86400,
                    'timecreated' => strtotime(Carbon::now()),
                    'timemodified' => strtotime(Carbon::now())
                ]
            );
        }

        //get info of role teacher
        $role_teacher = MdlRole::where('shortname', 'teacher')->first();
        //call function auto enrol to show list courses for teacher when teacher create a course
        enrole_user_to_course(Auth::user()->id, $role_teacher->id, $course->id, $course->category);

        //write data to table mdl_grade_categories -> muc dich phuc vu cham diem, Vinh PT yeu cau
        $mdl_grade_cate = MdlGradeCategory::firstOrCreate([
            'courseid' => $course->id,
            'depth' => 1,
            'aggregation' => 13,
            'aggregateonlygraded' => 1,
            'timecreated' => strtotime(Carbon::now()),
            'timemodified' => strtotime(Carbon::now())
        ]);

        $mdl_grade_cate->path = '/' . $mdl_grade_cate->id . '/';
        $mdl_grade_cate->save();

        //write data to table mdl_grade_items
        MdlGradeItem::firstOrCreate([
            'courseid' => $course->id,
            'itemname' => $course->fullname,
            'itemtype' => 'course',
            'iteminstance' => $mdl_grade_cate->id,
            'gradepass' => $pass_score
        ]);

        return $course;
    }

    public function importFile()
    {
        // TODO: Implement importFile() method.
    }
}
