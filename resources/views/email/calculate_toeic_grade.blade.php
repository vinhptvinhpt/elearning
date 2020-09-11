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
    $text = $data['calculate_toeic_grade'];

    $result = json_decode($content);
    $listening = "N/A";
    $reading = "N/A";
    $total = "NA";
    if (!empty($result)) {
        $listening = $result->listening;
        $reading = $result->reading;
        $total = $result->total;
    }

    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::LISTENING, $listening, $text);
    $text = str_replace(CourseSendMail::READING, $reading, $text);
    $text = str_replace(CourseSendMail::TOTAL, $total, $text);

    echo $text;
    ?>
</div>


</body>
</html>



