<?php


namespace App\Repositories;


use App\Repositories\IBussinessInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\MdlLogstoreStandardLog;
use App\MdlCapabilities;
use App\MdlRoleCapabilities;
use App\MdlUser;
use App\TmsDevice;
use App\PermissionSlugRole;
use App\StudentCertificate;
use App\TmsLog;
use App\TmsNotification;
use App\Role;
use App\MdlQuizAttempts;
use App\MdlQuiz;
use App\TmsBranchMaster;
use App\ModelHasRole;
use App\TmsBranch;
use App\TmsBranchSaleRoom;
use App\TmsUserDetail;
use App\TmsSaleRoomUser;
use App\MdlContext;
use App\MdlCourse;
use App\MdlCourseCategory;
use App\MdlCourseCompletionCriteria;
use App\MdlEnrol;
use App\TmsCity;
use App\TmsCityBranch;
use App\TmsDepartmentCity;
use App\TmsDepartments;
use App\TmsRoleOrganize;
use App\TmsSaleRooms;
use App\MdlGradeCategory;
use App\MdlGradeItem;
use App\TmsTrainningCategory;
use App\TmsTrainningCourse;
use App\TmsTrainningProgram;
use App\TmsTrainningUser;
use App\ViewModel\CheckRoleModel;
use App\TmsQuestion;
use App\TmsQuestionAnswer;
use App\TmsQuestionData;
use App\TmsSurvey;
use App\TmsSurveyUser;
use App\ImageCertificate;
use App\TmsSurveyUserView;
use App\ViewModel\AnswerModel;
use App\ViewModel\DataModel;
use App\ViewModel\QuestionChildModel;
use App\ViewModel\QuestionModel;
use Horde\Socket\Client\Exception;
use App\Imports\QuestionImport;
use App\Imports\UsersImport;
use App\MdlQuestion;
use App\MdlQuestionAnswer;
use App\MdlRole;
use App\ViewModel\ImportModel;
use App\ViewModel\ResponseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\MdlRoleAssignments;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Exports\SurveyExportView;
use Spatie\Permission\Models\Permission;


class BussinessRepository implements IBussinessInterface
{

    public function reportbranch()
    {
        // TODO: Implement reportbranch() method.
    }

    // BackendController
    public function apiActivityLog(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $type = $request->input('type');
        $action = $request->input('action');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'type' => 'text',
            'action' => 'text',
        ];

        $validator = validate_fails($request, $param);

        if (!empty($validator)) {
            return response()->json([]);
        }

        if ($type == 'education') {
            $data = MdlLogstoreStandardLog::with('user')
                ->where('mdl_logstore_standard_log.target', 'course')
                ->select(
                    DB::raw('"education" as type'),
                    'mdl_logstore_standard_log.action',
                    DB::raw('FROM_UNIXTIME(mdl_logstore_standard_log.timecreated) as created_at'),
                    'mdl_course.shortname as course_name',
                    'mdl_logstore_standard_log.contextinstanceid as course_id',
                    'mdl_logstore_standard_log.userid',
                    'mdl_logstore_standard_log.target'
                )
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_logstore_standard_log.contextinstanceid')
                ->where('mdl_logstore_standard_log.contextlevel', 50);

            if ($keyword && strlen($keyword) != 0) {
                $data = $data->where('mdl_logstore_standard_log.other', 'like', "%{$keyword}%");
            }
            if (strlen($action) != 0) {
                $data = $data->where('mdl_logstore_standard_log.action', $action . "d");
            }
            $data = $data->orderBy('mdl_logstore_standard_log.timecreated', 'desc');
        } else {
            $data = TmsLog::with('user');
            if ($keyword) {
                $data = $data->orWhere('url', 'like', "%{$keyword}%");
                $data = $data->orWhere('info', 'like', "%{$keyword}%");
            }
            if ($type != '') {
                $data = $data->where('type', $type);
            }
            if (strlen($action) != 0) {
                $data = $data->where('action', $action);
            }
            $data = $data->orderBy('created_at', 'desc');
        }

        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function chartData(Request $request)
    {
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $validates = validate_fails($request, [
            'startdate' => 'date',
            'enddate' => 'date',
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            if (strlen($startdate) != 0 && strlen($enddate) != 0) {
                $full_start_date = $startdate . " 00:00:00";
                $full_end_date = $enddate . " 23:59:59";
                $start_time = strtotime($full_start_date);
                $end_time = strtotime($full_end_date);
            } else {
                $now = time();
                $start_time = strtotime(date("Y-m-01 H:i:s", strtotime("-4 months"))); //first day of month / max = 11 month
                $end_time = $now;
            }

            $response = array();
            //        chart 1 data
            //        So luong hoan thanh khoa hoc / thang
            //        So luong enrol khoa hoc / thang
            //        $completed = MdlCourseCompletions::where("timecompleted", ">=", $start_time)
            //            ->where("timecompleted", "<=", $end_time)
            //            ->select(
            //                \DB::raw('MONTH(FROM_UNIXTIME(timecompleted)) as mth'),
            //                \DB::raw('count(id) as total')
            //            )
            //            ->groupBy(\DB::raw('MONTH(FROM_UNIXTIME(timecompleted))'))
            //            ->get()->toArray();
            //
            //        $completed = fillMissingMonthChartData($completed, $start_time, $end_time);
            //        $response['completed'] = $completed;

            //        $enrolled = MdlCourseCompletions::where("timeenrolled", ">=", $start_time)
            //            ->where("timeenrolled", "<=", $end_time)
            //            ->select(
            //                \DB::raw('MONTH(FROM_UNIXTIME(timeenrolled)) as mth'),
            //                \DB::raw('count(id) as total')
            //            )
            //            ->groupBy(\DB::raw('MONTH(FROM_UNIXTIME(timeenrolled))'))
            //            ->get()->toArray();
            //
            //        $enrolled = fillMissingMonthChartData($enrolled, $start_time, $end_time);
            //        $response['enrolled'] = $enrolled;


            //chart 1,2 data
            //Hoc viên enrol khóa offline/online trong tháng

            $role_id = 5;
            //        $enrolled_all = MdlUserEnrolments::where('mdl_enrol.enrol', 'manual')
            //            ->where('roles.id', '=', $role_id)
            //            ->where(function ($q) use ($start_time, $end_time) {
            //                $q
            //                    ->where(function ($q1) use ($start_time, $end_time) {
            //                    $q1->where("mdl_user_enrolments.timecreated", ">=", $start_time)->where("mdl_user_enrolments.timecreated", "<=", $now);
            //                    })
            //                    ->orWhere(function ($q2) use ($start_time, $end_time) {
            //                        $q2->where("mdl_user_enrolments.timestart", ">=", $start_time)->where("mdl_user_enrolments.timestart", "<=", $now);
            //                    });
            //            })
            //            ->select(
            //                'mdl_course.id',
            //                'mdl_user_enrolments.userid',
            //                'mdl_course.category',
            //                \DB::raw('MONTH(FROM_UNIXTIME(mdl_user_enrolments.timestart)) as mth'),
            //                \DB::raw('MONTH(FROM_UNIXTIME(mdl_user_enrolments.timestart)) as mth2')
            //            )
            //            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
            //            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
            //            ->join('model_has_roles', 'mdl_user_enrolments.userid', '=', 'model_has_roles.model_id')
            //            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            //            ->get()->toArray();

            //        $enrolled = array();

            //        $this_month_offline = 0;
            //        $this_month_online = 0;
            //
            //        foreach ($enrolled_all as $enrol) {
            //            $mth = $enrol['mth'];
            //            if (strlen($mth) == 0) {
            //                $mth = $enrol['mth2'];
            //            }
            //
            //            if ($mth == date('n', $now)) {
            //                if ($enrol['category'] == 5) { //offline
            //                    $this_month_offline += 1;
            //                } elseif ($enrol['category'] != 5 && $enrol['category'] != 2) { //online
            //                    $this_month_online += 1;
            //                }
            //            }
            //
            //            if (array_key_exists($mth, $enrolled)) {
            //                $enrolled[$mth] += 1;
            //            } else {
            //                $enrolled[$mth] = 1;
            //            }
            //        }

            //        $enrolled_source = array();
            //        foreach ($enrolled as $key => $val) {
            //            $enrolled_source[] = array(
            //              'mth' => $key,
            //              'total' => $val
            //            );
            //        }

            //chart 3 data
            //hoc vien dang ki moi
            $students = MdlUser::where('roles.id', '=', $role_id)//Role hoc vien
            ->where("model_has_roles.created_at", ">=", date("Y-m-d H:i:s", $start_time))
                ->where("model_has_roles.created_at", "<=", date("Y-m-d H:i:s", $end_time))
                ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
                ->select(
                //                error strict mode laravel https://stackoverflow.com/a/44984930/3387087
                //                DB::raw("DATE_FORMAT(model_has_roles.created_at, '%m') mth"),
                //                DB::raw("DATE_FORMAT(model_has_roles.created_at, '%Y') yr"),
                    DB::raw('CONCAT(MONTH(model_has_roles.created_at), "/", DATE_FORMAT(model_has_roles.created_at, "%y")) mthyr'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('mthyr')
                ->get()
                ->toArray();

            $registered = array();
            $stack_registered = array();

            foreach ($students as $student) {
                $mthyr = $student['mthyr'];
                $registered[$mthyr] = $student['total'];
            }

            $registered_source = array();
            foreach ($registered as $key => $val) {
                $registered_source[] = array(
                    'mthyr' => $key,
                    'total' => $val
                );
            }

            //        $confirm_students = TmsUserDetail::where("confirm_time", ">=", $start_time)
            //            ->where("confirm_time", "<=", $end_time)
            //            ->select(
            //                \DB::raw('MONTH(FROM_UNIXTIME(confirm_time)) as mth')
            //            )
            //            ->get()->toArray();
            //
            //        $confirmed = array();
            //        foreach ($confirm_students as $confirm) {
            //            $mth = $confirm['mth'];
            //            if (array_key_exists($mth, $confirmed)) {
            //                $confirmed[$mth] += 1;
            //            } else {
            //                $confirmed[$mth] = 1;
            //            }
            //        }
            //        $confirm_source = array();
            //        foreach ($confirmed as $key => $val) {
            //            $confirm_source[] = array(
            //                'mth' => $key,
            //                'total' => $val
            //            );
            //        }

            $confirm_students = StudentCertificate::where("student_certificate.created_at", ">=", date("Y-m-d H:i:s", $start_time))
                ->join('course_final as cf', 'cf.userid', '=', 'student_certificate.userid')
                ->where("student_certificate.created_at", "<=", date("Y-m-d H:i:s", $end_time))
                ->select(
                    DB::raw('CONCAT(MONTH(student_certificate.created_at), "/", DATE_FORMAT(student_certificate.created_at, "%y")) mthyr'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('mthyr')
                ->get()->toArray();

            $confirmed = array();
            foreach ($confirm_students as $confirm) {
                $mthyr = $confirm['mthyr'];
                $confirmed[$mthyr] = $confirm['total'];
            }
            $confirm_source = array();
            foreach ($confirmed as $key => $val) {
                $confirm_source[] = array(
                    'mthyr' => $key,
                    'total' => $val
                );
            }

            $quit_students = MdlUser::where('roles.id', '=', $role_id)//Role hoc vien
            ->where("quit_time", ">=", $start_time)
                ->where("quit_time", "<=", $end_time)
                ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->join('tms_user_detail', 'mdl_user.id', '=', 'tms_user_detail.user_id')
                ->select(
                    DB::raw('CONCAT(MONTH(FROM_UNIXTIME(quit_time)), "/", DATE_FORMAT(FROM_UNIXTIME(quit_time), "%y")) mthyr'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('mthyr')
                ->get()->toArray();

            $quit = array();

            foreach ($quit_students as $student) {
                $mthyr = $student['mthyr'];
                $confirmed[$mthyr] = $student['total'];
            }


            $quit_source = array();
            foreach ($quit as $key => $val) {
                $quit_source[] = array(
                    'mthyr' => $key,
                    'total' => $val
                );
            }

            //chart 2(count course only), 4 data
            $courses = MdlCourse::where('mdl_course.visible', 1)->where('mdl_course.category', "<>", 2)
                ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                ->select('mdl_course.id', 'mdl_course.category', 'mdl_course.visible')
                ->get();

            $online = 0;
            $offline = 0;
            foreach ($courses as $course) {
                if ($course->category == 5) { //offline
                    $offline += 1;
                } else { //online
                    $online += 1;
                }
            }
            //chart1 bonus
            //        $enrolled = fillMissingMonthChartData($enrolled_source, $start_time, $end_time);
            //        $response['enrolled'] = $enrolled;

            //chart2
            //        $response['online'] = $this_month_online;
            //        $response['offline'] = $this_month_offline;
            $response['online'] = $online;
            $response['offline'] = $offline;

            //chart 3
            $registered = fillMissingMonthChartData($registered_source, $start_time, $end_time);
            $response['registered'] = $registered;
            $stack_total = 0;
            foreach ($registered as $key => $val) {
                $stack_total += $val['total'];
                $stack_registered[] = array(
                    'mth' => $key,
                    'total' => $stack_total
                );
            }
            $response['stack_registered'] = $stack_registered;

            $confirmed = fillMissingMonthChartData($confirm_source, $start_time, $end_time);
            $response['confirmed'] = $confirmed;

            $quit = fillMissingMonthChartData($quit_source, $start_time, $end_time);
            $response['quit'] = $quit;

            return response()->json($response);
        }
    }

    public function tableData(Request $request)
    {
        $now = time();
        $row = $request->input('row');

        $validates = validate_fails($request, [
            'row' => 'number',
        ]);
        if (!empty($validates)) {
            return response()->json([]);
        } else {
            $data = MdlCourse::where('category', '<>', 2)
                ->where("enddate", ">=", $now)
                ->where("startdate", "<=", $now)
                ->select(
                    'id',
                    'shortname',
                    'fullname',
                    \DB::raw('FROM_UNIXTIME(startdate) as start'),
                    \DB::raw('FROM_UNIXTIME(enddate) as end'),
                    'course_place',
                    'category'
                );

            $data = $data->paginate($row);
            $total = ceil($data->total() / $row);

            $response = [
                'pagination' => [
                    'total' => $total,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
            ];
        }
        return response()->json($response);
    }

    #region check for sidebar

    public function checkRoleSidebar()
    {
        $checkRole = new CheckRoleModel();
        try {

            $user_id = Auth::id();

            $sru = DB::table('model_has_roles as mhr')
                ->join('roles', 'roles.id', '=', 'mhr.role_id')
                ->where('roles.name', '=', Role::MANAGE_MARKET)
                ->where('mhr.model_id', '=', $user_id)
                ->count();
            if ($sru > 0)
                $checkRole->has_user_market = true;

            $my_branches = TmsBranchMaster::where('master_id', $user_id)->count();
            if ($my_branches > 0)
                $checkRole->has_master_agency = true;

            $count = DB::table('model_has_roles as mhr')
                ->select('mhr.id')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->where('mhr.model_id', '=', $user_id)
                ->where('r.name', '=', Role::MANAGE_AGENTS)
                ->count();
            if ($count > 0)
                $checkRole->has_role_agency = true;

            $count_role = DB::table('model_has_roles as mhr')
                ->select('mhr.id')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->where('mhr.model_id', '=', $user_id)
                ->where('r.name', '=', Role::MANAGE_POS)
                ->count();
            if ($count_role > 0)
                $checkRole->has_role_pos = true;

            $roleName = ['Root', 'root'];

            $roleCheck = DB::table('model_has_roles as mhr')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->where('mhr.model_id', '=', $user_id)->whereIn('r.name', $roleName)->first();

            if ($roleCheck)
                $checkRole->root_user = true;
        } catch (Exception $e) {
            $checkRole->has_user_market = false;
            $checkRole->has_master_agency = false;
            $checkRole->has_role_agency = false;
            $checkRole->has_role_pos = false;
            $checkRole->root_user = false;
        }
        return response()->json($checkRole);
    }
    #endregion

    // End BackendController

    // BranchController

    public function apiListUserByBranch(Request $request)
    {
        $branch_id = $request->input('id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $saleroom = $request->input('saleroom');
        $working_status = $request->input('working_status');

        $validates = validate_fails($request, [
            'id' => 'number',
            'keyword' => 'text',
            'row' => 'number',
            'saleroom' => 'number',
            'working_status' => 'number'
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            $type = "all";
            $saleroom_ids = [];

            if (strlen($saleroom) != 0) { //filter selected
                if ($saleroom != 0) {
                    $saleroom_ids = [$saleroom];
                    $type = TmsSaleRoomUser::POS;
                } else {
                    $type = TmsSaleRoomUser::AGENTS;
                }
            } else { //all
                $saleroom_ids = TmsBranchSaleRoom::where('branch_id', $branch_id)->pluck('sale_room_id');
            }
            $data = TmsSaleRoomUser::where('tms_user_detail.deleted', 0)
                ->select(
                    'tms_user_detail.user_id',
                    'tms_user_detail.fullname',
                    'tms_user_detail.cmtnd',
                    'tms_user_detail.phone',
                    'tms_user_detail.email',
                    'tms_sale_rooms.name as saleroom_name',
                    'tms_branch.name as branch_name',
                    'tms_sale_room_user.type'
                )
                ->join('tms_user_detail', 'tms_sale_room_user.user_id', '=', 'tms_user_detail.user_id')
                ->leftJoin('tms_sale_rooms', function ($join) {
                    $join->on('tms_sale_room_user.sale_room_id', '=', 'tms_sale_rooms.id');
                    //$join->where('tms_sale_room_user.type', TmsSaleRoomUser::POS);
                })
                ->leftJoin('tms_branch', function ($join) {
                    $join->on('tms_sale_room_user.sale_room_id', '=', 'tms_branch.id');
                    //$join->where('tms_sale_room_user.type', TmsSaleRoomUser::AGENTS);
                });
            //->join('tms_sale_rooms', 'tms_sale_room_user.sale_room_id', '=', 'tms_sale_rooms.id');

            if ($type == "all") {
                $data->where(function ($q) use ($saleroom_ids, $branch_id) {
                    $q->orWhere(function ($q2) use ($saleroom_ids) {
                        $q2->whereIn('tms_sale_room_user.sale_room_id', $saleroom_ids)->where('tms_sale_room_user.type', TmsSaleRoomUser::POS);
                    });
                    $q->orWhere(function ($q3) use ($branch_id) {
                        $q3->where('tms_sale_room_user.sale_room_id', $branch_id)->where('tms_sale_room_user.type', TmsSaleRoomUser::AGENTS);
                    });
                });
            } elseif ($type == TmsSaleRoomUser::AGENTS) {
                $data->where('tms_sale_room_user.sale_room_id', $branch_id)->where('tms_sale_room_user.type', TmsSaleRoomUser::AGENTS);
            } elseif ($type == TmsSaleRoomUser::POS) {
                $data->whereIn('tms_sale_room_user.sale_room_id', $saleroom_ids)->where('tms_sale_room_user.type', TmsSaleRoomUser::POS);
            }

            if (strlen($working_status) != 0) {
                $data->where('tms_user_detail.working_status', $working_status);
            }

            if (strlen($keyword) != 0) {
                $data = $data->where(function ($q) use ($keyword) {
                    $q->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%")
                        ->orWhere('tms_user_detail.cmtnd', 'like', "%{$keyword}%")
                        ->orWhere('tms_user_detail.email', 'like', "%{$keyword}%")
                        ->orWhere('tms_user_detail.phone', 'like', "%{$keyword}%");
                });
            }
            $data = $data->paginate($row);
            $total = ceil($data->total() / $row);
            $response = [
                'pagination' => [
                    'total' => $total,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
                'keyword' => $keyword
            ];
            return response()->json($response);
        }
    }


    public function detailBranchUser(Request $request, $branch_id, $user_id)
    {
        $branch = TmsBranch::find($branch_id);
        $type = $request->input('type') ? $request->input('type') : 'owner';
        $role = ModelHasRole::with('role')->where('model_id', $user_id)->first();
        return view('system.branch.user_view', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name'],
            'branch_id' => $branch_id,
            'branch_name' => $branch->name,
            'type' => $type
        ]);
    }

    public function editBranchUser(Request $request, $branch_id, $user_id)
    {

        $branch = TmsBranch::find($branch_id);
        $role = ModelHasRole::with('role')->where('model_id', $user_id)->first();
        $type = $request->input('type') ? $request->input('type') : 'owner';

        return view('system.branch.user_edit', [
            'user_id' => $user_id,
            'role_name' => $role['role']['name'],
            'branch_id' => $branch_id,
            'branch_name' => $branch->name,
            'type' => $type
        ]);
    }

    //Api lấy danh sách Điểm bán theo Đại lý
    public function apiGetSaleRoomByBranch(Request $request)
    {
        $branch = $request->input('branch');
        $salerooms = TmsBranchSaleRoom::with('sale_room_name')
            ->where('branch_id', $branch);
        $salerooms = $salerooms->get()->toArray();
        return response()->json($salerooms);
    }

    // End BranchController

    //api lấy danh sách giáo viên
    //hiển hị dưới view create và edit course
    //ThoLD (21/08/2019)
    public function apiGetListTeacher()
    {
        $listUsers = DB::table('mdl_user')
            ->join('tms_user_detail', 'tms_user_detail.user_id', '=', 'mdl_user.id')
            ->select('mdl_user.id as id', 'tms_user_detail.fullname as fullname')
            ->where('mdl_user.deleted', '=', 0)->get();
        return response()->json($listUsers);
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    //ThoLD (24/08/2019)
    public function apiGetListCategory()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->where('mdl_course_categories.id', '!=', 2)
            ->where('mdl_course_categories.id', '!=', 3)
            ->where('mdl_course_categories.id', '!=', 5)
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    //ThoLD (24/08/2019)
    public function apiGetListCategoryForEdit()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->where('mdl_course_categories.id', '!=', 2)
            ->where('mdl_course_categories.id', '!=', 5)
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }

    //api lấy danh sách danh mục khóa học
    //hiển hị dưới view create và edit course
    //ThoLD (24/08/2019)
    public function apiGetListCategoryForClone()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->where('mdl_course_categories.id', '!=', 2)
            ->where('mdl_course_categories.id', '!=', 3)
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }

    //api lấy danh sách danh mục khóa học cho chức năng restore
    //hiển hị dưới view create và edit course
    //ThoLD (10/09/2019)
    public function apiGetListCategoryRestore()
    {
        $listCategories = DB::table('mdl_course_categories')
            ->select('mdl_course_categories.id as id', 'mdl_course_categories.name as category_name')
            ->where('mdl_course_categories.visible', '=', 1)->get();
        return response()->json($listCategories);
    }

    public function apiGetCourseDetail($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $course_info = DB::table('mdl_course')
            ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
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
                'mdl_course.course_budget'
            )
            ->where('mdl_course.id', '=', $id)->first();

        return response()->json($course_info);
    }

    //api xóa khóa học
    //ThoLD (22/08/2019)
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
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }


            $course = MdlCourse::where('id', '=', $id)
                ->where('startdate', '<=', time())
                ->where('enddate', '>=', time())->first();

            //nếu tồn tại khóa học trong thời gian học và không phải khóa học mẫu
            if ($course && $course->category != 2) {
                //Đang trong thời gian học, không xóa được
                $result = 10;
            } else {

                $app_name = Config::get('constants.domain.APP_NAME');

                $key_app = encrypt_key($app_name);

                $data = array(
                    'courseid' => $id,
                    'app_key' => $key_app
                );

                $data = createJWT($data, 'data');

                $data_del = array(
                    'data' => $data
                );

                $url = Config::get('constants.domain.LMS') . '/course/delete_course.php';
                //call api write log
                $result = callAPI('POST', $url, $data_del, false, '');
            }

            if ($result == 1) {
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
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }


    //api lấy danh sách khóa học mẫu
    //ThoLD (05/09/2019)
    public function apiGetListCourseSample()
    {
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

        $listCourses = $listCourses->where('mdl_course.category', '=', 2); //2 là khóa học mẫu

        $listCourses = $listCourses->orderBy('id', 'desc')->get();

        return response()->json($listCourses);
    }

    //api clone khóa học
    //ThoLD (21/08/2019)
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
                'course_budget' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }


            //check course info exist
            $courseInfo = MdlCourse::select('id')->where('shortname', $shortname)->first();

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
            $course->allow_register = $allow_register;
            $course->visible = 0;

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
            $response->message = __('nhan_ban_khoa_hoc');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //api lấy danh sách khóa học tập trung
    //ThoLD (09/09/2019)
    public function apiGetListCourseConcen(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $category_id = $request->input('category_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $status_course = $request->input('status_course');
        //        $sample = $request->input('sample'); //field xác định giá trị là khóa học mẫu hay không


        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'category_id' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $checkRole = tvHasRole(\Auth::user()->id, "teacher");
        if ($checkRole === TRUE) {
            $listCourses = DB::table('mdl_user_enrolments')
                ->where('mdl_user_enrolments.userid', '=', \Auth::user()->id)
                ->join('mdl_enrol', 'mdl_user_enrolments.enrolid', '=', 'mdl_enrol.id')
                ->join('mdl_course', 'mdl_enrol.courseid', '=', 'mdl_course.id')
                ->where('mdl_course.category', '=', $category_id)
                ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
                ->select(
                    'mdl_course.id',
                    'mdl_course.fullname',
                    'mdl_course.shortname',
                    'mdl_course.startdate',
                    'mdl_course.enddate',
                    'mdl_course.visible',
                    'mdl_course_completion_criteria.gradepass as pass_score'
                );
        } else {

            $listCourses = DB::table('mdl_course')
                ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
                ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                ->where('mdl_course.category', '=', $category_id)
                ->select(
                    'mdl_course.id',
                    'mdl_course.fullname',
                    'mdl_course.shortname',
                    'mdl_course.startdate',
                    'mdl_course.enddate',
                    'mdl_course.visible',
                    'mdl_course_completion_criteria.gradepass as pass_score'
                );
        }


        //là khóa học mẫu
        //        if ($sample == 1) {
        //            $listCourses = $listCourses->where('mdl_course.category', '=', 2); //2 là khóa học mẫu
        //        } else {
        //            $listCourses = $listCourses->where('mdl_course.category', '!=', 2);
        //        }

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

        if ($status_course) {
            $unix_now = strtotime(Carbon::now());
            if ($status_course == 1) { //các khóa sắp diễn ra
                $listCourses = $listCourses->where('mdl_course.startdate', '>', $unix_now);
            } else if ($status_course == 2) { //các khóa đang diễn ra
                $listCourses = $listCourses->where('mdl_course.startdate', '<=', $unix_now);
                $listCourses = $listCourses->where('mdl_course.enddate', '>=', $unix_now);
            } else if ($status_course == 3) { //các khóa đã diễn ra
                $listCourses = $listCourses->where('mdl_course.enddate', '<', $unix_now);
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


    //api lấy danh sách khóa học cần restore
    //ThoLD (09/09/2019)
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


        $listCourses = DB::table('mdl_tool_recyclebin_category')
            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_tool_recyclebin_category.categoryid')
            ->select(
                'mdl_tool_recyclebin_category.id',
                'mdl_tool_recyclebin_category.fullname',
                'mdl_tool_recyclebin_category.shortname',
                'mdl_tool_recyclebin_category.timecreated',
                'mdl_course_categories.name as category_name',
                'mdl_tool_recyclebin_category.categoryid'
            );

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

            $listCourses = $listCourses->whereRaw('( mdl_tool_recyclebin_category.fullname like "%' . $keyword . '%" OR mdl_tool_recyclebin_category.shortname like "%' . $keyword . '%" )');
        }

        if ($category_id) {
            $listCourses = $listCourses->where('mdl_tool_recyclebin_category.categoryid', '=', $category_id);
        }

        if ($timecreated) {
            $cv_startDate = strtotime($timecreated);
            $listCourses = $listCourses->where('mdl_tool_recyclebin_category.timecreated', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate . " 23:59:59");
            $listCourses = $listCourses->where('mdl_tool_recyclebin_category.timecreated', '<=', $cv_endDate);
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

    //api restore khóa học
    //ThoLD (10/09/2019)
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
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }


            $contextData = MdlContext::query();
            $contextData = $contextData->where('contextlevel', '=', \App\MdlUser::CONTEXT_COURSECAT);
            $contextData = $contextData->where('instanceid', '=', $instance_id);
            $contextData = $contextData->orderBy('id', 'desc')->first();

            if (!$contextData) {
                $response->status = false;
                $response->message = __('khoi_phuc_khoa_hoc');
                return response()->json($response);
            }

            $app_name = Config::get('constants.domain.APP_NAME');

            $key_app = encrypt_key($app_name);

            $data = array(
                'contextid' => $contextData->id,
                'itemid' => $id,
                'action' => $action,
                'app_key' => $key_app
            );

            $data = createJWT($data, 'data');

            $data_res = array(
                'data' => $data
            );

            $url = Config::get('constants.domain.LMS') . '/admin/tool/recyclebin/recyclebin.php';

            //call api write log
            $result = callAPI('POST', $url, $data_res, false, '');

            if ($result == 1) {
                $response->status = true;
                $response->message = __('thao_tac_thanh_cong');
            } else {
                $response->status = false;
                $response->message = __('thao_tac_khong_thanh_cong');
            }
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //api lọc thông tin người dùng đang được ghi danh vào khóa học
    //ThoLD 13/09/2019
    public function apiUserCurrentEnrol(Request $request)
    {

        $course_id = $request->input('course_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $role_id = $request->input('role_id');
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


        //lấy danh sách học viên đang được enrol vào khóa học hiện tại
        $currentUserEnrol = DB::table('mdl_user_enrolments')
            ->join('mdl_user', 'mdl_user.id', '=', 'mdl_user_enrolments.userid')
            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
            ->where('mdl_course.id', '=', $course_id)
            ->select('mdl_user.id', 'mdl_user.username', 'mdl_user.firstname', 'mdl_user.lastname', 'mdl_enrol.id as enrol_id');
        if ($keyword) {
            $currentUserEnrol = $currentUserEnrol->where('mdl_user.username', 'like', '%' . $keyword . '%');
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
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('mdl_user.deleted', '=', 0)
            ->select('mdl_user.id', 'mdl_user.username', 'mdl_user.firstname', 'mdl_user.lastname', 'roles.name as rolename')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('mdl_user_enrolments')
                    ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                    ->where('mdl_course.id', '=', $this->courseCurrent_id)
                    ->whereRaw('mdl_user_enrolments.userid = mdl_user.id');
            });

        if ($keyword) {
            $userNeedEnrol = $userNeedEnrol->where('mdl_user.username', 'like', '%' . $keyword . '%');
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
            $response->message = $e->getMessage();
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
            $response->message = $e->getMessage();
        }
        return json_encode($response);
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
            ->count();

        $lstUserCourse = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('model_has_roles', 'u.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->where('c.id', '=', $course_id)
            ->where('roles.id', 5)//hoc vien only
            ->select(
                'u.id as user_id',
                'u.username',
                'u.firstname',
                'u.lastname',
                DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm inner join
                mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on
                cm.course = cs.course and cm.section = cs.id inner join mdl_course cc on cm.course = cc.id where
                cs.section <> 0 and cmc.completionstate = 1 and cm.course = c.id and cmc.userid = u.id) as user_course_learn'),

                DB::raw('(select `g`.`finalgrade`
  				from mdl_grade_items as gi
				join mdl_grade_grades as g
				on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = u.id ) as finalgrade')
            );
        if ($keyword) {
            $lstUserCourse = $lstUserCourse->where('u.username', 'like', '%' . $keyword . '%');
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
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->leftJoin('mdl_attendance as mat', 'mat.userid', '=', 'mu.userid')
            ->where('c.id', '=', $course_id)
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

    public function importFile()
    {
        return view('survey.test');
    }

    //api import câu hỏi vào ngân hàng câu hỏi
    //ThoLD 05/11/2019
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
                $responseModel->message = 'Định dạng dữ liệu danh mục không hợp lệ';
                return response()->json($responseModel);
            }

            if (empty($this->category_id)) {
                $responseModel->status = false;
                $responseModel->message = 'Bạn chưa chọn danh mục câu hỏi';
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
    // End CourseController

    // LanguageController
    public function getInfoSidebar()
    {
        $permissions = [];
        try {
            $permission_slugs = DB::table('permission_slug_role as psr')
                ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'psr.role_id')
                ->join('mdl_user as mu', 'mu.id', '=', 'mhr.model_id');


            if (tvHasRole(Auth::user()->id, 'Root') || tvHasRole(Auth::user()->id, 'root')) {
                $permission_slugs = $permission_slugs->select('psr.permission_slug')->groupBy('psr.permission_slug')->get();

                foreach ($permission_slugs as $per_slug) {
                    array_push($permissions, $per_slug->permission_slug);
                }
            } else {
                $permission_slugs = $permission_slugs->where('mu.id', '=', Auth::user()->id)
                    ->select('psr.permission_slug')->groupBy('psr.permission_slug')->get();
                foreach ($permission_slugs as $per_slug) {
                    array_push($permissions, $per_slug->permission_slug);
                }
            }
        } catch (\Exception $e) {

        }

        return response()->json($permissions);
    }
    // End LanguageController

    // NotificationController
    public function apiListUserNotification(Request $request)
    {
        $keyword = $request->input('keyword');

        $validates = validate_fails($request, [
            'keyword' => 'text'
        ]);
        $students = [];
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            $students = MdlUser::where('roles.id', '=', 5)//Role hoc vien
            ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('tms_device', 'mdl_user.id', '=', 'tms_device.user_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select(
                    'mdl_user.id',
                    'username',
                    'firstname',
                    'lastname',
                    'email'
                );
            if ($keyword) {
                $students = $students->where('firstname', 'like', "%{$keyword}%");
                $students = $students->orWhere('lastname', 'like', "%{$keyword}%");
            }
            if (!$keyword) {
                $students = $students->skip(0)->take(10);
            }
            $students = $students->get()->toArray();
        }
        return response()->json($students);
    }

    public function apiSend(Request $request)
    {
        $message = $request->all();

        $validates = validate_fails($request, [
            'content' => 'text',
            'user_id' => 'number'
        ]);
        if (!empty($validates)) {
            return 'pushContentWrongFormat';
            //var_dump($validates);
        } else {
            if (strlen($message['content']) != 0) {
                $android_device_tokens = array();
                $ios_device_tokens = array();
                if (strlen($message['user_id']) != 0 && $message['user_id'] != 0) {
                    $devices = TmsDevice::where('user_id', $message['user_id']);
                } else {
                    $devices = TmsDevice::all();
                }
                foreach ($devices as $item) {
                    if (strlen($item->token) != 0) {
                        if ($item->type == TmsDevice::TYPE_ANDROID) {
                            $android_device_tokens[] = $item->token;
                        }
                        if ($item->type == TmsDevice::TYPE_IOS) {
                            $ios_device_tokens[] = $item->token;
                        }
                    }
                }
                $host = request()->getHttpHost();

                $params = [
                    'title' => 'Vietlott Elearning',
                    'link' => $host
                ];
                if (!empty($android_device_tokens)) {
                    sendPushNotification($message['content'], 'android', $android_device_tokens, $params);
                }
                if (!empty($ios_device_tokens)) {
                    sendPushNotification($message['content'], 'ios', $ios_device_tokens, $params);
                }

                devcpt_log_system(TmsLog::TYPE_NOTIFICATION, '/notification', 'create', __('gui_thong_bao') . ": " . $message['content']);

                return 'success';
            } else {
                return 'pushContentWrongFormat';
            }
        }
    }
    // End NotificationController

    // PermissionController
    public function apiPermissionAdd(Request $request)
    {
        try {
            $slug = $request->input('slug');
            $name = $request->input('name');
            $url = $request->input('url');
            $method = $request->input('method');
            $description = $request->input('description');
            $param = [
                'slug' => 'text',
                'name' => 'longtext',
                'url' => 'longtext',
                'method' => 'text',
                'description' => 'longtext',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json('error');
            }
            $perCheck = Permission::select('id')->where('name', $name)->first();
            if ($perCheck)
                return 'warning';
            \DB::beginTransaction();
            $permission = Permission::create([
                'name' => $name,
                'url' => $url,
                'method' => $method,
                'description' => $description,
                'permission_slug' => $slug,
            ]);

            $info = 'Create permission: ' . '<br>';
            $info .= 'Permission slug: ' . $slug . '<br>';
            $info .= 'Permission name: ' . $name . '<br>';
            $info .= 'Permission url: ' . $url . '<br>';
            $info .= 'Permission method: ' . $method;

            $role_ids = PermissionSlugRole::where('permission_slug', $slug)->pluck('role_id');
            giveRoleToPermission($permission->id, $role_ids); //Add Permission To Roles
            devcpt_log_system('role', '/permission/detail/' . $permission->id, 'create', $info);
            \DB::commit();
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function apiPermissionListDetail(Request $request)
    {
        $slug = $request->input('slug');
        $param = [
            'slug' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $permissions = Permission::select('id', 'name', 'url', 'method', 'description')
            ->where('permission_slug', $slug)
            ->get()->toArray();
        return response()->json($permissions);
    }

    public function apiPermissionDelete($permission_id)
    {
        try {
            \DB::beginTransaction();
            if (!is_numeric($permission_id)) {
                return 'error';
            }
            $permission = Permission::findOrFail($permission_id);
            $permission->delete();

            removeRoleTo($permission_id); //Remove Permission all role
            devcpt_log_system('role', '/permission/detail/' . $permission_id, 'delete', 'Delete permission ID = ' . $permission_id);
            \DB::commit();
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function apiPermissionDetail(Request $request)
    {
        $data = [];
        $param = [
            'permission_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $permission_id = $request->input('permission_id');
        $permission = Permission::select('id', 'name', 'url', 'method', 'description', 'permission_slug')
            ->findOrFail($permission_id);
        $data['permission'] = $permission;
        $permission_slug = permission_slug();
        $permission_name = permission_cat_name();
        $items = [];
        $row = 0;
        foreach ($permission_slug as $key => $values) {
            $row++;
            $items[$row]['id'] = '';
            $items[$row]['space'] = '+)';
            $items[$row]['name'] = $permission_name[$key];
            $items[$row]['disabled'] = true;
            foreach ($values as $var_key => $value) {
                $row++;
                $items[$row]['id'] = '';
                $items[$row]['space'] = '----';
                $items[$row]['name'] = $permission_name[$var_key];
                $items[$row]['disabled'] = true;
                foreach ($value as $v_key => $val) {
                    $row++;
                    $items[$row]['id'] = $v_key;
                    $items[$row]['space'] = '--------';
                    $items[$row]['name'] = $val;
                    $items[$row]['disabled'] = false;
                }
            }
        }
        $data['permission_list'] = $items;
        return response()->json($data);
    }

    public function apiPermissionUpdate(Request $request)
    {
        try {
            $permission_id = $request->input('permission_id');
            $name = $request->input('name');
            //            $description        = $request->input('description');
            $url = $request->input('url');
            $method = $request->input('method');
            $permission_slug = $request->input('permission_slug');
            \DB::beginTransaction();
            $param = [
                'permission_id' => 'number',
                'name' => 'longtext',
                'description' => 'longtext',
                'url' => 'longtext',
                'method' => 'longtext',
                'permission_slug' => 'text'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json('error');
            }
            $permission = Permission::findOrFail($permission_id);
            $permissions = Permission::select('id')
                ->where('name', $name)
                ->whereNotIn('name', [$permission['name']])
                ->first();
            if ($permissions)
                return 'warning';

            $info = 'Update permission: ' . '<br>';
            $info .= 'Permission slug: ' . $permission->permission_slug . ' -> ' . $permission_slug . '<br>';
            $info .= 'Permission name: ' . $permission->name . ' -> ' . $name . '<br>';
            $info .= 'Permission url: ' . $permission->url . ' -> ' . $url . '<br>';
            $info .= 'Permission method: ' . $permission->method . ' -> ' . $method;

            $permission->name = $name;
            $permission->description = $name;
            $permission->url = $url;
            $permission->method = $method;
            $permission->permission_slug = $permission_slug;
            $permission->save();

            devcpt_log_system('role', '/permission/detail/' . $permission_id, 'update', $info);

            //$roleArray = PermissionSlugRole::where('permission_slug',$info['permission_slug'])->pluck('role_id');
            //giveRoleToPermission($permission_id,$roleArray);
            \DB::commit();
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }
    // End PermissionController

    // ReportController
    //Api lấy danh sách chi nhánh
    public function apiGetDistrict(Request $request)
    {
        $districts = TmsDepartments::all()->toArray();
        return response()->json($districts);
    }


    //Api lấy danh sách tỉnh thành theo khu vực
    public function apiGetCityByDistrictReport(Request $request)
    {
        $district = $request->input('district');
        $validates = validate_fails($request, [
            'district' => 'text',
        ]);
        if (!empty($validates)) {
            var_dump($validates);
        } else {
            $citys = TmsCity::where('district', $district)->get()->toArray();
            return response()->json($citys);
        }
    }

    //Api lấy danh sách tỉnh thành theo chi nhánh
    public function apiGetCityByDepartmentReport(Request $request)
    {
        $department = $request->input('department');
        $validates = validate_fails($request, [
            'department' => 'number',
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            $citys = TmsCity::whereIn('id', function ($query) use ($department) {
                $query->select('city_id')->from('tms_department_citys')->where('department_id', $department);
            })->get()->toArray();
            return response()->json($citys);
        }
    }

    //Api lấy danh sách Đại lý theo Tỉnh
    public function apiGetBranchByCityReport(Request $request)
    {
        $city = $request->input('city');
        $validates = validate_fails($request, [
            'city' => 'number',
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            $branchs = TmsCityBranch::with('branch')
                ->where('city_id', $city);
            $branchs = $branchs->get()->toArray();
            return response()->json($branchs);
        }
    }

    //Api lấy danh sách Điểm bán theo Đại lý
    public function apiGetSaleRoomByBranchReport(Request $request)
    {
        $branch = $request->input('branch');
        $validates = validate_fails($request, [
            'branch' => 'number'
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            $salerooms = TmsBranchSaleRoom::with('sale_room_name')
                ->where('branch_id', $branch);
            $salerooms = $salerooms->get()->toArray();
            return response()->json($salerooms);
        }
    }

    //chart
    public function apiShowStatistic(Request $request)
    {
        //define
        $data = [
            'district' => [],
            'city' => [],
            'branch' => [],
            'saleroom' => [],
            'user' => [],
            'userConfirm' => [],
            'course_offline' => 0,
            'course_online' => 0
        ];

        $districts = TmsDepartments::all();

        foreach ($districts as $district) {
            $data['district'][$district->id] = $district->name;
            $data['city'][$district->id] = [];
            $data['branch'][$district->id] = [];
            $data['saleroom'][$district->id] = [];
            $data['user'][$district->id] = [];
            $data['userConfirm'][$district->id] = [];
        }

        //pos
        $base_data = TmsCity::where('tms_city.deleted', 0)
            ->where('tms_city.parent', 0)
            //->whereIn('tms_city.district', ['MB', 'MT', 'MN'])
            ->leftJoin('tms_department_citys', 'tms_city.id', '=', 'tms_department_citys.city_id')
            ->leftJoin('tms_departments', 'tms_departments.id', '=', 'tms_department_citys.department_id')
            ->leftJoin('tms_city_branch', 'tms_city.id', '=', 'tms_city_branch.city_id')
            ->leftJoin('tms_branch_sale_room', 'tms_city_branch.branch_id', '=', 'tms_branch_sale_room.branch_id')
            ->leftJoin('tms_sale_room_user', function ($join) {
                $join->on('tms_branch_sale_room.sale_room_id', '=', 'tms_sale_room_user.sale_room_id');
                $join->where('tms_sale_room_user.type', '=', TmsSaleRoomUser::POS);
            })
            ->leftJoin('tms_user_detail', 'tms_sale_room_user.user_id', '=', 'tms_user_detail.user_id')
            ->select(
                'tms_city.id',
                'tms_departments.name as district_name',
                'tms_departments.id as district',
                //'tms_city.district',
                'tms_city_branch.branch_id',
                'tms_branch_sale_room.sale_room_id',
                'tms_sale_room_user.user_id',
                'tms_user_detail.fullname',
                'tms_user_detail.confirm'
            )->get();
        //agents
        $base_data_2 = TmsCity::where('tms_city.deleted', 0)
            ->where('tms_city.parent', 0)
            //            ->whereIn('tms_city.district', ['MB', 'MT', 'MN'])
            ->leftJoin('tms_department_citys', 'tms_city.id', '=', 'tms_department_citys.city_id')
            ->leftJoin('tms_departments', 'tms_departments.id', '=', 'tms_department_citys.department_id')
            ->leftJoin('tms_city_branch', 'tms_city.id', '=', 'tms_city_branch.city_id')
            ->leftJoin('tms_sale_room_user', function ($join) {
                $join->on('tms_city_branch.branch_id', '=', 'tms_sale_room_user.sale_room_id');
                $join->where('tms_sale_room_user.type', '=', TmsSaleRoomUser::AGENTS);
            })
            ->leftJoin('tms_user_detail', 'tms_sale_room_user.user_id', '=', 'tms_user_detail.user_id')
            ->select(
                'tms_city.id',
                'tms_departments.name as district_name',
                'tms_departments.id as district',
                //'tms_city.district',
                'tms_city_branch.branch_id',
                'tms_sale_room_user.user_id',
                'tms_user_detail.fullname',
                'tms_user_detail.confirm'
            )->get()->toArray();

        foreach ($base_data as $item) {
            if ($item->district) {
                if (!in_array($item->id, $data['city'][$item->district])) {
                    $data['city'][$item->district][] = $item->id;
                }
                if (isset($item->branch_id) && !in_array($item->branch_id, $data['branch'][$item->district])) {
                    $data['branch'][$item->district][] = $item->branch_id;
                }
                if (isset($item->sale_room_id) && !in_array($item->sale_room_id, $data['saleroom'][$item->district])) {
                    $data['saleroom'][$item->district][] = $item->sale_room_id;
                }
                if (isset($item->user_id)) {
                    $data['user'][$item->district][] = $item->user_id;
                    if ($item->confirm == 1) {
                        $data['userConfirm'][$item->district][] = $item->user_id;
                    }
                }
            }
        }

        foreach ($base_data_2 as $item) {
            if (isset($item->user_id)) {
                $data['user'][$item->district][] = $item->user_id;
                if ($item->confirm == 1) {
                    $data['userConfirm'][$item->district][] = $item->user_id;
                }
            }
        }

        $course_data = MdlCourse::where('category', '<>', 2)->get();
        foreach ($course_data as $course_item) {
            if ($course_item->category == 5) {
                $data['course_offline'] += 1;
            } else {
                $data['course_online'] += 1;
            }
        }

        return response()->json($data);
    }

    //Table 2
    public function apiShowReportByCity(Request $request)
    {
        $city = $request->input('city');
        $branch = $request->input('branch');
        $saleroom = $request->input('saleroom');

        $validates = validate_fails($request, [
            'city' => 'number',
            'saleroom' => 'number',
            'branch' => 'number'
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {

            if (strlen($saleroom) != 0) {
                $selected_level = "saleroom";
            } elseif (strlen($branch) != 0) {
                $selected_level = "branch";
            } else {
                $selected_level = "city";
            }

            $city_data_source = TmsCity::where('tms_city.id', $city)
                ->where('tms_city.parent', 0)
                ->leftJoin('tms_city_branch', 'tms_city.id', '=', 'tms_city_branch.city_id')
                ->leftJoin('tms_branch', 'tms_city_branch.branch_id', '=', 'tms_branch.id')
                ->leftJoin('tms_branch_sale_room', 'tms_city_branch.branch_id', '=', 'tms_branch_sale_room.branch_id')
                ->leftJoin('tms_sale_rooms', 'tms_branch_sale_room.sale_room_id', '=', 'tms_sale_rooms.id')
                ->leftJoin('tms_sale_room_user', function ($join) {
                    $join->on('tms_branch_sale_room.sale_room_id', '=', 'tms_sale_room_user.sale_room_id');
                    $join->where('tms_sale_room_user.type', TmsSaleRoomUser::POS);
                })
                ->leftJoin('tms_user_detail', 'tms_sale_room_user.user_id', '=', 'tms_user_detail.user_id')
                ->leftJoin('course_final', 'tms_sale_room_user.user_id', '=', 'course_final.userid')
                ->leftJoin('student_certificate', 'tms_sale_room_user.user_id', '=', 'student_certificate.userid');
            //                ->leftJoin('mdl_course_completions', function ($join) {
            //                    $join->on('mdl_course_completions.userid', '=', 'tms_sale_room_user.user_id');
            //                    $join->whereExists(function ($query) {
            //                        $query->select(DB::raw(1))
            //                            ->from('tms_traninning_users as ttu')
            //                            ->join('tms_trainning_categories as ttc','ttc.trainning_id','=','ttu.trainning_id')
            //                            ->whereRaw('mdl_course_completions.userid = ttu.user_id');
            //                    });
            //                    //$join->whereIn('mdl_course_completions.course', $certificate_course_ids);
            //                });

            $city_data_source_branch = TmsCity::where('tms_city.id', $city)
                ->where('tms_city.parent', 0)
                ->leftJoin('tms_city_branch', 'tms_city.id', '=', 'tms_city_branch.city_id')
                ->leftJoin('tms_branch', 'tms_city_branch.branch_id', '=', 'tms_branch.id')
                ->leftJoin('tms_sale_room_user', function ($join) {
                    $join->on('tms_city_branch.branch_id', '=', 'tms_sale_room_user.sale_room_id');
                    $join->where('tms_sale_room_user.type', TmsSaleRoomUser::AGENTS);
                })
                ->leftJoin('tms_user_detail', 'tms_sale_room_user.user_id', '=', 'tms_user_detail.user_id')
                ->leftJoin('course_final', 'tms_sale_room_user.user_id', '=', 'course_final.userid')
                ->leftJoin('student_certificate', 'tms_sale_room_user.user_id', '=', 'student_certificate.userid');
            //                ->leftJoin('mdl_course_completions', function ($join) {
            //                    $join->on('mdl_course_completions.userid', '=', 'tms_sale_room_user.user_id');
            //                    $join->whereExists(function ($query) {
            //                        $query->select(DB::raw(1))
            //                            ->from('tms_traninning_users as ttu')
            //                            ->join('tms_trainning_categories as ttc','ttc.trainning_id','=','ttu.trainning_id')
            //                            ->whereRaw('mdl_course_completions.userid = ttu.user_id');
            //                    });
            //                    //$join->whereIn('mdl_course_completions.course', $certificate_course_ids);
            //                });

            if (strlen($branch) != 0) {
                $city_data_source = $city_data_source->where('tms_city_branch.branch_id', $branch);
                $city_data_source_branch = $city_data_source_branch->where('tms_city_branch.branch_id', $branch);
            }

            if (strlen($saleroom) != 0) {
                $city_data_source = $city_data_source->where('tms_branch_sale_room.sale_room_id', $saleroom);
            }

            $city_data_org = $city_data_source->select(
                'tms_city.id',
                'tms_city.name',
                'tms_city.district',
                'tms_city_branch.branch_id',
                'tms_branch.name as branch_name',
                'tms_branch.code as branch_code',
                'tms_branch_sale_room.sale_room_id',
                'tms_sale_rooms.name as sale_room_name',
                'tms_sale_rooms.code as sale_room_code',
                'tms_sale_room_user.user_id',
                DB::raw("'" . TmsSaleRoomUser::POS . "' as type"),
                'tms_user_detail.fullname',
                'tms_user_detail.confirm',
                //'mdl_course_completions.course'
                'course_final.timecompleted',
                'student_certificate.status'
            )
                ->get()->toArray();


            $city_data_branch = $city_data_source_branch->select(
                'tms_city.id',
                'tms_city.name',
                'tms_city.district',
                'tms_city_branch.branch_id',
                'tms_branch.name as branch_name',
                'tms_branch.code as branch_code',
                DB::raw("NULL as sale_room_id"),
                DB::raw("NULL as sale_room_name"),
                DB::raw("NULL as sale_room_code"),
                'tms_sale_room_user.user_id',
                DB::raw("'" . TmsSaleRoomUser::AGENTS . "' as type"),
                'tms_user_detail.fullname',
                'tms_user_detail.confirm',
                //'mdl_course_completions.course'
                'course_final.timecompleted',
                'student_certificate.status'
            )
                ->get()->toArray();


            $city_data = array_merge($city_data_org, $city_data_branch);

            $data = array();

            foreach ($city_data as $item) {

                if ($selected_level == 'saleroom' || $selected_level == 'branch') {
                    $item_id = $item['sale_room_id'];
                    $child_id = $item['sale_room_id'];
                    $child_name = strlen($item['sale_room_name']) != 0 ? $item['sale_room_name'] : $item['sale_room_code'];
                    $parent_id = $item['branch_id'];
                    $parent_name = strlen($item['branch_name']) != 0 ? $item['branch_name'] : $item['branch_code'];
                } else { //city
                    $item_id = $item['branch_id'];
                    $child_id = $item['branch_id'];
                    $child_name = strlen($item['branch_name']) != 0 ? $item['branch_name'] : $item['branch_code'];
                    $parent_id = $item['id'];
                    $parent_name = $item['name'];
                }

                //Define parent object
                if (empty($data)) {
                    $data = array(
                        'id' => $parent_id,
                        'name' => $parent_name,
                        'items' => [],
                        'branch_users' => [],
                    );
                }

                //Define child object
                if ($item['type'] != TmsSaleRoomUser::AGENTS) {
                    if (!isset($data['items'][$item_id]) && isset($item_id)) {
                        $data['items'][$item_id] = [
                            'id' => $child_id,
                            'name' => $child_name,
                            'user' => [],
                            'user_confirm' => [],
                            'user_completed' => [],
                        ];
                    }
                }

                if ($item['user_id']) {

                    $single_user = array(
                        'user_id' => $item['user_id'],
                        'user_name' => $item['fullname']
                    );

                    //Agents only
                    if ($item['type'] == TmsSaleRoomUser::AGENTS) { //Check for branch data
                        //Add branch users to parent continue to next iteration

                        //Define
                        if (!isset($data['branch_users'][$item['branch_id']])) {
                            $data['branch_users'][$item['branch_id']] = [
                                'user' => [],
                                'user_confirm' => [],
                                'user_completed' => [],
                            ];
                        }

                        //all user
                        $data['branch_users'][$item['branch_id']]['user'][$item['user_id']] = $single_user;
                        //user confirmed
                        if ($item['confirm'] == 1 || $item['status'] == 2) {
                            $data['branch_users'][$item['branch_id']]['user_confirm'][$item['user_id']] = $single_user;
                        }
                        //user completed
                        //                        if (is_numeric($item['course']) && $item['course'] != 0) {
                        //                            //define user
                        //                            if (!isset($data['branch_users'][$item['branch_id']]['user_completed'][$item['user_id']])) {
                        //                                $data['branch_users'][$item['branch_id']]['user_completed'][$item['user_id']] = array(
                        //                                    'fullname' => $item['fullname'],
                        //                                    'courses' => []
                        //                                );
                        //                            }
                        //                            $data['branch_users'][$item['branch_id']]['user_completed'][$item['user_id']]['courses'][] = $item['course'];
                        //                        }
                        if (isset($item['timecompleted']) || $item['confirm'] == 1 || $item['status'] == 2 || $item['status'] == 1) {
                            if (!isset($data['branch_users'][$item['branch_id']]['user_completed'][$item['user_id']])) {
                                $data['branch_users'][$item['branch_id']]['user_completed'][$item['user_id']] = $single_user;
                            }
                        }
                        continue;
                    }
                    //End agents only

                    //all user
                    $data['items'][$item_id]['user'][$item['user_id']] = $single_user;
                    //user confirmed
                    if ($item['confirm'] == 1 || $item['status'] == 2) {
                        $data['items'][$item_id]['user_confirm'][$item['user_id']] = $single_user;
                    }
                    //user completed
                    //                    if (is_numeric($item['course']) && $item['course'] != 0) {
                    //                        //define user
                    //                        if (!isset($data['items'][$item_id]['user_completed'][$item['user_id']])) {
                    //                            $data['items'][$item_id]['user_completed'][$item['user_id']] = array(
                    //                                'fullname' => $item['fullname'],
                    //                                'courses' => []
                    //                            );
                    //                        }
                    //                        $data['items'][$item_id]['user_completed'][$item['user_id']]['courses'][] = $item['course'];
                    //                    }
                    if (isset($item['timecompleted']) || $item['confirm'] == 1 || $item['status'] == 2 || $item['status'] == 1) {
                        $data['items'][$item_id]['user_completed'][$item['user_id']] = $single_user;
                    }
                }
            }

            $arranged_items = [];

            $total_user_array = [];
            $total_confirm_array = [];
            $total_completed_array = [];
            $total_incomplete_array = [];

            $new_branch_users = array();
            $branch_user_count = 0;

            $branch_users = $data['branch_users'];
            if (count($branch_users) == 0) {
                $new_branch_users[$data['id']]['user'] = [];
                $new_branch_users[$data['id']]['user_confirm'] = [];
                $new_branch_users[$data['id']]['user_completed'] = [];
                $new_branch_users[$data['id']]['user_incomplete'] = [];
            } else {
                foreach ($branch_users as $branch_id => $branch_user) {
                    $arranged_completed = [];
                    $arranged_incomplete = [];

                    //                    foreach ($branch_user['user_completed'] as $user_id => $course_array) {
                    //                        if (count($course_array['courses']) == certificate_course_number($user_id)) {
                    //                            $arranged_completed[$user_id] = [
                    //                                'user_id' => $user_id,
                    //                                'user_name' => $course_array['fullname'],
                    //                            ];
                    //                        }
                    //                    }
                    $arranged_completed = $branch_user['user_completed'];

                    foreach ($branch_user['user'] as $user_id => $user_info) {
                        if (!in_array($user_id, array_keys($arranged_completed))) {
                            $arranged_incomplete[$user_id] = $user_info;
                        }
                    }

                    $new_branch_users[$branch_id]['user'] = $branch_user['user'];
                    $new_branch_users[$branch_id]['user_confirm'] = $branch_user['user_confirm'];
                    $new_branch_users[$branch_id]['user_completed'] = $arranged_completed;
                    $new_branch_users[$branch_id]['user_incomplete'] = $arranged_incomplete;

                    $total_user_array = array_merge($total_user_array, array_keys($branch_user['user']));
                    $total_confirm_array = array_merge($total_confirm_array, array_keys($branch_user['user_confirm']));
                    $total_completed_array = array_merge($total_completed_array, array_keys($arranged_completed));
                    $total_incomplete_array = array_merge($total_incomplete_array, array_keys($arranged_incomplete));
                    $branch_user_count += count($branch_user['user']);
                }
            }

            $data['branch_users'] = $new_branch_users;

            //Loops for children
            $items = $data['items'];


            $arranged = $data;

            foreach ($items as $branch_id => $item_single) {

                $arranged_completed = [];
                $arranged_incomplete = [];
                $user = [];
                $user_confirm = [];

                //Level city cong vao du lieu branch items
                if (isset($data['branch_users'][$branch_id]) && $selected_level == "city") { //Level city
                    $arranged_completed = $data['branch_users'][$branch_id]['user_completed'];
                    $user = $data['branch_users'][$branch_id]['user'];
                    $user_confirm = $data['branch_users'][$branch_id]['user_confirm'];
                    $arranged_incomplete = $data['branch_users'][$branch_id]['user_incomplete'];
                }

                //                foreach ($item_single['user_completed'] as $user_id => $course_array) {
                //                    if (count($course_array['courses']) == certificate_course_number($user_id)) {
                //                        $arranged_completed[$user_id] = [
                //                            'user_id' => $user_id,
                //                            'user_name' => $course_array['fullname'],
                //                        ];
                //                    }
                //                }

                $arranged_completed = $arranged_completed + $item_single['user_completed']; //array merge reset keys show use add instead

                foreach ($item_single['user'] as $user_id => $user_info) {
                    if (!in_array($user_id, array_keys($arranged_completed))) {
                        $arranged_incomplete[$user_id] = $user_info;
                    }
                }

                $merge_user = array_values($this->arrayUniqueMultidimensional(array_merge($user, $item_single['user'])));
                $merge_user_confirm = array_values($this->arrayUniqueMultidimensional(array_merge($user_confirm, $item_single['user_confirm'])));
                $merge_user_completed = array_values($this->arrayUniqueMultidimensional($arranged_completed));
                $merge_user_incomplete = array_values($this->arrayUniqueMultidimensional($arranged_incomplete));

                $total_user_array = array_merge($total_user_array, array_keys($item_single['user']));
                $total_confirm_array = array_merge($total_confirm_array, array_keys($item_single['user_confirm']));
                $total_completed_array = array_merge($total_completed_array, array_keys($arranged_completed));
                $total_incomplete_array = array_merge($total_incomplete_array, array_keys($arranged_incomplete));

                $arranged_items[] = [
                    'id' => $item_single['id'],
                    'name' => $item_single['name'],
                    'user' => $merge_user,
                    'user_confirm' => $merge_user_confirm,
                    'user_completed' => $merge_user_completed,
                    'user_incomplete' => $merge_user_incomplete,
                ];
            }

            $arranged['user_count'] = count(array_unique($total_user_array));
            $arranged['user_confirm_count'] = count(array_unique($total_confirm_array));
            $arranged['user_completed_count'] = count(array_unique($total_completed_array));
            $arranged['user_incomplete_count'] = count(array_unique($total_incomplete_array));
            $arranged['branch_user_count'] = $branch_user_count;
            $arranged['items'] = $arranged_items;
            $arranged['selected_level'] = $selected_level;


            return $arranged;
        }
    }

    //Table 1
    public function apiShowReportByRegion(Request $request)
    { //show theo vung mien, can chia truong hop co chon vung va k chon

        $selected = '';
        $selected_level = "district";

        $district = $request->input('district');
        $validates = validate_fails($request, [
            'district' => 'text',
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            //            $district_arr = ['MB', 'MT', 'MN'];

            $base_query_pos = TmsCity::where('tms_city.deleted', 0);
            $base_query_agents = TmsCity::where('tms_city.deleted', 0);

            if (strlen($district) != 0) {
                $selected = $district;
                //                $district_arr = [$district];
                $base_query_pos = $base_query_pos->where('tms_department_citys.department_id', $district);
                $base_query_agents = $base_query_agents->where('tms_department_citys.department_id', $district);
            }

            $region_data_pos = $base_query_pos
                ->where('tms_city.parent', 0)
                //->whereIn('tms_city.district', $district_arr)
                ->leftJoin('tms_department_citys', 'tms_city.id', '=', 'tms_department_citys.city_id')
                ->leftJoin('tms_departments', 'tms_departments.id', '=', 'tms_department_citys.department_id')
                ->leftJoin('tms_city_branch', 'tms_city.id', '=', 'tms_city_branch.city_id')
                ->leftJoin('tms_branch_sale_room', 'tms_city_branch.branch_id', '=', 'tms_branch_sale_room.branch_id')
                //->leftJoin('tms_sale_room_user', 'tms_branch_sale_room.sale_room_id', '=', 'tms_sale_room_user.sale_room_id')
                ->leftJoin('tms_sale_room_user as su1', function ($join) {
                    $join->on('tms_branch_sale_room.sale_room_id', '=', 'su1.sale_room_id');
                    $join->where('su1.type', TmsSaleRoomUser::POS);
                })
                ->leftJoin('tms_user_detail', 'su1.user_id', '=', 'tms_user_detail.user_id')
                ->leftJoin('course_final', 'su1.user_id', '=', 'course_final.userid')
                ->leftJoin('student_certificate', 'su1.user_id', '=', 'student_certificate.userid')
                //                ->leftJoin('mdl_course_completions', function ($join) {
                //                    $join->on('mdl_course_completions.userid', '=', 'su1.user_id');
                //                    $join->whereExists(function ($query) {
                //                        $query->select(DB::raw(1))
                //                            ->from('tms_traninning_users as ttu')
                //                            ->join('tms_trainning_categories as ttc','ttc.trainning_id','=','ttu.trainning_id')
                //                            ->whereRaw('mdl_course_completions.userid = ttu.user_id');
                //                    });
                //                    //$join->whereIn('mdl_course_completions.course', $certificate_course_ids);
                ////                    $join->whereIn('mdl_course_completions.course', function ($query) {
                ////                        $query->select('id')->from('mdl_course')->where('category', 3);
                ////                    });
                //                })
                ->select(
                    'tms_city.id',
                    'tms_city.name',
                    'tms_departments.name as district_name',
                    'tms_departments.id as district',
                    //'tms_city.district',
                    'tms_city_branch.branch_id',
                    'tms_branch_sale_room.sale_room_id',
                    'su1.user_id',
                    'su1.type as type',
                    'tms_user_detail.fullname',
                    'tms_user_detail.confirm',
                    //'mdl_course_completions.course'
                    'course_final.timecompleted',
                    'student_certificate.status'
                )->get()->toArray();


            $region_data_agents = $base_query_agents
                ->where('tms_city.parent', 0)
                //->whereIn('tms_city.district', $district_arr)
                ->leftJoin('tms_department_citys', 'tms_city.id', '=', 'tms_department_citys.city_id')
                ->leftJoin('tms_departments', 'tms_departments.id', '=', 'tms_department_citys.department_id')
                ->leftJoin('tms_city_branch', 'tms_city.id', '=', 'tms_city_branch.city_id')
                ->leftJoin('tms_sale_room_user as su2', function ($join) {
                    $join->on('tms_city_branch.branch_id', '=', 'su2.sale_room_id');
                    $join->where('su2.type', TmsSaleRoomUser::AGENTS);
                })
                ->leftJoin('tms_user_detail', 'su2.user_id', '=', 'tms_user_detail.user_id')
                ->leftJoin('course_final', 'su2.user_id', '=', 'course_final.userid')
                ->leftJoin('student_certificate', 'su2.user_id', '=', 'student_certificate.userid')
                //                ->leftJoin('mdl_course_completions', function ($join) {
                //                    $join->on('mdl_course_completions.userid', '=', 'su2.user_id');
                //                    $join->whereExists(function ($query) {
                //                        $query->select(DB::raw(1))
                //                            ->from('tms_traninning_users as ttu')
                //                            ->join('tms_trainning_categories as ttc','ttc.trainning_id','=','ttu.trainning_id')
                //                            ->whereRaw('mdl_course_completions.userid = ttu.user_id');
                //                    });
                //                    //$join->whereIn('mdl_course_completions.course', $certificate_course_ids);
                ////                    $join->whereIn('mdl_course_completions.course', function ($query) {
                ////                        $query->select('id')->from('mdl_course')->where('category', 3);
                ////                    });
                //                })
                ->select(
                    'tms_city.id',
                    'tms_city.name',
                    'tms_departments.name as district_name',
                    'tms_departments.id as district',
                    //'tms_city.district',
                    'tms_city_branch.branch_id',
                    DB::raw('NULL as saleroom_id'),
                    'su2.user_id',
                    'su2.type as type',
                    'tms_user_detail.fullname',
                    'tms_user_detail.confirm',
                    //'mdl_course_completions.course'
                    'course_final.timecompleted',
                    'student_certificate.status'
                )->get()->toArray();

            $region_data = array_merge($region_data_pos, $region_data_agents);

            $data_request = array();

            foreach ($region_data as $item) {

                //Define region object
                //$data_request[$item['district']]['name'] = $this->getRegionName($item['district']);
                $data_request[$item['district']]['name'] = $item['district_name'];


                if (!isset($data_request[$item['district']]['cities'][$item['id']])) {
                    $data_request[$item['district']]['cities'][$item['id']] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'user' => [], //all
                        'user_confirm' => [],
                        'user_completed' => [],
                    ];
                }

                if ($item['user_id']) { //no duplicate
                    $data_request[$item['district']]['cities'][$item['id']]['user'][$item['user_id']] = $item['user_id'];
                    if ($item['confirm'] == 1 || $item['status'] == 2) { //no duplicate
                        $data_request[$item['district']]['cities'][$item['id']]['user_confirm'][$item['user_id']] = $item['user_id'];
                    }
                    //                    if (is_numeric($item['course']) && $item['course'] != 0) { //duplicated courses => need array_unique
                    //                        $data_request[$item['district']]['cities'][$item['id']]['user_completed'][$item['user_id']][] = $item['course'];
                    //                    }
                    if (isset($item['timecompleted']) || $item['confirm'] == 1 || $item['status'] == 2 || $item['status'] == 1) {
                        $data_request[$item['district']]['cities'][$item['id']]['user_completed'][$item['user_id']] = $item['user_id'];
                    }
                }
            }

            //Re arrange region object
            //Unique user
            //Unique user_confirm
            //Check user completed and compare with $certificate_course_count

            $arranged = array(
                'region' => [],
                'selected' => $selected
            );

            foreach ($data_request as $key => $data_item) {
                $region = [];
                $region['name'] = $data_item['name'];
                $region['code'] = $key;
                $region['cities'] = [];

                $total_user = 0;
                $total_confirm = 0;
                $total_completed = 0;
                $total_incomplete = 0;

                $cities = $data_item['cities'];
                foreach ($cities as $city) {
                    $city_user_completed = [];

                    //                    foreach ($city['user_completed'] as $user_id => $course_array) {
                    //                        if (count(array_unique($course_array)) == certificate_course_number($user_id)) {
                    //                            $city_user_completed[] = $user_id;
                    //                        }
                    //                    }
                    //                    $total_completed += count($city_user_completed);

                    $city_user = array_values($city['user']);
                    $total_user += count($city_user);

                    $city_user_completed = array_values($city['user_completed']);
                    $total_completed += count($city_user_completed);

                    $city_user_confirm = array_values($city['user_confirm']);
                    $total_confirm += count($city_user_confirm);

                    $city_user_incomplete = array_values(array_diff($city_user, $city_user_completed));
                    $total_incomplete += count($city_user_incomplete);

                    $region['cities'][] = [
                        'id' => $city['id'],
                        'name' => $city['name'],
                        'user' => $city_user,
                        'user_confirm' => $city_user_confirm,
                        'user_completed' => $city_user_completed,
                        'user_incomplete' => $city_user_incomplete,
                    ];

                    $region['user_count'] = $total_user;
                    $region['user_confirm_count'] = $total_confirm;
                    $region['user_completed_count'] = $total_completed;
                    $region['user_incomplete_count'] = $total_incomplete;
                }

                $arranged['region'][] = $region;
            }

            $arranged['selected_level'] = $selected_level;

            return $arranged;
        }
    }

    function getRegionName($regionCode)
    {
        switch ($regionCode) {
            case "MB":
                return "Miền Bắc";
            case "MT":
                return "Miền Trung";
            case "MN":
                return "Miền Nam";
            default:
                return "";
        }
    }

    function arrayUniqueMultidimensional($array)
    {
        return array_intersect_key($array, array_unique(array_map('serialize', $array)));
    }
    // End ReportController

    // RoleController
    public function apiCreateRole(Request $request)
    {
        try {
            $name = $request->input('name');
            $description = $request->input('description');

            $param = [
                'name' => 'code',
                'description' => 'longtext',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $lastRole = MdlRole::latest()->first();
            $checkRole = Role::where('name', $name)->first();
            if ($checkRole) {
                return response()->json(status_message('error', __('quen_da_ton_tai_khong_the_them')));
            }
            //Tạo quyền bên LMS
            $mdlRole = new MdlRole;
            //dd($mdlRole);
            $mdlRole->shortname = $name;
            $mdlRole->description = $description;
            $mdlRole->sortorder = $lastRole['sortorder'] + 1;
            $mdlRole->archetype = 'user';
            $mdlRole->save();

            $role = new Role();
            $role->mdl_role_id = $mdlRole->id;
            $role->name = $name;
            $role->description = $description;
            $role->status = 1;
            $role->save();

            $type = 'role';
            $url = '/roles/edit/' . $role->id;
            $action = 'create';
            $info = 'Tạo mới quyền: ' . $name;
            devcpt_log_system($type, $url, $action, $info);
            return response()->json(status_message('success', __('them_quyen_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListRoleRole()
    {
        $roles = Role::whereNotIn('name', [\App\Role::EDITING_TEACHER, \App\Role::COURSE_CREATOR])->get()->toArray();
        return response()->json($roles);
    }

    //Lấy dữ liệu của role
    public function apiListDataRole(Request $request)
    {
        $role_id = $request->input('role_id');
        if (!is_numeric($role_id))
            return response()->json([]);
        $role = Role::select('mdl_role_id')->findOrFail($role_id);
        $data = [];
        $data['roles'] = Role::select('name', 'description', 'status')->findOrFail($role_id);
        $data['permission_name'] = permission_cat_name();
        $data['permission_slug'] = permission_slug();
        $data['permission_select'] = PermissionSlugRole::where('role_id', $role_id)->pluck('permission_slug');

        $dataCap = MdlCapabilities::select('name', 'component');
        $dataCap = $dataCap->orderBy('component', 'ASC');
        $dataCap = $dataCap->get()->toArray();
        //$total = ceil($dataCap->total()/20);
        $pagination = [
            //'total' => $total,
            //'current_page' => $dataCap->currentPage(),
            'data' => $dataCap,
        ];
        $data['pagination'] = $pagination;
        $cap_select = MdlRoleCapabilities::where('roleid', $role['mdl_role_id'])->pluck('capability');
        $data['cap_select'] = $cap_select;

        return response()->json($data);
    }

    //Lấy danh sách tỉnh thành theo khu vực ( Edit Role )
    public function apiGetDataCity(Request $request)
    {
        $district = $request->input('district');
        $data = [];
        if ($district) {
            $data = TmsCity::select('id', 'name')->where([
                'deleted' => 0,
                'parent' => 0,
                'district' => $district
            ])->get()->toArray();
        }
        return response()->json($data);
    }

    //Lấy danh sách Đại lý theo Tỉnh thành ( Edit Role )
    public function apiGetDataBranch(Request $request)
    {
        $citys = $request->input('citys');
        $data = [];
        if (!empty($citys)) {
            $data = TmsCityBranch::with('branch')->whereIn('city_id', $citys)->get()->toArray();
        }
        return response()->json($data);
    }

    //Lấy danh sách Điểm bán theo Đại lý ( Edit Role )
    public function apiGetDataSaleroom(Request $request)
    {
        $branchs = $request->input('branchs');
        $data = [];
        if (!empty($branchs)) {
            $data = TmsBranchSaleRoom::with('sale_room')->whereIn('branch_id', $branchs)->get()->toArray();
        }
        return response()->json($data);
    }

    public function apiUpdateRole(Request $request)
    {
        try {
            $role_id = $request->input('role_id');
            $per_slug_input = $request->input('per_slug_input');
            //$cap_input      = $request->input('cap_input');
            $name = $request->input('name');
            $description = $request->input('description');

            if (!is_array($per_slug_input))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));

            $param = [
                'role_id' => 'number',
                'name' => 'code',
                'description' => 'longtext',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            \DB::beginTransaction();
            $role = Role::findOrFail($role_id);
            $role->name = $name;
            $role->description = $description;
            $role->save();

            $mdlRole = MdlRole::findOrFail($role['mdl_role_id']);
            $mdlRole->shortname = $name;
            $mdlRole->description = $description;
            $mdlRole->save();

            PermissionSlugRole::where('role_id', $role_id)->delete();
            if (!empty($per_slug_input)) {

                $arr_data = [];
                $data_item = [];

                foreach ($per_slug_input as $per_slug) {
                    if (!preg_match('/^[A-Za-z\-]+$/', $per_slug)) {
                        //return $per_slug;
                        return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                    }
                    //                    PermissionSlugRole::create([
                    //                        'role_id' => $role_id,
                    //                        'permission_slug' => $per_slug
                    //                    ]);

                    $data_item['role_id'] = $role_id;
                    $data_item['permission_slug'] = $per_slug;

                    array_push($arr_data, $data_item);
                }

                PermissionSlugRole::insert($arr_data);
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            removePermissionTo($role_id); //Remove permission to role
            if (!empty($per_slug_input)) {
                $permissions = Permission::whereIn('permission_slug', $per_slug_input)->pluck('id');
                givePermissionToRole($role_id, $permissions); //Add permission to Role
            }

            //Thêm quyền bên LMS
            apply_role_lms($role_id, $per_slug_input);


            $type = 'role';
            $url = '/roles/edit/' . $role_id;
            $action = 'update';
            $info = 'Cập nhật quyền: ' . $name;
            devcpt_log_system($type, $url, $action, $info);
            \DB::commit();
            return response()->json(status_message('success', __('cap_nhat_vai_tro_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiDeleteRole(Request $request)
    {
        try {
            $role_id = $request->input('role_id');
            if (!is_numeric($role_id))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            \DB::beginTransaction();
            $role = Role::findOrFail($role_id);
            $role_name = $role['name'];

            //Xóa role bên LMS
            MdlRole::where('id', $role['mdl_role_id'])->delete();
            MdlRoleCapabilities::where('roleid', $role['mdl_role_id'])->delete();
            MdlRoleAssignments::where('roleid', $role['mdl_role_id'])->delete();

            //Clear cache
            api_lms_clear_cache($role['mdl_role_id']);

            //Xóa role bên TMS
            PermissionSlugRole::where('role_id', $role_id)->delete();
            ModelHasRole::where('role_id', $role_id)->delete();
            removePermissionTo($role_id); //Remove permission to role
            $role->delete();

            $type = 'role';
            $url = '*';
            $action = 'delete';
            $info = 'Xóa quyền quyền: ' . $role_name;
            devcpt_log_system($type, $url, $action, $info);
            \DB::commit();
            return response()->json(status_message('success', __('xoa_vai_tro_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListAddUserRole(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $role_id = $request->input('role_id');

        $param = [
            'role_id' => 'number',
            'row' => 'number',
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_user_detail as tud')
            ->select('tud.user_id', 'tud.fullname', 'tud.cmtnd', 'tud.email', 'tud.phone', 'mu.username')
            ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
            ->where('tud.deleted', 0)
            ->whereNotExists(function ($query) use ($role_id) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles as mhr')
                    ->where('mhr.role_id', $role_id)
                    ->whereRaw('mhr.model_id = tud.user_id');
            });


        $role = \App\Role::findOrFail($role_id);
        if (!$role) {
            return response()->json();
        }

        if (in_array($role->name, \App\Role::arr_role_special)) { //them dieu kien check khi quyen la nvkd, truong dai ly or truong diem ban
            $arr_data = \App\Role::arr_role_special;
            if (($key = array_search($role->name, $arr_data)) !== false) {
                unset($arr_data[$key]);
            }

            $data = $data->whereNotExists(function ($query) use ($arr_data) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles as mhr')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->whereIn('r.name', $arr_data)
                    ->whereRaw('mhr.model_id = tud.user_id');
            });
        }

        if ($this->keyword) {
            $data = $data->where(function ($query) {
                $query->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.phone', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }

        $data = $data->orderBy('mu.username', 'ASC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiAddUserByRole(Request $request)
    {
        $user_add = $request->input('user_add');
        $role_id = $request->input('role_id');
        if (!is_numeric($role_id) || !is_array($user_add)) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
        $role = Role::select('mdl_role_id', 'name')->findOrFail($role_id);
        $mdl_role_id = $role['mdl_role_id'];
        try {
            if ($role_id && $user_add) {
                foreach ($user_add as $user_id) {
                    \DB::beginTransaction();

                    $mdlUser = MdlUser::findOrFail($user_id);
                    $tmsUser = TmsUserDetail::where('user_id', $user_id)->first();
                    if ($role['name'] == 'student') {
                        $mdlUser->update(['redirect_type' => 'lms']);
                    } else {
                        $mdlUser->update(['redirect_type' => 'default']);
                    }
                    //Assign TMS
                    add_user_by_role($user_id, $role_id);
                    //Assign LMS
                    enrole_lms($user_id, $mdl_role_id, $tmsUser['confirm']);

                    /*$mdlRoleAssignment = MdlRoleAssignments::where([
                        'roleid'  => $mdl_role_id,
                        'userid'  => $user_id
                    ])->first();
                    if(!$mdlRoleAssignment){
                        $roleAssign = new MdlRoleAssignments;
                        $roleAssign->roleid = $mdl_role_id;
                        $roleAssign->userid = $user_id;
                        $roleAssign->save();
                    }

                    if($role['name'] == 'student'){
                        $mdlUser = MdlUser::findOrFail($user_id);
                        $mdlUser->redirect_type = 'lms';
                        $mdlUser->save();
                    }else{
                        $mdlUser = MdlUser::findOrFail($user_id);
                        $mdlUser->redirect_type = 'default';
                        $mdlUser->save();
                    }*/

                    $type = 'role';
                    $url = '/roles/edit/' . $role_id;
                    $action = 'add';
                    $info = 'Gán quyền  ' . $role['name'] . ' cho tài khoản' . $mdlUser['username'];
                    devcpt_log_system($type, $url, $action, $info);

                    //clear cache LMS roles
                    api_lms_clear_cache_enrolments($mdl_role_id, $user_id);

                    \DB::commit();
                }
                return response()->json(status_message('success', __('them_nguoi_dung_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListUserByRole(Request $request)
    {
        $role_id = $request->input('id');
        $param = [
            'id' => 'number',
            'keyword' => 'text',
            'row' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $data = DB::table('tms_user_detail as tud')
            ->select('tud.user_id', 'tud.fullname', 'tud.cmtnd', 'tud.email', 'tud.phone', 'mu.username')
            ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
            ->where('tud.deleted', 0)
            ->whereExists(function ($query) use ($role_id) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles as mhr')
                    ->where('mhr.role_id', $role_id)
                    ->whereRaw('mhr.model_id = tud.user_id');
            });
        if ($this->keyword) {
            $data = $data->where(function ($query) {
                $query->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.phone', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->orderBy('mu.username', 'ASC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $length = $data->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'length' => $length
        ];
        return response()->json($response);
    }

    public function apiRemoveUserRole(Request $request)
    {
        try {
            $role_id = $request->input('role_id');
            $user_id = $request->input('user_id');
            if (!is_numeric($role_id) || !is_numeric($user_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            $role = Role::select('mdl_role_id', 'name')->findOrFail($role_id);
            $mdl_role_id = $role['mdl_role_id'];
            if ($role_id && $user_id) {
                \DB::beginTransaction();
                //Remove user TMS
                ModelHasRole::where([
                    'role_id' => $role_id,
                    'model_id' => $user_id
                ])->delete();
                //Remove user LMS
                MdlRoleAssignments::where([
                    'roleid' => $mdl_role_id,
                    'userid' => $user_id
                ])->delete();

                if ($role['name'] == 'student') {
                    $mdlUser = MdlUser::findOrFail($user_id);
                    $mdlUser->description = '';
                    $mdlUser->save();
                } else if ($role['name'] == 'usermarket') {
                    //remove nhân viên giám sát thị trường
                    remove_user_market($user_id);
                }
                //Clear cache
                api_lms_clear_cache_enrolments($mdl_role_id, $user_id);

                $type = 'role';
                $url = '/roles/edit/' . $role_id;
                $action = 'remove';
                $info = 'Gỡ quyền  ' . $role['name'] . ' cho tài khoản' . MdlUser::findOrFail($user_id)['username'];
                devcpt_log_system($type, $url, $action, $info);
                \DB::commit();
                return response()->json(status_message('success', __('go_nguoi_dung_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListRoleOrganize(Request $request)
    {
        $role_id = $request->input('role_id');
        $row = $request->input('row');
        //$keyword    = $request->input('keyword');
        $data = TmsRoleOrganize::with('city', 'branch', 'saleroom')->where('role_id', $role_id);
        /*if($keyword){
            $data = $data->where('fullname','like',"%{$keyword}%");
            $data = $data->orWhere('cmtnd','like',"%{$keyword}%");
        }*/
        $data = $data->orderBy('created_at', 'DESC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            //'keyword' => $keyword
        ];
        return response()->json($response);
    }

    public function apiListOrganize(Request $request)
    {
        $row = $request->input('row');
        $role_id = $request->input('role_id');
        $keyword = $request->input('keyword');
        $searchType = $request->input('searchType');
        $select = TmsRoleOrganize::with('city_branch.branch_sale_room', 'branch_saleroom')->where('role_id', $role_id)->get()->toArray();
        if ($searchType == 'district') {
            $response = [
                'data' => [
                    1 => [
                        'id' => 'MB',
                        'code' => 'MB',
                        'name' => 'Miền Bắc'
                    ],
                    2 => [
                        'id' => 'MT',
                        'code' => 'MT',
                        'name' => 'Miền Trung'
                    ],
                    3 => [
                        'id' => 'MN',
                        'code' => 'MN',
                        'name' => 'Miền Nam'
                    ]
                ],
                'select' => $select
            ];
        } else {
            if ($searchType == 'city') {
                $data = TmsCity::where([
                    'deleted' => 0,
                    'parent' => 0
                ]);
            } elseif ($searchType == 'branch') {
                $data = TmsBranch::where([
                    'deleted' => 0,
                ]);
            } else {
                $data = TmsSaleRooms::where([
                    'deleted' => 0,
                ]);
            }

            if ($keyword) {
                $data = $data->where('name', 'like', "%{$keyword}%");
                $data = $data->orWhere('code', 'like', "%{$keyword}%");
            }
            $data = $data->orderBy('created_at', 'DESC');
            $data = $data->paginate($row);
            $total = ceil($data->total() / $row);
            $response = [
                'pagination' => [
                    'total' => $total,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
                'select' => $select
                //'keyword' => $keyword
            ];
        }

        return response()->json($response);
    }

    public function apiAddRoleOrganize(Request $request)
    {
        $organize_id = $request->input('organize_id');
        $role_id = $request->input('role_id');
        $type = $request->input('type');
        $roleOrganize = new TmsRoleOrganize;
        $roleOrganize->role_id = $role_id;
        if ($type == TmsRoleOrganize::ORGANIZETYPE_DT) {
            $roleOrganize->organize_id = 0;
            $roleOrganize->type = $organize_id;
        } else {
            $roleOrganize->organize_id = $organize_id;
            $roleOrganize->type = $type;
        }
        $roleOrganize->save();
        return 'success';
    }

    public function apiRemoveRoleOrganize(Request $request)
    {
        $id = $request->input('id');
        TmsRoleOrganize::findOrFail($id)->delete();
        return 'success';
    }
    // End RoleController

    // SaleRoomController
    public function apiListSaleRoomByBranchSaleroom(Request $request)
    {
        $branch_id = $request->input('id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');

        $validates = validate_fails($request, [
            'id' => 'number',
            'keyword' => 'text',
            'row' => 'number',
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            $sale_room_id = TmsBranchSaleRoom::where('branch_id', $branch_id)->pluck('sale_room_id');
            $data = TmsSaleRooms::with('saleroom_user', 'user.detail')->whereIn('id', $sale_room_id);
            if ($keyword) {
                $data = $data->where('name', 'like', "%{$keyword}%");
            }
            $data = $data->orderBy('created_at', 'DESC');
            $data = $data->paginate($row);
            $total = ceil($data->total() / $row);
            $response = [
                'pagination' => [
                    'total' => $total,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
                'keyword' => $keyword
            ];
            return response()->json($response);
        }
    }
    // End SaleRoomController

    // SaleRoomUserController
    public function apiListUsers(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $working_status = $request->input('working_status');
        $pos = $request->input('pos');

        $validates = validate_fails($request, [
            'keyword' => 'text',
            'row' => 'number',
            'working_status' => 'number',
            'pos' => 'number'
        ]);
        if (!empty($validates)) {
            return response()->json([]);
        } else {
            $user_id = \Auth::user()->id;

            //get user by id
            $listUsers = DB::table('tms_user_detail as tud')
                ->select('tud.fullname', 'tud.email', 'mu.username', 'tud.user_id', 'tud.cmtnd', 'tud.confirm', 'tud.phone', 'tsr.name as posname')
                ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
                ->join('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'tud.user_id')
                ->join('tms_sale_rooms as tsr', 'tsr.id', '=', 'tsru.sale_room_id')
                ->where('tsr.user_id', '=', $user_id)
                ->where('tsru.type', '=', TmsSaleRoomUser::POS)
                ->where('tud.deleted', '=', 0);

            if (strlen($working_status) != 0) {
                $listUsers->where('tud.working_status', $working_status);
            }
            if ($pos != 0) {
                $listUsers->where('tsr.id', $pos);
            }

            if ($keyword) {
                $listUsers = $listUsers->where(function ($query) use ($keyword) {
                    $query->orWhere('tud.fullname', 'like', "%{$keyword}%")
                        ->orWhere('tud.email', 'like', "%{$keyword}%")
                        ->orWhere('tud.cmtnd', 'like', "%{$keyword}%")
                        ->orWhere('tud.phone', 'like', "%{$keyword}%")
                        ->orWhere('mu.username', 'like', "%{$keyword}%");
                });
            }
            $listUsers = $listUsers->orderBy('tsr.name', 'ASC');
            $listUsers = $listUsers->paginate($row);
            $total = ceil($listUsers->total() / $row);
            $response = [
                'pagination' => [
                    'total' => $total,
                    'current_page' => $listUsers->currentPage(),
                ],
                'data' => $listUsers,
                'keyword' => $keyword
            ];
            return response()->json($response);
        }
    }

    public function apiListPos()
    {
        $user_id = \Auth::user()->id;
        $data = DB::table('tms_sale_rooms as tsr')
            ->select('tsr.id', 'tsr.name')
            ->where('tsr.user_id', '=', $user_id)
            ->get()->toArray();
        return response()->json($data);
    }
    // End SaleRoomUserController

    // StudentController
    //api to get students achieve certificate
    public function apiListStudentsUncertificate(Request $request)
    {
        //get value of request
        $row = $request->input('row');
        $keyword = $request->input('keyword');

        $validates = validate_fails($request, [
            'row' => 'number',
            'keyword' => 'text',
        ]);
        if (!empty($validates)) {
            return response()->json([]);
        }
        //get number of course required
        // $course_count = MdlCourseCategory::where('id', 3)->get()->pluck('coursecount');

        //get list id students get certificate by when number course lager course count above
        $listIdStudentsDone = DB::table('course_final')->get()->pluck('userid');
        //        $listIdStudentsHaveFinal = DB::table('course_completion')
        //            ->where('iscoursefinal', '=', 1)
        //            ->get()->pluck('userid');


        //get info of students by id students above
        $listStudentsDone = DB::table('tms_user_detail')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->leftJoin('student_certificate', 'tms_user_detail.user_id', '=', 'student_certificate.userid')
            ->select('tms_user_detail.user_id', 'tms_user_detail.fullname as fullname', 'tms_user_detail.email as email', 'mdl_user.username as username', 'tms_user_detail.user_id as user_id', 'tms_user_detail.cmtnd as cmtnd', 'tms_user_detail.confirm as confirm', 'tms_user_detail.phone as phone', 'student_certificate.status as status', 'student_certificate.code as code', 'student_certificate.timecertificate as timecertificate')
            ->where('student_certificate.userid', '=', null)
            ->whereIn('tms_user_detail.user_id', $listIdStudentsDone);

        //search
        if (strlen($keyword) != 0) {
            $listStudentsDone->where(function ($query) use ($keyword) {
                $query->orWhere('tms_user_detail.fullname', 'like', "%{$keyword}%")
                    ->orWhere('tms_user_detail.email', 'like', "%{$keyword}%")
                    ->orWhere('tms_user_detail.cmtnd', 'like', "%{$keyword}%")
                    ->orWhere('tms_user_detail.phone', 'like', "%{$keyword}%")
                    ->orWhere('student_certificate.code', 'like', "%{$keyword}%")
                    ->orWhere('mdl_user.username', 'like', "%{$keyword}%");
            });
        }

        //paging
        $listStudentsDone = $listStudentsDone->paginate($row);
        $total = ceil($listStudentsDone->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listStudentsDone->currentPage(),
            ],
            'data' => $listStudentsDone,
        ];
        return response()->json($response);
    }

    //generate code for multiple user
    public function apiGenerateSelectedUser(Request $request)
    {
        try {
            $user_selected = $request->input('user_selected');
            \DB::beginTransaction();

            $arr_data = [];
            $data_item = [];

            foreach ($user_selected as $user_id) {
                $student = StudentCertificate::where('userid', $user_id)->first();
                //nếu học viên đã có mã thì không làm gì cả
                if (!$student) {
                    //check role: if role managemarket => dont generate code
                    $checkRole = tvHasRole($user_id, 'managemarket');
                    if (!$checkRole) {
                        //update status to 1
                        $certificatecode = $user_id . $this->randomNumber(7 - strlen($user_id));
                        //                        StudentCertificate::create([
                        //                            'userid' => $user_id,
                        //                            'code' => $certificatecode,
                        //                            'status' => 1,
                        //                            'timecertificate' => time()
                        //                        ]);

                        $data_item['userid'] = $user_id;
                        $data_item['code'] = $certificatecode;
                        $data_item['status'] = 1;
                        $data_item['timecertificate'] = time();

                        array_push($arr_data, $data_item);
                    }
                }
                usleep(100); //sleep tranh tinh trang query db lien tiep
            }

            StudentCertificate::insert($arr_data);

            \DB::commit();
            return 'success';
        } catch (\Exception  $e) {
            return 'error';
        }
    }


    //gen mã chứng chỉ
    public function generateCodeCertificate(Request $request)
    {
        $response = new ResponseModel();
        try {
            $user_id = $request->input('user_id');

            $validates = validate_fails($request, [
                'user_id' => 'number',
            ]);
            if (!empty($validates)) {
                //var_dump($validates);
            } else {
                $student = StudentCertificate::where('userid', $user_id)->first();
                //check role: if role managemarket => dont generate code
                $checkRole = tvHasRole($user_id, 'managemarket');
                if (!$checkRole) {
                    $response_text = "";
                    \DB::beginTransaction();
                    //set value for default: 3 is not exists in table student_certificate
                    $check = 3;
                    if ($student) {
                        //dont has certificate code
                        if ($student->code == '') {
                            $student->code = $user_id . $this->randomNumber(7 - strlen($user_id));
                            $student->save();
                            $check = 0;
                        } else if ($student->code != '' && $student->status == 0) {
                            //create certificate image
                            $student->status = 1;
                            $student->save();
                            $check = 3;
                        } else if ($student->code != '' && $student->status == 1)
                            $check = 1;
                    } else {
                        //create certificate code
                        $certificatecode = $user_id . $this->randomNumber(7 - strlen($user_id));
                        StudentCertificate::create([
                            'userid' => $user_id,
                            'code' => $certificatecode,
                            'status' => 0,
                            'timecertificate' => time()
                        ]);
                        $get_user = $listCourses = DB::table('tms_user_detail')
                            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id')
                            ->select('tms_user_detail.user_id', 'tms_user_detail.fullname as fullname', 'tms_user_detail.email as email', 'mdl_user.username as username')
                            ->where('mdl_user.id', '=', $user_id)->first();
                        $this->sendMail($get_user, $certificatecode);
                        $check = 0;
                    }
                    \DB::commit();

                    if ($check == 0) {
                        $response_text = __('cap_ma_thanh_cong');
                    } else if ($check == 3) {
                        //                    $msg = $this->autoGenCertificate($user_id);
                        //                    $response->msg = $msg;
                        $response_text = __('yeu_cau_cap_chung_chi_thanh_cong');
                    } else if ($check == 1) {
                        $response_text = __('dang_doi_cap_chung_chi');
                    }
                    $response->status = true;
                    $response->message = $response_text;
                } else {
                    $response->status = false;
                    $response->message = __('chuyen_vien_kinh_doanh_khong_duoc_cap_chung_chi');
                }
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function randomNumber($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    //Lấy danh sách những học viên được cấp chứng chỉ dựa vào status = 2
    public function apiListStudentsCertificate(Request $request)
    {
        //get value of request
        $row = $request->input('row');
        $keyword = $request->input('keyword');

        $validates = validate_fails($request, [
            'row' => 'number',
            'keyword' => 'text',
        ]);

        if (!empty($validates)) {
            return response()->json();
        }

        //        //get list id students in table student_certificate
        //        $listIdStudentsDone = DB::table('student_certificate')
        ////            ->select('userid')
        ////            ->where('status', '=', 2)
        //            ->get()->pluck('userid');
        //
        //        //get info of students by id students above
        //        $listStudentsDone = DB::table('tms_user_detail')
        //            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id')
        //            ->leftJoin('student_certificate', 'tms_user_detail.user_id', '=', 'student_certificate.userid')
        //            ->select('tms_user_detail.user_id', 'tms_user_detail.fullname as fullname', 'tms_user_detail.email as email', 'mdl_user.username as username', 'tms_user_detail.user_id as user_id', 'tms_user_detail.cmtnd as cmtnd', 'tms_user_detail.confirm as confirm', 'tms_user_detail.phone as phone', 'student_certificate.status as status', 'student_certificate.code as code', 'student_certificate.timecertificate as timecertificate')
        //            ->whereIn('tms_user_detail.user_id', $listIdStudentsDone);

        //fix query of DatDT
        $listStudentsDone = DB::table('tms_user_detail as tud')
            ->join('mdl_user as u', 'u.id', '=', 'tud.user_id')
            ->join('student_certificate as sc', 'tud.user_id', '=', 'sc.userid')
            ->select(
                'u.id as user_id',
                'tud.fullname as fullname',
                'tud.email as email',
                'u.username as username',
                'tud.cmtnd as cmtnd',
                'tud.confirm as confirm',
                'tud.phone as phone',
                'sc.status as status',
                'sc.code as code',
                'sc.timecertificate as timecertificate'
            );

        //search
        if (strlen($keyword) != 0) {
            $listStudentsDone->where(function ($query) use ($keyword) {
                $query->orWhere('tud.fullname', 'like', "%{$keyword}%")
                    ->orWhere('tud.email', 'like', "%{$keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$keyword}%")
                    ->orWhere('tud.phone', 'like', "%{$keyword}%")
                    ->orWhere('u.username', 'like', "%{$keyword}%");
            });
        }

        $listStudentsDone = $listStudentsDone->groupBy('u.id');

        //paging
        $listStudentsDone = $listStudentsDone->paginate($row);
        $total = ceil($listStudentsDone->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listStudentsDone->currentPage(),
            ],
            'data' => $listStudentsDone,
        ];
        return response()->json($response);
    }


    //thêm vào table TmsNotification để tự động cron và gửi
    public function sendMail($user, $certificatecode)
    {
        $data = array();
        $data[] = array(
            'type' => TmsNotification::MAIL,
            'target' => TmsNotification::REMIND_CERTIFICATE,
            'status_send' => 0,
            'sendto' => $user->user_id,
            'createdby' => 0,
            'course_id' => 0,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
            'content' => $certificatecode
        );
        $mail = TmsNotification::where([
            ['type', '=', TmsNotification::MAIL],
            ['target', '=', TmsNotification::REMIND_CERTIFICATE],
            ['status_send', '=', 0],
            ['sendto', '=', $user->user_id]
        ])->first();
        try {
            if ($mail) {
                $mail->content = $certificatecode;
                $mail->save();
            } else {
                TmsNotification::insert($data);
            }
        } catch (\Exception $e) {
        }
    }


    public function settingCertificate()
    {
        return view('education.setting_certificate');
    }

    public function apiGetListImagesCertificate()
    {
        $response = DB::table('image_certificate')
            ->get();
        return response()->json($response);
    }

    //tạo ảnh chứng chỉ
    public function apiCreateCertificate(Request $request)
    {
        $response = new ResponseModel();
        try {
            $avatar = $request->file('file');
            $name = $request->input('name');
            $description = $request->input('description');
            $is_active = $request->input('is_active');

            $validates = validate_fails($request, [
                'name' => 'text',
                'description' => 'longtext',
                'is_active' => 'number',
            ]);
            if (!empty($validates)) {
                //var_dump($validates);
            } else {
                //thực hiện insert dữ liệu
                $path_avatar = '';
                if ($avatar) {
                    $name_avatar = time() . '.' . $avatar->getClientOriginalExtension();
                    //                    $destinationPath = public_path('/upload/certificate/');
                    //                    $avatar->move($destinationPath, $name_avatar);

                    // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                    Storage::putFileAs(
                        'public/upload/certificate',
                        $avatar,
                        $name_avatar
                    );

                    $path_avatar = '/storage/upload/certificate/' . $name_avatar;

                    //                    $path_avatar = 'upload/certificate/' . $name_avatar;
                } else {
                    $path_avatar = '/storage/upload/certificate/default_certificate.png';
                }

                \DB::beginTransaction();
                if ($is_active == 1) {
                    DB::table('image_certificate')
                        ->where('is_active', 1)
                        ->update(['is_active' => 0]);
                }
                ImageCertificate::create([
                    'path' => $path_avatar,
                    'name' => $name,
                    'description' => $description,
                    'is_active' => $is_active,
                ]);
                \DB::commit();

                $response->status = true;
                $response->message = __('tao_mau_chung_chi_thanh_cong');
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //xóa chứng chỉ
    public function apiDeleteStudent($id)
    {
        try {
            //biến check: 1 - có thể xóa, 0 - không thể xóa
            $check = 1;
            \DB::beginTransaction();
            //tìm chứng chỉ dựa vào id
            $deletedRow = ImageCertificate::find($id);
            //nếu tồn tại chứng chỉ và chứng chỉ đó đang được active thì không xóa được
            if ($deletedRow && $deletedRow->is_active == 1) {
                return 'exists';
            }
            $deletedRow->delete();
            \DB::commit();
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    //Chi tiết ảnh chứng chỉ
    public function apiDetailCertificate(Request $request)
    {
        $id = $request->input('id');

        $validates = validate_fails($request, [
            'id' => 'number'
        ]);
        if (!empty($validates)) {
            return response()->json();
        }
        $certificate_info = DB::table('image_certificate')
            ->where('image_certificate.id', '=', $id)->first();
        return response()->json($certificate_info);
    }


    //update ảnh chứng chỉ
    public function apiUpdateCertificate(Request $request)
    {
        $response = new ResponseModel();
        try {
            \DB::beginTransaction();
            $avatar = $request->file('file');
            $id = $request->input('id');
            $name = $request->input('name');
            $description = $request->input('description');
            $is_active = $request->input('is_active');

            $validates = validate_fails($request, [
                'id' => 'number',
                'name' => 'text',
                'description' => 'longtext',
                'is_active' => 'number'
            ]);
            if (!empty($validates)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }
            //thực hiện update dữ liệu
            $cer = ImageCertificate::where('id', $id)->first();
            $cer->name = $name;
            $cer->description = $description;
            $cer->is_active = $is_active;

            if ($avatar) {
                $name_avatar = time() . '.' . $avatar->getClientOriginalExtension();
                //                    $destinationPath = public_path('/upload/certificate/');
                //                    $avatar->move($destinationPath, $name_avatar);

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/certificate',
                    $avatar,
                    $name_avatar
                );

                $path_avatar = '/storage/upload/certificate/' . $name_avatar;

                //                    $path_avatar = 'upload/certificate/' . $name_avatar;
                $cer->path = $path_avatar;
            }

            if ($is_active == 0) {
                $get_active = DB::table('image_certificate')
                    ->where('is_active', 1)
                    ->pluck('id')->toArray();
                if (count($get_active) == 1 && in_array($id, $get_active)) {
                    $response->status = false;
                    $response->message = __('hay_chon_mau_chung_chi_nay_la_mac_dinh_vi_chua_co_mau_mac_dinh');
                    return response()->json($response);
                }
            }
            $cer->save();
            \DB::commit();
            $response->status = true;
            $response->message = __('cap_nhat_mau_chung_chi_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }


    public function apiAutoGenCertificate()
    {
        $responseModel = new ResponseModel();
        try {
            //            $file_path = public_path('certificate.png');
            //
            //            $py_path = public_path('generate.py');

            $file_path = storage_path('app/public/python/certificate.png');
            $py_path = storage_path('app/public/python/generate.py');

            $command_str = 'python ' . $py_path . ' ' . $file_path . ' ' . 'u:1 w:0 c:0';
            $command = escapeshellcmd($command_str);
            $output = shell_exec($command);
            $responseModel->link = $command_str;
            $responseModel->otherData = json_encode($output);
            $responseModel->status = true;
            $responseModel->message = 'success';
        } catch (\Exception $e) {
            $responseModel->status = false;
            $responseModel->message = $e->getMessage();
        }
        return response()->json($responseModel);
    }

    //tự động gen chứng chỉ: chèn tên và mã chứng chỉ vào chứng chỉ
    public function autoGenCertificate($user_id)
    {
        try {
            $file_path = storage_path('app/public/python/certificate.png');
            $py_path = storage_path('app/public/python/generate.py');
            $command_str = 'python ' . $py_path . ' ' . $file_path . ' ' . $user_id . ' u:1 w:0 c:0';
            $command = escapeshellcmd($command_str);
            $output = shell_exec($command);
            $message = json_encode($output);
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        return response()->json($message);
    }
    // End StudentController

    // EducationController
    public function apiListUserTeacher(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $user_id = $request->input('user');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $role = Role::select('id')->where('name', 'teacher')->first();
        $userArray = ModelHasRole::where('role_id', $role['id'])->pluck('model_id');
        $listUsers = DB::table('tms_user_detail')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id');
        $listUsers = $listUsers->select('tms_user_detail.fullname as fullname', 'tms_user_detail.email as email', 'mdl_user.username as username', 'tms_user_detail.user_id as user_id', 'tms_user_detail.cmtnd as cmtnd')
            ->where('tms_user_detail.deleted', 0)
            ->whereIn('tms_user_detail.user_id', $userArray);

        if ($user_id) {
            $listUsers->where('mdl_user.id', $user_id);
        }
        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tms_user_detail.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mdl_user.username', 'like', "%{$this->keyword}%");
            });
        }
        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $total_user = $listUsers->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }

    public function apiListUserStudent(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $confirm = $request->input('confirm');
        $user_id = $request->input('user');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'confirm' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $role = Role::select('id')->where('name', 'student')->first();
        $userArray = ModelHasRole::where('role_id', $role['id'])->pluck('model_id');
        $listUsers = DB::table('tms_user_detail')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id');
        $listUsers = $listUsers->select('tms_user_detail.fullname as fullname', 'tms_user_detail.email as email', 'mdl_user.username as username', 'tms_user_detail.user_id as user_id', 'tms_user_detail.cmtnd as cmtnd', 'tms_user_detail.confirm as confirm')
            ->where('tms_user_detail.deleted', 0)
            ->whereIn('tms_user_detail.user_id', $userArray);

        if ($user_id) {
            $listUsers->where('mdl_user.id', $user_id);
        }
        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tms_user_detail.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mdl_user.username', 'like', "%{$this->keyword}%");
            });
        }
        if ($confirm != '') {
            $listUsers = $listUsers->where('tms_user_detail.confirm', '=', $confirm);
        }
        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $total_user = $listUsers->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }

    public function apiListUserTeacherTrash(Request $request)
    {
        //$paged = $request->input('paged');
        $role = Role::select('id')->where('name', 'teacher')->first();
        $userArray = ModelHasRole::where('role_id', $role['id'])->pluck('model_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $listUsers = TmsUserDetail::with('user')
            ->where('deleted', 1)
            ->whereIn('user_id', $userArray);
        if ($keyword) {
            $listUsers = $listUsers->where('fullname', 'like', "%{$keyword}%");
        }
        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }

    public function apiListUserStudentTrash(Request $request)
    {
        //$paged = $request->input('paged');
        $role = Role::select('id')->where('name', 'student')->first();
        $userArray = ModelHasRole::where('role_id', $role['id'])->pluck('model_id');
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $listUsers = TmsUserDetail::with('user')
            ->where('deleted', 1)
            ->whereIn('user_id', $userArray);
        if ($keyword) {
            $listUsers = $listUsers->where('fullname', 'like', "%{$keyword}%");
        }
        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }
    // End EducationController

    // SurveyController

    //api lấy danh sách survey
    //ThoLD (21/09/2019)
    public function apiGetListSurvey(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstSurvey = TmsSurvey::query();
        $lstSurvey = $lstSurvey->where('isdeleted', '=', 0);

        if ($keyword) {
            $lstSurvey = $lstSurvey->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            });
        }

        if ($startdate) {
            $cv_startDate = strtotime($startdate);
            $lstSurvey = $lstSurvey->where('startdate', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate);
            $lstSurvey = $lstSurvey->where('enddate', '<=', $cv_endDate);
        }

        $lstSurvey = $lstSurvey->orderBy('id', 'desc');

        $lstSurvey = $lstSurvey->paginate($row);
        $total = ceil($lstSurvey->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstSurvey->currentPage(),
            ],
            'data' => $lstSurvey
        ];

        return response()->json($response);
    }

    //api tạo mới khóa học
    //ThoLD (21/08/2019)
    public function apiCreateSurvey(Request $request)
    {
        $response = new ResponseModel();
        try {
            $sur_code = $request->input('sur_code');
            $sur_name = $request->input('sur_name');
            $startdate = $request->input('startdate');
            $enddate = $request->input('enddate');
            $description = $request->input('description');

            $param = [
                'sur_code' => 'code',
                'sur_name' => 'text',
                'description' => 'longtext'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            //Check survey code
            $checkSurvey = TmsSurvey::select('id')->where('code', $sur_code)->where('isdeleted', 0)->first();

            if ($checkSurvey) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }

            $survey = new TmsSurvey();
            $survey->code = $sur_code;
            $survey->name = $sur_name;
            $survey->startdate = strtotime($startdate);
            $survey->enddate = strtotime($enddate);
            $survey->description = $description;
            $survey->isdeleted = 0;
            $survey->save();

            $response->otherData = $survey->id;
            $response->status = true;
            $response->message = __('tao_moi_survey_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //ThoLD (21/08/2019)
    public function apiGetDetailSurvey($id)
    {
        $survey = TmsSurvey::findOrFail($id);
        return json_encode($survey);
    }

    //api tạo mới khóa học
    //ThoLD (21/08/2019)
    public function apiEditSurvey($id, Request $request)
    {
        $response = new ResponseModel();
        try {
            $sur_code = $request->input('sur_code');
            $sur_name = $request->input('sur_name');
            $startdate = $request->input('startdate');
            $enddate = $request->input('enddate');
            $description = $request->input('description');

            $param = [
                'sur_code' => 'code',
                'sur_name' => 'text',
                'description' => 'longtext'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            //Check course code
            $checkCourse = TmsSurvey::select('id')->whereNotIn('id', [$survey->id])->where('code', $sur_code)->where('isdeleted', 0)->first();

            if ($checkCourse) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }


            $survey->code = $sur_code;
            $survey->name = $sur_name;
            $survey->startdate = strtotime($startdate);
            $survey->enddate = strtotime($enddate);

            $survey->description = $description;
            $survey->save();

            $response->status = true;
            $response->message = __('sua_survey_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //api tạo mới khóa học
    //ThoLD (21/08/2019)
    public function apiDeleteSurvey(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('survey_id');

            $param = [
                'survey_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $survey->isdeleted = 1;
            $survey->save();
            devcpt_log_system(TmsLog::TYPE_SURVEY, '/survey/detail/' . $survey->id, 'delete', 'Xóa khảo sát ' . $survey->code);
            //xử lý xóa tất cả các câu hỏi thuộc survey
            TmsQuestion::where('survey_id', '=', $survey->id)->update(['isdeleted' => 1]);

            $response->status = true;
            $response->message = __('xoa_survey');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //api lấy danh sách survey
    //ThoLD (21/09/2019)
    public function apiGetListSurveyRestore(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'keyword' => 'text',
            'row' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $lstSurvey = TmsSurvey::query();
        $lstSurvey = $lstSurvey->where('isdeleted', '=', 1);

        if ($keyword) {
            $lstSurvey = $lstSurvey->where('name', 'like', '%' . $keyword . '%');
            $lstSurvey = $lstSurvey->orWhere('code', 'like', '%' . $keyword . '%');
        }

        if ($startdate) {
            $cv_startDate = strtotime($startdate);
            $lstSurvey = $lstSurvey->where('startdate', '>=', $cv_startDate);
        }

        if ($enddate) {
            $cv_endDate = strtotime($enddate);
            $lstSurvey = $lstSurvey->where('enddate', '<=', $cv_endDate);
        }

        $lstSurvey = $lstSurvey->orderBy('id', 'desc');

        $lstSurvey = $lstSurvey->paginate($row);
        $total = ceil($lstSurvey->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $lstSurvey->currentPage(),
            ],
            'data' => $lstSurvey
        ];

        return response()->json($response);
    }

    //api tạo mới khóa học
    //ThoLD (21/08/2019)
    public function apiRestoreSurvey(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('survey_id');

            $param = [
                'survey_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            //Check course code
            $checkCourse = TmsSurvey::select('id')->whereNotIn('id', [$survey->id])->where('code', $survey->code)->where('isdeleted', 0)->first();

            if ($checkCourse) {
                $response->status = false;
                $response->message = __('ma_survey_da_ton_tai');
                return response()->json($response);
            }

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $survey->isdeleted = 0;
            $survey->save();

            //xử lý mở lại các câu hỏi thuộc survey
            TmsQuestion::where('survey_id', '=', $survey->id)->update(['isdeleted' => 0]);

            devcpt_log_system(TmsLog::TYPE_SURVEY, '/survey/detail/' . $survey->id, 'restore', 'Khôi phục khảo sát ' . $survey->code);

            $response->status = true;
            $response->message = __('khoi_phuc_survey_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    //api tạo mới khóa học
    //ThoLD (21/08/2019)
    public function apiDeleteSurveyRestore(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('survey_id');

            $param = [
                'survey_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }
            devcpt_log_system(TmsLog::TYPE_SURVEY, '/survey/detail/' . $survey->id, 'delete', 'Xóa hoàn toàn khảo sát ' . $survey->code);
            //xử lý xóa tất cả các câu hỏi thuộc survey
            TmsQuestion::where('survey_id', '=', $survey->id)->delete();

            $survey->delete();

            $response->status = true;
            $response->message = __('xoa_survey');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    #endregion


    #region question


    public function apiGetListQuestion(Request $request)
    {
        $keyword = $request->input('keyword');
        $row = $request->input('row');
        $type_question = $request->input('type_question');
        $survey_id = $request->input('survey_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'survey_id' => 'number',
            'type_question' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }


        $listQuestions = DB::table('tms_questions as tq')
            ->join('tms_surveys as ts', 'ts.id', '=', 'tq.survey_id')
            ->where('tq.isdeleted', '=', 0)
            ->select(
                'tq.id',
                'tq.name',
                'tq.type_question',
                'ts.name as survey_name',
                'ts.code as survey_code'
            );

        if ($survey_id) {
            $listQuestions = $listQuestions->where('tq.survey_id', '=', $survey_id);
        }
        if ($type_question) {
            $listQuestions = $listQuestions->where('tq.type_question', '=', $type_question);
        }

        if ($keyword) {
            $listQuestions = $listQuestions->where('tq.name', 'like', '%' . $keyword . '%');
        }

        $listQuestions = $listQuestions->orderBy('id', 'desc');

        $listQuestions = $listQuestions->paginate($row);
        $total = ceil($listQuestions->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listQuestions->currentPage(),
            ],
            'data' => $listQuestions
        ];

        return response()->json($response);
    }

    //api lấy danh sách survey
    //ThoLD (29/09/2019)
    public function apiGetListSurveyQuestion()
    {
        $lstSurvey = TmsSurvey::query();
        $lstSurvey = $lstSurvey->where('isdeleted', '=', 0);
        $lstSurvey = $lstSurvey->select('id', 'name', 'code');
        $lstSurvey = $lstSurvey->orderBy('id', 'desc')->get();

        return response()->json($lstSurvey);
    }


    public function apiCreateQuestion(Request $request)
    {
        $response = new ResponseModel();
        try {
            $survey_id = $request->input('survey_id');
            $type_question = $request->input('type_question');
            $question_name = $request->input('question_name');
            $question_content = $request->input('question_content');
            $anwsers = $request->input('anwsers');
            $question_childs = $request->input('question_childs');

            $param = [
                'question_content' => 'longtext',
                'question_name' => 'text',
                'survey_id' => 'number',
                'type_question' => 'text'
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }


            $count_ans = 0;
            if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE || $type_question == \App\TmsQuestion::GROUP) {
                $count_ans = count($anwsers);
                if ($count_ans == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_tra_loi');
                    return response()->json($response);
                }
            }

            $count_ques_child = 0;
            if ($type_question == \App\TmsQuestion::GROUP) {
                $count_ques_child = count($question_childs);
                if ($count_ques_child == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_hoi_con');
                    return response()->json($response);
                }
            }

            \DB::beginTransaction();
            $tms_question = new TmsQuestion();
            $tms_question->survey_id = $survey_id;
            $tms_question->type_question = $type_question;
            $tms_question->display = 1;

            $tms_question->name = $question_name;
            $tms_question->content = $question_content;
            $tms_question->created_by = Auth::user()->id;
            $tms_question->status = 1;
            $tms_question->total_answer = count($anwsers);
            $tms_question->isdeleted = 0;
            $tms_question->save();


            if ($type_question == \App\TmsQuestion::GROUP) {

                for ($m = 0; $m < $count_ques_child; $m++) {
                    $tms_ques_data = new TmsQuestionData();
                    $tms_ques_data->question_id = $tms_question->id;
                    $tms_ques_data->content = $question_childs[$m]['content'];
                    $tms_ques_data->created_by = Auth::user()->id;
                    $tms_ques_data->status = 1;
                    $tms_ques_data->type_question = \App\TmsQuestion::MULTIPLE_CHOICE;
                    $tms_ques_data->save();

                    for ($i = 0; $i < $count_ans; $i++) {
                        if (!empty($anwsers[$i]['content'])) {

                            $tms_question_ans = new TmsQuestionAnswer();
                            $tms_question_ans->content = $anwsers[$i]['content'];
                            $tms_question_ans->question_id = $tms_ques_data->id;
                            $tms_question_ans->save();
                        }

                        sleep(0.01);
                    }
                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE) { //insert dap an trong TH la cau hoi chon dap an

                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::MULTIPLE_CHOICE;
                $tms_ques_data->save();

                for ($i = 0; $i < $count_ans; $i++) {

                    if (!empty($anwsers[$i]['content'])) {
                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $anwsers[$i]['content'];
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();
                    }

                    sleep(0.01);
                }
            } else {
                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::FILL_TEXT;
                $tms_ques_data->save();
            }


            \DB::commit();

            $response->status = true;
            $response->message = __('them_moi_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }


    public function apiGetDetailQuestion($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $question = TmsQuestion::findOrFail($id);
        return response()->json($question);
    }

    public function apiGetListAnswerQuestion($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $questionData = TmsQuestionData::query();
        $questionData = $questionData->where('question_id', $id)->select('id')->first();

        $lstAnswer = TmsQuestionAnswer::query();
        $lstAnswer = $lstAnswer->where('question_id', $questionData->id)->select('content')->get();

        return response()->json($lstAnswer);
    }

    public function apiGetListQuestionChild($id)
    {
        $id = is_numeric($id) ? $id : 0;

        $questionData = TmsQuestionData::query();
        $questionData = $questionData->where('question_id', $id)->select('id', 'question_id', 'content')->get();

        return response()->json($questionData);
    }


    public function apiUpdateQuestion($id, Request $request)
    {
        $response = new ResponseModel();
        try {
            $survey_id = $request->input('survey_id');
            $type_question = $request->input('type_question');

            $question_name = $request->input('question_name');
            $question_content = $request->input('question_content');
            $anwsers = $request->input('anwsers');
            $question_childs = $request->input('question_childs');

            $param = [
                'question_content' => 'longtext',
                'question_name' => 'text',
                'survey_id' => 'number',
                'type_question' => 'text'
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $count_ans = 0;
            if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE || $type_question == \App\TmsQuestion::GROUP) {
                $count_ans = count($anwsers);
                if ($count_ans == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_tra_loi');
                    return response()->json($response);
                }
            }

            $count_ques_child = 0;
            if ($type_question == \App\TmsQuestion::GROUP) {
                $count_ques_child = count($question_childs);
                if ($count_ques_child == 0) {
                    $response->status = false;
                    $response->message = __('ban_chua_nhap_cau_hoi_con');
                    return response()->json($response);
                }
            }

            \DB::beginTransaction();
            $tms_question = TmsQuestion::findOrFail($id);

            if (!$tms_question) {
                $response->status = false;
                $response->message = __('khong_tim_thay_cau_hoi');
                return response()->json($response);
            }

            $tms_question->survey_id = $survey_id;
            $tms_question->type_question = $type_question;

            $tms_question->name = $question_name;
            $tms_question->content = $question_content;
            $tms_question->created_by = Auth::user()->id;
            $tms_question->status = 1;
            $tms_question->total_answer = $count_ans;
            $tms_question->isdeleted = 0;
            $tms_question->save();


            if ($type_question == \App\TmsQuestion::GROUP) {

                TmsQuestionData::where('question_id', $id)->delete();

                for ($m = 0; $m < $count_ques_child; $m++) {
                    $tms_ques_data = new TmsQuestionData();
                    $tms_ques_data->question_id = $tms_question->id;
                    $tms_ques_data->content = $question_childs[$m]['content'];
                    $tms_ques_data->created_by = Auth::user()->id;
                    $tms_ques_data->status = 1;
                    $tms_ques_data->type_question = \App\TmsQuestion::MULTIPLE_CHOICE;
                    $tms_ques_data->save();

                    for ($i = 0; $i < $count_ans; $i++) {
                        if (!empty($anwsers[$i]['content'])) {

                            $tms_question_ans = new TmsQuestionAnswer();
                            $tms_question_ans->content = $anwsers[$i]['content'];
                            $tms_question_ans->question_id = $tms_ques_data->id;
                            $tms_question_ans->save();
                        }

                        sleep(0.01);
                    }
                    sleep(0.01);
                }
            } else if ($type_question == \App\TmsQuestion::MULTIPLE_CHOICE) { //insert dap an trong TH la cau hoi chon dap an

                $questionData = TmsQuestionData::query();
                $questionData = $questionData->where('question_id', $id)->select('id')->first();

                //xóa tất cả các đáp án và insert lại
                TmsQuestionAnswer::where('question_id', $questionData->id)->delete();
                TmsQuestionData::where('question_id', $id)->delete();

                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::MULTIPLE_CHOICE;
                $tms_ques_data->save();

                for ($i = 0; $i < $count_ans; $i++) {
                    if (!empty($anwsers[$i]['content'])) {
                        $tms_question_ans = new TmsQuestionAnswer();
                        $tms_question_ans->content = $anwsers[$i]['content'];
                        $tms_question_ans->question_id = $tms_ques_data->id;
                        $tms_question_ans->save();
                    }

                    sleep(0.01);
                }
            } else {
                TmsQuestionData::where('question_id', $id)->delete();
                $tms_ques_data = new TmsQuestionData();
                $tms_ques_data->question_id = $tms_question->id;
                $tms_ques_data->content = $question_content;
                $tms_ques_data->created_by = Auth::user()->id;
                $tms_ques_data->status = 1;
                $tms_ques_data->type_question = \App\TmsQuestion::FILL_TEXT;
                $tms_ques_data->save();
            }

            \DB::commit();

            $response->status = true;
            $response->message = __('sua_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }


    public function apiDeleteQuestion(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('question_id');

            $param = [
                'question_id' => 'number'
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            \DB::beginTransaction();
            $tms_question = TmsQuestion::findOrFail($id);

            if (!$tms_question) {
                $response->status = false;
                $response->message = __('khong_tim_thay_cau_hoi');
                return response()->json($response);
            }

            if ($tms_question->type_question == 'multiplechoice') { //insert dap an trong TH la cau hoi chon dap an
                //xóa tất cả các đáp án
                TmsQuestionAnswer::where('question_id', $id)->delete();
            }

            $tms_question->delete();

            \DB::commit();

            $response->status = true;
            $response->message = __('xoa_cau_hoi_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }
    #endregion


    //api lay thong tin survey va cau hoi trong survey
    //ThoLd (03/10/2019)
    public function apiPresentSurvey($id)
    {
        if (!is_numeric($id)) {
            return response()->json([]);
        }

        $dataSurvey = TmsSurvey::with(['questions', 'questions.question_data', 'questions.question_data.answers'])->findOrFail($id)->toArray();
        return response()->json($dataSurvey);
    }

    //api lsubmit ket qua survey
    //ThoLd (04/10/2019)
    public function apiSubmitSurvey($id, Request $request)
    {
        $response = new ResponseModel();
        try {

            if (!is_numeric($id)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $survey = TmsSurvey::findOrFail($id);

            if (!$survey) {
                $response->status = false;
                $response->message = __('khong_tim_thay_survey');
                return response()->json($response);
            }

            $question_answers = $request->input('question_answers');
            $ddtotext = $request->input('ddtotext');


            \DB::beginTransaction();

            $count_multi = count($question_answers);
            if ($count_multi > 0) { //insert ket qua cau hoi chon dap an
                for ($i = 0; $i < $count_multi; $i++) {
                    $tms_survey_user = new TmsSurveyUser();
                    $tms_survey_user->survey_id = $id;
                    $tms_survey_user->question_id = $question_answers[$i]['ques_id'];
                    $tms_survey_user->answer_id = $question_answers[$i]['ans_id'];
                    if (!empty(Auth::user())) {
                        $tms_survey_user->user_id = Auth::user()->id;
                    } else {
                        $tms_survey_user->user_id = 1; //ko cần đăng nhập để làm survey, id tài khoản guest
                    }

                    $tms_survey_user->type_question = $question_answers[$i]['type_ques'];
                    $tms_survey_user->content_answer = $question_answers[$i]['ans_content'];

                    $tms_survey_user->save();

                    sleep(0.01);
                }
            }

            $count_ddtotext = count($ddtotext['questions']);
            if ($count_ddtotext > 0) { //insert ket qua cau hoi dien dap an
                for ($j = 0; $j < $count_ddtotext; $j++) {

                    if ($ddtotext['questions'][$j]['type_question'] === \App\TmsQuestion::FILL_TEXT && isset($ddtotext['questions'][$j]['question_data'][0]['answers'][0])) {

                        $tms_survey_user = new TmsSurveyUser();
                        $tms_survey_user->survey_id = $id;
                        $tms_survey_user->question_id = $ddtotext['questions'][$j]['id'];
                        if (!empty(Auth::user())) {
                            $tms_survey_user->user_id = Auth::user()->id;
                        } else {
                            $tms_survey_user->user_id = 1; //ko cần đăng nhập để làm survey, id tài khoản guest
                        }
                        $tms_survey_user->type_question = $ddtotext['questions'][$j]['type_question'];
                        $tms_survey_user->content_answer = $ddtotext['questions'][$j]['question_data'][0]['answers'][0];

                        $tms_survey_user->save();
                        sleep(0.01);
                    }
                }
            }

            \DB::commit();

            $response->status = true;
            $response->message = __('gui_ket_qua_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }


    public function apiStatisticSurveyView(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'survey_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_survey_users as uv')
            ->join('mdl_user as u', 'u.id', '=', 'uv.user_id')
            ->where('uv.survey_id', '=', $survey_id)
            ->select('u.id as user_id');

        if ($startdate) {
            $full_start_date = $startdate . " 00:00:00";
            $start_time = strtotime($full_start_date);

            $data = $data->where('uv.created_at', ">=", date("Y-m-d H:i:s", $start_time));
        }
        if ($enddate) {
            $full_end_date = $enddate . " 23:59:59";
            $end_time = strtotime($full_end_date);

            $data = $data->where('uv.created_at', "<=", date("Y-m-d H:i:s", $end_time));
        }

        $data = $data->groupBy('u.id')->get();


        return response()->json($data);
    }

    //api lay thong tin survey va cau hoi trong survey
    //ThoLd (03/10/2019)
    public function apiStatisticSurveyExam(Request $request)
    {
        $survey_id = $request->input('survey_id');
        $province_id = $request->input('province_id');
        $branch_id = $request->input('branch_id');
        $saleroom_id = $request->input('saleroom_id');

        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $param = [
            'survey_id' => 'number',
            'province_id' => 'text',
            'branch_id' => 'text',
            'saleroom_id' => 'text'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $leftjoin_query = 'tms_survey_users';
        if ($saleroom_id != 'nohave') {
            $leftjoin_query = '(SELECT su.survey_id, su.user_id,ru.sale_room_id,su.answer_id, su.created_at FROM tms_survey_users su
                                join tms_sale_room_user ru
                                on ru.user_id = su.user_id where  ru.sale_room_id = ' . $saleroom_id . ')';
        } else if ($branch_id != 'nohave') {
            $leftjoin_query = '(SELECT su.survey_id, su.user_id,ru.sale_room_id,su.answer_id, su.created_at FROM tms_survey_users su
                                join tms_sale_room_user ru
                                on ru.user_id = su.user_id
                                 join tms_sale_rooms sr
                                on sr.id = ru.sale_room_id
                                join tms_branch_sale_room bs
                                on bs.sale_room_id = sr.id
                                 join tms_branch b
                                on b.id = bs.branch_id
                                where  b.id = ' . $branch_id . ')';
        }


        $query = 'select
                    ques_a.ques_pid,
                    ques_a.qpid_content,
                    ques_a.qp_type,
                    ques_a.ques_id,
                    ques_a.content,
                    ques_a.an_id ,
                    ques_a.ans_content,
                    count(su.answer_id) total_choice
                    from
                    (select s.id survey_id, s.name sur_name, q.id ques_pid,q.content qpid_content,q.type_question qp_type, qd.id ques_id,qd.content qd_content,qa.id an_id, qd.content, qa.content ans_content  from tms_question_answers qa
                    join tms_question_datas qd
                    on qd.id = qa.question_id
                    join tms_questions q
                    on q.id = qd.question_id
                    join tms_surveys s
                    on s.id = q.survey_id) ques_a

                    LEFT JOIN ' . $leftjoin_query . ' su
                    on su.answer_id = ques_a.an_id
                    where ques_a.survey_id = ' . $survey_id . '';
        // GROUP by ques_a.an_id';

        if ($startdate) {
            $query .= ' and su.created_at >= ' . "'" . $startdate . "'";
        }

        if ($enddate) {
            $query .= ' and su.created_at <= ' . "'" . $enddate . "'";
        }

        $query .= ' GROUP by ques_a.an_id';

        $lstData = DB::select(DB::raw($query));
        $datas = array();
        $count_data = count($lstData);
        if ($count_data > 0) {
            for ($i = 0; $i < $count_data; $i++) {
                $quesModel = new QuestionModel();
                $quesModel->questionid = $lstData[$i]->ques_pid;
                $quesModel->question_content = $lstData[$i]->qpid_content;
                $quesModel->type_question = $lstData[$i]->qp_type;

                if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            $ansModel->total_choice = $lstData[$k]->total_choice;
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                                break;
                            }
                        }
                    }
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else {

                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            $ansModel->total_choice = $lstData[$k]->total_choice;
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                            }
                        }
                    }

                    $data_childs = my_array_unique($data_childs, true);
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                }
            }
        }

        $datas = my_array_unique($datas, true);

        return response()->json($datas);
    }

    //api lay danh sach tinh thanh
    //ThoLD (08/10/2019)
    public function apiGetProvinces()
    {
        $dataProvinces = TmsCity::select('id', 'name')->where('parent', '=', 0)->get();
        return response()->json($dataProvinces);
    }

    //api lay danh sach dai ly theo tinh thanh
    //ThoLD (08/10/2019)
    public function apiGetBarnchs($province_id)
    {
        if (!is_numeric($province_id)) {
            return response()->json([]);
        }
        $dataBranchs = TmsBranch::query();
        $dataBranchs = $dataBranchs->join('tms_city_branch as cb', 'cb.branch_id', '=', 'tms_branch.id')
            ->join('tms_city as c', 'c.id', '=', 'cb.city_id')
            ->select('tms_branch.id', 'tms_branch.name')
            ->where('c.id', $province_id)->get();

        return response()->json($dataBranchs);
    }


    //api lay danh sach diem ban theo dai ly
    //ThoLD (08/10/2019)
    public function apiGetSaleRooms($branch_id)
    {
        if (!is_numeric($branch_id)) {
            return response()->json([]);
        }

        $dataSaleRooms = TmsSaleRooms::query();
        $dataSaleRooms = $dataSaleRooms->join('tms_branch_sale_room as sr', 'sr.sale_room_id', '=', 'tms_sale_rooms.id')
            ->join('tms_branch as b', 'b.id', '=', 'sr.branch_id')
            ->select('tms_sale_rooms.id', 'tms_sale_rooms.name')
            ->where('b.id', $branch_id)->get();

        return response()->json($dataSaleRooms);
    }

    public $dataResult;

    public function apiExportFile($survey_id, $branch_id, $saleroom_id, $type_file)
    {
        if (!is_numeric($survey_id) && !is_numeric($branch_id) && !is_numeric($saleroom_id)) {
            return '';
        }

        $leftjoin_query = 'tms_survey_users';
        if ($saleroom_id != 'nohave') {
            $leftjoin_query = '(SELECT su.survey_id, su.user_id,ru.sale_room_id,su.answer_id FROM `tms_survey_users` su
                                join tms_sale_room_user ru
                                on ru.user_id = su.user_id where  ru.sale_room_id = ' . $saleroom_id . ')';
        } else if ($branch_id != 'nohave') {
            $leftjoin_query = '(SELECT su.survey_id, su.user_id,ru.sale_room_id,su.answer_id FROM `tms_survey_users` su
                                join tms_sale_room_user ru
                                on ru.user_id = su.user_id
                                 join tms_sale_rooms sr
                                on sr.id = ru.sale_room_id
                                join tms_branch_sale_room bs
                                on bs.sale_room_id = sr.id
                                 join tms_branch b
                                on b.id = bs.branch_id
                                where  b.id = ' . $branch_id . ')';
        }


        $query = 'select
                    ques_a.ques_pid,
                    ques_a.qpid_content,
                    ques_a.qp_type,
                    ques_a.ques_id,
                    ques_a.content,
                    ques_a.an_id ,
                    ques_a.ans_content,
                    count(su.answer_id) total_choice
                    from
                    (select s.id survey_id, s.name sur_name, q.id ques_pid,q.content qpid_content,q.type_question qp_type, qd.id ques_id,qd.content qd_content,qa.id an_id, qd.content, qa.content ans_content  from tms_question_answers qa
                    join tms_question_datas qd
                    on qd.id = qa.question_id
                    join tms_questions q
                    on q.id = qd.question_id
                    join tms_surveys s
                    on s.id = q.survey_id) ques_a

                    LEFT JOIN ' . $leftjoin_query . ' su
                    on su.answer_id = ques_a.an_id
                    where ques_a.survey_id = ' . $survey_id . '
                    GROUP by ques_a.an_id';

        $lstData = DB::select(DB::raw($query));
        $datas = array();

        #region sort data
        $count_data = count($lstData);
        if ($count_data > 0) {
            for ($i = 0; $i < $count_data; $i++) {
                $quesModel = new QuestionModel();
                $quesModel->questionid = $lstData[$i]->ques_pid;
                $quesModel->question_content = $lstData[$i]->qpid_content;
                $quesModel->type_question = $lstData[$i]->qp_type;

                if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::MULTIPLE_CHOICE) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->indexAns = 'Đáp án ' . $k;
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            $ansModel->total_choice = $lstData[$k]->total_choice;
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                                break;
                            }
                        }
                    }
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                } else {

                    $data_childs = array();
                    for ($j = 0; $j < $count_data; $j++) {

                        if ($lstData[$j]->ques_pid == $quesModel->questionid) {
                            if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                $quesChildModel = new QuestionChildModel();
                                $quesChildModel->questionid = $lstData[$j]->ques_id;
                                $quesChildModel->question_content = $lstData[$j]->content;

                                $total_ques = 0;
                                $answers = array();
                                for ($k = 0; $k < $count_data; $k++) {
                                    if ($lstData[$k]->ques_id == $quesChildModel->questionid) {
                                        if ($lstData[$i]->qp_type === \App\TmsQuestion::GROUP) {
                                            $ansModel = new AnswerModel();
                                            $ansModel->indexAns = 'Đáp án ' . $k;
                                            $ansModel->answerid = $lstData[$k]->an_id;
                                            $ansModel->answer_content = $lstData[$k]->ans_content;
                                            $ansModel->total_choice = $lstData[$k]->total_choice;
                                            $total_ques += $ansModel->total_choice;
                                            array_push($answers, $ansModel);
                                        }
                                    }
                                }

                                $quesChildModel->total_choice = $total_ques;
                                $quesChildModel->lstAnswers = $answers;

                                array_push($data_childs, $quesChildModel);
                            }
                        }
                    }

                    $data_childs = my_array_unique($data_childs, true);
                    $quesModel->lstQuesChild = $data_childs;
                    array_push($datas, $quesModel);
                }
            }
        }
        #endregion

        $datas = my_array_unique($datas, true);

        $arrKeys = array_keys($datas);
        $countkey = count($arrKeys);

        $dataFinish = array();

        if ($countkey > 0) {
            foreach ($arrKeys as $key) {
                $dataObj = $datas[$key];
                if ($dataObj->type_question === \App\TmsQuestion::GROUP) {
                    $arrKeyGrs = array_keys($dataObj->lstQuesChild);

                    $count_group = count($arrKeyGrs);
                    $dataGroups = array();
                    if ($count_group > 0) {
                        foreach ($arrKeyGrs as $keyGr) {
                            $dtGr = $dataObj->lstQuesChild[$keyGr];
                            array_push($dataGroups, $dtGr);
                        }
                    }
                    $dataObj->lstQuesChild = $dataGroups;
                }
                array_push($dataFinish, $dataObj);
            }
        }
        $dataModel = new DataModel();

        $survey = TmsSurvey::findOrFail($survey_id);

        $startdate = date('d/m/Y', $survey->startdate);
        $enddate = date('d/m/Y', $survey->enddate);

        $dataModel->survey = $survey;
        $dataModel->survey->startdate = $startdate;
        $dataModel->survey->enddate = $enddate;
        $dataModel->statistics = $dataFinish;

        $lstFeedback = DB::table('tms_survey_users as tsu')
            ->join('tms_questions as tq', 'tq.id', '=', 'tsu.question_id')
            ->join('mdl_user as u', 'u.id', '=', 'tsu.user_id')
            ->where('tsu.type_question', '=', TmsQuestion::FILL_TEXT)
            ->where('tsu.survey_id', '=', $survey_id)
            ->select('tq.content', 'u.username', 'tsu.content_answer')->get();

        $dataModel->lstFeedback = $lstFeedback;

        if ($type_file == 'pdf') {

            $pdf = PDF::loadView('survey.survey_export', compact('dataModel'));
            return $pdf->download($survey->code . '-' . $survey->name . '.pdf');
        } else {
            $this->dataResult = $dataModel;
            return Excel::download(new SurveyExportView($this->dataResult), $survey->code . '-' . $survey->name . '.xlsx', 'Xlsx');
        }
    }

    #endregion
    // End SurveyController

    // SystemController
    public function apiListRole()
    {
        $special_role = Role::arr_role_special;
        $default_role = Role::arr_role_default;
        $excluded = array_merge($special_role, $default_role);

        Role::whereNotIn('name', $excluded)
            ->update(['status' => 0]);

        $roles = Role::whereNotIn('name', [Role::EDITING_TEACHER, Role::COURSE_CREATOR])
            ->select('id', 'name', 'status')
            ->get()->toArray();
        return response()->json($roles);
    }

    public function apiListUser(Request $request)
    {
        //$paged = $request->input('paged');
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $roles = $request->input('roles');
        $user_id = $request->input('user');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'roles' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listUsers = DB::table('tms_user_detail')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id');
        $listUsers = $listUsers->select(
            'tms_user_detail.fullname as fullname',
            'tms_user_detail.email as email',
            'mdl_user.username as username',
            'tms_user_detail.user_id as user_id',
            'tms_user_detail.cmtnd as cmtnd',
            DB::raw('(select count(mhr.model_id) as user_count from tms_user_detail tud
                inner join model_has_roles mhr on mhr.model_id = tud.user_id
                inner join roles r on r.id = mhr.role_id
                where tud.user_id = mdl_user.id and r.name = "student")
                as user_count')
        )
            ->where('tms_user_detail.deleted', 0)
            ->whereNotIn('mdl_user.username', ['admin']);
        if ($roles != 0) {
            $listUsers = $listUsers->join('model_has_roles', 'model_has_roles.model_id', '=', 'mdl_user.id');
            $listUsers = $listUsers->where('model_has_roles.role_id', $roles);
        }
        //else {
        /*$listUsers = $listUsers->leftJoin('model_has_roles','model_has_roles.model_id','=','mdl_user.id');
        $listUsers = $listUsers->join('roles','roles.id','=','model_has_roles.role_id');*/
        /*$listUsers = $listUsers->select(
            DB::raw('(select count(mhr.id) as user_count from model_has_roles mhr
            inner join roles r on r.id = mhr.role_id
            where mhr.model_id = tms_user_detail.user_id and r.name in ("teacher","student"))
            as user_count')
        );*/
        //$listUsers = $listUsers->where('status','=',0);
        //$listUsers = $listUsers->whereNotIn('roles.name',['teacher','student']);
        //}

        if ($user_id) {
            $listUsers->where('mdl_user.id', $user_id);
        }
        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tms_user_detail.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mdl_user.username', 'like', "%{$this->keyword}%");
            });
        }
        $listUsers = $listUsers->orderBy('tms_user_detail.created_at', 'desc');
        $listUsers = $listUsers->paginate($row);

        $total = ceil($listUsers->total() / $row);
        $total_user = $listUsers->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }

    public function apiFilterFetch($request)
    {
        $response = [];
        $type = $request->input('type');

        if ($type == 'user') {
            $response = TmsUserDetail::where('deleted', 0)->select('user_id as id', 'fullname as label')->limit(20)->get()->toArray();
        } elseif ($type == 'course-online') {
            $response = DB::table('mdl_course as c')
                ->select(
                    'c.id',
                    'c.fullname as label'
                )
                ->where('c.category', '!=', 2)
                ->where('c.category', '!=', 5)->limit(20)->get()->toArray();
        }
        return response()->json($response);
    }

    public function apiStore(Request $request)
    {
        try {
            $avatar = $request->file('file');
            $fullname = $request->input('fullname');
            $dob = $request->input('dob');
            $email = $request->input('email');
            $username = $request->input('username');
            $password = $request->input('password');
            $passwordConf = $request->input('passwordConf');
            $phone = $request->input('phone');
            $cmtnd = $request->input('cmtnd');
            $address = $request->input('address');
            $inputRole = $request->input('inputRole');
            $type = $request->input('type');

            $sex = $request->input('sex');
            $code = ($request->input('code') && $request->input('code') != 'null') ? $request->input('code') : '';
            $start_time = $request->input('start_time');
            $working_status = $request->input('working_status');
            $confirm_address = $request->input('confirm_address');
            $confirm = $request->input('confirm');
            $certificate_code = $request->input('certificate_code');
            $certificate_date = $request->input('certificate_date');
            $branch = $request->input('branch');
            $saleroom = $request->input('saleroom');
            $branch_select = $request->input('branch_select');
            $saleroom_select = $request->input('saleroom_select');
            $training_id = $request->input('training_id');

            $param = [
                'fullname' => 'text',
                'dob' => 'date',
                //'email' => 'email',
                'username' => 'code',
                /*'password' => 'token',
                'passwordConf' => 'token',*/
                'phone' => 'phone',
                'cmtnd' => 'number',
                'address' => 'text',
                'sale_room_id' => 'number',
                'sex' => 'number',
                'code' => 'text',
                'start_time' => 'date',
                'working_status' => 'number',
                'confirm_address' => 'number',
                'confirm' => 'number',
                'certificate_code' => 'text',
                'certificate_date' => 'date',
                'file' => 'image',
                'training_id' => 'number',
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                return response()->json($validator);
            }

            if (!$email) {
                $email = Config::get('constants.domain.EMAIL-DEFAULT');
            }

            //kiem tra xem fullname co nhap dung dinh dang hay khong
            $arr_fullname = explode(' ', $fullname);
            if (count($arr_fullname) < 2) {
                return response()->json(status_message('error', __('truong_ho_va_ten_phai_co_dau_cach')));
            }

            $username = strtolower($username);

            //Check scmtnd
            $userByCmtnd = TmsUserDetail::select('id')->where('cmtnd', $cmtnd)->first();

            if ($userByCmtnd)
                return response()->json(error_message('inputCmtnd', __('so_cmtnd_da_ton_tai')));
            //Check user
            $userByUser = MdlUser::where('username', $username)->where('deleted', 1)->count();
            if ($userByUser > 0)
                return response()->json(error_message('inputUsername', __('ten_dang_nhap_da_ton_tai_va_dang_bi_khoa')));
            $userByUser = MdlUser::where('username', $username)->count();
            if ($userByUser > 0)
                return response()->json(error_message('inputUsername', __('ten_dang_nhap_da_ton_tai')));

            //Check email
            /*$userByEmail = TmsUserDetail::select('id')->where('email', $email)->first();
            if ($userByEmail)
                return response()->json(error_message('inputEmail', __('dia_chi_email_da_ton_tai')));*/

            if ($password != $passwordConf)
                return response()->json(error_message('inputPassword', __('thong_tin_mat_khau_chua_khop_nhau')));

            if (!validate_password_func($password))
                return response()->json(error_message('inputPassword', __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet')));

            if (!validate_password_func($passwordConf))
                return response()->json(error_message('inputPasswordConfirm', __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet')));

            $param = [
                'email' => 'email',
            ];
            $validator = validate_fails($request, $param);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !empty($validator))
                return response()->json(error_message('inputEmail', __('email_sai_dinh_dang')));

            \DB::beginTransaction();
            //  Role::select('id', 'name', 'mdl_role_id', 'status')->where('name', 'student')->first();
            if ($type == 'teacher') {
                $roles = Role::select('id', 'name', 'mdl_role_id', 'status')->where('name', 'teacher')->get()->toArray();
            } elseif ($type == 'student') {
                $roles = Role::select('id', 'name', 'mdl_role_id', 'status')->where('name', 'student')->get()->toArray();
            } else {
                if ($inputRole) {
                    $roles = Role::select('id', 'name', 'mdl_role_id', 'status')->whereIn('id', explode(',', $inputRole))->get()->toArray();
                } else {
                    $roles = Role::select('id', 'name', 'mdl_role_id', 'status')->where('name', 'student')->get()->toArray();
                }
            }
            $convert_name = convert_name($fullname);
            $mdlUser = new MdlUser;
            $mdlUser->username = $username;
            $mdlUser->email = $email;
            $mdlUser->confirmed = 1;
            $mdlUser->firstname = $convert_name['firstname'];
            $mdlUser->lastname = $convert_name['lastname'];
            $mdlUser->password = bcrypt($password);
            $mdlUser->save();

            //Thêm nơi làm việc cho tài khoản
            $arr_data = [];
            $data_item = [];
            if ($branch_select) {
                $branch_select = explode(',', $branch_select);
                if (!empty($branch_select)) {
                    foreach ($branch_select as $branch_id) {
                        if (!is_numeric($branch_id)) {
                            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                        }

                        $data_item['sale_room_id'] = $branch_id;
                        $data_item['user_id'] = $mdlUser->id;
                        $data_item['type'] = TmsSaleRoomUser::AGENTS;
                        array_push($arr_data, $data_item);

                        //                        TmsSaleRoomUser::firstOrCreate([
                        //                            'sale_room_id' => $branch_id,
                        //                            'user_id' => $mdlUser->id,
                        //                            'type' => TmsSaleRoomUser::AGENTS
                        //                        ]);
                    }
                    TmsSaleRoomUser::insert($arr_data);
                }
            }
            if ($saleroom_select) {
                $arr_data = [];
                $data_item = [];
                $saleroom_select = explode(',', $saleroom_select);
                if (!empty($saleroom_select)) {
                    foreach ($saleroom_select as $sale_room_id) {
                        if (!is_numeric($sale_room_id)) {
                            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                        }

                        $data_item['sale_room_id'] = $sale_room_id;
                        $data_item['user_id'] = $mdlUser->id;
                        $data_item['type'] = TmsSaleRoomUser::POS;
                        array_push($arr_data, $data_item);

                        //                        TmsSaleRoomUser::firstOrCreate([
                        //                            'sale_room_id' => $sale_room_id,
                        //                            'user_id' => $mdlUser->id,
                        //                            'type' => TmsSaleRoomUser::POS
                        //                        ]);
                    }
                    TmsSaleRoomUser::insert($arr_data);
                }
            }

            //Thêm vị trí( Khung năng lực )
            if ($training_id > 0) {
                TmsTrainningUser::firstOrCreate([
                    'trainning_id' => $training_id,
                    'user_id' => $mdlUser->id
                ]);
                //$category = TmsTrainningCategory::select('category_id')->where('trainning_id', $training_id)->first();
                //enrole cho học viên theo khung năng lực
                //\DB::beginTransaction();
                training_enrole($mdlUser->id, $training_id);
                //\DB::commit();
            }

            if ($roles) {
                $arr_data = [];
                $data_item = [];

                $arr_data_enrol = [];
                $data_item_enrol = [];
                foreach ($roles as $role) {
                    //if (!$checkrole) {
                    if ($role['name'] == 'student') {
                        $mdlUser->update(['redirect_type' => 'lms']);
                    } else {
                        $mdlUser->update(['redirect_type' => 'default']);
                    }
                    //endrole

                    $data_item['role_id'] = $role['id'];
                    $data_item['model_id'] = $mdlUser->id;
                    $data_item['model_type'] = 'App/MdlUser';
                    array_push($arr_data, $data_item);

                    //                    add_user_by_role($mdlUser->id, $role['id']);
                    //                    enrole_lms($mdlUser->id, $role['mdl_role_id'], $confirm);

                    bulk_enrol_lms($mdlUser->id, $role['mdl_role_id'], $arr_data_enrol, $data_item_enrol);

                    usleep(100);
                }

                ModelHasRole::insert($arr_data);
                MdlRoleAssignments::insert($arr_data_enrol);
            }

            $user = new TmsUserDetail;
            $user->user_id = $mdlUser->id;
            $user->cmtnd = $cmtnd;
            $user->fullname = $fullname ? $fullname : $username;
            $timestamp = strtotime($dob);
            if ($timestamp === FALSE) {
                $timestamp = strtotime(str_replace('/', '-', $dob));
            }
            $user->dob = $timestamp;
            $user->email = $email;
            $user->phone = $phone;
            $user->address = $address;

            $user->sex = $sex;
            $user->code = $code;
            if ($start_time && $start_time != 'null') {
                $start_time = strtotime($start_time);
                if ($start_time === FALSE) {
                    $start_time = strtotime(str_replace('/', '-', $start_time));
                }
                $user->start_time = $start_time;
            }
            $user->start_time = $start_time;
            $user->working_status = $working_status;
            $user->confirm = $confirm;
            $user->confirm_address = $confirm_address;
            if ($avatar) {
                $name = time() . '.' . $avatar->getClientOriginalExtension();

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/user',
                    $avatar,
                    $name
                );

                $path_avatar = '/storage/upload/user/' . $name;
                //
                //                $destinationPath = public_path('/upload/user/');
                //                $avatar->move($destinationPath, $name);
                $user->avatar = $path_avatar;
            }
            $user->save();

            if (strlen($certificate_code) != 0) {
                $student_certificate = new StudentCertificate();
                $student_certificate->userid = $mdlUser->id;
                $student_certificate->code = $certificate_code;
                if (strlen($certificate_date) != 0) {
                    $student_certificate->timecertificate = strtotime($certificate_date);
                } else {
                    $student_certificate->timecertificate = time();
                }
                $student_certificate->status = 1;
                $student_certificate->save();
            }


            devcpt_log_system('user', '/system/user/edit/' . $mdlUser->id, 'create', 'Tạo mới User: ' . $mdlUser->username);
            \DB::commit();
            return response()->json(status_message('success', __('tao_moi_tai_khoan_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Tạo nhân viên thuộc điểm bán
    public function apiStoreSaleRoom(Request $request)
    {
        try {
            $avatar = $request->file('file');
            $fullname = $request->input('fullname');
            $dob = $request->input('dob');
            $email = $request->input('email');
            $username = $request->input('username');
            $password = $request->input('password');
            $passwordConf = $request->input('passwordConf');
            $phone = $request->input('phone');
            $cmtnd = $request->input('cmtnd');
            $address = $request->input('address');
            $sale_room_id = $request->input('sale_room_id');

            $sex = $request->input('sex');
            $code = ($request->input('code') && $request->input('code') != 'null') ? $request->input('code') : '';
            $start_time = $request->input('start_time');
            $working_status = $request->input('working_status');
            $confirm_address = $request->input('confirm_address');
            $certificate_code = $request->input('certificate_code');
            $certificate_date = $request->input('certificate_date');
            $confirm = $request->input('confirm');
            $type = $request->input('type');
            $training_id = $request->input('training_id');

            $param = [
                'fullname' => 'text',
                'dob' => 'date',
                'email' => 'email',
                'username' => 'code',
                /*'password' => 'token',
                'passwordConf' => 'token',*/
                'phone' => 'phone',
                'cmtnd' => 'number',
                'address' => 'text',
                'sale_room_id' => 'number',
                'sex' => 'number',
                'code' => 'text',
                'start_time' => 'date',
                'working_status' => 'number',
                'confirm_address' => 'number',
                'confirm' => 'number',
                'file' => 'image',
                'type' => 'code',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            if (!$email) {
                $email = Config::get('constants.domain.EMAIL-DEFAULT');
            }

            //kiem tra xem fullname co nhap dung dinh dang hay khong
            $arr_fullname = explode(' ', $fullname);
            if (count($arr_fullname) < 2) {
                return response()->json(status_message('error', __('truong_ho_va_ten_phai_co_dau_cach')));
            }

            $username = strtolower($username);

            //Check scmtnd
            $userByCmtnd = TmsUserDetail::select('id')->where('cmtnd', $cmtnd)->first();

            if ($userByCmtnd)
                return response()->json(error_message('inputCmtnd', __('so_cmtnd_da_ton_tai')));
            //Check user
            $userByUser = MdlUser::where('username', $username)->where('deleted', 1)->count();
            if ($userByUser > 0)
                return response()->json(error_message('inputUsername', __('ten_dang_nhap_da_ton_tai_va_dang_bi_khoa')));
            $userByUser = MdlUser::where('username', $username)->count();
            if ($userByUser > 0)
                return response()->json(error_message('inputUsername', __('ten_dang_nhap_da_ton_tai')));

            //Check email
            /*$userByEmail = TmsUserDetail::select('id')->where('email', $email)->first();
            if ($userByEmail)
                return response()->json(error_message('inputEmail', __('dia_chi_email_da_ton_tai')));*/

            if ($password != $passwordConf)
                return response()->json(error_message('inputPassword', __('dia_chi_email_da_ton_tai')));

            if (!validate_password_func($password))
                return response()->json(error_message('inputPassword', __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet')));

            if (!validate_password_func($passwordConf))
                return response()->json(error_message('inputPasswordConfirm', __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet')));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                return response()->json(error_message('inputEmail', __('email_sai_dinh_dang')));

            \DB::beginTransaction();
            $roles = Role::select('id', 'name', 'mdl_role_id')->where('name', 'student')->get()->toArray();
            $convert_name = convert_name($fullname);
            $mdlUser = new MdlUser;
            $mdlUser->username = $username;
            $mdlUser->email = $email;
            $mdlUser->confirmed = 1;
            $mdlUser->firstname = $convert_name['firstname'];
            $mdlUser->lastname = $convert_name['lastname'];
            $mdlUser->password = bcrypt($password);
            $mdlUser->save();
            if ($roles) {
                $arr_data = [];
                $data_item = [];

                $arr_data_enrol = [];
                $data_item_enrol = [];

                foreach ($roles as $role) {
                    $mdlUser->update(['redirect_type' => 'lms']);

                    $data_item['role_id'] = $role['id'];
                    $data_item['model_id'] = $mdlUser->id;
                    $data_item['model_type'] = 'App/MdlUser';
                    array_push($arr_data, $data_item);

                    bulk_enrol_lms($mdlUser->id, $role['mdl_role_id'], $arr_data_enrol, $data_item_enrol);

                    usleep(100);

                    //                    add_user_by_role($mdlUser->id, $role['id']);
                    //                    enrole_lms($mdlUser->id, $role['mdl_role_id'], $confirm);
                    //                    usleep(100);
                }

                ModelHasRole::insert($arr_data);
                MdlRoleAssignments::insert($arr_data_enrol);
            }

            $user = new TmsUserDetail;
            $user->user_id = $mdlUser->id;
            $user->cmtnd = $cmtnd;
            $user->fullname = $fullname ? $fullname : $username;
            $timestamp = strtotime($dob);
            if ($timestamp === FALSE) {
                $timestamp = strtotime(str_replace('/', '-', $dob));
            }
            $user->dob = $timestamp;
            $user->email = $email;
            $user->phone = $phone;
            $user->address = $address;

            $user->sex = $sex;
            $user->code = $code;
            if ($start_time && $start_time != 'null') {
                $start_time = strtotime($start_time);
                if ($start_time === FALSE) {
                    $start_time = strtotime(str_replace('/', '-', $start_time));
                }
                $user->start_time = $start_time;
            }
            $user->start_time = $start_time;
            $user->working_status = $working_status;
            $user->confirm = $confirm;
            $user->confirm_address = $confirm_address;
            if ($avatar) {
                $name = time() . '.' . $avatar->getClientOriginalExtension();
                //                $destinationPath = public_path('/upload/user/');
                //                $avatar->move($destinationPath, $name);

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/user',
                    $avatar,
                    $name
                );

                $path_avatar = '/storage/upload/user/' . $name;

                $user->avatar = $path_avatar;
            }
            $user->save();

            //Thêm vị trí( Khung năng lực )
            if ($training_id > 0) {
                TmsTrainningUser::firstOrCreate([
                    'trainning_id' => $training_id,
                    'user_id' => $mdlUser->id
                ]);
                //$category = TmsTrainningCategory::select('category_id')->where('trainning_id', $training_id)->first();
                //enrole cho học viên theo khung năng lực
                //\DB::beginTransaction();
                training_enrole($mdlUser->id, $training_id);
                //\DB::commit();
            }

            if ($mdlUser->id && $sale_room_id) {
                $tsru = new TmsSaleRoomUser;
                $tsru->sale_room_id = $sale_room_id;
                $tsru->user_id = $mdlUser->id;
                if ($type == TmsSaleRoomUser::AGENTS) {
                    $tsru->type = TmsSaleRoomUser::AGENTS;
                }
                $tsru->save();
            }

            if (strlen($certificate_code) != 0) {
                $student_certificate = new StudentCertificate();
                $student_certificate->userid = $mdlUser->id;
                $student_certificate->code = $certificate_code;
                if (strlen($certificate_date) != 0) {
                    $student_certificate->timecertificate = strtotime($certificate_date);
                } else {
                    $student_certificate->timecertificate = time();
                }
                $student_certificate->status = 1;
                $student_certificate->save();
            }

            devcpt_log_system('user', '/system/user/edit/' . $mdlUser->id, 'create', 'Tạo mới User: ' . $mdlUser->username);
            \DB::commit();
            return response()->json(status_message('success', __('tao_moi_tai_khoan_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }


    public function apiUpdateProfile(Request $request)
    {
        try {
            $avatar = $request->file('file');
            $fullname = $request->input('fullname');
            $dob = $request->input('dob');
            $email = $request->input('email');
            $phone = $request->input('phone');
            $address = $request->input('address');
            $user_id = Auth()->user()->id;

            $sex = $request->input('sex');
            //            $code = $request->input('code');
            $convert_name = convert_name($fullname);

            $param = [
                'fullname' => 'text',
                'dob' => 'date',
                'email' => 'email',
                'username' => 'text',
                'phone' => 'phone',
                'address' => 'text',
                'sex' => 'number',
                'code' => 'text',
                'file' => 'image',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            if (!$email) {
                $email = Config::get('constants.domain.EMAIL-DEFAULT');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                return response()->json(error_message('inputEmail', __('email_sai_dinh_dang')));

            //kiem tra xem fullname co nhap dung dinh dang hay khong
            $arr_fullname = explode(' ', $fullname);
            if (count($arr_fullname) < 2) {
                return response()->json(status_message('error', __('truong_ho_va_ten_phai_co_dau_cach')));
            }

            //Check email
            /*$userByEmail = TmsUserDetail::select('user_id')->whereNotIn('user_id', [$user_id])->where('email', $email)->first();
            if ($userByEmail)
                return response()->json(error_message('inputEmail', __('dia_chi_email_da_ton_tai')));*/

            \DB::beginTransaction();
            $mdlUser = MdlUser::findOrFail($user_id);
            $mdlUser->firstname = $convert_name['firstname'];
            $mdlUser->lastname = $convert_name['lastname'];
            $mdlUser->email = $email;
            $mdlUser->save();
            $infoLog = 'Tài khoản :' . $mdlUser['username'] . 'Update lại thông tin.';

            $user = TmsUserDetail::where('user_id', $user_id)->first();
            $user->fullname = $fullname;
            $timestamp = strtotime($dob);
            if ($timestamp === FALSE) {
                $timestamp = strtotime(str_replace('/', '-', $dob));
            }
            $user->dob = $timestamp;
            $user->email = $email;
            $user->phone = ($phone && $phone != 'null' && $phone != 'NULL') ? $phone : '';
            $user->address = $address ? $address : '';

            $user->sex = $sex;
            if ($avatar) {
                $image_path = $user['avatar'];
                if ($image_path) {
                    //                    $filename = public_path() . $image_path;
                    //                    \File::delete($filename);
                    if (file_exists(storage_path($image_path))) {
                        Storage::delete($image_path);
                    }
                }


                $name = time() . '.' . $avatar->getClientOriginalExtension();
                //                $destinationPath = public_path('/upload/user/');
                //                $avatar->move($destinationPath, $name);

                // cơ chế lưu ảnh vào folder storage, tăng cường bảo mật
                Storage::putFileAs(
                    'public/upload/user',
                    $avatar,
                    $name
                );

                $path_avatar = '/storage/upload/user/' . $name;

                $user->avatar = $path_avatar;
            }
            $user->save();

            devcpt_log_system('user', '/system/user/edit/' . $mdlUser['id'], 'update_profile', $infoLog);

            \DB::commit();
            return response()->json(status_message('success', __('cap_nhat_tai_khoan_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //form user update
    //form user detail
    public function apiUserDetail(Request $request)
    {
        $user_id = $request->input('user_id');

        if (!$user_id) {
            return response()->json([]);
        }

        $param = [
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $now = Carbon::now();
        $mdlUser = MdlUser::select('username')->findOrFail($user_id);
        $users = TmsUserDetail::with('city', 'training.training_detail')
            ->with('certificate')
            ->where([
                'user_id' => $user_id,
            ])->first();
        $users['username'] = $mdlUser['username'];
        if ($users['dob']) {
            $dob = date('Y-m-d', $users['dob']);
            $users['dob'] = $dob;
        }

        if ($users['certificate']) {
            if (strlen($users['certificate']->timecertificate) != 0) {
                $tc = date('Y-m-d', $users['certificate']->timecertificate);
                $users['certificate']->timecertificate = $tc;
            }
        }

        if ($users['start_time']) {
            $start_time = date('Y-m-d', $users['start_time']);
            $users['start_time'] = $start_time;
        }
        //$created_at = date('Y-m-d',strtotime($users['created_at']));
        //$create_time = (strtotime($created_at.' 00:00') + 2592000‬ - strtotime($now));
        $diffs = strtotime($users['created_at']) - strtotime($now);
        $diff = abs(30 * 60 * 60 * 24 + $diffs);
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $diff_time = ($diffs + 30 * 60 * 60 * 24) < 0 ? 'Đã hết hạn' : $days . ' Ngày, ' . $hours . 'Giờ';
        $diff_time_class = ($diffs + 30 * 60 * 60 * 24) < 0 ? 'text-danger' : 'text-warning';
        $users['diff_time'] = $diff_time;
        $users['diff_time_class'] = $diff_time_class;
        $roless = ModelHasRole::with('role')->where('model_id', $user_id)->get()->toArray();
        $roles = ModelHasRole::where('model_id', $user_id)->pluck('role_id');
        $studentRoleCount = Role::whereIn('id', $roles)->where('name', 'student')->count();
        $users['role'] = $roles;
        $users['roles'] = $roless;
        $users['student_role'] = $studentRoleCount;
        $salerooms = DB::table('tms_sale_room_user as tsru')
            ->select(
                'tb.id as branch_id',
                'tb.name as branch_name',
                'tb.code as branch_code',
                'tsr.id',
                'tsr.name',
                'tsr.code',
                'tsru.type'
            )
            ->leftJoin('tms_branch as tb', 'tb.id', '=', 'tsru.sale_room_id')
            ->leftJoin('tms_sale_rooms as tsr', 'tsr.id', '=', 'tsru.sale_room_id')
            ->where('tsru.user_id', '=', $user_id)
            ->whereIn('tsru.type', [TmsSaleRoomUser::AGENTS, TmsSaleRoomUser::POS])
            ->get();
        $users['salerooms'] = $salerooms;
        return response()->json($users);
    }

    public function apiUpdate(Request $request)
    {
        try {
            $avatar = $request->file('file');
            $fullname = $request->input('fullname');
            $dob = $request->input('dob');
            $email = $request->input('email');
            $username = $request->input('username');
            //$password = $request->input('password');
            $phone = $request->input('phone');
            $cmtnd = $request->input('cmtnd');
            $address = $request->input('address');
            $roles = $request->input('role');
            //$type = $request->input('type');
            $user_id = $request->input('user_id');

            $sex = $request->input('sex');
            $code = ($request->input('code') && $request->input('code') != 'null') ? $request->input('code') : '';
            $start_time = $request->input('start_time');
            $working_status = $request->input('working_status');
            $confirm_address = $request->input('confirm_address');
            $confirm = $request->input('confirm');
            $certificate_code = $request->input('certificate_code');
            $certificate_date = $request->input('certificate_date');
            //$branch = $request->input('branch');
            //$saleroom = $request->input('saleroom');
            $branch_select = $request->input('branch_select');
            $saleroom_select = $request->input('saleroom_select');
            $trainning_id = $request->input('trainning_id');

            $param = [
                'fullname' => 'text',
                'dob' => 'date',
                'email' => 'email',
                'username' => 'text',
                'phone' => 'phone',
                'cmtnd' => 'number',
                'address' => 'text',
                'sale_room_id' => 'number',
                'sex' => 'number',
                'code' => 'text',
                'start_time' => 'date',
                'working_status' => 'number',
                'confirm_address' => 'number',
                'confirm' => 'number',
                'certificate_code' => 'text',
                'certificate_date' => 'date',
                'file' => 'image',
                'trainning_id' => 'number'
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                return response()->json($validator);
            }

            if (!$email) {
                $email = Config::get('constants.domain.EMAIL-DEFAULT');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                return response()->json(error_message('inputEmail', __('email_sai_dinh_dang')));

            if (has_user_market($user_id)) {
                $confirm = 1;
            }

            //kiem tra xem fullname co nhap dung dinh dang hay khong
            $arr_fullname = explode(' ', $fullname);
            if (count($arr_fullname) < 2) {
                return response()->json(status_message('error', __('truong_ho_va_ten_phai_co_dau_cach')));
            }

            $username = strtolower($username);

            $convert_name = convert_name($fullname);
            $roles = explode(',', $roles);
            //Check scmtnd
            $userByCmtnd = TmsUserDetail::select('user_id')->whereNotIn('user_id', [$user_id])->where('cmtnd', $cmtnd)->first();
            if ($userByCmtnd)
                return response()->json(error_message('inputCmtnd', __('so_cmtnd_da_ton_tai')));
            //Check user
            $userByUser = MdlUser::select('id')->whereNotIn('id', [$user_id])->where('username', $username)->first();
            if ($userByUser)
                return response()->json(error_message('inputUsername', __('ten_dang_nhap_da_ton_tai')));
            //Check email
            /*$userByEmail = TmsUserDetail::select('user_id')->whereNotIn('user_id', [$user_id])->where('email', $email)->first();
            if ($userByEmail)
                return response()->json(error_message('inputEmail', __('dia_chi_email_da_ton_tai')));*/
            \DB::beginTransaction();
            $infoLog = 'Sửa thông tin tài khoản :' . $username;
            $mdlUser = MdlUser::findOrFail($user_id);
            $mdlUser->username = $username;
            $mdlUser->firstname = $convert_name['firstname'];
            $mdlUser->lastname = $convert_name['lastname'];
            $mdlUser->email = $email;
            /*if ($password && $password != 'undefined') {
                if(validate_password_func($password)){
                    $mdlUser->password = bcrypt($password);
                }else{
                    return 'Mật khẩu phải trên 8 ký tự, gồm cữ hoa, chữ cái thường, chữ số và ký tự đặc biệt.';
                }
            }*/

            $root = MdlUser::where('username', 'admin')->first();
            if ($root['id'] == $user_id) {
                $mdr_root = ModelHasRole::where('model_id', $root['id'])->get()->toArray();
                if ($mdr_root) {
                    foreach ($mdr_root as $mhr) {
                        ModelHasRole::where([
                            'role_id' => $mhr['role_id'],
                            'model_id' => $mhr['model_id']
                        ])->delete();
                    }
                }
            } else {
                ModelHasRole::where('model_id', $user_id)->delete();
            }

            //            $checkStudent = false;
            if ($roles[0]) {
                //$checkrole = false;
                foreach ($roles as $role_id) {
                    //if (!$checkrole) {
                    $role = Role::findOrFail($role_id);
                    if ($role['name'] == 'student' && count($roles) == 1) {
                        $mdlUser->redirect_type = 'lms';
                        //                        $checkStudent = true;
                    } else {
                        $mdlUser->redirect_type = 'default';
                        MdlRoleAssignments::where([
                            'userid' => $user_id
                        ])->whereIn('roleid', [$role['mdl_role_id']])->delete();
                    }
                    //}

                    add_user_by_role($user_id, $role['id']);
                    enrole_lms($user_id, $role['mdl_role_id'], $confirm);

                    /*if ($role['name'] == Role::MANAGE_MARKET) {
                        $checkrole = true;
                    }*/
                }
            } else {
                $mdlUser->redirect_type = 'lms';
                $role = Role::where('name', 'student')->first();
                add_user_by_role($user_id, $role['id']);
                enrole_lms($user_id, $role['mdl_role_id'], $confirm);
                $checkStudent = true;
            }
            /*if(!$checkStudent){
                $mdlUser->redirect_type = 'lms';
                $role = Role::where('name','student')->first();
                add_user_by_role($user_id,$role['id']);
                enrole_lms($user_id,$role['mdl_role_id'],$confirm);
            }*/
            $mdlUser->save();

            //Thêm nơi làm việc cho tài khoản
            TmsSaleRoomUser::where('user_id', $mdlUser->id)->delete();

            if ($branch_select) { //Thêm đại lý
                $branch_select = explode(',', $branch_select);
                if (!empty($branch_select)) {
                    foreach ($branch_select as $branch_id) {
                        if (!is_numeric($branch_id)) {
                            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                        }
                        TmsSaleRoomUser::firstOrCreate([
                            'sale_room_id' => $branch_id,
                            'user_id' => $mdlUser->id,
                            'type' => TmsSaleRoomUser::AGENTS
                        ]);
                    }
                }
            }
            if ($saleroom_select) { //Thêm điểm bán
                $saleroom_select = explode(',', $saleroom_select);
                if (!empty($saleroom_select)) {
                    foreach ($saleroom_select as $sale_room_id) {
                        if (!is_numeric($sale_room_id)) {
                            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                        }
                        TmsSaleRoomUser::firstOrCreate([
                            'sale_room_id' => $sale_room_id,
                            'user_id' => $mdlUser->id,
                            'type' => TmsSaleRoomUser::POS
                        ]);
                    }
                }
            }

            //Thêm vị trí ( Khung năng lực )
            if ($trainning_id > 0) {
                TmsTrainningUser::where([
                    'user_id' => $mdlUser->id
                ])->delete();
                TmsTrainningUser::create([
                    'trainning_id' => $trainning_id,
                    'user_id' => $mdlUser->id
                ]);
                //$category = TmsTrainningCategory::select('category_id')->where('trainning_id', $trainning_id)->first();
                //enrole cho học viên theo khung năng lực
                //\DB::beginTransaction();
                training_enrole($mdlUser->id, $trainning_id);
                //\DB::commit();
            }

            //Không phải student thì xóa enrole
            /*if(!$checkStudent){
                DB::table('mdl_user_enrolments')
                    ->where('userid','=',$user_id)
                    ->delete();
            }*/

            $user = TmsUserDetail::where('user_id', $user_id)->first();
            $old_confirm = $user->confirm;

            $user->cmtnd = $cmtnd;
            $user->fullname = $fullname;
            $timestamp = strtotime($dob);
            if ($timestamp === FALSE) {
                $timestamp = strtotime(str_replace('/', '-', $dob));
            }
            $user->dob = $timestamp;
            $user->email = $email;
            $user->phone = ($phone && $phone != 'null' && $phone != 'NULL') ? $phone : '';
            $user->address = ($address && $address != 'null') ? $address : '';

            $user->sex = $sex;
            $user->code = $code ? $code : '';
            $start_time = strtotime($start_time);
            if ($start_time) {
                if ($start_time === FALSE) {
                    $start_time = strtotime(str_replace('/', '-', $start_time));
                }
                $user->start_time = $start_time;
            }
            $user->working_status = $working_status;
            $user->confirm_address = $confirm_address ? $confirm_address : 0;
            $user->confirm = $confirm ? $confirm : 0;
            if ($old_confirm == 0 && $confirm == 1) {
                $user->confirm_time = time();
            }

            if ($avatar) {
                $image_path = $user['avatar'];
                //                if ($image_path) {
                //                    $filename = public_path() . $image_path;
                //                    \File::delete($filename);
                //                }
                $name = time() . '.' . $avatar->getClientOriginalExtension();
                //                $destinationPath = public_path('/upload/user/');
                //                $avatar->move($destinationPath, $name);

                if ($image_path) {
                    //                    $filename = public_path() . $image_path;
                    //                    \File::delete($filename);
                    if (file_exists(storage_path($image_path))) {
                        Storage::delete($image_path);
                    }
                }


                Storage::putFileAs(
                    'public/upload/user',
                    $avatar,
                    $name
                );

                $path_avatar = '/storage/upload/user/' . $name;

                $user->avatar = $path_avatar;

                //                $user->avatar = '/upload/user/' . $name;
            }
            $user->save();

            devcpt_log_system('user', '/system/user/edit/' . $mdlUser['id'], 'update', $infoLog);

            if (strlen($certificate_code) != 0) {
                $student_certificate = StudentCertificate::where('userid', $user_id)->first();
                if (!isset($student_certificate)) {
                    $student_certificate = new StudentCertificate();
                }
                $student_certificate->userid = $mdlUser->id;
                $student_certificate->code = $certificate_code;
                if (strlen($certificate_date) != 0) {
                    $student_certificate->timecertificate = strtotime($certificate_date);
                } else {
                    $student_certificate->timecertificate = time();
                }
                $student_certificate->status = 1;
                $student_certificate->save();
            } else {
                StudentCertificate::where('userid', $user_id)->delete();
            }

            \DB::commit();
            return response()->json(status_message('success', __('cap_nhat_tai_khoan_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apidelete($user_id)
    {
        try {
            if (!$user_id || !is_numeric($user_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            \DB::beginTransaction();
            devcpt_delete_user($user_id);
            \DB::commit();
            return response()->json(status_message('success', __('xoa_tai_khoan_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apideleteListUser(Request $request)
    {
        try {
            $user_delete = $request->input('user_delete');

            \DB::beginTransaction();
            foreach ($user_delete as $user_id) {
                devcpt_delete_user($user_id);
            }
            \DB::commit();
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function apiImportUser(Request $request)
    {
        if (!$request->file('file')) {
            return response()->json([
                'fileError' => 'error'
            ]);
        }
        $path = $request->file('file')->getRealPath();
        $extension = $request->file('file')->getClientOriginalExtension();

        $array = (new UsersImport)->toArray($request->file('file'), '', '');
        if ($extension != 'xls' && $extension != 'xlsx') {
            return response()->json([
                'extension' => 'error'
            ]);
        }

        $this->role_name = $request->input('role_name');
        $this->importType = $request->input('importType');

        $this->importOutput['rowSuccess'] = 0;
        $this->importOutput['rowError'] = 0;
        $this->importOutput['userOuput'] = [];
        foreach ($array as &$row) {
            if (!empty($row) && count($row) > 1) {
                //loại bỏ hàng đầu tiên
                $first_element = array_shift($row);
                foreach ($row as $item) {
                    $user = array_combine($first_element, $item);
                    $userOuput = [];
                    try {

                        //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                        if (strpos($user['dob'], '/') !== false) {
                            $newDate = strtotime(str_replace('/', '-', $user['dob']));
                        } else if (strpos($user['dob'], '-') !== false) {
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
                        $user['sex'] = $user['sex'] == 'M' or $user['sex'] == 1 ? 1 : 0;
                        //
                        $userCheck = TmsUserDetail::select('user_id');
                        if (!$user['email'] || !$user['cmtnd'] || !$user['username'] || !$user['fullname']) {
                            if (!$user['email'] && !$user['cmtnd'] && !$user['username'] && !$user['fullname'] && !$user['confirm'] && !$user['phone'] && !$user['address'] && !$user['dob'] && !$user['sex']) {
                            } else {
                                $this->importOutput['rowError']++;
                                $userOuput['username'] = $user['username'] ? $user['username'] : '';
                                $userOuput['status'] = 'error';
                                $userOuput['message'] = __('thong_tin') . ' : ';

                                if (!$user['username']) {
                                    $userOuput['message'] .= __('tai_khoan') . ' , ';
                                }
                                if (!$user['email']) {
                                    $userOuput['message'] .= 'Email , ';
                                }
                                if (!$user['cmtnd']) {
                                    $userOuput['message'] .= __('so_cmtnd') . ' , ';
                                }
                                if (!$user['fullname']) {
                                    $userOuput['message'] .= __('ho_va_ten') . ' ';
                                }
                                $userOuput['message'] .= __('khong_duoc_de_trong');
                                array_push($this->importOutput['userOuput'], $userOuput);
                            }
                        } //kiểm tra nếu tồn tại các trường và các trường đó không phải là số => sai định dạng
                        else if (($user['confirm'] && !is_numeric($user['confirm'])) ||
                            ($user['cmtnd'] && !is_numeric($user['cmtnd'])) ||
                            ($user['phone'] && !is_numeric($user['phone']))
                        ) {
                            //                            !is_numeric($user['confirm']) || !is_numeric($user['cmtnd'])
                            $userOuput['username'] = $user['username'] ? $user['username'] : '';
                            $userOuput['status'] = 'error';
                            $userOuput['message'] = __('thong_tin') . ' : ';
                            if (!is_numeric($user['confirm'])) {
                                $userOuput['message'] .= 'confirm , ';
                            }
                            if (!is_numeric($user['cmtnd'])) {
                                $userOuput['message'] .= __('so_cmtnd') . ' , ';
                            }
                            if (!is_numeric($user['phone'])) {
                                $userOuput['message'] .= __('so_dien_thoai') . ' , ';
                            }
                            $userOuput['message'] .= __('sai_dinh_dang');
                            array_push($this->importOutput['userOuput'], $userOuput);
                        } //kiểm tra nếu tồn tại trường dob mà giá trị sau khi gán = 0 => không hợp lệ
                        else if ($user['dob'] && $timestamp == 0) {
                            $userOuput['username'] = $user['username'] ? $user['username'] : '';
                            $userOuput['status'] = 'error';
                            $userOuput['message'] = __('thong_tin') . ' : dob ' . __('sai_dinh_dang');
                            array_push($this->importOutput['userOuput'], $userOuput);
                        } else {
                            $nameExpl = explode(' ', $user['fullname']);
                            $rowname = count($nameExpl);
                            $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
                            $lastname = str_replace($nameExpl[$rowname - 1], '', $user['fullname']);
                            $lastname = $lastname ? $lastname : '';

                            $checkUsername = MdlUser::where('username', $user['username'])->first();
                            $checkEmail = TmsUserDetail::select('user_id')->where('email', $user['email'])->first();
                            $checkCmtnd = TmsUserDetail::select('user_id')->where('cmtnd', $user['cmtnd'])->first();
                            if ($checkUsername || $checkEmail || $checkCmtnd) {
                                if ($checkUsername) {
                                    if ($this->importType == 1) {
                                        $checkEmail = TmsUserDetail::select('user_id')->where('email', $user['email'])->whereNotIn('user_id', [$checkUsername['id']])->first();
                                        $checkCmtnd = TmsUserDetail::select('user_id')->where('cmtnd', $user['cmtnd'])->whereNotIn('user_id', [$checkUsername['id']])->first();

                                        if ($checkEmail || $checkCmtnd) {
                                            $this->importOutput['rowError']++;
                                            $userOuput['username'] = $user['username'];
                                            $userOuput['status'] = 'error';
                                            $userOuput['message'] = __('thong_tin') . ' : ';
                                            if ($checkEmail) {
                                                $userOuput['message'] .= 'Email , ';
                                            }
                                            if ($checkCmtnd) {
                                                $userOuput['message'] .= __('so_cmtnd') . ' ';
                                            }
                                            $userOuput['message'] .= __('da_ton_tai') . '.';
                                            array_push($this->importOutput['userOuput'], $userOuput);
                                        } else {
                                            \DB::beginTransaction();
                                            $checkUsername->redirect_type = 'lms';
                                            $checkUsername->firstname = $firstname;
                                            $checkUsername->lastname = $lastname;
                                            $checkUsername->email = $user['email'];
                                            $checkUsername->save();

                                            $tmsUser = TmsUserDetail::where('user_id', $checkUsername['id'])->first();
                                            $tmsUser->cmtnd = $user['cmtnd'];
                                            $tmsUser->fullname = $user['fullname'];
                                            $tmsUser->dob = $timestamp;
                                            $tmsUser->email = $user['email'];
                                            $tmsUser->phone = $user['phone'];
                                            $tmsUser->address = $user['address'];
                                            $tmsUser->sex = $user['sex'] ? $user['sex'] : 1;
                                            $tmsUser->confirm = $user['confirm'] ? $user['confirm'] : 0;
                                            $tmsUser->user_id = $checkUsername['id'];
                                            $tmsUser->save();

                                            $this->importOutput['rowSuccess']++;
                                            $userOuput['username'] = $user['username'];
                                            $userOuput['status'] = 'success';
                                            $userOuput['message'] = __('import_user_thanh_cong');
                                            array_push($this->importOutput['userOuput'], $userOuput);

                                            devcpt_log_system('user', '/system/user/edit/' . $checkUsername['id'], 'update', 'Import Update User: ' . $user['username']);
                                            \DB::commit();
                                        }
                                    } else {
                                        $this->importOutput['rowError']++;
                                        $userOuput['username'] = $user['username'];
                                        $userOuput['status'] = 'error';
                                        $userOuput['message'] = __('thong_tin') . ' ';
                                        if ($checkUsername) {
                                            $userOuput['message'] .= __('tai_khoan') . ' , ';
                                        }
                                        if ($checkEmail) {
                                            $userOuput['message'] .= 'Email , ';
                                        }
                                        if ($checkCmtnd) {
                                            $userOuput['message'] .= __('so_cmtnd') . ' ';
                                        }
                                        $userOuput['message'] .= __('da_ton_tai') . '.';
                                        array_push($this->importOutput['userOuput'], $userOuput);
                                    }
                                } else {
                                    $this->importOutput['rowError']++;
                                    $userOuput['username'] = $user['username'];
                                    $userOuput['status'] = 'error';
                                    $userOuput['message'] = __('thong_tin') . ' ';
                                    if ($checkUsername) {
                                        $userOuput['message'] .= __('tai_khoan') . ' , ';
                                    }
                                    if ($checkEmail) {
                                        $userOuput['message'] .= 'Email , ';
                                    }
                                    if ($checkCmtnd) {
                                        $userOuput['message'] .= __('so_cmtnd') . ' ';
                                    }
                                    $userOuput['message'] .= __('da_ton_tai') . '.';
                                    array_push($this->importOutput['userOuput'], $userOuput);
                                }
                            } else {
                                \DB::beginTransaction();
                                $role = Role::select('id', 'name', 'mdl_role_id')->where('name', $this->role_name)->first();
                                $mdlUser = new MdlUser;
                                $mdlUser->username = $user['username'];
                                $mdlUser->password = bcrypt('Bgt@2019');
                                $mdlUser->redirect_type = 'lms';
                                $mdlUser->firstname = $firstname;
                                $mdlUser->lastname = $lastname;
                                $mdlUser->email = $user['email'];
                                $mdlUser->save();

                                if ($role) {
                                    add_user_by_role($mdlUser->id, $role['id']);
                                    enrole_lms($mdlUser->id, $role['mdl_role_id'], $user['confirm'] ? $user['confirm'] : 0);
                                }

                                $tmsUser = new TmsUserDetail;
                                $tmsUser->cmtnd = $user['cmtnd'];
                                $tmsUser->fullname = $user['fullname'];
                                $tmsUser->dob = $timestamp;
                                $tmsUser->email = $user['email'];
                                $tmsUser->phone = $user['phone'];
                                $tmsUser->address = $user['address'];
                                $tmsUser->sex = $user['sex'] ? $user['sex'] : 1;
                                $tmsUser->confirm = $user['confirm'] ? $user['confirm'] : 0;
                                $tmsUser->user_id = $mdlUser->id;
                                $tmsUser->save();

                                $this->importOutput['rowSuccess']++;
                                $userOuput['username'] = $user['username'];
                                $userOuput['status'] = 'success';
                                $userOuput['message'] = 'Import thành công.';
                                array_push($this->importOutput['userOuput'], $userOuput);

                                devcpt_log_system('user', '/system/user/edit/' . $mdlUser->id, 'create', 'Import User: ' . $user['username']);
                                \DB::commit();
                            }
                        }
                    } catch (\Exception $e) {
                        $this->importOutput['rowError']++;
                        $userOuput['username'] = $user['username'];
                        $userOuput['status'] = 'error';
                        $userOuput['message'] = __('gap_loi_khi_import');
                        array_push($this->importOutput['userOuput'], $userOuput);
                    }
                }
            }
        }

        return response()->json($this->importOutput);
    }

    //import excel multip sheets
    public function apiImportExcel(Request $request)
    {
        set_time_limit(0);
        if (!$request->file('file')) {
            return response()->json([
                'fileError' => 'error'
            ]);
        }

        $extension = $request->file('file')->getClientOriginalExtension();

        $array = (new UsersImport)->toArray($request->file('file'), '', '');
        if ($extension != 'xls' && $extension != 'xlsx') {
            return response()->json([
                'extension' => 'error'
            ]);
        }

        $countError = 0;

        $this->importOutput['userOuput'] = [];

        $listEmployees = $array[0];

        $this->importOutput['rowSuccess'] = 0;
        $this->importOutput['rowError'] = 0;

        $this->role_name = $request->input('role_name');
        $this->importType = $request->input('importType');
        //insert nhân viên
        //loại bỏ 2 hàng đầu tiên
        array_shift($listEmployees);
        array_shift($listEmployees);
        $stt = 1;
        //nếu là quyền chuyên viên kinh doanh
        if ($this->role_name == 'managemarket') {

            foreach ($listEmployees as $row) {
                $user = [];
                $userOuput = [];
                $checkTrue = true;
                try {
                    if (!empty($stt)) {
                        //tên đăng nhập
                        $user['username'] = $row[1];
                        //mật khẩu
                        $user['password'] = $row[2];
                        //tên đầy đủ
                        $user['fullname'] = $row[3];
                        //email
                        $user['email'] = $row[4];
                        //cmtnd
                        $user['cmtnd'] = str_replace(' ', '', $row[5]);
                        //danh sách đại lý
                        $listAgencies = $row[6];
                        //Ngày tháng năm sinh
                        $user['dob'] = $row[7];
                        //Số điện thoại
                        $user['phone'] = str_replace(["''", "'"], '', "" . $row[8] . "");
                        //Địa chỉ thường trú
                        $user['address'] = $row[9];
                        //Giới tính
                        $user['sex'] = $row[10] == 'Nam' or $row[10] == 'nam' ? 1 : 0;
                        //Ngày bắt đầu làm
                        $user['start_date'] = $row[11];
                        //Tình trạng làm việc
                        $user['working_status'] = ($row[12] == 1) ? 1 : 0;

                        //Kiểm tra nếu cột ngày tháng năm mà là dạng ngoài general (date, custom, ...) -> convert sang datetime
                        //ngày tháng năm sinh
                        if (is_numeric($user['dob'])) {
                            $getDate = Date::excelToDateTimeObject($row[10]);
                            $user['dob'] = $getDate->format('d-m-Y');
                        }
                        //Ngày bắt đầu làm
                        if (is_numeric($user['start_date'])) {
                            $getDateW = Date::excelToDateTimeObject($row[10]);
                            $user['start_date'] = $getDateW->format('d-m-Y');
                        }

                        //biến lỗi
                        $userOuput['username'] = $user['username'];
                        $userOuput['fullname'] = $user['fullname'];
                        $userOuput['message'] = 'Thông tin ';
                        $userOuput['stt'] = $stt;
                        $userOuput['status'] = '';
                        //                __('thong_tin').' :
                        //kiểm tra nếu tên đầy đủ hoặc chứng minh thư nhân dân mà trống -> bắt nhập lại
                        if (empty($user['fullname']) || empty($user['cmtnd'])) {
                            if (empty($user['fullname'])) {
                                $userOuput['message'] .= __('ten_day_du_khong_duoc_de_trong') . '.';
                            }
                            if (empty($user['cmtnd'])) {
                                $userOuput['message'] .= __('so_chung_minh_thu_nhan_dan_khong_duoc_de_trong') . '.';
                            }
                            $userOuput['status'] = 'error';
                            $checkTrue = false;
                            array_push($this->importOutput['userOuput'], $userOuput);
                        } else {
                            //validate mật khẩu
                            if (!empty($user['password']) && !validate_password_func($user['password'])) {
                                $userOuput['status'] = 'error';
                                $checkTrue = false;
                                $userOuput['message'] .= __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet') . '.';
                            }

                            //validate số cmtnd và số điện thoại
                            if ((!empty($user['cmtnd']) && !is_numeric($user['cmtnd'])) || (!empty($user['phone']) && !is_numeric($user['phone']))) {
                                if (!empty($user['cmtnd']) && !is_numeric($user['cmtnd'])) {
                                    $userOuput['message'] .= __('so_chung_minh_thu_nhan_dan_khong_dung_dinh_dang') . '.';
                                }
                                if (!empty($user['phone']) && !is_numeric($user['phone'])) {
                                    $userOuput['message'] .= __('so_dien_thoai_khong_dung_dinh_dang') . '.';
                                }
                                $userOuput['status'] = 'error';
                                $checkTrue = false;
                            }

                            //validate ngày tháng năm sinh
                            $timestamp = 0;
                            //                    $userOuput['status'] = 'error';
                            //                    $userOuput['message'] .= __('Ngay_thang_nam_sinh_khong_dung_dinh_dang').'.';
                            if (!empty($user['dob']) && strlen($user['dob']) > 7) {
                                //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                                if (strpos($user['dob'], '/') !== false) {
                                    $newDate = strtotime(str_replace('/', '-', $user['dob']));
                                } else if (strpos($user['dob'], '-') !== false) {
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


                            //Nếu email là rỗng -> lấy tên đăng nhập + @gmail.com
                            if (empty($user['email'])) {
                                $user['email'] = $user['username'] . '@gmail.com';
                            } else {
                                if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                                    $checkTrue = false;
                                    $userOuput['status'] = 'error';
                                    $userOuput['message'] .= __('email_sai_dinh_dang') . '.';
                                }
                            }
                            //Nếu tên đăng nhập là rỗng thì lấy thên đầy đủ viết liền không dấu
                            if (empty($user['username'])) {
                                $user['username'] = empty($user['username']) ? $this->vn_to_str($user['fullname']) : $row[4];
                            }


                            //Nếu mật khẩu rỗng -> mật khẩu mặc định là Vietlott@123
                            $user['password'] = empty($user['password']) ? 'Vietlott@123' : $user['password'];

                            //check thỏa mãn ngày tháng năm
                            if ($user['dob'] && $timestamp == 0) {
                                $checkTrue = false;
                                $userOuput['status'] = 'error';
                                $userOuput['message'] .= __('ngay_thang_nam_sinh_sai_dinh_dang') . '.';
                            }
                            if ($user['start_date'] && $timestamp_start == 0) {
                                $checkTrue = false;
                                $userOuput['status'] = 'error';
                                $userOuput['message'] .= __('ngay_thang_nam_lam_viec_sai_dinh_dang') . '.';
                            }
                            //nếu thỏa mãn thì thêm
                            if ($checkTrue) {
                                //lấy danh sách đại lý
                                $checkAgencyTM = true;
                                $agencies = explode(",", $listAgencies);
                                $messageAgency = 'Mã đại lý: ';
                                if ($agencies) {
                                    foreach ($agencies as $agency) {
                                        $checkAgency = TmsBranch::where('code', '=', str_replace(' ', '', $agency))->first();
                                        if (!$checkAgency) {
                                            $userOuput['status'] = 'error';
                                            $checkAgencyTM = false;
                                            $messageAgency .= $agency . ', ';
                                        }
                                    }
                                    $messageAgency .= __('khong_ton_tai') . '.';
                                }

                                if ($checkAgencyTM) {
                                    \DB::beginTransaction();
                                    $resultCreateUser = $this->CreateUser(
                                        $this->role_name,
                                        $user['username'],
                                        $user['password'],
                                        $user['email'],
                                        0,
                                        $user['cmtnd'],
                                        $user['fullname'],
                                        $user['phone'],
                                        null,
                                        $user['address'],
                                        $user['sex'],
                                        $timestamp,
                                        $timestamp_start,
                                        $user['working_status']
                                    );
                                    $resultType = $resultCreateUser['type'];
                                    $resultIdUser = $resultCreateUser['id'];
                                    $resultUserName = $resultCreateUser['username'];
                                    if ($resultIdUser == 0) {
                                        $userOuput['username'] = $resultUserName;
                                        $userOuput['fullname'] = $user['fullname'];
                                        $userOuput['status'] = 'error';
                                        $userOuput['message'] = __('xay_ra_loi_khi_them') . '.';
                                    } else {
                                        //thêm hoặc update thành công
                                        //
                                        $roles = Role::select('id', 'name', 'mdl_role_id')->where('name', 'managemarket')->get()->toArray();
                                        if ($roles) {
                                            $mdlUser = MdlUser::where('id', $resultIdUser)->first();
                                            foreach ($roles as $role) {
                                                $mdlUser->update(['redirect_type' => 'default']);
                                                //endrole
                                                add_user_by_role($mdlUser->id, $role['id']);
                                                enrole_lms($mdlUser->id, $role['mdl_role_id'], 0);
                                            }
                                        }
                                        //gán quyền đại lý cho chuyên viên kinh doanh
                                        if ($agencies) {
                                            foreach ($agencies as $agency) {
                                                $role_organize = new TmsRoleOrganize;
                                                $checkAgency = TmsBranch::where('code', '=', str_replace(' ', '', $agency))->first();
                                                $role_organize->user_id = $mdlUser->id;
                                                $role_organize->organize_id = $checkAgency->id;
                                                $role_organize->type = 'branch';
                                                $role_organize->save();
                                            }
                                        }

                                        $userOuput['username'] = $resultUserName;
                                        $userOuput['fullname'] = $user['fullname'];
                                        $userOuput['status'] = 'success';
                                        if ($resultType == 'add')
                                            $userOuput['message'] = __('them_thanh_cong') . '.';
                                        else
                                            $userOuput['message'] = __('cap_nhat_thanh_cong') . '.';
                                    }
                                    \DB::commit();
                                } else {
                                    $userOuput['message'] .= $messageAgency;
                                }
                            }
                            if ($userOuput['status'] == 'error')
                                $this->importOutput['rowError']++;
                            else if ($userOuput['status'] == 'success')
                                $this->importOutput['rowSuccess']++;
                            array_push($this->importOutput['userOuput'], $userOuput);
                        }
                    }
                } catch (\Exception $e) {
                    $countError++;
                    $userOuput['username'] = '';
                    $userOuput['fullname'] = $user['fullname'];
                    $userOuput['password'] = '';
                    $userOuput['status'] = 'error';
                    $userOuput['stt'] = $row[0];
                    $userOuput['cmtnd'] = $user['cmtnd'];
                    $userOuput['message'] = $e->getMessage();
                    //                $userOuput['message'] = 'Lỗi dữ liệu, kiểm tra lại';
                    array_push($this->importOutput['userOuput'], $userOuput);
                }
                $stt++;
            }
        } else {
            // $stt = 1;
            foreach ($listEmployees as $row) {
                $user = [];
                $userOuput = [];
                $checkTrue = true;
                try {
                    if (!empty($stt)) {
                        //mã nhân viên
                        $user['code'] = $row[1];
                        //mã giấy chứng nhân
                        $user['certificate_code'] = $row[2];
                        //nơi cấp giấy chứng nhận: mã đại lý
                        $user['agency_code'] = $row[3];
                        //tên đăng nhập
                        $user['username'] = $row[4];
                        //mật khẩu
                        $user['password'] = $row[5];
                        //tên đầy đủ
                        $user['fullname'] = $row[6];
                        //email
                        $user['email'] = $row[7];
                        //cmtnd
                        $user['cmtnd'] = str_replace(' ', '', $row[8]);
                        //đơn vị quản lý
                        $managementCode = $row[9];
                        //Ngày tháng năm sinh
                        $user['dob'] = $row[10];
                        //Số điện thoại
                        $user['phone'] = str_replace(["''", "'"], '', "" . $row[11] . "");
                        //Địa chỉ thường trú
                        $user['address'] = $row[12];
                        //Giới tính
                        $user['sex'] = $row[13] == 'Nam' or $row[13] == 'nam' ? 1 : 0;
                        //Ngày bắt đầu làm
                        $user['start_date'] = $row[14];
                        //Tình trạng làm việc
                        $user['working_status'] = ($row[15] == 1) ? 1 : 0;
                        $checkNumber = 0;

                        //Kiểm tra nếu cột ngày tháng năm mà là dạng ngoài general (date, custom, ...) -> convert sang datetime
                        //ngày tháng năm sinh
                        if (is_numeric($user['dob'])) {
                            $getDate = Date::excelToDateTimeObject($row[10]);
                            $user['dob'] = $getDate->format('d-m-Y');
                        }
                        //Ngày bắt đầu làm
                        if (is_numeric($user['start_date'])) {
                            $getDateW = Date::excelToDateTimeObject($row[10]);
                            $user['start_date'] = $getDateW->format('d-m-Y');
                        }

                        //biến lỗi
                        $userOuput['username'] = $user['username'];
                        $userOuput['fullname'] = $user['fullname'];
                        $userOuput['message'] = 'Thông tin ';
                        $userOuput['stt'] = $stt;
                        //kiểm tra nếu tên đầy đủ hoặc chứng minh thư nhân dân mà trống -> bắt nhập lại
                        if (empty($user['fullname']) || empty($user['cmtnd'])) {
                            if (empty($user['fullname'])) {
                                $userOuput['message'] .= __('ten_day_du_khong_duoc_de_trong') . '.';
                            }
                            if (empty($user['cmtnd'])) {
                                $userOuput['message'] .= __('so_chung_minh_thu_nhan_dan_khong_duoc_de_trong') . '.';
                            }
                            $userOuput['status'] = 'error';
                            $checkTrue = false;
                            array_push($this->importOutput['userOuput'], $userOuput);
                        } else {
                            //validate mật khẩu
                            if (!empty($user['password']) && !validate_password_func($user['password'])) {
                                $userOuput['status'] = 'error';
                                $checkTrue = false;
                                $userOuput['message'] .= __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet') . '.';
                            }

                            //validate số cmtnd và số điện thoại
                            if ((!empty($user['cmtnd']) && !is_numeric($user['cmtnd'])) || (!empty($user['phone']) && !is_numeric($user['phone']))) {
                                if (!empty($user['cmtnd']) && !is_numeric($user['cmtnd'])) {
                                    $userOuput['message'] .= __('so_chung_minh_thu_nhan_dan_khong_dung_dinh_dang') . '.';
                                }
                                if (!empty($user['phone']) && !is_numeric($user['phone'])) {
                                    $userOuput['message'] .= __('so_dien_thoai_khong_dung_dinh_dang') . '.';
                                }
                                $userOuput['status'] = 'error';
                                $checkTrue = false;
                            }

                            //validate ngày tháng năm sinh
                            $timestamp = 0;
                            if (!empty($user['dob']) && strlen($user['dob']) > 7) {
                                //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                                if (strpos($user['dob'], '/') !== false) {
                                    $newDate = strtotime(str_replace('/', '-', $user['dob']));
                                } else if (strpos($user['dob'], '-') !== false) {
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


                            //Nếu email là rỗng -> lấy tên đăng nhập + @gmail.com
                            if (empty($user['email'])) {
                                $user['email'] = $user['username'] . '@gmail.com';
                            } else {
                                if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                                    $checkTrue = false;
                                    $userOuput['status'] = 'error';
                                    $userOuput['message'] .= __('email_sai_dinh_dang') . '.';
                                }
                            }
                            //Nếu tên đăng nhập là rỗng thì lấy thên đầy đủ viết liền không dấu
                            if (empty($user['username'])) {
                                $user['username'] = empty($user['username']) ? $this->vn_to_str($user['fullname']) : $row[4];
                            }

                            //Nếu có mã nhân viên mà k có mã giấy chứng nhận -> mã giấy chứng nhận là mã nhân viên
                            $certificateCode = empty($row[2]) ? $row[1] : $row[2];
                            //confirm: 0 là chưa có gcn, 1 là đã có
                            $user['confirm'] = empty($certificateCode) ? 0 : 1;

                            //Nếu mật khẩu rỗng -> mật khẩu mặc định là Vietlott@123
                            $user['password'] = empty($user['password']) ? 'Vietlott@123' : $user['password'];

                            //check thỏa mãn ngày tháng năm
                            if ($user['dob'] && $timestamp == 0) {
                                $checkTrue = false;
                                $userOuput['status'] = 'error';
                                $userOuput['message'] .= __('ngay_thang_nam_sinh_sai_dinh_dang') . '.';
                            }
                            if ($user['start_date'] && $timestamp_start == 0) {
                                $checkTrue = false;
                                $userOuput['status'] = 'error';
                                $userOuput['message'] .= __('ngay_thang_nam_lam_viec_sai_dinh_dang') . '.';
                            }
                            //nếu thỏa mãn thì thêm
                            if ($checkTrue) {
                                $resultCreateUser = $this->CreateUser(
                                    $this->role_name,
                                    $user['username'],
                                    $user['password'],
                                    $user['email'],
                                    $user['confirm'],
                                    $user['cmtnd'],
                                    $user['fullname'],
                                    $user['phone'],
                                    $user['code'],
                                    $user['address'],
                                    $user['sex'],
                                    $timestamp,
                                    $timestamp_start,
                                    $user['working_status']
                                );
                                $resultType = $resultCreateUser['type'];
                                $resultIdUser = $resultCreateUser['id'];
                                $resultUserName = $resultCreateUser['username'];
                                if ($resultIdUser == 0) {
                                    $userOuput['username'] = $resultUserName;
                                    $userOuput['fullname'] = $user['fullname'];
                                    $userOuput['status'] = 'error';
                                    $userOuput['message'] = __('xay_ra_loi_khi_them') . '.';
                                } else {
                                    //thêm thành công
                                    //kiểm tra nếu có mã chứng nhận thì thêm mã chứng nhận
                                    if (!empty($certificateCode)) {
                                        $student = StudentCertificate::where('userid', $resultIdUser)->first();
                                        //nếu học viên đã có mã thì không làm gì cả
                                        if (!$student) {
                                            //update status to 1
                                            StudentCertificate::create([
                                                'userid' => $resultIdUser,
                                                'code' => $certificateCode,
                                                'status' => 1,
                                                'timecertificate' => time()
                                            ]);
                                        }
                                    }

                                    //
                                    //kiểm tra mã đơn vị quản lý
                                    //kiểm tra trong điểm bán
                                    if (!empty($managementCode)) {
                                        $checkSaleRoomUser = TmsSaleRooms::where('code', '=', $managementCode)->first();
                                        if (!empty($checkSaleRoomUser)) {
                                            $this->CreateSaleRoomUser($checkSaleRoomUser->id, $resultIdUser, TmsSaleRoomUser::POS);
                                        } else {
                                            $checkBranchUser = TmsBranch::where('code', '=', $managementCode)->first();
                                            if (!empty($checkBranchUser)) {
                                                $this->CreateSaleRoomUser($checkBranchUser->id, $resultIdUser, TmsSaleRoomUser::AGENTS);
                                            }
                                        }
                                    }
                                    $userOuput['username'] = $resultUserName;
                                    $userOuput['fullname'] = $user['fullname'];
                                    $userOuput['status'] = 'success';
                                    if ($resultType == 'add')
                                        $userOuput['message'] = __('them_thanh_cong') . '.';
                                    else
                                        $userOuput['message'] = __('cap_nhat_thanh_cong') . '.';
                                }
                            } else {
                            }
                            if ($userOuput['status'] == 'error')
                                $this->importOutput['rowError']++;
                            else if ($userOuput['status'] == 'success')
                                $this->importOutput['rowSuccess']++;
                            array_push($this->importOutput['userOuput'], $userOuput);
                        }
                    }
                } catch (\Exception $e) {
                    $countError++;
                    $userOuput['username'] = '';
                    $userOuput['fullname'] = $user['fullname'];
                    $userOuput['password'] = '';
                    $userOuput['status'] = 'error';
                    $userOuput['stt'] = $row[0];
                    $userOuput['cmtnd'] = $user['cmtnd'];
                    $userOuput['message'] = $e->getMessage();
                    array_push($this->importOutput['userOuput'], $userOuput);
                }
                $stt++;
            }
        }


        return response()->json($this->importOutput);
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

    public function CreateUser(
        $role_name,
        $username,
        $password,
        $email,
        $confirm,
        $cmtnd,
        $fullname,
        $phone,
        $code,
        $address,
        $sex,
        $timestamp,
        $start_date,
        $working_status
    )
    {
        $userOuput = [];
        $newUserId = 0;
        $userOuput['type'] = '';
        $userOuput['id'] = 0;
        $userOuput['username'] = '';
        try {
            $usernameNew = $username;
            $nameExpl = explode(' ', $fullname);
            $rowname = count($nameExpl);
            $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
            $lastname = str_replace($nameExpl[$rowname - 1], '', $fullname);
            $lastname = $lastname ? $lastname : '';

            $checkUsername = MdlUser::where('username', 'like', "{$username}%")->get();
            $checkedUsers = [];

            \DB::beginTransaction();

            if (count($checkUsername) > 0) {
                foreach ($checkUsername as $user) {
                    $getTMSUser = TmsUserDetail::where('user_id', $user->id)->first();
                    if ($getTMSUser->cmtnd == $cmtnd) {
                        $newUserId = $user->id;
                        $checkedUsers = [];
                        break;
                    } else {
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
                    $userGetTms->start_time = $start_date;
                    $userGetTms->save();

                    devcpt_log_system('user', '/system/user/edit/' . $newUserId, 'update', 'Import Update User: ' . $username);

                    $userOuput['type'] = 'update';
                    $userOuput['username'] = $username;
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
                        $password,
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
                        $timestamp,
                        $start_date,
                        $working_status
                    );
                    $userOuput['username'] = $usernameNew;
                    $userOuput['type'] = 'add';
                }
            } else {
                //                    thêm mới user
                $newUserId = $this->createUserOrg(
                    $usernameNew,
                    $password,
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
                    $timestamp,
                    $start_date,
                    $working_status
                );
                $userOuput['username'] = $usernameNew;
                $userOuput['type'] = 'add';
            }

            if ($newUserId) {
                //add user vao khung nang luc chung chi trong he thong (day la khung nang luc bat buoc)
                //                $trainning = new TmsTrainningUser();
                //                $trainning->user_id = $newUserId;
                //                $trainning->trainning_id = 1; //id khung nang luc bat buoc
                //                $trainning->save();

                $trainning = TmsTrainningUser::firstOrCreate([
                    'user_id' => $newUserId,
                    'trainning_id' => 1 //id khung nang luc bat buoc
                ]);

                sleep(0.01);

                //$category = TmsTrainningCategory::select('category_id')->where('trainning_id', $trainning->id)->first();

                training_enrole($newUserId, 1);
            }


            \DB::commit();
            $userOuput['status'] = 'success';
            $userOuput['id'] = $newUserId;
        } catch (\Exception $e) {
            \DB::rollBack();
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
                $userOuput['type'] = 'error_name';
            else
                $userOuput['type'] = $e->getMessage();
            $userOuput['id'] = 0;
        }
        return $userOuput;
    }

    public function createUserOrg($usernameNew, $password, $firstname, $lastname, $email, $role_name, $confirm, $cmtnd, $fullname, $phone, $code, $address, $sex, $timestamp, $start_date, $working_status)
    {
        $role = Role::select('id', 'name', 'mdl_role_id')->where('name', $role_name)->first();
        $mdlUser = new MdlUser;
        $mdlUser->username = $usernameNew;
        $mdlUser->password = bcrypt($password);
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
        $tmsUser->start_time = $start_date;
        $tmsUser->save();

        return $mdlUser->id;
    }

    public function CreateSaleRoomUser($managementId, $user_id, $type)
    {
        try {
            \DB::beginTransaction();
            $saleRoomUser = new TmsSaleRoomUser;
            $saleRoomUser->sale_room_id = $managementId;
            $saleRoomUser->user_id = $user_id;
            $saleRoomUser->type = $type;
            $saleRoomUser->save();
            \DB::commit();
            return [
                'code' => $saleRoomUser->id,
                'message' => 'Thêm thành công'
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            return [
                'code' => 0,
                'message' => 'Lỗi dữ liệu, kiểm tra lại'
            ];
        }
    }

    public function apiImportTeacher(Request $request)
    {
        if (!$request->file('file')) {
            return response()->json([
                'fileError' => 'error'
            ]);
        }

        $path = $request->file('file')->getRealPath();
        $extension = $request->file('file')->getClientOriginalExtension();

        $array = (new UsersImport)->toArray($request->file('file'), '', '');
        if ($extension != 'xls' && $extension != 'xlsx') {
            return response()->json([
                'extension' => 'error'
            ]);
        }

        //dd($reader->toArray());
        $this->importOutput['rowSuccess'] = 0;
        $this->importOutput['rowError'] = 0;
        $this->importOutput['userOuput'] = [];
        foreach ($array as &$row) {
            if (!empty($row) && count($row) > 1) {
                $first_element = array_shift($row);
                foreach ($row as $item) {
                    $user = array_combine($first_element, $item);
                    $userOuput = [];
                    try {
                        //kiểm tra xem dob trong excel có chứa kí tự / không? nếu có thì replace thành -
                        if (strpos($user['dob'], '/') !== false) {
                            $newDate = strtotime(str_replace('/', '-', $user['dob']));
                        } else if (strpos($user['dob'], '-') !== false) {
                            $newDate = strtotime($user['dob']);
                        } //nếu chứa chữ cái thì không hợp lệ
                        else if (preg_match("/[a-z]/i", $user['dob'])) {
                            $newDate = 0;
                        } //còn nếu type của cột dob là dạng date sẽ convert sang timestamp
                        else {
                            $newDate = Date::excelToTimestamp($user['dob']);
                        }
                        //gán timestamp = newdate
                        $timestamp = $newDate;
                        $user['sex'] = $user['sex'] == 'M' or $user['sex'] == 1 ? 1 : 0;
                        //
                        $userCheck = TmsUserDetail::select('user_id');

                        $checkUsername = MdlUser::select('id')->where('username', $user['username'])->first();
                        $checkEmail = $userCheck->where('email', $user['email'])->first();
                        $checkCmtnd = $userCheck->where('cmtnd', $user['cmtnd'])->first();
                        if ($checkUsername || $checkEmail || $checkCmtnd) {
                            $this->importOutput['rowError']++;
                            $userOuput['username'] = $user['username'];
                            $userOuput['status'] = 'error';
                            $userOuput['message'] = 'Thông tin ';
                            if ($checkUsername) {
                                $userOuput['message'] .= 'Tài khoản , ';
                            }
                            if ($checkEmail) {
                                $userOuput['message'] .= 'Email , ';
                            }
                            if ($checkCmtnd) {
                                $userOuput['message'] .= 'Số CMTND ';
                            }
                            $userOuput['message'] .= 'đã tồn tại.';
                            array_push($this->importOutput['userOuput'], $userOuput);
                        } //kiểm tra nếu tồn tại các trường và các trường đó không phải là số => sai định dạng
                        else if (($user['confirm'] && !is_numeric($user['confirm'])) ||
                            ($user['cmtnd'] && !is_numeric($user['cmtnd'])) ||
                            ($user['phone'] && !is_numeric($user['phone']))
                        ) {
                            //                            !is_numeric($user['confirm']) || !is_numeric($user['cmtnd'])
                            $userOuput['username'] = $user['username'] ? $user['username'] : '';
                            $userOuput['status'] = 'error';
                            $userOuput['message'] = 'Thông tin : ';
                            if (!is_numeric($user['confirm'])) {
                                $userOuput['message'] .= 'confirm , ';
                            }
                            if (!is_numeric($user['cmtnd'])) {
                                $userOuput['message'] .= 'Số CMTND , ';
                            }
                            if (!is_numeric($user['phone'])) {
                                $userOuput['message'] .= 'Số điện thoại , ';
                            }
                            $userOuput['message'] .= ' không đúng định dạng';
                            array_push($this->importOutput['userOuput'], $userOuput);
                        } //kiểm tra nếu tồn tại trường dob mà giá trị sau khi gán = 0 => không hợp lệ
                        else if ($user['dob'] && $timestamp == 0) {
                            $userOuput['username'] = $user['username'] ? $user['username'] : '';
                            $userOuput['status'] = 'error';
                            $userOuput['message'] = ' Thông tin : dob không đúng định dạng';
                            array_push($this->importOutput['userOuput'], $userOuput);
                        } else {
                            \DB::beginTransaction();
                            $role = Role::select('id', 'name', 'mdl_role_id')->where('name', 'teacher')->first();
                            $nameExpl = explode(' ', $user['fullname']);
                            $rowname = count($nameExpl);
                            $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
                            $lastname = str_replace($nameExpl[$rowname - 1], '', $user['fullname']);
                            $lastname = $lastname ? $lastname : '';
                            $mdlUser = new MdlUser;
                            $mdlUser->username = $user['username'];
                            $mdlUser->password = bcrypt('Bgt@2019');
                            $mdlUser->redirect_type = 'lms';
                            $mdlUser->firstname = $firstname;
                            $mdlUser->lastname = $lastname;
                            $mdlUser->email = $user['email'];
                            $mdlUser->save();

                            //                            $timestamp = strtotime($user['dob']);
                            //                            if ($timestamp === FALSE) {
                            //                                $timestamp = strtotime(str_replace('/', '-', $user['dob']));
                            //                            }

                            if ($role) {
                                //Assign TMS
                                $modelHasRole = ModelHasRole::where([
                                    'role_id' => $role['id'],
                                    'model_id' => $mdlUser->id
                                ])->first();
                                if (!$modelHasRole) {
                                    $userRole = new ModelHasRole;
                                    $userRole->role_id = $role['id'];
                                    $userRole->model_id = $mdlUser->id;
                                    $userRole->model_type = 'App/MdlUser';
                                    $userRole->save();
                                }

                                //Assign LMS
                                $mdlRoleAssignment = MdlRoleAssignments::where([
                                    'roleid' => $role['mdl_role_id'],
                                    'userid' => $mdlUser->id
                                ])->first();
                                if (!$mdlRoleAssignment) {
                                    $roleAssign = new MdlRoleAssignments;
                                    $roleAssign->roleid = $role['mdl_role_id'];
                                    $roleAssign->userid = $mdlUser->id;
                                    $roleAssign->save();
                                }
                            }

                            $tmsUser = new TmsUserDetail;
                            $tmsUser->cmtnd = $user['cmtnd'];
                            $tmsUser->fullname = $user['fullname'];
                            $tmsUser->dob = $timestamp;
                            $tmsUser->email = $user['email'];
                            $tmsUser->phone = $user['phone'];
                            $tmsUser->address = $user['address'];
                            $tmsUser->sex = $user['sex'];
                            $tmsUser->confirm = $user['confirm'];
                            $tmsUser->user_id = $mdlUser->id;
                            $tmsUser->save();

                            $this->importOutput['rowSuccess']++;
                            $userOuput['username'] = $user['username'];
                            $userOuput['status'] = 'success';
                            $userOuput['message'] = 'Import thành công.';
                            array_push($this->importOutput['userOuput'], $userOuput);
                            \DB::commit();
                        }
                    } catch (\Exception $e) {
                        $this->importOutput['rowError']++;
                        $userOuput['username'] = $user['username'];
                        $userOuput['status'] = 'error';
                        $userOuput['message'] = 'Gặp lỗi khi Import.';
                        array_push($this->importOutput['userOuput'], $userOuput);
                    }
                }
            }
        }
        return response()->json($this->importOutput);
    }

    public function apiImportStudent(Request $request)
    {
        $path = $request->file('file')->getRealPath();

        Excel::load($path, function ($reader) {
            //dd($reader->toArray());
            $this->importOutput['rowSuccess'] = 0;
            $this->importOutput['rowError'] = 0;
            $this->importOutput['userOuput'] = [];
            foreach ($reader->toArray() as $key => $row) {
                if (!empty($row)) {
                    foreach ($row as $user) {
                        $userOuput = [];
                        try {
                            $userCheck = TmsUserDetail::select('user_id');

                            $checkUsername = MdlUser::select('id')->where('username', $user['username'])->first();
                            $checkEmail = $userCheck->where('email', $user['email'])->first();
                            $checkCmtnd = $userCheck->where('cmtnd', $user['cmtnd'])->first();
                            if ($checkUsername || $checkEmail || $checkCmtnd) {
                                $this->importOutput['rowError']++;
                                $userOuput['username'] = $user['username'];
                                $userOuput['status'] = 'error';
                                $userOuput['message'] = 'Thông tin ';
                                if ($checkUsername) {
                                    $userOuput['message'] .= 'Tài khoản , ';
                                }
                                if ($checkEmail) {
                                    $userOuput['message'] .= 'Email , ';
                                }
                                if ($checkCmtnd) {
                                    $userOuput['message'] .= 'Số CMTND ';
                                }
                                $userOuput['message'] .= 'đã tồn tại.';
                                array_push($this->importOutput['userOuput'], $userOuput);
                            } else {
                                \DB::beginTransaction();
                                $role = Role::select('id', 'name', 'mdl_role_id')->where('name', 'student')->first();
                                $nameExpl = explode(' ', $user['fullname']);
                                $rowname = count($nameExpl);
                                $firstname = $nameExpl[$rowname - 1] ? $nameExpl[$rowname - 1] : '';
                                $lastname = str_replace($nameExpl[$rowname - 1], '', $user['fullname']);
                                $lastname = $lastname ? $lastname : '';
                                $mdlUser = new MdlUser;
                                $mdlUser->username = $user['username'];
                                $mdlUser->password = bcrypt('Bgt@2019');
                                $mdlUser->redirect_type = 'lms';
                                $mdlUser->firstname = $firstname;
                                $mdlUser->lastname = $lastname;
                                $mdlUser->email = $user['email'];
                                $mdlUser->save();

                                $timestamp = strtotime($user['dob']);
                                if ($timestamp === FALSE) {
                                    $timestamp = strtotime(str_replace('/', '-', $user['dob']));
                                }

                                if ($role) {
                                    //Assign TMS
                                    $modelHasRole = ModelHasRole::where([
                                        'role_id' => $role['id'],
                                        'model_id' => $mdlUser->id
                                    ])->first();
                                    if (!$modelHasRole) {
                                        $userRole = new ModelHasRole;
                                        $userRole->role_id = $role['id'];
                                        $userRole->model_id = $mdlUser->id;
                                        $userRole->model_type = 'App/MdlUser';
                                        $userRole->save();
                                    }

                                    //Assign LMS
                                    $mdlRoleAssignment = MdlRoleAssignments::where([
                                        'roleid' => $role['mdl_role_id'],
                                        'userid' => $mdlUser->id
                                    ])->first();
                                    if (!$mdlRoleAssignment) {
                                        $roleAssign = new MdlRoleAssignments;
                                        $roleAssign->roleid = $role['mdl_role_id'];
                                        $roleAssign->userid = $mdlUser->id;
                                        $roleAssign->save();
                                    }
                                }

                                $tmsUser = new TmsUserDetail;
                                $tmsUser->cmtnd = $user['cmtnd'];
                                $tmsUser->fullname = $user['fullname'];
                                $tmsUser->dob = $timestamp;
                                $tmsUser->email = $user['email'];
                                $tmsUser->phone = $user['phone'];
                                $tmsUser->address = $user['address'];
                                $tmsUser->sex = $user['sex'];
                                $tmsUser->confirm = $user['confirm'];
                                $tmsUser->user_id = $mdlUser->id;
                                $tmsUser->save();

                                $this->importOutput['rowSuccess']++;
                                $userOuput['username'] = $user['username'];
                                $userOuput['status'] = 'success';
                                $userOuput['message'] = 'Import thành công.';
                                array_push($this->importOutput['userOuput'], $userOuput);
                                \DB::commit();
                            }
                        } catch (\Exception $e) {
                            $this->importOutput['rowError']++;
                            $userOuput['username'] = $user['username'];
                            $userOuput['status'] = 'error';
                            $userOuput['message'] = 'Gặp lỗi khi Import.';
                            array_push($this->importOutput['userOuput'], $userOuput);
                        }
                    }
                }
            }
        });
        return response()->json($this->importOutput);
    }

    //$fileName = $file->getClientOriginalName();
    //Storage::putFileAs('/upload/file_import', $file, $fileName);
    /*$name = time() . '.' . $file->getClientOriginalExtension();
    $destinationPath = public_path('/upload/file_import/');
    $file->move($destinationPath, $name);*/


    public function apiListUserTrash(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $role_name = $request->input('role_name');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'role_name' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $data = DB::table('tms_user_detail as tud')
            ->select('tud.cmtnd', 'tud.fullname', 'tud.email', 'mu.username', 'tud.user_id')
            ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id');
        if ($role_name != '') {
            $data = $data->join('model_has_roles as mhr', 'mhr.model_id', '=', 'tud.user_id');
            $data = $data->join('roles as r', 'r.id', '=', 'mhr.role_id');
            $data = $data->where('r.name', '=', $role_name);
        }
        $data = $data->where('tud.deleted', '=', 1);
        if ($this->keyword) {
            $data = $data->where(function ($query) {
                $query->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiUserRestore(Request $request)
    {
        try {
            $now = Carbon::now();
            $user_id = $request->input('user_id');

            $param = [
                'user_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator) || !$user_id) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            \DB::beginTransaction();
            $mdlUser = MdlUser::findOrFail($user_id);
            if ($mdlUser) {
                $mdlUser->deleted = 0;
                $mdlUser->save();

                $user = TmsUserDetail::where('user_id', $user_id)->first();
                $user->deleted = 0;
                $user->created_at = $now;
                $user->save();

                $type = 'user';
                $url = '/system/user/edit/' . $user_id;
                $action = 'restore';
                $info = 'Khôi phục tài khoản ' . $mdlUser['username'];
                devcpt_log_system($type, $url, $action, $info);
            }
            \DB::commit();
            return response()->json(status_message('success', __('khoi_phuc_tai_khoan_thanh_cong')));
        } catch (\Exception  $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiUserRestoreList(Request $request)
    {
        try {
            $now = Carbon::now();
            $user_restore = $request->input('user_restore');
            if (!is_array($user_restore))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            \DB::beginTransaction();
            foreach ($user_restore as $user_id) {
                if (!is_numeric($user_id))
                    return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                $mdlUser = MdlUser::findOrFail($user_id);
                if ($mdlUser) {
                    $mdlUser->deleted = 0;
                    $mdlUser->save();

                    $user = TmsUserDetail::where('user_id', $user_id)->first();
                    $user->deleted = 0;
                    $user->created_at = $now;
                    $user->save();

                    $type = 'user';
                    $url = '/system/user/edit/' . $user_id;
                    $action = 'restore';
                    $info = 'Khôi phục tài khoản ' . $mdlUser['username'];
                    devcpt_log_system($type, $url, $action, $info);
                }
            }
            \DB::commit();
            return response()->json(status_message('success', __('khoi_phuc_tai_khoan_thanh_cong')));
        } catch (\Exception  $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Xóa vĩnh viễn nhiều tài khoản
    public function apiClearUser(Request $request)
    {
        try {
            $user_clear = $request->input('user_clear');
            if (!is_array($user_clear))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            \DB::beginTransaction();
            foreach ($user_clear as $user_id) {
                if (!is_numeric($user_id))
                    return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                $mdlUser = MdlUser::findOrFail($user_id);

                //Function clear user khỏi DB
                TmsUserDetail::clearUser($user_id);
                StudentCertificate::where('userid', $user_id)->delete();

                $type = 'user';
                $url = '*';
                $action = 'clear';
                $info = 'Xóa vĩnh viễn tài khoản ' . $mdlUser['username'];
                devcpt_log_system($type, $url, $action, $info);
            }
            \DB::commit();
            return response()->json(status_message('success', __('xoa_vinh_vien_tai_khoan_thanh_cong')));
        } catch (\Exception  $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiUpdatePassword(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $password = $request->input('password');
            $passwordConf = $request->input('passwordConf');

            /*$param = [
                'user_id' => 'number',
                'password' => 'token',
                'passwordConf' => 'token',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return 'validateFail';
            }*/
            if (!is_numeric($user_id)) {
                return 'error';
            }

            $mdlUser = MdlUser::findOrFail($user_id);
            if (!validate_password_func($password))
                return 'passFail';
            if (!validate_password_func($passwordConf))
                return 'passConfFail';
            if ($password != $passwordConf)
                return 'passwordFail';
            if ($password == $passwordConf && password_verify($password, $mdlUser['password']))
                return 'passwordExist';
            $mdlUser->password = bcrypt($password);
            $mdlUser->save();

            $type = 'user';
            $url = '/system/user/edit/' . $user_id;
            $action = 'update';
            $info = 'Sửa lại mật khẩu của tài khoản: ' . $mdlUser['username'];
            devcpt_log_system($type, $url, $action, $info);
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function apiUserSchedule(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $user_id = $request->input('user_id');
        $status = $request->input('status');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'user_id' => 'number',
            'status' => 'number',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $now = Carbon::now();
        $mdlUserEnr = DB::table('mdl_user_enrolments as enrolments')
            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'enrolments.enrolid')
            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
            ->join('mdl_role', 'mdl_role.id', '=', 'mdl_enrol.roleid')
            ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->where('enrolments.userid', '=', $user_id)
            ->select(
                'mdl_course.id',
                'mdl_course.fullname as course_name',
                'mdl_course.shortname as shortname',
                'mdl_course.startdate as startdate',
                'mdl_course.enddate as enddate',
                'mdl_role.shortname as role_name',
                'mdl_course.category as category'
            );
        if ($status != 0) {
            switch ($status) {
                case 1:
                    $mdlUserEnr = $mdlUserEnr->where('mdl_course.startdate', '<', strtotime($now));
                    $mdlUserEnr = $mdlUserEnr->where('mdl_course.enddate', '>', strtotime($now));
                    break;
                case 2:
                    $mdlUserEnr = $mdlUserEnr->where('mdl_course.startdate', '>', strtotime($now));
                    break;
                case 3:
                    $mdlUserEnr = $mdlUserEnr->where('mdl_course.enddate', '<', strtotime($now));
                    break;
            }
        }
        $mdlUserEnr = $mdlUserEnr->orderBy('mdl_course.startdate', 'desc');

        if ($start_date) {
            //            $mdlUserEnr->where('mdl_course.enddate','>',strtotime($start_date));
            $mdlUserEnr->where('mdl_course.startdate', '>', strtotime($start_date));
        }
        if ($end_date) {
            $mdlUserEnr->where('mdl_course.enddate', '<', strtotime($end_date));
            //            $mdlUserEnr->where('mdl_course.startdate','<',strtotime($end_date));
        }

        if ($this->keyword) {
            if ($this->keyword == 'offline' || $this->keyword == 'Offline') {
                $mdlUserEnr->whereIn('mdl_course.category', [2, 5]);
            } else if ($this->keyword == 'online' || $this->keyword == 'Online') {
                $mdlUserEnr->whereNotIn('mdl_course.category', [2, 5]);
            } else {
                $mdlUserEnr = $mdlUserEnr->where(function ($q) {
                    $q->orWhere('mdl_course.fullname', 'like', "%{$this->keyword}%")
                        ->orWhere('mdl_course.shortname', 'like', "%{$this->keyword}%");
                });
            }
        }
        $mdlUserEnr = $mdlUserEnr->paginate($row);
        $total = ceil($mdlUserEnr->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $mdlUserEnr->currentPage(),
            ],
            'data' => $mdlUserEnr,
        ];
        return response()->json($response);
    }

    public function apiGradeCourseTotal(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $user_id = $request->input('user_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $category_id = TmsTrainningUser::with('category')->where('user_id', $user_id)->first();
        $category = $category_id['category']['category_id'];

        $data = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id')
            ->where('u.id', '=', $user_id)
            ->select(
                'c.id as course_id',
                'c.shortname',
                'c.fullname',
                'c.fullname',
                'ccc.gradepass as gradepass',
                DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm
                inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cmc.completionstate != 0 and cmc.userid = ' . $user_id . ' and cm.course = c.id)
                as user_course_completionstate'),
                DB::raw('(select count(cm.id) as course_learn from mdl_course_modules cm
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cm.course = c.id) as user_course_learn'),
                DB::raw('IF( EXISTS(select cc.id from mdl_course_completions as cc
                                 where cc.userid = ' . $user_id . ' and cc.course = c.id and cc.timecompleted is not null ), "1", "0") as status_user'),
                DB::raw('(select `g`.`finalgrade`
  				from mdl_grade_items as gi
				join mdl_grade_grades as g
				on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = ' . $user_id . ' ) as finalgrade')
            );

        $data = $data->orderBy('c.id', 'desc')->distinct();
        if ($category) {
            $data = $data->where('c.category', '=', $category);
        }
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('c.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('c.shortname', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiCourseList()
    {
        $courses = MdlCourseCategory::get()->toArray();
        return response()->json($courses);
    }

    public function apiCourseGradeDetail(Request $request)
    {
        $data = [];
        $user_id = $request->input('user_id');
        $course_id = $request->input('course_id');

        $param = [
            'user_id' => 'number',
            'course_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $course = MdlCourse::findOrFail($course_id);
        $course_grade = DB::table('mdl_grade_items as mgi')
            ->join('mdl_grade_grades as mgg', 'mgg.itemid', '=', 'mgi.id')
            ->where('mgi.courseid', '=', $course_id)
            ->where('mgg.userid', '=', $user_id)
            ->where('mgi.weightoverride', '=', 1)
            ->select('mgi.itemname as itemname', 'mgg.finalgrade as finalgrade')
            ->get();
        $data['detail'] = $course;
        $data['course_grade'] = $course_grade;
        return response()->json($data);
    }

    //Danh sách người dùng là nhân viên giám sát thị trường
    public function apiListUserMarket(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listUsers = DB::table('tms_user_detail')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'mdl_user.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
        $listUsers = $listUsers->select(
            'roles.name',
            'tms_user_detail.fullname as fullname',
            'tms_user_detail.email as email',
            'mdl_user.username as username',
            'tms_user_detail.user_id as user_id',
            'tms_user_detail.cmtnd as cmtnd',
            DB::raw('(select count(tro.id) as agents_length from tms_role_organize tro
                where tro.user_id = mdl_user.id) as agents_length')
        )
            ->where('tms_user_detail.deleted', 0)
            ->where('roles.name', 'managemarket');
        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tms_user_detail.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mdl_user.username', 'like', "%{$this->keyword}%");
            });
        }
        $listUsers = $listUsers->orderBy('tms_user_detail.created_at', 'desc');
        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $total_user = $listUsers->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }


    //Danh sách người dùng là chủ đại lý
    public function apiListBranchMaster(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $listUsers = DB::table('tms_user_detail')
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id');

        $listUsers = $listUsers
            ->select(
                'tms_user_detail.fullname as fullname',
                'tms_user_detail.email as email',
                'mdl_user.username as username',
                'tms_user_detail.user_id as user_id',
                'tms_user_detail.cmtnd as cmtnd',
                DB::raw('(select count(branch_id) from tms_branch_master where master_id = mdl_user.id) as agents_length')
            )
            ->where('tms_user_detail.deleted', 0)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_branch_master')
                    ->whereRaw('master_id = tms_user_detail.user_id');
            });

        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tms_user_detail.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mdl_user.username', 'like', "%{$this->keyword}%");
            });
        }
        $listUsers = $listUsers->orderBy('tms_user_detail.created_at', 'desc');

        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $total_user = $listUsers->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }


    //Danh sách người dùng có thể gán quyền nhân viên giám sát thị trường
    public function apiShowUserMarket(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $user_market = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'managemarket')
            ->pluck('model_has_roles.model_id as user_id')->toArray();
        $listUsers = DB::table('tms_user_detail')
            ->select(
                'tms_user_detail.fullname as fullname',
                'tms_user_detail.email as email',
                'mdl_user.username as username',
                'tms_user_detail.user_id as user_id',
                'tms_user_detail.cmtnd as cmtnd'
            )
            ->join('mdl_user', 'mdl_user.id', '=', 'tms_user_detail.user_id')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'tms_user_detail.user_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('tms_user_detail.deleted', '=', 0)
            ->whereNotIn('tms_user_detail.user_id', $user_market);


        $arr_data = Role::arr_role_special;
        if (($key = array_search(Role::MANAGE_MARKET, $arr_data)) !== false) {
            unset($arr_data[$key]);
        }

        foreach ($arr_data as $item) {
            $listUsers = $listUsers->where('roles.name', '!=', $item);
        }


        if ($this->keyword) {
            $listUsers = $listUsers->where(function ($query) {
                $query->orWhere('tms_user_detail.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tms_user_detail.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mdl_user.username', 'like', "%{$this->keyword}%");
            });
        }
        $listUsers = $listUsers->orderBy('tms_user_detail.created_at', 'desc');
        $listUsers = $listUsers->paginate($row);
        $total = ceil($listUsers->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $listUsers->currentPage(),
            ],
            'data' => $listUsers,
        ];
        return response()->json($response);
    }

    public function apiGetListRole()
    {
        $roles = Role::whereNotIn('name', [Role::EDITING_TEACHER, Role::COURSE_CREATOR])->select('id', 'name')
            //->whereNotIn('name',['teacher','student','managemarket'])
            ->get()->toArray();
        return response()->json($roles);
    }

    //Lấy danh sách tỉnh thành trang quản lý thị trường
    public function apiUserMarketGetCity()
    {
        $citys = listCitySelect();
        return response()->json($citys);
    }

    //Lấy danh sách Đại lý để gán cho nhân viên quản lý thị trường
    //UserMarketOrganizeComponent.vue
    public function apiUserMarketListOrganize(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $city_id = $request->input('city');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'city' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch as tb')
            ->select(
                'tb.id',
                'tb.code',
                'tb.name',
                'tro.organize_id as organize_id',
                'tud.fullname as user_name',
                'tc.name as city_name'
            )
            ->leftJoin('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
            ->leftJoin('tms_city as tc', 'tc.id', '=', 'tcb.city_id')
            ->leftJoin('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id')
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'tb.user_id');
        if ($this->keyword) {
            $data = $data->where(function ($query) {
                $query->orWhere('tb.code', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.name', 'like', "%{$this->keyword}%");
            });
        }
        if ($city_id != 0) {
            $data = $data->where('tcb.city_id', '=', $city_id);
        }
        $data = $data->where('tb.deleted', '=', 0);
        $data = $data->whereNull('tro.organize_id');
        $data = $data->orderBy('tb.code', 'asc');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    //Gán Đại lý cho nhân viên quản lý thị trường
    //UserMarketOrganizeComponent.vue
    public function apiUserMarketAddOrganize(Request $request)
    {
        $organize_id = $request->input('organize_id');
        $user_id = $request->input('user_id');
        if (!$organize_id && !$user_id)
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));

        $param = [
            'organize_id' => 'number',
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }

        $role_organize = new TmsRoleOrganize;
        $role_organize->user_id = $user_id;
        $role_organize->organize_id = $organize_id;
        $role_organize->type = 'branch';
        $role_organize->save();
        return response()->json(status_message('success', __('gan_dai_ly_thanh_cong')));
    }

    //Gỡ Đại lý của nhân viên quản lý thị trường
    //UserMarketOrganizeComponent.vue
    public function apiUserMarketRemoveOrganize(Request $request)
    {
        $organize_id = $request->input('organize_id');
        $user_id = $request->input('user_id');
        if (!$organize_id && !$user_id)
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));

        $param = [
            'organize_id' => 'number',
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }

        TmsRoleOrganize::where([
            'user_id' => $user_id,
            'organize_id' => $organize_id
        ])->delete();
        return response()->json(status_message('success', __('go_dai_ly_thanh_cong')));
    }

    public function apiUserMarketListRoleOrganize(Request $request)
    {
        $row = $request->input('row');
        $user_id = $request->input('user_id');

        $param = [
            'row' => 'text',
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch as tb')
            ->select('tb.id', 'tb.code', 'tb.name', 'tud.fullname as user_name', 'tc.name as city_name')
            ->leftJoin('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
            ->leftJoin('tms_city as tc', 'tc.id', '=', 'tcb.city_id')
            ->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id')
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'tb.user_id');
        $data = $data->where('tb.deleted', '=', 0);
        $data = $data->where('tro.user_id', '=', $user_id);
        $data = $data->orderBy('tb.code', 'asc');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    //Lấy danh sách điểm bán đã được gán cho nhân viên giám sát thị trường
    public function apiUserMarketListBranch()
    {
        $user_id = Auth::id();
        $branchs = DB::table('tms_role_organize as tro')
            ->select('tb.id', 'tb.name')
            ->join('tms_branch as tb', 'tb.id', '=', 'tro.organize_id')
            ->where('tro.user_id', '=', $user_id)
            ->get()->toArray();
        return response()->json($branchs);
    }

    //Api lấy danh sách Điểm bán theo Đại lý
    public function apiSaleRoomSearchBox(Request $request)
    {
        $branch = $request->input('branch');
        $this->keyword = $request->input('keyword');
        $data = DB::table('tms_branch_sale_room as tbsr')
            ->select('tsr.id', 'tsr.name')
            ->join('tms_sale_rooms as tsr', 'tsr.id', '=', 'tbsr.sale_room_id')
            ->where('tbsr.branch_id', '=', $branch);
        if ($this->keyword) {
            $data = $data->where(function ($query) {
                $query->orWhere('tsr.code', 'like', "%{$this->keyword}%")
                    ->orWhere('tsr.name', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->paginate(20);
        $total = ceil($data->total() / 20);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiUserMarketListUserByRole(Request $request)
    {
        $row = $request->input('row');
        $this->branch_id = $request->input('branch_id');
        $saleroom_id = $request->input('saleroom_id');
        $this->keyword = $request->input('keyword');
        $style = $request->input('style');

        $param = [
            'row' => 'number',
            'branch_id' => 'number',
            'saleroom_id' => 'number',
            'keyword' => 'text',
            'style' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        if ($style == 0) { //Lọc theo danh sách nhân viên
            $data = DB::table('tms_user_detail as tud')
                ->select(
                    'tud.user_id',
                    'tud.cmtnd',
                    'tud.fullname',
                    'tud.email',
                    'tsru.id as id',
                    'mu.username',
                    'tsr.name as sale_room_name',
                    'tsr.id as sale_room_id',
                    'tb.name as branch_name'
                )
                ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
                ->join('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'mu.id')
                ->leftJoin('tms_role_organize as tro', function ($join) {
                    $join->on('tro.organize_id', '=', 'tsru.sale_room_id');
                    $join->where('tsru.type', '=', TmsSaleRoomUser::AGENTS);
                })
                ->leftJoin('tms_branch_sale_room as tbsr', function ($join) {
                    $join->on('tbsr.sale_room_id', '=', 'tsru.sale_room_id');
                    $join->where('tsru.type', '=', TmsSaleRoomUser::POS);
                })
                ->leftJoin('tms_role_organize as troo', 'troo.organize_id', '=', 'tbsr.branch_id')
                ->leftJoin('tms_sale_rooms as tsr', 'tsr.id', '=', 'tbsr.sale_room_id')
                ->leftJoin('tms_branch as tb', 'tb.id', '=', 'tro.organize_id')
                ->where('tud.deleted', '=', 0);
            $data = $data->where(function ($q) {
                $q->where('tro.user_id', '=', Auth::user()->id)
                    ->orWhere('troo.user_id', '=', Auth::user()->id);
            });
            if ($saleroom_id != 0) {
                $data = $data->where('tsr.id', '=', $saleroom_id);
            } else {
                if ($this->branch_id != 0) {
                    $data = $data->where(function ($q) {
                        $q->where('tsru.sale_room_id', '=', $this->branch_id)
                            ->where('.tsru.type', '=', TmsSaleRoomUser::AGENTS);
                        $q->orWhere(function ($qu) {
                            $qu->where('tbsr.branch_id', '=', $this->branch_id)
                                ->where('.tsru.type', '=', TmsSaleRoomUser::POS);
                        });
                    });
                }
            }
        } else { //lọc theo danh sách quản lý
            $data = DB::table('tms_user_detail as tud')
                ->select(
                    'tud.user_id',
                    'tud.cmtnd',
                    'tud.fullname',
                    'tud.email',
                    'mu.username',
                    'tsr.name as sale_room_name',
                    'tsr.id as sale_room_id',
                    'tb.name as branch_name',
                    'tb.id as branch_id'
                )
                ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
                ->leftJoin('tms_branch as tb', 'tb.user_id', '=', 'tud.user_id')
                ->leftJoin('tms_sale_rooms as tsr', 'tsr.user_id', '=', 'tud.user_id')
                ->leftJoin('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                ->leftJoin('tms_role_organize as troo', 'troo.organize_id', '=', 'tbsr.branch_id')
                ->leftJoin('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id')
                ->where('tud.deleted', '=', 0);
            $data = $data->where(function ($q) {
                $q->where('tro.user_id', '=', Auth::user()->id)
                    ->orWhere('troo.user_id', '=', Auth::user()->id);
            });
            if ($saleroom_id != 0) {
                $data = $data->where('tsr.id', '=', $saleroom_id);
            } else {
                if ($this->branch_id != 0) {
                    $data = $data->where(function ($q) {
                        $q->where('tro.organize_id', '=', $this->branch_id)
                            ->orWhere('.troo.organize_id', '=', $this->branch_id);
                    });
                }
            }
        }

        if ($this->keyword) {
            $data = $data->where(function ($query) {
                $query->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }

        $data = $data->orderBy('tsr.name', 'desc');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $total_user = $data->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'total_user' => $total_user,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiUserMarketRemove(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            if (!is_numeric($user_id))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            $role = Role::select('id')->where('name', 'managemarket')->first();
            ModelHasRole::where([
                'role_id' => $role['id'],
                'model_id' => $user_id
            ])->delete();
            //remove nhân viên giám sát thị trường
            remove_user_market($user_id);
            return response()->json(status_message('success', __('go_nguoi_dung_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiUserMarketAddRole(Request $request)
    {
        try {
            $user_select = $request->input('user_select');
            if (!is_array($user_select))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            $role = Role::select('id')->where('name', 'managemarket')->first();
            if ($user_select) {
                foreach ($user_select as $user_id) {
                    if (!is_numeric($user_id))
                        return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                    if (
                        ModelHasRole::where([
                            'role_id' => $role['id'],
                            'model_id' => $user_id
                        ])->count() == 0
                    ) {
                        $mhr = new ModelHasRole;
                        $mhr->role_id = $role['id'];
                        $mhr->model_id = $user_id;
                        $mhr->model_type = 'App/MdlUser';
                        $mhr->save();
                    }

                    $mdlUser = MdlUser::findOrFail($user_id);
                    $mdlUser->update(['redirect_type' => 'default']);
                }
            }
            return response()->json(status_message('success', __('them_nhan_vien_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Tạo nhân viên giám sát thị trường
    public function apiCreateUserMarket(Request $request)
    {
        try {
            $avatar = $request->file('file');
            $fullname = $request->input('fullname');
            $dob = $request->input('dob');
            $email = $request->input('email');
            $username = $request->input('username');
            $password = $request->input('password');
            $passwordConf = $request->input('passwordConf');
            $phone = $request->input('phone');
            $cmtnd = $request->input('cmtnd');
            $address = $request->input('address');

            $sex = $request->input('sex');
            $code = ($request->input('code') && $request->input('code') != 'null') ? $request->input('code') : '';
            $start_time = $request->input('start_time');
            $working_status = $request->input('working_status');
            $confirm_address = $request->input('confirm_address');
            $confirm = $request->input('confirm');
            $training_id = $request->input('training_id');

            $param = [
                'fullname' => 'text',
                'dob' => 'date',
                'email' => 'email',
                'username' => 'text',
                /*'password' => 'token',
                'passwordConf' => 'token',*/
                'phone' => 'number',
                'cmtnd' => 'number',
                'address' => 'text',
                'sale_room_id' => 'number',
                'sex' => 'number',
                'code' => 'text',
                'start_time' => 'date',
                'working_status' => 'number',
                'confirm_address' => 'number',
                'confirm' => 'number',
                'file' => 'image',
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                return response()->json($validator);
            }

            if (!$email) {
                $email = Config::get('constants.domain.EMAIL-DEFAULT');
            }

            $username = strtolower($username);

            //Check scmtnd
            $userByCmtnd = TmsUserDetail::select('id')->where('cmtnd', $cmtnd)->first();

            if ($userByCmtnd)
                return response()->json(error_message('inputCmtnd', __('so_cmtnd_da_ton_tai')));

            //Check user
            $userByUser = MdlUser::where('username', $username)->where('deleted', 1)->count();
            if ($userByUser > 0)
                return response()->json(error_message('inputUsername', __('ten_dang_nhap_da_ton_tai_va_dang_bi_khoa')));
            $userByUser = MdlUser::where('username', $username)->count();
            if ($userByUser > 0)
                return response()->json(error_message('inputUsername', __('ten_dang_nhap_da_ton_tai')));

            //Check email
            /*$userByEmail = TmsUserDetail::select('id')->where('email', $email)->first();
            if ($userByEmail)
                return response()->json(error_message('inputEmail', __('dia_chi_email_da_ton_tai')));*/

            if ($password != $passwordConf)
                return response()->json(error_message('inputPassword', __('thong_tin_mat_khau_chua_khop_nhau')));

            if (!validate_password_func($password))
                return response()->json(error_message('inputPassword', __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet')));

            if (!validate_password_func($passwordConf))
                return response()->json(error_message('inputPasswordConfirm', __('sai_dinh_dang_mat_khau_mat_khau_co_it_nhat_8_ky_tu_gom_chu_hoa_chu_cai_thuong_chu_so_va_ky_tu_dac_biet')));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                return response()->json(error_message('inputEmail', __('email_sai_dinh_dang')));

            \DB::beginTransaction();
            $roles = Role::select('id', 'name', 'mdl_role_id')->where('name', 'managemarket')->get()->toArray();
            $convert_name = convert_name($fullname);
            $mdlUser = new MdlUser;
            $mdlUser->username = $username;
            $mdlUser->email = $email;
            $mdlUser->confirmed = 1;
            $mdlUser->firstname = $convert_name['firstname'];
            $mdlUser->lastname = $convert_name['lastname'];
            $mdlUser->password = bcrypt($password);
            $mdlUser->save();
            if ($roles) {
                foreach ($roles as $role) {
                    $mdlUser->update(['redirect_type' => 'default']);
                    //endrole
                    add_user_by_role($mdlUser->id, $role['id']);
                    enrole_lms($mdlUser->id, $role['mdl_role_id'], $confirm);
                }
            }

            //Thêm vị trí( Khung năng lực )
            if ($training_id > 0) {
                TmsTrainningUser::firstOrCreate([
                    'trainning_id' => $training_id,
                    'user_id' => $mdlUser->id
                ]);
                //$category = TmsTrainningCategory::select('category_id')->where('trainning_id', $training_id)->first();
                //enrole cho học viên theo khung năng lực
                //\DB::beginTransaction();
                training_enrole($mdlUser->id, $training_id);
                //\DB::commit();
            }

            $user = new TmsUserDetail;
            $user->user_id = $mdlUser->id;
            $user->cmtnd = $cmtnd;
            $user->fullname = $fullname ? $fullname : $username;
            $timestamp = strtotime($dob);
            if ($timestamp === FALSE) {
                $timestamp = strtotime(str_replace('/', '-', $dob));
            }
            $user->dob = $timestamp;
            $user->email = $email;
            $user->phone = $phone;
            $user->address = $address;

            $user->sex = $sex;
            $user->code = $code;
            if ($start_time && $start_time != 'null') {
                $start_time = strtotime($start_time);
                if ($start_time === FALSE) {
                    $start_time = strtotime(str_replace('/', '-', $start_time));
                }
                $user->start_time = $start_time;
            }
            $user->start_time = $start_time;
            $user->working_status = $working_status;
            $user->confirm = 1;
            $user->confirm_address = $confirm_address;
            if ($avatar) {
                $name = time() . '.' . $avatar->getClientOriginalExtension();
                $destinationPath = public_path('/upload/user/');
                $avatar->move($destinationPath, $name);
                $user->avatar = '/upload/user/' . $name;
            }
            $user->save();

            devcpt_log_system('user', '/system/user/edit/' . $mdlUser->id, 'create', 'Tạo mới User: ' . $mdlUser->username);
            \DB::commit();
            return response()->json(status_message('success', __('tao_moi_tai_khoan_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiGetListSaleRoomSystem(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $data = DB::table('tms_sale_rooms as tsr')
            ->select('tsr.id', 'tsr.name', 'tsr.code')
            ->where('tsr.deleted', '=', 0);

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tsr.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tsr.code', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //lấy danh sách nơi làm vc ( Điểm bán )
    //Search box
    public function apiWordPlaceList(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $user_id = $request->input('user_id');

        $param = [
            'keyword' => 'text',
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $saleroom_not = TmsSaleRoomUser::where('user_id', $user_id)->pluck('sale_room_id')->toArray();

        $data = DB::table('tms_sale_rooms as tsr')
            ->select('tsr.name', 'tsr.id', 'tsr.code')
            ->whereNotIn('tsr.id', $saleroom_not)
            ->where('tsr.deleted', '=', 0);

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tsr.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tsr.code', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Thêm nơi làm vc ( Điểm bán )
    //Search box
    public function apiWordPlaceAdd(Request $request)
    {
        $word_place = $request->input('word_place');
        $user_id = $request->input('user_id');

        $param = [
            'word_place' => 'number',
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json('error');
        }

        $mdlUser = MdlUser::find($user_id);
        $tmsSr = TmsSaleRooms::find($word_place);

        $check_word_place = TmsSaleRoomUser::where([
            'user_id' => $user_id,
            'sale_room_id' => $word_place
        ])->count();

        if ($check_word_place != 0)
            return response()->json('error');
        $sale_room = new TmsSaleRoomUser;
        $sale_room->user_id = $user_id;
        $sale_room->sale_room_id = $word_place;
        $sale_room->save();

        devcpt_log_system('user', '/system/user/edit/' . $user_id, 'add', 'Thêm Nơi làm việc: ' . $tmsSr->name . ' cho tài khoản ' . $mdlUser->username);

        return response()->json('success');
    }

    //Gỡ nơi làm vc ( Điểm bán )
    public function apiWordPlaceRemove(Request $request)
    {
        $word_place = $request->input('word_place');
        $user_id = $request->input('user_id');

        $param = [
            'word_place' => 'number',
            'user_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json('error');
        }

        $mdlUser = MdlUser::find($user_id);
        $tmsSr = TmsSaleRooms::find($word_place);

        TmsSaleRoomUser::where([
            'user_id' => $user_id,
            'sale_room_id' => $word_place
        ])->delete();

        devcpt_log_system('user', '/system/user/edit/' . $user_id, 'remove', 'Gỡ Nơi làm việc: ' . $tmsSr->name . ' đã gán cho tài khoản ' . $mdlUser->username);

        return response()->json('success');
    }

    //gỡ avatar
    public function apiRemoveAvatar(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            if (!is_numeric($user_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            $user = TmsUserDetail::where('user_id', $user_id)->first();
            if (!$user) {
                return response()->json(status_message('error', __('khong_tim_thay_tai_khoan')));
            }
            $user->avatar = '';
            $user->save();
            return response()->json(status_message('success', __('go_avatar_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //lấy danh sách đại lý gán cho nhân viên
    //Form tạo mới người dùng
    public function apiGetListBranchSystem(Request $request)
    {
        try {
            $this->keyword = $request->input('keyword');
            $branch_select = $request->input('branch_select');
            if (!empty($branch_select)) {
                foreach ($branch_select as $branch_id) {
                    if (!is_numeric($branch_id)) {
                        return response()->json([]);
                    }
                }
            }

            $param = [
                'keyword' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = DB::table('tms_branch as tb')
                ->select('tb.id', 'tb.name', 'tb.code');
            if (has_user_market()) {
                $data = $data->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id')
                    ->where('tro.user_id', '=', Auth::id());
            }
            $data = $data
                ->where('tb.deleted', '=', 0)
                ->whereNotIn('tb.id', $branch_select);
            if ($this->keyword) {
                $data = $data->where(function ($q) {
                    $q->orWhere('tb.name', 'like', "%{$this->keyword}%")
                        ->orWhere('tb.code', 'like', "%{$this->keyword}%");
                });
            }
            $data = $data->skip(0)->take(10);
            if ($this->keyword)
                $data = $data->skip(0)->take(20);
            $data = $data->get()->toArray();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    //lấy thông tin của danh sách đại lý đã chọn để gán cho nhân viên
    //Form tạo mới người dùng
    public function apiGetListBranchSelect(Request $request)
    {
        try {
            $branch_select = $request->input('branch_select');
            if (empty($branch_select)) {
                return response()->json([]);
            }
            foreach ($branch_select as $branch_id) {
                if (!is_numeric($branch_id)) {
                    return response()->json([]);
                }
            }

            $data = DB::table('tms_branch as tb')
                ->select('tb.id', 'tb.name', 'tb.code')
                ->whereIn('tb.id', $branch_select);
            $data = $data->get()->toArray();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    //lấy thông tin của danh sách đại lý đã chọn để gán cho nhân viên
    //Form tạo mới người dùng
    public function apiGetListSaleRoomSelect(Request $request)
    {
        try {
            $saleroom_select = $request->input('saleroom_select');
            if (empty($saleroom_select)) {
                return response()->json([]);
            }
            foreach ($saleroom_select as $saleroom_id) {
                if (!is_numeric($saleroom_id)) {
                    return response()->json([]);
                }
            }

            $data = DB::table('tms_sale_rooms as tsr')
                ->select('tsr.id', 'tsr.name', 'tsr.code')
                ->whereIn('tsr.id', $saleroom_select);
            $data = $data->get()->toArray();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    //lấy danh sách điểm bán gán cho nhân viên
    //Form tạo mới người dùng
    public function apiGetListSaleRoomSearch(Request $request)
    {
        try {
            $this->keyword = $request->input('keyword');
            $saleroom_select = $request->input('saleroom_select');

            if (!empty($saleroom_select)) {
                foreach ($saleroom_select as $saleroom_id) {
                    if (!is_numeric($saleroom_id)) {
                        return response()->json([]);
                    }
                }
            }

            $param = [
                'keyword' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = DB::table('tms_sale_rooms as tsr')
                ->select('tsr.id', 'tsr.name', 'tsr.code')
                ->where('tsr.deleted', '=', 0)
                ->whereNotIn('tsr.id', $saleroom_select);
            if (has_user_market()) {
                $data = $data->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                    ->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tbsr.branch_id')
                    ->where('tro.user_id', Auth::id());
            }
            if ($this->keyword) {
                $data = $data->where(function ($q) {
                    $q->orWhere('tsr.name', 'like', "%{$this->keyword}%")
                        ->orWhere('tsr.code', 'like', "%{$this->keyword}%");
                });
            }
            $data = $data->skip(0)->take(10);
            if ($this->keyword)
                $data = $data->skip(0)->take(20);
            $data = $data->get()->toArray();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    //lấy đại lý của điểm bán
    //Form tạo mới người dùng
    public function apiGetBranchBySaleRoom(Request $request)
    {
        try {
            $saleroom = $request->input('saleroom');
            if ($saleroom == 0) {
                return response()->json('*');
            }

            $param = [
                'saleroom' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json('');
            }

            $data = DB::table('tms_branch as tb')
                ->select('tb.id', 'tb.name', 'tb.code')
                ->join('tms_branch_sale_room as tbsr', 'tbsr.branch_id', '=', 'tb.id')
                ->where('tbsr.sale_room_id', '=', $saleroom)
                ->where('tb.deleted', '=', 0);

            $data = $data->first();
            if (isset($data->name) && $data->name) {
                $branch_name = $data->name . ' ( ' . $data->code . ' )';
            } else {
                $branch_name = '';
            }
            return response()->json($branch_name);
        } catch (\Exception $e) {
            return response()->json('');
        }
    }

    public function apiGetTrainingList(Request $request)
    {
        try {
            $data = TmsTrainningProgram::where('deleted', 0)->get()->toArray();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    //Hủy quyền chủ đại lý
    public function apiRemoveMaster($id, Request $request)
    {
        try {
            \DB::beginTransaction();
            if (!is_numeric($id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            TmsBranchMaster::where('master_id', $id)->delete();
            devcpt_log_system('system', '/system/branch_master/remove/' . $id, 'delete', __('huy_quyen_chu_dai_ly') . ": " . $id);
            \DB::commit();
            return response()->json(status_message('success', __('huy_quyen_chu_dai_ly_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    // End SystemController

    // SystemOrganizeController
    public function apiLoadDataOrganize()
    {
        $response = TmsCity::select('id', 'name')->with('city_branch.branch_name.branch_sale_room.sale_room_name')
            ->where('deleted', 0)
            ->get()->toArray();

        return response()->json($response);
    }

    public function apiGetListCity()
    {
        $response = TmsCity::select('id', 'name')
            ->where('deleted', 0)
            ->get()->toArray();
        return response()->json($response);
    }

    public function apiGetListBranch(Request $request)
    {
        $city_id = $request->input('city_id');
        if (!is_numeric($city_id)) {
            return response()->json([]);
        }
        $response = TmsCityBranch::with('branch_check')
            ->where('city_id', $city_id)
            ->get()->toArray();
        return response()->json($response);
    }

    public function apiGetListSaleRoom(Request $request)
    {
        $branch_id = $request->input('branch_id');
        if (!is_numeric($branch_id)) {
            return response()->json([]);
        }
        $response = TmsBranchSaleRoom::with('sale_room_check')
            ->where('branch_id', $branch_id)
            ->get()->toArray();
        return response()->json($response);
    }

    public function apiListDataUser(Request $request)
    {
        $keyword = $request->input('keyword');
        $filter = $request->input('filter');
        $name = $request->input('name');
        $data = DB::table('tms_city')
            ->join('tms_city_branch as cb', 'cb.city_id', '=', 'tms_city.id')
            ->join('tms_branch', 'tms_branch.id', '=', 'cb.branch_id')
            ->join('tms_branch_sale_room as bsr', 'bsr.branch_id', '=', 'tms_branch.id')
            ->join('tms_sale_rooms as sr', 'sr.id', '=', 'bsr.sale_room_id')
            ->join('tms_sale_room_user as sru', 'sru.sale_room_id', '=', 'sr.id')
            ->join('mdl_user', 'mdl_user.id', '=', 'sru.user_id')
            ->join('tms_user_detail as detail', 'detail.user_id', '=', 'mdl_user.id')
            ->select(
                'detail.fullname as fullname',
                'detail.cmtnd as cmtnd',
                'detail.phone as phone',
                'tms_city.name as city_name',
                'tms_branch.name as branch_name',
                'sr.name as sale_room_name',
                'sr.id as sale_room_id'
            );
        switch ($name) {
            case 'city_id':
                $data = $data->where('tms_city.id', $filter);
                break;
            case 'branch_id':
                $data = $data->where('tms_branch.id', $filter);
                break;
            case 'sale_room_id':
                $data = $data->where('tms_sale_rooms.id', $filter);
                break;
        }
        if ($keyword) {
            $data = $data->where('detail.fullname', 'like', "%{$keyword}%");
            $data = $data->orWhere('detail.cmtnd', 'like', "%{$keyword}%");
        }
        $data = $data->orderBy('detail.fullname', 'DESC');
        $data = $data->paginate(10);
        $total = ceil($data->total() / 10);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $keyword
        ];
        return response()->json($response);
    }

    public function apiListCity()
    {
        $citys = TmsCity::select('id', 'name')->where([
            'deleted' => 0,
            'parent' => 0
        ])->get()->toArray();
        return response()->json($citys);
    }

    public function apiCityData()
    {
        $data = [];
        $city = listCitySelect();
        $data['city'] = $city;

        return response()->json($data);
    }

    public function apiCityCreate(Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $district = $request->input('district');
            $department = $request->input('department');
            //$user_id = $request->input('user_id');
            //$description = $request->input('description');

            $param = [
                'name' => 'text',
                'code' => 'code',
                'district' => 'text',
                'department' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check_name = TmsCity::where('name', $name)->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_tinh_thanh_da_ton_tai')
                ]);

            $check = TmsCity::where('code', $code)->count();
            if ($check > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_tinh_thanh_da_ton_tai')
                ]);
            $tmsCity = new TmsCity;
            $tmsCity->name = $name;
            $tmsCity->code = $code;
            $tmsCity->district = $district;
            $tmsCity->save();

            if ($tmsCity && $department != 0) {
                TmsDepartmentCity::insert([
                    'city_id' => $tmsCity->id,
                    'department_id' => $department
                ]);
            }

            devcpt_log_system('organize', '/system/organize/city/edit/' . $tmsCity->id, 'create', 'Thêm mới Thành phố: ' . $name);
            return response()->json(status_message('success', __('them_moi_tinh_thanh_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiCityListData(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $district = $request->input('district');
        $type = $request->input('type');
        $department = $request->input('department');

        $param = [
            'row' => 'number',
            'keyword' => 'text',
            'district' => 'text',
            'type' => 'text',
            'department' => 'number'
        ];
        $validator = validate_fails($request, $param);

        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_city as tc')
            ->select(
                'tc.id',
                'tc.name',
                'tc.code',
                'tc.district',
                'tc.type',
                'td.name as department_name',
                DB::raw('(select count(tcb.id) as branch_count from tms_city_branch tcb
                where tcb.city_id = tc.id) as branch_count')
            )
            ->leftJoin('tms_department_citys as tdc', 'tdc.city_id', '=', 'tc.id')
            ->leftJoin('tms_departments as td', 'td.id', '=', 'tdc.department_id')
            ->where('tc.deleted', '=', 0)
            ->where('tc.parent', '=', 0);
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tc.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tc.code', 'like', "%{$this->keyword}%");
            });
        }
        if ($district != '') {
            $data = $data->where('tc.district', $district);
        }
        if ($department != '') {
            $data = $data->where('tdc.department_id', $department);
        }
        if ($type) {
            $data = $data->where('type', $type);
        }

        $data = $data->orderBy('code', 'ASC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiCityDelete($city_id, Request $request)
    {
        try {
            \DB::beginTransaction();

            if (!is_numeric($city_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            $city = TmsCity::findOrFail($city_id);
            if ($city) {
                /*$city->deleted = 1;*/
                $city->delete();

                TmsCityBranch::where('city_id', $city_id)->delete();
                TmsDepartmentCity::where('city_id', $city_id)->delete();
                devcpt_log_system('organize', '/system/organize/city/edit/' . $city_id, 'delete', 'Xóa Tỉnh: ' . $city['name']);
            }
            \DB::commit();
            return response()->json(status_message('success', __('xoa_tinh_thanh_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiCityDetailData($city_id)
    {
        $data = [];
        if (!is_numeric($city_id))
            return response()->json([]);
        $city = TmsCity::findOrFail($city_id);
        $department = TmsDepartmentCity::select('department_id')->where('city_id', $city_id)->first();
        $data['city'] = $city;
        $data['city']['department'] = $department['department_id'];
        //uydd 3/1/2020
        //$notUser = [];
        //$useCity = TmsCity::pluck('user_id')->toArray();
        //$userBranch     = TmsBranch::pluck('user_id')->toArray();
        //$notUser = array_merge($notUser, $useCity);
        //$notUser = array_diff($notUser, [$city['user_id']]);
        //$notUser = array_diff($notUser, [NULL, 0]);
        //$user = TmsUserDetail::where('deleted', 0)->whereNotIn('user_id', $notUser)->get()->toArray();
        //$data['user'] = $user;
        //$branchs = TmsBranch::select('id', 'name', 'code')
        //    ->where('deleted', 0)
        //    ->get()->toArray();
        //$data['branch'] = $branchs;
        return response()->json($data);
    }

    public function apiListAddBranch(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch as tb')
            ->select('tb.id', 'tb.name', 'tb.code', 'tb.address')
            ->leftJoin('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
            ->where('tb.deleted', '=', 0)
            ->whereNull('tcb.city_id');
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tb.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.code', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->orderBy('tb.created_at', 'DESC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiAddBranchByCity(Request $request)
    {
        $branch_add = $request->input('branch_add');
        $city_id = $request->input('city_id');

        $param = [
            'city_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json(status_message('error', 'sai_dinh_dang_ky_tu_khong_cho_phep'));
        }

        $city = TmsCity::select('name')->findOrFail($city_id);
        try {
            if ($city_id && $branch_add && is_array($branch_add)) {
                foreach ($branch_add as $branch) {
                    \DB::beginTransaction();
                    $cityBranch = new TmsCityBranch;
                    $cityBranch->city_id = $city_id;
                    $cityBranch->branch_id = $branch;
                    $cityBranch->save();

                    $branchName = TmsBranch::select('name')->findOrFail($branch);
                    devcpt_log_system('organize', '/system/organize/city/edit/' . $city_id, 'add', 'Gán đại lý: ' . $branchName['name'] . ' cho Tỉnh : ' . $city['name']);
                    \DB::commit();
                }
                return response()->json(status_message('success', __('them_dai_ly_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiCityUpdate($city_id, Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $district = $request->input('district');
            $department = $request->input('department');

            if (!is_numeric($city_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            $param = [
                'name' => 'text',
                'code' => 'code',
                'district' => 'text',
                'department' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check_name = TmsCity::where([
                'name' => $name,
                'deleted' => 0,
            ])->whereNotIn('id', [$city_id])->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_tinh_thanh_da_ton_tai')
                ]);

            //$type = $data['type'];
            $check = TmsCity::where([
                'code' => $code,
                'deleted' => 0,
            ])->whereNotIn('id', [$city_id])->count();
            if ($check > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_tinh_thanh_da_ton_tai')
                ]);
            $tmsCity = TmsCity::findOrFail($city_id);
            $tmsCity->name = $name;
            $tmsCity->code = $code;
            $tmsCity->district = $district;
            $tmsCity->save();

            TmsDepartmentCity::where('city_id', $tmsCity->id)->delete();
            if ($tmsCity && $department != 0) {
                TmsDepartmentCity::insert([
                    'city_id' => $tmsCity->id,
                    'department_id' => $department
                ]);
            }

            devcpt_log_system('organize', '/system/organize/city/edit/' . $city_id, 'update', 'Sửa Tỉnh: ' . $name);
            return response()->json(status_message('success', __('cap_nhat_tinh_thanh_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Api lấy danh sách đại lý trang ( danh sách đại lý )
    public function apiBranchListData(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $citySearch = $request->input('citySearch');
        $districtSearch = $request->input('districtSearch');
        $departmentSearch = $request->input('departmentSearch');
        $user_id = $request->input('user_id');

        $param = [
            'row' => 'number',
            'citySearch' => 'text',
            'districtSearch' => 'text',
            'departmentSearch' => 'number',
            'keyword' => 'text',
            'user_id' => 'number'
        ];

        $validator = validate_fails($request, $param);

        if (!empty($validator)) {
            return response()->json([]);
        }

        //Set đại lý từ Diva
        /*if($this->keyword) {
            $branchCount = TmsBranch::where('code',$this->keyword)->count();
            if($branchCount == 0)
                setBranchOnDiva($this->keyword);
        }*/
        $data = DB::table('tms_branch as tb')
            ->select(
                'tb.id',
                'tb.name',
                'tb.code',
                'tb.address',
                'tc.name as cityname',
                'tud.fullname as user_name',
                'tud.user_id as user_id',

                DB::raw('(select count(tbsr.id) as saleroom_count from tms_branch_sale_room tbsr
                where tbsr.branch_id = tb.id) as saleroom_count'),

                DB::raw('(select count(tsru.id) as user_count from tms_sale_room_user tsru
                left join tms_branch_sale_room tbsr on tbsr.sale_room_id = tsru.sale_room_id and tsru.type = "' . TmsSaleRoomUser::POS . '"
                left join tms_user_detail tud on tud.user_id = tsru.user_id
                where tud.deleted = 0 and (tbsr.branch_id = tb.id or (tsru.sale_room_id = tb.id and tsru.type = "' . TmsSaleRoomUser::AGENTS . '"))) as user_count')

            )
            ->leftJoin('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
            ->leftJoin('tms_city as tc', 'tc.id', '=', 'tcb.city_id')
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'tb.user_id');

        if (has_user_market()) {
            $data = $data->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id');
            $data = $data->where('tro.user_id', '=', \Auth::id());
        }

        if (strlen($user_id) != 0) {
            $my_branches = TmsBranchMaster::where('master_id', $user_id)->pluck('branch_id');
            if (count($my_branches) != 0) {
                $data = $data->whereIn('tb.id', $my_branches);
            } else {
                $data = $data->whereIn('tb.id', [0]);
            }
        }

        $data = $data->where('tb.deleted', '=', 0);

        if ($citySearch) {
            if ($citySearch == 'all') {
                $data = $data->whereNull('tcb.city_id');
            } else {
                $data = $data->where('tcb.city_id', '=', $citySearch);
            }
        }

        if (isset($districtSearch) && strlen($districtSearch) != 0) {
            $data = $data->whereIn('tcb.city_id', function ($query) use ($districtSearch) {
                $query->select('id')->from('tms_city')->where('district', $districtSearch);
            });
        }

        if (isset($departmentSearch) && strlen($departmentSearch) != 0) {
            $data = $data->whereIn('tcb.city_id', function ($query) use ($departmentSearch) {
                $query->select('city_id')->from('tms_department_citys')->where('department_id', $departmentSearch);
            });
        }

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tb.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.code', 'like', "%{$this->keyword}%");
            });
            /*$data = $data->where('code',$this->keyword);*/
        }

        /*$data   = TmsBranch::select('id','name','address','user_id','code')
            ->with('city_branch.city_name','user.detail','branch_sale_room')
            ->where('deleted',0);*/

        /*if($citySearch){
            $branch_id = TmsCityBranch::where('city_id',$citySearch)->pluck('branch_id');
            $data = $data->whereIn('id',$branch_id);
        }*/
        $data = $data->orderBy('tb.name', 'ASC');

        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];

        return response()->json($response);
    }

    //Thêm mới đại lý Uydd
    public function apiBranchCreate(Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $user_id = $request->input('user_id');
            //$description = $request->input('description');
            $city = $request->input('city');
            $address = $request->input('address');

            $param = [
                'name' => 'text',
                'code' => 'code',
                'user_id' => 'number',
                'city' => 'number',
                'address' => 'text',
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check_name = DB::table('tms_branch as tb')
                ->select()
                ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
                ->where('tb.name', '=', $name)
                ->where('tcb.city_id', '=', $city)
                ->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_dai_ly_da_ton_tai')
                ]);

            $check_branch = TmsBranch::where('code', $code)->count();
            if ($check_branch > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_dai_ly_da_ton_tai')
                ]);

            \DB::beginTransaction();
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
                $role_organize->user_id = \Auth::id();
                $role_organize->organize_id = $tmsBranch->id;
                $role_organize->type = 'branch';
                $role_organize->save();
            }

            devcpt_log_system('organize', '/system/organize/branch/edit/' . $tmsBranch->id, 'create', 'Thêm mới Đại lý: ' . $name);
            \DB::commit();
            return response()->json(status_message('success', __('them_dai_ly_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Api Xóa đại lý Uydd

    public function apiBranchDelete($branch_id, Request $request)
    {
        try {
            \DB::beginTransaction();
            if (!is_numeric($branch_id))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            $branch = TmsBranch::find($branch_id);
            if ($branch) {
                //xoa han Vuong TM
                //                $branch->deleted = 1;
                //                $branch->save();
                TmsBranchSaleRoom::where('branch_id', $branch_id)->delete();
                TmsCityBranch::where('branch_id', $branch_id)->delete();
                $branch->delete();
                devcpt_log_system('organize', '/system/organize/branch/edit/' . $branch_id, 'delete', 'Xóa Đại lý: ' . $branch['name']);
            }
            \DB::commit();
            return response()->json(status_message('success', __('xoa_dai_ly_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Lấy dữ liệu của đại lý, trang sửa đại lý
    public function apiBranchDetailData($branch_id)
    {
        $data = [];
        if (!is_numeric($branch_id))
            return response()->json([]);
        $branch = TmsBranch::with('tmsuser')->findOrFail($branch_id);
        $cityBranch = TmsCityBranch::select('city_id')->where('branch_id', $branch_id)->first();
        $data['branch'] = $branch;
        $data['branch']['city_id'] = $cityBranch['city_id'];
        $data['branch']['fullname'] = $branch['tmsuser']['fullname'];
        $city = listCitySelect();
        $data['city'] = $city;
        return response()->json($data);
    }

    public function apiBranchUpdate($branch_id, Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $user_id = $request->input('user_id');
            $city_id = $request->input('city_id');
            $address = $request->input('address');

            $param = [
                'name' => 'text',
                'code' => 'code',
                'user_id' => 'number',
                'city_id' => 'number',
                'address' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            if (!is_numeric($branch_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            $check_name = DB::table('tms_branch as tb')
                ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
                ->where('tb.name', '=', $name)
                ->where('tcb.city_id', '=', $city_id)
                ->whereNotIn('tb.id', [$branch_id])
                ->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_dai_ly_da_ton_tai')
                ]);

            $check_branch = TmsBranch::where('code', $code)->whereNotIn('id', [$branch_id])->count();
            if ($check_branch > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_dai_ly_da_ton_tai')
                ]);

            \DB::beginTransaction();
            $tmsBranch = TmsBranch::findOrFail($branch_id);

            if ($user_id && $user_id != 0 && $tmsBranch['user_id'] != $user_id) {
                $role = Role::where('name', Role::MANAGE_AGENTS)->first();
                ModelHasRole::where([
                    'role_id' => $role['id'],
                    'model_id' => $tmsBranch['user_id']
                ])->delete();
                add_role_for_user($user_id);
            }

            $tmsBranch->name = $name;
            $tmsBranch->code = $code;
            $tmsBranch->user_id = $user_id;
            $tmsBranch->address = $address;
            $tmsBranch->save();

            TmsCityBranch::where('branch_id', $branch_id)->delete();
            if ($city_id != 0) {
                $cityBranch = new TmsCityBranch;
                $cityBranch->city_id = $city_id;
                $cityBranch->branch_id = $tmsBranch->id;
                $cityBranch->save();
            }

            devcpt_log_system('organize', '/system/organize/branch/edit/' . $branch_id, 'edit', 'Sửa Đại lý: ' . $name);
            \DB::commit();
            return response()->json(status_message('success', __('cap_nhat_dai_ly_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Gán master cho đại lý
    public function apiBranchAssignMaster(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $branch_id = $request->input('branch_id');
            $param = [
                'user_id' => 'number',
                'branch_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check = TmsBranchMaster::where('branch_id', $branch_id)
                ->first();

            if (isset($check)) {
                $check->master_id = $user_id;
                $check->save();
            } else {
                $new_connection = new TmsBranchMaster();
                $new_connection->branch_id = $branch_id;
                $new_connection->master_id = $user_id;
                $new_connection->save();
            }
            devcpt_log_system('system', '/system/organize/branch/assign_master', 'create', __('cap_quyen_chu_dai_ly') . ": " . $user_id);
            return response()->json(status_message('success', __('cap_quyen_chu_dai_ly_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiSaleRoomData()
    {
        //Lấy danh sách user
        $notUser = [];
        $useCity = TmsCity::pluck('user_id')->toArray();
        $userBranch = TmsBranch::pluck('user_id')->toArray();
        $notUser = array_merge($notUser, $useCity, $userBranch);
        $notUser = array_diff($notUser, [NULL, 0]);
        $user = TmsUserDetail::select('user_id', 'fullname')
            ->where('deleted', 0)
            ->whereNotIn('user_id', $notUser)
            ->get()->toArray();
        $data = [];
        $data['user'] = $user;
        $branchs = TmsBranch::select('id', 'name')->where([
            'deleted' => 0,
        ])->get()->toArray();
        $data['branch'] = $branchs;
        return response()->json($data);
    }

    public function apiBranchDataForSaleroom(Request $request)
    {
        $city = $request->input('city');
        $citySearch = $request->input('citySearch');
        $department = $request->input('department');
        $departmentSearch = $request->input('departmentSearch');

        $param = [
            'city' => 'number',
            'citySearch' => 'number',
            'department' => 'number',
            'departmentSearch' => 'number',

        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch as tb')
            ->select('tb.id', 'tb.name', 'tb.code')
            ->leftJoin('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
            ->where('tb.deleted', '=', 0);
        if (has_user_market()) {
            $data = $data->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id');
            $data = $data->where('tro.user_id', '=', \Auth::id());
        }

        if ($city) {
            $data = $data->where('tcb.city_id', '=', $city);
        }

        if ($citySearch) {
            $data = $data->where('tcb.city_id', '=', $citySearch);
        }

        if ($department) {
            $data = $data->whereIn('tcb.city_id', function ($query) use ($department) {
                $query->select('city_id')->from('tms_department_citys')->where('department_id', $department);
            });
        }

        if ($departmentSearch) {
            $data = $data->whereIn('tcb.city_id', function ($query) use ($departmentSearch) {
                $query->select('city_id')->from('tms_department_citys')->where('department_id', $departmentSearch);
            });
        }

        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api Lấy danh sách đại lý gán cho điểm bán
    public function apiSaleRoomDataSearchBox(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $city = $request->input('city');
        $district = $request->input('district');

        $param = [
            'keyword' => 'text',
            'district' => 'text',
            'city' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch as tb')
            ->select('tb.id', 'tb.name', 'tb.code')
            ->leftJoin('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
            ->where('tb.deleted', '=', 0);
        if (has_user_market()) {
            $data = $data->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id');
            $data = $data->where('tro.user_id', '=', \Auth::id());
        }

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tb.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.code', 'like', "%{$this->keyword}%");
            });
        }

        if (isset($district) && strlen($district) != 0) {
            $param['district'] = 'text';
            $data = $data->whereIn('tcb.city_id', function ($query) use ($district) {
                $query->select('id')->from('tms_city')->where('district', $district);
            });
        }

        if ($city) {
            $data = $data->where('tcb.city_id', '=', $city);
        }

        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api Lấy danh sách Người dùng gán cho Đại lý
    public function apiBranchDataSearchBoxUser(Request $request)
    {
        $this->keyword = $request->input('keyword');

        $param = [
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_user_detail as tud')
            ->select('tud.user_id', 'tud.fullname')
            ->where('tud.deleted', '=', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles as mhr')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->where('r.name', '=', Role::MANAGE_MARKET)
                    ->whereRaw('mhr.model_id = tud.user_id');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_branch as tb')
                    ->where('tb.deleted', '=', 0)
                    ->whereRaw('tb.user_id = tud.user_id');
            });

        if ($this->keyword) {
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api lấy danh sách Trưởng Đại lý
    public function apiBranchDataSearchBoxBranchMaster(Request $request)
    {
        $this->keyword = $request->input('keyword');

        $param = [
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_user_detail as tud')
            ->select('tud.user_id', 'tud.fullname')
            ->where('tud.deleted', '=', 0)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles as mhr')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->where('r.name', '=', Role::MANAGE_AGENTS)
                    ->whereRaw('mhr.model_id = tud.user_id');
            });

        if ($this->keyword) {
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api lấy danh sách đại lý
    public function apiBranchDataSearchBoxBranch(Request $request)
    {
        $this->keyword = $request->input('keyword');

        $param = [
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch as tb')
            ->select('tb.id', 'tb.name')->where('deleted', 0);

        if ($this->keyword) {
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }
            $data = $data->where(function ($q) {
                $q->orWhere('tb.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.code', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.address', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.description', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api lấy danh sách đại lý chưa có chủ đại lý
    public function apiBranchDataSearchBoxBranchForMaster(Request $request)
    {
        $this->keyword = $request->input('keyword');

        $param = [
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch')
            ->select('id', 'name')
            ->where('deleted', 0)
            ->whereNotIn('id', function ($query) {
                $query->select('branch_id')->from('tms_branch_master');
            });;

        if ($this->keyword) {
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }
            $data = $data->where(function ($q) {
                $q->orWhere('name', 'like', "%{$this->keyword}%")
                    ->orWhere('code', 'like', "%{$this->keyword}%")
                    ->orWhere('address', 'like', "%{$this->keyword}%")
                    ->orWhere('description', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api Lấy danh sách Người dùng gán cho điểm bán
    public function apiSaleRoomDataSearchBoxUser(Request $request)
    {
        $this->keyword = $request->input('keyword');

        $param = [
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_user_detail as tud')
            ->select('tud.user_id', 'tud.fullname')
            ->where('tud.deleted', '=', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles as mhr')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->where('r.name', '=', Role::MANAGE_MARKET)
                    ->whereRaw('mhr.model_id = tud.user_id');
            });
        /*->whereNotExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('tms_sale_rooms as tsr')
                    ->where('tsr.deleted','=',0)
                    ->whereRaw('tsr.user_id = tud.user_id');
            });*/
        if ($this->keyword) {
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(10);
        if ($this->keyword)
            $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api Lấy danh sách Tỉnh thành gán cho điểm bán
    public function apiSaleRoomDataSearchBoxCity(Request $request)
    {
        $keyword = $request->input('keyword'); //district code

        $param = [
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = TmsCity::select('id', 'name')->where([
            'deleted' => 0,
            'parent' => 0
        ]);
        if (isset($keyword) && strlen($keyword) > 0) {
            $data = $data->where('district', $keyword);
        }
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api lấy danh sách điểm bán trang điểm bán index
    public function apiSaleRoomListData(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $branchSearch = $request->input('branchSearch');
        $citySearch = $request->input('citySearch');
        $districtSearch = $request->input('districtSearch');

        $param = [
            'name' => 'text',
            'row' => 'number',
            'branchSearch' => 'text',
            'citySearch' => 'number',
            'districtSearch' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json($validator);
        }

        //Set Điểm bán từ Diva
        /*if($this->keyword){
            $saleroomCount = TmsBranch::where('code',$this->keyword)->count();
            if($saleroomCount == 0)
                setSaleRoomOnDiva($this->keyword);
        }*/
        $data = DB::table('tms_sale_rooms as tsr')
            ->select(
                'tsr.id',
                'tsr.code',
                'tsr.name',
                'tsr.address',
                'tud.user_id as user_id',
                'tud.fullname as user_name',
                'tb.name as branch_name',
                'tc.name as city_name',
                'tb.code as branch_code',
                DB::raw('(select count(tsru.id) as usercount from tms_sale_room_user tsru
                inner join tms_user_detail ud on ud.user_id = tsru.user_id
                where tsru.sale_room_id = tsr.id and ud.deleted = 0 and tsru.type = "' . TmsSaleRoomUser::POS . '") as usercount')
            )
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'tsr.user_id')
            ->leftJoin('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
            ->leftJoin('tms_branch as tb', 'tb.id', '=', 'tbsr.branch_id')
            ->leftJoin('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tbsr.branch_id')
            ->leftJoin('tms_city as tc', 'tc.id', '=', 'tcb.city_id')
            ->where('tsr.deleted', '=', 0);
        if (has_user_market()) {
            $data = $data->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id');
            $data = $data->where('tro.user_id', '=', \Auth::id());
        } elseif (has_manage_saleroom()) {
            $data = $data->where('tsr.user_id', '=', \Auth::id());
        }
        if ($branchSearch) {
            if ($branchSearch == 'all') {
                $data = $data->whereNull('tbsr.branch_id');
            } else {
                $data = $data->where('tbsr.branch_id', '=', $branchSearch);
            }
        } else {
            if ($citySearch) {
                $data = $data->where('tcb.city_id', '=', $citySearch);
            }
            if ($districtSearch) {
                $city_ids = TmsCity::where('district', $districtSearch)->pluck("id");
                $data = $data->whereIn('tcb.city_id', $city_ids);
            }
        }
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tsr.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tsr.code', 'like', "%{$this->keyword}%");
            });
        }

        $data = $data->orderBy('tsr.name', 'ASC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiSaleRoomCreate(Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $branch = $request->input('branch');
            $user_id = $request->input('user_id');
            $address = $request->input('address');

            $param = [
                'name' => 'text',
                'code' => 'code',
                'branch' => 'number',
                'user_id' => 'number',
                'address' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check_name = DB::table('tms_sale_rooms as tsr')
                ->select()
                ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                ->where('tsr.name', '=', $name)
                ->where('tbsr.branch_id', '=', $branch)
                ->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_diem_ban_da_ton_tai')
                ]);

            $check_saleroom = TmsSaleRooms::where('code', $code)->count();
            if ($check_saleroom > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_diem_ban_da_ton_tai')
                ]);
            \DB::beginTransaction();
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
            \DB::commit();
            return response()->json(status_message('success', __('them_diem_ban_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiSaleRoomDelete($saleroom_id, Request $request)
    {
        try {
            if (!is_numeric($saleroom_id))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            \DB::beginTransaction();
            $saleRoom = TmsSaleRooms::find($saleroom_id);
            if ($saleRoom) { //xóa hoàn toàn điểm bán khỏi hệ thống, đã bàn thống nhất với VuongTM va UyDD, modifier: ThoLD
                TmsBranchSaleRoom::where('sale_room_id', $saleroom_id)->delete();
                TmsSaleRoomUser::where('sale_room_id', $saleroom_id)->delete();

                $saleRoom->delete();
                //            $saleRoom->deleted = 1;
                //            $saleRoom->save();
                //            devcpt_log_system('organize','/system/organize/saleroom/edit/'.$saleroom_id,'delete','Xóa Điểm bán: '.$saleRoom['name']);
                devcpt_log_system('organize', '/system/organize/saleroom', 'delete', 'Xóa Điểm bán: ' . $saleRoom['name']);
            }
            \DB::commit();
            return response()->json(status_message('success', __('xoa_diem_ban_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiSaleRoomDetailData($saleroom_id)
    {
        $data = [];
        if (!is_numeric($saleroom_id))
            return response()->json([]);
        $saleRoom = TmsSaleRooms::with('tmsuser')->select('id', 'name', 'code', 'address', 'user_id')->findOrFail($saleroom_id);
        $data['saleroom'] = $saleRoom;
        $data['saleroom']['fullname'] = $saleRoom['tmsuser']['fullname'];
        $branchs = TmsBranchSaleRoom::with('branch_name')->where([
            'sale_room_id' => $saleroom_id
        ]);
        $branchs = $branchs->first();
        $data['saleroom']['branch_id'] = $branchs['branch_id'];
        $data['saleroom']['branch_name'] = $branchs['branch_name']['name'];

        if ($branchs['branch_id']) {
            $city = TmsCityBranch::select('city_id')->where('branch_id', $branchs['branch_id'])->first();
            $data['saleroom']['city_id'] = $city['city_id'];
        }

        return response()->json($data);
    }

    public function apiSaleRoomUpdate($saleroom_id, Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $user_id = $request->input('user_id');
            $branch_id = $request->input('branch_id');
            $address = $request->input('address');

            if (!is_numeric($saleroom_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            $param = [
                'name' => 'text',
                'code' => 'code',
                'user_id' => 'number',
                'branch_id' => 'number',
                'address' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check_name = DB::table('tms_sale_rooms as tsr')
                ->select('tsr.id')
                ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
                ->where('tsr.name', '=', $name)
                ->where('tbsr.branch_id', '=', $branch_id)
                ->whereNotIn('tsr.id', [$saleroom_id])
                ->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_diem_ban_da_ton_tai')
                ]);

            $check_saleroom = TmsSaleRooms::where('code', $code)->whereNotIn('id', [$saleroom_id])->count();
            if ($check_saleroom > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_diem_ban_da_ton_tai')
                ]);
            \DB::beginTransaction();
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

            //Đóng chức năng khi tích hợp diva
            TmsBranchSaleRoom::where('sale_room_id', $saleRoom->id)->delete();
            if ($branch_id != 0) {
                $branchRoom = new TmsBranchSaleRoom;
                $branchRoom->branch_id = $branch_id;
                $branchRoom->sale_room_id = $saleRoom->id;
                $branchRoom->save();
            }

            devcpt_log_system('organize', '/system/organize/saleroom/edit/' . $saleroom_id, 'edit', 'Sửa Điểm bán: ' . $name);
            \DB::commit();
            return response()->json(status_message('success', __('cap_nhat_diem_ban_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListBranchByCity(Request $request)
    {
        $city_id = $request->input('id');
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'id' => 'number',
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_branch as tb')
            ->select(
                'tb.id',
                'tb.code',
                'tb.name',
                'tb.address',
                'tud.fullname as user_name',
                DB::raw('(select count(tbsr.id) as saleroom_count from tms_branch_sale_room tbsr
                where tbsr.branch_id = tb.id) as saleroom_count')
            )
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'tb.user_id')
            ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tb.id')
            ->where('tcb.city_id', '=', $city_id)
            ->where('tb.deleted', '=', 0);
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tb.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tb.code', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->orderBy('tb.created_at', 'DESC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiRemoveBranch(Request $request)
    {
        try {
            $city_id = $request->input('city_id');
            $branch_id = $request->input('branch_id');

            $param = [
                'city_id' => 'number',
                'branch_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(status_message('error', __('sai_dinh_dang_ky_tu_khong_cho_phep')));
            }

            if ($city_id && $branch_id) {
                \DB::beginTransaction();
                TmsCityBranch::where([
                    'city_id' => $city_id,
                    'branch_id' => $branch_id
                ])->delete();

                $city = TmsCity::select('name')->findOrFail($city_id);
                $branch = TmsBranch::select('name')->findOrFail($branch_id);
                devcpt_log_system('organize', '/system/organize/city/edit/' . $city_id, 'remove', 'Gỡ đại lý: ' . $branch['name'] . ' khỏi Tỉnh : ' . $city['name']);
                \DB::commit();
                return response()->json(status_message('success', __('go_dai_ly_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Api danh sách Điểm bán theo đại lý
    public function apiListSaleRoomByBranch(Request $request)
    {
        $branch_id = $request->input('id');
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'id' => 'number',
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_sale_rooms as tsr')
            ->select(
                'tsr.id',
                'tsr.code',
                'tsr.name',
                'tsr.address',
                'tud.fullname as user_name',
                DB::raw('(select count(tsru.id) as usercount from tms_sale_room_user tsru
                inner join tms_user_detail ud on ud.user_id = tsru.user_id
                where tsru.sale_room_id = tsr.id and ud.deleted = 0) as usercount')
            )
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'tsr.user_id')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
            ->where('tbsr.branch_id', '=', $branch_id);
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tsr.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tsr.code', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->orderBy('tsr.created_at', 'DESC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiRemoveSaleRoom(Request $request)
    {
        try {
            $sale_room_id = $request->input('sale_room_id');
            $branch_id = $request->input('branch_id');

            $param = [
                'sale_room_id' => 'number',
                'branch_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(status_message('error', __('sai_dinh_dang_ky_tu_khong_cho_phep')));
            }

            if ($sale_room_id && $branch_id) {
                \DB::beginTransaction();
                TmsBranchSaleRoom::where([
                    'sale_room_id' => $sale_room_id,
                    'branch_id' => $branch_id
                ])->delete();

                $sale_room = TmsSaleRooms::select('name')->findOrFail($sale_room_id);
                $branch = TmsBranch::select('name')->findOrFail($branch_id);
                devcpt_log_system('organize', '/system/organize/branch/edit/' . $branch_id, 'remove', 'Gỡ điểm bán: ' . $sale_room['name'] . ' khỏi Đại lý : ' . $branch['name']);
                \DB::commit();
                return response()->json(status_message('success', __('go_diem_ban_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListAddSaleRoom(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $data = DB::table('tms_sale_rooms as tsr')
            ->select(
                'tsr.id',
                'tsr.code',
                'tsr.name',
                'tsr.address',
                'tud.fullname as user_name'
            )
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'tsr.user_id')
            ->leftJoin('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsr.id')
            ->where('tsr.deleted', '=', 0)
            ->whereNull('tbsr.branch_id');
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tsr.name', 'like', "%{$this->keyword}%")
                    ->orWhere('tsr.code', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->orderBy('tsr.created_at', 'DESC');
        $data = $data->paginate(10);
        $total = ceil($data->total() / 10);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiAddSaleRoomByBranch(Request $request)
    {
        $sale_room_add = $request->input('sale_room_add');
        $branch_id = $request->input('branch_id');
        try {
            if (!is_numeric($branch_id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            $branch = TmsBranch::select('name')->findOrFail($branch_id);
            if ($branch_id && $sale_room_add) {
                foreach ($sale_room_add as $saleRoom) {
                    \DB::beginTransaction();
                    if (!is_numeric($saleRoom)) {
                        return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                    }
                    $branchSaleRoom = new TmsBranchSaleRoom;
                    $branchSaleRoom->sale_room_id = $saleRoom;
                    $branchSaleRoom->branch_id = $branch_id;
                    $branchSaleRoom->save();

                    $saleRoomName = TmsSaleRooms::select('name')->findOrFail($saleRoom);
                    devcpt_log_system('organize', '/system/organize/branch/edit/' . $branch_id, 'add', 'Gán điểm bán: ' . $saleRoomName['name'] . ' cho Đại lý : ' . $branch['name']);
                    \DB::commit();
                }
                return response()->json(status_message('success', __('them_diem_ban_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListUserBySaleRoom(Request $request)
    {
        $sale_room_id = $request->input('id');
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $working_status = $request->input('working_status');

        $param = [
            'id' => 'number',
            'keyword' => 'text',
            'row' => 'number',
            'working_status' => 'number'
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_user_detail as tud')
            ->select(
                'tud.user_id',
                'tud.cmtnd',
                'tud.fullname',
                'tud.email',
                'mu.username'
            )
            ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
            ->join('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'tud.user_id')
            ->where('tud.deleted', '=', 0)
            ->where('tsru.sale_room_id', '=', $sale_room_id)
            ->where('tsru.type', '=', TmsSaleRoomUser::POS);

        if (strlen($working_status) != 0) {
            $data->where('tud.working_status', $working_status);
        }

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('.tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiListUserByBranchSytemOrganize(Request $request)
    {
        $branch_id = $request->input('id');
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $saleroom_ids = TmsBranchSaleRoom::where('branch_id', $branch_id)->pluck('sale_room_id');
        $user_ids = TmsSaleRoomUser::whereIn('sale_room_id', $saleroom_ids)->pluck('user_id');

        $data = TmsUserDetail::where('tms_user_detail.deleted', 0)
            ->select(
                'tms_user_detail.user_id',
                'tms_user_detail.fullname',
                'tms_user_detail.cmtnd',
                'tms_user_detail.phone',
                'tms_user_detail.email',
                'tms_sale_rooms.name as saleroom_name'
            )
            ->whereIn('tms_user_detail.user_id', $user_ids)
            ->join('tms_sale_room_user', 'tms_sale_room_user.user_id', '=', 'tms_user_detail.user_id')
            ->join('tms_sale_rooms', 'tms_sale_room_user.sale_room_id', '=', 'tms_sale_rooms.id');

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('email', 'like', "%{$this->keyword}%")
                    ->orWhere('phone', 'like', "%{$this->keyword}%");
            });
        }


        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiRemoveUser(Request $request)
    {
        try {
            $sale_room_id = $request->input('sale_room_id');
            $user_id = $request->input('user_id');

            $param = [
                'sale_room_id' => 'number',
                'user_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            if ($sale_room_id && $user_id) {
                \DB::beginTransaction();
                TmsSaleRoomUser::where([
                    'sale_room_id' => $sale_room_id,
                    'user_id' => $user_id,
                    'type' => TmsSaleRoomUser::POS
                ])->delete();

                $sale_room = TmsSaleRooms::select('name')->findOrFail($sale_room_id);
                $userName = TmsUserDetail::select('fullname')->where('user_id', $user_id)->first();
                devcpt_log_system('organize', '/system/organize/saleroom/edit/' . $sale_room_id, 'remove', 'Gỡ Nhân viên : ' . $userName['fullname'] . ' khỏi Điểm bán : ' . $sale_room['name']);
                \DB::commit();
                return response()->json(status_message('success', __('go_nguoi_dung_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListAddUser(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $this->saleroom_id = $request->input('sale_room_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'sale_room_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $city = DB::table('tms_city_branch as tcb')
            ->select('tcb.city_id')
            ->join('tms_branch_sale_room as tbsr', 'tbsr.branch_id', '=', 'tcb.branch_id')
            ->where('tbsr.sale_room_id', '=', $this->saleroom_id)
            ->get()->first();
        $this->city_id = $city->city_id;

        $data = DB::table('tms_user_detail as tud')
            ->select(
                'tud.user_id',
                'tud.fullname',
                'tud.cmtnd',
                'tud.email',
                'tud.phone'
            )
            ->where('tud.deleted', '=', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_sale_room_user as tsru')
                    ->where('tsru.sale_room_id', '=', $this->saleroom_id)
                    ->where('.tsru.type', '=', TmsSaleRoomUser::POS)
                    ->whereRaw('tsru.user_id = tud.user_id');
            });

        if ($this->city_id) {
            $data = $data
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tms_sale_room_user as tsru')
                        ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tsru.sale_room_id')
                        ->where('tcb.city_id', '!=', $this->city_id)
                        ->where('.tsru.type', '=', TmsSaleRoomUser::AGENTS)
                        ->whereRaw('tsru.user_id = tud.user_id');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tms_sale_room_user as tsru')
                        ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsru.sale_room_id')
                        ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tbsr.branch_id')
                        ->where('tcb.city_id', '!=', $this->city_id)
                        ->where('.tsru.type', '=', TmsSaleRoomUser::POS)
                        ->whereRaw('tsru.user_id = tud.user_id');
                });
        }

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.phone', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->orderBy('tud.created_at', 'DESC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiAddUserBySaleRoom(Request $request)
    {
        try {
            $user_add = $request->input('user_add');
            $sale_room_id = $request->input('sale_room_id');

            $param = [
                'sale_room_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator) && !is_array($user_add)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            $saleRoom = TmsSaleRooms::select('name')->findOrFail($sale_room_id);
            if ($sale_room_id && $user_add) {
                foreach ($user_add as $user_id) {
                    \DB::beginTransaction();
                    $user = TmsUserDetail::select('fullname')->where('user_id', $user_id)->first();
                    $saleRoomCheck = TmsSaleRoomUser::select('id')
                        ->where([
                            'sale_room_id' => $sale_room_id,
                            'user_id' => $user_id,
                            'type' => TmsSaleRoomUser::POS
                        ])->count();
                    if ($saleRoomCheck == 0) {
                        $branch_check = TmsSaleRoomUser::select('id')
                            ->where([
                                'sale_room_id' => $sale_room_id,
                                'user_id' => $user_id,
                                'type' => TmsSaleRoomUser::AGENTS
                            ])->first();
                        if (!$branch_check) {
                            $saleRoomUser = new TmsSaleRoomUser;
                            $saleRoomUser->sale_room_id = $sale_room_id;
                            $saleRoomUser->user_id = $user_id;
                            $saleRoomUser->save();
                        } else {
                            $branch_check->type = TmsSaleRoomUser::POS;
                            $branch_check->save();
                        }
                        devcpt_log_system('organize', '/system/organize/saleroom/edit/' . $sale_room_id, 'add', 'Gán Nhân viên: ' . $user['fullname'] . ' cho Điểm bán : ' . $saleRoom['name']);
                    }/*else{
                        return response()->json(status_message('error','Nhân viên đã thuộc điểm bán. Không thể thêm!'));
                    }*/

                    \DB::commit();
                }
                return response()->json(status_message('success', __('them_nguoi_dung_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Api lấy danh sách tỉnh thành theo khu vực
    public function apiGetCityByDistrict(Request $request)
    {
        $district = $request->input('district');
        $param = [
            'district' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        $citys = TmsCity::where('district', $district)->get()->toArray();
        return response()->json($citys);
    }

    //Api lấy danh sách tỉnh thành theo chi nhánh
    public function apiGetCityByDepartment(Request $request)
    {
        $department = $request->input('department');
        $validates = validate_fails($request, [
            'department' => 'number',
        ]);
        if (!empty($validates)) {
            //var_dump($validates);
        } else {
            $citys = TmsCity::whereIn('id', function ($query) use ($department) {
                $query->select('city_id')->from('tms_department_citys')->where('department_id', $department);
            })->get()->toArray();
            return response()->json($citys);
        }
    }

    //Api lấy danh sách Đại lý theo Tỉnh
    public function apiGetBranchByCity(Request $request)
    {
        $city = $request->input('city');
        $saleroom_id = $request->input('saleroom_id');

        $param = [
            'city' => 'number',
            'saleroom_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json($validator);
        }

        $branchNotIn = TmsBranchSaleRoom::select('branch_id')->where([
            'sale_room_id' => $saleroom_id
        ])->first();
        $branchs = TmsCityBranch::with('branch')
            ->where('city_id', $city);
        if ($branchNotIn) {
            $branchs = $branchs->whereNotIn('branch_id', [$branchNotIn]);
        }
        $branchs = $branchs->get()->toArray();
        return response()->json($branchs);
    }

    //Api id tỉnh dựa vào id branch
    public function apiGetCityByBranch(Request $request)
    {
        $input_search_box_id = $request->input('input_search_box_id');

        $param = [
            'input_search_box_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json($validator);
        }
        $city = TmsCityBranch::where('branch_id', '=', $input_search_box_id)->first();
        return response()->json($city);
    }

    //Api lấy danh sách Đại lý mà nhân viên giám sát được quản lý
    public function apiGetBranchByUserMarket(Request $request)
    {
        $data = DB::table('tms_branch as tb')
            ->select(
                'tb.id',
                'tb.name'
            );

        $data = $data->join('tms_role_organize as tro', 'tro.organize_id', '=', 'tb.id');
        $data = $data->where('tro.user_id', '=', \Auth::id());
        $data = $data->where('tb.deleted', '=', 0);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //Api lấy tất cả tỉnh thành đã gán đại lý
    public function apiGetCityAllBranch()
    {
        $citys = TmsCityBranch::pluck('city_id')->toArray();
        array_unique($citys);
        $city = TmsCity::where('deleted', 0)->whereIn('id', $citys)->get()->toArray();
        return response()->json($city);
    }

    //Api lấy tất cả đại lý đã gán điểm bán
    public function apiGetBranchAllSaleRoom()
    {
        $branchs = TmsBranchSaleRoom::pluck('branch_id')->toArray();
        array_unique($branchs);
        $branch = TmsBranch::where('deleted', 0)->whereIn('id', $branchs)->get()->toArray();
        return response()->json($branch);
    }

    public function apiImportCity(Request $request)
    {
        if (!$request->file('file')) {
            return response()->json([
                'fileError' => 'error'
            ]);
        }

        $path = $request->file('file')->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $csv_data = array_slice($data, 1);

        $this->importOutput['rowSuccess'] = 0;
        $this->importOutput['rowError'] = 0;
        $this->importOutput['error'] = [];
        foreach ($csv_data as $key => $row) {
            if (!empty($row)) {
                $tmsCityCount = TmsCity::select('id')->where('code', $row['0'])->count();
                if ($tmsCityCount == 0) {
                    $tmsCity = new TmsCity;
                    $tmsCity->name = $row['1'];
                    $tmsCity->code = $row['0'];
                    $tmsCity->type = $row['2'];
                    $tmsCity->parent = $row['3'];
                    $tmsCity->district = '';
                    $tmsCity->save();
                    $this->importOutput['rowSuccess']++;
                } else {
                    $this->importOutput['rowError']++;
                    $this->importOutput['error'][] = $row['0'] . ' , ' . $row['1'];
                }
            }
        }
        return response()->json($this->importOutput);
    }

    public function apiGetListUserByBranch(Request $request)
    {
        $branch_id = $request->input('id');
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $this->branch_id = $branch_id;

        $param = [
            'id' => 'number',
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_user_detail as tud')
            ->select(
                'tud.user_id',
                'tud.cmtnd',
                'tud.fullname',
                'tud.email',
                'mu.username',
                'tsr.name as saleroom_name',
                'tsru.id'
            )
            ->join('mdl_user as mu', 'mu.id', '=', 'tud.user_id')
            ->join('tms_sale_room_user as tsru', 'tsru.user_id', '=', 'mu.id')
            ->leftJoin('tms_branch_sale_room as tbsr', function ($join) {
                $join->on('tbsr.sale_room_id', '=', 'tsru.sale_room_id');
                $join->where('tsru.type', '=', TmsSaleRoomUser::POS);
            })
            ->leftJoin('tms_sale_rooms as tsr', 'tsr.id', '=', 'tbsr.sale_room_id')
            ->where('tud.deleted', '=', 0);
        $data = $data->where(function ($q) {
            $q->where('tsru.sale_room_id', '=', $this->branch_id)
                ->where('.tsru.type', '=', TmsSaleRoomUser::AGENTS);
            $q->orWhere(function ($qu) {
                $qu->where('tbsr.branch_id', '=', $this->branch_id)
                    ->where('.tsru.type', '=', TmsSaleRoomUser::POS);
            });
        });
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('.tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('mu.username', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiBranchRemoveUser(Request $request)
    {
        try {
            $id = $request->input('id');

            $param = [
                'id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            if ($id) {
                \DB::beginTransaction();
                $tms_sr = TmsSaleRoomUser::findOrFail($id);
                if ($tms_sr['type'] == TmsSaleRoomUser::POS) {
                    $sale_room = TmsSaleRooms::select('name')->findOrFail($tms_sr['sale_room_id']);
                    $word_name = $sale_room['name'];
                    $url = '/system/organize/saleroom/edit/' . $tms_sr['sale_room_id'];
                    $word = ' khỏi Điểm bán : ';
                } else {
                    $branch = TmsBranch::select('name')->findOrFail($tms_sr['sale_room_id']);
                    $word_name = $branch['name'];
                    $url = '/system/organize/branch/edit/' . $tms_sr['sale_room_id'];
                    $word = ' khỏi Đại lý : ';
                }
                $userName = TmsUserDetail::select('fullname')->where('user_id', $tms_sr['user_id'])->first();

                $tms_sr->delete();
                devcpt_log_system('organize', $url, 'remove', 'Gỡ Nhân viên : ' . $userName['fullname'] . $word . $word_name);
                \DB::commit();
                return response()->json(status_message('success', __('go_nguoi_dung_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiListAddUserBranch(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $this->branch_id = $request->input('branch_id');

        $param = [
            'keyword' => 'text',
            'row' => 'number',
            'branch_id' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $city = DB::table('tms_city_branch as tcb')
            ->select('tcb.city_id')
            ->where('tcb.branch_id', '=', $this->branch_id)
            ->get()->first();
        $this->city_id = $city->city_id;

        $data = DB::table('tms_user_detail as tud')
            ->select(
                'tud.user_id',
                'tud.fullname',
                'tud.cmtnd',
                'tud.email',
                'tud.phone'
            )
            ->where('tud.deleted', '=', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_sale_room_user as tsru')
                    ->where('tsru.sale_room_id', '=', $this->branch_id)
                    ->where('.tsru.type', '=', TmsSaleRoomUser::AGENTS)
                    ->whereRaw('tsru.user_id = tud.user_id');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_sale_room_user as tsru')
                    ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsru.sale_room_id')
                    ->where('tbsr.branch_id', '=', $this->branch_id)
                    ->where('.tsru.type', '=', TmsSaleRoomUser::POS)
                    ->whereRaw('tsru.user_id = tud.user_id');
            });

        if ($this->city_id) {
            $data = $data
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tms_sale_room_user as tsru')
                        ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tsru.sale_room_id')
                        ->where('tcb.city_id', '!=', $this->city_id)
                        ->where('.tsru.type', '=', TmsSaleRoomUser::AGENTS)
                        ->whereRaw('tsru.user_id = tud.user_id');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tms_sale_room_user as tsru')
                        ->join('tms_branch_sale_room as tbsr', 'tbsr.sale_room_id', '=', 'tsru.sale_room_id')
                        ->join('tms_city_branch as tcb', 'tcb.branch_id', '=', 'tbsr.branch_id')
                        ->where('tcb.city_id', '!=', $this->city_id)
                        ->where('.tsru.type', '=', TmsSaleRoomUser::POS)
                        ->whereRaw('tsru.user_id = tud.user_id');
                });
        }

        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.phone', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->orderBy('tud.created_at', 'DESC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
            'keyword' => $this->keyword
        ];
        return response()->json($response);
    }

    public function apiAddUserByBranch(Request $request)
    {
        try {
            $user_add = $request->input('user_add');
            $this->branch_id = $request->input('branch_id');

            $param = [
                'branch_id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator) && !is_array($user_add)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }

            $branch = TmsBranch::select('name')->findOrFail($this->branch_id);
            if ($this->branch_id != 0 && $user_add) {
                foreach ($user_add as $user_id) {
                    \DB::beginTransaction();
                    $branch_check = DB::table('tms_sale_room_user as tsru')
                        ->select('tsru.id')
                        ->join('tms_user_detail as tud', 'tud.user_id', '=', 'tsru.user_id')
                        ->leftJoin('tms_branch_sale_room as tbsr', function ($join) {
                            $join->on('tbsr.sale_room_id', '=', 'tsru.sale_room_id');
                            $join->where('tsru.type', TmsSaleRoomUser::POS);
                        })
                        ->where('tud.deleted', '=', 0)
                        ->where('tud.user_id', '=', $user_id);
                    $branch_check = $branch_check->where(function ($q) {
                        $q->where('tsru.sale_room_id', '=', $this->branch_id)
                            ->where('.tsru.type', '=', TmsSaleRoomUser::AGENTS);
                        $q->orWhere(function ($qu) {
                            $qu->where('tbsr.branch_id', '=', $this->branch_id)
                                ->where('.tsru.type', '=', TmsSaleRoomUser::POS);
                        });
                    });
                    $branch_check = $branch_check->count();
                    if ($branch_check == 0) {
                        $saleRoomUser = new TmsSaleRoomUser;
                        $saleRoomUser->sale_room_id = $this->branch_id;
                        $saleRoomUser->user_id = $user_id;
                        $saleRoomUser->type = TmsSaleRoomUser::AGENTS;
                        $saleRoomUser->save();
                        $user = TmsUserDetail::select('fullname')->where('user_id', $user_id)->first();
                        devcpt_log_system('organize', '/system/organize/branch/edit/' . $this->branch_id, 'add', 'Gán Nhân viên: ' . $user['fullname'] . ' cho Đại lý : ' . $branch['name']);
                    }/*else{
                        return response()->json(status_message('error','Nhân viên đã thuộc Đại lý. Không thể thêm!'));
                    }*/

                    \DB::commit();
                }
                return response()->json(status_message('success', __('them_nhan_vien_thanh_cong')));
            } else {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiGetBranchName(Request $request)
    {
        try {
            $branch = TmsBranch::select('name')->find($request->input('branch_id'));
            return response()->json($branch['name']);
        } catch (\Exception $e) {
            return response()->json(__('chon_dai_ly'));
        }
    }

    //Api Lấy danh sách Người dùng gán cho Chi nhánh
    //form thêm mới và cập nhật chi nhánh
    public function apiDepartmentDataSearchBoxUser(Request $request)
    {
        $this->keyword = $request->input('keyword');

        $param = [
            'keyword' => 'text',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_user_detail as tud')
            ->select('tud.user_id', 'tud.fullname')
            ->where('tud.deleted', '=', 0)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tms_departments as td')
                    ->whereRaw('td.manage = tud.user_id');
            });

        if ($this->keyword) {

            $data = $data->where(function ($q) {
                $q->orWhere('tud.fullname', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.cmtnd', 'like', "%{$this->keyword}%")
                    ->orWhere('tud.email', 'like', "%{$this->keyword}%");
            });
        }
        $data = $data->skip(0)->take(20);
        $data = $data->get()->toArray();
        return response()->json($data);
    }

    //List all chi nhánh
    //IndexDepartmentComponent.vue
    public function apiDepartmentListAll(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');

        $param = [
            'row' => 'number',
            'keyword' => 'text'
        ];
        $validator = validate_fails($request, $param);

        if (!empty($validator)) {
            return response()->json([]);
        }

        $data = DB::table('tms_departments as td')
            ->select(
                'td.id',
                'td.name',
                'td.code',
                'td.des',
                'tud.fullname',
                DB::raw('(select count(tdc.id) as city_count from tms_department_citys tdc
                where tdc.department_id = td.id) as city_count')
            )
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'td.manage');
        if ($this->keyword) {
            $data = $data->where(function ($q) {
                $q->orWhere('td.name', 'like', "%{$this->keyword}%")
                    ->orWhere('td.code', 'like', "%{$this->keyword}%");
            });
        }

        $data = $data->orderBy('td.code', 'ASC');
        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $totalRow = $data->total();
        $response = [
            'pagination' => [
                'total' => $total,
                'totalRow' => $totalRow,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    //Tạo mới chi nhánh
    //IndexDepartmentComponent.vue
    public function apiDepartmentCreate(Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $manage = $request->input('manage');
            $des = $request->input('des');

            $param = [
                'name' => 'text',
                'code' => 'code',
                'manage' => 'number',
                'des' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check_name = TmsDepartments::where('name', $name)->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_chi_nhanh_da_ton_tai')
                ]);

            $check = TmsDepartments::where('code', $code)->count();
            if ($check > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_chi_nhanh_da_ton_tai')
                ]);
            $tmsDepartment = new TmsDepartments;
            $tmsDepartment->name = $name;
            $tmsDepartment->code = $code;
            $tmsDepartment->manage = $manage;
            $tmsDepartment->des = $des;
            $tmsDepartment->save();

            devcpt_log_system('organize', '/system/organize/departments/edit/' . $tmsDepartment->id, 'create', 'Thêm mới Chi nhánh: ' . $name);
            return response()->json(status_message('success', __('them_moi_chi_nhanh_thanh_cong')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Xóa chi nhánh
    //IndexDepartmentComponent.vue
    public function apiDepartmentDelete($id, Request $request)
    {
        try {
            \DB::beginTransaction();

            if (!is_numeric($id)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            $department = TmsDepartments::findOrFail($id);
            if ($department) {
                $department->delete();

                TmsDepartmentCity::where('department_id', $id)->delete();
                devcpt_log_system('organize', '/system/organize/departments/edit/' . $id, 'delete', 'Xóa Chi nhánh: ' . $department['name']);
            }
            \DB::commit();
            return response()->json(status_message('success', __('delete_branch_success')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    //Thông tin chi tiết chi nhanh
    //Form edit chi nhánh
    public function apiDepartmentsDetailData($id)
    {
        if (!is_numeric($id))
            return response()->json([]);
        $data = DB::table('tms_departments as td')
            ->select('td.id', 'td.name', 'td.code', 'td.des', 'td.manage', 'tud.fullname')
            ->leftJoin('tms_user_detail as tud', 'tud.user_id', '=', 'td.manage')
            ->where('td.id', '=', $id)->first();
        return response()->json($data);
    }

    //Cập nhật chi nhánh
    public function apiDepartmentUpdate(Request $request)
    {
        try {
            $name = $request->input('name');
            $code = $request->input('code');
            $manage = $request->input('manage');
            $des = $request->input('des');
            $id = $request->input('id');

            $param = [
                'name' => 'text',
                'code' => 'code',
                'manage' => 'number',
                'des' => 'text',
                'id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            $check_name = TmsDepartments::where('name', $name)->whereNotIn('id', [$id])->count();
            if ($check_name > 0)
                return response()->json([
                    'key' => 'name',
                    'message' => __('ten_chi_nhanh_da_ton_tai')
                ]);

            $check = TmsDepartments::where('code', $code)->whereNotIn('id', [$id])->count();
            if ($check > 0)
                return response()->json([
                    'key' => 'code',
                    'message' => __('ma_chi_nhanh_da_ton_tai')
                ]);
            $tmsDepartment = TmsDepartments::find($id);
            $tmsDepartment->name = $name;
            $tmsDepartment->code = $code;
            $tmsDepartment->manage = $manage;
            $tmsDepartment->des = $des;
            $tmsDepartment->save();

            devcpt_log_system('organize', '/system/organize/departments/edit/' . $tmsDepartment->id, 'update', 'Cập nhật Chi nhánh: ' . $name);
            return response()->json(status_message('success', __('update_branch_success')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }


    public function apiDepartmentCity(Request $request)
    {
        try {
            $row = $request->input('row');
            $keyword = $request->input('keyword');
            $id = $request->input('id');

            $param = [
                'row' => 'number',
                'keyword' => 'code',
                'id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = DB::table('tms_city as tc')
                ->select(
                    'tdc.id as tcd_id',
                    'tc.name',
                    'tc.code',
                    'tc.district',
                    'tdc.city_id',
                    DB::raw('(select count(tcb.id) as branch_count from tms_city_branch tcb
                where tcb.city_id = tc.id) as branch_count')
                )
                ->join('tms_department_citys as tdc', 'tdc.city_id', '=', 'tc.id')
                ->where('tc.parent', '=', 0)
                ->where('tdc.department_id', '=', $id);
            if ($keyword) {
                $data = $data->where(function ($q) use ($keyword) {
                    $q->orWhere('tc.name', 'like', "%{$keyword}%")
                        ->orWhere('tc.code', 'like', "%{$keyword}%");
                });
            }

            $data = $data->orderBy('tc.code', 'ASC');
            $data = $data->paginate($row);
            $total = ceil($data->total() / $row);
            $totalRow = $data->total();
            $response = [
                'pagination' => [
                    'total' => $total,
                    'totalRow' => $totalRow,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
            ];
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    public function apiDepartmentListCityAdd(Request $request)
    {
        try {
            $row = $request->input('row');
            $keyword = $request->input('keyword');

            $param = [
                'row' => 'number',
                'keyword' => 'text',
                'id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json([]);
            }

            $data = DB::table('tms_city as tc')
                ->select(
                    'tdc.id as tcd_id',
                    'tc.name',
                    'tc.code',
                    'tc.district',
                    'tc.id as city_id',
                    DB::raw('(select count(tcb.id) as branch_count from tms_city_branch tcb
                where tcb.city_id = tc.id) as branch_count')
                )
                ->leftJoin('tms_department_citys as tdc', 'tdc.city_id', '=', 'tc.id')
                ->where('tc.parent', '=', 0)
                ->whereNull('tdc.id');
            if ($keyword) {
                $data = $data->where(function ($q) use ($keyword) {
                    $q->orWhere('tc.name', 'like', "%{$keyword}%")
                        ->orWhere('tc.code', 'like', "%{$keyword}%");
                });
            }

            $data = $data->orderBy('tc.code', 'ASC');
            $data = $data->paginate($row);
            $total = ceil($data->total() / $row);
            $totalRow = $data->total();
            $response = [
                'pagination' => [
                    'total' => $total,
                    'totalRow' => $totalRow,
                    'current_page' => $data->currentPage(),
                ],
                'data' => $data,
            ];
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    public function apiDepartmentRemoveCity(Request $request)
    {
        try {
            $id = $request->input('id');

            $param = [
                'id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            \DB::beginTransaction();
            $tdc = TmsDepartmentCity::find($id);
            $tdc->delete();
            $department = TmsDepartments::find($tdc->department_id);
            $city = TmsCity::find($tdc->city_id);

            devcpt_log_system('organize', '/system/organize/departments/edit/' . $tdc->department_id, 'update', 'Gỡ Tỉnh thành ' . $city['name'] . ' khỏi Chi nhánh: ' . $department['name']);
            \DB::commit();
            return response()->json(status_message('success', __('remove_city_from_branch_success')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiDepartmentAddCity(Request $request)
    {
        try {
            $id = $request->input('id');
            $city_add = $request->input('city_add');

            $param = [
                'id' => 'number',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                return response()->json($validator);
            }

            \DB::beginTransaction();
            $department = TmsDepartments::find($id);
            if (empty($city_add)) {
                return response()->json(status_message('error', __('not_select_province')));
            }

            $data = [];
            $data_item = [];
            foreach ($city_add as $city_id) {
                $data_item['city_id'] = $city_id;
                $data_item['department_id'] = $id;
                array_push($data, $data_item);
            }
            TmsDepartmentCity::insert($data);

            devcpt_log_system('organize', '/system/organize/departments/edit/' . $id, 'update', 'Gán Tỉnh thành cho Chi nhánh: ' . $department['name']);
            \DB::commit();
            return response()->json(status_message('success', __('add_city_success')));
        } catch (Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiGetDepartmentList(Request $request)
    {
        try {
            $data = TmsDepartments::get()->toArray();
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }
    // End SystemOrganizeController

    // TrainningController

    public function apiCreateTrainning(Request $request)
    {
        $response = new ResponseModel();
        try {
            $code = $request->input('code');
            $name = $request->input('name');

            $param = [
                'code' => 'code',
                'name' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            \DB::beginTransaction();
            //check course info exist
            $trainningInfo = TmsTrainningProgram::select('id')->where('code', $code)->first();

            if ($trainningInfo) {
                $response->status = false;
                $response->message = __('ma_trainning_da_ton_tai');
                return response()->json($response);
            }

            $tms_trainning = TmsTrainningProgram::firstOrCreate([
                'code' => $code,
                'name' => $name
            ]);

            $mdl_cate = MdlCourseCategory::firstOrCreate([
                'name' => $name,
                'idnumber' => 'CT.0' . $tms_trainning->id,
                'parent' => 0,
                'visible' => 1,
                'depth' => 1,
                'path' => '/' . $tms_trainning->id
            ]);


            TmsTrainningCategory::firstOrCreate([
                'trainning_id' => $tms_trainning->id,
                'category_id' => $mdl_cate->id
            ]);


            \DB::commit();
            $response->status = true;
            $response->otherData = $tms_trainning->id;
            $response->message = __('them_moi_khung_nang_luc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function apiGetDetailTrainning($id)
    {
        $id = is_numeric($id) ? $id : 0;
        $trainning = DB::table('tms_traninning_programs as ttp')
            ->join('tms_trainning_categories as ttc', 'ttc.trainning_id', '=', 'ttp.id')
            ->where('ttp.id', '=', $id)
            ->select('ttp.id', 'ttp.code', 'ttp.name', 'ttc.category_id')
            ->first();

        return response()->json($trainning);
    }

    public function apiEditTrainning($id, Request $request)
    {
        $response = new ResponseModel();
        try {
            $code = $request->input('code');
            $name = $request->input('name');

            $param = [
                'code' => 'code',
                'name' => 'text',
            ];
            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            \DB::beginTransaction();
            $trainning = TmsTrainningProgram::findOrFail($id);

            $trainningInfo = TmsTrainningProgram::select('id')->whereNotIn('id', [$trainning->id])->where('code', $code)->first();
            if ($trainningInfo) {
                $response->status = false;
                $response->message = __('ma_trainning_da_ton_tai');
                return response()->json($response);
            }

            $trainning->code = $code;
            $trainning->name = $name;
            $trainning->save();

            \DB::commit();
            $response->status = true;
            $response->message = __('sua_khung_nang_luc_thanh_cong');
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function apiDeteleTrainning(Request $request)
    {
        $response = new ResponseModel();
        try {
            $id = $request->input('id');

            $param = [
                'id' => 'number'
            ];

            $validator = validate_fails($request, $param);
            if (!empty($validator)) {
                $response->status = false;
                $response->message = __('dinh_dang_du_lieu_khong_hop_le');
                return response()->json($response);
            }

            $trainning = TmsTrainningProgram::findOrFail($id);
            $trainning->deleted = 1;
            $trainning->save();

            $response->status = true;
            $response->message = __('xoa_khung_nang_luc_thanh_cong');
        } catch (\Exception $e) {
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function apiTrainningList(Request $request)
    {
        try {
            $data = TmsTrainningProgram::where('deleted', 0)->get()->toArray();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function apiTrainningChange(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $trainning_id = $request->input('trainning_id');

            $param = [
                'user_id' => 'number',
                'trainning_id' => 'number',
            ];
            $validator = validate_fails($request, $param);

            if (!empty($validator)) {
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            }
            $trainning = TmsTrainningUser::where([
                'user_id' => $user_id
            ])->first();
            if ($trainning['trainning_id'] != $trainning_id && $trainning_id != 0) {
                $trainning->trainning_id = $trainning_id;
                $trainning->save();
                //$category = TmsTrainningCategory::select('category_id')->where('trainning_id', $trainning_id)->first();
                //enrole cho học viên theo khung năng lực
                //\DB::beginTransaction();
                training_enrole($user_id, $trainning_id);
                //\DB::commit();
            }
            return response()->json(status_message('success', __('cap_nhat_khung_nang_luc_thanh_cong')));
        } catch (\Exception $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }

    public function apiUpdateUserTrainning($trainning_id)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = is_numeric($trainning_id) ? $trainning_id : 0;

            $lstData = DB::table('mdl_user_enrolments')
                ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                ->join('mdl_user', 'mdl_user.id', '=', 'mdl_user_enrolments.userid')
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                ->where('mdl_course.category', '=', 3)
                ->select('mdl_user.id')
                ->groupBy('mdl_user.id')->pluck('mdl_user.id');

            $count_dt = count($lstData);
            $arrData = [];
            $data_item = [];
            if ($count_dt > 0) {
                foreach ($lstData as $data) {
                    $data_item['trainning_id'] = $trainning_id;
                    $data_item['user_id'] = $data;

                    array_push($arrData, $data_item);
                }

                TmsTrainningUser::insert($arrData);
            }
            $response->status = true;
            $response->message = 'success';
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function apiUpdateStudentTrainning($trainning_id)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = is_numeric($trainning_id) ? $trainning_id : 0;

            $lstData = DB::table('mdl_user as u')
                ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->select('u.id')
                ->where('r.name', '=', \App\Role::STUDENT)
                ->groupBy('u.id')->pluck('u.id');

            $count_dt = count($lstData);
            $arrData = [];
            $data_item = [];
            if ($count_dt > 0) {
                foreach ($lstData as $data) {
                    $data_item['trainning_id'] = $trainning_id;
                    $data_item['user_id'] = $data;

                    array_push($arrData, $data_item);
                }

                TmsTrainningUser::insert($arrData);
            }
            $response->status = true;
            $response->message = 'success';
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function apiUpdateUserMarket($trainning_id)
    {
        $response = new ResponseModel();
        try {
            $trainning_id = is_numeric($trainning_id) ? $trainning_id : 0;

            $role = DB::table('roles')->where('name', '=', Role::MANAGE_MARKET)->first();

            $lstData = DB::table('model_has_roles as mhr')
                ->join('mdl_user as u', 'u.id', '=', 'mhr.model_id')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->where('r.id', '=', $role->id)
                ->select('u.id')->groupBy('u.id')->pluck('u.id');

            $count_dt = count($lstData);
            $arrData = [];
            $data_item = [];
            if ($count_dt > 0) {
                foreach ($lstData as $data) {
                    $data_item['trainning_id'] = $trainning_id;
                    $data_item['user_id'] = $data;

                    array_push($arrData, $data_item);
                }

                TmsTrainningUser::insert($arrData);
            }
            $response->status = true;
            $response->message = 'success';
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function apiUpdateUserMarketCourse($course_id)
    {
        $response = new ResponseModel();
        try {
            $course_id = is_numeric($course_id) ? $course_id : 0;

            $role = DB::table('roles')->where('name', '=', Role::MANAGE_MARKET)->first();

            $lstData = DB::table('model_has_roles as mhr')
                ->join('mdl_user as u', 'u.id', '=', 'mhr.model_id')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->where('r.id', '=', $role->id)
                ->select('u.id')->groupBy('u.id')->pluck('u.id');

            //Update performance 02/03/2020 by cuonghq
            enrole_user_to_course_multiple($lstData,  $role->id, $course_id);

//            $count_dt = count($lstData);
//            if ($count_dt > 0) {
//                foreach ($lstData as $data) {
//                    enrole_user_to_course($data, $role->id, $course_id, 0);
//                    sleep(0.01);
//                }
//            }

            $response->status = true;
            $response->message = 'success';
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }

    public function apiUpdateUserBGT()
    {
        $response = new ResponseModel();
        try {
            $pass = '123456789';

            MdlUser::where('id', '>', 1389)->update(['password' => bcrypt($pass)]);

            $response->status = true;
            $response->message = 'success';
        } catch (\Exception $e) {
            \DB::rollBack();
            $response->status = false;
            $response->message = $e->getMessage();
        }
        return response()->json($response);
    }
    // End TrainningController

    // UserExamController
    public function getListUser(Request $request)
    {
        $this->keyword = $request->input('keyword');
        $row = $request->input('row');
        $param = [
            'keyword' => 'text',
            'row' => 'number',
        ];
        $validator = validate_fails($request, $param);
        if (!empty($validator)) {
            return response()->json([]);
        }
        // Get user
        $data = DB::table(DB::raw('(select qa.userid, count(qa.id) as attempt_time
        from mdl_quiz as q inner join mdl_quiz_attempts as qa on q.id = qa.quiz
        where q.course = 251
        group by qa.userid) as data1'))
            ->join(DB::raw('(select userid, finalgrade
        from mdl_grade_grades
        where itemid = 1070 and finalgrade < 7) as data2'), 'data1.userid', '=', 'data2.userid')
            ->join('tms_user_detail as tud', 'data1.userid', '=', 'tud.user_id')
            ->join('mdl_user as u', 'data1.userid', '=', 'u.id')
            ->where('data1.attempt_time', '>=', '2')
            ->select('u.username', 'tud.cmtnd', 'tud.fullname', 'tud.email', 'data1.userid as user_id', 'data1.attempt_time', 'data2.finalgrade');

        $data = $data->paginate($row);
        $total = ceil($data->total() / $row);
        $response = [
            'pagination' => [
                'total' => $total,
                'current_page' => $data->currentPage(),
            ],
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function apiRestUserExam(Request $request)
    {
        try {
            $now = Carbon::now();
            $user_restore = $request->input('user_restore');
            if (!is_array($user_restore))
                return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
            \DB::beginTransaction();

            foreach ($user_restore as $user_id) {
                if (!is_numeric($user_id))
                    return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
                $mdlUser = MdlUser::findOrFail($user_id);
                if ($mdlUser) {
                    $finalquizid = MdlQuiz::where('course', '=', 251)->first()->id;
                    $resetexam = MdlQuizAttempts::where([
                        ['quiz', '=', $finalquizid],
                        ['userid', '=', $user_id]
                    ])->delete();
                    $type = 'user';
                    $url = '/education/resetexam/resetuser';
                    $action = 'reset';
                    $info = 'Cho phép thi lại ' . $mdlUser['username'];
                    devcpt_log_system($type, $url, $action, $info);
                }
            }
            \DB::commit();
            return response()->json(status_message('success', __('khoi_phuc_tai_khoan_thanh_cong')));
        } catch (\Exception  $e) {
            return response()->json(status_message('error', __('loi_he_thong_thao_tac_that_bai')));
        }
    }
    // End UserExamController

}
