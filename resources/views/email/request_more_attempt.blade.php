<html>
<head>
    <style>
        p {
            font: 16px "Times New Roman", Times, serif;
        }
    </style>
</head>
<body>
<div>
    <?php
    /**
     * @var string $fullname
     * @var string $course_name
     * @var \App\TmsNotification $content
     *
     */
    //using class
    use App\Mail\CourseSendMail;

    // echo public_path();
    $dir = public_path(). "/files/email";
    // //return file or foler in directory above
    $temp_files = scandir($dir);
    //get content of file with name
    $string = file_get_contents(public_path()."/files/email/template.json");
    // //decode content of file above=
    $data = json_decode($string, true);
    $text = $data['request_more_attempt'];

    $real_content = $content->content;
    $notification_id = $content->id;

    $result = json_decode($real_content);

    $student = "N/A";
    $user_id = "N/A";
    $url = "NA";
    if (!empty($result)) {
        $student = $result->object_name;
        $attempt = $result->attempt;
        $lms_base_url = Config::get('constants.domain.LMS');
        $url_review = $lms_base_url . '/mod/quiz/review.php?attempt=' . $attempt;
        $tms_base_url = Config::get('constants.domain.TMS');
        $url_unlock = $tms_base_url . 'page/notification/unlock/' . $notification_id;
    }
    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::STUDENT, $student, $text);
    $text = str_replace(CourseSendMail::LINK_TO_REVIEW, $url_review, $text);
    $text = str_replace(CourseSendMail::LINK_TO_UNLOCK, $url_unlock, $text);
    $text = str_replace(CourseSendMail::COURSENAME, $course_name, $text);

    echo $text;
    ?>
</div>


</body>
</html>



