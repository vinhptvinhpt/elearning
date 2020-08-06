<?php

namespace App\Http\Controllers\Backend;

use App\Exports\ListMismatchData;
use App\Http\Controllers\Controller;
use App\Imports\DataImport;
use App\Mail\CourseSendMail;
use App\MdlContext;
use App\MdlCourse;
use App\MdlCourseCompletionCriteria;
use App\MdlCourseCompletions;
use App\MdlEnrol;
use App\MdlGradeCategory;
use App\MdlGradeGrade;
use App\MdlGradeItem;
use App\MdlQuiz;
use App\MdlRoleAssignments;
use App\MdlUser;
use App\MdlUserEnrolments;
use App\ModelHasRole;
use App\Role;
use App\StudentCertificate;
use App\TmsBranch;
use App\TmsBranchSaleRoom;
use App\TmsCityBranch;
use App\TmsConfigs;
use App\TmsNotification;
use App\TmsRoleOrganize;
use App\TmsSaleRooms;
use App\TmsSaleRoomUser;
use App\TmsTrainningCategory;
use App\TmsTrainningUser;
use App\TmsUserDetail;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Http\Controllers\Api\MailController;

set_time_limit(0);

class EmailTemplateController extends Controller
{

    public function viewIndex()
    {
        return view('email.template.index');
    }

    public function apiGetListEmailTemplate()
    {
        //load
        $this->deleteOldConfigs();
        //
        $data = [];
        $configs = TmsConfigs::where('editor', 'checkbox')->get();
        if (count($configs) != 0) {
            foreach ($configs as $config) {
                $label = $this->convertNameFile($config->target);
                if (empty($label) || $label == '')
                    $label = $config->target;
                $config->label = $label;
                $data[] = $config;
            }
        }
        return response()->json($data);
    }

    public function deleteOldConfigs()
    {
        //set old configs (using in bgt)
        $configsDelete = array(
            TmsNotification::SUGGEST => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_START => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_END => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_COMPLETED => TmsConfigs::ENABLE,
            TmsNotification::REMIND_LOGIN => TmsConfigs::ENABLE,
            TmsNotification::REMIND_ACCESS_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EDUCATION_SCHEDULE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_UPCOMING_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_CERTIFICATE => TmsConfigs::ENABLE
        );

        $configs = array(
            TmsNotification::ASSIGNED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::ASSIGNED_COMPETENCY => TmsConfigs::ENABLE,
            TmsNotification::SUGGEST_OPTIONAL_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXAM => TmsConfigs::ENABLE,
            TmsNotification::INVITATION_OFFLINE_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::INVITE_STUDENT => TmsConfigs::ENABLE,
            TmsNotification::COMPLETED_FRAME => TmsConfigs::ENABLE,
            TmsNotification::ENROL => TmsConfigs::ENABLE
        );
        $pdo = DB::connection()->getPdo();
        if ($pdo) {
            $stored_configs = TmsConfigs::whereIn('target', array_keys($configs))->get();
            $today = date('Y-m-d H:i:s', time());
            ////delete all old configs (using in bgt)
            TmsConfigs::whereIn('target', array_keys($configsDelete))->delete();
            //
            if (count($stored_configs) == 0 || count($stored_configs) != count($configs)) {
                TmsConfigs::whereIn('target', array_keys($configs))->delete();
                $insert_configs = array();
                foreach ($configs as $key => $value) {
                    $insert_configs[] = array(
                        'target' => $key,
                        'content' => $value,
                        'editor' => TmsConfigs::EDITOR_CHECKBOX,
                        'created_at' => $today
                    );
                }
                TmsConfigs::insert($insert_configs);
            } else {
                $configs = array();
                foreach ($stored_configs as $item) {
                    $configs[$item->target] = $item->content;
                }
            }
        }
        return $configs;
    }

    public function viewEmailTemplateDetail($name_file)
    {
        $label = $this->convertNameFile($name_file);
        return view('email.template.detail', ['name_file' => $name_file, 'name_show' => $label]);
    }

    public function readJson($name_file)
    {
        //path to files folder contain json file
        // echo public_path();
        $dir = public_path() . "/files/email";
        // //return file or foler in directory above
        $temp_files = scandir($dir);
        //get content of file with name
        $string = file_get_contents(public_path() . "/files/email/" . $name_file . ".json");
        // //decode content of file above=
        $data = json_decode($string, true);

        return response()->json($string);
    }

