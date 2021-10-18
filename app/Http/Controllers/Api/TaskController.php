<?php

namespace App\Http\Controllers\Api;

use App\CourseCompletion;
use App\CourseFinal;
use App\Http\Controllers\Controller;
use App\Mail\CourseSendMail;
use App\MdlCourse;
use App\MdlCourseCompletions;
use App\MdlEnrol;
use App\MdlGradeGrade;
use App\MdlGradeItem;
use App\MdlHvp;
use App\MdlRoleAssignments;
use App\MdlUser;
use App\MdlUserEnrolments;
use App\ModelHasRole;
use App\Role;
use App\StudentCertificate;
use App\TmsLearnerHistory;
use App\TmsLog;
use App\TmsNotification;
use App\TmsSaleRoomUser;
use App\TmsTrainningComplete;
use App\TmsTrainningCourse;
use App\TmsTrainningGroup;
use App\TmsTrainningProgram;
use App\TmsTrainningUser;
use App\TmsUserDetail;
use Carbon\Carbon;
use Horde\Socket\Client\Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Storage;

class TaskController extends Controller
{
    public function resetAdminPassword()
    {
        MdlUser::findOrFail(2)->update([
            'password' => bcrypt("Bgt@2019")
        ]);
        echo "Success!";
    }

    // Update email và action cho toàn bộ user
    public function apiUpdateEmailAndAction()
    {
        try {
            // Xóa đi bản ghi trùng
            $query = 'DELETE u1 FROM mdl_user as u1 INNER JOIN mdl_user as u2 WHERE u1.id < u2.id AND u1.username = u2.username';
            $deleted = DB::delete($query);

            // Cập nhật email + action
            $query = 'UPDATE mdl_user SET email = CONCAT(username,"@gmail.com"), active = 1';
            DB::update($query);
            return "SUCCESS";
        } catch (\Horde\Socket\Client\Exception $e) {
            return "ERROR";
        }
    }

    //region deleted course restore after 15 days
    public function deleteCourseRestore()
    {
        $listCourses = DB::table('mdl_course')
            ->leftJoin('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
            ->where('mdl_course.deleted', '=', 1)
            ->select(
                'mdl_course.id', 'mdl_course.updated_at'
            )->get();

        $now = date('Y-m-d', strtotime(Carbon::now()));
        $arr_course = [];
        foreach ($listCourses as $course) {
            $updated_at = date('Y-m-d', strtotime($course->updated_at));
            if ((strtotime($now) - strtotime($updated_at)) > 15 * 60 * 60 * 24) { // xoa cac khoa sau 15 ngay
                array_push($arr_course, $course->id);
            }
        }


        MdlCourse::whereIn('id', $arr_course)->delete();

    }
    //endregion

    #region insert student to course_completion courses certificate
    //insert hoc vien da hoan thanh khoa hoc vao bang course_completion
    // fix cho TH hoc vien duoc enrol doc lap vao khoa, ko lien quan den KNL
    public function completeCourseSingle()
    {
        $lstUserCourse = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->leftJoin('course_completion as courc', function ($join) {
                $join->on('courc.userid', '=', 'u.id');
                $join->on('courc.courseid', '=', 'c.id');
            })
            ->where('c.category', '!=', 2)//ko phai thu vien khoa hoc
            ->whereNull('courc.id')
            ->select('u.id as user_id',
                'c.id as course_id',
                DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm inner join
                mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on
                cm.course = cs.course and cm.section = cs.id inner join mdl_course cc on cm.course = cc.id where
                cs.section <> 0 and cmc.completionstate in (1,2) and cm.course = c.id and cmc.userid = u.id) as user_course_learn'),
                DB::raw('(select count(cm.id) as number_modules_of_course from mdl_course_modules cm inner join mdl_course_sections cs on
	                   cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = c.id and cm.completion <> 0) as total_module')
            )
            ->groupBy(['u.id', 'c.id'])
            ->get();
//        Log::info($lstUserCourse);
//        die;

        $arrData = [];
        $data_item = [];

        $num = 0;
        $limit = 500;

        foreach ($lstUserCourse as $course) {

            if ($course->total_module > 0 && $course->user_course_learn >= $course->total_module) {
                $data_item['userid'] = $course->user_id;
                $data_item['courseid'] = $course->course_id;
                $data_item['timecompleted'] = strtotime(Carbon::now());
                $data_item['timeenrolled'] = strtotime(Carbon::now());
                $data_item['created_at'] = Carbon::now();
                $data_item['updated_at'] = Carbon::now();

                array_push($arrData, $data_item);
                $num++;
            }

            if ($num >= $limit) {
                CourseCompletion::insert($arrData);
                $num = 0;
                $arrData = [];
            }

            usleep(100);
        }

        CourseCompletion::insert($arrData);

        usleep(100);

    }

    /* 20200909 Cuonghq hide join for gradepass and finalgrade */

