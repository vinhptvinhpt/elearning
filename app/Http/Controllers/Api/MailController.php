<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CourseSendMail;
use App\MdlCourse;
use App\MdlQuiz;
use App\MdlUser;
use App\Role;
use App\TmsConfigs;
use App\TmsDevice;
use App\TmsInvitation;
use App\TmsNotification;
use App\TmsOrganizationEmployee;
use App\TmsUserDetail;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Tintnaingwin\EmailChecker\Facades\EmailChecker;

class MailController extends Controller
{
    const DEFAULT_ITEMS_PER_SESSION = 200;
//    const DEVELOPMENT = 0;

    /* Load / generate configuration */
    public function loadConfiguration()
    {
//        $configs = array(
//            TmsNotification::ENROL => TmsConfigs::ENABLE,
//            TmsNotification::SUGGEST => TmsConfigs::ENABLE,
//            TmsNotification::QUIZ_START => TmsConfigs::ENABLE,
//            TmsNotification::QUIZ_END => TmsConfigs::ENABLE,
//            TmsNotification::QUIZ_COMPLETED => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_LOGIN => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_ACCESS_COURSE => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_EDUCATION_SCHEDULE => TmsConfigs::ENABLE,
//            TmsNotification::REMIND_UPCOMING_COURSE => TmsConfigs::ENABLE,
//        );

        $configs = array(
            //Default list
            TmsNotification::ENROL => TmsConfigs::ENABLE, // = ASSIGNED_COURSE
            TmsNotification::SUGGEST => TmsConfigs::ENABLE, // = SUGGEST_OPTIONAL_COURSE
            TmsNotification::ASSIGNED_COMPETENCY => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXAM => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::INVITE_STUDENT => TmsConfigs::ENABLE,
            TmsNotification::COMPLETED_FRAME => TmsConfigs::ENABLE,
            TmsNotification::FORGOT_PASSWORD => TmsConfigs::ENABLE,
            //Not in default list
            TmsNotification::REQUEST_MORE_ATTEMPT => TmsConfigs::ENABLE,
            TmsNotification::REMIND_CERTIFICATE => TmsConfigs::ENABLE,
            TmsNotification::CALCULATE_TOEIC_GRADE => TmsConfigs::ENABLE
        );
        $pdo = DB::connection()->getPdo();
        if ($pdo) {
            $stored_configs = TmsConfigs::whereIn('target', array_keys($configs))->get();
            $today = date('Y-m-d H:i:s', time());
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


    ///email_template/sendDemo/
    public function demoSendMail()
    {
        $countTotal = 0;
        $lstData = DB::table('tms_nofitications')
            ->where('updated_at', '<', '2020-08-10 23:59:59')
            ->select('sendto')->groupBy('sendto')->pluck('sendto');

        //get all emails in db
        $users = DB::table('tms_user_detail')
            ->whereIn('user_id', $lstData)
            ->select(
                'email',
                'fullname',
                'user_id'
            )->get();
        $sent = 0;
        $fail = 0;
        $countTotal = count($users);
        try {
            foreach ($users as $user) {
                //send mail can not continue if has fake email
                if (strlen($user->email) != 0 && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($user->email)->send(new CourseSendMail(
                        TmsNotification::NOTICE_SPAM_EMAIL,
                        '',
                        $user->fullname
                    ));

                    usleep(100);
                    $sent += 1;
                } else {
                    $fail += 1;
                }
            }
            \Log::info('success: ' . $user->user_id . ', email: ' . $user->email);
            $sent += 1;

        } catch (\Mockery\Exception $e) {
            $fail += 1;
            \Log::info('error: ' . $user->user_id . ', email: ' . $user->email);
        }

        return 'Total: ' . $countTotal . '; Sent: ' . $sent . ', Fail: ' . $fail;
    }


    public function welcome()
    {
        Mail::to("innrhy@gmail.com")->send(new CourseSendMail(
            TmsNotification::WELCOME,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ));
    }

    //Send email invite student in tms_invitation table
    public function sendInvitation()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::INVITE_STUDENT] == TmsConfigs::ENABLE) {
            $invite_list = TmsInvitation::with('course')->with('user.user')
                ->where('sent', \App\TmsNotification::UN_SENT)
                //->whereHas('user')
                //->whereHas('course')
                ->limit(100)
                ->get(); //lay danh sach cac thong bao chua gui

            $missing_info = 0;
            $sent = 0;
            $fail = 0;

            foreach ($invite_list as $item) {
                if (!$item->user || !$item->course) {
                    $missing_info += 1;
                } else {
                    $username = $item->user->user->username;
                    $fullname = $item->user->fullname;
                    $email = $item->user->email;
                    $course_code = $item->course->shortname;
                    $course_name = $item->course->fullname;
                    $start_date = $item->course->startdate;
                    $end_date = $item->course->enddate;
                    $course_place = $item->course->course_place;
                    $course_description = $item->course->summary;
                    $invite_id = $item->id;

                    //$email = "immrhy@gmail.com";

                    try {
                        //send mail can not continue if has fake email
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::INVITE_STUDENT,
                                $username,
                                $fullname,
                                $course_code,
                                $course_name,
                                $start_date,
                                $end_date,
                                $course_place,
                                '',
                                $invite_id,
                                '',
                                '',
                                '',
                                '',
                                $course_description
                            ));
                            $sent += 1;

                            $item->sent = TmsNotification::SENT;
                            $item->save();
                        } else {
                            $missing_info += 1;
                        }
                    } catch (Exception $e) {
                        $fail += 1;
                    }
                }
            }

            return response()->json(
                [
                    'missing_info' => $missing_info,
                    'sent' => $sent,
                    'fail' => $fail
                ]
            );

        }
    }

//Send email remind exam
    public function sendRemindExam()
    {
        $next_3_days = time() + 86400 * 3;

        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_EXAM] == TmsConfigs::ENABLE) {
            $lstNotifi = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::REMIND_EXAM)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->where('date_quiz', '<', $next_3_days)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotification = count($lstNotifi);

            if ($countRemindNotification > 0) {
                \DB::beginTransaction();
                foreach ($lstNotifi as $itemNotification) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotification->lastname . ' ' . $itemNotification->firstname;
                        $email = $itemNotification->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {

                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::REMIND_EXAM,
                                $itemNotification->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $itemNotification->content
                            ));
                            $object_content = [];
                            $exam = json_decode($itemNotification->content);
                            if (!empty($exam)) {
                                $object_content = array(
                                    'object_id' => '',
                                    'object_name' => $exam->quiz_name ,
                                    'object_type' => 'exam',
                                    'parent_id' => '',
                                    'parent_name' => '',
                                    'start_date' => $exam->start_time,
                                    'end_date' => $exam->end_time,
                                    'code' => '',
                                    'room' => '',
                                    'grade' => '',
                                );
                            }
                            $itemNotification->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotification, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                //$this->sendPushNotification($lstNotifi);
                \DB::commit();
            }
        }
    }

//Send email toeic result (type = calculate_toeic_grade)
    public function sendToeicResult()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::CALCULATE_TOEIC_GRADE] == TmsConfigs::ENABLE) {
            $lstNotifi = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::CALCULATE_TOEIC_GRADE)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotification = count($lstNotifi);

            if ($countRemindNotification > 0) {
                \DB::beginTransaction();
                foreach ($lstNotifi as $itemNotification) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotification->lastname . ' ' . $itemNotification->firstname;
                        $email = $itemNotification->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {

                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::CALCULATE_TOEIC_GRADE,
                                $itemNotification->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $itemNotification->content
                            ));

                            $object_content = [];

                            $result = json_decode($itemNotification->content);

                            if (!empty($result)) {
                                $object_content = array(
                                    'object_id' => '',
                                    'object_name' => '',
                                    'object_type' => 'toeic-result',
                                    'parent_id' => '',
                                    'parent_name' => '',
                                    'start_date' => '',
                                    'end_date' => '',
                                    'code' => '',
                                    'room' => '',
                                    'grade' => '',
                                    'reading' => $result->reading,
                                    'listening' => $result->listening,
                                    'total' => $result->total
                                );
                            }
                            $itemNotification->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotification, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                //$this->sendPushNotification($lstNotifi);
                \DB::commit();
            }
        }
    }