    public function getContentFile($name_file)
    {
        $label = $this->convertNameFile($name_file);
        //get content of file with name
        $string = file_get_contents(public_path() . "/files/email/template.json");

        return response()->json(['content' => $string, 'name_show' => $label]);
    }

    public function convertNameFile($name_file)
    {
        switch ($name_file) {
            case TmsNotification::ASSIGNED_COURSE:
                $label = __('assigned_course');
                break;
            case TmsNotification::ASSIGNED_COMPETENCY:
                $label = __('assigned_competency');
                break;
            case TmsNotification::SUGGEST_OPTIONAL_COURSE:
                $label = __('suggest_optional_course');
                break;
            case TmsNotification::REMIND_EXAM:
                $label = __('remind_exam');
                break;
            case TmsNotification::INVITATION_OFFLINE_COURSE:
                $label = __('invitation_offline_course');
                break;
            case TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE:
                $label = __('nhac_nho_khoa_hoc_bat_buoc_sap_het_han');
                break;
            case TmsNotification::FORGOT_PASSWORD:
                $label = __('quen_mat_khau');
                break;
            case TmsNotification::COMPLETED_FRAME:
                $label = __('chung_chi_hoan_thanh');
                break;
            case TmsNotification::INVITE_STUDENT:
                $label = __('invite_student');
                break;
            case TmsNotification::REMIND_CERTIFICATE:
                $label = __('remind_certificate');
                break;
            case TmsNotification::ENROL:
                $label = __('tham_gia_khoa_hoc');
                break;
            default:
                $label = '';
                break;
        }
        return $label;
    }

