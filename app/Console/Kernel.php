<?php

namespace App\Console;

use App\CourseCompletion;
use App\CourseFinal;
use App\Mail\CourseSendMail;
use App\MdlCourse;
use App\MdlCourseCompletions;
use App\MdlEnrol;
use App\MdlGradeGrade;
use App\MdlGradeItem;
use App\MdlQuiz;
use App\MdlRoleAssignments;
use App\MdlUserEnrolments;
use App\TmsConfigs;
use App\TmsDevice;
use App\TmsNotification;
use App\TmsUserDetail;
use App\Role;
use App\ModelHasRole;
use App\MdlUser;
use Carbon\Carbon;
use App\TmsLog;
use function foo\func;
use Horde\Socket\Client\Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use mod_questionnaire\question\date;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * https://stackoverflow.com/questions/28866821/call-laravel-controller-via-command-line
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\CallRoute',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public $courseCurrent_id;

    protected function schedule(Schedule $schedule) {

        // $schedule->command('inspire')->hourly();

        /* Load / generate configuration */
        $configs = TmsConfigs::defaultNotificationConfig();
        $pdo = DB::connection()->getPdo();
        if($pdo) {
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

        //test schedule
        $schedule->call(function () use ($configs) {
            //test content
        })->everyMinute();

        //Send email enrol, quiz_start, quiz_end, quiz_completed (has course data included in content)
        //Notification record created by VinhPT, Tho
        //Checked ok 2020 March 24

        $schedule->call(function () use ($configs) {
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
        })->everyMinute();

        //Send email remind certificate
        //Checked ok 2020 March 24
        $schedule->call(function () use ($configs) {
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
        })->everyMinute();


        //Insert notification records to db for suggest soft skill courses
        $schedule->call(function () use ($configs) {
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
                        'mdl_user.email')
                    ->whereNotExists(function ($query) { // neu user da dang khoa hoc ki nang mem thi bo qua
                        $query->select(DB::raw(1))
                            ->from('mdl_user_enrolments')
                            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                            ->where('mdl_course_categories.id', '=', 4)// khoa hoc ky nang mem
                            ->whereRaw('mdl_user_enrolments.userid = mdl_user.id');
                    })
                    //check not exist in table tms_nofitications
                    ->whereNotIn('mdl_user.id', function ($query) {
                        $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::SUGGEST);
                    });
                $userNeedEnrol = $userNeedEnrol->where('roles.id', '=', 5);
                $userNeedEnrol = $userNeedEnrol->orderBy('mdl_user.id', 'desc')->get();
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
                        //xoa notification cho user da dang ki
                        TmsNotification::where('target', \App\TmsNotification::SUGGEST)
                            ->whereNotIn('sendto', $user_ids_array)
                            ->delete();
                    } else {
                        //xoa het notification neu k co user thoa man
                        TmsNotification::where('target', \App\TmsNotification::SUGGEST)
                            ->delete();
                    }
                }
            }
        })->everyMinute();

    }

    protected function scheduleX(Schedule $schedule) {

        // $schedule->command('inspire')->hourly();

        /* Load / generate configuration */
        $configs = TmsConfigs::defaultNotificationConfig();
        $pdo = DB::connection()->getPdo();
        if($pdo) {
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

        //test
        $schedule->call(function () use ($configs) {
            //test content
        })->everyMinute();


        //Insert notification records to db for suggest soft skill courses
        $schedule->call(function () use ($configs) {
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
                        'mdl_user.email')
                    ->whereNotExists(function ($query) { // neu user da dang khoa hoc ki nang mem thi bo qua
                        $query->select(DB::raw(1))
                            ->from('mdl_user_enrolments')
                            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                            ->join('mdl_course_categories', 'mdl_course_categories.id', '=', 'mdl_course.category')
                            ->where('mdl_course_categories.id', '=', 4)// khoa hoc ky nang mem
                            ->whereRaw('mdl_user_enrolments.userid = mdl_user.id');
                    })
                    //check not exist in table tms_nofitications
                    ->whereNotIn('mdl_user.id', function ($query) {
                        $query->select('sendto')->from('tms_nofitications')->where('target', '=', TmsNotification::SUGGEST);
                    });
                $userNeedEnrol = $userNeedEnrol->where('roles.id', '=', 5);
                $userNeedEnrol = $userNeedEnrol->orderBy('mdl_user.id', 'desc')->get();
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
                        //xoa notification cho user da dang ki
                        TmsNotification::where('target', \App\TmsNotification::SUGGEST)
                            ->whereNotIn('sendto', $user_ids_array)
                            ->delete();
                    } else {
                        //xoa het notification neu k co user thoa man
                        TmsNotification::where('target', \App\TmsNotification::SUGGEST)
                            ->delete();
                    }
                }
            }
        })->everyMinute();

        //Send email suggest soft skill courses
        $schedule->call(function () use ($configs) {
            if ($configs[TmsNotification::SUGGEST] == TmsConfigs::ENABLE) {
                $courses = MdlCourse::all()->where('category', "=", 4)->random(rand(3, 5));
                $countCourse = count($courses);
                if ($countCourse > 0) {
                    $curentDate = time();
                    $checkDate = date('Y-m-d H:i:s', strtotime('-3 days', $curentDate));
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
                        ->get(); //lay danh sach cac thong bao chua gui
                    $countNotif = count($lstNotif);
                    if ($countNotif > 0) {
                        \DB::beginTransaction();
                        foreach ($lstNotif as $itemNotif) {
                            try {
                                //send mail can not continue if has fake email
                                $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                                $email = $itemNotif->email;

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
                            //(1);
                        }
                        \DB::commit();
                    }
                }
            }

        })
            //->everyMinute();
            ->mondays()->wednesdays()->fridays()->at('11:00');

        //Insert notification remind expired required courses
        //Category = 3
        //check course completion of user mdl_course_completions.timecompleted
        $schedule->call(function () use ($configs) {
            if ($configs[TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE] == TmsConfigs::ENABLE) {
                $next_3_days = time() + 86400 * 3;
                $userNeedRemindExpired = MdlUser::whereNull('mdl_course_completions.timecompleted')
                    ->where('mdl_course.category', 3)
                    ->where('mdl_course.enddate', "<", $next_3_days)
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
                        //Xoa notify cu
                        TmsNotification::where('target', \App\TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE)
                            ->delete();
                        //batch insert
                        TmsNotification::insert($convert_to_json);
                    }
                } else {
                    //Xoa notify cu
                    TmsNotification::where('target', \App\TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE)
                        ->delete();
                }
            }
        })->everyMinute();

        //Send email remind user about expire course
        $schedule->call(function () use ($configs) {
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
        })->everyMinute();

        //Insert notification for education schedule
        //Check courses required category = 3
        //Check user completions for those course
        $schedule->call(function () use ($configs) {
            if ($configs[TmsNotification::REMIND_EDUCATION_SCHEDULE] == TmsConfigs::ENABLE) {
                $now = time();
                $userNeedPush = MdlUser::where("mdl_course.category", 3)
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
                    ->get(); // hoc vien
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
                        //Xoa notify cu
                        TmsNotification::where('target', \App\TmsNotification::REMIND_EDUCATION_SCHEDULE)
                            ->delete();
                        //batch insert
                        TmsNotification::insert($convert_to_json);
                    }
                } else {
                    //Xoa notify cu
                    TmsNotification::where('target', \App\TmsNotification::REMIND_EDUCATION_SCHEDULE)
                        ->delete();
                }
            }
        })->everyMinute();

        //Send email remind user about education schedule
        $schedule->call(function () use ($configs) {
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
                    ->get(); //lay danh sach cac thong bao chua gui

                $countRemindSchedule = count($lstRemindExpireSchedule);

                if ($countRemindSchedule > 0) {
                    \DB::beginTransaction();
                    foreach ($lstRemindExpireSchedule as $itemNotif) {
                        try {
                            //send mail can not continue if has fake email
                            $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                            $email = $itemNotif->email;
                            if (strlen($email) != 0 && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
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
        })->everyMinute();

        //Insert notification records to db for remind login  activity
        //mdl_user get greatest value of lastlogin & currentlogin
        $schedule->call(function () use ($configs) {
            if ($configs[TmsNotification::REMIND_LOGIN] == TmsConfigs::ENABLE) {
                $check_login_duration = time() - 86400 * 7;
                $userNeedRemindLogin = DB::table('mdl_user')
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
                        //Xoa notify cu
                        TmsNotification::where('target', \App\TmsNotification::REMIND_LOGIN)
                            ->delete();
                        //batch insert
                        TmsNotification::insert($data);
                    }
                } else {
                    //Xoa notify cu
                    TmsNotification::where('target', \App\TmsNotification::REMIND_LOGIN)
                        ->delete();
                }
            }
        })->everyMinute();

        //Send email remind login
        $schedule->call(function () use ($configs) {
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
                    ->get();

                if (count($listRemindLoginNotification) != 0) {
                    \DB::beginTransaction();
                    foreach ($listRemindLoginNotification as $itemNotif) {
                        try {
                            if (!empty($itemNotif->email) && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
                                $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                                $email = $itemNotif->email;
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
                    $this->sendPushNotification($listRemindLoginNotification);
                    \DB::commit();
                }
            }
        })->everyMinute();

        //Insert notification records to db for upcoming courses
        //mdl_user: all student / role = 5
        $schedule->call(function () use ($configs) {
            if ($configs[TmsNotification::REMIND_UPCOMING_COURSE] == TmsConfigs::ENABLE) {
                $userNeedRemindUpcoming = MdlUser::where('roles.id', '=', 5)//Role hoc vien
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
                        //xoa het notification neu k co user thoa man
                        TmsNotification::where('target', \App\TmsNotification::REMIND_UPCOMING_COURSE)
                            ->delete();
                        TmsNotification::insert($data);
                    }
                } else {
                    //Xoa notify cu neu k co user
                    TmsNotification::where('target', \App\TmsNotification::REMIND_UPCOMING_COURSE)
                        ->delete();
                }
            }

        })->everyMinute();

        //Send email remind upcoming courses
        $schedule->call(function () use ($configs) {
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
                        ->get(); //lay danh sach cac thong bao chua gui

                    if (count($lstUpcomingNotif) > 0) {
                        \DB::beginTransaction();
                        foreach ($lstUpcomingNotif as $itemNotif) {
                            try {
                                //send mail can not continue if has fake email
                                $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                                $email = $itemNotif->email;

                                if (strlen($email) != 0 && filter_var($itemNotif->email, FILTER_VALIDATE_EMAIL)) {
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
                            //(1);
                        }

                        $this->sendPushNotification($lstUpcomingNotif);

                        \DB::commit();
                    }
                }
            }
        })
            //->everyMinute();
            ->mondays()->wednesdays()->fridays()->at('10:00');
    }

    protected function scheduleMoved(Schedule $schedule) {
        // $schedule->command('inspire')->hourly();

        /* Load / generate configuration */
        $configs = TmsConfigs::defaultNotificationConfig();
        $pdo = DB::connection()->getPdo();
        if($pdo) {
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

        //Insert notification records to db for remind access course activity
        //check course completion of user => get info and remind about those course
        //Moved to mail controller
        $schedule->call(function () use ($configs) {
            if ($configs[TmsNotification::REMIND_ACCESS_COURSE] == TmsConfigs::ENABLE) {
                $prev_3_days = time() - 86400 * 3;
                $userNeedRemind = MdlUser::whereNull('mdl_course_completions.timecompleted')
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
                        //'mdl_course_completions.timecompleted',
                        //'mdl_user_lastaccess.timeaccess',

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
                        //Xoa notify cu
                        TmsNotification::where('target', \App\TmsNotification::REMIND_ACCESS_COURSE)
                            ->delete();
                        //batch insert
                        TmsNotification::insert($convert_to_json);
                    }
                } else {
                    //Xoa notify cu
                    TmsNotification::where('target', \App\TmsNotification::REMIND_ACCESS_COURSE)
                        ->delete();
                }
            }
        })->everyMinute();

        //Send email remind user to access course
        //Moved to mail controller
        $schedule->call(function () use ($configs) {
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
                    ->get(); //lay danh sach cac thong bao chua gui

                $countRemindNotif = count($lstRemindNotif);

                if ($countRemindNotif > 0) {
                    \DB::beginTransaction();
                    foreach ($lstRemindNotif as $itemNotif) {
                        try {
                            //send mail can not continue if has fake email
                            $fullname = $itemNotif->lastname . ' ' . $itemNotif->firstname;
                            $email = $itemNotif->email;
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
        })->mondays()->wednesdays()->fridays()->at('09:00');
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

    function checkCourseComplete($user_id)
    {
        $check = true;
        /*$completion_count = \App\MdlCourseCompletions::where('userid', $user_id)->whereIn('course', $this->certificate_course_id())->count();
        if ($completion_count == $this->certificate_course_number()) {
            $check = true;
        }*/

        $category = $this->certificate_course();
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
                where cs.section <> 0 and cmc.completionstate = 1 and cmc.userid = ' . $user_id . ' and cm.course = c.id)
                as user_course_completionstate')

                , DB::raw('(select count(cm.id) as course_learn from mdl_course_modules cm
                inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
                where cs.section <> 0 and cm.course = c.id) as user_course_learn')

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

    function certificate_course_number()
    {
        $course_count = DB::table('mdl_course')
            ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->where('category', $this->certificate_course())->count();
        return $course_count;
    }

    function certificate_course_id()
    {
        $course = DB::table('mdl_course')
            ->join('mdl_course_completion_criteria', 'mdl_course_completion_criteria.course', '=', 'mdl_course.id')
            ->where('category', $this->certificate_course())
            ->pluck('mdl_course.id');
        return $course;
    }

    function certificate_course()
    {
        return 3;
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    // protected function commands()
    // {
    //     $this->load(__DIR__.'/Commands');

    //     require base_path('routes/console.php');
    // }
}
