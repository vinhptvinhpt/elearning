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
    //replace values
    $text = str_replace(CourseSendMail::FULLNAME, $fullname, $text);
    $text = str_replace(CourseSendMail::USERNAME, $username, $text);
    //define string for table
    $course_list_string = '<table style="border: 1px">
        <tr>
            <th>Mã khung năng lực</th>
            <th>Tên khung năng lực</th>
            <th>Thời gian bắt đầu</th>
            <th>Thời gian kết thúc</th>
        </tr>';
    //loop to set tr to table
    foreach($course_list as $course) {
        $course_list_string .= '<tr>
            <td><p style="color: blue;">'. $course->code .'</p></td>
            <td><p style="color: blue;">'.$course->training_name.'</p></td>
            <td><p style="color: blue;">'.($course->startdate != 0 ? date('d/m/Y', $course->startdate) : "").'</p></td>
            <td><p style="color: blue;">'.($course->enddate != 0 ? date('d/m/Y', $course->enddate) : "").'</p></td>
            </tr>';
    }
    $course_list_string .= '</table>';
    //replace string with list above
    $text = str_replace(CourseSendMail::COURSELIST, $course_list_string, $text);
    //
    echo $text;
    ?>
</div>


</body>
</html>
