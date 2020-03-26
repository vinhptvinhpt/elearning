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
    $text = $data['active_email'];
    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::USERNAME, $username, $text);
    $text = str_replace(CourseSendMail::PASSWORD, $password, $text);
    $text = str_replace(CourseSendMail::URL_CONFIRM_EMAIL, $url_confirm_email, $text);

    echo $text;
    ?>
</div>


</body>
</html>



