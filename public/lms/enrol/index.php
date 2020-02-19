<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This page shows all course enrolment options for current user.
 *
 * @package    core_enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->libdir/formslib.php");

$id = required_param('id', PARAM_INT);
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);

if (!isloggedin()) {
    $referer = get_local_referer();
    if (empty($referer)) {
        // A user that is not logged in has arrived directly on this page,
        // they should be redirected to the course page they are trying to enrol on after logging in.
        $SESSION->wantsurl = "$CFG->wwwroot/course/view.php?id=$id";
    }
    // do not use require_login here because we are usually coming from it,
    // it would also mess up the SESSION->wantsurl
    redirect(get_login_url());
}

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

// Everybody is enrolled on the frontpage
if ($course->id == SITEID) {
    redirect("$CFG->wwwroot/");
}

if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
    print_error('coursehidden');
}

$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/enrol/index.php', array('id' => $course->id));

// do not allow enrols when in login-as session
if (\core\session\manager::is_loggedinas() and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
    print_error('loginasnoenrol', '', $CFG->wwwroot . '/course/view.php?id=' . $USER->loginascontext->instanceid);
}

// Check if user has access to the category where the course is located.
if (!core_course_category::can_view_course_info($course) && !is_enrolled($context, $USER, '', true)) {
    print_error('coursehidden', '', $CFG->wwwroot . '/');
}

// get all enrol forms available in this course
$enrols = enrol_get_plugins(true);
$enrolinstances = enrol_get_instances($course->id, true);
$forms = array();
foreach ($enrolinstances as $instance) {
    if (!isset($enrols[$instance->enrol])) {
        continue;
    }
    $form = $enrols[$instance->enrol]->enrol_page_hook($instance);
    if ($form) {
        $forms[$instance->id] = $form;
    }
}

// Check if user already enrolled
if (is_enrolled($context, $USER, '', true)) {
    if (!empty($SESSION->wantsurl)) {
        $destination = $SESSION->wantsurl;
        unset($SESSION->wantsurl);
    } else {
        $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    }
    redirect($destination);   // Bye!
}

$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('enrolmentoptions', 'enrol'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('enrolmentoptions', 'enrol'));

// $courserenderer = $PAGE->get_renderer('core', 'course');
// echo $courserenderer->course_info_box($course);

// // Add description to top of course content
$courserenderer = $PAGE->get_renderer('core', 'course');
// echo $courserenderer->course_info_box_course_view($course);

//TODO: find if future enrolments present and display some info
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

// Get list teacher of course
$sql_teachers = "
select
        userid,
        U.firstname,
		U.lastname
	from
		mdl_user_enrolments UE
	inner join mdl_enrol E on
		UE.enrolid = E.id
	inner join mdl_user U on
		UE.userid = U.id
	where
        E.courseid = " . $course->id . "";
$list_enroll = array_values($DB->get_records_sql($sql_teachers));
$list_teacher = array();
$list_student = array();
$role_teacher = array("teacher", "editingteacher");
foreach ($list_enroll as $user) {
    if (in_array(current(get_user_roles($context, $user->userid))->shortname, $role_teacher)) {
        array_push($list_teacher, $user);
    } elseif (current(get_user_roles($context, $user->userid))->shortname == 'student') {
        array_push($list_student, $user);
    }
}

// Get number of total modules of course
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
        and cmc.completionstate = 1
	and cm.course = " . $course->id . "
	and cmc.userid = " . $USER->id . "
";
$num_learned = (int) array_values($DB->get_records_sql($sql_learned))[0]->num;
$num_progress = round(($num_learned / $num_modules) * 100, 2);
if (is_nan($num_progress)) {
    $num_progress = 0;
}
?>





<div class="hidden-form">
    <?php
    foreach ($forms as $form) {
        echo $form;
    }
    ?>
</div>

<script>
    var mform = document.getElementsByClassName("generalbox")[0];
    if (typeof mform !== 'undefined') {

        var in_form = mform.getElementsByTagName("form")[0].getAttribute("id");
    }

    window.onload = init;

    function init() {
        if (typeof mform !== 'undefined') {
            document.getElementById("button_register").setAttribute("form", in_form);
        }
        if (document.getElementById("button_register").getAttribute("form") === null) {
            document.getElementById("button_register").setAttribute("href", "#myModal");
            document.getElementById("button_register").setAttribute("data-toggle", "modal");
        }
    }
</script>

