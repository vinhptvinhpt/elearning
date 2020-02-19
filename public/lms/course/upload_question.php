<?php
require_once('../config.php');

$courseid     = optional_param('id', 0, PARAM_INT); // This are required.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);
require_login($course);

$systemcontext = context_system::instance();
$isfrontpage = ($course->id == SITEID);

$frontpagectx = context_course::instance(SITEID);

if ($isfrontpage) {
    $PAGE->set_pagelayout('admin');
} else {
    $PAGE->set_pagelayout('incourse');
}

$PAGE->set_title("$course->shortname: " . "Grade offline");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

$node = $PAGE->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
if ($node) {
    $node->force_open();
}
echo $OUTPUT->header();

?>



<head>
    <style>
        /* body {
            background: rgba(0, 0, 0, 0.9);
        }

        #form_upload_excel {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -100px;
            margin-left: -250px;
            width: 500px;
            height: 200px;
            border: 2px dashed #000000;
        }

        #form_upload_excel p {
            width: 100%;
            height: 100%;
            text-align: center;
            line-height: 170px;
            color: #000000;
            font-family: Arial;
        }

        #form_upload_excel input {
            position: absolute;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            outline: none;
            opacity: 0;
        }

        #form_upload_excel button {
            margin: 0;
            color: #000000;
            background: #16a085;
            border: none;
            width: 508px;
            height: 35px;
            margin-top: -20px;
            margin-left: -4px;
            border-radius: 4px;
            border-bottom: 4px solid #117A60;
            transition: all .2s ease;
            outline: none;
        }

        #form_upload_excel button:hover {
            background: #149174;
            color: #0C5645;
        }

        #form_upload_excel button:active {
            border: 0;
        } */
    </style>
    <script>
        // $(document).ready(function() {
        //     $('form input').change(function() {
        //         $('form p').text(this.files.length + " file(s) selected");
        //     });
        // });
    </script>
</head>

<form id="form_upload_excel" action="upload_question.php?id=<?= $course->id ?>" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="question" id="question" multiple>
    <input type="submit" value="Upload file" name="submit">
</form>


<?php

if (isset($_FILES['question'])) {
    $errors = array();
    $file_name = $_FILES['question']['name'];
    $file_size = $_FILES['question']['size'];
    $file_tmp = $_FILES['question']['tmp_name'];
    $file_type = $_FILES['question']['type'];
    $file_ext = strtolower(end(explode('.', $_FILES['question']['name'])));

    $extensions = array("xlsx", "xls");

    if (in_array($file_ext, $extensions) === false) {
        $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
    }

    if ($file_size > 2097152) {
        $errors[] = 'File size must be excately 2 MB';
    }

    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, "upload/" . $file_name);
        echo "Success";
    } else {
        print_r($errors);
    }
}

echo $OUTPUT->footer();
?>