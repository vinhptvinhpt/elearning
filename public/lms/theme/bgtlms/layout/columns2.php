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
 * A two column layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');


if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$wwwroot = $CFG->wwwroot;
$pathLogo = $_SESSION["pathLogo"];
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$courseid = $PAGE->course->id;
$cmid = $PAGE->cm->id ? $PAGE->cm->id : 0;
$section = $PAGE->cm->section ? $PAGE->cm->section : 0;
$sectionname = '';
$pagelayout = $PAGE->pagelayout;
$bodyid = $OUTPUT->body_id();
$incourse = false;
$units = [];
$modules = [];
$courseurl = '';
$prevsectionno = 0;
$nextsectionno = 0;
$currentsectionno = 0;
$modulesidsstring = '';
$permission_edit = false;
$permission_editor = false;
$permission_tms = false;
$getPathPublic = '';
$imgCongra = '';
$roleInCourse = 0;
$finishCourse = false;
$pathBadge = 'images/default_badge.png';
$course_name = '';
$numOfModule = 0;
$numOfLearned = 0;

global $DB;
$sqlCheck = 'SELECT permission_slug, roles.name from `model_has_roles` as `mhr`
inner join `roles` on `roles`.`id` = `mhr`.`role_id`
left join `permission_slug_role` as `psr` on `psr`.`role_id` = `mhr`.`role_id`
inner join `mdl_user` as `mu` on `mu`.`id` = `mhr`.`model_id`
where `mhr`.`model_id` = ' . $USER->id . ' and `mhr`.`model_type` = "App/MdlUser"';

$check = $DB->get_records_sql($sqlCheck);

$viewCoursePage = false;

$permissions = array_values($check);

if ($pagelayout == 'incourse') {
    // Case course in library
    if($COURSE->category != '2'){
        // get role in course is teacher or trainee
        $sqlGetRole = 'SELECT me.roleid FROM mdl_course mc
        left join mdl_enrol me on mc.id = me.courseid
        left join mdl_user_enrolments mue on me.id = mue.enrolid
        where mc.id = ' . $courseid . ' and mue.userid = ' . $USER->id;

        $resultGetRole = $DB->get_records_sql($sqlGetRole);
        if(!empty($resultGetRole)) {
            $getRole = array_values($resultGetRole)[0];
            $roleInCourse = $getRole->roleid;
        } else {
            $roleInCourse = 5;
        }
    }

    require_once('courselib.php');
    $params = array('id' => $courseid);
    $units = get_course_contents($PAGE->course->id);
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    foreach ($units as $unit) {
        $sectionno = $unit['section'];
        if ($unit['id'] == $section) {
            $modules = $unit['modules'];
            $sectionname = $unit['name'];
            $currentsectionno = $sectionno;
        } else {
            if ($sectionno > $currentsectionno) {
                if ($nextsectionno == 0) {
                    $nextsectionno = $sectionno;
                }
            } else {
                $prevsectionno = $sectionno;
            }
        }
    }

    $sqlGetBadge = 'select path from image_certificate where type = 2 and is_active = 1';
    $getBadge = array_values($DB->get_records_sql($sqlGetBadge))[0];
    $pathBadge = $getBadge->path;
    $pathBadge = ltrim($pathBadge, $pathBadge[0]);
    $pathBadge = $CFG->wwwtmsbase . $pathBadge;
    //$viewCoursePage = true;
    //get progress learning
    $sqlGetProgress = 'select
    (select count(cm.id) as num
    from mdl_course_modules cm
    inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
    where cs.section <> 0
    and cm.completion <> 0
    and cm.course = mc.id) as numofmodule,

    (select count(cmc.coursemoduleid) as num
    from mdl_course_modules cm
    inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid
    inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id
    inner join mdl_course c on cm.course = c.id
    where cs.section <> 0
    and cmc.completionstate in (1, 2)
    and cm.course = mc.id
    and cm.completion <> 0
    and cmc.userid = ' . $USER->id . ') as numoflearned,
  	tcc.display,
  	mc.fullname
	from mdl_course mc
    left join mdl_enrol me on mc.id = me.courseid AND me.roleid = 5 AND `me`.`enrol` <> "self"
	left join mdl_user_enrolments mue on me.id = mue.enrolid AND mue.userid = ' . $USER->id . '
    left join tms_course_congratulations tcc on tcc.course_id = mc.id AND tcc.user_id = ' . $USER->id . '
	where mc.id = ' . $courseid;

    $progress = array_values($DB->get_records_sql($sqlGetProgress))[0];
    $course_name = $progress->fullname;
    $numOfModule = $progress->numofmodule;
    $numOfLearned = $progress->numoflearned;

    if ($numOfModule > 0) {
        if ($numOfModule == $numOfLearned && $progress->display != 1) {
            $finishCourse = true;
            $DB->execute("UPDATE tms_course_congratulations SET display=1 WHERE user_id = " . $USER->id . " and course_id = " . $courseid);
        }
    } else {
        $finishCourse = false;
    }

    $incourse = true;
    if (!empty($modules)) {
        $modulesidsstring = implode(',', array_column($modules, 'id'));
    }
    $course_category = $PAGE->course->category;
    foreach ($permissions as $permission) {
        if (!in_array($permission->name, ['student', 'employee'])) {
            $permission_edit = true;
            $permission_editor = true;
            break;
        }
        if ($permission->permission_slug == 'tms-educate-libraly-edit' && $course_category = 3) {
            $permission_edit = true;
            $permission_editor = true;
            break;
        }
        if ($permission->permission_slug == 'tms-educate-exam-offline-edit' && $course_category = 5) {
            $permission_edit = true;
            $permission_editor = true;
            break;
        }
        if ($permission->permission_slug == 'tms-educate-exam-online-edit' && $course_category != 3 && $course_category != 5) {
            $permission_edit = true;
            $permission_editor = true;
            break;
        }
    }
} else {
    $finishCourse = false;
    if ($bodyid == 'page-course-view')
        $viewCoursePage = true;
}

