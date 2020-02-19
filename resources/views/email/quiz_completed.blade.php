<html lang="en">
<head>
    <title>Thông báo kết quả kiểm tra</title>
</head>
<body>
<div>
    <?php 
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
        $text = $data['quiz_completed'];

        //replace values
        $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
        $text = str_replace(CourseSendMail::USERNAME, $username, $text);
        $text = str_replace(CourseSendMail::COURSENAME, $course_name, $text);
        $text = str_replace(CourseSendMail::COURSECODE, $course_code, $text);
        $text = str_replace(CourseSendMail::QUIZNAME, $content->name, $text);
        $point = ($content->attempt_sumgrades / $content->sumgrades) * 10;
        echo $point;
        die;
        $point_up = round($point, 1);
        $text = str_replace(CourseSendMail::QUIZPOINT, $point_up, $text);
        //
        echo $text;
    ?>


</body>
</html>
