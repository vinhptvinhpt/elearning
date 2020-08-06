<?php

namespace App\Mail;

use App\TmsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseSendMail extends Mailable
{
    use Queueable, SerializesModels;


    const FULLNAME = "[FULLNAME]";
    const USERNAME = "[USERNAME]";
    const COURSECODE = "[COURSECODE]";
    const COURSENAME = "[COURSENAME]";
    const STARTDATE = "[STARTDATE]";
    const ENDDATE = "[ENDDATE]";
    const COURSEPLACE = "[COURSEPLACE]";
    // const QUIZDATE = "[QUIZDATE]";
    const CONTENT = "[CONTENT]";
    const CONTENTNAME = "[CONTENTNAME]";
    const COURSELIST = "[COURSELIST]";
    const QUIZNAME = "[QUIZNAME]";
    const QUIZSTART = "[QUIZSTART]";
    const QUIZEND = "[QUIZEND]";
    const QUIZPOINT = "[QUIZPOINT]";
    const NEWPASS = "[NEWPASS]";
    const ACCEPT_INVITE_URL = "[ACCEPT_INVITE_URL]";
    const URL_CONFIRM_EMAIL = "[URL_CONFIRM_EMAIL]";
    const PASSWORD = "[PASSWORD]";

    //
    const EMAIL = "[EMAIL]";
    const COMPETENCYNAME = "[COMPETENCYNAME]";
    const COURSELEARNINGOUTCOMES = "[COURSELEARNINGOUTCOMES]";
    const LANGUAGE = "[LANGUAGE]";
    const TRAINER = "[TRAINER]";
    const STARTTIME = "[STARTTIME]";
    const ENDTIME = "[ENDTIME]";
    const DATE = "[DATE]";
    const FIRST = "[FIRST]";
    const DEADLINE = "[DEADLINE]";


    private $activity;
    private $fullname;
    private $username;
    private $course_code;
    private $course_name;
    private $start_date;
    private $end_date;
    private $course_place;
    private $quiz_date;
    private $content;
    private $course_list = [];
    private $url_confirm_email;
    private $password;

    /**
     * Create a new message instance.
     *
     * @param $activity
     * @param $username
     * @param $fullname
     * @param string $course_code
     * @param string $course_name
     * @param string $start_date
     * @param string $end_date
     * @param string $course_place
     * @param string $quiz_date
     * @param string $content
     * @param string $course_list
     *
     */
    public function __construct(
        $activity,
        $username,
        $fullname,
        $course_code = '',
        $course_name = '',
        $start_date = '',
        $end_date = '',
        $course_place = '',
        $quiz_date = '',
        $content = '',
        $course_list = '',
        $url_confirm_email = '',
        $competency_name = '',
        $quiz_end = '',
        $quiz_start = '',
        $password = '',
        $course_learning_outcomes = '',
        $language = '',
        $trainer = '',
        $start_time = '',
        $end_time = '',
        $date = '',
        $first = '',
        $deadline = ''
    )
    {
        $this->activity = $activity;
        $this->username = $username;
        $this->fullname = $fullname;
        $this->course_code = $course_code;
        $this->course_name = $course_name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->course_place = $course_place;
        $this->quiz_date = $quiz_date;
        $this->content = $content;
        $this->course_list = $course_list;
        $this->url_confirm_email = $url_confirm_email;
        $this->password = $password;
        //
        $this->competency_name = $competency_name;
        $this->quiz_start = $quiz_start;
        $this->quiz_end = $quiz_end;
        $this->course_learning_outcomes = $course_learning_outcomes;
        $this->language = $language;
        $this->trainer = $trainer;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->date = $date;
        $this->first = $first;
        $this->deadline = $deadline;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '';
        $view = '';
        if ($this->activity == TmsNotification::FORGOT_PASSWORD) {
            $subject = '[ELEARNING] '. __('forgot_password');
            $view = 'email.forgot_password';
        }
        elseif ($this->activity == TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE) {
            $course_array = json_decode($this->content);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] '. __('remind_expire_required_course');
            $view = 'email.remind_expire_required_course';
        }
        elseif($this->activity == TmsNotification::COMPLETED_FRAME){
            $course_array = json_decode($this->content);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] Thông báo xác nhận email';
            $view = 'email.completed_competency';
        }
        elseif ($this->activity == TmsNotification::ASSIGNED_COURSE) {
            $subject = '[ELEARNING] '. __('assigned_course');
            $view = 'email.assigned_course';
        }
        elseif ($this->activity == TmsNotification::ASSIGNED_COMPETENCY) {
            $subject = '[ELEARNING] '. __('assigned_competency');
            $view = 'email.assigned_competency';
        }
        elseif ($this->activity == TmsNotification::SUGGEST_OPTIONAL_COURSE) {
            $course_array = json_decode($this->course_list);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] '. __('suggest_optional_course');
            $view = 'email.suggest_optional_course';
        }
        elseif ($this->activity == TmsNotification::REMIND_EXAM) {
            $subject = '[ELEARNING] '. __('remind_exam');
            $view = 'email.remind_exam';
        }
        elseif ($this->activity == TmsNotification::INVITATION_OFFLINE_COURSE) {
            $subject = '[ELEARNING] '. __('invitation_offline_course');
            $view = 'email.invitation_offline_course';
        }elseif ($this->activity == TmsNotification::INVITE_STUDENT) {
            $subject = __('thu_moi_tham_gia_khoa_hoc');
            $view = 'email.invite_student';
        }elseif($this->activity == TmsNotification::ENROL){
            $subject = '[ELEARNING] '. __('thong_bao_hoc_vien_duoc_ghi_danh_vao_khoa_hoc');
            $view = 'email.enrol_course';
        }


        if (strlen($subject) != 0 AND strlen($view) != 0) {
            $this->subject($subject)
                ->with('fullname', $this->fullname)
                ->with('username', $this->username)
                ->with('course_code', $this->course_code)
                ->with('course_name', $this->course_name)
                ->with('start_date', $this->start_date)
                ->with('end_date', $this->end_date)
                ->with('course_place', $this->course_place)
                ->with('content', $this->content)
                ->with('quiz_date', $this->quiz_date)
                ->with('course_list', $this->course_list)
                ->with('url_confirm_email', $this->url_confirm_email)
                ->with('password', $this->password)
                ->with('competency_name', $this->competency_name)
                ->with('quiz_start', $this->quiz_start)
                ->with('quiz_end', $this->quiz_end)
                ->with('course_learning_outcomes', $this->course_learning_outcomes)
                ->with('language', $this->language)
                ->with('trainer', $this->trainer)
                ->with('start_time', $this->start_time)
                ->with('end_time', $this->end_time)
                ->with('date', $this->date)
                ->with('first', $this->first)
                ->with('deadline', $this->deadline)
                ->view($view);
        }
        return $this;
    }
}
