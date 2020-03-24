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
     * @param array $course_list
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
        $course_list = array()
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
        if ($this->activity == TmsNotification::INVITE_STUDENT) {
            $subject = __('thu_moi_tham_gia_khoa_hoc');
            $view = 'email.invite_student';
        }
        else if ($this->activity == TmsNotification::ENROL
        || $this->activity == TmsNotification::QUIZ_START
        || $this->activity == TmsNotification::QUIZ_END
        || $this->activity == TmsNotification::QUIZ_COMPLETED
        || $this->activity == TmsNotification::REMIND_CERTIFICATE
        ) {
            switch ($this->activity) {
                case TmsNotification::ENROL:
                    $subject = '[BGT ELEARNING] Thông báo học viên được ghi danh vào khóa học';
                    $view = 'email.enrol_course';
                    break;
                case TmsNotification::QUIZ_START:
                    $subject = '[BGT ELEARNING] Thông báo bài kiểm tra mới';
                    $view = 'email.quiz_start';
                    break;
                case TmsNotification::QUIZ_END:
                    $subject = '[BGT ELEARNING] Thông báo bài kiểm tra sắp hết hạn';
                    $view = 'email.quiz_end';
                    break;
                case TmsNotification::QUIZ_COMPLETED:
                    $subject = '[BGT ELEARNING] Thông báo kết quả kiểm tra';
                    $view = 'email.quiz_completed';
                    break;
                case TmsNotification::REMIND_CERTIFICATE:
                    $subject = '[BGT ELEARNING] Thông báo về việc cấp chứng chỉ';
                    $view = 'email.remind_certificate';
                    break;
                case TmsNotification::FORGOT_PASSWORD:
                    $subject = '[BGT ELEARNING] Thông báo lấy lại mật khẩu';
                    $view = 'email.forgot_password';
                    break;
                default:
                    $subject = '';
                    $view = '';
            }
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
                ->view($view);
        }
        return $this;
    }
}
