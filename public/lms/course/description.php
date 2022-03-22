<?php
require_once('../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');

$courseid     = optional_param('id', 0, PARAM_INT); // This are required.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
// Get context base on course
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);

// global variable
global $DB;

$systemcontext = context_system::instance();
$isfrontpage = ($course->id == SITEID);

$frontpagectx = context_course::instance(SITEID);
// Check front page
if ($isfrontpage) {
    $PAGE->set_pagelayout('admin');
} else {
    $PAGE->set_pagelayout('incourse');
}
// set page layout
$PAGE->set_title("$course->shortname: " . "Giới thiệu khoá học");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

$node = $PAGE->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
if ($node) {
    $node->force_open();
}
echo $OUTPUT->header();

// // Add description to top of course content
$courserenderer = $PAGE->get_renderer('core', 'course');
// echo $courserenderer->course_info_box_course_view($course);

// Get course information
$sql = "
select
    c.category,
    c.shortname,
	c.fullname,
	c.course_place,
	c.startdate,
    c.enddate,
    c.summary,
    cc.name as cat_name,
    (
		select ccc.gradepass
	from
		mdl_course
	inner join mdl_course_completion_criteria ccc on
		mdl_course.id = ccc.course
	where
		mdl_course.id = " . $course->id . ") as gradepass,
	(
	select
		count(userid)userid
	from
		mdl_user_enrolments UE
	inner join mdl_enrol E on
		UE.enrolid = E.id
	inner join mdl_user U on
		UE.userid = U.id
	where
		E.courseid = " . $course->id . ") as num_of_mems
from
	mdl_course c
inner join mdl_course_categories cc on
	c.category = cc.id
where
	c.id = " . $course->id . "

";
$course_info = array_values($DB->get_records_sql($sql))[0];

// // Get list teacher of course
// $sql_teachers = "
// select
//         userid,
//         U.firstname,
// 		U.lastname
// 	from
// 		mdl_user_enrolments UE
// 	inner join mdl_enrol E on
// 		UE.enrolid = E.id
// 	inner join mdl_user U on
// 		UE.userid = U.id
// 	where
//         E.courseid = " . $course->id . "";
// $list_enroll = array_values($DB->get_records_sql($sql_teachers));
// $list_teacher = array();
// $list_student = array();
// $role_teacher = array("teacher", "editingteacher");
// foreach ($list_enroll as $user) {
//     if (in_array(current(get_user_roles($context, $user->userid))->shortname, $role_teacher)) {
//         array_push($list_teacher, $user);
//     } elseif (current(get_user_roles($context, $user->userid))->shortname == 'student') {
//         array_push($list_student, $user);
//     }
// }

// [VinhPT][30.12.2019] Count number of teacher, student
$contextids = $context->get_parent_context_ids();
$contextids[] = $context->id;

list($contextids, $params) = $DB->get_in_or_equal($contextids, SQL_PARAMS_QM);

$sql_count = "
    SELECT r.shortname, count(ra.id) as num  
    FROM {role_assignments} ra, {role} r, {context} c
    WHERE ra.roleid = r.id
        AND ra.contextid = c.id
        AND ra.contextid $contextids
    group by r.shortname
    ORDER BY c.contextlevel DESC, r.sortorder asc";

$num_members = $DB->get_records_sql($sql_count ,$params);

$sql_teacher = "
    SELECT ra.userid, u.firstname, u.lastname, r.shortname as role
    FROM mdl_role r, mdl_context c, mdl_role_assignments ra
    inner join mdl_user u on ra.userid = u.id
    WHERE ra.roleid = r.id
        AND ra.contextid = c.id
        AND ra.contextid $contextids
        AND r.shortname IN ('teacher','editingteacher')
    ORDER BY c.contextlevel DESC, r.sortorder asc";
$list_teacher = $DB->get_records_sql($sql_teacher ,$params);

// Case course category = 3 or course categor = 4
// Get number of total modules of course
if ($course_info->category == '3' || $course_info->category == '4') {
    $sql_modules = "
    select
        count(cm.id) as num
    from
        mdl_course_modules cm
    inner join mdl_course_sections cs on
        cm.course = cs.course
        and cm.section = cs.id
    where
        cs.section <> 0
        and cm.course =  " . $course->id . "";
    $num_modules = (int) array_values($DB->get_records_sql($sql_modules))[0]->num;

    // Get number of total learned modules in course
    $sql_learned =
        "
        select
            count(cmc.coursemoduleid) as num
        from
            mdl_course_modules cm
        inner join mdl_course_modules_completion cmc on
            cm.id = cmc.coursemoduleid
        inner join mdl_course_sections cs on
            cm.course = cs.course
            and cm.section = cs.id
        inner join mdl_course c on
                cm.course = c.id
        where
            cs.section <> 0
            and cmc.completionstate <> 0
            and cm.course = " . $course->id . "
            and cmc.userid = " . $USER->id . "
        ";
    $num_learned = (int) array_values($DB->get_records_sql($sql_learned))[0]->num;
    $num_progress = round(($num_learned / $num_modules) * 100, 2);
    if (is_nan($num_progress)) {
        $num_progress = 0;
    }
} elseif ($course_info->category == '5') {
    $sql_date = 'select
                    count(id) as attendancedate,
                    (
                        select total_date_course
                    from
                        mdl_course
                    where
                        id = ' . $course->id . ') as totalcoursedate
                from
                    mdl_attendance
                where
                    courseid = ' . $course->id . '
                    and userid = ' . $USER->id;

    $num_date = array_values($DB->get_records_sql($sql_date))[0];
    $num_progress = round(($num_date->attendancedate / $num_date->totalcoursedate) * 100, 2);
    if (is_nan($num_progress)) {
        $num_progress = 0;
    }
}

