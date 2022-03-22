<?php

require_once('../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

global $DB, $CFG, $USER;

$input = filter_input_array(INPUT_POST);
if ($input['action'] == 'edit') {
    $update_field = '';
    if (isset($input['finalgrade'])) {
        $update_field .= "finalgrade='" . $input['finalgrade'] . "'";
    }
    if ($update_field && $input['id']) {
        $sql_query = "UPDATE {grade_grades} SET $update_field WHERE id='" . $input['id'] . "'";
        $DB->execute($sql_query);
    }
}
