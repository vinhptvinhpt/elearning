<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../lib/enrollib.php');

$course_id = $_REQUEST['course_id'] ? $_REQUEST['course_id'] : 0;
$user_id = $_REQUEST['user_id'] ? $_REQUEST['user_id'] : 0;

try {
    if (is_numeric($user_id) && $user_id != 0 && is_numeric($course_id) && $course_id != 0) {
        // //check and enrol user to course here
        // //Check mdl_context
        // $context = $DB->get_record('context', ['instanceid' => $course_id, 'contextlevel' => CONTEXT_COURSE]);
        // if (!empty($context)) {
        //     $context_id = $context->id;
        // } else {
        //     $context_id = 0;
        // }
        // //Check mdl_enrol
        // $checkEnrol = $DB->get_record('enrol', ['enrol' => 'manual', 'courseid' => $course_id, 'roleid' => 5]);
        // if (empty($checkEnrol)) { //Chua co ban ghi enrol thi gan vao day
        //     $record = new \stdClass();
        //     $record->enrol = 'manual';
        //     $record->courseid = $course_id;
        //     $record->roleid = 5;
        //     $record->sortorder = 0;
        //     $record->status = 0;
        //     $record->expirythreshold = 86400;
        //     $record->timecreated = time();
        //     $record->timemodified = time();

        //     $enrol_id = $DB->insert_record('enrol', $record);
        //     $need_to_insert_users = [$user_id];
        // } else {
        //     $enrol_id = $checkEnrol->id;
        // }
        // //Check mdl_user_enrolments
        // $checkEnrolment = $DB->get_record('user_enrolments', ['enrolid' => $enrol_id, 'userid' => $user_id]);
        // if (empty($checkEnrolment)) {
        //     $record_enrolment = new \stdClass();
        //     $record_enrolment->enrolid = $enrol_id;
        //     $record_enrolment->userid = $user_id;
        //     $record_enrolment->timestart = time();
        //     $record_enrolment->modifierid = $user_id;
        //     $record_enrolment->timecreated = time();
        //     $record_enrolment->timemodified = time();

        //     $enrolment_id = $DB->insert_record('user_enrolments', $record_enrolment);
        // }
        // //Check mdl_role_assignments
        // $checkAssignment = $DB->get_record('role_assignments', ['roleid' => 5, 'contextid' => $context_id, 'userid' => $user_id]);
        // if (empty($checkAssignment)) {
        //     $record_assignment = new \stdClass();
        //     $record_assignment->roleid = 5;
        //     $record_assignment->userid = $user_id;
        //     $record_assignment->contextid = $context_id;
        //     $enrolment_id = $DB->insert_record('role_assignments', $record_assignment);
        // }
        // //Check mdl_grade_items
        // $checkGradeItem = $DB->get_record('grade_items', ['courseid' => $course_id]);
        // if (!empty($checkGradeItem)) {
        //     //insert du lieu vao bang mdl_grade_grades phuc vu chuc nang cham diem -> Vinh PT require
        //     $checkGradeGrade = $DB->get_record('grade_grades', ['itemid' => $checkGradeItem->id, 'userid' => $user_id]);
        //     if (empty($checkGradeGrade)) {
        //         $record_grade_grade = new \stdClass();
        //         $record_grade_grade->itemid = $checkGradeItem->id;
        //         $record_grade_grade->userid = $user_id;
        //         $enrolment_id = $DB->insert_record('grade_grades', $record_grade_grade);
        //     }
        // }
        // Try to enrol user via default internal auth plugin.
        enrol_try_internal_enrol($course_id, $user_id, 5);
        //Log to tms_manual_enrol_log
        $servername = $CFG->dbhost;
        $dbname = $CFG->dbname;
        $username = $CFG->dbuser;
        $password = $CFG->dbpass;
        $conn = new mysqli($servername, $username, $password, $dbname);
        $now = date('Y-m-d H:i:s', time());

        $insert_query = "INSERT INTO `tms_manual_enrol_log`(user_id, course_id, created_at, updated_at) VALUES ($user_id, $course_id, '$now', '$now')";

        $conn->query($insert_query);
    }

    $status = true;
    $message = "Enrol successfully";

    // // Executed when user enrolment was changed to check if course contacts cache needs to be cleared
    // $cache = cache::make('core', 'coursecontacts');
    // // remove course contacts cache for this course.
    // $cache->delete($course_id);
    // // this must be done after the enrolment event so that the role_assigned event is triggered afterwards
    // role_assign(5, $user_id, $context_id);
} catch (Exception $e) {
    $status = false;
    $message = "Enrol fail";
}
echo json_encode([
    'status' => $status,
    'msg' => $message
]);