//Send email enrol, quiz_start, quiz_end, quiz_completed (has course data included in content)
//Notification record created by VinhPT, Tho
//Checked ok 2020 March 24
//Type one time(just send unsent notification)
    public function sendEnrolQuizStartQuizEndQuizCompleted()
    {
        $configs = self::loadConfiguration();
        $turn_on = 0;
        $turn_on_simple = array();
        $turn_on_complex = array();
        if ($configs[TmsNotification::ENROL] == 'enable') {
            $turn_on_simple[] = TmsNotification::ENROL;
            $turn_on += 1;
        }
//        if ($configs[TmsNotification::QUIZ_COMPLETED] == 'enable') {
//            $turn_on_simple[] = TmsNotification::QUIZ_COMPLETED;
//            $turn_on += 1;
//        }
//        if ($configs[TmsNotification::QUIZ_START] == 'enable') {
//            $turn_on_simple[] = TmsNotification::QUIZ_START;
//            $turn_on += 1;
//        }
//        if ($configs[TmsNotification::QUIZ_END] == 'enable') {
//            $turn_on_complex[] = TmsNotification::QUIZ_END;
//            $turn_on += 1;
//        }

        if ($turn_on != 0) {
            $lstNotif = TmsNotification::where('status_send', \App\TmsNotification::UN_SENT)
                ->where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where(function ($q) use ($turn_on_simple, $turn_on_complex) {

                    $next_3_days = time() + 86400 * 3;

                    if (!empty($turn_on_simple)) {
                        $q->whereIn('target', $turn_on_simple);
                    }
                    if (!empty($turn_on_complex)) {
                        if (!empty($turn_on_simple)) {
                            $q->orWhere([
                                ['target', '=', \App\TmsNotification::QUIZ_END], //quiz end, date_quiz < current date + 3 days
                                ['date_quiz', '<', $next_3_days]
                            ]);
                        } else {
                            $q->where([
                                ['target', '=', \App\TmsNotification::QUIZ_END], //quiz end, date_quiz < current date + 3 days
                                ['date_quiz', '<', $next_3_days]
                            ]);
                        }
                    }

//                        $q->where('target', TmsNotification::ENROL);
//                        $q->orWhere('target', TmsNotification::QUIZ_COMPLETED);
//                        $q->orWhere('target', TmsNotification::QUIZ_START);
//                        $q->orWhere([
//                            ['target', '=', \App\TmsNotification::QUIZ_END], //quiz end, date_quiz < current date + 3 days
//                            ['date_quiz', '<', $next_3_days]
//                        ]);

                })
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->leftJoin('mdl_course', 'mdl_course.id', '=', 'tms_nofitications.course_id')
                //check exist mdl_course
//                ->whereIn('tms_nofitications.course_id', function ($query) {
//                    $query->select('id')->from('mdl_course');
//                })
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.date_quiz',
                    'tms_nofitications.content',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username',

                    'mdl_course.fullname',
                    'mdl_course.shortname',
                    'mdl_course.course_place',
                    'mdl_course.startdate',
                    'mdl_course.enddate'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get();

            $countNotif = count($lstNotif);

            if ($countNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstNotif as $itemNotif) {
                    try {
                        $email = $itemNotif->email;
                        if (strlen($email) > 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                            $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                            $send = 1;
                            $quiz_data = null;
                            if (
                                $itemNotif->target == TmsNotification::QUIZ_COMPLETED
                                || $itemNotif->target == TmsNotification::QUIZ_START
                                || $itemNotif->target == TmsNotification::QUIZ_END
                            ) {
                                $send = 0;
                                $content = $itemNotif->content;
                                if (strlen($content) != 0) {
                                    $content_array = json_decode($content, true);
                                    if (!($content_array === null && json_last_error() !== JSON_ERROR_NONE)) {// json hop le

                                        $quiz_id = isset($content_array['quiz_id']) ? $content_array['quiz_id'] : '';
                                        if (strlen($quiz_id) != 0 && is_numeric($quiz_id)) {
                                            if ($itemNotif->target == TmsNotification::QUIZ_START || $itemNotif->target == TmsNotification::QUIZ_END) {
                                                $quiz_data = MdlQuiz::where('mdl_quiz.id', $quiz_id)
                                                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_quiz.course')
                                                    ->select(
                                                        'mdl_quiz.name',
                                                        'mdl_quiz.course',
                                                        'mdl_course.fullname as course_name'
                                                    )
                                                    ->first();
                                                if (isset($quiz_data)) {
                                                    $send = 1;
                                                    $object_content = array(
                                                        'object_id' => $quiz_id,
                                                        'object_name' => $quiz_data->name,
                                                        'object_type' => 'quiz',
                                                        'parent_id' => $quiz_data->course,
                                                        'parent_name' => $quiz_data->course_name,
                                                        'start_date' => '',
                                                        'end_date' => $itemNotif->date_quiz,
                                                        'code' => '',
                                                        'room' => '',
                                                        'grade' => '',
                                                    );
                                                    $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                                                }
                                            } elseif ($itemNotif->target == TmsNotification::QUIZ_COMPLETED) { //return result;
                                                $quiz_data = MdlQuiz::where('mdl_quiz.id', $quiz_id)
                                                    ->where('mdl_quiz.attempts', 1)
                                                    ->join('mdl_quiz_attempts', 'mdl_quiz.id', '=', 'mdl_quiz_attempts.quiz')
                                                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_quiz.course')
                                                    ->select(
                                                        'mdl_quiz.name',
                                                        'mdl_quiz.sumgrades',
                                                        'mdl_quiz_attempts.sumgrades as attempt_sumgrades',
                                                        'mdl_quiz.course',
                                                        'mdl_course.fullname as course_name'
                                                    )
                                                    ->first();

                                                if (isset($quiz_data)) {
                                                    $send = 1;
                                                    $grade = ($quiz_data->attempt_sumgrades / $quiz_data->sumgrades) * 10;
                                                    $object_content = array(
                                                        'object_id' => $quiz_id,
                                                        'object_name' => $quiz_data->name,
                                                        'object_type' => 'quiz',
                                                        'parent_id' => $quiz_data->course,
                                                        'parent_name' => $quiz_data->course_name,
                                                        'start_date' => '',
                                                        'end_date' => $itemNotif->date_quiz,
                                                        'code' => '',
                                                        'room' => '',
                                                        'grade' => $grade,
                                                    );
                                                    $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {//TmsNotification::ENROL;
                                $course_detail = MdlCourse::where('id', $itemNotif->course_id)->first();
                                if (isset($course_detail)) {
                                    $object_content = array(
                                        'object_id' => $itemNotif->course_id,
                                        'object_name' => $course_detail->fullname,
                                        'object_type' => 'course',
                                        'parent_id' => '',
                                        'parent_name' => '',
                                        'start_date' => $course_detail->startdate,
                                        'end_date' => $course_detail->enddate,
                                        'code' => '',
                                        'room' => $course_detail->course_place,
                                        'grade' => '',
                                    );
                                    $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                                }
                            }

                            if ($send == 1) {
                                //send mail can not continue if has fake email

                                $startdate = strlen($itemNotif->startdate) != 0 && $itemNotif->startdate != 0 ? date('Y jS F g:iA', $itemNotif->startdate) : 'N/A';
                                $enddate = strlen($itemNotif->enddate) != 0 && $itemNotif->enddate != 0  ? date('Y jS F g:iA', $itemNotif->enddate) : 'N/A';

                                Mail::to($email)->send(new CourseSendMail(
                                    $itemNotif->target,
                                    $itemNotif->username,
                                    $fullname, //user full name
                                    $itemNotif->shortname, // course code
                                    $itemNotif->fullname, //course name
                                    $startdate,
                                    $enddate,
                                    $itemNotif->course_place,
                                    $itemNotif->date_quiz,
                                    $quiz_data
                                ));
                            }
                            $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                \DB::commit();
            }
        }
    }

//Send email completed competency framework
    public function sendCompetencyCompleted()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::COMPLETED_FRAME] == TmsConfigs::ENABLE) {
            $lstCompletedFrameNotifi = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::COMPLETED_FRAME)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->join('tms_traninning_programs', 'tms_nofitications.content', '=', 'tms_traninning_programs.id')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username',

                    'tms_traninning_programs.id as training_id',
                    'tms_traninning_programs.name as training_name',
                    'tms_traninning_programs.code as training_code',
                    'tms_traninning_programs.time_start',
                    'tms_traninning_programs.time_end'


                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotification = count($lstCompletedFrameNotifi);

            if ($countRemindNotification > 0) {
                \DB::beginTransaction();
                foreach ($lstCompletedFrameNotifi as $itemNotification) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotification->lastname . ' ' . $itemNotification->firstname;
                        $email = $itemNotification->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {

                            $training = json_encode(
                                array(
                                    'training_id' => $itemNotification->training_id,
                                    'training_name' => $itemNotification->training_name,
                                    'training_code' => $itemNotification->training_code,
                                    'time_start' => $itemNotification->time_start,
                                    'time_end' => $itemNotification->time_end
                                )
                            );

                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::COMPLETED_FRAME,
                                $itemNotification->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $training,
                                '',
                                '',
                                $itemNotification->training_name,
                                '',
                                '',
                                $itemNotification->training_code
                            ));

                            $object_content = array(
                                'object_id' => $itemNotification->training_id,
                                'object_name' => $itemNotification->training_name,
                                'object_type' => 'training',
                                'parent_id' => '',
                                'parent_name' => '',
                                'start_date' => $itemNotification->time_start,
                                'end_date' => $itemNotification->time_end,
                                'code' => $itemNotification->training_code,
                                'room' => '',
                                'grade' => '',
                            );

                            $itemNotification->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotification, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                //$this->sendPushNotification($lstCompletedFrameNotifi);
                \DB::commit();
            }
        }
    }

//Send email enrol competency framework
//Send 1 time only, then change status of notification
    public function sendEnrolCompetency()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::ASSIGNED_COMPETENCY] == TmsConfigs::ENABLE) {
            $lstCompletedFrameNotifi = MdlUser::query()
                ->join('tms_user_detail', 'tms_user_detail.user_id', '=', 'mdl_user.id')
                ->join('tms_nofitications', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->join('tms_traninning_programs', 'tms_nofitications.content', '=', 'tms_traninning_programs.id')
                ->where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::ASSIGNED_COMPETENCY)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',
                    'mdl_user.email',
                    'tms_user_detail.fullname',
                    'mdl_user.username',

                    'tms_traninning_programs.id as training_id',
                    'tms_traninning_programs.name as training_name',
                    'tms_traninning_programs.code as training_code',
                    'tms_traninning_programs.time_start',
                    'tms_traninning_programs.time_end'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotification = count($lstCompletedFrameNotifi);

            if ($countRemindNotification > 0) {
                \DB::beginTransaction();
                foreach ($lstCompletedFrameNotifi as $itemNotification) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotification->fullname;
                        $email = $itemNotification->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {

                            $training = json_encode(
                                array(
                                    'training_id' => $itemNotification->training_id,
                                    'training_name' => $itemNotification->training_name,
                                    'training_code' => $itemNotification->training_code,
                                    'time_start' => $itemNotification->time_start,
                                    'time_end' => $itemNotification->time_end
                                )
                            );

                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::ASSIGNED_COMPETENCY,
                                $itemNotification->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $training,
                                '',
                                '',
                                $itemNotification->training_name,
                                '',
                                '',
                                $itemNotification->training_code
                            ));
                            $object_content = array(
                                'object_id' => $itemNotification->training_id,
                                'object_name' => $itemNotification->training_name,
                                'object_type' => 'training',
                                'parent_id' => '',
                                'parent_name' => '',
                                'start_date' => $itemNotification->time_start,
                                'end_date' => $itemNotification->time_end,
                                'code' => $itemNotification->training_code,
                                'room' => '',
                                'grade' => '',
                            );
                            $itemNotification->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotification, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                //$this->sendPushNotification($lstCompletedFrameNotifi);
                \DB::commit();
            }
        }
    }

