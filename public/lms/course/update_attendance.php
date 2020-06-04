<?php

require_once('../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

$courseid     = optional_param('courseid', 0, PARAM_INT);
$table     = optional_param('table', 0, PARAM_RAW);
// This are required.
global $DB, $CFG, $USER;

$inputdata = json_decode($table)->datatable;

foreach ($inputdata as $input) {
    if (isset($input->userid)) {
        $userid = $input->userid;
    }

    if (isset($input->note)) {
        $note = $input->note;
    } else {
        $note = '';
    }

    if (isset($input->user)) {
        $user = $input->user;
    } else {
        $user = '';
    }

    if (isset($input->username)) {
        $username = $input->username;
    } else {
        $username = '';
    }

    if (isset($input->attendance)) {
        if ($input->attendance == "1") {
            $present = 1;
        } elseif ($input->attendance == "0") {
            $present = 0;
        } else {
            exit;
        }
    } else {
        exit;
    }

    $date = date('Y-m-d', time());

    if ($userid) {
        $check_grade = 'SELECT * from {attendance} where (courseid=? and userid=? and attendance=?)';
        $result = array_values($DB->get_records_sql($check_grade, array($courseid, $userid, $date)));
        // $DB->execute($sql_query, array($courseid, $userid, $date));
        if ($result) {
            $update = 'UPDATE {attendance} set present=?, note=?  where (courseid=? and userid=? and attendance=?)';
            $DB->execute($update, array($present, $note, $courseid, $userid, $date));
        } else {
            $sql_query = 'INSERT INTO {attendance} (courseid, userid, attendance, note, present, user, username) VALUES (?,?,?,?,?,?,?)';
            $DB->execute($sql_query, array($courseid, $userid, $date, $note, $present, $user, $username));
        }
    }
}