<style>
    .hidden-form {
        display: none;
    }

    .activity img.activityicon {
        position: inherit;
    }

    .section .activity .activityinstance>a {
        text-indent: 0;
        pointer-events: none;
        cursor: default;
        text-decoration: none;
        color: black;
    }

    .autocompletion {
        display: none;
    }

    .tabs-content {
        margin-right: 15px;
        margin-left: 15px;
    }

    #container {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
    }

    .register {
        text-align: center;
    }

    #button_register {
        display: inline-block;
    }

    /*  */
    .modal-confirm {
        color: #636363;
        max-width: 440px;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%) !important;
        -webkit-transform: translate(-50%, -50%) !important;
        -o-transform: translate(-50%, -50%) !important;
        -moz-transform: translate(-50%, -50%) !important;
        -ms-transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        padding: 20px;
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
        background: #d42c2c;
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
        background: #d42c2c;
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

    #course-information {
        padding: 0 !important;
    }

    #course-summary-content {
        margin-left: 16px;
        margin-right: 16px;
    }

    /* End modal */
</style>

<div id="page-content" class="row pb-3">
    <div id="region-main-box" class="col-12">
        <div class="media border bg-light rounded" style="padding:0!important;">
            <div id="container">
                <div class="col-md-6" style="padding:15px;">
                    <img src="<?= $courserenderer->get_imgurl($course) ?>" class="img-res" onclick="window.location.href='/lms/course/view.php?id=<?= $course->id ?>'">
                </div>
                <div class="col-md-6">
                    <!-- <h3 class="mt-3"><span class="badge badge-dark">Khóa học: <?= $course_info->shortname ?></span> <?= $course_info->fullname ?></h3> -->
                    <!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus eligendi sed, qui nostrum voluptates in.</p> -->
                    <p>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('coursecategorylabel') ?></small> <strong><?= $course_info->cat_name ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('teacherlisttext') ?></small> <?php foreach ($list_teacher as $teacher) { ?><strong><?= $teacher->firstname . " " . $teacher->lastname ?></strong> <?php } ?> </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('numberofstudent') ?></small> <strong><?= count($list_student) ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center"><small><?= get_string('numberofactivity') ?></small> <strong><?= $num_modules ?></strong></li>
                        </ul>

                        <p>
                            <div class="register">
                                <button id="button_register" type="submit" class="btn btn-info py-3 px-4" style="font-size: 20px"><?= get_string('registercourse') ?></button>
                            </div>
                            <!-- Modal HTML -->
                            <div id="myModal" class="modal fade">
                                <div class="modal-dialog modal-confirm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="icon-box">
                                                <i class="fa fa-close" aria-hidden="true"></i>
                                            </div>
                                            <h4 class="modal-title" style="margin-top: 20px;margin-left: auto;margin-right: auto;"><?= get_string('registerfail') ?></h4>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-info btn-block" data-dismiss="modal" onClick="window.location.reload();">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </p>
                </div>
            </div>
        </div>
        <br>
        <!-- <h3 class="mt-4 bg-light p-3" style="border-left: 3px solid #bbb">Thông tin chi tiết khóa học.</h3> -->
        <div id="course-information">
            <div class="row">
                <div class="tabs-content">
                    <div class="list-tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link text-center active" href="#information" role="tab" data-toggle="tab"><span><?= get_string('courseinformation') ?></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-center" href="#courseoutline" role="tab" data-toggle="tab"><span><?= get_string('courseoutlinelabel') ?></span></a>
                            </li>
                        </ul>
                        <br>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane in active" id="information">

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
                <!-- <h3 class="mt-4 bg-light p-3" style="border-left: 3px solid #bbb">Nội dung khoá học.</h3> -->

                <div role="tabpanel" class="tab-pane" id="courseoutline">
                    <div id="course-info-outline">
                        <?php
                        // Course wrapper start.
                        echo html_writer::start_tag('div', array('class' => 'course-content'));

                        // make sure that section 0 exists (this function will create one if it is missing)
                        course_create_sections_if_missing($course, 0);

                        // get information about course modules and existing module types
                        // format.php in course formats may rely on presence of these variables
                        $modinfo = get_fast_modinfo($course);
                        $sections = $modinfo->get_section_info_all();

                        // Include the actual course format.
                        require($CFG->dirroot . '/course/format/' . $course->format . '/format.php');
                        // Content wrapper end.

                        echo html_writer::end_tag('div');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="hidden-form">

    <?php

    if (!$forms) {
        if (isguestuser()) {
            notice(get_string('noguestaccess', 'enrol'), get_login_url());
        } else if ($returnurl) {
            notice(get_string('notenrollable', 'enrol'), $returnurl);
        } else {
            $url = get_local_referer(false);
            if (empty($url)) {
                $url = new moodle_url('/index.php');
            }
            notice(get_string('notenrollable', 'enrol'), $url);
        }
    }

    ?>
</div>
<?php
echo $OUTPUT->footer();
?>