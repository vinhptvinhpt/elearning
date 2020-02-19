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

set_time_limit(0);

class EmailTemplateController extends Controller
{

    //
    public function viewIndex()
    {
        return view('email.template.index');
    }


    //
    public function apiGetListEmailTemplate()
    {
        $data = [];
        $configs = TmsConfigs::where('editor', 'checkbox')->get();
        if (count($configs) != 0) {
            foreach ($configs as $config) {
                switch ($config->target) {
                    case TmsNotification::ENROL:
                        $label = __('tham_gia_khoa_hoc');
                        break;
                    case TmsNotification::SUGGEST:
                        $label = __('gioi_thieu_khoa_hoc_ki_nang_mem');
                        break;
                    case TmsNotification::QUIZ_START:
                        $label = __('bat_dau_bai_kiem_tra');
                        break;
                    case TmsNotification::QUIZ_END:
                        $label = __('ket_thuc_bai_kiem_tra');
                        break;
                    case TmsNotification::QUIZ_COMPLETED:
                        $label = __('ket_qua_kiem_tra');
                        break;
                    case TmsNotification::REMIND_LOGIN:
                        $label = __('nhac_nho_dang_nhap');
                        break;
                    case TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE:
                        $label = __('nhac_nho_khoa_hoc_bat_buoc_sap_het_han');
                        break;
                    case TmsNotification::REMIND_ACCESS_COURSE:
                        $label = __('nhac_nho_tuong_tac_voi_cac_khoa_hoc');
                        break;
                    case TmsNotification::REMIND_EDUCATION_SCHEDULE:
                        $label = __('nhac_nho_hoan_thanh_lo_trinh_dao_tao');
                        break;
                    case TmsNotification::REMIND_UPCOMING_COURSE:
                        $label = __('thong_bao_khoa_hoc_sap_bat_dau');
                        break;
                    case TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY:
                        $label = __('firebase_server_key');
                        break;
                    case TmsConfigs::TARGET_FIREBASE_TOPIC:
                        $label = __('firebase_topic');
                        break;
                    case TmsNotification::REMIND_CERTIFICATE:
                        $label = __('thong_bao_chung_chi');
                        break;
                    default:
                        $label = $config->target;
                        break;
                }
                $config->label = $label;
                $data[] = $config;
            }
        }
        return response()->json($data);
    }

    public function viewEmailTemplateDetail($name_file)
    {
        switch ($name_file) {
            case TmsNotification::ENROL:
                $label = __('tham_gia_khoa_hoc');
                break;
            case TmsNotification::SUGGEST:
                $label = __('gioi_thieu_khoa_hoc_ki_nang_mem');
                break;
            case TmsNotification::QUIZ_START:
                $label = __('bat_dau_bai_kiem_tra');
                break;
            case TmsNotification::QUIZ_END:
                $label = __('ket_thuc_bai_kiem_tra');
                break;
            case TmsNotification::QUIZ_COMPLETED:
                $label = __('ket_qua_kiem_tra');
                break;
            case TmsNotification::REMIND_LOGIN:
                $label = __('nhac_nho_dang_nhap');
                break;
            case TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE:
                $label = __('nhac_nho_khoa_hoc_bat_buoc_sap_het_han');
                break;
            case TmsNotification::REMIND_ACCESS_COURSE:
                $label = __('nhac_nho_tuong_tac_voi_cac_khoa_hoc');
                break;
            case TmsNotification::REMIND_EDUCATION_SCHEDULE:
                $label = __('nhac_nho_hoan_thanh_lo_trinh_dao_tao');
                break;
            case TmsNotification::REMIND_UPCOMING_COURSE:
                $label = __('thong_bao_khoa_hoc_sap_bat_dau');
                break;
            case TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY:
                $label = __('firebase_server_key');
                break;
            case TmsConfigs::TARGET_FIREBASE_TOPIC:
                $label = __('firebase_topic');
                break;
            case TmsNotification::REMIND_CERTIFICATE:
                $label = __('thong_bao_chung_chi');
                break;
            default:
                $label = '';
                break;
        }
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

    public function getContentFile()
    {
        //path to files folder contain json file
        // echo public_path();
        $dir = public_path() . "/files/email";
        // //return file or foler in directory above
        $temp_files = scandir($dir);
        //get content of file with name
        $string = file_get_contents(public_path() . "/files/email/template.json");
        // //decode content of file above=
        $data = json_decode($string, true);

        return response()->json($string);
    }

    public function writeToJson( Request $rq)
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
            } else {
                $path = public_path() . '/files/email/' . $name_file . ".json";

                //get content of file with name
                $string = file_get_contents(public_path() . "/files/email/" . $name_file . ".json");
                //decode content of file above
                $data = json_decode($string, true);
                //set values
                $data['StringHello'] = $rq['StringHello'];
                $data['StringContent'] = $rq['StringContent'];
                $data['StringFullName'] = $rq['StringFullName'];
                $data['StringUserName'] = $rq['StringUserName'];
                $data['StringThanks'] = $rq['StringThanks'];
                //check name file and set values continue
                switch ($name_file) {
                    case 'remind_upcoming_course':
                    case 'remind_login':
                    case 'suggest':
                    case 'remind_access_course':
                    case 'remind_expire_required_course':
                    case 'remind_education_schedule':
                        {
                            //set value to json file
                            $data['StringIntro'] = $rq['StringIntro'];
                        }
                        break;
                    case 'enrol':
                        {
                            $data['StringIntro'] = $rq['StringIntro'];
                            $data['IdCourse'] = $rq['IdCourse'];
                            $data['NameCourse'] = $rq['NameCourse'];
                            $data['TimeStart'] = $rq['TimeStart'];
                            $data['TimeDone'] = $rq['TimeDone'];
                            $data['Address'] = $rq['Address'];
                            $data['StringLogin'] = $rq['StringLogin'];
                        }
                        break;
                    case 'quiz_completed':
                        {
                            $data['IdCourse'] = $rq['IdCourse'];
                            $data['NameCourse'] = $rq['NameCourse'];
                            $data['NameExam'] = $rq['NameExam'];
                            $data['ContentExam'] = $rq['ContentExam'];
                        }
                        break;
                    case 'quiz_end':
                        {
                            $data['IdCourse'] = $rq['IdCourse'];
                            $data['NameCourse'] = $rq['NameCourse'];
                            $data['NameExam'] = $rq['NameExam'];
                            $data['ContentExam'] = $rq['ContentExam'];
                            $data['StringCheck'] = $rq['StringCheck'];
                        }
                        break;
                    case 'quiz_start':
                        {
                            $data['IdCourse'] = $rq['IdCourse'];
                            $data['NameCourse'] = $rq['NameCourse'];
                            $data['NameExam'] = $rq['NameExam'];
                            $data['ContentExam'] = $rq['ContentExam'];
                            $data['TimeStart'] = $rq['TimeStart'];
                            $data['StringCheck'] = $rq['StringCheck'];
                        }
                        break;
                    default:
                        break;
                }

                // Write File
                $newJsonString = json_encode($data, JSON_UNESCAPED_UNICODE);
                file_put_contents($path, $newJsonString);
            }

