<?php


namespace App\Repositories;


use App\Imports\UsersImport;
use App\MdlContext;
use App\MdlCourse;
use App\MdlCourseCompletionCriteria;
use App\MdlEnrol;
use App\MdlGradeCategory;
use App\MdlGradeItem;
use App\MdlRole;
use App\MdlUser;
use App\Role;
use App\ViewModel\ImportModel;
use App\ViewModel\ResponseModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MdlCourseRepository implements IMdlCourseInterface, ICommonInterface
{
    //api lấy danh sách khóa học
    //ThoLD (21/08/2019)
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
        $checkRole = tvHasRole(\Auth::user()->id, "teacher");
        if ($checkRole === TRUE) {
            $listCourses = DB::table('mdl_user_enrolments as mue')
                ->where('mue.userid', '=', \Auth::user()->id)
                ->join('mdl_enrol as e', 'mue.enrolid', '=', 'e.id')
                ->join('mdl_course as c', 'e.courseid', '=', 'c.id')
                ->leftJoin('mdl_course_completion_criteria as mccc', 'mccc.course', '=', 'c.id')
                ->select(
                    'c.id',
                    'c.fullname',
                    'c.shortname',
                    'c.startdate',
                    'c.enddate',
                    'c.visible',
                    'mccc.gradepass as pass_score'
                );
        } else {
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
        }


        //là khóa học mẫu
        if ($sample == 1) {
            $listCourses = $listCourses->where('c.category', '=', 2); //2 là khóa học mẫu
        } else {
            $listCourses = $listCourses->where('c.category', '!=', 2);
            $listCourses = $listCourses->where('c.category', '!=', 5);
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
        $course->course_avatar = $path_avatar;
        if ($sample == 1) {
            $course->startdate = strtotime(Carbon::now());
            $course->enddate = strtotime(Carbon::now()->addYear(100)); // gia hạn thời gian cho khóa học mẫu là 100 năm
            $course->visible = 1;  //luôn hiển thị khi là khóa học mẫu
        } else {
            $stdate = strtotime($startdate);
            $eddate = strtotime($enddate);


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
            //thực hiện insert dữ liệu
            $param = [
                'shortname' => 'code',
                'description' => 'longtext',
                'pass_score' => 'number',
                'category_id' => 'number',
                'offline' => 'number',
                'course_place' => 'text',
                'allow_register' => 'number',
                'total_date_course' => 'number',
                'is_end_quiz' => 'number',
                'fullname' => 'text',
                'estimate_duration' => 'number',
                'course_budget' => 'decimal'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = 'Định dạng dữ liệu không hợp lệ';
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
                $startdate = new Carbon($startdate);
                $startdate = $startdate->subHour(7);

                $enddate = new Carbon($enddate);
                $enddate = $enddate->subHour(7);
            }

            if ($category_id == 3) {
                $course->is_certificate = 1;
                $course->is_end_quiz = $is_end_quiz;
            }


            if ($category_id != 2) { //nếu là thư viện khóa học thì không check thời gian
                $stdate = strtotime($startdate);
                $eddate = strtotime($enddate);
                if ($stdate > $eddate) {
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

            $course->allow_register = $allow_register;

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
            $app_name = Config::get('constants.domain.APP_NAME');

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
            callAPI('POST', $url, $data_write, false, '');

            $response->status = true;
            $response->message = __('sua_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
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
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
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
            $response->message = __('phe_duyet_khoa_hoc');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }


    public $arrError = [];
    //api import enrol học viên vào khóa học
    //ThoLD 15/09/2019
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
            $response->message = $e->getMessage();
        }
        return json_encode($response);
    }

    public function detail($id)
    {
        // TODO: Implement detail() method.
    }

    public function apiAttendanceList(Request $request) { //Skipped
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
            ->join('tms_user_detail',  'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->where('mdl_attendance.attendance', '=', $date)
            ->where('mdl_course.id', '=', $course_id)
            ->where('mdl_enrol.roleid', '=', Role::ROLE_STUDENT)
            ->select('mdl_user.id',
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
}
