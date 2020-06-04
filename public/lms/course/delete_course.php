<?php

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../authen_api.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

$course = null;
if (isset($_POST['data'])) {
    $data = $_POST['data'];
    if($data){
        $decode_data = decodeJWT($data);
        if($decode_data){
            $json_data = json_decode($decode_data);
            if(encrypt_key(API_KEY_SEC) == $json_data->data->app_key){
                $id = $json_data->data->courseid;
            }
        }
    }
}
// Get course from course id
try{
    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
}catch (Exception $e) {
    echo 0;
    die;
}

ob_start();
// Delete course
if (delete_course($course)){
    // Fix course sort order
    fix_course_sortorder();
    ob_end_clean();
    echo 1;
}else{
    ob_end_clean();
    echo 0;
}

exit;