//Send email about exam fail for to student
//Send email request more attempt to uppers(For T&D, Manager, Leader of trainee)

//Send 1 time only, then change status of notification
    public function sendRequestMoreAttempt()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REQUEST_MORE_ATTEMPT] == TmsConfigs::ENABLE) {
            $lstNotifi = TmsNotification::query()
                ->join('tms_user_detail', 'tms_user_detail.user_id', '=', 'tms_nofitications.sendto')
                ->join('mdl_course', 'mdl_course.id', '=', 'tms_nofitications.course_id')
                ->where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::REQUEST_MORE_ATTEMPT)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.content',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',

                    'tms_user_detail.email',
                    'tms_user_detail.fullname',
                    'mdl_course.shortname as course_code',
                    'mdl_course.fullname as course_name'

                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotification = count($lstNotifi);

            if ($countRemindNotification > 0) {
                \DB::beginTransaction();
                foreach ($lstNotifi as $itemNotification) {

                    try {
                        //send mail can not continue if has fake email
                        $user_id = $itemNotification->sendto;
                        $fullname = $itemNotification->fullname;
                        $email = $itemNotification->email;

                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {

                            $content = json_decode($itemNotification->content);

                            //Thông báo fail exam cho student
                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::FAIL_EXAM,
                                $itemNotification->username,
                                $fullname,
                                $itemNotification->course_code,
                                $itemNotification->course_name,
                                '',
                                '',
                                '',
                                '',
                                $itemNotification->content
                            ));
                            //Ghi log
                            $object_content = array(
                                'object_id' => $user_id,
                                'object_name' => $fullname,
                                'object_type' => 'fail_exam',
                                'parent_id' => '',
                                'parent_name' => $itemNotification->course_name,
                                'start_date' => '',
                                'end_date' => ' ',
                                'code' => '',
                                'room' => '',
                                'grade' => '',
                                'attempt' => $content->attempt
                            );

                            //chỉ lưu log cho email thông báo fail của học viên
                            $tms_notifLog = new \stdClass();
                            $tms_notifLog->type = $itemNotification->type;
                            $tms_notifLog->target = TmsNotification::FAIL_EXAM;
                            $tms_notifLog->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $tms_notifLog->sendto = $itemNotification->sendto;
                            $tms_notifLog->status_send = 1;
                            $tms_notifLog->createdby = $itemNotification->createdby;
                            $tms_notifLog->course_id = $itemNotification->course_id;
                            $this->insert_single_notification_log($tms_notifLog, \App\TmsNotificationLog::UPDATE_STATUS_NOTIF, $tms_notifLog->content);

                            //T&D employee = admins, send only
                            $admins =  DB::table('model_has_roles as mhr')
                                ->join('roles', 'roles.id', '=', 'mhr.role_id')
                                ->leftJoin('permission_slug_role as psr', 'psr.role_id', '=', 'mhr.role_id')
                                ->join('mdl_user as mu', 'mu.id', '=', 'mhr.model_id')
                                ->join('tms_user_detail as tud', 'mu.id', '=', 'tud.user_id')
                                ->where('mhr.model_type', 'App/MdlUser')
                                ->where('mhr.model_id', '<>', 2)
                                ->where(function ($q) {
                                    $q->where('roles.name', 'root')->orWhere('psr.permission_slug', 'tms-system-administrator-grant');
                                })
                                ->select('mhr.model_id', 'mu.username', 'tud.fullname')
                                ->groupBy(['mhr.model_id', 'mu.username', 'tud.fullname'])
                                ->get();
                            $sent = array();
                            foreach ($admins as $admin) {
                                $sent[] = $admin->model_id;
                                $admin_email = $admin->username;
                                if (strlen($admin_email) != 0 && filter_var($admin_email, FILTER_VALIDATE_EMAIL) && $this->filterMail($admin_email)) {
                                    Mail::to($admin_email)->send(new CourseSendMail(
                                        TmsNotification::REQUEST_MORE_ATTEMPT,
                                        $admin->username,
                                        $admin->fullname,
                                        $itemNotification->course_code,
                                        $itemNotification->course_name,
                                        '',
                                        '',
                                        '',
                                        '',
                                        $itemNotification
                                    ));
                                }
                            }

                            //org uppers, send only
                            $orgUppers = self::orgUppers($user_id);
                            if (!empty($orgUppers)) {
                                foreach ($orgUppers as $orgUpper) {
                                    $upper_email = $orgUpper->email;
                                    if (!in_array($orgUpper->user_id, $sent) && strlen($upper_email) != 0 && filter_var($upper_email, FILTER_VALIDATE_EMAIL) && $this->filterMail($upper_email)) {
                                        Mail::to($upper_email)->send(new CourseSendMail(
                                            TmsNotification::REQUEST_MORE_ATTEMPT,
                                            $orgUpper->email,
                                            $orgUpper->fullname,
                                            $itemNotification->course_code,
                                            $itemNotification->course_name,
                                            '',
                                            '',
                                            '',
                                            '',
                                            $itemNotification
                                        ));
                                    }
                                }
                            }

                            $new_content = json_decode($itemNotification->content);
                            $new_content->parent_id = $itemNotification->id;
                            $itemNotification->content = json_encode($new_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotification, \App\TmsNotification::SENT);
                        } else {
                            $new_content = json_decode($itemNotification->content);
                            $new_content->parent_id = $itemNotification->id;
                            $itemNotification->content = json_encode($new_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotification, \App\TmsNotification::SEND_FAILED);
                    }
                    sleep(1);
                }
                //$this->sendPushNotification($lstNotifi);
                \DB::commit();
            }
        }
    }

//Send 1 time only, then change status of notification
    public function sendRequestMoreAttemptDemo()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REQUEST_MORE_ATTEMPT] == TmsConfigs::ENABLE) {
            $lstNotifi = TmsNotification::query()
                ->join('tms_user_detail', 'tms_user_detail.user_id', '=', 'tms_nofitications.sendto')
                ->join('mdl_course', 'mdl_course.id', '=', 'tms_nofitications.course_id')
                ->where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::REQUEST_MORE_ATTEMPT)
                //->where('status_send', \App\TmsNotification::UN_SENT)
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.content',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',

                    'tms_user_detail.email',
                    'tms_user_detail.fullname',
                    'mdl_course.shortname as course_code',
                    'mdl_course.fullname as course_name'

                )
                ->limit(1)
                ->orderBy('id', 'desc')
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotification = count($lstNotifi);


            if ($countRemindNotification > 0) {
                \DB::beginTransaction();
                foreach ($lstNotifi as $itemNotification) {

                    try {
                        //send mail can not continue if has fake email
                        $user_id = $itemNotification->sendto;
                        //T&D employee = admins, send only
                        $admins =  DB::table('model_has_roles as mhr')
                            ->join('roles', 'roles.id', '=', 'mhr.role_id')
                            ->leftJoin('permission_slug_role as psr', 'psr.role_id', '=', 'mhr.role_id')
                            ->join('mdl_user as mu', 'mu.id', '=', 'mhr.model_id')
                            ->join('tms_user_detail as tud', 'mu.id', '=', 'tud.user_id')
                            ->where('mhr.model_type', 'App/MdlUser')
                            ->where('mhr.model_id', '<>', 2)
                            ->where(function ($q) {
                                $q->where('roles.name', 'root')->orWhere('psr.permission_slug', 'tms-system-administrator-grant');
                            })
                            ->select('mhr.model_id', 'mu.username', 'tud.fullname')
                            ->groupBy(['mhr.model_id', 'mu.username', 'tud.fullname'])
                            ->get();
                        $cc = false;
                        foreach ($admins as $admin) {
                            $content_to_admins = new CourseSendMail(
                                TmsNotification::REQUEST_MORE_ATTEMPT,
                                $admin->username,
                                'Admins',
                                $itemNotification->course_code,
                                $itemNotification->course_name,
                                '',
                                '',
                                '',
                                '',
                                $itemNotification
                            );
                            if (!$cc) {
                                self::cc($content_to_admins);
                                $cc = true;
                            }
                        }

                        $cc_again = false;
                        //org uppers, send only
                        $orgUppers = self::orgUppers($user_id);
                        if (!empty($orgUppers)) {
                            foreach ($orgUppers as $orgUpper) {
                                $content_to_uppers = new CourseSendMail(
                                    TmsNotification::REQUEST_MORE_ATTEMPT,
                                    $orgUpper->email,
                                    'Leaders',
                                    $itemNotification->course_code,
                                    $itemNotification->course_name,
                                    '',
                                    '',
                                    '',
                                    '',
                                    $itemNotification
                                );
                                if (!$cc_again) {
                                    self::cc($content_to_uppers);
                                    $cc_again = true;
                                }
                            }
                        }
                        echo "Successfully";
                    } catch (Exception $e) {
                        dd($e->getMessage());
                    }
                    sleep(1);
                }
                \DB::commit();
            }
        }
    }