// TODO optimize check permission
foreach ($permissions as $permission) {
    if (in_array($permission->name, ['admin', 'root'])) {
        $roleInCourse = 0;
    }
    if (strpos(strtolower($permission->name), 'student') === false
        && strpos(strtolower($permission->name), 'employee') === false
        && strpos(strtolower($permission->name), 'executive') === false) {
        $permission_tms = true;
    }
    if ($permission->permission_slug == 'tms-educate-libraly-edit') {
        $permission_editor = true;
        break;
    }
    if ($permission->permission_slug == 'tms-educate-exam-offline-edit') {
        $permission_editor = true;
        break;
    }
    if ($permission->permission_slug == 'tms-educate-exam-online-edit') {
        $permission_editor = true;
        break;
    }
}

$getPathPublic = str_replace('lms', '', $wwwroot);

$editing = false;
if ($pagelayout == 'course' && strpos($bodyattributes, 'editing ') !== false) {
    $editing = true;
}

$top_bar_home = $bodyid == 'page-my-index' ? 'current-selected' : '';
$top_bar_course = '';
if ($bodyid == 'page-course-index' || $bodyid == 'page-course-view')
    $top_bar_course = 'current-selected';

$wwwtms = $CFG->wwwtms;

//color organization
$color = $_SESSION["color"];

//Check cookie
$permission_histaff = false;
$username = $USER->username;
$hrm_token = '';
$histaff_key = 'hrm_token';
$wwwhrm = $CFG->wwwhrm;

if(isset($_COOKIE[$histaff_key])) {
    if (strlen($_COOKIE[$histaff_key]) > 0) {
        $permission_histaff = true;
        $hrm_token = $_COOKIE[$histaff_key];
    }
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'courseid' => $courseid,
    'activityid' => $section,
    'cmid' => $cmid,
    'pagelayout' => $pagelayout,
    'incourse' => $incourse,
    'editing' => $editing,
    'units' => $units,
    'modules' => $modules,
    'courseurl' => $courseurl,
    'sectionname' => $sectionname,
    'prevsectionno' => $prevsectionno,
    'nextsectionno' => $nextsectionno,
    'modulesidsstring' => $modulesidsstring,
    'permission_edit' => $permission_edit,
    'permission_tms' => $permission_tms,
    'permission_editor' => $permission_editor,
    'permission_histaff' => $permission_histaff,
    'pathLogo' => $pathLogo,
    'getPathPublic' => $getPathPublic,
    'wwwroot' => $wwwroot,
    'top_bar_home' => $top_bar_home,
    'top_bar_course' => $top_bar_course,
    'wwwtms' => $wwwtms,
    'viewCoursePage' => $viewCoursePage,
    'color' => $color,
    'finishCourse' => $finishCourse,
    'pathBadge' => $pathBadge,
    'courseName' => $course_name,
    'roleInCourse' => $roleInCourse,
    'numOfLearned' => $numOfLearned,
    'numOfModule' => $numOfModule,
    'username' => $username,
    'hrm_token' => $hrm_token,
    'wwwhrm' => $wwwhrm,
    'section' => $section
];

