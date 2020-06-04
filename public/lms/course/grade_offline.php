<?php
require_once('../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

$courseid     = optional_param('id', 0, PARAM_INT); // This are required.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);
require_login($course);

$systemcontext = context_system::instance();
$isfrontpage = ($course->id == SITEID);

$frontpagectx = context_course::instance(SITEID);

if ($isfrontpage) {
    $PAGE->set_pagelayout('admin');
} else {
    $PAGE->set_pagelayout('incourse');
}

$PAGE->set_title("$course->shortname: "."Grade offline");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

$node = $PAGE->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
if ($node) {
    $node->force_open();
}
echo $OUTPUT->header();

global $DB, $CFG, $USER;

$itemtype = 'course';

$sql_01 = '
    select
        grade.id,
        grade.userid,
        u.username,
        u.lastname,
        u.firstname,
        grade.finalgrade
    from
        {grade_grades} grade
    inner join 
        {user} u
    on 
    grade.userid = u.id
    where
        itemid = (
        select
            id
        from
            {grade_items}
        where
            courseid = ' . $courseid . ' 
            and itemtype = \'course\')
';
// Get grade from table grade_grades for user enroll in the course
$user_grade = array_values($DB->get_records_sql($sql_01));
$index = 0;
?>

<head>
<style>
    #data_table {
        font-family: "Roboto",sans-serif
        border-collapse: collapse;
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
</style>
</head>

<?php
// Create the beginning of HTML table, and the first row with colums title
$html_table = '<table id="data_table">
    <thead>
    <tr>
        <th>ID</th>
        <th>'.get_string('indexlabel').'</th>
        <th>'.get_string('accountlabel').'</th>
        <th>'.get_string('usernamelabel').'</th>
        <th>'.get_string('finalgradelabel').'</th>
    </tr>
    </thead>
    <tbody>';
$index = 1;
// Parse the result set, and adds each row and colums in HTML table
foreach ($user_grade as $row) {
    $html_table .=
        '<tr id = ' . $row->id . '>
        <td>' . $row->id . '</td>
        <td>' . $index++ . '</td>
        <td>' . $row->username . '</td>
        <td>' . $row->lastname . ' ' . $row->firstname . '</td>
        <td>' . round($row->finalgrade,2) . '</td>
    </tr>';
}
$html_table .= '</tbody></table>';           // ends the HTML table

echo $html_table;

echo $OUTPUT->footer();
?>

<script>
    $(document).ready(function() {
        $('#data_table').Tabledit({
            deleteButton: false,
            editButton: false,
            columns: {
                identifier: [0, 'id'],
                editable: [
                    [4, 'finalgrade']
                ]
            },
            hideIdentifier: true,
            url: 'update_grade.php'
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#data_table').tableSearch({
                searchPlaceHolder: ''
            });
        });
</script>