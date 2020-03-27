<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CourseSendMail;
use App\MdlCourse;
use App\MdlQuiz;
use App\MdlUser;
use App\TmsConfigs;
use App\TmsDevice;
use App\TmsInvitation;
use App\TmsNotification;
use App\TmsUserDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MailController extends Controller
{
    const DEFAULT_ITEMS_PER_SESSION = 200;

    /* Load / generate configuration */
    public function loadConfiguration()
    {
        $configs = array(
            TmsNotification::ENROL => TmsConfigs::ENABLE,
            TmsNotification::SUGGEST => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_START => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_END => TmsConfigs::ENABLE,
            TmsNotification::QUIZ_COMPLETED => TmsConfigs::ENABLE,
            TmsNotification::REMIND_LOGIN => TmsConfigs::ENABLE,
            TmsNotification::REMIND_ACCESS_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_EDUCATION_SCHEDULE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_UPCOMING_COURSE => TmsConfigs::ENABLE,
            TmsNotification::REMIND_CERTIFICATE => TmsConfigs::ENABLE,
            TmsNotification::INVITE_STUDENT => TmsConfigs::ENABLE
        );
        $pdo = DB::connection()->getPdo();
        if ($pdo) {
            $stored_configs = TmsConfigs::all();
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
    public function sendInvitation() {
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
                    $invite_id = $item->id;

                    //$email = "immrhy@gmail.com";

                    try {
                        //send mail can not continue if has fake email
                        if (strlen($email) != 0 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
                                $invite_id
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

    //Send email enrol, quiz_start, quiz_end, quiz_completed (has course data included in content)
    //Notification record created by VinhPT, Tho
    //Checked ok 2020 March 24
    //Type one time => change status, next time not send again
    public function sendEnrolQuizStartQuizEndQuizCompleted() {
        $configs = self::loadConfiguration();
        $turn_on = 0;
        $turn_on_simple = array();
        $turn_on_complex = array();
        if ($configs[TmsNotification::ENROL] == 'enable') {
            $turn_on_simple[] = TmsNotification::ENROL;
            $turn_on += 1;
        }
        if ($configs[TmsNotification::QUIZ_COMPLETED] == 'enable') {
            $turn_on_simple[] = TmsNotification::QUIZ_COMPLETED;
            $turn_on += 1;
        }
        if ($configs[TmsNotification::QUIZ_START] == 'enable') {
            $turn_on_simple[] = TmsNotification::QUIZ_START;
            $turn_on += 1;
        }
        if ($configs[TmsNotification::QUIZ_END] == 'enable') {
            $turn_on_complex[] = TmsNotification::QUIZ_END;
            $turn_on += 1;
        }

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
                ->get();

            $countNotif = count($lstNotif);

            if ($countNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstNotif as $itemNotif) {
                    try {
                        if (!empty($itemNotif->email) && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
                            $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                            $email = $itemNotif->email;
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
                                        'object_id' =>  $itemNotif->course_id,
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
                                Mail::to($email)->send(new CourseSendMail(
                                    $itemNotif->target,
                                    $itemNotif->username,
                                    $fullname, //user full name
                                    $itemNotif->shortname, // course code
                                    $itemNotif->fullname, //course name
                                    date('d/m/Y', strtotime($itemNotif->startdate)),
                                    date('d/m/Y', strtotime($itemNotif->enddate)),
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

    //Send email remind certificate
    //Notification record created by Tho
    //Checked ok 2020 March 24
    //Type one time => change status, next time not send again
    public function sendRemindCertificate() {
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
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotif = count($lstRemindExpireNotif);

            if ($countRemindNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstRemindExpireNotif as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        $email = $itemNotif->email;
                        if (strlen($email) != 0 && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
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
                            $object_content = array(
                                'object_id' =>  '',
                                'object_name' => '',
                                'object_type' => '',
                                'parent_id' => '',
                                'parent_name' => '',
                                'start_date' => '',
                                'end_date' => '',
                                'code' => $itemNotif->content,
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
                $this->sendPushNotification($lstRemindExpireNotif);
                \DB::commit();
            }
        }
    }

    //Insert notification records to db for suggest soft skill courses
    //Checked March 25, 2020
    //Add limit to avoid rows limit exceed
    //Type repeat
    public function insertSuggestSoftSkillCourses() {
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
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->orderBy('mdl_user.id', 'desc')->get();

            if (count($userNeedEnrol) > 0) {
                //batch insert
                $data = array();
                $user_ids_array = array();
                foreach ($userNeedEnrol as $user_item) {
                    $user_ids_array[] = $user_item->id;
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

    //Remove suggest soft skill courses notification for enrolled student
    public function removeSuggestSoftSkillCourses() {
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
    //Update: Add limit and dynamic days
    public function sendSuggestSoftSkillCourses() {
        $configs = self::loadConfiguration();
        $schedule = 3; //send again after x days
        if ($configs[TmsNotification::SUGGEST] == TmsConfigs::ENABLE) {
            $courses = MdlCourse::all()->where('category', "=", 4)->random(rand(3, 5));
            $countCourse = count($courses);
            if ($countCourse > 0) {
                $curentDate = time();
                $checkDate = date('Y-m-d H:i:s', strtotime('-'. $schedule .' days', $curentDate));
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
                            if (strlen($email) != 0 && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
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
                                        'object_id' =>  $course_detail->id,
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

    //Insert notification remind expired required courses
    //Category = 3 required courses
    //check course completion of user mdl_course_completions.timecompleted
    //Checked March 25, 2020
    //Add limit to avoid rows limit exceed, run again
    //Type one time
    public function insertRemindExpireRequiredCourses() {
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE] == TmsConfigs::ENABLE) {
            $next_3_days = time() + 86400 * 3;
            $userNeedRemindExpired = MdlUser::whereNull('mdl_course_completions.timecompleted')
                ->where('mdl_course.category', 3)
                ->where('mdl_course.enddate', "<", $next_3_days)
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
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
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

    //Remove remind expired required courses notification
    //Every weeks
    public function removeRemindExpireRequiredCourses() {
        TmsNotification::where('target', '=', TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE)->delete();
    }

    //Send email remind user about expire course
    public function sendRemindExpireRequiredCourses() {
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
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotif = count($lstRemindExpireNotif);

            if ($countRemindNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstRemindExpireNotif as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        $email = $itemNotif->email;
                        if (strlen($email) != 0 && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
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
                                    'object_id' =>  $course_detail['course_id'],
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

    //user not in notification, insert 1000 each
    public function insertRemindAccess() {
        set_time_limit(0);
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_ACCESS_COURSE] == TmsConfigs::ENABLE) {
            $prev_3_days = time() - 86400 * 3;

            //Limit 1000 by left table before join
            $userNeedRemind =
                DB::table(
                    //'mdl_user'
                    DB::raw('(select * from mdl_user limit ' . self::DEFAULT_ITEMS_PER_SESSION . ') mdl_user')
                )
                ->whereNull('mdl_course_completions.timecompleted')
                ->where(function ($q) use ($prev_3_days) {
                    $q->where('mdl_user_lastaccess.timeaccess', '<', $prev_3_days)->orWhereNull('mdl_user_lastaccess.timeaccess');
                })
                ->join('mdl_course_completions', 'mdl_user.id', '=', 'mdl_course_completions.userid')
                ->leftjoin('mdl_user_lastaccess', function ($join) {
                    $join->on('mdl_course_completions.course', '=', 'mdl_user_lastaccess.courseid');
                    $join->on('mdl_course_completions.userid', '=', 'mdl_user_lastaccess.userid');
                })
                //khoa hoc ton tai moi co du lieu
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course')
                //chua co trong bang notification
                ->whereNotIn('mdl_user.id', function ($query) {
                    $query->select('sendto')->from('tms_nofitications')
                        ->where('target', TmsNotification::REMIND_ACCESS_COURSE);
                })

                ->select(
                    'mdl_course_completions.userid',

                    'mdl_user.username',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.email',

                    'mdl_course_completions.course',
                    //'mdl_course_completions.timecompleted',
                    //'mdl_user_lastaccess.timeaccess',

                    'mdl_course.shortname',
                    'mdl_course.fullname'
                )
                ->get();

            dd($userNeedRemind);


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

                dd($data);
                //lấy x users để xử lý, 1000 is okay
                //$data = array_slice($data, 0, 1000);

                if (!empty($data)) {
                    $convert_to_json = array();
                    foreach ($data as $item) { //auto strip key of element, just use value = necessary data
                        $item['content'] = json_encode($item['content'], JSON_UNESCAPED_UNICODE);
                        $convert_to_json[] = $item;
                    }
                    TmsNotification::insert($convert_to_json);
                }
            }
            else {
                //Xoa notify cu
                TmsNotification::where('target', \App\TmsNotification::REMIND_ACCESS_COURSE)
                    ->delete();
            }
        }
    }

    //user in notification, and sent, update 1000 each
    public function updateRemindAccess() {
        set_time_limit(0);
        $configs = self::loadConfiguration();
        if ($configs[TmsNotification::REMIND_ACCESS_COURSE] == TmsConfigs::ENABLE) {
            $prev_3_days = time() - 86400 * 3;
            $userNeedRemind = DB::table('mdl_user')
                ->whereNull('mdl_course_completions.timecompleted')
                ->where(function ($q) use ($prev_3_days) {
                    $q->where('mdl_user_lastaccess.timeaccess', '<', $prev_3_days)->orWhereNull('mdl_user_lastaccess.timeaccess');
                })
                ->join('mdl_course_completions', 'mdl_user.id', '=', 'mdl_course_completions.userid')
                ->leftjoin('mdl_user_lastaccess', function ($join) {
                    $join->on('mdl_course_completions.course', '=', 'mdl_user_lastaccess.courseid');
                    $join->on('mdl_course_completions.userid', '=', 'mdl_user_lastaccess.userid');
                })
                //khoa hoc ton tai moi co du lieu
                ->join('mdl_course', 'mdl_course.id', '=', 'mdl_course_completions.course')
                //chua co trong bang notification
                ->whereNotIn('mdl_user.id', function ($query) {
                    $query->select('sendto')->from('tms_nofitications')
                        //->where('status_send', \App\TmsNotification::UN_SENT)
                        ->where('target', TmsNotification::REMIND_ACCESS_COURSE);
                })

                ->select(
                    'mdl_course_completions.userid',

                    'mdl_user.username',
                    'mdl_user.firstname',
                    'mdl_user.lastname',
                    'mdl_user.email',

                    'mdl_course_completions.course',
                    //'mdl_course_completions.timecompleted',
                    //'mdl_user_lastaccess.timeaccess',

                    'mdl_course.shortname',
                    'mdl_course.fullname'
                )
                ->limit(self::DEFAULT_ITEMS_PER_SESSION)
                ->get();

            echo count($userNeedRemind);die;

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

                //lấy x users để xử lý, 1000 is okay
                $data = array_slice($data, 0, 1000);

                dd($data);

                if (!empty($data)) {
                    $convert_to_json = array();
                    foreach ($data as $item) { //auto strip key of element, just use value = necessary data
                        $item['content'] = json_encode($item['content'], JSON_UNESCAPED_UNICODE);
                        $convert_to_json[] = $item;
                    }
                    TmsNotification::insert($convert_to_json);
                }
            }
            else {
                //Xoa notify cu
                TmsNotification::where('target', \App\TmsNotification::REMIND_ACCESS_COURSE)
                    ->delete();
            }
        }
    }

    //send remind to unsent user 1000 each
    public function sendRemindAccess() { //mondays()->wednesdays()->fridays()->at('09:00');
        //Send email remind user to access course
        set_time_limit(0);
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
                ->limit(1) //giới hạn số thông báo trong phiên, tránh overload
                ->get(); //lay danh sach cac thong bao chua gui

            $countRemindNotif = count($lstRemindNotif);

            if ($countRemindNotif > 0) {
                \DB::beginTransaction();
                foreach ($lstRemindNotif as $itemNotif) {
                    try {
                        //send mail can not continue if has fake email
                        $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                        //$email = $itemNotif->email;
                        $email = "innrhy@gmail.com";
                        if (strlen($email) != 0 && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
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
                                    'object_id' =>  $course_detail['course_id'],
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

    function update_notification($tmsNotif, $status_send)
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

        $this->insert_single_notification_log($tms_notifLog, \App\TmsNotificationLog::UPDATE_STATUS_NOTIF, $tmsNotif->content);
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
}
