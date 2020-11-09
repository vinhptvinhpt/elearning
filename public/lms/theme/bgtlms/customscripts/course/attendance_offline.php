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

$PAGE->set_title("$course->shortname: " . "Attendance offline");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

$node = $PAGE->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
if ($node) {
    $node->force_open();
}
echo $OUTPUT->header();

global $DB, $CFG, $USER;

$itemtype = 'course';
// Get records for attendance check
$sql_01 = '
    select
        userid,
        U.username,
        U.firstname,
        U.lastname
    from
        mdl_user_enrolments UE
    inner join mdl_enrol E on
        UE.enrolid = E.id
    inner join mdl_user U on 
        UE.userid = U.id
    where
        E.courseid = ' . $courseid;

// List attendance history of course
$sql_02 = '
    select 
        * 
    from
        mdl_attendance 
    where
        courseid = ' . $courseid;


// Get all user enrolled in the course include both teacher and student
$user_attendance = array_values($DB->get_records_sql($sql_01));
$attendance_history = array_values($DB->get_records_sql($sql_02));
// $array_date = [];
// foreach ($attendance_history as $row) {
//     $row->attendance = date("d-m-Y", strtotime($row->attendance));
//     array_push($array_date, $row->attendance);
// }
// $array_date = array_unique($array_date);
$array_att = [];
$list_date = [];
$i = 0;
$j = 0;
foreach ($attendance_history as $history) {
    $found = 0;
    if (!in_array($history->attendance, $list_date)) {
        array_push($list_date, $history->attendance);
    }
    $count = 0;
    foreach (range(0, $i) as $count) {
        if ($array_att[$count][0] == $history->userid) {
            $found = 1;
            break;
        }
    }
    if ($found == 0) {
        $temp = [$history->userid, $history->user, $history->username];
        array_push($array_att, $temp);
        $i++;
    }
    // if ($history->present == '1' && $found == 1) {
    //     array_push($a[$count], $history->attendance);
    // }elseif ($history->present == '1' && $found == 0){
    //     array_push($a[$count], $history->attendance);
    // }
    if ($history->present == '1') {
        array_push($array_att[$count], "x");
    } elseif ($history->present == '0') {
        array_push($array_att[$count], " ");
    }
}


?>

