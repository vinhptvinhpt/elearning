<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CourseSendMail;
use App\MdlCourse;
use App\MdlUser;
use App\TmsConfigs;
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
    public function inviteStudent() {
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

    //Send email suggest soft skill courses
    //Update: Add limit and dynamic days
    public function suggestSoftSkillCourses() {
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
}
