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
         * @var integer $content
         *
         */

        use App\Mail\CourseSendMail;

        $string = file_get_contents(public_path()."/files/email/template.json");
        $data = json_decode($string, true);

        $text = $data['invite_student'];

        $app_base_url = Config::get('constants.domain.TMS');

        $url = $app_base_url . 'page/invitation/confirm/' . $content;

        //replace values
        $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
        $text = str_replace(CourseSendMail::COURSENAME, $course_name, $text);
        $text = str_replace(CourseSendMail::COURSECODE, $course_code, $text);
        $text = str_replace(CourseSendMail::COURSEPLACE, $course_place, $text);
        $text = str_replace(CourseSendMail::ACCEPT_INVITE_URL, $url, $text);

        echo $text;
    ?>
</div>


</body>
</html>