//Insert notification to manager to adding more attempt for fail student //Demo only
    public function insertRequestMoreAttempt() {

        //Redis::set('name', 'Taylor');
        //$user = Redis::get('name');
        //echo $user;

        $user_id = 23696;
        $course_id = 389;

        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REQUEST_MORE_ATTEMPT] == TmsConfigs::ENABLE) {
            $user = TmsUserDetail::query()->where('user_id', $user_id)->first();
            if (isset($user)) {
                $course = MdlCourse::query()->where('id', $course_id)->first();
                if (isset($course)) {
                    //Tạo 1 notification only
                    $data = array();
                    $element = array(
                        'type' => TmsNotification::MAIL,
                        'target' => TmsNotification::REQUEST_MORE_ATTEMPT,
                        'status_send' => 0,
                        'sendto' => $user_id,
                        'createdby' => 0,
                        'course_id' => $course_id,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                    );

                    //{"quiz_id":"254",
                    //"quiz_name":"Listening Test",
                    //"parent_name":"test u0111iem",
                    //"object_name":"Listening Test",
                    //"grade":0,
                    //"end_date":"2020-09-11 11:49:46",
                    //"attempt":1057,
                    //"object_id":"23908",
                    //"object_type":"request_more_attempt"}
                    $element['content'] = json_encode(array(
                        'object_id' => $user_id,
                        'object_name' => $user->fullname,
                        'object_type' => 'request_more_attempt',
                        'attempt' => 999,
                        'quiz_id' => 185,
                        'module_id' => 988

                    ), JSON_UNESCAPED_UNICODE);
                    $data[] = $element;
                    TmsNotification::insert($data);

                    //Tạo nhiều notifications cho uppers
                    /*
                    $orgUppers = self::orgUppers($user_id);
                    if (!empty($orgUppers)) {
                        $data = array();
                        foreach ($orgUppers as $orgUpper) {
                            if (strlen($orgUpper->email) != 0) {
                                $element = array(
                                    'type' => TmsNotification::MAIL,
                                    'target' => TmsNotification::REQUEST_MORE_ATTEMPT,
                                    'status_send' => 0,
                                    'sendto' => $orgUpper->user_id,
                                    'createdby' => 0,
                                    'course_id' => $course_id,
                                    'created_at' => date('Y-m-d H:i:s', time()),
                                    'updated_at' => date('Y-m-d H:i:s', time()),
                                );
                                $element['content'] = array(
                                    'object_id' => $user_id,
                                    'object_name' => $user->fullname,
                                    'object_type' => 'request_more_attempt',
                                    'attempt' => '999'
                                );
                                $data[] = $element;
                            }
                        }
                        if (!empty($data)) {
                            $convert_to_json = array();
                            foreach ($data as $item) { //auto strip key of element, just use value = necessary data
                                $item['content'] = json_encode($item['content'], JSON_UNESCAPED_UNICODE);
                                $convert_to_json[] = $item;
                            }
                            //batch insert
                            TmsNotification::insert($convert_to_json);
                        }
                    }*/


                }
            }
        }
    }

//get managers, leaders of user
    /**
     * @param $user_id
     * @return array
     */
    function orgUppers($user_id)
    {
        return DB::table('tms_organization_employee as toe')
            ->join('tms_user_detail as tud', 'toe.user_id', '=', 'tud.user_id')
            ->whereIn('toe.organization_id', function ($query) use ($user_id) {
                //check exist in table tms_nofitications
                $query->select('organization_id')->from('tms_organization_employee')->where('user_id', '=', $user_id);
            })
            ->whereIn('toe.position', [
                //TmsOrganizationEmployee::POSITION_MANAGER,
                TmsOrganizationEmployee::POSITION_LEADER
            ])
            ->select('tud.email', 'tud.fullname', 'tud.user_id')
            ->get()
            ->toArray();
    }

//Send email remind certificate
//Notification record created by Tho
//Checked ok 2020 March 24
//Type one time
//Add limit 23/4
    public function sendRemindCertificate()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_CERTIFICATE] == TmsConfigs::ENABLE) {
            $lstRemindExpireNotif = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::REMIND_CERTIFICATE)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->leftJoin('tms_device', 'mdl_user.id', '=', 'tms_device.user_id')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'tms_device.token',
                    'tms_device.type as device_type',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotif = count($lstRemindExpireNotif);

            if ($countRemindNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstRemindExpireNotif as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        $email = $itemNotif->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::REMIND_CERTIFICATE,
                                $itemNotif->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $itemNotif->content
                            ));

                            $decoded_content = json_decode($itemNotif->content);

                            $object_content = array(
                                'object_id' => '',
                                'object_name' => $decoded_content->competency_framework,
                                'object_type' => 'training',
                                'parent_id' => '',
                                'parent_name' => '',
                                'start_date' => '',
                                'end_date' => '',
                                'code' => $decoded_content->certificate_code,
                                'room' => '',
                                'grade' => '',
                            );
                            $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                //$this->sendPushNotification($lstRemindExpireNotif);
                \DB::commit();
            }
        }
    }

//Insert notification records to db for suggest soft skill courses
//Checked March 25, 2020
//Limit
    public function insertSuggestSoftSkillCourses()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::SUGGEST] == TmsConfigs::ENABLE) {
            $userNeedEnrol = DB::table('model_has_roles')
                ->join('mdl_user', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select(
                    'mdl_user.id',
                    'mdl_user.username',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'roles.name as rolename',
                    'mdl_user.email'
                )
                //neu user da dang khoa hoc ki nang mem thi bo qua
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('mdl_user_enrolments')
                        ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                        ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                        ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                        ->where('mdl_course_categories.id', '=', 4)// khoa hoc ky nang mem
                        ->whereRaw('mdl_user_enrolments.userid = mdl_user.id');
                })
                //Là học viên
                ->where('roles.id', '=', 5)
                //check not exist in table tms_nofitications
                ->whereNotIn('mdl_user.id', function ($query) {
                    $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::SUGGEST);
                });

            $userNeedEnrol = $userNeedEnrol->where('roles.id', '=', 5)
                //Giới hạn số lượng bản ghi
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->orderBy('mdl_user.id', 'desc')
                ->get();

            if (count($userNeedEnrol) > 0) {
                //batch insert
                $data = array();
                foreach ($userNeedEnrol as $user_item) {
                    $data[] = array(
                        'type' => TmsNotification::MAIL,
                        'target' => TmsNotification::SUGGEST,
                        'status_send' => 0,
                        'sendto' => $user_item->id,
                        'createdby' => 0,
                        'course_id' => 0,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    );
                }
                if (!empty($data)) {
                    TmsNotification::insert($data);
                }
            }
        }
    }

//Insert notification records to db for suggest optional courses (các khóa được phân quyền dữ liệu cho tổ chức mà user chưa enrol)
//Created September 10, 2020
//Limit
    public function insertSuggestOptionalCourses()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::SUGGEST] == TmsConfigs::ENABLE) {
            $userNeedEnrol = DB::table('tms_organization')
                //Lấy nhân viên
                ->join('tms_organization_employee', 'tms_organization.id', '=', 'tms_organization_employee.organization_id')
                ->join('tms_user_detail', 'tms_organization_employee.user_id', '=', 'tms_user_detail.user_id')
                //Lấy khóa được phân quyền dữ liệu
                ->join('tms_role_organization', 'tms_organization.id', '=', 'tms_role_organization.organization_id')
                ->join('tms_role_course', 'tms_role_organization.role_id', '=', 'tms_role_course.role_id')
                ->join('mdl_course', 'mdl_course.id', '=', 'tms_role_course.course_id')
                //User được enrol as student
                ->leftjoin('mdl_enrol', function ($join) {
                    $join->on('mdl_course.id', '=', 'mdl_enrol.courseid');
                    $join->where('mdl_enrol.roleid', '=', Role::ROLE_STUDENT);
                })
                ->leftJoin('mdl_user_enrolments', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                //Lấy thông tin user
                ->select(
                    'tms_user_detail.user_id',
                    'tms_user_detail.fullname',
                    'tms_user_detail.email'
                )
                //check not exist in table tms_nofitications
                ->whereNotIn('tms_user_detail.user_id', function ($query) {
                    $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::SUGGEST);
                })
                //Null tức là chưa được enrol
                ->whereNull('mdl_user_enrolments.userid')
                //Ngoại trừ thư viện
                ->where('mdl_course.category', '<>', 2)
                //Ngoại trừ khóa bị xóa
                ->where('mdl_course.deleted', '=', 0)
                //Loại bỏ bản ghi trùng
                ->groupBy(['tms_user_detail.user_id'])
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get();
            //Khóa học phân quyền dữ liệu - các khóa đã enrol => Ra những khóa thư viện và những khóa bị xóa => Có cần thiết

            if (count($userNeedEnrol) > 0) {
                //batch insert
                $data = array();
                foreach ($userNeedEnrol as $user_item) {
                    $data[] = array(
                        'type' => TmsNotification::MAIL,
                        'target' => TmsNotification::SUGGEST,
                        'status_send' => 0,
                        'sendto' => $user_item->user_id,
                        'createdby' => 0,
                        'course_id' => 0,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    );
                }
                if (!empty($data)) {
                    TmsNotification::insert($data);
                }
            }
        }
    }

