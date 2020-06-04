<?php

require_once('../config.php');
require_once($CFG->dirroot . '/course/lib.php');

global $DB;

// Get parameters
$userid = $_POST['userid'];
$itemid = $_POST['itemid'];
$finalgrade = $_POST['finalgrade'];
$time = time();
ob_start();
// Check not Null parameter
if (isset($userid) && isset($itemid) && isset($finalgrade)) {
    // Check record if exist in table mdl_grade_grades
    $sql_check = 'SELECT id FROM mdl_grade_grades WHERE itemid = ? AND userid = ?';
    $result = array_values($DB->get_records_sql($sql_check, array($itemid, $userid)));
    try {
        if ($result) {
            // If exist -> update record with grade from game
            $update = 'UPDATE {grade_grades} SET finalgrade = ?, timemodified = ?  where (itemid = ? and userid = ?)';
            $DB->execute($update, array($finalgrade, $time, $itemid, $userid));
            ob_end_clean();
            echo 1;
        } else {
            // If exist -> insert record with grade from game
            $sql_query = 'INSERT INTO {grade_grades} (itemid, userid, finalgrade, timecreated) VALUES (?,?,?,?)';
            $DB->execute($sql_query, array($itemid, $userid, $finalgrade, $time));
            ob_end_clean();
            echo 1;
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo 0;
        die;
    }
} else {
    ob_end_clean();
    echo 0;
    die;
}
