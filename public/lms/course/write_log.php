<?php

require_once('../config.php');
require_once(__DIR__ . '/../authen_api.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

if (isset($_POST['data'])) {
    $data = $_POST['data'];
    if ($data) {
        $decode_data = decodeJWT($data);
        if ($decode_data) {
            $json_data = json_decode($decode_data);
            if (encrypt_key(API_KEY_SEC) == $json_data->data->app_key) {
                $courseid = $json_data->data->courseid;
                $action = $json_data->data->action;
            }
        }
    }
}

// This are required.
global $DB, $CFG, $USER;

try {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
} catch (Exception $e) {
    ob_clean();
    echo 0;
    die;
}
if (isset($action)) {
    switch ($action) {
        case "create":
            // Trigger a course created event.
            $event = \core\event\course_created::create(array(
                'objectid' => $course->id,
                'context' => context_course::instance($course->id),
                'other' => array(
                    'shortname' => $course->shortname,
                    'fullname' => $course->fullname
                )
            ));
        case "edit":
            // Trigger a course created event.
            $event = \core\event\course_updated::create(array(
                'objectid' => $course->id,
                'context' => context_course::instance($course->id),
                'other' => array(
                    'shortname' => $course->shortname,
                    'fullname' => $course->fullname
                )
            ));
    }
} else {
    ob_clean();
    echo 0;
    die;
}

try {
    $event->trigger();
    ob_clean();
    echo 1;
    die;
} catch (Exception $e) {
    ob_clean();
    echo 0;
    die;
}
