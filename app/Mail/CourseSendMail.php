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
    const QUIZDATE = "[QUIZDATE]";
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

    const EMAIL = "[EMAIL]";
    const COMPETENCYNAME = "[COMPETENCYNAME]";
    const CERTIFICATECODE = "[CERTIFICATECODE]";
    const COURSEDESCRIPTION = "[COURSEDESCRIPTION]";
    const STARTTIME = "[STARTTIME]";
    const ENDTIME = "[ENDTIME]";
    const COMPETENCYCODE = "[COMPETENCYCODE]";
    const LISTENING = "[LISTENING]";
    const READING = "[READING]";
    const TOTAL = "[TOTAL]";
    const STUDENT = "[STUDENT]";
    const LINK_TO_REVIEW = "[LINK_TO_REVIEW]";
    const EXAMNAME = "[EXAMNAME]";
    const LINK_TO_UNLOCK = "[LINK_TO_UNLOCK]";


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
    private $competency_name;
    private $quiz_start;
    private $quiz_end;
    private $course_description;
    private $certificate_code;
    private $competency_code;
    private $exam_name;

    /**
     * Create a new message instance.
     *
     * @param string $activity
     * @param string $username
     * @param string $fullname
     * @param string $course_code
     * @param string $course_name
     * @param string $start_date
     * @param string $end_date
     * @param string $course_place
     * @param string $quiz_date
     * @param string $content
     * @param string $course_list
     * @param string $url_confirm_email
     * @param string $competency_name
     * @param string $competency_code
     * @param string $password
     * @param string $course_description
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
        $password = '',
        $course_description = '',
        $competency_code = ''
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
        $this->competency_name = $competency_name;
        $this->course_description = $course_description;
        $this->competency_code = $competency_code;
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
        elseif($this->activity == TmsNotification::REMIND_CERTIFICATE) {
            $detail = json_decode($this->content);
            $this->certificate_code = $detail->certificate_code;
            $this->competency_name = $detail->competency_framework;
            $subject = '[ELEARNING] '. __('remind_certificate');
            $view = 'email.remind_certificate';
        }
        elseif ($this->activity == TmsNotification::ENROL) {
            $subject = '[ELEARNING] '. __('assigned_course');
            $view = 'email.assigned_course';
        }
        elseif ($this->activity == TmsNotification::SUGGEST) {
            $subject = '[ELEARNING] '. __('suggest_optional_course');
            $view = 'email.suggest_optional_course';
        }
        elseif ($this->activity == TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE) {
            $course_array = json_decode($this->content);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] '. __('remind_expire_required_course');
            $view = 'email.remind_expire_required_course';
        }
        elseif ($this->activity == TmsNotification::INVITE_STUDENT) {
            $subject = '[ELEARNING] '. __('invitation_offline_course');
            $view = 'email.invite_student';
        }
        elseif ($this->activity == TmsNotification::ASSIGNED_COMPETENCY) {
            $training = json_decode($this->content);
            $this->competency_name = $training->training_name;
            $this->competency_code = $training->training_code;
            $this->start_date = $training->time_start;
            $this->end_date = $training->time_end;
            $subject = '[ELEARNING] '. __('assigned_competency');
            $view = 'email.assigned_competency';
        }
        elseif($this->activity == TmsNotification::COMPLETED_FRAME){
            $training = json_decode($this->content);
            $this->competency_name = $training->training_name;
            $this->competency_code = $training->training_code;
            $this->start_date = $training->time_start;
            $this->end_date = $training->time_end;
            $subject = '[ELEARNING] '. __('completed_competency_framework');
            $view = 'email.completed_competency';
        }
        elseif ($this->activity == TmsNotification::REMIND_EXAM) {
            $exam = json_decode($this->content);
            $this->start_date = $exam->start_time;
            $this->end_date = $exam->end_time;
            $this->exam_name = $exam->quiz_name;
            $subject = '[ELEARNING] '. __('remind_exam');
            $view = 'email.remind_exam';
        }
        //calculate_toeic_grade
        elseif ($this->activity == TmsNotification::CALCULATE_TOEIC_GRADE) {
            //Passing data via content
            $subject = '[ELEARNING] '. __('calculate_toeic_grade');
            $view = 'email.calculate_toeic_grade';
        }
        //request_more_attempt
        elseif ($this->activity == TmsNotification::REQUEST_MORE_ATTEMPT) {
            //Passing data via content
            $subject = '[ELEARNING] '. __('request_more_attempt');
            $view = 'email.request_more_attempt';
        }
        //fail exam
        elseif ($this->activity == TmsNotification::FAIL_EXAM) {
            //Passing data via content
            $subject = '[ELEARNING] '. __('request_more_attempt');
            $view = 'email.request_more_attempt';
        }

        elseif($this->activity == TmsNotification::NOTICE_SPAM_EMAIL){
            $subject = '[ELEARNING] Testing email from Elearning';
            $view = 'email.notice_spam_email';
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
                ->with('course_description', $this->course_description)
                ->with('certificate_code', $this->certificate_code)
                ->with('competency_code', $this->competency_code)
                ->with('exam_name', $this->exam_name)
                ->view($view);
        }
        return $this;
    }
}