    public function completeCourseForStudent()
    {
        $lstTrainning = TmsTrainningCourse::where('deleted', '=', 0)->select('trainning_id', 'course_id')->get();

        foreach ($lstTrainning as $data) {
            $lstUserCourse = DB::table('mdl_user_enrolments as mu')
                ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
                ->join('tms_traninning_users as ttu', 'ttu.user_id', '=', 'u.id')
                ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
                ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
                //->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id')
                //->join('mdl_grade_items as gri', 'gri.courseid', '=', 'c.id')
                ->leftJoin('course_completion as courc', function ($join) {
                    $join->on('courc.userid', '=', 'u.id');
                    $join->on('courc.courseid', '=', 'c.id');
                    $join->on('courc.training_id', '=', 'ttu.trainning_id');
                })
                ->where('ttu.trainning_id', '=', $data->trainning_id)
                ->where('c.id', '=', $data->course_id)
                ->whereNull('courc.id')
                ->select('u.id as user_id',
                    'c.id as course_id',
                    //'ccc.gradepass',
                    //'gri.itemmodule',
                    DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm inner join
                mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on
                cm.course = cs.course and cm.section = cs.id inner join mdl_course cc on cm.course = cc.id where
                cs.section <> 0 and cmc.completionstate in (1,2) and cm.course = c.id and cmc.userid = u.id) as user_course_learn'),
                    DB::raw('(select count(cm.id) as number_modules_of_course from mdl_course_modules cm inner join mdl_course_sections cs on
	                   cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = c.id and cm.completion <> 0) as total_module')
                //,DB::raw('(select `g`.`finalgrade` from mdl_grade_items as gi join mdl_grade_grades as g on g.itemid = gi.id
                //where gi.courseid = c.id and gi.itemtype = "course" and g.userid = u.id ) as finalgrade')
                )
                ->groupBy(['u.id'])
                ->get();

//            Log::info($lstUserCourse);
//            die;

            $arrData = [];
            $data_item = [];

            $num = 0;
            $limit = 300;

            foreach ($lstUserCourse as $course) {

                if ($course->total_module > 0 && $course->user_course_learn >= $course->total_module) {
//                    if (!empty($course->finalgrade) && $course->finalgrade >= $course->gradepass) {
                    $data_item['userid'] = $course->user_id;
                    $data_item['courseid'] = $course->course_id;
                    //$data_item['finalgrade'] = $course->finalgrade;
                    $data_item['timecompleted'] = strtotime(Carbon::now());
                    $data_item['timeenrolled'] = strtotime(Carbon::now());
                    $data_item['training_id'] = $data->trainning_id;
                    $data_item['created_at'] = Carbon::now();
                    $data_item['updated_at'] = Carbon::now();

                    array_push($arrData, $data_item);
                    $num++;

//                    } else {
//                        $data_item['userid'] = $course->user_id;
//                        $data_item['courseid'] = $course->course_id;
//                        $data_item['finalgrade'] = $course->finalgrade;
//                        $data_item['timecompleted'] = strtotime(Carbon::now());
//                        $data_item['timeenrolled'] = strtotime(Carbon::now());
//                        $data_item['training_id'] = $data->trainning_id;
//
//                        array_push($arrData, $data_item);
//                        $num++;
//
//                    }
                }

                if ($num >= $limit) {
                    CourseCompletion::insert($arrData);
                    $num = 0;
                    $arrData = [];
                }

                usleep(200);
            }

            CourseCompletion::insert($arrData);

            usleep(200);
        }
    }

    //region them user duoc enrol vao cac course ko nam trong KNL vao bang trainning_user
    // phat sinh do yeu cau cua KH muon khoa hoc don le cung duoc cap chung chi
    // he thong dang tao case moi khoa hoc don le duoc tao ra măc dinh sinh them 1 KNL nhung ko hien thi trong he thong


    public function addSingleUserToTrainningUser()
    {
        $lstTrainning = DB::table('tms_trainning_courses as ttc')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
            ->where('ttp.deleted', '=', 2)//cac khoa don le, ko hien thi
            ->where('ttc.deleted', '=', 0)
            ->select('ttc.trainning_id', 'ttc.course_id')->get();

//        Log::info(json_encode($lstTrainning));

        foreach ($lstTrainning as $data) {

            //query lay tat ca nguoi dung duoc ghi danh vao khoa hoc khong nam trong bang trainning_user
            $query_sql = '(select mu.id from mdl_user_enrolments as mue
                            join mdl_user as mu on mu.id = mue.userid
                            join tms_user_detail as tud on mu.id = tud.user_id
                            join mdl_enrol as me on me.id = mue.enrolid
                            join mdl_course as mc on mc.id = me.courseid
                            left join tms_traninning_users as ttu on ttu.user_id = mu.id and ttu.trainning_id = ' . $data->trainning_id . '
                            where mc.id = ' . $data->course_id . ' and tud.deleted = 0 and ttu.trainning_id is null)';

            $query_sql = DB::raw($query_sql);
            $lstUser = DB::select($query_sql);

            $queryArray = [];
            $userArrayByTraining = [];

            $num = 0;
            $limit = 300;

            foreach ($lstUser as $user) {
                $queryItem = [];
                $queryItem['trainning_id'] = $data->trainning_id;
                $queryItem['user_id'] = $user->id;
                $queryItem['created_at'] = Carbon::now();
                $queryItem['updated_at'] = Carbon::now();

                array_push($queryArray, $queryItem);
                $userArrayByTraining[$data->trainning_id][] = $user;

                $num++;
                if ($num >= $limit) {
                    TmsTrainningUser::insert($queryArray);
//                    foreach ($userArrayByTraining as $training => $users) {
//                        $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
//                    }
                    $num = 0;
                    $queryArray = [];
                    $userArrayByTraining = [];
                }
            }

            TmsTrainningUser::insert($queryArray);
//            foreach ($userArrayByTraining as $training => $users) {
//                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
//            }

            $queryArray = [];

            usleep(200);
        }
    }
    //endregion

    //danh sach hoc vien da hoan thanh KNL
    public function userCompleteTrainning()
    {
        //Lấy ra số khóa học theo khung năng lực deleted = 0
        $lstTrainning = DB::table('tms_trainning_courses as ttc')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
            ->whereRaw('(ttp.deleted = 0 or ttp.deleted = 2)')//khung nang luc thong thuong, chua bi xoa
            ->where('ttc.deleted', '=', '0')//Khoa hoc trong khung nang luc
            ->select('ttc.trainning_id', DB::raw('GROUP_CONCAT(`ttc`.`course_id`) as `training_courses`'), 'ttp.deleted')
            ->groupBy(['ttc.trainning_id'])
            ->get();

        foreach ($lstTrainning as $training) {

            $training_courses = $training->training_courses;
            $training_courses_arr = array();
            if (strlen($training_courses) != 0) {
                $training_courses_arr = explode(',', $training_courses);
            }

            if (!empty($training_courses_arr)) {

                //lấy ra danh sách user và số khóa học đã hoàn thành theo khung năng lực
                $lstData = DB::table('course_completion as cc')
                    ->leftJoin('tms_trainning_complete as ttc', function ($join) {
                        $join->on('ttc.user_id', '=', 'cc.userid');
                        $join->on('ttc.trainning_id', '=', 'cc.training_id');
                    })
                    ->whereNull('ttc.id')//record không tồn tại trong bảng ttc, user chưa hoàn thành knl trước đó
                    ->select('cc.training_id', 'cc.userid', DB::raw('GROUP_CONCAT(`cc`.`courseid`) as `completed_courses`'))
                    ->where('cc.training_id', '=', $training->trainning_id)
                    ->groupBy(['cc.training_id', 'cc.userid'])
                    ->get();

                $arrData = [];
                $num = 0;
                $limit = 200;

                foreach ($lstData as $course) {

                    $completed_courses = $course->completed_courses;
                    $completed_courses_arr = array();
                    if (strlen($completed_courses) != 0) {
                        $completed_courses_arr = explode(',', $completed_courses);
                    }

                    if (empty(array_diff($training_courses_arr, $completed_courses_arr))) {//Số hóa học đã hoàn thành trùng số khóa học trong khung => Đã hoàn thành KNL
                        $data_item = [];
                        $data_item['trainning_id'] = $training->trainning_id;
                        $data_item['user_id'] = $course->userid;
                        $data_item['created_at'] = Carbon::now();
                        $data_item['updated_at'] = Carbon::now();

                        array_push($arrData, $data_item);
                        $num++;
                    }

                    if ($num >= $limit) {
                        TmsTrainningComplete::insert($arrData);
                        if ($training->deleted == 0) {
                            $this->insertCompetencyCompleted($arrData);
                        }
                        $num = 0;
                        $arrData = [];
                    }

                    usleep(100);
                }

                TmsTrainningComplete::insert($arrData);
                if ($training->deleted == 0) {
                    $this->insertCompetencyCompleted($arrData);
                }
                sleep(1);
            }
        }
        sleep(1);

        //Với những khóa học đơn lẻ : deleted = 2
//        $lstTrainning = DB::table('tms_trainning_courses as ttc')
//            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
//            ->where('ttp.deleted', '=', '2') //những khóa lẻ
//            ->where('ttc.deleted', '=', '0') //Khoa hoc trong khung nang luc
//            ->select('ttc.trainning_id', DB::raw('GROUP_CONCAT(`ttc`.`course_id`) as `training_courses`'))
//            ->groupBy(['ttc.trainning_id'])
//            ->get();


//        foreach ($lstTrainning as $training) {
//
//            $training_courses = $training->training_courses;
//            $training_courses_arr = array();
//            if (strlen($training_courses) != 0) {
//                $training_courses_arr = explode(',', $training_courses);
//            }
//            if (!empty($training_courses_arr) && $training->trainning_id == 118) {
//
//                //lấy ra danh sách user và số khóa học đã hoàn thành theo khung năng lực
//                $lstData = DB::table('course_completion as cc')
//                    ->leftJoin('tms_trainning_complete as ttc', function ($join) {
//                        $join->on('ttc.user_id', '=', 'cc.userid');
//                        $join->on('ttc.trainning_id', '=', 'cc.training_id');
//                    })
//                    ->whereNull('ttc.id') //record không tồn tại trong bảng ttc, user chưa hoàn thành knl trước đó
//                    ->select('cc.training_id', 'cc.userid', DB::raw('GROUP_CONCAT(`cc`.`courseid`) as `completed_courses`'))
//                    ->where('cc.training_id', '=', $training->trainning_id)
//                    ->groupBy(['cc.training_id', 'cc.userid'])
//                    ->get();
//
//                $arrData = [];
//                $num = 0;
//                $limit = 200;
//
//                foreach ($lstData as $course) {
//
//                    $completed_courses = $course->completed_courses;
//                    $completed_courses_arr = array();
//                    if (strlen($completed_courses) != 0) {
//                        $completed_courses_arr = explode(',', $completed_courses);
//                    }
//
//                    if (empty(array_diff($training_courses_arr, $completed_courses_arr))) {//Số hóa học đã hoàn thành trùng số khóa học trong khung => Đã hoàn thành KNL
//                        $data_item = [];
//                        $data_item['trainning_id'] = $training->trainning_id;
//                        $data_item['user_id'] = $course->userid;
//                        $data_item['created_at'] = Carbon::now();
//                        $data_item['updated_at'] = Carbon::now();
//
//                        array_push($arrData, $data_item);
//                        $num++;
//                    }
//
//                    if ($num >= $limit) {
//                        TmsTrainningComplete::insert($arrData);
//                        $this->insertCompetencyCompleted($arrData);
//                        $num = 0;
//                        $arrData = [];
//                    }
//
//                    usleep(100);
//                }
//
//                TmsTrainningComplete::insert($arrData);
//                $this->insertCompetencyCompleted($arrData);
//                sleep(1);
//            }
//        }
//        sleep(1);

        //tu dong gen ma chung chi cho hoc vien
        $listStudentsDone = DB::table('tms_trainning_complete as ttc')
            ->join('mdl_user as mu', 'mu.id', '=', 'ttc.user_id')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'mu.id')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
            ->leftJoin('student_certificate as sc', function ($join) {
                $join->on('sc.userid', '=', 'mu.id');
                $join->on('sc.trainning_id', '=', 'ttc.trainning_id');
            })
            ->select('tud.user_id', 'ttp.id as training_id')
            ->whereNull('sc.id')
            ->whereRaw('(ttp.auto_certificate = 1 OR ttp.auto_badge = 1)')
            ->groupBy(['ttc.user_id', 'ttc.trainning_id'])->get();

        $arrDataST = [];
        $data_item = [];

        $num = 0;
        $limit = 200;

        foreach ($listStudentsDone as $st) {

            $certificatecode = $st->user_id . $this->randomNumber(7 - strlen($st->user_id));

            $data_item['trainning_id'] = $st->training_id;
            $data_item['userid'] = $st->user_id;
            $data_item['code'] = $certificatecode;
            $data_item['status'] = 1;
            $data_item['timecertificate'] = time();
            $data_item['auto_run'] = 1;
            $data_item['created_at'] = Carbon::now();
            $data_item['updated_at'] = Carbon::now();

            array_push($arrDataST, $data_item);
            $num++;

            if ($num >= $limit) {
                StudentCertificate::insert($arrDataST);
                $num = 0;
                $arrDataST = [];
            }

            usleep(100); //sleep tranh tinh trang query db lien tiep

            //insert du lieu lich su hoc tap
            $user_id = $st->user_id;
            $training_id = $st->training_id;

            $lstHistory = DB::table('course_completion as cc')
                ->join('mdl_course as c', 'c.id', '=', 'cc.courseid')
                ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'cc.training_id')
                ->where('cc.userid', '=', $user_id)
                ->where('cc.training_id', '=', $training_id)
                ->select('c.id as course_id', 'c.shortname as course_code', 'c.fullname as course_name', 'ttp.name as trainning_name')
                ->whereNotExists(function ($query) use ($training_id, $user_id) { //Check chưa tồn tại tr
                    $query->select(DB::raw(1))
                        ->from('tms_learner_histories as tlh')
                        ->where('tlh.trainning_id', '=', $training_id)
                        ->where('tlh.user_id', '=', $user_id)
                        ->where('tlh.course_id', '=', 'c.id');
                })
                ->get();

            $arr_data_his = [];
            $data_item_his = [];

            foreach ($lstHistory as $his) {
                $data_item_his['trainning_id'] = $st->training_id;
                $data_item_his['trainning_name'] = $his->trainning_name;
                $data_item_his['user_id'] = $st->user_id;
                $data_item_his['course_id'] = $his->course_id;
                $data_item_his['course_code'] = $his->course_code;
                $data_item_his['course_name'] = $his->course_name;
                $data_item_his['created_at'] = Carbon::now();
                $data_item_his['updated_at'] = Carbon::now();

                array_push($arr_data_his, $data_item_his);
            }
            TmsLearnerHistory::insert($arr_data_his);

            usleep(100); //sleep tranh tinh trang query db lien tiep

        }
        StudentCertificate::insert($arrDataST);

        usleep(100); //sleep tranh tinh trang query db lien tiep
