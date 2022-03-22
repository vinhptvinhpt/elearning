<?php
require_once('../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');

if (optional_param('pageno', 0, PARAM_INT)) {
    $pageno = optional_param('pageno', 0, PARAM_INT);
} else {
    $pageno = 1;
}


$courseid     = optional_param('id', 0, PARAM_INT); // This are required.
$list_view = optional_param('action', 0, PARAM_TEXT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);
require_login($course);

$course_id_url = '?id=' . $course->id;

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
$PAGE->set_title("$course->shortname: " . "Participants");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

$node = $PAGE->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
if ($node) {
    $node->force_open();
}
echo $OUTPUT->header();

global $DB, $CFG, $USER;

// Get total page
$no_of_records_per_page = 10;
$offset = ($pageno - 1) * $no_of_records_per_page;
$sql_count = '
select
		COUNT(UE.id) as count
	from
		mdl_user_enrolments UE
	inner join mdl_enrol E on
		UE.enrolid = E.id
	inner join mdl_user U on
		UE.userid = U.id
	where
        E.courseid = ' . $course->id;
$total_rows = array_values($DB->get_records_sql($sql_count))[0]->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

// Get records for attendance check
$sql_01 = '
select
    UE.id as enrolmentid, userid, username, firstname, lastname
	from
		mdl_user_enrolments UE
	inner join mdl_enrol E on
		UE.enrolid = E.id
	inner join mdl_user U on
		UE.userid = U.id
	where
		E.courseid = ' . $course->id  . ' LIMIT ' . $offset . ', ' . $no_of_records_per_page;

// Get all activity log in course
$participants = array_values($DB->get_records_sql($sql_01));
foreach ($participants as $participant) {
    $participant->role = current(get_user_roles($context, $participant->userid))->shortname;
}

?>

<head>
    <style>
        
        .div-pagination {
            display: inline-block;
        }

        .pagination{
            text-align: center;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        .pagination a:first-child {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .pagination a:last-child {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }
    </style>
    <input type="hidden" name="sesskey" value=<?= sesskey() ?> />
    <h2><?= get_string('numberofparticipants') ?><?= $total_rows ?></h2>
    <br>
</head>

<table id="data_table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th><?= get_string('indexlabel') ?></th>
            <th><?= get_string('username') ?></th>
            <th><?= get_string('fullname') ?></th>
            <th><?= get_string('role') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $index = $offset;

        // Parse the result set, and adds each row and colums in HTML table
        foreach ($participants as $row) {
            $index++;
            ?>
            <tr id=<?= $row->id ?>>
                <td hidden><?= $row->userid ?></td>
                <td><?= $index ?></td>
                <td><?= $row->username ?></td>
                <td><?= $row->lastname . ' ' . $row->firstname ?></td>
                <td><?= $row->role ?></td>
                <td>
                    <button type="button" class="btn btn-danger btn-lg" onclick="location.href='/lms/enrol/unenroluser.php?confirm=true&ue=<?= $row->enrolmentid ?>&sesskey=<?= sesskey() ?>'">
                        <span class="fa fa-close"></span>
                    </button>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<div class="div-pagination">
    <div class="pagination">
        <a href="<?= $course_id_url ?>&pageno=1"><?= get_string('firstbutton') ?></a>
        <div class="<?php if ($pageno <= 1) {
                        echo 'disabled';
                    } ?>">
            <a href="<?php if ($pageno <= 1) {
                            echo '#';
                        } else {
                            echo $course_id_url . "&pageno=" . ($pageno - 1);
                        } ?>">
                < </a> </div> <div class="<?php if ($pageno >= $total_pages) {
                                                echo 'disabled';
                                            } ?>">
                    <a href="<?php if ($pageno >= $total_pages) {
                                    echo '#';
                                } else {
                                    echo $course_id_url . "&pageno=" . ($pageno + 1);
                                } ?>"> > </a>
        </div>
        <a href="<?= $course_id_url ?>&pageno=<?php echo $total_pages; ?>"><?= get_string('lastbutton') ?></a>
    </div>
</div>

<?php

echo $OUTPUT->footer();
?>
<script>
    // $(document).ready(function() {
    //     $('#data_table').DataTable();
    // });

    // $(document).ready(function() {
    //     $('#data_table').tableSearch({
    //         searchPlaceHolder: 'Tìm kiếm'
    //     });
    // });
</script>