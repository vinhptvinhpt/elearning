<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmsNotification extends Model
{
    //declare constant type notification
    const MAIL = 'mail';
    const WEB = 'web';
    const PUSH_NOTIFICATION = 'push';

    //declare constant target log notification
    const COURSE = 'course';
    const USER = 'user';

    //declare constant activity
    const ENROL = 'enrol';
    const WELCOME = 'welcome';
    const SUGGEST = 'suggest';
    const QUIZ_START = 'quiz_start';
    const QUIZ_END = 'quiz_end';
    const QUIZ_COMPLETED = 'quiz_completed';
    const REMIND_LOGIN = 'remind_login';
    const REMIND_EXPIRE_REQUIRED_COURSE = 'remind_expire_required_course';
    const REMIND_EDUCATION_SCHEDULE = 'remind_education_schedule';
    const REMIND_ACCESS_COURSE = 'remind_access_course';
    const REMIND_UPCOMING_COURSE ='remind_upcoming_course';
    const REMIND_CERTIFICATE ='remind_certificate';
    const FORGOT_PASSWORD ='forgot_password';
    const INVITE_STUDENT ='invite_student';
    //declare constant target = active_email
    const ACTIVE_EMAIL = 'active_email';


    //declare status send mail
    const UN_SENT = 0; //chưa gửi
    const SENT = 1;  //gửi thành công
    const SEND_FAILED = 2; // gửi không thành công

    protected $table = 'tms_nofitications';
    protected $fillable = [
        'type', 'target', 'status_send', 'sendto', 'createdby', 'course_id', 'date_quiz', 'content'
    ];
}
