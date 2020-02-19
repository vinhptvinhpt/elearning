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
        $text = $data['remind_login'];
        //replace values
        $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
        $text = str_replace(CourseSendMail::USERNAME, $username, $text);
        echo $text;
    ?>
</div>


</body>
</html>