    public function writeToJson(Request $rq)
    {
        $name_file = $rq->input('name_file');
        $response = new ResponseModel();
        try {
            //path of file
            $dir = public_path() . "/files/email";
            // //return file or foler in directory above
            $temp_files = scandir($dir);
            $type = $rq->input('type');
            if ($type === 'ckeditor') {
                $path_html = public_path() . "/files/email/template.json";
                //write to content html
                $string_html = file_get_contents(public_path() . "/files/email/template.json");
                $data_html = json_decode($string_html, true);
                // //set values
                $data_html['' . $name_file] = $rq->input('editor_data');
                // // Write File
                $newJsonHtml = json_encode($data_html, JSON_UNESCAPED_UNICODE);
                file_put_contents($path_html, $newJsonHtml);
            }
            $response->status = true;
            $response->message = __('sua_thanh_cong_template') . $this->convertNameFile($name_file);
        } catch (Exception $e) {
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    ///email_template/sendDemo/{name_file}
    public function demoSendMail($name_file)
    {
        $name_action = '';
        switch ($name_file) {
            case 'remind_expire_required_course':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE,
                        "hycuong",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '[{"course_id":14,"course_code":"BGT655","course_name":"Đào tạo sản phẩm Power 6\/55","startdate":1566147600,"enddate":1597683600,"course_place":"Hà Nội"},{"course_id":15,"course_code":"BGTMAX3D","course_name":"Đào tạo sản phẩm Max 3D","startdate":1566147600,"enddate":1597683600,"course_place":"Hồ Chí Minh"}]'
                    ));
                }
                break;
            case 'forgot_password':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::FORGOT_PASSWORD,
                        "hycuong",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '1234567'
                    ));
                }
                break;
            case 'assigned_course':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::ASSIGNED_COURSE,
                        "",
                        "Hy Quốc Cường",
                        '',
                        'How to love?',
                        '27/07/2020',
                        '02/09/2020'
                    ));
                }
                break;
            case 'assigned_competency':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::ASSIGNED_COMPETENCY,
                        "",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '27/07/2020',
                        '02/09/2020',
                        '',
                        '',
                        '',
                        '',
                        '',
                        'TOEIC Certificate'
                    ));
                }
                break;
            case 'suggest_optional_course':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::SUGGEST_OPTIONAL_COURSE,
                        "",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '[{"course_id":14,"course_code":"BGT655","course_name":"Đào tạo sản phẩm Power 6\/55","startdate":1566147600,"enddate":1597683600,"course_place":"Hà Nội"},{"course_id":15,"course_code":"BGTMAX3D","course_name":"Đào tạo sản phẩm Max 3D","startdate":1566147600,"enddate":1597683600,"course_place":"Hồ Chí Minh"}]',
                        '[{"course_id":14,"course_code":"BGT655","course_name":"Đào tạo sản phẩm Power 6\/55","startdate":1566147600,"enddate":1597683600,"course_place":"Hà Nội"},{"course_id":15,"course_code":"BGTMAX3D","course_name":"Đào tạo sản phẩm Max 3D","startdate":1566147600,"enddate":1597683600,"course_place":"Hồ Chí Minh"}]'
                    ));
                }
                break;
            case 'remind_exam':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::REMIND_EXAM,
                        "",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '02/09/2020',
                        '27/07/2020'
                    ));
                }
                break;
            case 'invitation_offline_course':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::INVITATION_OFFLINE_COURSE,
                        "",
                        "Hy Quốc Cường",
                        '',
                        'How to love?',
                        '',
                        '',
                        'Hà Nội',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        'LOVE',
                        'English',
                        'DatDT',
                        '8h',
                        '17h',
                        '22/11/2020',
                        '3',
                        '02/09/2020'
                    ));
                }
                break;
            case 'enrol':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::ENROL,
                        "hycuong",
                        "Hy Quốc Cường",
                        'KH0019BAC',
                        'Khóa học quản trị doanh nghiệp',
                        '30/11/2019',
                        '30/11/2020',
                        '',
                        '',
                        ''
                    ));
                }
                break;

            default:
                break;
        }

    }

    public function apiCreateCourse(Request $request)
    {
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
            $sample = $request->input('sample');
            $course_place = $request->input('course_place');

            //check course info exist
            $courseInfo = MdlCourse::select('id')->where('shortname', $shortname)->first();

            if ($courseInfo) {
                $response->status = false;
                $response->message = __('ma_khoa_hoc_da_ton_tai_trong_he_thong');
                return response()->json($response);
            }

            //thực hiện insert dữ liệu
//            $path_avatar = '';
            if ($avatar) {
                $name_file = str_replace(' ', '', $shortname);
                $name_file = str_replace('/', '', $name_file);
                $name_file = str_replace('\\', '', $name_file);
                $name_file = utf8convert($name_file);
                $name = $name_file . '.' . $avatar->getClientOriginalExtension();
                $destinationPath = public_path('/upload/course/');
                $avatar->move($destinationPath, $name);
                $path_avatar = '/upload/course/' . $name;
            } else {
                $path_avatar = '/upload/course/default_course.jpg';
            }

            \DB::beginTransaction();
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
                $course->course_place = $course_place;
                $course->startdate = strtotime($startdate);
                $course->enddate = strtotime($enddate);
                $course->visible = 0;
            }
            $course->save();

            //insert dữ liệu điểm qua môn
            MdlCourseCompletionCriteria::create(array(
                'course' => $course->id,
                'criteriatype' => 6, //default là 6 trong trường hợp này
                'gradepass' => $pass_score
            ));

            $context_cate = MdlContext::where('contextlevel', '=', MdlUser::CONTEXT_COURSECAT)
                ->where('instanceid', '=', $category_id)->first();

            if ($context_cate) {
                //insert dữ liệu vào bảng mdl_context
                $mdl_context = MdlContext::firstOrCreate([
                    'contextlevel' => MdlUser::CONTEXT_COURSE,
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

            \DB::commit();

            $response->otherData = $course->id;
            $response->status = true;
            $response->message = __('tao_moi_khoa_hoc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            //$response->message = $e->getMessage();
            $response->message = __('loi_he_thong_thao_tac_that_bai');
        }
        return response()->json($response);
    }

    public function autoEnrol()
    {

        $role_id = 5; //student_role
        $category_id = 3; //category chung chi

        $all_courses = MdlCourse::where('category', $category_id)
            ->select('mdl_course.id', 'mdl_context.id as context_id', 'mdl_grade_items.id as grade_id')
            ->leftJoin('mdl_context', function ($join) {
                $join->on('mdl_context.instanceid', '=', 'mdl_course.id');
                $join->where('mdl_context.contextlevel', '=', MdlUser::CONTEXT_COURSE);
            })
            ->leftJoin('mdl_grade_items', function ($join) {
                $join->on('mdl_course.id', '=', 'mdl_grade_items.courseid');
                $join->where('mdl_grade_items.itemtype', '=', 'course');
            })
            ->get();

        $a = 0;
        $b = 0;
        if (count($all_courses) != 0) {
            $course_data_array = array();
            $students = MdlUser::where('roles.id', '=', 5)//Role hoc vien
            ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select(
                    'mdl_user.id',
                    'username',
                    'firstname',
                    'lastname',
                    'email'
                )
                ->orderBy(DB::raw('RAND()'))
                ->take(100)
                ->get();
            if (count($students) != 0) {
                $student_ids = array();
                foreach ($students as $student) {
                    $student_ids[] = $student->id;
                }
                foreach ($all_courses as $one_course) {
                    $a += count($student_ids);
                    $course_data_array[$one_course->id]['students'] = $student_ids;
                    $course_data_array[$one_course->id]['context_id'] = $one_course->context_id;
                    $course_data_array[$one_course->id]['grade_id'] = $one_course->grade_id;
                }
            }

            $courses = MdlUserEnrolments::where('mdl_course.category', $category_id)
                ->where('mdl_enrol.enrol', 'manual')
                ->where('roles.id', '=', $role_id)
                ->select(
                    'mdl_course.id',
                    'mdl_user_enrolments.userid'
                )
                ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                ->join('model_has_roles', 'mdl_user_enrolments.userid', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->get();


            $exists = array();
            if (count($courses) != 0) {
                foreach ($courses as $course) {
                    $exists[$course->id][] = $course->userid;
                }
            }

            foreach ($exists as $course_id => $user_ids) {
                $b += count($user_ids);
                if (array_key_exists($course_id, $course_data_array)) {
                    $course_data_array[$course_id]['students'] = array_diff($course_data_array[$course_id]['students'], $user_ids);
                }
            }

            $now = time();

//                echo $a. " - " . $b;die;

            foreach ($course_data_array as $course_id => $data) {
                $user_ids = $data['students'];
                $context_id = $data['context_id'] && strlen($data['context_id']) != 0 ? $data['context_id'] : 0;
                $grade_id = $data['grade_id'] && strlen($data['grade_id']) != 0 ? $data['grade_id'] : 0;

                $new_enrol = MdlEnrol::create([
                    'enrol' => 'manual',
                    'courseid' => $course_id,
                    'roleid' => $role_id,
                    'sortorder' => 0,
                    'expirythreshold' => 86400,
                    'timecreated' => $now,
                    'timemodified' => $now
                ]);
                $guest = MdlEnrol::firstOrCreate(
                    [
                        'enrol' => 'guest',
                        'courseid' => $course_id,
                        'roleid' => $role_id,
                        'sortorder' => 1
                    ],
                    [
                        'expirythreshold' => 86400,
                        'timecreated' => $now,
                        'timemodified' => $now
                    ]
                );
                $self = MdlEnrol::firstOrCreate(
                    [
                        'enrol' => 'self',
                        'courseid' => $course_id,
                        'roleid' => $role_id,
                        'sortorder' => 2
                    ],
                    [
                        'expirythreshold' => 86400,
                        'timecreated' => $now,
                        'timemodified' => $now
                    ]
                );
                foreach ($user_ids as $user_id) {
                    MdlUserEnrolments::create([
                        'enrolid' => $new_enrol->id,
                        'userid' => $user_id,
                        'timestart' => $now,
                        'timecreated' => $now,
                        'timemodified' => $now
                    ]);

                    $assigment = MdlRoleAssignments::firstOrCreate(
                        [
                            'roleid' => $role_id,
                            'userid' => $user_id,
                            'contextid' => $context_id
                        ]
                    );

                    //Tồn tại bản ghi trong bang mdl_grade_items
                    if ($grade_id != 0) {
                        //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                        $grade = MdlGradeGrade::firstOrCreate(
                            [
                                'userid' => $user_id,
                                'itemid' => $grade_id
                            ],
                            [
                                'timecreated' => $now,
                                'timemodified' => $now,
                                'created_at' => date("Y-m-d H:i:s", $now),
                                'updated_at' => date("Y-m-d H:i:s", $now),
                            ]
                        );
                    }

                    //Update mdl_course_completions
                    $course_completions = MdlCourseCompletions::firstOrCreate(
                        [
                            'userid' => $user_id,
                            'course' => $course_id
                        ],
                        [
                            'timeenrolled' => $now,
                        ]
                    );
                }
            }
        }

    }
}