//Remove suggest soft skill courses notification for enrolled student
//Xóa notify cho học viên đã tham gia rồi
//Checked March 26, 2020
    public function removeSuggestSoftSkillCourses()
    {
        TmsNotification::whereIn('sendto', function ($query) {
            $query->select('mdl_user_enrolments.userid')
                ->from('mdl_user_enrolments')
                ->join('mdl_user', 'mdl_user.id', '=', 'mdl_user_enrolments.userid')
                ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                ->where('mdl_course_categories.id', '=', 4);// khoa hoc ky nang mem
        })
            ->where('target', '=', TmsNotification::SUGGEST)
            ->delete();
    }

//Send email suggest soft skill courses
//Update: Add limit and dynamic x days
//Limit
//Chưa gửi hoặc đã gửi lần cuối cách đây > x ngày
//Checked March 27, 2020
//Type repeat
    public function sendSuggestSoftSkillCourses()
    {
        $configs = self::loadConfiguration();
        $schedule = 3; //send again after x days
        if ($configs[TmsNotification::SUGGEST] == TmsConfigs::ENABLE) {
            //$courses = MdlCourse::all()->where('category', "=", 4)->random(rand(3, 5));
            $courses = MdlCourse::query()->where('category', "=", 4)->orderByRaw('RAND()')->get();
            $countCourse = count($courses);
            if ($countCourse > 0) {
                $curentDate = time();
                $checkDate = date('Y-m-d H:i:s', strtotime('-' . $schedule . ' days', $curentDate));
                $lstNotif = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                    ->where('tms_nofitications.target', \App\TmsNotification::SUGGEST)
                    ->where(function ($q) use ($checkDate) {
                        $q->where('tms_nofitications.updated_at', '<', $checkDate)
                            ->orWhere('status_send', \App\TmsNotification::UN_SENT);
                    })
                    ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                    ->select(
                        'tms_nofitications.id',
                        'tms_nofitications.target',
                        'tms_nofitications.course_id',
                        'tms_nofitications.type',
                        'tms_nofitications.sendto',
                        'tms_nofitications.createdby',

                        'mdl_user.email',
                        'mdl_user.firstname',
                        'mdl_user.lastname',
                        'mdl_user.username'
                    )
                    ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                    ->get(); //lay danh sach cac thong bao chua gui
                $countNotif = count($lstNotif);
                if ($countNotif > 0) {
                    \DB::beginTransaction();
                    foreach ($lstNotif as $itemNotif) {
                        try {
                            //send mail can not continue if has fake email
                            $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                            $email = $itemNotif->email;
                            //Check email format
                            if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                                Mail::to($email)->send(new CourseSendMail(
                                    TmsNotification::SUGGEST,
                                    $itemNotif->username,
                                    $fullname,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $courses
                                ));
                                $object_content = [];
                                foreach ($courses as $course_detail) {
                                    $object_content[] = array(
                                        'object_id' => $course_detail->id,
                                        'object_name' => $course_detail->fullname,
                                        'object_type' => 'course',
                                        'parent_id' => '',
                                        'parent_name' => '',
                                        'start_date' => $course_detail->startdate,
                                        'end_date' => $course_detail->enddate,
                                        'code' => $course_detail->shortname,
                                        'room' => $course_detail->course_place,
                                        'grade' => '',
                                    );
                                }
                                $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                                $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                            } else {
                                $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                            }
                        } catch (Exception $e) {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    }
                    \DB::commit();
                }
            }
        }
    }

//Send email suggest optional courses
//Limit
//Chưa gửi

    public function sendSuggestOptionalCourses()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::SUGGEST] == TmsConfigs::ENABLE) {
            $lstNotif = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::SUGGEST)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui


            $countNotif = count($lstNotif);
            if ($countNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstNotif as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $user_id = $itemNotif->sendto;
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        $email = $itemNotif->email;
                        //Check email format
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {

                            //Get list optional course
                            $courses = DB::table('tms_organization')
                                //Lấy nhân viên
                                ->join('tms_organization_employee', 'tms_organization.id', '=', 'tms_organization_employee.organization_id')
                                ->join('tms_user_detail', 'tms_organization_employee.user_id', '=', 'tms_user_detail.user_id')
                                //Lấy khóa được phân quyền dữ liệu
                                ->join('tms_role_organization', 'tms_organization.id', '=', 'tms_role_organization.organization_id')
                                ->join('tms_role_course', 'tms_role_organization.role_id', '=', 'tms_role_course.role_id')
                                ->join('mdl_course', 'mdl_course.id', '=', 'tms_role_course.course_id')
                                //User được enrol as student
                                ->leftjoin('mdl_enrol', function ($join) {
                                    $join->on('mdl_course.id', '=', 'mdl_enrol.courseid');
                                    $join->where('mdl_enrol.roleid', '=', Role::ROLE_STUDENT);
                                })
                                ->leftJoin('mdl_user_enrolments', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                                //Lấy thông tin user
                                ->select('mdl_course.*')
                                //Null tức là chưa được enrol
                                ->whereNull('mdl_user_enrolments.userid')
                                //cho user hiện tại
                                ->where('tms_user_detail.user_id', $user_id)
                                //Ngoại trừ thư viện
                                ->where('mdl_course.category', '<>', 2)
                                //Ngoại trừ khóa bị xóa
                                ->where('mdl_course.deleted', '=', 0)
                                //Lấy 5 khóa
                                ->limit(5)
                                //Mới nhất
                                ->orderBy('mdl_course.id', 'desc')
                                ->get();

                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::SUGGEST,
                                $itemNotif->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $courses
                            ));

                            $object_content = [];
                            foreach ($courses as $course_detail) {
                                $object_content[] = array(
                                    'object_id' => $course_detail->id,
                                    'object_name' => $course_detail->fullname,
                                    'object_type' => 'course',
                                    'parent_id' => '',
                                    'parent_name' => '',
                                    'start_date' => $course_detail->startdate,
                                    'end_date' => $course_detail->enddate,
                                    'code' => $course_detail->shortname,
                                    'room' => $course_detail->course_place,
                                    'grade' => '',
                                );
                            }
                            $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                            sleep(1);
                        } else {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                    }
                }
                \DB::commit();
            }

        }
    }

//Remove reminds notification type repeat
//Every week => month
    public function removeAllRemind()
    {
        TmsNotification::where('target', '=', TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE)->delete();
        TmsNotification::where('target', '=', TmsNotification::REMIND_EDUCATION_SCHEDULE)->delete();
        TmsNotification::where('target', '=', TmsNotification::REMIND_LOGIN)->delete();
        TmsNotification::where('target', '=', TmsNotification::REMIND_UPCOMING_COURSE)->delete();
        TmsNotification::where('target', '=', TmsNotification::REMIND_ACCESS_COURSE)->delete();
        //2020 September 09, Thêm loại này do đổi query sang optional course và dùng chung template suggest
        TmsNotification::where('target', '=', TmsNotification::SUGGEST)->delete();
    }

//Insert notification remind expired required courses
//Category = 3 required courses
//check course completion of user mdl_course_completions.timecompleted
//Checked March 25, 2020
//Limit
//Type repeat
    public function insertRemindExpireRequiredCourses()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE] == TmsConfigs::ENABLE) {

            $next_3_days = time() + 86400 * 3;

            //Type 2
//            $subQuery = MdlUser::query()
//                //Check không có trong bảng notification
//                ->whereNotIn('id', function ($query) {
//                    //check exist in table tms_nofitications
//                    $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE);
//                })
//                ->limit(self::DEFAULT_ITEMS_PER_SESSION);


            $userNeedRemindExpired =
                //Type 1 limit using sub query wit same condition
                DB::query()->fromSub(function ($query) use ($next_3_days) {
                    $query->from('mdl_user')
                        ->whereNotIn('id', function ($query) {
                            //check exist in table tms_nofitications
                            $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE);
                        })
                        ->whereIn('id', function ($query) use ($next_3_days) {
                            $query->select('userid')
                                ->from('mdl_course_completions')
                                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course')
                                ->where('mdl_course.category', 3)
                                ->where('mdl_course.enddate', "<", $next_3_days)
                                ->whereNull('mdl_course_completions.timecompleted');
                        })
                        ->limit(self::DEFAULT_ITEMS_PER_SESSION);
                }, 'mdl_user')

                    //Type 2 limit using subquery
                    //DB::table(DB::raw("(({$subQuery->toSql()}) mdl_user)"))
                    //->mergeBindings($subQuery->getQuery())

                    //Type old: query all
                    //MdlUser::query()
                    ->whereNull('mdl_course_completions.timecompleted')
                    ->where('mdl_course.category', 3)
                    //->where('mdl_course.enddate', "<", $next_3_days)
                    //Check không có trong bảng notification
                    ->whereNotIn('mdl_user.id', function ($query) {
                        //check exist in table tms_nofitications
                        $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE);
                    })
                    ->join('mdl_course_completions', 'mdl_user.id', '=', 'mdl_course_completions.userid')
                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course')
                    ->select(
                        'mdl_user.id',
                        'mdl_user.username',
                        'mdl_user.firstname',
                        'mdl_user.lastname',
                        'mdl_user.email',
                        'mdl_course_completions.course',
                        'mdl_course_completions.timeenrolled',
                        'mdl_course_completions.timecompleted',
                        'mdl_course.shortname',
                        'mdl_course.fullname',
                        'mdl_course.startdate',
                        'mdl_course.enddate',
                        'mdl_course.course_place',
                        'mdl_course.category'
                    )
                    ->get();
            if (count($userNeedRemindExpired) > 0) {
                $data = array();
                foreach ($userNeedRemindExpired as $user_item) {
                    if (strlen($user_item->email) != 0) {
                        if (!array_key_exists($user_item->username, $data)) {
                            $element = array(
                                'type' => TmsNotification::MAIL,
                                'target' => TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE,
                                'status_send' => 0,
                                'sendto' => $user_item->id,
                                'createdby' => 0,
                                'course_id' => 0,
                                'created_at' => date('Y-m-d H:i:s', time()),
                                'updated_at' => date('Y-m-d H:i:s', time()),
                            );
                            $element['content'] = array(
                                array(
                                    'course_id' => $user_item->course,
                                    'course_code' => $user_item->shortname,
                                    'course_name' => $user_item->fullname,
                                    'startdate' => $user_item->startdate,
                                    'enddate' => $user_item->enddate,
                                    'course_place' => $user_item->course_place,
                                )
                            );
                            $data[$user_item->username] = $element;
                        } else { // user exists in array, just update content element
                            $data[$user_item->username]['content'][] = array(
                                'course_id' => $user_item->course,
                                'course_code' => $user_item->shortname,
                                'course_name' => $user_item->fullname,
                                'startdate' => $user_item->startdate,
                                'enddate' => $user_item->enddate,
                                'course_place' => $user_item->course_place,
                            );
                        }
                    }
                }

                if (!empty($data)) {
                    $convert_to_json = array();
                    foreach ($data as $item) { //auto strip key of element, just use value = necessary data
                        $item['content'] = json_encode($item['content'], JSON_UNESCAPED_UNICODE);
                        $convert_to_json[] = $item;
                    }
                    //batch insert
                    TmsNotification::insert($convert_to_json);
                }
            }
        }
    }