            $response->status = true;

            switch ($name_file) {
                case TmsNotification::ENROL:
                    $label = __('tham_gia_khoa_hoc');
                    break;
                case TmsNotification::SUGGEST:
                    $label = __('gioi_thieu_khoa_hoc_ki_nang_mem');
                    break;
                case TmsNotification::QUIZ_START:
                    $label = __('bat_dau_bai_kiem_tra');
                    break;
                case TmsNotification::QUIZ_END:
                    $label = __('ket_thuc_bai_kiem_tra');
                    break;
                case TmsNotification::QUIZ_COMPLETED:
                    $label = __('ket_qua_kiem_tra');
                    break;
                case TmsNotification::REMIND_LOGIN:
                    $label = __('nhac_nho_dang_nhap');
                    break;
                case TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE:
                    $label = __('nhac_nho_khoa_hoc_bat_buoc_sap_het_han');
                    break;
                case TmsNotification::REMIND_ACCESS_COURSE:
                    $label = __('nhac_nho_tuong_tac_voi_cac_khoa_hoc');
                    break;
                case TmsNotification::REMIND_EDUCATION_SCHEDULE:
                    $label = __('nhac_nho_hoan_thanh_lo_trinh_dao_tao');
                    break;
                case TmsNotification::REMIND_UPCOMING_COURSE:
                    $label = __('thong_bao_khoa_hoc_sap_bat_dau');
                    break;
                case TmsConfigs::TARGET_NOTIFICATION_SERVER_KEY:
                    $label = __('firebase_server_key');
                    break;
                case TmsConfigs::TARGET_FIREBASE_TOPIC:
                    $label = __('firebase_topic');
                    break;
                case TmsNotification::REMIND_CERTIFICATE:
                    $label = __('thong_bao_chung_chi');
                    break;
                default:
                    $label = '';
                    break;
            }