//        TmsLearnerHistory::insert($arr_data_his);

    }
    #endregion

    //insert training into tms_nofitications table
    public function insertCompetencyCompleted($arrayData)
    {
        $data = array();
        foreach ($arrayData as $user_item) {
            $element = array(
                'type' => TmsNotification::MAIL,
                'target' => TmsNotification::COMPLETED_FRAME,
                'status_send' => 0,
                'sendto' => $user_item['user_id'],
                'createdby' => 0,
                'course_id' => 0,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'content' => $user_item['trainning_id']
            );
            $data[] = $element;
        }
        if (!empty($data)) {
            //batch insert
            TmsNotification::insert($data);
        }
    }

    #region insert student to course_final from courses certificate
    public function finalizeCourseForRole()
    {
        $lstUserCourse = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('tms_traninning_users as ttu', 'ttu.user_id', '=', 'u.id')
            ->join('tms_trainning_courses as ttc', 'ttc.trainning_id', '=', 'ttu.trainning_id')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', function ($join) {
                $join->on('ttc.course_id', '=', 'c.id');
                $join->on('e.courseid', '=', 'c.id');
            })
            /*->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')*/
            ->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id')
            ->join('mdl_grade_items as gri', 'gri.courseid', '=', 'c.id')
            ->leftJoin('course_final as courc', function ($join) {
                $join->on('courc.userid', '=', 'u.id');
                $join->on('courc.courseid', '=', 'c.id');
            })
            //->where('c.category', '=', 3)
            ->where('c.is_end_quiz', '=', 1)//điều kiện xác nhận là bài thi cuối khóa
            ->whereNull('courc.userid')
            ->whereNull('courc.courseid')
            ->select('u.id as user_id', 'c.id as course_id', 'ccc.gradepass', 'gri.itemmodule', 'courc.courseid as courcID', 'courc.userid as courscUID'
                , DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm inner join
                mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on
                cm.course = cs.course and cm.section = cs.id inner join mdl_course cc on cm.course = cc.id where
                cs.section <> 0 and cmc.completionstate in (1,2) and cm.course = c.id and cmc.userid = u.id) as user_course_learn'),
                DB::raw('(select count(cm.id) as number_modules_of_course from mdl_course_modules cm inner join mdl_course_sections cs on
	                   cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = c.id and cm.completion <> 0) as total_module'),
                DB::raw('(select `g`.`finalgrade` from mdl_grade_items as gi join mdl_grade_grades as g on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = u.id ) as finalgrade'))->groupBy('u.id')->get();

        $count_course = count($lstUserCourse);

        if ($count_course > 0) {
            $arrData = [];
            $data_item = [];

            $num = 0;
            $limit = 300;

            foreach ($lstUserCourse as $course) {
                if ($course->total_module > 0 && $course->user_course_learn >= $course->total_module
                    && !empty($course->finalgrade) && $course->finalgrade >= $course->gradepass) {

                    $data_item['userid'] = $course->user_id;
                    $data_item['courseid'] = $course->course_id;
                    $data_item['finalgrade'] = $course->finalgrade;
                    $data_item['timecompleted'] = strtotime(Carbon::now());

                    array_push($arrData, $data_item);

                    $num++;
                    if ($num >= $limit) {
                        CourseFinal::insert($arrData);
                        $num = 0;
                        $arrData = [];
                    }

                }
            }
            CourseFinal::insert($arrData);
        }
    }
    #endregion

    #region student to course_completion courses offline
    public function completeOfflineCourseForStudent()
    {
        $lstUserCourse = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id')
            ->join('mdl_grade_items as gri', 'gri.courseid', '=', 'c.id')
            ->leftJoin('course_completion as courc', function ($join) {
                $join->on('courc.userid', '=', 'u.id');
                $join->on('courc.courseid', '=', 'c.id');
            })
            ->where('c.category', '=', 5)///điều kiện xác nhận là khóa học offline
            ->whereNull('courc.userid')
            ->whereNull('courc.courseid')
            ->select('u.id as user_id', 'c.id as course_id', 'ccc.gradepass', 'gri.itemmodule', 'courc.courseid as courcID', 'courc.userid as courscUID', 'c.total_date_course'
                , DB::raw('(SELECT count(att.id) FROM `mdl_attendance` att
                                    join mdl_course mc on mc.id = att.courseid
                                    join mdl_user mu on mu.id = att.userid where att.courseid = c.id and att.userid = u.id) as count_date'),
                DB::raw('(select `g`.`finalgrade` from mdl_grade_items as gi join mdl_grade_grades as g on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = u.id ) as finalgrade'))->groupBy('u.id')->get();

        $count_course = count($lstUserCourse);

        if ($count_course > 0) {
            $arrData = [];
            $data_item = [];

            $num = 0;
            $limit = 300;

            foreach ($lstUserCourse as $course) {

                if ($course->total_date_course > 0 && $course->count_date >= $course->total_date_course
                    && !empty($course->finalgrade) && $course->finalgrade >= $course->gradepass) {

                    $data_item['userid'] = $course->user_id;
                    $data_item['courseid'] = $course->course_id;
                    $data_item['finalgrade'] = $course->finalgrade;
                    $data_item['timecompleted'] = strtotime(Carbon::now());
                    $data_item['timeenrolled'] = strtotime(Carbon::now());
                    array_push($arrData, $data_item);

                    $num++;
                    if ($num >= $limit) {
                        CourseCompletion::insert($arrData);
                        $num = 0;
                        $arrData = [];
                    }

                }
            }
            CourseCompletion::insert($arrData);
        }
    }
    #endregion

    #region lock student account after 30 days no certificate
    public function lockStudentNoCertificate()
    {
        $now = date('Y-m-d', strtotime(Carbon::now()));

        #region cmt code cu, query qua nhieu
//        $role = Role::where('name', 'student')->pluck('id');
//        $user_array = ModelHasRole::whereIn('role_id', $role)->pluck('model_id');
//        $user_detail = TmsUserDetail::where([
//            'deleted' => 0,
//            'confirm' => 0
//        ])
//            ->where('email', 'not like', '%bgt%')//ko xoa cac account la nhan vien BGT, phuong an xu ly tam thoi voi so luong user BGT hien co trong he thong
//            ->whereIn('user_id', $user_array)
//            ->get();
//
//        if (!empty($user_detail)) {
//            $user_detail = $user_detail->toArray();
//            foreach ($user_detail as $user) {
//                $created_at = date('Y-m-d', strtotime($user['created_at']));
//                if ((strtotime($now) - strtotime($created_at)) > 30 * 60 * 60 * 24 && !$this->checkCourseComplete($user['user_id'])) {
//                    \DB::beginTransaction();
//                    $user_id = $user['user_id'];
//                    $mdlUser = MdlUser::findOrFail($user_id);
//                    if ($mdlUser) {
//                        $mdlUser->deleted = 1;
//                        $mdlUser->save();
//
//                        $tmsUser = TmsUserDetail::where('user_id', $user_id)->first();
//                        $tmsUser->deleted = 1;
//                        $tmsUser->save();
//
//                        $type = 'user';
//                        $url = URL::current();
//                        $action = 'delete';
//                        $info = 'Khóa Tài khoản học viên: ' . $mdlUser['username'] . ' sau 30 ngày chưa được cấp giấy chứng nhập.';
//                        TmsLog::create([
//                            'type' => $type,
//                            'url' => $url,
//                            'user' => $user_id,
//                            'ip' => '',
//                            'action' => $action,
//                            'info' => $info,
//                        ]);
//                    }
//                    \DB::commit();
//                }
//            }
//        }

        #endregion

        //ThoLD = 31/12/2019
        //lay danh sach cac hoc vien dang nam trong khung nang luc chung chi

        $student_role = Role::ROLE_STUDENT;

        $lstUsers = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('tms_user_detail as tud', 'tud.user_id', '=', 'u.id')
            ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id')
            ->join('tms_traninning_users as ttu', 'ttu.user_id', '=', 'u.id')
            //comment get all roles not only student
            //->where('r.name', '=', \App\Role::STUDENT)

            //Lọc bỏ các user có thêm role khác ngoài role student
            ->whereNotExists(function ($query) use ($student_role) {
                $query->select(DB::raw(1))
                    ->from('model_has_roles')
                    ->whereRaw('model_has_roles.model_id = u.id')
                    ->where('model_has_roles.role_id', "<>", $student_role);
            })
            ->where('tud.deleted', '=', 0)
            ->where('tud.confirm', '=', 0)
            ->where('ttu.trainning_id', '=', \App\TmsTrainningProgram::PROGRAM_CERTIFICATE)//id khung nang luc bat buoc
            ->where('tud.email', 'not like', '%bgt%')//ko xoa cac account la nhan vien BGT, phuong an xu ly tam thoi voi so luong user BGT hien co trong he thong
            ->select(
                'u.id as user_id'
                , 'tud.created_at'
                , 'ccc.gradepass as gradepass'
                , DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm
                inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cmc.completionstate in (1,2) and cmc.userid = u.id and cm.course = c.id)
                as user_course_completionstate')

                , DB::raw('(select count(cm.id) as course_learn from mdl_course_modules cm
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cm.course = c.id and cm.completion <> 0) as user_course_learn')

                , DB::raw('IF( EXISTS(select cc.id from mdl_course_completions as cc
                                 where cc.userid = u.id and cc.course = c.id and cc.timecompleted is not null ), "1", "0") as status_user')

                , DB::raw('(select `g`.`finalgrade`
  				from mdl_grade_items as gi
				join mdl_grade_grades as g
				on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = u.id ) as finalgrade'))->get();

        if ($lstUsers) {

            $arr_userId = [];
            $arr_log = [];
            $data_log = [];

            $num = 0;
            $limit = 300;

            \DB::beginTransaction();
            foreach ($lstUsers as $user) {
                $created_at = date('Y-m-d', strtotime($lstUsers->created_at));

                if ((
                        strtotime($now) - strtotime($created_at)) > 30 * 60 * 60 * 24  //>30 ngay
                    && $user->status_user == 0  //chua duoc cap giay cn
                    && $user->finalgrade < $user->gradepass //thi truot
                    && $user->user_course_completionstate < $user->user_course_learn //chua hoan thanh cac khoa hoc theo lo trinh
                ) {

                    $user_id = $user->user_id;
                    $mdlUser = MdlUser::findOrFail($user_id);
                    if ($mdlUser) {

                        array_push($arr_userId, $user->user_id);

                        $data_log['type'] = 'user';
                        $data_log['url'] = URL::current();
                        $data_log['user'] = $user->user_id;
                        $data_log['ip'] = '';
                        $data_log['action'] = 'delete';
                        $data_log['info'] = 'Khóa Tài khoản học viên: ' . $mdlUser['username'] . ' sau 30 ngày chưa được cấp giấy chứng nhận.';

                        array_push($arr_log, $data_log);
                        $num++;

                        if ($num >= $limit) { //lấy đủ 300 update, sau đó lại lấy lại
                            MdlUser::whereIn('id', $arr_userId)->update(['deleted' => 1]);

                            TmsUserDetail::whereIn('user_id', $arr_userId)->update(['deleted' => 1]);

                            TmsLog::insert($arr_log);

                            //khởi tạo lại
                            $num = 0;
                            $arr_userId = [];
                            $arr_log = [];
                        }

                    }

                    usleep(200);
                }
            }

            MdlUser::whereIn('id', $arr_userId)->update(['deleted' => 1]);

            TmsUserDetail::whereIn('user_id', $arr_userId)->update(['deleted' => 1]);

            TmsLog::insert($arr_log);

            \DB::commit();
        }
    }
    #endregion

    #region xóa tài khoản trong thùng rác sau 60 ngày
    public function deleteAccountAfter()
    {
        $now = date('Y-m-d', strtotime(Carbon::now()));
        $user_detail = TmsUserDetail::select('user_id', 'updated_at')->where([
            'deleted' => 1,
        ])->get();
        if (!empty($user_detail)) {
            $user_detail = $user_detail->toArray();

            $arr_userId = [];

            $arr_log = [];
            $data_log = [];

            $num = 0;
            $limit = 300;

            \DB::beginTransaction();
            foreach ($user_detail as $user) {
                $updated_at = date('Y-m-d', strtotime($user['updated_at']));
                if ((strtotime($now) - strtotime($updated_at)) > 30 * 60 * 60 * 24 * 2) {

                    $user_id = $user['user_id'];

                    array_push($arr_userId, $user_id);

                    $mdlUser = MdlUser::findOrFail($user_id);

//                    //Function clear user khỏi DB
//                    TmsUserDetail::clearUser($user_id);

                    $data_log['type'] = 'user';
                    $data_log['url'] = '*';
                    $data_log['user'] = $user_id;
                    $data_log['ip'] = '';
                    $data_log['action'] = 'clear';
                    $data_log['info'] = 'Xóa vĩnh viễn tài khoản: ' . $mdlUser['username'] . ' sau 60 ngày trong thùng rác.';

                    array_push($arr_log, $data_log);
                    $num++;

                    if ($num > $limit) {
                        MdlUser::whereIn('id', $arr_userId)->delete();
                        TmsUserDetail::whereIn('user_id', $arr_userId)->delete();
                        //Xóa khỏi bảng ModelHasRole
                        ModelHasRole::whereIn('model_id', $arr_userId)->delete();
                        //Xóa khỏi bảng TmsSaleRoomUser
                        TmsSaleRoomUser::whereIn('user_id', $arr_userId)->delete();
                        //Xóa khỏi bảng TmsSaleRoomUser
                        TmsSaleRoomUser::whereIn('user_id', $arr_userId)->delete();

                        TmsLog::insert($arr_log);
                        $num = 0;
                        $arr_userId = [];
                        $arr_log = [];
                    }


                }
            }

            MdlUser::whereIn('id', $arr_userId)->delete();
            TmsUserDetail::whereIn('user_id', $arr_userId)->delete();
            //Xóa khỏi bảng ModelHasRole
            ModelHasRole::whereIn('model_id', $arr_userId)->delete();
            //Xóa khỏi bảng TmsSaleRoomUser
            TmsSaleRoomUser::whereIn('user_id', $arr_userId)->delete();
            //Xóa khỏi bảng TmsSaleRoomUser
            TmsSaleRoomUser::whereIn('user_id', $arr_userId)->delete();

            TmsLog::insert($arr_log);
            \DB::commit();
        }
    }

    #endregion

    //cron enrol user to course in organization
    public function enrolUserOrganization()
    {
        //lay danh sach khoa hoc duoc gan cho cctc
        $lstData = DB::table('tms_role_course as trc')
            ->join('mdl_course as c', 'c.id', '=', 'trc.course_id')
            ->join('tms_role_organization as tro', 'tro.role_id', '=', 'trc.role_id')
            ->join('tms_organization as tor', 'tor.id', '=', 'tro.organization_id')
            ->where('c.visible', '=', 1)//khoa hoc duoc enable moi enrol hoc vien
            ->where('c.deleted', '=', 0)
            ->where('c.category', '!=', 2)
            ->select('tor.id as org_id', 'trc.course_id')->groupBy(['tor.id', 'trc.course_id'])
            ->get();

        if ($lstData) {
            foreach ($lstData as $data) {
                $leftJoin = '(SELECT mue.userid, mue.enrolid FROM mdl_user_enrolments mue
                            join mdl_enrol me on me.id = mue.enrolid join mdl_course mc on mc.id = me.courseid
                            where mc.id = ' . $data->course_id . ') as ue';

                $leftJoin = DB::raw($leftJoin);

                //lay danh sach hoc vien nam trong cctc chua dc enroll vao khoa hoc
                $users = DB::table('tms_organization_employee as toe')
                    ->leftJoin($leftJoin, 'ue.userid', '=', 'toe.user_id')
                    ->where('toe.organization_id', '=', $data->org_id)
                    ->whereNull('ue.enrolid')->groupBy('toe.user_id')->pluck('toe.user_id')->toArray();

                if (count($users) > 0) {
                    // enroll user to course in competency framework
                    // do moodle chi hieu user duoc hoc khi duoc enroll voi quyen student or teacher
                    // he thong dang set mac dinh user tao ra deu co quyen student
                    $this->cron_enroll_user_to_course_multiple($users, Role::ROLE_STUDENT, $data->course_id, false);
                }

                usleep(100);
            }
        }

        usleep(100);
    }

    //cron enroll user to competency framework
    public function autoEnrolTrainning()
    {
        $result = $this->updateFlagCron(Config::get('constants.domain.ENROLL_USER'), Config::get('constants.domain.ACTION_READ_FLAG'), '');
        $result = json_decode($result, true);

        if ($result['flag'] == 'stop')
            return;


        //lay danh sach khoa hoc theo tung khung nang luc
        $lstData = DB::table('tms_trainning_courses as ttc')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
            ->join('mdl_course as c', 'c.id', '=', 'ttc.course_id')
            ->where('c.visible', '=', 1)//khoa hoc duoc enable moi enrol hoc vien
            ->where('ttp.run_cron', '=', 1)
            ->where('ttc.deleted', '=', 0)
            ->where('ttp.deleted', '=', 0)
            ->select('ttc.trainning_id', 'ttc.course_id')->get();

        if ($lstData) {
            foreach ($lstData as $data) {
                //raw query lay so hoc vien da enrol vao course
                $leftJoin = '(SELECT mue.userid, mue.enrolid FROM mdl_user_enrolments mue
                            join mdl_enrol me on me.id = mue.enrolid join mdl_course mc on mc.id = me.courseid
                            where mc.id = ' . $data->course_id . ') as ue';

                $leftJoin = DB::raw($leftJoin);

                //lay danh sach hoc vien nam trong KNL chua dc enroll vao khoa hoc
                $users = DB::table('tms_traninning_users as ttu')
                    ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttu.trainning_id')
                    ->leftJoin($leftJoin, 'ue.userid', '=', 'ttu.user_id')
                    ->where('ttp.id', '=', $data->trainning_id)
                    ->whereNull('ue.enrolid')->groupBy('ttu.user_id')->pluck('ttu.user_id')->toArray();

                if (count($users) > 0) {
                    // enroll user to course in competency framework
                    // do moodle chi hieu user duoc hoc khi duoc enroll voi quyen student or teacher
                    // he thong dang set mac dinh user tao ra deu co quyen student
                    $this->cron_enroll_user_to_course_multiple($users, Role::ROLE_STUDENT, $data->course_id, false);
                }

                usleep(100);
            }
        }

        usleep(100);
        //cap nhat trang thai cho cron
        $this->updateFlagCron(Config::get('constants.domain.ENROLL_USER'), Config::get('constants.domain.ACTION_UPDATE_FLAG'),
            Config::get('constants.domain.STOP_CRON'));
    }

    //cron enroll user to competency framework, run cron by time
    public function autoEnrolTrainningCron()
    {
//        $result = $this->updateFlagCron(Config::get('constants.domain.ENROLL_USER'), Config::get('constants.domain.ACTION_READ_FLAG'), '');
//        $result = json_decode($result, true);
//
//        if ($result['flag'] == 'stop')
//            return;


        //lay danh sach khoa hoc theo tung khung nang luc
        $lstData = DB::table('tms_trainning_courses as ttc')
            ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
            ->join('mdl_course as c', 'c.id', '=', 'ttc.course_id')
            ->where('c.visible', '=', 1)//khoa hoc duoc enable moi enrol hoc vien
            ->where('ttp.run_cron', '=', 1)
            ->where('ttc.deleted', '=', 0)
            ->where('ttp.deleted', '=', 0)
            ->select('ttc.trainning_id', 'ttc.course_id')->get();

        if ($lstData) {
            foreach ($lstData as $data) {
                //raw query lay so hoc vien da enrol vao course
                $leftJoin = '(SELECT mue.userid, mue.enrolid FROM mdl_user_enrolments mue
                            join mdl_enrol me on me.id = mue.enrolid join mdl_course mc on mc.id = me.courseid
                            where mc.id = ' . $data->course_id . ') as ue';

                $leftJoin = DB::raw($leftJoin);

                //lay danh sach hoc vien nam trong KNL chua dc enroll vao khoa hoc
                $users = DB::table('tms_traninning_users as ttu')
                    ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttu.trainning_id')
                    ->leftJoin($leftJoin, 'ue.userid', '=', 'ttu.user_id')
                    ->where('ttp.id', '=', $data->trainning_id)
                    ->whereNull('ue.enrolid')->groupBy('ttu.user_id')->pluck('ttu.user_id')->toArray();

                if (count($users) > 0) {
                    // enroll user to course in competency framework
                    // do moodle chi hieu user duoc hoc khi duoc enroll voi quyen student or teacher
                    // he thong dang set mac dinh user tao ra deu co quyen student
                    $this->cron_enroll_user_to_course_multiple($users, Role::ROLE_STUDENT, $data->course_id, false);
                }

                usleep(100);
            }
        }

        usleep(100);
        //cap nhat trang thai cho cron
