<html lang="en">
<head>
    <title>Khoa hoc hoan thanh</title>
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
     * @var string $competency_name
     * @var string $competency_code
     * @var string $start_date
     * @var string $end_date
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
    $text = $data['completed_competency_framework'];


    $start_date = !empty($start_date) ? date('Y-m-d H:i:s', $start_date) : 'N/A';
    $end_date = !empty($end_date) ? date('Y-m-d H:i:s', $end_date) : 'N/A';

    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::COMPETENCYNAME, $competency_name, $text);
    $text = str_replace(CourseSendMail::COMPETENCYCODE, $competency_code, $text);
    $text = str_replace(CourseSendMail::STARTDATE, $start_date, $text);
    $text = str_replace(CourseSendMail::ENDDATE, $end_date, $text);
    echo $text;
    ?>
</div>


</body>
</html>