$nav = $PAGE->flatnav;
// [VinhPT]
// Page custom layout course nav bar
$thispage = $nav->get_page();
$pagetype = $thispage->pagetype;
$collection_nav = $nav->get_navigation();
$first_node = $collection_nav[0]->key;

// Get role of account login
$context = context_course::instance($PAGE->course->id, MUST_EXIST);
// $role = current(get_user_roles($context, $USER->id))->shortname;

// Side bar for each role
if ($nav->get('coursehome') && $pagetype != "enrol-index") {
    $flag_rm = 0;
    foreach ($collection_nav as $node) {
        if ($flag_rm == 1) {
            if ($node->type == 20) {
                $nav->remove($node->key, 20);
            } else {
                $nav->remove($node->key);
            }
        }
        if ($node->key == "myhome") {
            $flag_rm = 1;
        }
    }
    // Move dashboard to first node
    $dashboard_node = $nav->get("myhome");
    $nav->remove("myhome");
    $nav->add($dashboard_node, $first_node);
    // Remove log if user is not admin
    if ($USER->id != 2) {
        $nav->remove("log");
    }

    // Add node description/introduction to course side bar
    $above_node = "coursehome";
    $root_url = "/course/description.php?id=" . $PAGE->course->id;
    $urlgr = new moodle_url($root_url);
    $array = array(
        'text' => get_string('introductionlabel'),
        'key' => 'intro',
        'action' => $urlgr,
        'icon' => new pix_icon('i/courseevent', ''),
    );
    $introduction = new navigation_node($array);
    $nav->add($introduction, $above_node);

    // // modify node participant to course side bar
    // $participants_node = $nav->get('participants');
    // $participants_url = "/course/participants.php?id=" . $PAGE->course->id;
    // $url_par = new moodle_url($participants_url);
    // $participants_node->action = $url_par;

    // add node nội dung khoá học
    $array_content = array(
        'text' => get_string('coursecontentlabel'),
        'key' => 'content_course',
        'icon' => new pix_icon('e/insert_row_after', ''),
        'submenu' => 'submenu-collapse'
    );
    $content = new navigation_node($array_content);
    $nav->add($content);
    $node_course_home = $nav->get("coursehome");
    $nav->remove("coursehome");
    // $node_course_home->text = 'Mục lục';
    // $node_course_home->submenu = 'submenu-collapse-items';
    // $node_course_home->set_parent($nav->get("content_course"));
    // $nav->add($node_course_home);
    foreach ($collection_nav as $node) {
        if ($node->type == 30) {
            $node->set_parent($nav->get("content_course"));
            $node->submenu = 'submenu-collapse-items';
            $nav->remove($node->key);
            $nav->add($node);
        }
    }

    $node_log = $nav->get('log');
    $node_log->text = get_string('activityloglabel');
    // if ($nav->get('log')) {
    //     $above_node = "grades";
    //     $root_url = "/course/module_log.php?id=" . $PAGE->course->id;
    //     $urlgr = new moodle_url($root_url);
    //     $array = array(
    //         'text' => get_string('activityloglabel'),
    //         'key' => 'activitylog',
    //         'action' => $urlgr,
    //         'icon' => new pix_icon('i/permissions', ''),
    //     );
    //     $grade_offline = new navigation_node($array);
    //     $nav->add($grade_offline, $above_node);
    // }
}

// Case category of course is category "Khoá học tập trung" (categoryid = 3)
// Add mark grade for offline courses
if ($PAGE->course->category == "5") {
    if ($nav->get('log')) {
        $above_node = "grades";
        $root_url = "/course/grade_offline.php?id=" . $PAGE->course->id;
        $urlgr = new moodle_url($root_url);
        $array = array(
            'text' => get_string('offlinegradelabel'),
            'key' => 'offgrade',
            'action' => $urlgr,
            'icon' => new pix_icon('i/permissions', ''),
        );
        $grade_offline = new navigation_node($array);
        $nav->add($grade_offline, $above_node);
    }
    // Check attendance for student enrolled in offline courses
    if ($nav->get('log')) {
        $above_node = "offgrade";
        $url = "/course/attendance_offline.php?id=" . $PAGE->course->id;
        $urlat = new moodle_url($url);
        $array = array(
            'text' => get_string('attendancelabel'),
            'key' => 'offattendance',
            'action' => $urlat,
            'icon' => new pix_icon('e/template', ''),
        );
        $grade_offline = new navigation_node($array);
        $nav->add($grade_offline, $above_node);
    }
}

