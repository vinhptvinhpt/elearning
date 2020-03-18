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
                ->view($view);
        }
        return $this;
    }
}
