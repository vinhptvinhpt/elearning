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
    const REMIND_EDUCATION_SCHEDULE = 'remind_education_schedule';
    const REMIND_ACCESS_COURSE = 'remind_access_course';
    const REMIND_UPCOMING_COURSE ='remind_upcoming_course';
    const REMIND_CERTIFICATE ='remind_certificate';
    const INVITE_STUDENT ='invite_student';
    const REMIND_EXPIRE_REQUIRED_COURSE = 'remind_expire_required_course';
    const FORGOT_PASSWORD ='forgot_password';

    const ACTIVE_EMAIL = 'active_email';
    const COMPLETED_FRAME = 'completed_competency_framework';
    const REQUEST_MORE_ATTEMPT = 'request_more_attempt';
    const FAIL_EXAM = 'fail_exam';
    const ASSIGNED_COMPETENCY = 'assigned_competency';
    const REMIND_EXAM = 'remind_exam';
    const CALCULATE_TOEIC_GRADE = 'calculate_toeic_grade';
    const RETAKE_EXAM = 'retake_exam';

    const NOTICE_SPAM_EMAIL ='notice_spam_email';

    //declare status send mail
    const UN_SENT = 0; //chưa gửi
    const SENT = 1;  //gửi thành công
    const SEND_FAILED = 2; // gửi không thành công

    protected $table = 'tms_nofitications';
    protected $fillable = [
        'type', 'target', 'status_send', 'sendto', 'createdby', 'course_id', 'date_quiz', 'content'
    ];
}