//Send email remind user about expire course
//Every minute
    public function sendRemindExpireRequiredCourses()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE] == TmsConfigs::ENABLE) {
            $lstRemindExpireNotif = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotif = count($lstRemindExpireNotif);

            if ($countRemindNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstRemindExpireNotif as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        $email = $itemNotif->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE,
                                $itemNotif->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $itemNotif->content
                            ));
                            $courses = json_decode($itemNotif->content, true);
                            $object_content = [];
                            foreach ($courses as $course_detail) {
                                $object_content[] = array(
                                    'object_id' => $course_detail['course_id'],
                                    'object_name' => $course_detail['course_name'],
                                    'object_type' => 'course',
                                    'parent_id' => '',
                                    'parent_name' => '',
                                    'start_date' => $course_detail['startdate'],
                                    'end_date' => $course_detail['enddate'],
                                    'code' => $course_detail['course_code'],
                                    'room' => $course_detail['course_place'],
                                    'grade' => '',
                                );
                            }
                            $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                \DB::commit();
            }
        }
    }

//Insert notification for education schedule - lộ trình đào tạo
//Check courses required category = 3
//Check user completions for those course
//last check March 31, 2020
    public function insertRemindEducationSchedule()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_EDUCATION_SCHEDULE] == TmsConfigs::ENABLE) {
            $now = time();
            $userNeedPush = DB::query()->fromSub(function ($query) use ($now) {
                $query->from('mdl_user')
                    ->whereNotIn('id', function ($query) {
                        //check exist in table tms_nofitications
                        $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_EDUCATION_SCHEDULE);
                    })
                    ->whereIn('id', function ($query) use ($now) {
                        $query->select('mu.id')
                            ->from('mdl_user as mu')
                            ->join('model_has_roles', 'mu.id', '=', 'model_has_roles.model_id')
                            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                            ->leftJoin('mdl_course_completions', 'mu.id', '=', 'mdl_course_completions.userid') //Left join vì gửi tất cả kể cả chưa enrol vì là khóa học bắt buộc
                            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course')
                            ->where("mdl_course.category", 3) //khóa bắt buộc
                            ->whereNull('mdl_course_completions.timecompleted') //Chưa hoàn thành khóa học
                            ->where('roles.id', '=', 5) //là học viên
                            ->where('mdl_course.startdate', '<', $now); //Khóa học đã diễn ra
                    })
                    ->limit(self::DEFAULT_ITEMS_PER_SESSION);
            }, 'mdl_user')
                //MdlUser::query()
                ->where("mdl_course.category", 3)
                ->whereNull('mdl_course_completions.timecompleted')
                ->where('roles.id', '=', 5)
                ->where('mdl_course.startdate', '<', $now)
                ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->leftJoin('mdl_course_completions', 'mdl_user.id', '=', 'mdl_course_completions.userid')
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course')
                ->select(
                    'mdl_user.id',
                    'mdl_user.username',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'roles.name as rolename',
                    'mdl_user.email',
                    'mdl_course_completions.course',
                    'mdl_course_completions.timeenrolled',
                    'mdl_course_completions.timecompleted',
                    'mdl_course.shortname',
                    'mdl_course.fullname',
                    'mdl_course.startdate',
                    'mdl_course.enddate',
                    'mdl_course.course_place',
                    'mdl_course.category'
                )
                ->get();

            if (count($userNeedPush) > 0) {
                $data = array();
                foreach ($userNeedPush as $user_item) {
                    if (strlen($user_item->email) != 0) {
                        if (!array_key_exists($user_item->username, $data)) {
                            $element = array(
                                'type' => TmsNotification::MAIL,
                                'target' => TmsNotification::REMIND_EDUCATION_SCHEDULE,
                                'status_send' => 0,
                                'sendto' => $user_item->id,
                                'createdby' => 0,
                                'course_id' => 0,
                                'created_at' => date('Y-m-d H:i:s', time()),
                                'updated_at' => date('Y-m-d H:i:s', time()),
                            );
                            $element['content'] = array(
                                array(
                                    'course_id' => $user_item->course,
                                    'course_code' => $user_item->shortname,
                                    'course_name' => $user_item->fullname,
                                    'startdate' => $user_item->startdate,
                                    'enddate' => $user_item->enddate,
                                    'course_place' => $user_item->course_place,
                                )
                            );
                            $data[$user_item->username] = $element;
                        } else { // user exists in array, just update content element
                            $data[$user_item->username]['content'][] = array(
                                'course_id' => $user_item->course,
                                'course_code' => $user_item->shortname,
                                'course_name' => $user_item->fullname,
                                'startdate' => $user_item->startdate,
                                'enddate' => $user_item->enddate,
                                'course_place' => $user_item->course_place,
                            );
                        }
                    }
                }

                if (!empty($data)) {
                    $convert_to_json = array();
                    foreach ($data as $item) { //auto strip key of element, just use value = necessary data
                        $item['content'] = json_encode($item['content'], JSON_UNESCAPED_UNICODE);
                        $convert_to_json[] = $item;
                    }
                    //batch insert
                    TmsNotification::insert($convert_to_json);
                }
            }
        }
    }

//Send email remind user about education schedule - lộ trình đào tạo
//Every minute
//Last check March 31, 2020: add limit
    public function sendRemindEducationSchedule()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_EDUCATION_SCHEDULE] == TmsConfigs::ENABLE) {
            $lstRemindExpireSchedule = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::REMIND_EDUCATION_SCHEDULE)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindSchedule = count($lstRemindExpireSchedule);

            if ($countRemindSchedule > 0) {
                \DB::beginTransaction();
                foreach ($lstRemindExpireSchedule as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        $email = $itemNotif->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::REMIND_EDUCATION_SCHEDULE,
                                $itemNotif->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $itemNotif->content
                            ));
                            $courses = json_decode($itemNotif->content, true);
                            $object_content = [];
                            foreach ($courses as $course_detail) {
                                $object_content[] = array(
                                    'object_id' => $course_detail['course_id'],
                                    'object_name' => $course_detail['course_name'],
                                    'object_type' => 'course',
                                    'parent_id' => '',
                                    'parent_name' => '',
                                    'start_date' => $course_detail['startdate'],
                                    'end_date' => $course_detail['enddate'],
                                    'code' => $course_detail['course_code'],
                                    'room' => $course_detail['course_place'],
                                    'grade' => '',
                                );
                            }
                            $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                \DB::commit();
            }
        }
    }

//Insert notification records to db for remind access course activity
//check course completion of user => get info and remind about those course
//Add limit
//Type repeat
//last check April 1, 2020
    public function insertRemindLogin()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_LOGIN] == TmsConfigs::ENABLE) {

            $check_login_duration = time() - 86400 * 7; //Prev 7 days

            $userNeedRemindLogin = DB::table('mdl_user')
                ->whereNotIn('mdl_user.id', function ($query) {
                    //check exist in table tms_nofitications
                    $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_LOGIN);
                })
                ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where(DB::raw('GREATEST(lastlogin, currentlogin)'), '<', $check_login_duration)
                ->where(function ($q) {
                    $q->where('lastlogin', '<>', 0)
                        ->orWhere('currentlogin', '<>', 0);
                })
                ->where('roles.id', '=', 5)//Role hoc vien
                ->whereExists(function ($query) { // User da dang ki khoa hoc
                    $query->select(DB::raw(1))
                        ->from('mdl_user_enrolments')
                        ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                        ->whereRaw('mdl_user_enrolments.userid = mdl_user.id');
                })
                ->select(
                    'mdl_user.id',
                    'username',
                    'firstname',
                    'lastname',
                    'email',
                    DB::raw('GREATEST(lastlogin, currentlogin) AS truelastlogin')
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get();

            if (count($userNeedRemindLogin) > 0) {
                $data = array();
                foreach ($userNeedRemindLogin as $user_item) {
                    if (strlen($user_item->email) != 0) {
                        $data[] = array(
                            'type' => TmsNotification::MAIL,
                            'target' => TmsNotification::REMIND_LOGIN,
                            'status_send' => 0,
                            'sendto' => $user_item->id,
                            'createdby' => 0,
                            'course_id' => 0,
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'updated_at' => date('Y-m-d H:i:s', time()),
                        );
                    }
                }
                if (!empty($data)) {
                    //batch insert
                    TmsNotification::insert($data);
                }
            }
        }
    }

