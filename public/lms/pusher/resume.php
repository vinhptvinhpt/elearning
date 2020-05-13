<?php
require_once(__DIR__ . '/../config.php');


if (!isguestuser()) {     // Guests can never edit their profile.
    $userid = $USER->id ? $USER->id : 0;       // Owner of the page.
    $course_id = $_REQUEST['course_id'] ? $_REQUEST['course_id'] : 0;
    if ($userid != 0 && $course_id != 0) {
        $sql = "SELECT * from tms_learning_activity_logs WHERE user_id = $userid AND course_id = $course_id AND studying = 1 ORDER BY id desc LIMIT 1";
        $log = $DB->get_records_sql($sql);
        if (!empty($log)) { //Start learning
            $last_record = array_values($log)[0];
            echo $last_record->url;
            die;
        }
    }
}
echo '';