// // Back to TMS
// $isadmin = is_siteadmin($USER);
// if ($isadmin) {
//     $first_node_final = "myhome";
//     $root_url = $CFG->wwwroot;
//     $root_url = trim($root_url, "lms") . "dashboard";
//     $urltms = new moodle_url($root_url);
//     $itemarray = array(
//         'text' => get_string("tmssite"),
//         'key' => 'sitetms',
//         'action' => $urltms,
//         'icon' => new pix_icon('i/outcomes', ''),
//     );
//     $tms_node = new navigation_node($itemarray);
//     $nav->add($tms_node, $first_node_final);
// }

// Add collapse menu for side bar dashboard
// if ($nav->get('mycourses')) {
//     $nav->get('mycourses')->submenu = 'submenu-collapse';
//     foreach ($collection_nav as $node) {
//         if ($node->type == "20") {
//             $node->submenu = 'submenu-collapse-items';
//         }
//     }
// }

// [VinhPT][11.12.2019] Remove mycourse node from side bar
if ($nav->get('mycourses')) {
    foreach ($collection_nav as $node) {
        if ($node->type == "20") {
            $nav->remove($node->key);
        }
    }
    $nav->remove("mycourses");
}
// [VinhPT][12.11.2019] Page vietlot_introduction and Guideline
if ($pagetype == "my-index") {
    // Vietlot introduction node
    $above_node = "calendar";
    $url = "/my/vietlot_introduction.php";
    $urlvl = new moodle_url($url);
    $array = array(
        'text' => get_string('vietlot_introduction'),
        'key' => 'vietlot_introduction',
        'action' => $urlvl,
        'icon' => new pix_icon('i/info', ''),
    );
    $vietlot = new navigation_node($array);
    $nav->add($vietlot);

    // Guideline introduction node
    $above_node = "calendar";
    $url = "/my/guideline.php";
    $urlg = new moodle_url($url);
    $array = array(
        'text' => get_string('guideline'),
        'key' => 'guideline',
        'action' => $urlg,
        'icon' => new pix_icon('i/files', ''),
    );
    $guide = new navigation_node($array);
    $nav->add($guide);

    // Survey node
    $above_node = "calendar";
    $url = "/my/surveydashboard.php";
    $urls = new moodle_url($url);
    $array = array(
        'text' => get_string('surveydashboard'),
        'key' => 'surveydashboard',
        'action' => $urls,
        'icon' => new pix_icon('i/questions', ''),
    );
    $guide = new navigation_node($array);
    $nav->add($guide);
}

// [VinhPT][Easia][16.04.2020] Change list user has permission to access TMS
$can_access_tms = false;
global $DB;
$sql_check_role = "SELECT redirect_type FROM mdl_user WHERE id = " . $USER->id;
try {
    $result_check = array_values($DB->get_records_sql($sql_check_role))[0];
    if ($result_check != "lms") {
        $can_access_tms = true;
    }
} catch (Exception $e) {
    $can_access_tms = false;
}

if (is_siteadmin($USER)) {
    $can_access_tms = true;
}

if ($can_access_tms) {
    $first_node_final = "myhome";
    $root_url = $CFG->wwwroot;
    $root_url = trim($root_url, "lms") . "tms/dashboard";
    $urltms = new moodle_url($root_url);
    $itemarray = array(
        'text' => get_string("tmssite"),
        'key' => 'sitetms',
        'action' => $urltms,
        'icon' => new pix_icon('i/outcomes', ''),
    );
    $tms_node = new navigation_node($itemarray);
    $nav->add($tms_node, $first_node_final);
}

$dashboard_node = $nav->get('myhome');
$dashboard_node->text = get_string('dashboardtext');
$Sitehome_node = $nav->get('home');
$Sitehome_node->text = get_string('listcourseavailable');

$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();
echo $OUTPUT->render_from_template('theme_bgtlms/columns2', $templatecontext);
