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
     * @var string $content
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

    $result = json_decode($content);
    $student = "N/A";
    $user_id = "N/A";
    $url = "NA";
    if (!empty($result)) {
        $student = $result->object_name;
        $attempt = $result->attempt;
        $app_base_url = Config::get('constants.domain.LMS');
        $url = $app_base_url . '/mod/quiz/review.php?attempt=' . $attempt;
        $url_unlock = '';//Pending
    }
    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::STUDENT, $student, $text);
    $text = str_replace(CourseSendMail::LINK_TO_REVIEW, $url, $text);
    $text = str_replace(CourseSendMail::LINK_TO_UNLOCK, $url_unlock, $text);

    echo $text;
    ?>
</div>


</body>
</html>



