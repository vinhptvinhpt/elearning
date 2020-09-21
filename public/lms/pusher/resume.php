<?php
require_once(__DIR__ . '/../config.php');


if (!isguestuser()) {     // Guests can never edit their profile.
    $userid = $USER->id ? $USER->id : 0;       // Owner of the page.

    $course_id = $_REQUEST['course_id'] ? $_REQUEST['course_id'] : 0;

    if ($userid != 0 && $course_id != 0) { //Check enrol nếu chưa enrol thì không trả về thông báo nữa
        $sql = "SELECT * FROM tms_learning_activity_logs
INNER JOIN mdl_enrol me on tms_learning_activity_logs.course_id = me.courseid AND me.roleid = 5 AND me.enrol <> 'self'
INNER JOIN mdl_user_enrolments mue on me.id = mue.enrolid
WHERE tms_learning_activity_logs.user_id = $userid
AND tms_learning_activity_logs.course_id = $course_id
AND tms_learning_activity_logs.studying = 1
AND mue.userid = $userid
ORDER BY tms_learning_activity_logs.id DESC
LIMIT 1";
        $log = $DB->get_records_sql($sql);
        if (!empty($log)) { //Start learning
            $last_record = array_values($log)[0];
            echo $last_record->url;
            die;
        }
    }
}
echo '';
