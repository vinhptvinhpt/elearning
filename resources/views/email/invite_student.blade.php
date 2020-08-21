<html lang="en">
<head>
    <title>Nhắc nhở tham gia khóa học</title>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<div>
    <?php

    /**
     * @var string $fullname
     * @var string $course_name
     * @var string $course_code
     * @var string $course_place
     * @var integer $start_date
     * @var integer $end_date
     * @var string $course_description
     * @var integer $content //invite_id
     *
     */

    use App\Mail\CourseSendMail;

    $string = file_get_contents(public_path()."/files/email/template.json");
    $data = json_decode($string, true);

    $text = $data['invite_student'];

    $app_base_url = Config::get('constants.domain.TMS');

    $url = $app_base_url . 'page/invitation/confirm/' . $content;

    $start_date = !empty($start_date) ? date('Y-m-d H:i:s', $start_date) : '';
    $end_date = !empty($end_date) ? date('Y-m-d H:i:s', $end_date) : '';
    $course_description = !empty($course_description) ? $course_description : 'N/A';
    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::COURSENAME, $course_name, $text);
    $text = str_replace(CourseSendMail::COURSECODE, $course_code, $text);
    $text = str_replace(CourseSendMail::COURSEPLACE, $course_place, $text);
    $text = str_replace(CourseSendMail::ACCEPT_INVITE_URL, $url, $text);
    $text = str_replace(CourseSendMail::COURSEDESCRIPTION, $course_description, $text);
    $text = str_replace(CourseSendMail::STARTTIME, $start_date, $text);
    $text = str_replace(CourseSendMail::ENDTIME, $end_date, $text);

    echo $text;
    ?>
</div>


</body>
</html>