// Switch case for online course and offline course
switch ($course_info->category) {
    case 5:
        $sql_completion = 'SELECT id FROM course_completion WHERE courseid = ' . $course->id . ' and userid = ' . $USER->id;
        $compeletion_report = $DB->get_records_sql($sql_completion);

        if ($compeletion_report) {
            $txt_status = get_string('passstatus');
            $lbl_status = "label label-success";
        } else {
            $txt_status = get_string('inprogressstatus');
            $lbl_status = "label label-warning";
        }
        break;
    case 4:

        break;

    case 3:
        // $sql_completion = 'SELECT id FROM course_completion WHERE courseid = ' . $course->id . ' and userid = ' . $USER->id;
        // $compeletion_report = $DB->get_records_sql($sql_completion);

        // Set status for online course 
        //TODO => fail status
        if ($num_progress == 100) {
            $txt_status = get_string('passstatus');
            $lbl_status = "label label-success";
        } else {
            $txt_status = get_string('inprogressstatus');
            $lbl_status = "label label-warning";
        }
        break;
}

?>
    <style>
        #container {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        #course-summary-content {
            padding: 0 !important;
        }

        .learnnow {
            text-align: center;
        }

        #button_learnnow {
            display: inline-block;
        }
    </style>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0' />
    <div id="page-content" class="row pb-3">
        <div id="region-main-box" class="col-12">
            <div class="row" style="padding:0;">
                <div class="col-md-6 mb-2">
                    <img src="<?= $courserenderer->get_imgurl($course) ?>" class="img-res" onclick="window.location.href='/lms/course/view.php?id=<?= $course->id ?>'">
                </div>
                <div class="col-md-6 mb-2">
                    <!-- <h3 class="mt-3"><span class="badge badge-dark">Khóa học: <?= $course_info->shortname ?></span> <?= $course_info->fullname ?></h3> -->
                    <!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus eligendi sed, qui nostrum voluptates in.</p> -->

                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('coursecategorylabel') ?></small> <strong><?= $course_info->cat_name ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('teacherlisttext') ?></small> <?php foreach ($list_teacher as $teacher) { ?><strong><?= $teacher->firstname . " " . $teacher->lastname ?></strong> <?php } ?> </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('numberofstudent') ?></small> <strong><?= $num_members['student']->num ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('numberofactivity') ?></small> <strong><?= $num_modules ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('statuscourse') ?></small> <span class="<?= $lbl_status ?>"><?= $txt_status ?></span></li>
                    </ul>
                    <div class="card-footer border-top-0 px-0 bg-transparent">
                        <div class="progress bg-white border">
                            <div class="progress-bar bg-warning bar" role="progressbar" aria-valuenow="<?= $num_progress ?>" style="width: <?= $num_progress ?>%" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div>
                            <span class="sr-only">Tiến trình học:</span>
                            <strong><?= $num_progress ?></strong>% <?= get_string('progressbar') ?>
                        </div>
                    </div>
                    <p>
                        <div class="learnnow">
                            <button id="button_learnnow" type="button" class="btn btn-info py-3 px-4" style="font-size: 20px" onclick="window.location.href='/lms/course/view.php?id=<?= $course->id ?>'"><?= get_string('learnnow') ?></button>
                        </div>
                    </p>
                </div>
            </div>
            <h3 class="mt-4 bg-light p-3" style="border-left: 3px solid #bbb; margin-top:0px!important;"><?= get_string('courseinformation') ?></h3>
            <br>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col"><?= get_string('courseplace') ?></th>
                        <th scope="col"><?= get_string('coursestartdate') ?></th>
                        <th scope="col"><?= get_string('courseenddate') ?></th>
                        <th scope="col"><?= get_string('coursegradepass') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $course_info->course_place ?></td>
                        <td><?= gmdate("d-m-Y", $course_info->startdate) ?></td>
                        <td><?= gmdate("d-m-Y", $course_info->enddate) ?></td>
                        <td><?= round($course_info->gradepass, 2) ?></td>
                    </tr>
                </tbody>
            </table>
            <div id="course-summary-content">
                <?= $course_info->summary ?>
            </div>
        </div>
    </div>

    <?php

    echo $OUTPUT->footer();
