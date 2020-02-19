<?php
require_once('../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');

$courseid     = optional_param('id', 0, PARAM_INT); // This are required.
$list_view = optional_param('action', 0, PARAM_TEXT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);
require_login($course);

if ($list_view) {
    $list_view = optional_param('id', 0, PARAM_TEXT);
} else {
    $list_view = "add";
}


$systemcontext = context_system::instance();
$isfrontpage = ($course->id == SITEID);

$frontpagectx = context_course::instance(SITEID);

if ($isfrontpage) {
    $PAGE->set_pagelayout('admin');
} else {
    $PAGE->set_pagelayout('incourse');
}

$PAGE->set_title("$course->shortname: " . "Module log");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

$node = $PAGE->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
if ($node) {
    $node->force_open();
}
echo $OUTPUT->header();

global $DB, $CFG, $USER;

$itemtype = 'course';
$context_lvl = '70';
// Get records for attendance check
$sql_01 = '
select
	lsl.id,
	lsl.objectid,
	lsl.eventname,
	lsl.action,
	u.username,
	lsl.other,
	lsl.timecreated
from
	{logstore_standard_log} as lsl
inner join {user} as u on
	lsl.userid = u.id
where
    lsl.action <> \'viewed\' and
	courseid = ' . $course->id .
    ' and contextlevel = ' . $context_lvl . ' order by lsl.timecreated DESC';

// Get all activity log in course
$module_log = array_values($DB->get_records_sql($sql_01));

// Get mod info
$modinfo = get_fast_modinfo($course->id);

?>

<head>
    <style>
        /* Table CSS */
        .textbox {
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            border: 1px solid #848484;
            outline: 0;
            height: 25px;
            width: 275px;
        }

        #data_table {
            font-family: "Roboto", sans-serif border-collapse: collapse;
            width: 100%;
        }

        #data_table td,
        #data_table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #data_table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #data_table tr:hover {
            background-color: #ddd;
        }

        #data_table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #3A55B1;
            color: white;
        }

        #list_table {
            font-family: "Roboto", sans-serif border-collapse: collapse;
            width: 100%;
        }

        #list_table td,
        #list_table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #list_table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #list_table tr:hover {
            background-color: #ddd;
        }

        #list_table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #3A55B1;
            color: white;
        }
    </style>
</head>

<table id="data_table">
    <thead>
        <tr>
            <th><?= get_string('indexlabel')?></th>
            <th><?= get_string('eventcodelabel')?></th>
            <th><?= get_string('eventnamelabel')?></th>
            <th><?= get_string('eventlabel')?></th>
            <th><?= get_string('usermodifiedlabel')?></th>
            <th><?= get_string('contentlabel')?></th>
            <th><?= get_string('timemodifiedlabel')?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $index = 0;

        // Parse the result set, and adds each row and colums in HTML table
        foreach ($module_log as $row) {
            $index++;
            ?>
            <tr id=<?= $row->id ?>>
                <td hidden><?= $row->id ?></td>
                <td><?= $index ?></td>
                <td><?= $row->objectid ?></td>
                <td><?= $row->eventname ?></td>
                <td><?= $row->action ?></td>
                <td><?= $row->username ?></td>
                <td><?= $row->other ?></td>
                <td><?= gmdate("d-m-Y H:i:s", $row->timecreated) ?></td>
            </tr>
        <?php

        }
        ?>
    </tbody>
</table>

<?php

echo $OUTPUT->footer();
?>

<script>
    $(document).ready(function() {
        $('#data_table').tableSearch({
            searchPlaceHolder: 'Tìm kiếm'
        });
    });
</script>