//Send email remind login
//Add limit, check exist
//last check April 1, 2020
    public function sendRemindLogin()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_LOGIN] == TmsConfigs::ENABLE) {
            $listRemindLoginNotification = TmsNotification::where('status_send', \App\TmsNotification::UN_SENT)
                ->where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('target', TmsNotification::REMIND_LOGIN)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->leftJoin('mdl_course', 'mdl_course.id', '=', 'tms_nofitications.course_id')
                ->leftJoin('tms_device', 'mdl_user.id', '=', 'tms_device.user_id')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'tms_device.token',
                    'tms_device.type as device_type',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get();

            if (count($listRemindLoginNotification) != 0) {
                \DB::beginTransaction();
                foreach ($listRemindLoginNotification as $itemNotif) {
                    try {
                        $email = $itemNotif->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                            $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                            Mail::to($email)->send(new CourseSendMail(
                                $itemNotif->target,
                                $itemNotif->username,
                                $fullname
                            ));
                            $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                //$this->sendPushNotification($listRemindLoginNotification);
                \DB::commit();
            }
        }
    }

//Insert notification records to db for upcoming courses
//mdl_user: all student / role = 5
//Add limit, check exist
//last check April 1, 2020
    public function insertRemindUpcomingCourses()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_UPCOMING_COURSE] == TmsConfigs::ENABLE) {
            $userNeedRemindUpcoming = MdlUser::where('roles.id', '=', 5)//Role hoc vien
            ->join('model_has_roles', 'mdl_user.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->whereNotIn('mdl_user.id', function ($query) {
                    //check exist in table tms_nofitications
                    $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_UPCOMING_COURSE);
                })
                ->select(
                    'mdl_user.id',
                    'username',
                    'firstname',
                    'lastname',
                    'email'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get();

            if (count($userNeedRemindUpcoming) > 0) {
                $data = array();
                foreach ($userNeedRemindUpcoming as $user_item) {
                    if (strlen($user_item->email) != 0) {
                        $data[] = array(
                            'type' => TmsNotification::MAIL,
                            'target' => TmsNotification::REMIND_UPCOMING_COURSE,
                            'status_send' => 0,
                            'sendto' => $user_item->id,
                            'createdby' => 0,
                            'course_id' => 0,
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'updated_at' => date('Y-m-d H:i:s', time()),
                        );
                    }
                }
                if (!empty($data)) {
                    TmsNotification::insert($data);
                }
            }
        }
    }

//Send email remind upcoming courses
//Add status unsent
//Add limit
    public function sendRemindUpcomingCourses()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_UPCOMING_COURSE] == TmsConfigs::ENABLE) {
            $next_3_days = time() + 86400 * 3;
            $next_7_days = time() + 86400 * 7;
            $courses = MdlCourse::whereIn('category', [3, 4, 5])
                ->where('startdate', '>', $next_3_days)
                ->where('enddate', '<', $next_7_days)
                ->get();
            $countCourse = count($courses);

            if ($countCourse > 0) {
                $lstUpcomingNotif = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                    ->where('status_send', \App\TmsNotification::UN_SENT)
                    ->where('tms_nofitications.target', \App\TmsNotification::REMIND_UPCOMING_COURSE)
                    ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                    ->leftJoin('tms_device', 'mdl_user.id', '=', 'tms_device.user_id')
                    ->select(
                        'tms_nofitications.id',
                        'tms_nofitications.target',
                        'tms_nofitications.course_id',
                        'tms_nofitications.type',
                        'tms_nofitications.sendto',
                        'tms_nofitications.createdby',

                        'tms_device.token',
                        'tms_device.type as device_type',

                        'mdl_user.email',
                        'mdl_user.firstname',
                        'mdl_user.lastname',
                        'mdl_user.username'
                    )
                    ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                    ->get(); //lay danh sach cac thong bao chua gui

                if (count($lstUpcomingNotif) > 0) {
                    \DB::beginTransaction();
                    foreach ($lstUpcomingNotif as $itemNotif) {
                        try {
                            //send mail can not continue if has fake email
                            $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                            $email = $itemNotif->email;
                            if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                                Mail::to($email)->send(new CourseSendMail(
                                    TmsNotification::REMIND_UPCOMING_COURSE,
                                    $itemNotif->username,
                                    $fullname,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $courses
                                ));
                                $object_content = [];
                                foreach ($courses as $course_detail) {
                                    $object_content[] = array(
                                        'object_id' => $course_detail->id,
                                        'object_name' => $course_detail->fullname,
                                        'object_type' => 'course',
                                        'parent_id' => '',
                                        'parent_name' => '',
                                        'start_date' => $course_detail->startdate,
                                        'end_date' => $course_detail->enddate,
                                        'code' => $course_detail->shortname,
                                        'room' => $course_detail->course_place,
                                        'grade' => '',
                                    );
                                }
                                $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                                $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                            } else {
                                $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                            }
                        } catch (Exception $e) {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                        //(1);
                    }
                    //$this->sendPushNotification($lstUpcomingNotif);
                    \DB::commit();
                }
            }
        }
    }

//Insert notification records to db for remind access course activity
//check course completion of user => get info and remind about those course
//last check April 1, 2020
//Add limit by mdl_user left table
    public function insertRemindAccess()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_ACCESS_COURSE] == TmsConfigs::ENABLE) {
            $prev_3_days = time() - 86400 * 3;
            $userNeedRemind =
                DB::query()->fromSub(function ($query) use ($prev_3_days) {
                    $query->from('mdl_user')
                        ->whereNotIn('id', function ($query) {
                            //check exist in table tms_nofitications
                            $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_ACCESS_COURSE);
                        })
                        ->whereIn('id', function ($query) use ($prev_3_days) {
                            $query->select('mdl_course_completions.userid')
                                ->from('mdl_course_completions')
                                ->leftjoin('mdl_user_lastaccess', function ($join) {
                                    $join->on('mdl_course_completions.course', '=', 'mdl_user_lastaccess.courseid');
                                    $join->on('mdl_course_completions.userid', '=', 'mdl_user_lastaccess.userid');
                                })
                                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course') //khoa hoc ton tai moi co du lieu
                                ->where(function ($q) use ($prev_3_days) {
                                    $q->where('mdl_user_lastaccess.timeaccess', '<', $prev_3_days)->orWhereNull('mdl_user_lastaccess.timeaccess');
                                })
                                ->whereNull('mdl_course_completions.timecompleted');
                        })
                        ->limit(self::DEFAULT_ITEMS_PER_SESSION);
                }, 'mdl_user')
                    //MdlUser::query()
                    ->whereNull('mdl_course_completions.timecompleted')
                    ->whereNotIn('mdl_user.id', function ($query) {
                        //check exist in table tms_nofitications
                        $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::REMIND_ACCESS_COURSE);
                    })
                    ->where(function ($q) use ($prev_3_days) {
                        $q->where('mdl_user_lastaccess.timeaccess', '<', $prev_3_days)->orWhereNull('mdl_user_lastaccess.timeaccess');
                    })
                    ->join('mdl_course_completions', 'mdl_user.id', '=', 'mdl_course_completions.userid')
                    ->leftjoin('mdl_user_lastaccess', function ($join) {
                        $join->on('mdl_course_completions.course', '=', 'mdl_user_lastaccess.courseid');
                        $join->on('mdl_course_completions.userid', '=', 'mdl_user_lastaccess.userid');
                    })
                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course')//khoa hoc ton tai moi co du lieu
                    ->select(
                        'mdl_course_completions.userid',

                        'mdl_user.username',
                        'mdl_user.firstname',
                        'mdl_user.lastname',
                        'mdl_user.email',

                        'mdl_course_completions.course',
                        'mdl_course_completions.timecompleted',
                        'mdl_user_lastaccess.timeaccess',

                        'mdl_course.shortname',
                        'mdl_course.fullname'
                    )
                    ->get();


            if (count($userNeedRemind) > 0) {
                $data = array();
                foreach ($userNeedRemind as $user_item) {
                    if (strlen($user_item->email) != 0) {
                        if (!array_key_exists($user_item->username, $data)) {
                            $element = array(
                                'type' => TmsNotification::MAIL,
                                'target' => TmsNotification::REMIND_ACCESS_COURSE,
                                'status_send' => 0,
                                'sendto' => $user_item->userid,
                                'createdby' => 0,
                                'course_id' => 0,
                                'created_at' => date('Y-m-d H:i:s', time()),
                                'updated_at' => date('Y-m-d H:i:s', time()),
                            );
                            $element['content'] = array(
                                array(
                                    'course_id' => $user_item->course,
                                    'course_code' => $user_item->shortname,
                                    'course_name' => $user_item->fullname,
                                )
                            );
                            $data[$user_item->username] = $element;
                        } else { // user exists in array, just update content element
                            $data[$user_item->username]['content'][] = array(
                                'course_id' => $user_item->course,
                                'course_code' => $user_item->shortname,
                                'course_name' => $user_item->fullname,
                            );
                        }
                    }
                }
                if (!empty($data)) {
                    $convert_to_json = array();
                    foreach ($data as $item) { //auto strip key of element, just use value = necessary data
                        $item['content'] = json_encode($item['content'], JSON_UNESCAPED_UNICODE);
                        $convert_to_json[] = $item;
                    }
                    //batch insert
                    TmsNotification::insert($convert_to_json);
                }
            }
        }
    }

