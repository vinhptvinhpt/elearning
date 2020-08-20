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
     * @var string $certificate_code
     * @var string $competency_name
     * @var string $fullname
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
    $text = $data['remind_certificate'];
    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::COMPETENCYNAME, $competency_name, $text);
    $text = str_replace(CourseSendMail::CERTIFICATECODE, $certificate_code, $text);

    echo $text;
    ?>
</div>


</body>
</html>



