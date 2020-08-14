<html>
<head></head>
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
        $text = $data['enrol'];
        //replace values
        $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
        $text = str_replace(CourseSendMail::USERNAME, $username, $text);
        $text = str_replace(CourseSendMail::COURSENAME, $course_name, $text);
        $text = str_replace(CourseSendMail::STARTDATE, $start_date, $text);
        $text = str_replace(CourseSendMail::ENDDATE, $end_date, $text);

        if(strlen($course_place) != 0)
        {
            $course_place_string = 'Places to study: <p style="color: blue;">'.$course_place;
            $text = str_replace(CourseSendMail::COURSEPLACE, $course_place_string, $text);
        }else{
            $text = str_replace(CourseSendMail::COURSEPLACE, '', $text);
        }

        //
        echo $text;
    ?>
</div>


</body>
</html>