//        $this->updateFlagCron(Config::get('constants.domain.ENROLL_USER'), Config::get('constants.domain.ACTION_UPDATE_FLAG'),
//            Config::get('constants.domain.STOP_CRON'));
    }

    // ghi text vao file, phuc vu cho chay cron, bao cho cron biet khi nao start
    function updateFlagCron($filename, $action, $data = null)
    {
        $result = '';
        $exists = Storage::disk('public')->exists('cron/' . $filename);
        if ($exists) {
            $file_path = Storage::path('public/cron/' . $filename);
            switch ($action) {
                case Config::get('constants.domain.ACTION_READ_FLAG'):
                    $result = file_get_contents($file_path);
                    break;
                case Config::get('constants.domain.ACTION_UPDATE_FLAG'):
                    $content = '{"flag":"' . $data . '"}';
                    Storage::put('public/cron/' . $filename, $content);
                    usleep(100);
                    $result = file_get_contents($file_path);
                    break;
            }
        }
        usleep(100);
        return $result;
    }

    // funtion create notification with content

    /**
     *
     * @param $target
     * @param $receiver
     * @param $content
     * @param bool $encoded
     */
    function insert_mail_notifications($target, $receiver, $content, $encoded = false)
    {
        $send_to = [];
        if (!empty($receiver)) {
            if (is_array($receiver)) {
                $send_to = $receiver;
            } elseif (is_int($receiver)) {
                $send_to[] = $receiver;
            }

            if (!empty($send_to)) {
                foreach ($send_to as $user_id) {
                    $element = array(
                        'type' => TmsNotification::MAIL,
                        'target' => $target,
                        'status_send' => 0,
                        'sendto' => $user_id,
                        'createdby' => 0,
                        'course_id' => 0,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                        'content' => $encoded ? json_encode($content, JSON_UNESCAPED_UNICODE) : $content
                    );
                    $data[] = $element;
                }
                if (!empty($data)) {
                    TmsNotification::insert($data);
                }
            }
        }
    }

    //enrol user to course improve
    //ghi danh học viên vào khóa học
    //phuc vu chuc nang cron chay ghi danh hoc vien vao khoa hoc trong KNL
    function cron_enroll_user_to_course_multiple($user_ids, $role_id, $course_id, $notify = false)
    {
        $count_user = count($user_ids);
        if ($count_user > 0) {

            $context = DB::table('mdl_context')
                ->where('instanceid', '=', $course_id)
                ->where('contextlevel', '=', MdlUser::CONTEXT_COURSE)
                ->first();
            $context_id = $context ? $context->id : 0;


            //Check enrol
            $check_enrol = MdlEnrol::where([
                'mdl_enrol.enrol' => 'manual',
                'mdl_enrol.courseid' => $course_id,
                'mdl_enrol.roleid' => $role_id
            ])
                ->leftJoin('mdl_user_enrolments', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                ->select('mdl_enrol.id', 'mdl_user_enrolments.userid')
                ->get()
                ->toArray();
            //Insert missing


            if (empty($check_enrol)) {
                $new_enrol = MdlEnrol::create(
                    [
                        'enrol' => 'manual',
                        'courseid' => $course_id,
                        'roleid' => $role_id,
                        'sortorder' => 0,
                        'status' => 0,
                        'expirythreshold' => 86400,
                        'timecreated' => strtotime(Carbon::now()),
                        'timemodified' => strtotime(Carbon::now())
                    ]
                );
                $enrol_id = $new_enrol->id;
                $need_to_insert_users = $user_ids;
            } else {
                $existed_enrol = array();
                $enrol_id = 0;
                foreach ($check_enrol as $existed) {
                    $enrol_id = $existed['id'];
                    if (!empty($existed['userid'])) {
                        $existed_enrol[] = $existed['userid'];
                    }
                }
                $need_to_insert_users = array_diff($user_ids, $existed_enrol);
            }

            $insert_enrolment_data = array();
            foreach ($need_to_insert_users as $user_id) {
                $insert_enrolment_data[] = [
                    'enrolid' => $enrol_id,
                    'userid' => $user_id,
                    'timestart' => strtotime(Carbon::now()),
                    'modifierid' => 2, //mac dinh user tao la admin khi chay cron
                    'timecreated' => strtotime(Carbon::now()),
                    'timemodified' => strtotime(Carbon::now())
                ];
                if ($notify && $role_id == Role::ROLE_STUDENT) {
                    self::insert_single_notification(\App\TmsNotification::MAIL, $user_id, \App\TmsNotification::ENROL, $course_id);
                }
            }
            if (!empty($insert_enrolment_data)) {
                MdlUserEnrolments::insert($insert_enrolment_data);
            }

            //Check role assignment
            $check_assignment = MdlRoleAssignments::where([
                'roleid' => $role_id,
                'contextid' => $context_id
            ])
                ->whereIn('userid', $user_ids)
                ->select('userid')
                ->get()
                ->toArray();

            //Insert missing
            if (empty($check_assignment)) {
                $need_to_insert_assigment_users = $user_ids;
            } else {
                $existed_ass = array();
                foreach ($check_assignment as $existed) {
                    $existed_ass[] = $existed['userid'];
                }
                $need_to_insert_assigment_users = array_diff($user_ids, $existed_ass);
            }

            $insert_assignment_data = array();
            foreach ($need_to_insert_assigment_users as $user_id) {
                $insert_assignment_data[] = [
                    'roleid' => $role_id,
                    'userid' => $user_id,
                    'contextid' => $context_id
                ];
            }
            if (!empty($insert_assignment_data)) {
                MdlRoleAssignments::insert($insert_assignment_data);
            }

            //lay gia trị trong bang mdl_grade_items
            $mdl_grade_item = MdlGradeItem::where('courseid', $course_id)->first();

            if ($mdl_grade_item) {
                //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                $check_grade = MdlGradeGrade::where('itemid', $mdl_grade_item->id)
                    ->whereIn('userid', $user_ids)
                    ->select('userid')
                    ->get()
                    ->toArray();

                //Insert missing
                if (empty($check_grade)) {
                    $need_to_insert_grade_users = $user_ids;
                } else {
                    $existed_grd = array();
                    foreach ($check_grade as $existed) {
                        $existed_grd[] = $existed['userid'];
                    }
                    $need_to_insert_grade_users = array_diff($user_ids, $existed_grd);
                }

                $insert_grd_data = array();
                foreach ($need_to_insert_grade_users as $user_id) {
                    $insert_grd_data[] = [
                        'userid' => $user_id,
                        'itemid' => $mdl_grade_item->id
                    ];
                }
                if (!empty($insert_grd_data)) {
                    MdlGradeGrade::insert($insert_grd_data);
                }
            }
        }
    }

    function insert_single_notification($type, $sendto, $target, $course_id)
    {
        $tms_notif = \App\TmsNotification::firstOrCreate([
            'type' => $type,
            'sendto' => $sendto,
            'target' => $target,
            'status_send' => isset(\Illuminate\Support\Facades\Auth::user()->id) ? \Illuminate\Support\Facades\Auth::user()->id : 0,
            'course_id' => $course_id
        ]);
//        $tms_notif = new \App\TmsNotification();
//        $tms_notif->type = $type;
//        $tms_notif->sendto = $sendto;
//        $tms_notif->target = $target;
//        $tms_notif->status_send = \App\TmsNotification::UN_SENT;
//        $tms_notif->course_id = $course_id;
//        if (!empty(\Illuminate\Support\Facades\Auth::user())) {
//            $tms_notif->createdby = \Illuminate\Support\Facades\Auth::user()->id;
//        }
//
//        $tms_notif->save();
        usleep(100);
        $this->insert_single_notification_log($tms_notif, \App\TmsNotificationLog::CREATE_NOTIF);
    }

    function insert_single_notification_log($tmsNotif, $action)  //action bao gồm create, update, delete lấy trong bảng TmsNotificationLog
    {
        \App\TmsNotificationLog::firstOrCreate(
            [
                'type' => $tmsNotif->type,
                'target' => $tmsNotif->target,
                'sendto' => $tmsNotif->sendto
            ],
            [
                'content' => json_encode($tmsNotif),
                'status_send' => $tmsNotif->status_send,
                'createdby' => $tmsNotif->createdby,
                'course_id' => $tmsNotif->course_id,
                'action' => $action
            ]);
//        $tms_notifLog = new \App\TmsNotificationLog();
//        $tms_notifLog->type = $tmsNotif->type;
//        $tms_notifLog->target = $tmsNotif->target;
//        $tms_notifLog->content = json_encode($tmsNotif);
//        $tms_notifLog->sendto = $tmsNotif->sendto;
//        $tms_notifLog->status_send = $tmsNotif->status_send;
//        $tms_notifLog->createdby = $tmsNotif->createdby;
//        $tms_notifLog->course_id = $tmsNotif->course_id;
//        $tms_notifLog->action = $action;
//        $tms_notifLog->save();
    }

    #region auto enrol hoc vien vao khoa hoc cap chung chi khi duoc tao
    public function autoEnrol()
    {
        //category = 3
        //user role = 5 student
        //mdl_course / mdl_user / mdl_enrol / mdl_user_enrolments
        $role_id = Role::ROLE_STUDENT; //student_role
        $category_id = 3; //category chung chi
        $all_courses = $this->getAllRequiredCourses();
        $a = 0;
        $b = 0;

        if (count($all_courses) != 0) {
            $course_data_array = array();
            $students = $this->getStudents();

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

                $new_enrol = MdlEnrol::firstOrCreate(
                    [
                        'enrol' => 'manual',
                        'courseid' => $course_id,
                        'roleid' => $role_id,
                    ],
                    [
                        'sortorder' => 0,
                        'status' => 0,
                        'expirythreshold' => 86400,
                        'timecreated' => $now,
                        'timemodified' => $now
                    ]
                );
//                    $guest = MdlEnrol::firstOrCreate(
//                        [
//                            'enrol' => 'guest',
//                            'courseid' => $course_id,
//                            'roleid' => $role_id,
//                            'sortorder' => 1
//                        ],
//                        [
//                            'expirythreshold' => 86400,
//                            'timecreated' => $now,
//                            'timemodified' => $now
//                        ]
//                    );
//                    $self = MdlEnrol::firstOrCreate(
//                        [
//                            'enrol' => 'self',
//                            'courseid' => $course_id,
//                            'roleid' => $role_id,
//                            'sortorder' => 2
//                        ],
//                        [
//                            'expirythreshold' => 86400,
//                            'timecreated' => $now,
//                            'timemodified' => $now
//                        ]
//                    );
                foreach ($user_ids as $user_id) {
                    MdlUserEnrolments::firstOrCreate(
                        [
                            'enrolid' => $new_enrol->id,
                            'userid' => $user_id,
                        ],
                        [
                            'timestart' => $now,
                            'timecreated' => $now,
                            'timemodified' => $now
                        ]
                    );

                    MdlRoleAssignments::firstOrCreate(
                        [
                            'roleid' => $role_id,
                            'userid' => $user_id,
                            'contextid' => $context_id
                        ]
                    );

                    //Tồn tại bản ghi trong bang mdl_grade_items
                    if ($grade_id != 0) {
                        //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
                        MdlGradeGrade::firstOrCreate(
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
                    MdlCourseCompletions::firstOrCreate(
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

    #endregion


    function checkCourseComplete($user_id)
    {
        $check = true;
        /*$completion_count = \App\MdlCourseCompletions::where('userid', $user_id)->whereIn('course', $this->certificate_course_id())->count();
        if ($completion_count == $this->certificate_course_number()) {
            $check = true;
        }*/

        $category = $this->certificate_course($user_id);
        $data = DB::table('mdl_user_enrolments as mu')
            ->join('mdl_user as u', 'u.id', '=', 'mu.userid')
            ->join('mdl_enrol as e', 'e.id', '=', 'mu.enrolid')
            ->join('mdl_course as c', 'c.id', '=', 'e.courseid')
            ->join('mdl_course_completion_criteria as ccc', 'ccc.course', '=', 'c.id')
            ->where('u.id', '=', $user_id)
            ->select('ccc.gradepass as gradepass'
                , DB::raw('(select count(cmc.coursemoduleid) as course_learn from mdl_course_modules cm
                inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cmc.completionstate in (1,2) and cmc.userid = ' . $user_id . ' and cm.course = c.id)
                as user_course_completionstate')

                , DB::raw('(select count(cm.id) as course_learn from mdl_course_modules cm
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cm.course = c.id and cm.completion <> 0) as user_course_learn')

                , DB::raw('IF( EXISTS(select cc.id from mdl_course_completions as cc
                                 where cc.userid = ' . $user_id . ' and cc.course = c.id and cc.timecompleted is not null ), "1", "0") as status_user')

                , DB::raw('(select `g`.`finalgrade`
  				from mdl_grade_items as gi
				join mdl_grade_grades as g
				on g.itemid = gi.id
				where gi.courseid = c.id and gi.itemtype = "course" and g.userid = ' . $user_id . ' ) as finalgrade'));
        $data = $data->where('c.category', '=', $category);
        $data = $data->get();

        if ($data) {
            foreach ($data as $data_item) {
                if (
                    $data_item->status_user != 1 &&
                    $data_item->finalgrade < $data_item->gradepass &&
                    $data_item->user_course_completionstate != $data_item->user_course_learn &&
                    $data_item->user_course_completionstate == 0
                ) {
                    $check = false;
                }
            }
        }

        return $check;
    }

    function certificate_course($user_id)
    {
        $category_id = \App\TmsTrainningUser::with('category')->where('user_id', $user_id)->first();
        $category_id = $category_id['category']['category_id'];
        return $category_id;
    }

    function getStudents()
    {
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
            ->get();
        return $students;
    }

    function getUserByRole($role_id)
    {
        $students = MdlUser::where('roles.id', '=', $role_id)
            ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select(
                'mdl_user.id',
                'username',
                'firstname',
                'lastname',
                'email'
            )
            ->get();
        return $students;
    }

    /**
     *
     * @return array|null
     *
     */
    function getAllRequiredCourses()
    {
        $category_id = 3; //category chung chi
        $all_courses = MdlCourse::where('category', $category_id)
            ->select('mdl_course.id', 'mdl_context.id as context_id', 'mdl_grade_items.id as grade_id')
            ->leftJoin('mdl_context', function ($join) {
                $join->on('mdl_context.instanceid', '=', 'mdl_course.id');
                $join->where('mdl_context.contextlevel', '=', \App\MdlUser::CONTEXT_COURSE);
            })
            ->leftJoin('mdl_grade_items', function ($join) {
                $join->on('mdl_course.id', '=', 'mdl_grade_items.courseid');
                $join->where('mdl_grade_items.itemtype', '=', 'course');
            })
            ->get();

        return $all_courses;
    }

    /**
     *
     * uydd Auto add user from TmsTrainningUser Table
     *
     */
    function autoAddTrainningUser()
    {
        $result = $this->updateFlagCron(Config::get('constants.domain.ENROLL_TRAINNING'), Config::get('constants.domain.ACTION_READ_FLAG'), '');
        $result = json_decode($result, true);

        if ($result['flag'] == 'stop')
            return;

        $queryArray = [];
        $userArrayByTraining = [];

        $num = 0;
        $limit = 300;
        //Gán người dùng vào khung năng lực k được gán cơ cấu tổ chức và nhóm quyền ( Khung năng lực default )
        $trainningArray = DB::table('tms_traninning_programs as ttp')
            ->select('ttp.id', 'ttg.id as ttg_id')
            ->leftJoin('tms_trainning_groups as ttg', 'ttg.trainning_id', '=', 'ttp.id')
            ->leftJoin('tms_trainning_courses as ttc', 'ttc.trainning_id', '=', 'ttp.id')
            ->where('ttp.deleted', '=', 0)
            ->where('ttp.run_cron', '=', 1)
            ->where('ttp.style', '!=', 2)//ko quet cac KNL group course da hoan thanh
            ->whereNull('ttg.id')
            ->whereNotNull('ttc.id')
            ->where('ttc.deleted', '=', 0)
            ->pluck('ttp.id');

        if (!empty($trainningArray)) {
            foreach ($trainningArray as $trainning) {
                $leftjoin = '(SELECT ttu.trainning_id,ttu.user_id
                    FROM tms_traninning_users ttu
                     where ttu.trainning_id =' . $trainning . ') as ttpp';
                $leftjoin = DB::raw($leftjoin);
                $users = DB::table('tms_user_detail as tud')
                    ->select('ttpp.trainning_id', 'tud.user_id')
                    ->leftJoin($leftjoin, 'ttpp.user_id', '=', 'tud.user_id')
                    ->where('tud.deleted', '=', 0)
                    ->whereNull('ttpp.trainning_id')
                    ->pluck('tud.user_id');
                if (!empty($users)) {
                    foreach ($users as $user) {
                        $queryItem = [];
                        $queryItem['trainning_id'] = $trainning;
                        $queryItem['user_id'] = $user;
                        $queryItem['created_at'] = Carbon::now();
                        $queryItem['updated_at'] = Carbon::now();


                        array_push($queryArray, $queryItem);
                        $userArrayByTraining[$trainning][] = $user;

                        $num++;
                        if ($num >= $limit) { //Đạt giới hạn => insert luôn
                            TmsTrainningUser::insert($queryArray);
                            foreach ($userArrayByTraining as $training => $users) {
                                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                            }
                            //Reset
                            $num = 0;
                            $queryArray = [];
                            $userArrayByTraining = [];
                        }

                    }
                }
            }
            TmsTrainningUser::insert($queryArray);
            //Lần cuối insert nốt số còn lại
            foreach ($userArrayByTraining as $training => $users) {
                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
            }
            //Reset
            $num = 0;
            $queryArray = [];
            $userArrayByTraining = [];
        }

        usleep(100);
        //xy ly cho TH KNL gan cho co cau to chuc or nhom quyen
        //Gán người dùng vào khung năng lực đã được gán với cơ cấu tổ chức hoặc nhóm quyền
        $lstDataTrainning = TmsTrainningGroup::select('trainning_id', 'group_id', 'type', DB::raw('count(trainning_id) as total_tr'))->groupBy('trainning_id')->get();

        $userArrayByTraining = [];

        foreach ($lstDataTrainning as $trainning) {
            $count_courses = TmsTrainningCourse::where('trainning_id', $trainning->trainning_id)->where('deleted', 0)->count();
            if ($count_courses > 0) {
                usleep(50);
                if ($trainning->total_tr == 2) {
                    //region xu ly cho TH KNL ap dung cho ca role va cctc
                    $trainning_gr = TmsTrainningGroup::where('trainning_id', $trainning->trainning_id)->select('group_id', 'type')->get();
                    $role_id = 0;
                    $org_id = 0;
                    foreach ($trainning_gr as $ttr) {
                        if ($ttr->type == 0) {
                            $role_id = $ttr->group_id;
                        } else {
                            $org_id = $ttr->group_id;
                        }
                    }

                    usleep(100);

                    //raw query lay so user nam trong cctc
                    $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                    join tms_user_detail tud on tud.user_id = toe.user_id
                                     join tms_organization tor on tor.id = toe.organization_id
                                     where tud.deleted = 0
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $org_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe
                              join tms_user_detail tud on tud.user_id = toe.user_id
                            where tud.deleted = 0 and toe.organization_id = ' . $org_id . '
                            ) as org_tp';

                    $org_query = DB::raw($org_query);

                    $role_query = '(SELECT u.id as user_id from mdl_user u join model_has_roles mhr on mhr.model_id = u.id where mhr.role_id = ' . $role_id . ') ttp_r';

                    $role_query = DB::raw($role_query);

                    //raw query lay so user nam trong KNL
                    $trr_query = '(select user_id, trainning_id from tms_traninning_users where trainning_id = ' . $trainning->trainning_id . ') ttu';
                    $trr_query = DB::raw($trr_query);

                    //lay danh sach user nam trong cctc va role nhung chua  dc add vao KNL
                    $users = DB::table($org_query)->join($role_query, 'ttp_r.user_id', '=', 'org_tp.org_uid')
                        ->leftJoin($trr_query, 'ttu.user_id', '=', 'org_tp.org_uid')
                        ->whereNull('ttu.trainning_id')->groupBy('org_tp.org_uid')->pluck('org_tp.org_uid');

                    //add user to compentecy
                    if (count($users) > 0) {
                        foreach ($users as $user) {
                            $queryItem = [];

                            $queryItem['trainning_id'] = $trainning->trainning_id;
                            $queryItem['user_id'] = $user;
                            $queryItem['created_at'] = Carbon::now();
                            $queryItem['updated_at'] = Carbon::now();

                            array_push($queryArray, $queryItem);
                            $userArrayByTraining[$trainning->trainning_id] = $user;
                            $num++;

                            if ($num >= $limit) { //Reached limit, execute this session
                                TmsTrainningUser::insert($queryArray);
                                foreach ($userArrayByTraining as $training => $users) {
                                    $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                                }
                                //Reset
                                $num = 0;
                                $queryArray = [];
                                $userArrayByTraining = [];
                            }

                        }//endforeach
                        //Last turn, execute leftover < limit
                        TmsTrainningUser::insert($queryArray);
                        foreach ($userArrayByTraining as $training => $users) {
                            $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                        }
                        //Reset
                        $num = 0;
                        $queryArray = [];
                        $userArrayByTraining = [];
                    }
                    //endregion
                } else {
                    if ($trainning->type == 0) {

                        $this->trainning_id = $trainning->trainning_id;

                        //region Nhóm quyền

                        $users = '(SELECT u.id as user_id from mdl_user u
                    join model_has_roles mhr on mhr.model_id = u.id
                    join tms_user_detail tud on tud.user_id = u.id
                    left join tms_traninning_users ttu on ttu.trainning_id = ' . $trainning->trainning_id . ' and ttu.user_id = mhr.model_id
                    where mhr.role_id = ' . $trainning->group_id . ' and tud.deleted = 0 and ttu.id is null)';

                        $users = DB::raw($users);
                        $users = DB::select($users);

                        if (count($users) > 0) {
                            foreach ($users as $user) {
                                $queryItem = [];

                                $queryItem['trainning_id'] = $trainning->trainning_id;
                                $queryItem['user_id'] = $user->user_id;
                                $queryItem['created_at'] = Carbon::now();
                                $queryItem['updated_at'] = Carbon::now();

                                array_push($queryArray, $queryItem);
                                $userArrayByTraining[$trainning->trainning_id][] = $user;

                                $num++;

                                if ($num >= $limit) {
                                    TmsTrainningUser::insert($queryArray);
                                    foreach ($userArrayByTraining as $training => $users) {
                                        $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                                    }
                                    $num = 0;
                                    $queryArray = [];
                                    $userArrayByTraining = [];

                                }
                            }//endforeach
                            TmsTrainningUser::insert($queryArray);
                            foreach ($userArrayByTraining as $training => $users) {
                                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                            }
                            $num = 0;
                            $queryArray = [];
                            $userArrayByTraining = [];
                        }


                        //endregion
                    } else {
                        //region Cơ cấu tổ chức
                        //lay danh sach user nam trong co cau to chuc va ko nam trong KNL
//                    $tblQuery = '(select  ttoe.organization_id, ttoe.user_id
//                                    from (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
//                                     join tms_organization tor on tor.id = toe.organization_id
//                                     order by tor.parent_id, toe.id) ttoe,
//                                    (select @pv := ' . $trainning->group_id . ') initialisation
//                                    where   find_in_set(ttoe.parent_id, @pv)
//                                    and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
//                                    UNION
//                                    select   toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $trainning->group_id . ') as org_us';

                        $tblQuery = '(select ttoe.organization_id,
                                   ttoe.user_id
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                    join tms_user_detail tud on tud.user_id = toe.user_id
                                     join tms_organization tor on tor.id = toe.organization_id
                                     where tud.deleted = 0
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $trainning->group_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe
                              join tms_user_detail tud on tud.user_id = toe.user_id
                            where tud.deleted = 0 and toe.organization_id = ' . $trainning->group_id . '
                            ) as org_us';


                        $tblQuery = DB::raw($tblQuery);

                        $leftJoin = '(select user_id, trainning_id from tms_traninning_users where trainning_id = ' . $trainning->trainning_id . ') ttu';
                        $leftJoin = DB::raw($leftJoin);

                        $users = DB::table($tblQuery)->leftJoin($leftJoin, 'ttu.user_id', '=', 'org_us.user_id')
                            ->whereNull('ttu.trainning_id')->groupBy('org_us.user_id')
                            ->pluck('org_us.user_id')->toArray();

                        if (count($users) > 0) {
                            foreach ($users as $user) {
                                $queryItem = [];

                                $queryItem['trainning_id'] = $trainning->trainning_id;
                                $queryItem['user_id'] = $user;
                                $queryItem['created_at'] = Carbon::now();
                                $queryItem['updated_at'] = Carbon::now();

                                array_push($queryArray, $queryItem);
                                $userArrayByTraining[$trainning->trainning_id][] = $user;

                                $num++;

                                if ($num >= $limit) {
                                    TmsTrainningUser::insert($queryArray);
                                    foreach ($userArrayByTraining as $training => $users) {
                                        $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                                    }
                                    $num = 0;
                                    $queryArray = [];
                                    $userArrayByTraining = [];
                                }
                            }//endforeach
                            TmsTrainningUser::insert($queryArray);
                            foreach ($userArrayByTraining as $training => $users) {
                                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                            }
                            $num = 0;
                            $queryArray = [];
                            $userArrayByTraining = [];
                        }
                        //endregion
                    }
                }
            }

            usleep(50);
        }


        //cap nhat trang thai cho cron
        $this->updateFlagCron(Config::get('constants.domain.ENROLL_TRAINNING'), Config::get('constants.domain.ACTION_UPDATE_FLAG'),
            Config::get('constants.domain.STOP_CRON'));

        usleep(100);
        //khoi dong cron enroll hoc vien vao khoa hoc trong KNL
        $this->updateFlagCron(Config::get('constants.domain.ENROLL_USER'), Config::get('constants.domain.ACTION_UPDATE_FLAG'),
            Config::get('constants.domain.START_CRON'));
    }

    //them user vao KNL trong TH user duoc tao moi, cron chay theo thoi gian
    public $trainning_id;

    function autoAddTrainningUserCron()
    {
        $queryArray = [];
        $userArrayByTraining = [];

        $num = 0;
        $limit = 300;
        //Gán người dùng vào khung năng lực k được gán cơ cấu tổ chức và nhóm quyền ( Khung năng lực default )
        $trainningArray = DB::table('tms_traninning_programs as ttp')
            ->select('ttp.id', 'ttg.id as ttg_id')
            ->leftJoin('tms_trainning_groups as ttg', 'ttg.trainning_id', '=', 'ttp.id')
            ->leftJoin('tms_trainning_courses as ttc', 'ttc.trainning_id', '=', 'ttp.id')
            ->where('ttp.deleted', '=', 0)
            ->where('ttp.run_cron', '=', 1)
            ->where('ttp.style', '!=', 2)//ko quet cac KNL group course da hoan thanh
            ->whereNull('ttg.id')
            ->whereNotNull('ttc.id')
            ->where('ttc.deleted', '=', 0)
            ->pluck('ttp.id');

        if (!empty($trainningArray)) {
            foreach ($trainningArray as $trainning) {
                $leftjoin = '(SELECT ttu.trainning_id,ttu.user_id
                    FROM tms_traninning_users ttu
                     where ttu.trainning_id =' . $trainning . ') as ttpp';
                $leftjoin = DB::raw($leftjoin);
                $users = DB::table('tms_user_detail as tud')
                    ->select('ttpp.trainning_id', 'tud.user_id')
                    ->leftJoin($leftjoin, 'ttpp.user_id', '=', 'tud.user_id')
                    ->where('tud.deleted', '=', 0)
                    ->whereNull('ttpp.trainning_id')
                    ->pluck('tud.user_id');
                if (!empty($users)) {
                    foreach ($users as $user) {
                        $queryItem = [];
                        $queryItem['trainning_id'] = $trainning;
                        $queryItem['user_id'] = $user;
                        $queryItem['created_at'] = Carbon::now();
                        $queryItem['updated_at'] = Carbon::now();


                        array_push($queryArray, $queryItem);
                        $userArrayByTraining[$trainning][] = $user;

                        $num++;
                        if ($num >= $limit) { //Đạt giới hạn => insert luôn
                            TmsTrainningUser::insert($queryArray);
                            foreach ($userArrayByTraining as $training => $users) {
                                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                            }
                            //Reset
                            $num = 0;
                            $queryArray = [];
                            $userArrayByTraining = [];
                        }

                    }
                }
                TmsTrainningUser::insert($queryArray);
                //Lần cuối insert nốt số còn lại
                foreach ($userArrayByTraining as $training => $users) {
                    $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                }
                //Reset
                $num = 0;
                $queryArray = [];
                $userArrayByTraining = [];
            }

        }

        usleep(100);
        //xy ly cho TH KNL gan cho co cau to chuc or nhom quyen
        //Gán người dùng vào khung năng lực đã được gán với cơ cấu tổ chức hoặc nhóm quyền
        $lstDataTrainning = TmsTrainningGroup::select('trainning_id', 'group_id', 'type', DB::raw('count(trainning_id) as total_tr'))->groupBy('trainning_id')->get();

        $userArrayByTraining = [];

        foreach ($lstDataTrainning as $trainning) {
            $count_courses = TmsTrainningCourse::where('trainning_id', $trainning->trainning_id)->where('deleted', 0)->count();

            if ($count_courses > 0) {
                usleep(50);
                if ($trainning->total_tr == 2) {
                    //region xu ly cho TH KNL ap dung cho ca role va cctc
                    $trainning_gr = TmsTrainningGroup::where('trainning_id', $trainning->trainning_id)->select('group_id', 'type')->get();
                    $role_id = 0;
                    $org_id = 0;
                    foreach ($trainning_gr as $ttr) {
                        if ($ttr->type == 0) {
                            $role_id = $ttr->group_id;
                        } else {
                            $org_id = $ttr->group_id;
                        }
                    }

                    usleep(100);

                    //raw query lay so user nam trong cctc
                    $org_query = '(select ttoe.organization_id,
                                   ttoe.user_id as org_uid
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                    join tms_user_detail tud on tud.user_id = toe.user_id
                                     join tms_organization tor on tor.id = toe.organization_id
                                     where tud.deleted = 0
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $org_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe
                              join tms_user_detail tud on tud.user_id = toe.user_id
                            where tud.deleted = 0 and toe.organization_id = ' . $org_id . '
                            ) as org_tp';

                    $org_query = DB::raw($org_query);

                    $role_query = '(SELECT u.id as user_id from mdl_user u join model_has_roles mhr on mhr.model_id = u.id where mhr.role_id = ' . $role_id . ') ttp_r';

                    $role_query = DB::raw($role_query);

                    //raw query lay so user nam trong KNL
                    $trr_query = '(select user_id, trainning_id from tms_traninning_users where trainning_id = ' . $trainning->trainning_id . ') ttu';
                    $trr_query = DB::raw($trr_query);

                    //lay danh sach user nam trong cctc va role nhung chua  dc add vao KNL
                    $users = DB::table($org_query)->join($role_query, 'ttp_r.user_id', '=', 'org_tp.org_uid')
                        ->leftJoin($trr_query, 'ttu.user_id', '=', 'org_tp.org_uid')
                        ->whereNull('ttu.trainning_id')->groupBy('org_tp.org_uid')->pluck('org_tp.org_uid');

                    //add user to compentecy
                    if (count($users) > 0) {
                        foreach ($users as $user) {
                            $queryItem = [];

                            $queryItem['trainning_id'] = $trainning->trainning_id;
                            $queryItem['user_id'] = $user;
                            $queryItem['created_at'] = Carbon::now();
                            $queryItem['updated_at'] = Carbon::now();

                            array_push($queryArray, $queryItem);
                            $userArrayByTraining[$trainning->trainning_id] = $user;
                            $num++;

                            if ($num >= $limit) { //Reached limit, execute this session
                                TmsTrainningUser::insert($queryArray);
                                foreach ($userArrayByTraining as $training => $users) {
                                    $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                                }
                                //Reset
                                $num = 0;
                                $queryArray = [];
                                $userArrayByTraining = [];
                            }

                        }//endforeach
                        //Last turn, execute leftover < limit
                        TmsTrainningUser::insert($queryArray);
                        foreach ($userArrayByTraining as $training => $users) {
                            $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                        }
                        //Reset
                        $num = 0;
                        $queryArray = [];
                        $userArrayByTraining = [];
                    }
                    //endregion
                } else {
                    if ($trainning->type == 0) {

                        $this->trainning_id = $trainning->trainning_id;

                        //region Nhóm quyền

                        $users = '(SELECT u.id as user_id from mdl_user u
                    join model_has_roles mhr on mhr.model_id = u.id
                    join tms_user_detail tud on tud.user_id = u.id
                    left join tms_traninning_users ttu on ttu.trainning_id = ' . $trainning->trainning_id . ' and ttu.user_id = mhr.model_id
                    where mhr.role_id = ' . $trainning->group_id . ' and tud.deleted = 0 and ttu.id is null)';

                        $users = DB::raw($users);
                        $users = DB::select($users);

                        if (count($users) > 0) {
                            foreach ($users as $user) {
                                $queryItem = [];

                                $queryItem['trainning_id'] = $trainning->trainning_id;
                                $queryItem['user_id'] = $user->user_id;
                                $queryItem['created_at'] = Carbon::now();
                                $queryItem['updated_at'] = Carbon::now();

                                array_push($queryArray, $queryItem);
                                $userArrayByTraining[$trainning->trainning_id][] = $user;

                                $num++;

                                if ($num >= $limit) {
                                    TmsTrainningUser::insert($queryArray);
                                    foreach ($userArrayByTraining as $training => $users) {
                                        $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                                    }
                                    $num = 0;
                                    $queryArray = [];
                                    $userArrayByTraining = [];

                                }
                            }//endforeach
                            TmsTrainningUser::insert($queryArray);
                            foreach ($userArrayByTraining as $training => $users) {
                                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                            }
                            $num = 0;
                            $queryArray = [];
                            $userArrayByTraining = [];
                        }


                        //endregion
                    } else {
                        //region Cơ cấu tổ chức
                        //lay danh sach user nam trong co cau to chuc va ko nam trong KNL
//                    $tblQuery = '(select  ttoe.organization_id, ttoe.user_id
//                                    from (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
//                                     join tms_organization tor on tor.id = toe.organization_id
//                                     order by tor.parent_id, toe.id) ttoe,
//                                    (select @pv := ' . $trainning->group_id . ') initialisation
//                                    where   find_in_set(ttoe.parent_id, @pv)
//                                    and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
//                                    UNION
//                                    select   toe.organization_id,toe.user_id from tms_organization_employee toe where toe.organization_id = ' . $trainning->group_id . ') as org_us';

                        $tblQuery = '(select ttoe.organization_id,
                                   ttoe.user_id
                            from    (select toe.organization_id, toe.user_id,tor.parent_id from tms_organization_employee toe
                                    join tms_user_detail tud on tud.user_id = toe.user_id
                                     join tms_organization tor on tor.id = toe.organization_id
                                     where tud.deleted = 0
                                     order by tor.parent_id, toe.id) ttoe,
                                    (select @pv := ' . $trainning->group_id . ') initialisation
                            where   find_in_set(ttoe.parent_id, @pv)
                            and     length(@pv := concat(@pv, \',\', ttoe.organization_id))
                            UNION
                            select toe.organization_id,toe.user_id from tms_organization_employee toe
                              join tms_user_detail tud on tud.user_id = toe.user_id
                            where tud.deleted = 0 and toe.organization_id = ' . $trainning->group_id . '
                            ) as org_us';


                        $tblQuery = DB::raw($tblQuery);

                        $leftJoin = '(select user_id, trainning_id from tms_traninning_users where trainning_id = ' . $trainning->trainning_id . ') ttu';
                        $leftJoin = DB::raw($leftJoin);

                        $users = DB::table($tblQuery)->leftJoin($leftJoin, 'ttu.user_id', '=', 'org_us.user_id')
                            ->whereNull('ttu.trainning_id')->groupBy('org_us.user_id')
                            ->pluck('org_us.user_id')->toArray();

                        if (count($users) > 0) {
                            foreach ($users as $user) {
                                $queryItem = [];

                                $queryItem['trainning_id'] = $trainning->trainning_id;
                                $queryItem['user_id'] = $user;
                                $queryItem['created_at'] = Carbon::now();
                                $queryItem['updated_at'] = Carbon::now();

                                array_push($queryArray, $queryItem);
                                $userArrayByTraining[$trainning->trainning_id][] = $user;

                                $num++;

                                if ($num >= $limit) {
                                    TmsTrainningUser::insert($queryArray);
                                    foreach ($userArrayByTraining as $training => $users) {
                                        $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                                    }
                                    $num = 0;
                                    $queryArray = [];
                                    $userArrayByTraining = [];
                                }
                            }//endforeach
                            TmsTrainningUser::insert($queryArray);
                            foreach ($userArrayByTraining as $training => $users) {
                                $this->insert_mail_notifications(TmsNotification::ASSIGNED_COMPETENCY, $users, $training);
                            }
                            $num = 0;
                            $queryArray = [];
                            $userArrayByTraining = [];
                        }
                        //endregion
                    }
                }
            }

            usleep(50);
        }
    }

    /**
     *
     * uydd Tự động cấp chứng chỉ và huy hiệu
     * Tự động thêm học viên vào bảng StudentCertificate
     */
    function autoCertificate()
    {
        try {
            //Tự động cấp chứng chỉ
            $users = DB::table('mdl_user as mu')
                ->select('mu.id as user_id', 'ttc.trainning_id', 'ttp.code as trainning_code', 'ttp.style as style', 'ttp.time_start', 'ttp.time_end', 'cf.timecompleted')
                ->join('course_final as cf', 'cf.userid', '=', 'mu.id')
                ->join('tms_trainning_courses as ttc', 'ttc.course_id', '=', 'cf.courseid')
                ->join('tms_traninning_programs as ttp', 'ttp.id', '=', 'ttc.trainning_id')
                ->leftJoin('student_certificate as sc', function ($join) {
                    $join->on('sc.userid', '=', 'mu.id');
                    $join->on('sc.trainning_id', '=', 'ttc.trainning_id');
                })
                ->whereNull('sc.id')
                ->where('ttp.auto_certificate', '=', 1)
                ->get();

            \DB::beginTransaction();

            $num = 0;
            $arr_data = [];
            $limit = 300;
//dd($users);
            foreach ($users as $user) {
                if (
                    ($user->style == 1 && intval($user->time_start) <= intval($user->timecompleted) && intval($user->time_end) >= intval($user->timecompleted)) ||
                    $user->style == 0
                ) {
                    $student = StudentCertificate::where([
                        'userid' => $user->user_id,
                        'trainning_id' => $user->trainning_id
                    ])->first();
                    //nếu học viên đã có mã thì không làm gì cả
                    if (!$student) {
                        $data_item = [];
                        //update status to 1
                        $certificatecode = $user->trainning_code . $this->randomNumber(7 - strlen($user->user_id));

                        $data_item['userid'] = $user->user_id;
                        $data_item['trainning_id'] = $user->trainning_id;
                        $data_item['code'] = $certificatecode;
                        $data_item['status'] = 1;
                        $data_item['timecertificate'] = time();
                        $data_item['created_at'] = Carbon::now();
                        $data_item['updated_at'] = Carbon::now();

                        array_push($arr_data, $data_item);
                        $num++;
                    }
                    if ($num >= $limit) {
                        $arr_data = $this->unique_multi_array($arr_data, ['userid', 'trainning_id']);
                        StudentCertificate::insert($arr_data);
                        $num = 0;
                        $arr_data = [];
                        usleep(100); //sleep tranh tinh trang query db lien tiep
                    }
                }
            }
            $arr_data = $this->unique_multi_array($arr_data, ['userid', 'trainning_id']);
            StudentCertificate::insert($arr_data);
            \DB::commit();

        } catch (\Exception  $e) {

        }
    }

    public function unique_multi_array($array, $keyArray)
    {
        $temp_array = array();
        $val_array = array();

        foreach ($array as $val) {
            $val_check = '';
            foreach ($keyArray as $key) {
                $val_check .= $val[$key] . ':';
            }
            if (!in_array($val_check, $val_array)) {
                array_push($val_array, $val_check);
                array_push($temp_array, $val);
            }
        }
        return $temp_array;
    }

    public function randomNumber($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }


    // send mail active email_user
    public function sendMailActiveEmail()
    {
        try {
            $contents = DB::table('tms_nofitications as tn')
                ->where('tn.type', '=', 'mail')
                ->where('tn.target', '=', TmsNotification::ACTIVE_EMAIL)
                ->where('tn.status_send', '=', TmsNotification::UN_SENT)
                ->select('tn.id', 'tn.content')
                ->get();
            if ($contents) {
                foreach ($contents as $c) {
                    if (strlen($c->content) > 0) {
                        $user = json_decode($c->content);
                        $link = Config::get('constants.domain.TMS') . 'page/email/confirm/' . $c->id . '/' . base64_encode($user->email);
                        Mail::to($user->email)->send(new CourseSendMail(
                            TmsNotification::ACTIVE_EMAIL,
                            $user->email,
                            $user->fullname,
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $link,
                            $user->password
                        ));
                    }
                }
            }
            return 'SUCCESS';
        } catch (Exception $e) {
            return 'ERROR';
        }
    }

    public function testCron()
    {
        Log::info('test cron: ' . time());
        return 'SUCCESS';
    }

    //region generate SAS Url Azure

    public function apiGenerateSASUrlAzure()
    {
        $arrMainId = [33, 140, 141];
        $lstData = MdlHvp::whereIn('main_library_id', $arrMainId)->select('id', 'main_library_id', 'json_content')->get();

        foreach ($lstData as $data) {
            $jsonData = json_decode($data->json_content, true);

            switch ($data->main_library_id) {
                case 33:
                case 141:
                    $this->processInteractive33($jsonData, $data->id);
                    break;
                case 140:
                    $this->processInteractive140($jsonData, $data->id);
                    break;
            }

        }

    }

    public function processInteractive33($jsonData, $id)
    {
        if (isset($jsonData['interactiveVideo']) && isset($jsonData['interactiveVideo']['video']) && isset($jsonData['interactiveVideo']['video']['files'])
            && isset($jsonData['interactiveVideo']['video']['files'][0]) && isset($jsonData['interactiveVideo']['video']['files'][0]['path'])
        ) {

            $path = $jsonData['interactiveVideo']['video']['files'][0]['path'];
            if (strpos($path, Config::get('constants.domain.CONTAINER_NAME')) !== false) {
                $file_name = basename($path);

                $file_name_rp = str_replace('#', '?', $file_name);
                $arr_name = explode('?', $file_name_rp);

                $blob_name = $arr_name[0];

                //$end_date = Carbon::now()->addHour(23)->addMinute(58);
                //$end_date = Carbon::now()->addSecond(12);
                //[VinhPT][Request] Change time to generate new url for azure from 12 seconds to 3 minutes
                $end_date = Carbon::now()->addMinute(30);

                $end_date = gmdate('Y-m-d\TH:i:s\Z', strtotime($end_date));

                $_signature = $this->getSASForBlob(Config::get('constants.domain.ACCOUNT_NAME'), Config::get('constants.domain.CONTAINER_NAME'), $blob_name, 'b', 'r', $end_date, Config::get('constants.domain.ACCOUNT_KEY'));
                $_blobUrl = $this->getBlobUrl(Config::get('constants.domain.ACCOUNT_NAME'), Config::get('constants.domain.CONTAINER_NAME'), $blob_name, 'b', 'r', $end_date, $_signature);

                $jsonData['interactiveVideo']['video']['files'][0]['path'] = $_blobUrl;

                $hvp = MdlHvp::findOrFail($id);
                $hvp->json_content = json_encode($jsonData);
                $hvp->filtered = json_encode($jsonData);
                $hvp->save();
                usleep(200);

            }

        }
    }

    public function processInteractive140($jsonData, $id)
    {
        if (isset($jsonData['content'])) {

            foreach ($jsonData['content'] as $key => $data_content) {

                if (isset($data_content['content']) && isset($data_content['content']['params'])
                    && isset($data_content['content']['params']['interactiveVideo']) && isset($data_content['content']['params']['interactiveVideo']['video'])
                    && isset($data_content['content']['params']['interactiveVideo']['video']['files']) && isset($data_content['content']['params']['interactiveVideo']['video']['files'][0])
                    && isset($data_content['content']['params']['interactiveVideo']['video']['files'][0]['path'])
                ) {
                    $path = $data_content['content']['params']['interactiveVideo']['video']['files'][0]['path'];

                    if (strpos($path, Config::get('constants.domain.CONTAINER_NAME')) !== false) {
                        $file_name = basename($path);

                        $file_name_rp = str_replace('#', '?', $file_name);
                        $arr_name = explode('?', $file_name_rp);

                        $blob_name = $arr_name[0];
                        //[VinhPT][Request] Change time to generate new url for azure from 12 seconds to 3 minutes
                        $end_date = Carbon::now()->addMinute(30);
                        //$end_date = Carbon::now()->addHour(23)->addMinute(59);
                        //$end_date = Carbon::now()->addSecond(12);

                        $end_date = gmdate('Y-m-d\TH:i:s\Z', strtotime($end_date));

                        $_signature = $this->getSASForBlob(Config::get('constants.domain.ACCOUNT_NAME'), Config::get('constants.domain.CONTAINER_NAME'), $blob_name, 'b', 'r', $end_date, Config::get('constants.domain.ACCOUNT_KEY'));
                        $_blobUrl = $this->getBlobUrl(Config::get('constants.domain.ACCOUNT_NAME'), Config::get('constants.domain.CONTAINER_NAME'), $blob_name, 'b', 'r', $end_date, $_signature);

                        $data_content['content']['params']['interactiveVideo']['video']['files'][0]['path'] = $_blobUrl;


                        $jsonData['content'][$key] = $data_content;
                        usleep(100);

                        $hvp = MdlHvp::findOrFail($id);
                        $hvp->json_content = json_encode($jsonData);
                        $hvp->filtered = json_encode($jsonData);
                        $hvp->save();

                    }
                } else if (isset($data_content['content']) && isset($data_content['content']['params'])
                    && isset($data_content['content']['params']['sources']) && isset($data_content['content']['params']['sources'][0])
                    && isset($data_content['content']['params']['sources'][0]['path'])
                ) {
                    $path = $data_content['content']['params']['sources'][0]['path'];

                    if (strpos($path, Config::get('constants.domain.CONTAINER_NAME')) !== false) {
                        $file_name = basename($path);

                        $file_name_rp = str_replace('#', '?', $file_name);
                        $arr_name = explode('?', $file_name_rp);

                        $blob_name = $arr_name[0];
                        //[VinhPT][Request] Change time to generate new url for azure from 12 seconds to 3 minutes
                        $end_date = Carbon::now()->addMinute(3);
                        //$end_date = Carbon::now()->addHour(23)->addMinute(59);
                        // $end_date = Carbon::now()->addSecond(12);

                        $end_date = gmdate('Y-m-d\TH:i:s\Z', strtotime($end_date));

                        $_signature = $this->getSASForBlob(Config::get('constants.domain.ACCOUNT_NAME'), Config::get('constants.domain.CONTAINER_NAME'), $blob_name, 'b', 'r', $end_date, Config::get('constants.domain.ACCOUNT_KEY'));
                        $_blobUrl = $this->getBlobUrl(Config::get('constants.domain.ACCOUNT_NAME'), Config::get('constants.domain.CONTAINER_NAME'), $blob_name, 'b', 'r', $end_date, $_signature);

                        $data_content['content']['params']['sources'][0]['path'] = $_blobUrl;


                        $jsonData['content'][$key] = $data_content;
                        usleep(100);

                        $hvp = MdlHvp::findOrFail($id);
                        $hvp->json_content = json_encode($jsonData);
                        $hvp->filtered = json_encode($jsonData);
                        $hvp->save();

                    }
                }
            }

            usleep(200);
        }
    }

    public function getSASForBlob($accountName, $container, $blob, $resourceType, $permissions, $expiry, $key)
    {

        /* Create the signature */
        $_arraysign = array();
        $_arraysign[] = $permissions;
        $_arraysign[] = '';
        $_arraysign[] = $expiry;
        $_arraysign[] = '/' . $accountName . '/' . $container . '/' . $blob;
        $_arraysign[] = '';
        $_arraysign[] = "2014-02-14"; //the API version is now required
        $_arraysign[] = '';
        $_arraysign[] = '';
        $_arraysign[] = '';
        $_arraysign[] = '';
        $_arraysign[] = '';

        $_str2sign = implode("\n", $_arraysign);

        return base64_encode(
            hash_hmac('sha256', urldecode(utf8_encode($_str2sign)), base64_decode($key), true)
        );
    }

    public function getBlobUrl($accountName, $container, $blob, $resourceType, $permissions, $expiry, $_signature)
    {
        /* Create the signed query part */
        $_parts = array();
        $_parts[] = (!empty($expiry)) ? 'se=' . urlencode($expiry) : '';
        $_parts[] = 'sr=' . $resourceType;
        $_parts[] = (!empty($permissions)) ? 'sp=' . $permissions : '';
        $_parts[] = 'sig=' . urlencode($_signature);
        $_parts[] = 'sv=2014-02-14';

        /* Create the signed blob URL */
        $_url = 'https://'
            . $accountName . '.blob.core.windows.net/'
            . $container . '/'
            . $blob . '?'
            . implode('&', $_parts);

        return $_url;
    }

    public function apiGenerateSASUrlAzureTest()
    {
        $arrMainId = [33, 140];
        $lstData = MdlHvp::whereIn('main_library_id', $arrMainId)->where('id', 1016)->select('id', 'main_library_id', 'json_content')->get();

        foreach ($lstData as $data) {
            $jsonData = json_decode($data->json_content, true);

            switch ($data->main_library_id) {
                case 33:
                    $this->processInteractive33($jsonData, $data->id);
                    break;
                case 140:
                    $this->processInteractive140($jsonData, $data->id);
                    break;
            }

        }

    }

    //endregion
}
