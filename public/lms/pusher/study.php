<?php
require_once(__DIR__ . '/../config.php');

$servername = $CFG->dbhost;
$dbname = $CFG->dbname;
$username = $CFG->dbuser;
$password = $CFG->dbpass;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection


if (!isguestuser()) {     // Guests can never edit their profile.


    $userid = $USER->id ? $USER->id : 0;       // Owner of the page.
    $course_id = $_REQUEST['course_id'] ? $_REQUEST['course_id'] : 0;
    $activity_id = $_REQUEST['activity_id'] ? $_REQUEST['activity_id'] : 0;
    $module_id = $_REQUEST['module_id'] ? $_REQUEST['module_id'] : 0;
    $url = $_REQUEST['url'] ? $_REQUEST['url'] : '';
    $type = $_REQUEST['type'] ? $_REQUEST['type'] : ''; //final, init

    if ($userid != 0 && $course_id != 0 && $activity_id != 0 && $module_id != 0) {
        $now = time();
        $sql = "SELECT * from tms_learning_activity_logs WHERE user_id = $userid AND course_id = $course_id AND activity_id = $activity_id AND module_id = $module_id ORDER BY id desc LIMIT 1";
        $log = $DB->get_records_sql($sql);

        $today = date('Y-m-d H:i:s', $now);

        $insert_query = "INSERT INTO `tms_learning_activity_logs`(user_id, course_id, activity_id, module_id, start_time, studying, url, created_at, updated_at) VALUES ($userid, $course_id, $activity_id, $module_id, $now, 1, '$url', '$today', '$today')";

        if (empty($log)) { //Start learning

//            $sql = "INSERT INTO {tms_learning_activity_logs} (user_id, course_id, activity, start_time, studying, url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
//            $params = array($userid, $course_id, $module_id, $now, 1, $url, $today, $today);
//            $DB->execute($sql, $params);

            //$DB->execute("INSERT INTO `tms_learning_activity_logs`(user_id, course_id, activity, start_time, studying, url, created_at, updated_at) VALUES ($userid, $course_id, $module_id, $now, 1, '$url', '$today', '$today')");

            //can not save url => use raw query
            $conn->query($insert_query);
        }
        else {
            if ($type == 'init') { //create new log and change studying of other log to 0
                $conn->query($insert_query);
                $log = $DB->get_records_sql($sql);
                $last_record = array_values($log)[0];
                $last_id = $last_record->id;
                $DB->execute("UPDATE `tms_learning_activity_logs` SET studying = 0 WHERE user_id = $userid AND course_id = $course_id AND id <> $last_id");
            } elseif ($type == 'final') { //Update end_time to latest log
                $log = $DB->get_records_sql($sql);
                $last_record = array_values($log)[0];
                $last_id = $last_record->id;
                $last_start = $last_record->start_time;
                $duration = $now - $last_start;
                $DB->execute("UPDATE `tms_learning_activity_logs` SET end_time = $now, duration = $duration WHERE user_id = $userid AND course_id = $course_id AND activity_id = $activity_id AND module_id = $module_id AND id = $last_id");
            }
        }
    }
}






