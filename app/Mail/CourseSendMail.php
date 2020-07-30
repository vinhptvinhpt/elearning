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
        $course_list = array(),
        $url_confirm_email = '',
        $password = ''
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
            || $this->activity == TmsNotification::ACTIVE_EMAIL
            || $this->activity == TmsNotification::FORGOT_PASSWORD
        ) {
            switch ($this->activity) {
                case TmsNotification::ENROL:
                    $subject = '[ELEARNING] Thông báo học viên được ghi danh vào khóa học';
                    $view = 'email.enrol_course';
                    break;
                case TmsNotification::QUIZ_START:
                    $subject = '[ELEARNING] Thông báo bài kiểm tra mới';
                    $view = 'email.quiz_start';
                    break;
                case TmsNotification::QUIZ_END:
                    $subject = '[ELEARNING] Thông báo bài kiểm tra sắp hết hạn';
                    $view = 'email.quiz_end';
                    break;
                case TmsNotification::QUIZ_COMPLETED:
                    $subject = '[ELEARNING] Thông báo kết quả kiểm tra';
                    $view = 'email.quiz_completed';
                    break;
                case TmsNotification::REMIND_CERTIFICATE:
                    $subject = '[ELEARNING] Thông báo về việc cấp chứng chỉ';
                    $view = 'email.remind_certificate';
                    break;
                case TmsNotification::FORGOT_PASSWORD:
                    $subject = '[ELEARNING] Thông báo lấy lại mật khẩu';
                    $view = 'email.forgot_password';
                    break;
                case TmsNotification::ACTIVE_EMAIL:
                    $subject = '[ELEARNING] Thông báo xác nhận email';
                    $view = 'email.active_email';
                    break;
                default:
                    $subject = '';
                    $view = '';
            }
        }
        elseif($this->activity == TmsNotification::COMPLETED_FRAME){
            $course_array = json_decode($this->content);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] Thông báo xác nhận email';
            $view = 'email.completed_competency';
        }
        elseif ($this->activity == TmsNotification::SUGGEST) {
            $subject = '[ELEARNING] Giới thiệu một số khóa học kĩ năng mềm';
            $view = 'email.suggest_course';
        } elseif ($this->activity == TmsNotification::REMIND_EXPIRE_REQUIRED_COURSE) {
            $course_array = json_decode($this->content);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] Khóa học bắt buộc sắp hết hạn';
            $view = 'email.remind_expire_required_course';
        } elseif ($this->activity == TmsNotification::REMIND_EDUCATION_SCHEDULE) {
            $course_array = json_decode($this->content);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] Bạn có một số khóa học trong lộ trình chưa hoàn thành';
            $view = 'email.remind_education_schedule';
        } elseif ($this->activity == TmsNotification::REMIND_LOGIN) {
            $subject = '[ELEARNING] Bạn đã lâu không đăng nhập vào hệ thống';
            $view = 'email.remind_login';
        } elseif ($this->activity == TmsNotification::REMIND_UPCOMING_COURSE) {
            $subject = '[ELEARNING] Giới thiệu một số khóa học sắp bắt đầu';
            $view = 'email.upcoming_course';
        } elseif ($this->activity == TmsNotification::REMIND_ACCESS_COURSE) {
            $course_array = json_decode($this->content);
            $this->course_list = $course_array;
            $subject = '[ELEARNING] Bạn đã lâu không tương tác với khóa học chưa hoàn thành';
            $view = 'email.remind_access_course';
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
                ->view($view);
        }
        return $this;
    }
}