//Send email remind user to access course
//Add limit
    public function sendRemindAccess()
    {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_ACCESS_COURSE] == TmsConfigs::ENABLE) {
            $lstRemindNotif = TmsNotification::where('tms_nofitications.type', \App\TmsNotification::MAIL)
                ->where('tms_nofitications.target', \App\TmsNotification::REMIND_ACCESS_COURSE)
                ->where('status_send', \App\TmsNotification::UN_SENT)
                ->join('mdl_user', 'mdl_user.id', '=', 'tms_nofitications.sendto')
                ->select(
                    'tms_nofitications.id',
                    'tms_nofitications.target',
                    'tms_nofitications.course_id',
                    'tms_nofitications.type',
                    'tms_nofitications.sendto',
                    'tms_nofitications.createdby',
                    'tms_nofitications.content',

                    'mdl_user.email',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.username'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotif = count($lstRemindNotif);

            if ($countRemindNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstRemindNotif as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        $email = $itemNotif->email;
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->filterMail($email)) {
                            Mail::to($email)->send(new CourseSendMail(
                                TmsNotification::REMIND_ACCESS_COURSE,
                                $itemNotif->username,
                                $fullname,
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $itemNotif->content
                            ));
                            $courses = json_decode($itemNotif->content, true);
                            $object_content = [];
                            foreach ($courses as $course_detail) {
                                $object_content[] = array(
                                    'object_id' => $course_detail['course_id'],
                                    'object_name' => $course_detail['course_name'],
                                    'object_type' => 'course',
                                    'parent_id' => '',
                                    'parent_name' => '',
                                    'start_date' => '',
                                    'end_date' => '',
                                    'code' => $course_detail['course_code'],
                                    'room' => '',
                                    'grade' => '',
                                );
                            }
                            $itemNotif->content = json_encode($object_content, JSON_UNESCAPED_UNICODE);
                            $this->update_notification($itemNotif, \App\TmsNotification::SENT);
                        } else {
                            $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                        }
                    } catch (Exception $e) {
                        $this->update_notification($itemNotif, \App\TmsNotification::SEND_FAILED);
                    }
                    //sleep(1);
                }
                \DB::commit();
            }
        }
    }

    function update_notification($tmsNotif, $status_send, $write_log = true)
    {
        $tms_notifLog = \App\TmsNotification::findOrFail($tmsNotif->id);
        $tms_notifLog->type = $tmsNotif->type;
        $tms_notifLog->target = $tmsNotif->target;
        $tms_notifLog->sendto = $tmsNotif->sendto;
        $tms_notifLog->status_send = $status_send;
        $tms_notifLog->createdby = $tmsNotif->createdby;
        $tms_notifLog->course_id = $tmsNotif->course_id;
        $tms_notifLog->updated_at = date('Y-m-d H:i:s', time());
        $tms_notifLog->save();
        if ($write_log) {
            $this->insert_single_notification_log($tms_notifLog, \App\TmsNotificationLog::UPDATE_STATUS_NOTIF, $tmsNotif->content);
        }
    }

    function insert_single_notification_log($tmsNotif, $action, $content)
    { //action bao gồm create, update, delete lấy trong bảng TmsNotificationLog
        $tms_notifLog = new \App\TmsNotificationLog();
        $tms_notifLog->type = $tmsNotif->type;
        $tms_notifLog->target = $tmsNotif->target;
        $tms_notifLog->content = $content;
        $tms_notifLog->sendto = $tmsNotif->sendto;
        $tms_notifLog->status_send = $tmsNotif->status_send;
        $tms_notifLog->createdby = $tmsNotif->createdby;
        $tms_notifLog->course_id = $tmsNotif->course_id;
        $tms_notifLog->action = $action;
        $tms_notifLog->save();

        if (strlen($tmsNotif->sendto) != 0 && $tmsNotif->sendto != 0) {
            TmsUserDetail::where('user_id', $tmsNotif->sendto)->update(['total_unread_msg' => DB::raw('total_unread_msg+1')]);
        }
    }

    /**
     * @param $lstUpcomingNotif array
     */
    function sendPushNotification($lstUpcomingNotif)
    {
        $android_device_tokens = array();
        $ios_device_tokens = array();
        foreach ($lstUpcomingNotif as $itemNotif) {
            if (strlen($itemNotif->token) != 0) {
                if ($itemNotif->device_type == TmsDevice::TYPE_ANDROID) {
                    $android_device_tokens[] = $itemNotif->token;
                }
                if ($itemNotif->device_type == TmsDevice::TYPE_IOS) {
                    $ios_device_tokens[] = $itemNotif->token;
                }
            }
        }
        $params = [
            'title' => 'BGT Elearning',
            'link' => 'http://bgt.tinhvan.com'
        ];
        if (!empty($android_device_tokens)) {
            sendPushNotification("Một số khóa học sắp bắt đầu", 'android', $android_device_tokens, $params);
        }
        if (!empty($ios_device_tokens)) {
            sendPushNotification("Một số khóa học sắp bắt đầu", 'ios', $ios_device_tokens, $params);
        }
    }

    function filterMail($email)
    {
//        if (!EmailChecker::check($email)) {
//            return false;
//        } else {
//            //continue
//        }
        //Cache::flush();
        $mail_development_mode = true; //Default true to avoid spam mail
        //Check development_flag
        if (Cache::has('mail_development_mode')) {
            $flag = Cache::get('mail_development_mode');
            $mail_development_mode = $flag;
        } else {
            $getDevelopment = TmsConfigs::where('target', '=', TmsConfigs::DEVELOPMENT)->first();
            //Set development_flag
            if (isset($getDevelopment)) {
                if ($getDevelopment->content = 'enable') {
                    $mail_development_mode = true;
                    Cache::put('mail_development_mode', true, 1440);
                } else {//Only this case development mode is turn off
                    $mail_development_mode = false;
                }
            }
        }

        //Nếu không có thì đang trong chế độ nhà phát triển
        if ($mail_development_mode) {
            $dev_email = [
                'immrhy@gmail.com',
                'innrhy@gmail.com',
                'fruity.tester@gmail.com',
                'linhnt@tinhvan.com',
                'nguyenlinhcksl@gmail.com',
                'leduytho93@gmail.com',
                'duongtiendat.it@gmail.com'
            ];
            $tester_email = [
                'dieuly@easia-travel.com',
                'nguyenthao@easia-travel.com',
                'lanphuong@easia-travel.com',
                'haison@easia-travel.com',
                'quenguyen@easia-travel.com',
                'thuylinh@easia-travel.com',
                'quanghuy@easia-travel.com',
                'thaontp@easia-travel.com',
                'hongtham@easia-travel.com',
                'maihuong@easia-travel.com',
                'minhtrang@easia-travel.com',
                'nguyentrang@easia-travel.com',
                'ngocanh@easia-travel.com',
                'myhanh@easia-travel.com',
                'adam@easia-travel.com',
                'wailin@easia-travel.com',
                'j.renault@easia-travel.com',
                'celia@easia-travel.com',
                'suingun@easia-travel.com',
                'yadanar@easia-travel.com',
                'matt@easia-travel.com',
                'diemmy@easia-travel.com',
                'myvan@easia-travel.com',
                'hoaithu@easia-travel.com',
                'ngocthang@easia-travel.com',
                'nguyenthuphuong@easia-travel.com',
                'thuananh@easia-travel.com',
                'soulikone@easia-travel.com',
                'boun@easia-travel.com',
                'rany@easia-travel.com',
                'sreymom@easia-travel.com',
                'sokharat@easia-travel.com',
                'jessica@easia-travel.com',
                'bandit@easia-travel.com',
                'bram@easia-travel.com',
                'pornpawee@easia-travel.com',
                'suvaree@easia-travel.com',
                'anong@easia-travel.com',
                'suthi@easia-travel.com',
                'nadi@easia-travel.com',
                'zaryi@easia-travel.com',
                'thomas@easia-travel.com',
                'pichet@easia-travel.com',
                'nathalie@easia-travel.com',
                'sichan@easia-travel.com',
                'minhphuc@easia-travel.com',
                'tuphuong@easia-travel.com',
                'minhhoa@easia-travel.com',
                'baothu@easia-travel.com',
                'phuocdoan@easia-travel.com',
                'myvan@easia-travel.com',
                'ngochien@easia-travel.com',
                'quockhanh@easia-travel.com',
            ];
            $filter_email = array_merge($dev_email, $tester_email);
            if (in_array($email, $filter_email)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    function cc($content) {
        $to = [
            //'quenguyen@easia-travel.com',
            //'quenguyen@begodi.com',
            'leduytho93@gmail.com'
        ];
        foreach ($to as $item) {
            Mail::to($item)->send($content);
        }
    }
}