<head>
    <style>
        /*  */
        .modal-confirm {
            color: #636363;
            width: 325px;
        }

        .modal-confirm .modal-content {
            padding: 20px;
            border-radius: 5px;
            border: none;
        }

        .modal-confirm .modal-header {
            border-bottom: none;
            position: relative;
        }

        .modal-confirm h4 {
            text-align: center;
            font-size: 26px;
            margin: 30px 0 -15px;
        }

        .modal-confirm .form-control,
        .modal-confirm .btn {
            min-height: 40px;
            border-radius: 3px;
        }

        .modal-confirm .close {
            position: absolute;
            top: -5px;
            right: -5px;
        }

        .modal-confirm .modal-footer {
            border: none;
            text-align: center;
            border-radius: 5px;
            font-size: 13px;
        }

        .modal-confirm .icon-box {
            color: #fff;
            position: absolute;
            margin: 0 auto;
            left: 0;
            right: 0;
            top: -70px;
            width: 95px;
            height: 95px;
            border-radius: 50%;
            z-index: 9;
            background: #3A55B1;
            padding: 15px;
            text-align: center;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
        }

        .modal-confirm .icon-box i {
            font-size: 58px;
            position: relative;
            top: 3px;
        }

        .modal-confirm.modal-dialog {
            margin-top: 80px;
        }

        .modal-confirm .btn {
            color: #fff;
            border-radius: 4px;
            background: #3A55B1;
            text-decoration: none;
            transition: all 0.4s;
            line-height: normal;
            border: none;
        }

        .modal-confirm .btn:hover,
        .modal-confirm .btn:focus {
            background: #2ba3b3;
            outline: none;
        }

        .trigger-btn {
            display: inline-block;
            margin: 100px auto;
        }

        /* End modal */

        /* Tab CSS */
        .tabs-content {
            margin-right: 15px;
            margin-left: 15px;
        }

        /*  */
        /* Switch CSS */
        .checkbox label .toggle,
        .checkbox-inline .toggle {
            margin-left: -20px;
            margin-right: 5px
        }

        .toggle {
            position: relative;
            overflow: hidden
        }

        .toggle input[type=checkbox] {
            display: none
        }

        .toggle-group {
            position: absolute;
            width: 200%;
            top: 0;
            bottom: 0;
            left: 0;
            transition: left .35s;
            -webkit-transition: left .35s;
            -moz-user-select: none;
            -webkit-user-select: none
        }

        .toggle.off .toggle-group {
            left: -100%
        }

        .toggle-on {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 50%;
            margin: 0;
            border: 0;
            border-radius: 0
        }

        .toggle-off {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            right: 0;
            margin: 0;
            border: 0;
            border-radius: 0
        }

        .toggle-handle {
            position: relative;
            margin: 0 auto;
            padding-top: 0;
            padding-bottom: 0;
            height: 100%;
            width: 0;
            border-width: 0 1px
        }

        .toggle.btn {
            min-width: 59px;
            min-height: 20px
        }

        .toggle-on.btn {
            padding-right: 24px
        }

        .toggle-off.btn {
            padding-left: 24px
        }

        .toggle.btn-lg {
            min-width: 79px;
            min-height: 45px
        }

        .toggle-on.btn-lg {
            padding-right: 31px
        }

        .toggle-off.btn-lg {
            padding-left: 31px
        }

        .toggle-handle.btn-lg {
            width: 40px
        }

        .toggle.btn-sm {
            min-width: 50px;
            min-height: 30px
        }

        .toggle-on.btn-sm {
            padding-right: 20px
        }

        .toggle-off.btn-sm {
            padding-left: 20px
        }

        .toggle.btn-xs {
            min-width: 35px;
            min-height: 22px
        }

        .toggle-on.btn-xs {
            padding-right: 12px
        }

        .toggle-off.btn-xs {
            padding-left: 12px
        }

        /* End switch CSS */

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

        .switch {
            position: relative;
            display: inline-block;
            width: 55px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 30px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        #data_table {
            font-family: "Roboto", sans-serif;
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

        #list_table {
            font-family: "Roboto", sans-serif;
            border-collapse: collapse;
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

        .navbar {
            display: none;
        }
    </style>
</head>

<div class="row">
    <div class="tabs-content">
        <div class="list-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link text-center active" href="#attendance" role="tab" data-toggle="tab"><span><?= get_string('attendancelabel') ?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center" href="#list" role="tab" data-toggle="tab"><span><?= get_string('historylabel') ?></span></a>
                </li>
            </ul>
            <br>

        </div>
    </div>
</div>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane in active" id="attendance">

        <table id="data_table">
            <thead>
                <tr>
                    <th><?= get_string('indexlabel') ?></th>
                    <th><?= get_string('accountlabel') ?></th>
                    <th><?= get_string('usernamelabel') ?></th>
                    <th><?= get_string('presentlabel') ?></th>
                    <th><?= get_string('notelabel') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;

                // Parse the result set, and adds each row and colums in HTML table
                foreach ($user_attendance as $row) {

                    if ($isStudent = current(get_user_roles($context, $row->userid))->shortname == 'student' ? true : false) {
                        $index++;
                        ?>
                        <tr id=<?= $row->userid ?>>
                            <td hidden><?= $row->userid ?></td>
                            <td><?= $index ?></td>
                            <td><?= $row->username ?></td>
                            <td><?= $row->lastname . ' ' . $row->firstname ?></td>
                            <td>
                                <input id="switch-<?= $row->userid ?>" type="checkbox" onchange="switchValue(<?= $row->userid ?>)" checked data-toggle="toggle" data-on=<?= get_string('presentswitch') ?> data-off=<?= get_string('absentswitch') ?> data-onstyle="primary" data-offstyle="danger">
                            </td>
                            <td contenteditable></td>
                            <td id="attendance-<?= $row->userid ?>" hidden>1</td>
                        </tr>
                <?php
                    } else {
                        continue;
                    }
                }
                ?>
            </tbody>
        </table>
        <input type="button" href="#notimodal" data-toggle="modal" style="margin-top: 20px; margin-left: auto; margin-right: auto; display: block;" class="btn btn-primary btn-sm" value=<?= get_string('attendancesubmit') ?> onclick="update()" />

        <!-- Modal HTML -->
        <div id="notimodal" class="modal fade">
            <div class="modal-dialog modal-confirm">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="icon-box">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </div>
                        <h4 class="modal-title" style="margin-top: 20px;margin-left: auto;margin-right: auto;"><?= get_string('sucessfulalert') ?></h4>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-info btn-block" data-dismiss="modal" onClick="window.location.reload();">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane " id="list">
        <table id="list_table">
            <thead>
                <tr>
                    <th><?= get_string('indexlabel') ?></th>
                    <th><?= get_string('accountlabel') ?></th>
                    <th><?= get_string('usernamelabel') ?></th>
                    <?php
                    foreach ($list_date as $date) {
                        ?>
                        <th><?= date("d-m-Y", strtotime($date)) ?></th>
                    <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;

                // Parse the result set, and adds each row and colums in HTML table
                foreach ($array_att as $row) {

                    if ($isStudent = current(get_user_roles($context, $row[0]))->shortname == 'student' ? true : false) {
                        $index++;
                        ?>
                        <tr id=<?= $row[0] ?>>
                            <td hidden><?= $row[0] ?></td>
                            <td><?= $index ?></td>
                            <?php foreach (range(1, count($row) - 1) as $id) {
                                        ?>
                                <td><?= $row[$id] ?></td>
                            <?php $id++;
                                    } ?>
                        </tr>
                <?php
                    } else {
                        continue;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php

echo $OUTPUT->footer();
?>

<script>
    $(document).ready(function() {
        $('#data_table').tableSearch({
            searchPlaceHolder: ''
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#list_table').tableSearch({
            searchPlaceHolder: ''
        });
    });
</script>
<script>
    function switchValue(pram) {
        if ($('#switch-' + pram).prop('checked')) {
            $('#attendance-' + pram).html(1)
        } else {
            $('#attendance-' + pram).html(0)
        }
    }

    function getData() {
        var head = ['userid', 'id', 'user', 'username', 'switch', 'note', 'attendance'],
            tableObj = {
                datatable: []
            };
        $.each($("#data_table tbody tr"), function() {
            var $row = $(this),
                rowObj = {};

            i = 0;
            $.each($("td", $row), function() {
                var $col = $(this);
                rowObj[head[i]] = $col.text();
                i++;
            })

            tableObj.datatable.push(rowObj);
        });
        return JSON.stringify(tableObj);
    }

    function update() {
        var currentUrl = window.location.href;
        $.ajax({
            url: 'update_attendance.php?courseid=<?= $courseid ?>',
            data: {
                table: getData()
            },
            type: 'POST',
            dataType: "json",
            success: function(data) {
                window.location.href = currentUrl;
            }
        });
    }
</script>


<?php
die;
?>