            $response->message = __('sua_thanh_cong_template') . $label;
        } catch (Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    ///email_template/sendDemo/{name_file}
    public function demoSendMail($name_file)
    {
        $name_action = '';
        switch ($name_file) {
            case 'remind_upcoming_course':
                {
                    $next_3_days = time() + 86400 * 3;
                    $next_7_days = time() + 86400 * 7;
                    $courses = MdlCourse::whereIn('category', [3, 4, 5])
                        ->where('startdate', '>', $next_3_days)
                        ->where('enddate', '<', $next_7_days)
                        ->get();
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::REMIND_UPCOMING_COURSE,
                        "hycuong",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        $courses
                    ));
                }
                break;
            case 'remind_login':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::REMIND_LOGIN,
                        "hycuong2",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    ));
                }
                break;
            case 'suggest':
                {
                    $courses = MdlCourse::all()->where('category', "=", 4)->random(rand(3, 5));
                    $countCourse = count($courses);
                    if ($countCourse > 0) {
                        Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                            TmsNotification::SUGGEST,
                            "hycuong3",
                            "Hy Quốc Cường",
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $courses
                        ));
                    }
                }
                break;
            case 'remind_access_course':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::REMIND_ACCESS_COURSE,
                        "hycuong",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '[{"course_id":14,"course_code":"BGT655","course_name":"Đào tạo sản phẩm Power 6\/55","startdate":1566147600,"enddate":1597683600,"course_place":null},{"course_id":15,"course_code":"BGTMAX3D","course_name":"Đào tạo sản phẩm Max 3D","startdate":1566147600,"enddate":1597683600,"course_place":null}]'
                    ));
                }
                break;
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
                        '[{"course_id":14,"course_code":"BGT655","course_name":"Đào tạo sản phẩm Power 6\/55","startdate":1566147600,"enddate":1597683600,"course_place":null},{"course_id":15,"course_code":"BGTMAX3D","course_name":"Đào tạo sản phẩm Max 3D","startdate":1566147600,"enddate":1597683600,"course_place":null}]'
                    ));
                }
                break;
            case 'remind_education_schedule':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::REMIND_EDUCATION_SCHEDULE,
                        "hycuong",
                        "Hy Quốc Cường",
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '[{"course_id":14,"course_code":"BGT655","course_name":"Đào tạo sản phẩm Power 6\/55","startdate":1566147600,"enddate":1597683600,"course_place":"YOQLXM44"},{"course_id":15,"course_code":"BGTMAX3D","course_name":"Đào tạo sản phẩm Max 3D","startdate":1566147600,"enddate":1597683600,"course_place":"OBKQCCRR"},{"course_id":34,"course_code":"BGTMAX4D_1","course_name":"Đào tạo sản phẩm Max 4D chép 1","startdate":1566172800,"enddate":1597708800,"course_place":"IYPAQXX"}]'
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
            case 'quiz_completed':
                {
                    $content = '{"quiz_id":"23","quiz_name":"Bu00e0i kiu1ec3m su1ea3n phu1ea9m Mega 6/45"}';
                    if (strlen($content) != 0) {
                        $content_array = json_decode($content, true);
                        $quiz_id = isset($content_array['quiz_id']) ? $content_array['quiz_id'] : '';
                        if (strlen($quiz_id) != 0 && is_numeric($quiz_id)) {
                            $quiz_data = MdlQuiz::where('mdl_quiz.id', $quiz_id)
                                ->where('mdl_quiz.attempts', 1)
                                ->join('mdl_quiz_attempts', 'mdl_quiz.id', '=', 'mdl_quiz_attempts.quiz')
                                ->select(
                                    'mdl_quiz.name',
                                    'mdl_quiz.sumgrades',
                                    'mdl_quiz_attempts.sumgrades as attempt_sumgrades'
                                )
                                ->first();
                            if (isset($quiz_data)) {
                                Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                                    TmsNotification::QUIZ_COMPLETED,
                                    "hycuong",
                                    "Hy Quốc Cường",
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $quiz_data
                                ));
                            }
                        }
                    }
                }
                break;
            case 'quiz_end':
                {
                    $content = '{"quiz_id":"23","quiz_name":"Bu00e0i kiu1ec3m su1ea3n phu1ea9m Mega 6/45"}';
                    if (strlen($content) != 0) {
                        $content_array = json_decode($content, true);
                        $quiz_id = isset($content_array['quiz_id']) ? $content_array['quiz_id'] : '';
                        if (strlen($quiz_id) != 0 && is_numeric($quiz_id)) {
                            $quiz_data = MdlQuiz::where('id', $quiz_id)
                                ->select(
                                    'mdl_quiz.name'
                                )
                                ->first();
                            if (isset($quiz_data)) {
                                Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                                    TmsNotification::QUIZ_END,
                                    "hycuong",
                                    "Hy Quốc Cường",
                                    'KHKM0192AC',
                                    'Khóa học kỹ năng mềm',
                                    '',
                                    '',
                                    '',
                                    '30/11/2019',
                                    $quiz_data
                                ));
                            }
                        }
                    }
                }
                break;
            case 'quiz_start':
                {
                    $content = '{"quiz_id":"23","quiz_name":"Bu00e0i kiu1ec3m su1ea3n phu1ea9m Mega 6/45"}';
                    if (strlen($content) != 0) {
                        $content_array = json_decode($content, true);
                        $quiz_id = isset($content_array['quiz_id']) ? $content_array['quiz_id'] : '';
                        if (strlen($quiz_id) != 0 && is_numeric($quiz_id)) {
                            $quiz_data = MdlQuiz::where('id', $quiz_id)
                                ->select(
                                    'mdl_quiz.name'
                                )
                                ->first();
                            if (isset($quiz_data)) {
                                Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                                    TmsNotification::QUIZ_START,
                                    "hycuong",
                                    "Hy Quốc Cường",
                                    'KHKM0192AC',
                                    'Khóa học kỹ năng mềm',
                                    '',
                                    '',
                                    '',
                                    '30/11/2019',
                                    $quiz_data
                                ));
                            }
                        }
                    }
                }
                break;
            case 'remind_certificate':
                {
                    Mail::to("duongtiendat.it@gmail.com")->send(new CourseSendMail(
                        TmsNotification::REMIND_CERTIFICATE,
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
            $response->message = $e->getMessage();
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


    //
    public function viewTestIndex()
    {
        return view('test.index');
    }


    //import excel multiple sheets
    public function apiImportExcel(Request $request)
    {
        try{
            set_time_limit(0);
            if (!$request->file('file')) {
                return response()->json([
                    'fileError' => 'error'
                ]);
            }

            $extension = $request->file('file')->getClientOriginalExtension();

            $array = (new DataImport)->toArray($request->file('file'), '', '');

            if ($extension != 'xls' && $extension != 'xlsx') {
                return response()->json([
                    'extension' => 'error'
                ]);
            }

            $countError = 0;

            $this->importOutput['userOuput'] = [];
            $this->importOutput['agencyOuput'] = [];
            $this->importOutput['posHaveOuput'] = [];
            $this->importOutput['posNoOuput'] = [];

            $cityId = $request->input('city_id');

            $listEmployees = [];
            $listAgencies = [];
            $listPointOfSalesHaveCertificate = [];
            $listPointOfSalesNoCertificate = [];

            //set value
            if (isset($array['NhanVien'])) {
                $listEmployees = $array['NhanVien'];
            }
            if (isset($array['DaiLy'])) {
                $listAgencies = $array['DaiLy'];
            }
            if (isset($array['DiemBanHang co giay chung nhan'])) {
                $listPointOfSalesHaveCertificate = $array['DiemBanHang co giay chung nhan'];
            }
            if (isset($array['DiemBanHang k giay chung nhan'])) {
                $listPointOfSalesNoCertificate = $array['DiemBanHang k giay chung nhan'];
            }
            \DB::beginTransaction();


            //insert đại lý
            array_shift($listAgencies);
            array_shift($listAgencies);
            foreach ($listAgencies as $row) {
                $agency = [];
                $agencyOuput = [];
                //tên đại lý
                $agency['name'] = $row[2];
                try {
                    //mã đại lý
                    $agency['code'] = $row[1];
                    //địa chỉ
                    $agency['address'] = $row[3];
                    //
                    //kiểm tra xem trưởng đại lý đã tồn tại hay chưa
                    //gán id của user mặc định bằng 0
                    $user_id = 0;
                    $username = $this->vn_to_str($row[5]);
                    $cmtnd = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[6]);
                    $phone = str_replace(["''", "'", ' ', '.'], '', $row[4]);
                    if(empty($cmtnd))
                        $cmtnd = $this->RandomCMTND();
                    $resultCheck = $this->CreateUser('manageagents', $username, $row[7], 0, $cmtnd, $row[5], $phone, $username, '', '', '', '', 0, 0);
                    $valueCheck = $resultCheck[0]['code'];
                    //bằng 0 là thêm thất bại
                    if ($valueCheck == 0) {
                        $agencyOuput['agencyname'] = $agency['name'];
                        $agencyOuput['username'] = '';
                        $agencyOuput['password'] = '';
                        $agencyOuput['status'] = 'error';
                        $agencyOuput['message'] = $resultCheck[0]['message'];
                        array_push($this->importOutput['agencyOuput'], $agencyOuput);
                    } else {
                        //kiểm tra xem đại lý đã tồn tại hay chưa
                        $checkAgency = TmsBranch::where('code', '=', $agency['code'])->first();
                        //nếu đã tồn tại thì update thông tin
                        if (!empty($checkAgency)) {
                            $checkAgency->name = $agency['name'];
                            $checkAgency->address = $agency['address'];
                            $checkAgency->user_id = $valueCheck;
                            $checkAgency->save();
                            $agencyOuput['agencyname'] = $agency['name'];
                            $agencyOuput['status'] = 'success';
                            $agencyOuput['username'] = $username;
                            $agencyOuput['password'] = '';
                            $agencyOuput['message'] = 'Cập nhật đại lý thành công';
                            array_push($this->importOutput['agencyOuput'], $agencyOuput);
                        } else {
                            $agencyId = $this->CreateBranch($agency['name'], $agency['code'], $valueCheck, $cityId, $agency['address']);
                            $agencyOuput['agencyname'] = $agency['name'];
                            $agencyOuput['status'] = 'success';
                            $agencyOuput['username'] = $username;
                            if ($resultCheck[0]['type'] == 'update') {
                                $agencyOuput['password'] = '123456789';
                            } else {
                                $agencyOuput['password'] = '';
                            }
                            $agencyOuput['message'] = 'Thêm đại lý thành công';
                            array_push($this->importOutput['agencyOuput'], $agencyOuput);
                        }
                    }
                } catch (\Exception $e) {
                    // \DB::rollBack();
                    $agencyOuput['agencyname'] = $agency['name'];
                    $agencyOuput['username'] = '';
                    $agencyOuput['password'] = '';
                    $agencyOuput['status'] = 'error';
                    $agencyOuput['message'] = 'Lỗi dữ liệu, kiểm tra lại';
                    array_push($this->importOutput['agencyOuput'], $agencyOuput);
                }
            }



            //insert điểm bán hàng không có giấy chứng nhận
            array_shift($listPointOfSalesNoCertificate);
            array_shift($listPointOfSalesNoCertificate);
            foreach ($listPointOfSalesNoCertificate as $row) {
                $pointofsale = [];
                $posNoOuput = [];
                //tên điểm bán hàng
                $pointofsale['name'] = $row[2];

                try {
                    if (is_numeric($row[1])) { //check row has values
                        //mã điểm bán hàng;
                        $pointofsale['code'] = $row[1];
                        //địa chỉ
                        $pointofsale['address'] = $row[3];
                        //mã đại lý
                        $branchCode = $row[8];
                        //echo $branchCode;die; //=LEFT(B3,8) => WithCalculatedFormulas; not working
                        if (!is_numeric($branchCode)) {
                            $posNoOuput['username'] = $pointofsale['code'];
                            $posNoOuput['status'] = 'error';
                            $posNoOuput['message'] = 'Mã đại lý không đúng định dạng/bỏ trống: [ ' . $branchCode . ' ]';
                            array_push($this->importOutput['posNoOuput'], $posNoOuput);
                        }
                        else {
                            $username = $this->vn_to_str($row[5]);
                            $cmtnd = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[6]);
                            $phone = str_replace(["''", "'", ' ', '.'], '', $row[4]);
                            $email = $row[7];
                            if (empty($email)) {
                                $email = $username . '@gmail.com';
                            }
                            if(empty($cmtnd))
                                $cmtnd = $this->RandomCMTND();
                            $resultCheck = $this->CreateUser('managepos', $username, $email, 0, $cmtnd, $row[5], $phone, $username, '', '', '', '', 0, 0);
                            $valueCheck = $resultCheck[0]['code'];
                            if ($valueCheck == 0) {
                                $posNoOuput['username'] = $pointofsale['code'];
                                $posNoOuput['status'] = 'error';
                                $posNoOuput['message'] = 'Thông tin trưởng điểm bán của điểm bán có mã là: ' . $pointofsale['code'] . ' không hợp lệ. ' . $resultCheck[0]['message'];
                                array_push($this->importOutput['posNoOuput'], $posNoOuput);
                            } else {
                                //kiểm tra xem đại lý đã tồn tại hay chưa   //Nếu chưa thì tạo
                                $checkBranch = $this->CreateBranch($branchCode, $branchCode, $valueCheck, $cityId, '');
                                $branchId = $checkBranch['code'];
                                if ($branchId !== 0) {
                                    //kiểm tra xem điểm bán hàng đã tồn tại hay chưa
                                    $checkSaleroom = $this->CreateSaleRoom($pointofsale['name'], $pointofsale['code'], $branchId, $valueCheck, $pointofsale['address']);
                                    if($checkSaleroom['code'] > 0)
                                    {
                                        $posNoOuput['username'] = $pointofsale['code'];
                                        $posNoOuput['status'] = 'success';
                                        $posNoOuput['message'] = ' mã điểm bán ' . $pointofsale['code'] . ' ' . $checkSaleroom['message'];
                                        array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                    }
                                    else{
                                        $posNoOuput['username'] = $pointofsale['code'];
                                        $posNoOuput['status'] = 'error';
                                        $posNoOuput['message'] = ' mã điểm bán ' . $pointofsale['code'] . ' Thêm thất bại';
                                        array_push($this->importOutput['posNoOuput'], $posNoOuput);
                                    }
                                    // \DB::commit();
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // \DB::rollBack();
                    $posNoOuput['username'] = $pointofsale['code'];
                    $posNoOuput['status'] = 'error';
                    $posNoOuput['message'] = 'Lỗi dữ liệu, kiểm tra lại';
                    array_push($this->importOutput['posNoOuput'], $posNoOuput);
                }
            }


            //insert điểm bán hàng có giấy chứng nhận
            array_shift($listPointOfSalesHaveCertificate);
            array_shift($listPointOfSalesHaveCertificate);
            foreach ($listPointOfSalesHaveCertificate as $row) {
                $pointofsale = [];
                $posOuput = [];
                $posOuput['message'] = 'Điểm bán: ';
                $posOuput['username'] = $row[1];
                $userId = null;
                try {
                    if (is_numeric($row[1])) { //check row has values
                        //tên điểm bán
                        $pointofsale['name'] = $row[2];
                        //mã điểm bán hàng;
                        $pointofsale['code'] = $row[1];
                        //địa chỉ
                        $pointofsale['address'] = $row[3];
                        //trưởng điểm bán
                        $userId = $row[5];
                        //mã đại lý
                        $branchCode = $row[6];
                        if (!is_numeric($branchCode)) {
                            $posNoOuput['username'] = $pointofsale['code'];
                            $posNoOuput['status'] = 'error';
                            $posNoOuput['message'] = 'Mã đại lý không đúng định dạng / bỏ trống: [ ' . $branchCode . ' ]';
                            array_push($this->importOutput['posHaveOuput'], $posNoOuput);
                        }
                        else {
                            //Gán sẵn là không thỏa mãn
                            $checkUserTM = 0;
                            if (!empty($userId)) {
                                $checkUser = MdlUser::where('id', '=', $userId)->first();
                                if (empty($checkUser)) {
                                    $checkCertificate = StudentCertificate::where('code', '=', $userId)->first();
                                    if (!empty($checkCertificate)) {
                                        $userId = $checkCertificate->userid;
                                        $checkUserTM = 1;
                                    }
                                } else {
                                    $checkUserTM = 1;
                                }
                            }
                            if($checkUserTM == 0){
                                $userId = null;
                            }
                            $checkUserTM = 1;
                            if ($checkUserTM !== 0) {
                                // \DB::beginTransaction();
                                //kiểm tra xem đại lý đã tồn tại hay chưa
                                $checkBranch = $this->CreateBranch($branchCode, $branchCode, $userId, $cityId, '');
                                $branchId = $checkBranch['code'];
                                //kiểm tra xem điểm bán hàng đã tồn tại hay chưa
                                $CheckpointOfSale = $this->CreateSaleRoom($pointofsale['name'], $pointofsale['code'], $branchId, $userId, $pointofsale['address']);

                                $posOuput['message'] .= ' mã điểm bán ' . $pointofsale['code'] . ' ' . $CheckpointOfSale['message'];
                                $posOuput['code'] = 1;
                                $posOuput['status'] = 'success';
                                array_push($this->importOutput['posHaveOuput'], $posOuput);
                                // \DB::commit();
                            } else {
                                $posOuput['message'] .= ' mã nhân viên ' . $posOuput['username'] . ' không tồn tại';
                                $posOuput['code'] = 0;
                                $posOuput['status'] = 'error';
                                array_push($this->importOutput['posHaveOuput'], $posOuput);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // \DB::rollBack();
                    //dd($e);
                    $posOuput['message'] .= ' có mã điểm bán ' . $posOuput['username'] . ' đã xảy ra lỗi khi thêm';
                    $posOuput['code'] = 0;
                    $posOuput['status'] = 'error';
                    array_push($this->importOutput['posHaveOuput'], $posOuput);
                }
            }

            $this->importOutput['success'] = 0;
            $this->importOutput['error'] = 0;

            //insert nhân viên
            //loại bỏ 2 hàng đầu tiên
            array_shift($listEmployees);
            array_shift($listEmployees);
            $stt = 0;
            foreach ($listEmployees as $row) {
                $stt++;
                $user = [];
                $userOuput = [];
                try {
                    $user['username'] = $row[1];
                    //tên đầy đủ
                    $user['fullname'] = $row[6];
                    //username
                    iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $user['fullname']);
                    $user['username'] = empty($row[4]) ? $this->vn_to_str($user['fullname']) : $row[4];
                    $certificateCode = empty($row[1]) ? $row[2] : $row[1];
                    //Giới tính
                    $user['sex'] = $row[13] == 'Nam' or $row[13] == 'nam' ? 1 : 0;
                    //cmtnd
                    $user['cmtnd'] = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[8]);
                    //email
                    $user['email'] = $row[7];
                    if (empty($user['email'])) {
                        $user['email'] = $user['username'] . '@gmail.com';
                    }
                    //số điện thoại
                    $user['phone'] = str_replace(["''", "'", ' ', '.'], '', $row[11]);
                    $user['phone'] = str_replace(' ', '', $user['phone']);
                    $user['phone'] = str_replace('.', '', $user['phone']);
                    //địa chỉ
                    $user['address'] = $row[12];
                    //ma nhan vien
                    $user['code'] = $row[1];

                    //mã đại lý cấp giấy chứng nhận
                    $agencyCode = $row[3];
                    //mã đơn vị quản lý
                    $managementCode = $row[9];
                    //Sinh nhat
                    $user['dob'] = $row[10];
                    //Kiểm tra nếu cột ngày tháng năm mà là dạng ngoài general (date, custom, ...) -> convert sang datetime
                    //ngày tháng năm sinh
                    if (is_numeric($user['dob'])) {
                        $getDate = Date::excelToDateTimeObject($row[10]);
                        $user['dob'] = $getDate->format('d-m-Y');
                    }


                    $timestamp = 0;

                    if (!empty($user['dob']) && strlen($user['dob']) > 7) {
                        //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                        if (strpos($user['dob'], '/') !== false) {
                            $newDate = strtotime(str_replace('/', '-', $user['dob']));
                        }
                        else if (strpos($user['dob'], '.') !== false) {
                            $newDate = strtotime(str_replace('.', '-', $user['dob']));
                        }
                        else if (strpos($user['dob'], '-') !== false) {
                            $newDate = strtotime($user['dob']);
                        } //nếu chứa chữ cái thì không hợp lệ
                        else if (preg_match("/[a-z]/i", $user['dob'])) {
                            $newDate = 0;
                        } else if (is_numeric($user['dob'])) {
                            $newDate = (int)$user['dob'];
                        } //còn nếu type của cột dob là dạng date sẽ convert sang timestamp
                        else {
                            $newDate = Date::excelToTimestamp($user['dob']);
                        }
                        //gán timestamp = newdate
                        $timestamp = $newDate;
                    }
                    //ngày bắt đầu làm
                    $user['start_date'] = $row[14];
                    //Ngày bắt đầu làm
                    if (is_numeric($user['start_date'])) {
                        $getDateW = Date::excelToDateTimeObject($row[10]);
                        $user['start_date'] = $getDateW->format('d-m-Y');
                    }

                    //validate ngày bắt đầu làm
                    $timestamp_start = 0;
                    if (!empty($user['start_date']) && strlen($user['start_date']) > 7) {
                        //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                        if (strpos($user['start_date'], '/') !== false) {
                            $newDate_start = strtotime(str_replace('/', '-', $user['start_date']));
                        } else if (strpos($user['start_date'], '-') !== false) {
                            $newDate_start = strtotime($user['start_date']);
                        } //nếu chứa chữ cái thì không hợp lệ
                        else if (preg_match("/[a-z]/i", $user['start_date'])) {
                            $newDate_start = 0;
                        } else if (is_numeric($user['start_date'])) {
                            $newDate_start = (int)$user['start_date'];
                        } //còn nếu type của cột dob là dạng date sẽ convert sang timestamp
                        else {
                            $newDate_start = Date::excelToTimestamp($user['start_date']);
                        }
                        $timestamp_start = $newDate_start;
                    }

                    //Tình trạng làm việc
                    $user['working_status'] = ($row[15] == 1) ? 1 : 0;

                    //Da cap giay chung nhan
                    $user['confirm'] = 0;
                    $validUTF8 = !(false === mb_detect_encoding($row[6], 'UTF-8', true));

                    if ($validUTF8) {
                        if(empty($user['cmtnd']))
                            $user['cmtnd'] = $this->RandomCMTND();
                        $resultCheck = $this->CreateUser(
                            'student',
                            $user['username'],
                            $user['email'],
                            0,
                            $user['cmtnd'],
                            $user['fullname'],
                            $user['phone'],
                            $user['code'],
                            $user['address'],
                            $user['sex'],
                            $timestamp,
                            $timestamp_start,
                            $user['working_status'],
                            $managementCode
                        );

                        $user_id = !empty($resultCheck) ? $resultCheck[0]['code'] : 0;

                        if ($user_id != 0) {
                            if (!empty($certificateCode)) {
                                $student = StudentCertificate::where('userid', $user_id)->first();
                                //nếu học viên đã có mã thì không làm gì cả
                                if (!$student) {
                                    //update status to 1
                                    StudentCertificate::create([
                                        'userid' => $user_id,
                                        'code' => $certificateCode,
                                        'status' => 1,
                                        'timecertificate' => time()
                                    ]);
                                }
                            }


//                            $textMsg = '';
                            $this->importOutput['success']++;
                            $userOuput['username'] = $user['username'];
                            $userOuput['fullname'] = $user['fullname'];
                            if ($resultCheck[0]['type'] == 'update') {
                                $userOuput['password'] = '';
                            } else {
                                $userOuput['password'] = '123456789';
                            }
                            $userOuput['status'] = 'success';
                            $userOuput['cmtnd'] = $user['cmtnd'];

                            $userOuput['message'] = $resultCheck[0]['message'];
//                            if (!empty($textMsg)) {
//                                $userOuput['message'] = $textMsg;
//                            }
                            $userOuput['code'] = $user_id;
                            array_push($this->importOutput['userOuput'], $userOuput);
                        }
                        else {
                            $countError++;
                            $userOuput['username'] = '';
                            $userOuput['fullname'] = $user['fullname'];
                            $userOuput['status'] = 'error';
                            $userOuput['cmtnd'] = $user['cmtnd'];
                            $userOuput['password'] = '';
                            $userOuput['message'] = $resultCheck[0]['message'];
                            array_push($this->importOutput['userOuput'], $userOuput);
                        }
                    }
                    else {
                        $countError++;
                        $userOuput['username'] = '';
                        $userOuput['fullname'] = $user['fullname'];
                        $userOuput['status'] = 'error';
                        $userOuput['cmtnd'] = $user['cmtnd'];
                        $userOuput['password'] = '';
                        $userOuput['message'] = 'Tên không đúng định dạng UTF-8';
                        array_push($this->importOutput['userOuput'], $userOuput);
                    }
                }
                catch (\Exception $e) {
                    $countError++;
                    $userOuput['username'] = '';
                    $userOuput['fullname'] = $user['fullname'];
                    $userOuput['password'] = '';
                    $userOuput['status'] = 'error';
                    $userOuput['stt'] = $row[0];
                    $userOuput['cmtnd'] = str_replace([' ', 'CMND', 'CCCD', 'MST', ':', "''", "'", '.'], '', $row[8]);
                    $userOuput['message'] = $e->getMessage();
//                $userOuput['message'] = 'Lỗi dữ liệu, kiểm tra lại';
                    array_push($this->importOutput['userOuput'], $userOuput);
                }
            }

            //Export file for result
            $dataExport = [
                'NhanVien' => $this->importOutput['userOuput'],
                'DaiLy' => $this->importOutput['agencyOuput'],
                'DiemBanHang co giay chung nhan' => $this->importOutput['posHaveOuput'],
                'DiemBanHang k giay chung nhan' => $this->importOutput['posNoOuput']
            ];

            $filename_org = "error_$cityId.xlsx";
            if (Storage::exists($filename_org)) {
                // do something
                Storage::delete($filename_org);
            }

            $filename = "error.xlsx";
            //xóa file cũ
            //$myFile = '/app/'.$filename;
            if (Storage::exists($filename)) {
                // do something
                Storage::delete($filename);
            }

            //ghi file vào thư mục storage
            $exportExcel = new ListMismatchData($dataExport);
            $exportExcel->store($filename, '', Excel::XLSX);
            $exportExcel->store($filename_org, '', Excel::XLSX);

            \DB::commit();

            return response()->json(status_message('success', __('cap_nhat_thanh_cong')));
        }
        catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function vn_to_str($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);

        $str = str_replace(' ', '', $str);
        $str = strtolower($str);

        return $str;
    }

    public function CreateBranch($name, $code, $user_id, $city, $address)
    {
        try {
            $check_name = DB::table('tms_branch as tb')
                ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
                ->where('tb.name', '=', $name)
                ->where('tcb.city_id', '=', $city)
                ->count();
            if ($check_name > 0) {
                $Branch = DB::table('tms_branch as tb')
                    ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
                    ->where('tb.name', '=', $name)
                    ->where('tcb.city_id', '=', $city)->first();
                return [
                    'code' => $Branch->id,
                    'message' => 'Tên Đại lý đã tồn tại.'
                ];
            }

            $check_branch = TmsBranch::where('code', $code)->count();
            if ($check_branch > 0) {
                $Branch = TmsBranch::where('code', $code)->first();
                return [
                    'code' => $Branch->id,
                    'message' => 'Mã Đại lý đã tồn tại.'
                ];
            }

//            \DB::beginTransaction();
            $tmsBranch = new TmsBranch;
            $tmsBranch->name = $name;
            $tmsBranch->code = $code;
            $tmsBranch->user_id = $user_id;
            //$tmsBranch->description = $description;
            $tmsBranch->address = $address;
            $tmsBranch->save();
            if ($city) {
                $cityBranch = new TmsCityBranch;
                $cityBranch->city_id = $city;
                $cityBranch->branch_id = $tmsBranch->id;
                $cityBranch->save();
            }

            if ($user_id && $user_id != 0 && !has_user_market($user_id)) {
                add_role_for_user($user_id);
            }

            if (has_user_market()) {
                $role_organize = new TmsRoleOrganize;
                $role_organize->user_id = Auth::id();
                $role_organize->organize_id = $tmsBranch->id;
                $role_organize->type = 'branch';
                $role_organize->save();
            }

            devcpt_log_system('organize', '/system/organize/branch/edit/' . $tmsBranch->id, 'create', 'Thêm mới Đại lý: ' . $name);
//            \DB::commit();
            return [
                'code' => $tmsBranch->id,
                'message' => 'Thêm đại lý thành công'
            ];
        } catch (Exception $e) {
//            \DB::rollBack();
            return [
                'code' => 0,
                'message' => 'Lỗi hệ thống'
            ];
        }
    }

    public function CreateUser(
        $role_name, $username, $email, $confirm, $cmtnd,
        $fullname, $phone, $code, $address, $sex, $timestamp, $timestamp_start, $working_status, $managementCode
    )
    {
        $importOutput['userOuput'] = [];
        $userOuput = [];
        $checkTM = 1;
        $newUserId = 0;
        $userOuput['message'] = '';
        $userOuput['type'] = '';
        try {

            if (empty($email) || empty($cmtnd) || empty($username) || empty($fullname)) {
                $userOuput['username'] = $username;
                $userOuput['message'] .= ' Chi tiết : ';
                $misings = array();

                if (!$username) {
                    $misings[]= 'Tài khoản';
                }
                if (!$email) {
                    $misings[] = 'Email';
                }
                if (!$cmtnd) {
                    $misings[] = 'Số CMTND';
                }
                if (!$fullname) {
                    $misings[] = 'Họ và tên';
                }
                $userOuput['message'] .= implode(", ", $misings) . ' không được để trống.';
                $userOuput['status'] = 'error';
                $userOuput['type'] = 'error';
                $userOuput['code'] = 0;
                $checkTM = 0;
            }
            //kiểm tra nếu tồn tại các trường và các trường đó không phải là số => sai định dạng
            $array = [
                'phone' => $phone,
                'cmtnd' => $cmtnd,
                'confirm' => $confirm
            ];
            $check = validate_fields($array, [
                'phone' => 'phone',
                'cmtnd' => 'text',
                'confirm' => 'boolean'
            ]);
            if(!empty($check)){
                $msg = [];
                foreach($check as $item=>$value){
                    switch ($item)
                    {
                        case 'cmtnd':
                            $item = "Chứng minh thư nhân dân";
                            break;
                        case 'phone':
                            $item = "Số điện thoại";
                            break;
                        case 'confirm':
                            $item = "Confirm";
                            break;
                        default:
                            break;
                    }
                    $msg[]= $item. ': '.$value;
                }
                $userOuput['message'] .= 'Thông tin: '. implode(", ", $msg);
                $userOuput['status'] = 'error';
                $userOuput['code'] = 0;
                $userOuput['type'] = 'error';
                $checkTM = 0;
            }
            $usernameNew = $username;

            //Nếu thỏa mãn
            if ($checkTM == 1)
            {
                $nameExpl = explode(' ', $fullname);
                $rowname = count($nameExpl);
                $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
                $lastname = str_replace($nameExpl[$rowname - 1], '', $fullname);
                $lastname = $lastname ? $lastname : '';

                $checkUsername = MdlUser::where('username', 'like', "{$username}%")->get();
                $checkedUsers = [];
                //  \DB::beginTransaction();
//                \DB::disableQueryLog();
                if (count($checkUsername) > 0) {
                    foreach ($checkUsername as $user) {
                        $getTMSUser = TmsUserDetail::where('user_id', $user->id)->first();
                        if(!empty($getTMSUser)){
                            if ($getTMSUser->cmtnd == $cmtnd || strpos($getTMSUser->cmtnd, '00000') !== false) {
                                $newUserId = $user->id;
                                $checkedUsers = [];
                                break;
                            }
                        }
                        else {
                            $checkedUsers[] = $user->username;
                        }
                    }
                    //nếu iduserfix khác 0 -> cập nhật
                    if ($newUserId > 0) {
                        $userGet = MdlUser::where('id', $newUserId)->first();
                        //cập nhật thông tin user
                        $userGet->redirect_type = 'lms';
                        $userGet->firstname = $firstname;
                        $userGet->lastname = $lastname;
                        $userGet->email = $email;
                        $userGet->save();

                        //cập nhật thông tin chi tiết user
                        $userGetTms = TmsUserDetail::where('user_id', $newUserId)->first();
                        $userGetTms->cmtnd = $cmtnd;
                        $userGetTms->fullname = $fullname;
                        $userGetTms->email = $email;
                        $userGetTms->phone = $phone;
                        $userGetTms->address = $address;
                        $userGetTms->sex = $sex ? $sex : 1;
                        $userGetTms->confirm = $confirm ? $confirm : 0;
                        $userGetTms->user_id = $newUserId;
                        $userGetTms->dob = $timestamp;
                        $userGetTms->working_status = $working_status;
                        $userGetTms->start_time = $timestamp_start;
                        $userGetTms->save();

                        devcpt_log_system('user', '/system/user/edit/' . $newUserId, 'update', 'Import Update User: ' . $username);
                        $userOuput['username'] = $username;
                        $userOuput['code'] = $newUserId;
                        $userOuput['cmtnd'] = $cmtnd;
                        $userOuput['message'] = 'Cập nhật thành công';
                        $userOuput['type'] = 'update';
                        $checkedUsers = [];
                    }
                    //Nếu trùng username thì sẽ tìm ra số cuối để tăng
                    if (count($checkedUsers) > 0) {
                        $number = 0;
                        $max = 0;
                        //vòng lặp qua các username trùng
                        foreach ($checkedUsers as $checkedUser) {
                            $thisUserNumber = substr($checkedUser, strlen($username), strlen($checkedUser));
                            if (is_numeric($thisUserNumber) && $thisUserNumber > $max)
                                $max = $thisUserNumber;
                        }
                        //tạo mới
                        $usernameNew = $username . ($max + 1);

                        //thêm mới user
                        $newUserId = $this->createUserOrg(
                            $usernameNew,
                            $firstname,
                            $lastname,
                            $email,
                            $role_name,
                            $confirm,
                            $cmtnd,
                            $fullname,
                            $phone,
                            $code,
                            $address,
                            $sex,
                            $timestamp, $timestamp_start, $working_status
                        );
                        $userOuput['message'] = 'Thêm mới thành công.';
                        $userOuput['type'] = 'add';
                    }
                }
                else {
//                    thêm mới user
                    $newUserId = $this->createUserOrg(
                        $usernameNew,
                        $firstname,
                        $lastname,
                        $email,
                        $role_name,
                        $confirm,
                        $cmtnd,
                        $fullname,
                        $phone,
                        $code,
                        $address,
                        $sex,
                        $timestamp, $timestamp_start, $working_status
                    );
                    $userOuput['message'] = 'Thêm mới thành công.';
                    $userOuput['type'] = 'add';
                }
                if ($newUserId) {
                    //add user vao khung nang luc chung chi trong he thong (day la khung nang luc bat buoc)
//                    $trainning = new TmsTrainningUser();
//                    $trainning->user_id = $newUserId;
//                    $trainning->trainning_id = 1; //id khung nang luc bat buoc
//                    $trainning->save();

                    $trainning = TmsTrainningUser::firstOrCreate([
                        'user_id' => $newUserId,
                        'trainning_id' => 1 //id khung nang luc bat buoc
                    ]);

                    sleep(0.01);

                    $category = TmsTrainningCategory::select('category_id')->where('trainning_id', $trainning->id)->first();

                    training_enrole($newUserId, $category['category_id']);
                }


                //  \DB::commit();
                $textMsg='';
                //Nếu tồn tại mã quản lý : $managementCode -> kiểm tra nếu k có thì thêm, nếu có thì cập nhật
                if($newUserId > 0 && $managementCode > 0)
                {
                    //kiểm tra mã đơn vị quản lý
                    //kiểm tra trong điểm bán
                    $checkSaleRoomUser = TmsSaleRooms::where('code', '=', $managementCode)->first();
                    if (!empty($checkSaleRoomUser)) {
                        $getSRU = $this->CreateSaleRoomUser($checkSaleRoomUser->id, $newUserId, TmsSaleRoomUser::POS);
                        if ($getSRU['code'] == 0)
                            $textMsg = ' Thông tin điểm bán bị lỗi hoặc không đúng.';
                        else
                            $textMsg = ' Thông tin điểm bán: '.$getSRU['message'];
                    }
                    else {
                        $checkBranchUser = TmsBranch::where('code', '=', $managementCode)->first();
                        if (!empty($checkBranchUser)) {
                            $getSRU = $this->CreateSaleRoomUser($checkBranchUser->id, $newUserId, TmsSaleRoomUser::AGENTS);
                            if ($getSRU['code'] == 0)
                                $textMsg = ' Thông tin đại lý bị lỗi hoặc không đúng.';
                            else
                                $textMsg = ' Thông tin đại lý: '.$getSRU['message'];
                        }
                        else {
                            $createBranchUnknow = $this->CreateBranch('unknowbranch', 'unknowbranch', null, 12298, null);
                            $createSaleRoomUnknow = $this->CreateSaleRoom('unknowsaleroom', 'unknowsaleroom', $createBranchUnknow['code'], null, null);
                            $createSaleRoomUser = $this->CreateSaleRoomUser($createSaleRoomUnknow['code'], $newUserId,TmsSaleRoomUser::POS);
                            $textMsg = ' Nhân viên thuộc điểm bán unknowsaleroom.';
                        }
                    }
                }
                $userOuput['message'] .= $textMsg;
                $userOuput['code'] = $newUserId;
                $userOuput['cmtnd'] = $cmtnd;
                $userOuput['status'] = 'success';
                array_push($importOutput['userOuput'], $userOuput);
            }
            else{
                $userOuput['cmtnd'] = $cmtnd;
            }
        }
        catch (\Exception $e) {
            // \DB::rollBack();
            $userOuput['username'] = $username;
            $userOuput['status'] = 'error';
            $checkTest = 0;
            for ($i = 0; $i < strlen($username); $i++) {
                if (ord($username[$i]) > 127) {
                    $checkTest = 1;
                    break;
                }
            }
            if ($checkTest != 0)
                $userOuput['message'] = 'Lỗi format tên';
            else
                $userOuput['message'] = $e->getMessage();

            $userOuput['code'] = 0;
            $userOuput['cmtnd'] = $cmtnd;
            $userOuput['type'] = 'error';
        }
        array_push($importOutput['userOuput'], $userOuput);
        return $importOutput['userOuput'];
    }

    public function CreateBranchSaleRoom($branch, $saleroom_id)
    {
        try {
//            \DB::beginTransaction();
            $branchRoom = new TmsBranchSaleRoom;
            $branchRoom->branch_id = $branch;
            $branchRoom->sale_room_id = $saleroom_id;
            $branchRoom->save();
//            \DB::commit();
            return 1;
        } catch (\Exception $e) {
//            \DB::rollBack();
            return 0;
        }
    }

    public function CreateSaleRoom($name, $code, $branch, $user_id, $address)
    {
        try {
            if(!empty($name)){
                $check_name = DB::table('tms_sale_rooms as tsr')
                    ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                    ->where('tsr.name', '=', $name)
                    ->where('tbsr.branch_id', '=', $branch)
                    ->count();
                if ($check_name > 0) {
                    $sale_room = DB::table('tms_sale_rooms as tsr')
                        ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                        ->where('tsr.name', '=', $name)
                        ->where('tbsr.branch_id', '=', $branch)
                        ->first();
                    $this->UpdateSaleRoom($sale_room->id, $user_id, $name, $code, $address, $branch);
                    return [
                        'code' => $sale_room->id,
                        'type' => 'update',
                        'message' => 'Cập nhật thành công.'
                    ];
                }
            }

            $check_saleroom = TmsSaleRooms::where('code', $code)->count();

            if ($check_saleroom > 0) {
                $sale_room = TmsSaleRooms::where('code', $code)->first();
                $this->UpdateSaleRoom($sale_room->id, $user_id, $name, $code, $address, $branch);
                return [
                    'code' => $sale_room->id,
                    'type' => 'update',
                    'message' => 'Cập nhật thành công.'
                ];
            }
//            \DB::beginTransaction();
            $tmsSaleRoom = new TmsSaleRooms;
            $tmsSaleRoom->name = $name;
            $tmsSaleRoom->address = $address;
            $tmsSaleRoom->user_id = $user_id;
            $tmsSaleRoom->code = $code;
            $tmsSaleRoom->save();

            if ($branch != 0) {
                $branchRoom = new TmsBranchSaleRoom;
                $branchRoom->branch_id = $branch;
                $branchRoom->sale_room_id = $tmsSaleRoom->id;
                $branchRoom->save();
            }

            if ($user_id && $user_id != 0 && !has_user_market($user_id)) {
                add_managepos_for_user($user_id);
            }

            devcpt_log_system('organize', '/system/organize/saleroom/edit/' . $tmsSaleRoom->id, 'create', 'Thêm mới Điểm bán: ' . $name);
//            \DB::commit();
            return [
                'code' => $tmsSaleRoom->id,
                'type' => 'add',
                'message' => 'Thêm điểm bán thành công'
            ];
        } catch (\Exception $e) {
//            \DB::rollBack();
            return [
                'code' => 0,
                'type' => "error",
                'message' => 'Lỗi dữ liệu, kiểm tra lại'
            ];
        }
    }

    public function UpdateSaleRoom($saleroom_id, $user_id, $name, $code, $address, $branch_id)
    {
        try {
//           \DB::beginTransaction();
            $saleRoom = TmsSaleRooms::findOrFail($saleroom_id);
            if ($user_id && $user_id != 0 && $saleRoom['user_id'] != $user_id && !has_user_market($user_id)) {
                $role = Role::where('name', Role::MANAGE_POS)->first();
                ModelHasRole::where([
                    'role_id' => $role['id'],
                    'model_id' => $saleRoom['user_id']
                ])->delete();
                add_managepos_for_user($user_id);
            }

            $saleRoom->name = $name;
            $saleRoom->code = $code;
            $saleRoom->user_id = $user_id;
            $saleRoom->address = $address;
            $saleRoom->save();
//           devcpt_log_system('organize', '/system/organize/saleroom/edit/' . $saleroom_id, 'edit', 'Sửa Điểm bán: ' . $name);
//           \DB::commit();
        } catch (\exception $e) {
        }
    }

    public function CreateSaleRoomUser($managementId, $user_id, $type)
    {
        try {
            $saleRoomUser = TmsSaleRoomUser::firstOrCreate([
                'sale_room_id' => $managementId,
                'user_id' => $user_id,
                'type' => $type
            ]);

            return [
                'code' => $saleRoomUser->id,
                'message' => 'Thêm thành công'
            ];
        } catch (\Exception $e) {
//            \DB::rollBack();
            return [
                'code' => 0,
                'message' => 'Lỗi dữ liệu, kiểm tra lại'
            ];
        }
    }

    public function randomNumber($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    public function createUserOrg($usernameNew, $firstname, $lastname, $email, $role_name, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $timestamp_start, $working_status)
    {
        $role = Role::select('id', 'name', 'mdl_role_id')->where('name', $role_name)->first();
        $mdlUser = new MdlUser;
        $mdlUser->username = $usernameNew;
        $mdlUser->password = bcrypt('123456789');
        $mdlUser->redirect_type = 'lms';
        $mdlUser->firstname = $firstname;
        $mdlUser->lastname = $lastname;
        $mdlUser->email = $email;
        $mdlUser->save();

        if ($role) {
            add_user_by_role($mdlUser->id, $role['id']);
            enrole_lms($mdlUser->id, $role['mdl_role_id'], $confirm ? $confirm : 0);
        }

        $tmsUser = new TmsUserDetail;
        $tmsUser->cmtnd = $cmtnd;
        $tmsUser->fullname = $fullname;
        $tmsUser->email = $email;
        $tmsUser->phone = $phone;
        $tmsUser->code = $code;
        $tmsUser->address = $address;
        $tmsUser->sex = $sex ? $sex : 1;
        $tmsUser->confirm = $confirm ? $confirm : 0;
        $tmsUser->user_id = $mdlUser->id;
        $tmsUser->dob = $timestamp;
        $tmsUser->working_status = $working_status;
        $tmsUser->start_time = $timestamp_start;
        $tmsUser->save();

        return $mdlUser->id;
    }

    public function downloadExportReport()
    {
        $filename = "error.xlsx";
        return Storage::download($filename);
    }

    public function RandomCMTND(){
        $checkUserCMTND = TmsUserDetail::where('cmtnd', 'like', "0000%")->get()->toArray();
        $number = 0;
        $max = 0;
        if(count($checkUserCMTND) > 0){
            //vòng lặp qua các username trùng
            foreach ($checkUserCMTND as $checkedUser) {
                if (is_numeric($checkedUser['cmtnd']) && $checkedUser['cmtnd'] > $max)
                    $max = $checkedUser['cmtnd'];
            }
        }
        else{
            $max = 1;
        }
        $max += 1;
        $result = '';
        for ($i = 0; $i < (12 - strlen($max)); $i++) {
            $result .= 0;
        }
        return $result . $max;
    }
}
