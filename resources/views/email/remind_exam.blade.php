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
     * @var string $start_time
     * @var string $end_time
     * @var string $exam_name
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
    $text = $data['remind_exam'];

    $start_date = !empty($start_date) ? date('Y-m-d H:i:s', $start_date) : 'N/A';
    $end_date = !empty($end_date) ? date('Y-m-d H:i:s', $end_date) : 'N/A';

    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::STARTTIME, $start_date, $text);
    $text = str_replace(CourseSendMail::ENDTIME, $end_date, $text);
    $text = str_replace(CourseSendMail::EXAMNAME, $exam_name, $text);

    echo $text;
    ?>
</div>


</body>
</html>



