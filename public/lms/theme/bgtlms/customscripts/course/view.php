<?php
if (!isloggedin()) {
    require_login();
}
?>
<html>
<title>Detail course <?php echo $course->fullname; ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="js/popper.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="//unpkg.com/vue-plain-pagination@0.2.1"></script>

<style>
    @font-face {
        font-family: Roboto-Bold;
        src: url('fonts/Roboto-Bold.ttf');
    }

    @font-face {
        font-family: Roboto-Regular;
        src: url('fonts/Roboto-Regular.ttf');
    }

    img {
        width: 100%;
    }

    body {
        /*font-size: 14px !important;*/
        font-family: Roboto-Bold;
    }

    ul {
        list-style: none;
    }

    a {
        text-decoration: none;
    }

    #page {
        margin-top: 50px !important;
    }

    #page-wrapper .navbar {
        padding: 7px 1rem 9px .5rem !important;
    }

    .navbar .count-container {
        top: 2px !important;
    }

    /*    view*/
    .alert-block {
        opacity: 1 !important;
    }

    .modal-body {
        margin: 0 auto;
    }

    .modal-body img {
        max-width: 400px;
    }

    .modal-title{
        font-size: 17px;
        font-weight: 400;
    }
    #page {
        margin-right: 4%;
        /*margin-right: */
    <?//=$marginPage?> /*;*/
    }

    .div-import{
        width: 100%;
        display: inline-flex;
    }

    .file-import{

    }

    .file-import a{
        font-size: inherit;
        font-style: italic;
    }

    .import-student-score .container {
        padding: 2%;
        /*display: inline-flex;*/
    }

    .btn-up-file {
        width: 100%;
        height: 100%;
    }

    .list-point-toeic {
        width: 150px;
        /* text-align: center; */
        margin: 0 auto;
    }

    .list-point-toeic li {
        width: 100%;
        padding: 5%;
    }

    .list-point-toeic li span {
        font-family: Roboto;
        color: #0056b3;
    }

    .title-part {
        float: left;
        width: 50%;
        text-align: left;
    }

    .score-part {
        font-weight: bold;
        color: #000000 !important;
        float: left;
        width: 50%;
        text-align: right;
    }

    .score-total {
        color: #ff0000 !important;
    }

    #page-wrapper #page {
        margin-right: 0;
    }

    .alert {
        padding: 20px;
        background-color: #f44336;
        color: white;
    }

    .sp-name-course {
        font-size: 32px;
        position: absolute;
        width: 80%;
        text-align: center;
        margin-left: 0;
        margin-right: 0;
        right: 0;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }

    .prev-btn:hover, #menu-edit:hover {
        cursor: pointer;
    }

    .progress-bar {
        background-color: <?=$_SESSION["color"]?> !important;
    }

    .progress-info {
        overflow: hidden;
    }

    .progress-info__title span {
        text-align: left;
        letter-spacing: 0.8px;
        color: #202020;
        font-size: 23px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .info-course-detail {
        padding: 0;
    }

    .info-course-detail ul {
        display: inline-flex;
        padding: 0;
        width: 100%;
    }

    .info-course-detail ul li {
        margin-right: 8%;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #737373;
        font-size: 14px !important;
    }

    .info-course-progress > span {
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #202020;
        text-transform: uppercase;
    }

    .info-course-progress .col-9 {
        position: absolute;
        bottom: 32px;
        right: 0;
    }

    .btn-click {
        background: <?=$_SESSION["color"]?> 0% 0% no-repeat padding-box !important;
        border-radius: 4px;
        text-align: left;
        font-family: Roboto;
        letter-spacing: 0.45px;
        color: #FFFFFF !important;
        text-transform: uppercase;
    }

    .nav-course .nav {
        width: 100%;
        margin: auto;
    }

    .nav-first-tab {
        margin: 0 auto;
        margin-right: 0;
    }

    .nav-setting {
        margin-right: 15px;
        margin-top: 8px;
    }

    .setting-link {
        color: <?=$_SESSION["color"]?> !important;
        border: 1px solid<?=$_SESSION["color"]?>;
        padding: 5px;
        border-radius: 15px;
    }

    #menu-edit::after {
        display: none;
    }

    .nav-tab-last {
        margin: 0 auto;
        margin-left: 0;
    }

    .nav-course .nav .nav-item a {
        text-align: left;
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #737373;
    }

    .nav-course .nav li.active a {
        color: #202020;
    }

    .nav-course .nav .nav-item a.active {
        color: #202020;
        font-weight: 700;
    }

    .section-nav {
        border-top: 1px solid #C7C7C7;
        margin: 1% 0;
        margin-bottom: 0;
    }

    .section-course-info {
        background-color: #F1F1F1;
        padding: 2% 0;
    }

    .course-main {
        background-color: #FFFFFF;
        padding: 2%;
    }

    .course-block {
        margin-bottom: 2em;
        padding: 2%;
    }

    .course-block__title {
        font-size: 23px;
        letter-spacing: 0.8px;
        color: #202020;
    }

    .course-block__title p {
        margin-bottom: 0.5em;
    }

    .course-block__content, .course-block__content p, .course-block__content ul li {
        /*font-family: Roboto-Regular;*/
        font-family: Roboto;
        font-size: 13px;
        letter-spacing: 0.99px;
        color: #202020;
        opacity: 1;
        margin: 0;
    }

    .course-block__content ul li {
        list-style: disc;
    }

    .list-outcome {
        padding: 0 15px;
    }

    .course-content {
        display: none;
    }

    #courseunit, #toeicresult, #toeicadmin {
        display: none;
    }

    .main-detail {
        display: none;
        display: none;
    }

    .detail-list li {
        font-family: Roboto-Regular;
        font-size: 14px;
        letter-spacing: 0.99px;
        margin: 2% 0;
    }

    .detail-list li i {
        font-size: 23px;
        margin-right: 1%;
        color: <?=$_SESSION["color"]?>;
    }

    .detail-list li a {
        font-family: Roboto-Regular;
        font-size: 14px;
        letter-spacing: 0.99px;
        color: #333;
    }

    .detail-list li.li-module-done a, .detail-list li.li-module-done i {
        color: #00A426;
    }

    .detail-btn {
        text-align: right;
        width: 100%;
        padding-bottom: 3%;
    }

    .detail-content {
        padding: 2% 0;
        border-top: 1px solid #C7C7C7;
        border-bottom: 1px solid #C7C7C7;
        margin-bottom: 3%;
    }

    .detail-title {
        padding: 2% 0;
    }

    .detail-title p {
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
        opacity: 1;
        margin: 0;
    }

    .unit {
        border-radius: 4px;
        margin-bottom: 3%;
        position: relative;
        border: 2px solid #FFFFFF;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        overflow: hidden;
    }

    .unit:hover {
        cursor: pointer;
        box-shadow: 3px 3px 6px #00000029;
    }

    .unit-click {
        border: 2px solid <?=$_SESSION["color"]?> !important;
    }

    .unit-done {
        /*border: 2px solid #378449;*/
    }

    .unit-done .unit__title {
        /*background: #378449 0% 0% no-repeat;*/
    }

    .unit-done .unit__title p {
        /*color: #ffffff;*/
    }

    .unit-done .unit__icon i {
        color: #378449;
    }

    .unit-click .unit__title {
        background: <?=$_SESSION["color"]?> 0% 0% no-repeat;
    }

    .unit-click .unit__title p {
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #FFFFFF;
    }

    .unit-click .unit__icon i {
        color: #00A426;
    }

    .unit-learning .unit__title p {
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
    }

    .unit-learning .unit__icon i {
        color: #ff000e;
    }

    .unit__title {
        padding: 2%;
        background-color: #FFFFFF;
    }

    .unit__title p {
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #737373;
    }


    .unit__progress {
        border-top: 1px solid #3333;
    }

    .unit__icon i {
        position: absolute;
        color: #737373;
        font-size: 17px;
        border-radius: 50%;
        padding: 2%;
        top: 42%;
        background-color: #FFFFFF;
        right: 7%;
        box-shadow: 3px 3px 6px #00000029;
    }

    .unit__progress-number {
        padding: 2%;
        color: #737373;
        font-family: Roboto-Regular;
    }

    .unit__progress-number p {
        margin: 0;
    }

    .unit__progress-number i {
        letter-spacing: 0.5px;
        font-weight: 700;
    }

    .percent-get {
        letter-spacing: 0.5px;
    }

    .percent-total {
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
    }

    .unit-detail {
        background-color: #FFFFFF;
        height: fit-content;
    }

    .prev-btn {
        font-size: 23px;
        font-weight: 700;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 3px 3px 6px #0000002E;
        border: 1px solid #707070;
        border-radius: 4px;
    }

    .prev-btn i {
        padding: 1%;
        color: #3E3E3E;
    }

    .course-block__content-answer {
        margin-top: 3%;
    }

    .speech-bubble {
        position: relative;
        background: <?=$_SESSION["color"]?>;
        border-radius: 4px;
        min-width: 50px;
        width: fit-content;
        padding: 1px 5px;
        margin: 0;
        margin-bottom: 1em;
        text-align: center;
        color: white;
        font-weight: bold;
        text-shadow: 0 -0.05em 0.1em rgba(0, 0, 0, .3);
    }

    .speech-bubble:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 60%;
        width: 0;
        height: 0;
        border: 15px solid transparent;
        border-top-color: <?=$_SESSION["color"]?>;
        border-bottom: 0;
        margin-left: -20px;
        margin-bottom: -10px;
    }

    .progress {
        height: 0.5em !important;
        border-radius: 0 !important;
    }

    .number-module {
        font-size: 15px;
    }

    .info-course-btn {
        padding: 2% 1%;
        text-align: right;
    }

    .course-block-img img {
        border-radius: 3%;
    }

    .setting-option {
        padding: 0 1rem;
    }

    @media only screen and (max-width: 1368px) {
        .block {
            display: contents;
        }

        .info-course-detail ul li {
            font-size: 13px !important;
        }
    }

    @media only screen and (max-width: 1024px) {
        .block {
            display: contents;
        }

        .info-course-detail ul li {
            font-size: 12px !important;
        }
    }

    @media only screen and (max-width: 991px) {
        .block {
            display: contents;
        }

        .info-course-detail ul li {
            font-size: 12px !important;
        }

        .info-course-progress > span {
            font-size: 11px;
        }

        .info-course-detail ul {
            display: -webkit-inline-box;
        }
    }

    @media only screen and (max-width: 768px) {
        .info-course-detail ul li {
            font-size: 12px !important;
        }

        .info-course-detail, .info-course-detail ul {
            max-width: 100% !important;
        }

        .block {
            display: contents;
        }

        .progress-info, .btn-click {
            font-size: 10px;
        }

        .info-course-progress {
            display: block;
            max-width: 92% !important;
        }

        .info-course-btn {
            max-width: 90% !important;
        }

        .info-course-progress .col-9 {
            bottom: 24px;
        }

        .unit__title p, .unit-learning .unit__title p, .unit-click .unit__title p {
            font-size: 14px;
        }

        #user-notifications .alert-warning {
            opacity: 1 !important;
        }

        .progress-info__content .row {
            display: block;
        }

        .detail-list li a {
            padding-left: 6%;
        }

        .nav-tabs-courses .nav-click {
            margin: 0 auto;
        }

        .nav-course .nav .nav-item a {
            font-size: 17px;
        }

        .nav-introduction {
            margin-right: 0 !important;
        }

        .nav-unit {
            margin-left: 0 !important;
        }

        .nav-setting {
            margin-top: 8px !important;
        }

        .custom-file-select{
            width: 85% !important;
        }

        .custom-file-btn{
            width: 15% !important;
        }
    }

    @media only screen and (max-width: 480px) {
        .progress-info, .btn-click {
            margin-top: 8%;
        }

        .info-course-btn {
            padding-top: 0;
        }

        .btn-start-course {
            margin-top: 5%;
        }

        .nav-tabs-courses .nav-click {
            margin: 0 auto;
        }

        .nav-course .nav .nav-item a {
            font-size: 14px;
        }

        .nav-introduction {
            margin-right: 0 !important;
        }

        .nav-unit {
            margin-left: 0 !important;
        }

        .detail-list li a {
            padding-left: 12%;
        }

        .info-course-progress {
            margin-top: 10px;
        }
    }

    @media only screen and (max-width: 320px) {
        .info-course-progress > span {
            font-size: 10px;
        }

        .info-course-progress .col-9 {
            bottom: 19px;
        }

        .course-block-info {
            max-width: 100%;
            width: 100%;
            display: contents;
        }

        .course-block-img {
            display: none;
        }

        .detail-list li a {
            padding-left: 18%;
            font-size: 13px;
        }

        .detail-title p {
            font-size: 13px;
        }

        .unit__title p, .unit-learning .unit__title p, .unit-click .unit__title p {
            font-size: 11px;
            word-break: break-word;
        }

        .unit__progress-number p {
            font-size: 11px;
        }

        .unit-info {
            padding-left: 0 !important;
        }

        #courseunit {
            padding: 0;
            margin: 0;
        }

        .btn-start-unit {
            font-size: 11px !important;
            padding: 5px !important;
        }

        .nav-course .nav {
            display: block;
            margin-bottom: 15px;
        }
    }

</style>
<?php
require_once("courselib.php");
session_start();
$edit = optional_param('edit', -1, PARAM_BOOL);
$notifyeditingon = optional_param('notifyeditingon', -1, PARAM_BOOL);
$id = optional_param('id', 0, PARAM_INT);

// Set $USER->editing = 0 to switch to normal view in course
if ($edit == 0) {
    $USER->editing = 0;
}

// Switch to edit mode
if ($notifyeditingon == 1) {
} else if ($edit == 1 || $USER->editing == 1) {
    $USER->editing = 1;
    $url = new moodle_url("/course/viewedit.php?id=" . $id, array('notifyeditingon' => 1));
    redirect($url);
}

// [VinhPT][EAsia] Course IP address restrict
$result_ip = array_values($DB->get_records_sql("Select access_ip from mdl_course where id = " . $id))[0]->access_ip;
$root_url = $CFG->wwwroot;

$sqlUserCheck = 'SELECT id FROM tms_user_course_exception where user_id = ' . $USER->id . ' AND course_id = ' . $id;

$userCheck = array_values($DB->get_records_sql($sqlUserCheck));

$sql = 'SELECT mc.id, mc.fullname, mc.category, mc.course_avatar, mc.estimate_duration, mc.summary, mc.is_toeic, ( SELECT COUNT(mcs.id) FROM mdl_course_sections mcs WHERE mcs.course = mc.id AND mcs.section <> 0) AS numofsections, ( SELECT COUNT(cm.id) AS num FROM mdl_course_modules cm INNER JOIN mdl_course_sections cs ON cm.course = cs.course AND cm.section = cs.id WHERE cs.section <> 0 AND cm.course = mc.id) AS numofmodule, ( SELECT COUNT(cmc.coursemoduleid) AS num FROM mdl_course_modules cm INNER JOIN mdl_course_modules_completion cmc ON cm.id = cmc.coursemoduleid INNER JOIN mdl_course_sections cs ON cm.course = cs.course AND cm.section = cs.id INNER JOIN mdl_course c ON cm.course = c.id WHERE cs.section <> 0 AND cmc.completionstate <> 0 AND cm.course = mc.id AND cmc.userid = ' . $USER->id . ') AS numoflearned, mp.display FROM mdl_course mc LEFT JOIN tms_course_congratulations mp on mc.id = mp.course_id WHERE mc.id = ' . $id;
$course = array_values($DB->get_records_sql($sql))[0];
//
//$course->numoflearned * 100 / $course->numofmodule;
$course_numoflearned = 0;
$course_numofmodule = 0;
$percent_learned = 0;
if($course->numofmodule > 0){
    $course_numoflearned = $course->numoflearned;
    $course_numofmodule = $course->numofmodule;
    $percent_learned = round($course_numoflearned*100/$course_numofmodule);
}

//
$teachers_sql = 'select @s:=@s+1 stt,
muet.userid as teacher_id,
tud.fullname as teacher_name,
toe.position,
tor.name as organization_name,
muet.timecreated as teacher_created
from mdl_course mc
left join mdl_enrol met on mc.id = met.courseid AND met.roleid = 4
left join mdl_user_enrolments muet on met.id = muet.enrolid
left join tms_user_detail tud on tud.user_id = muet.userid
left join tms_organization_employee toe on toe.user_id = muet.userid
left join tms_organization tor on tor.id = toe.organization_id, (SELECT @s:= 0) AS s
where mc.id = ' . $id;


$teacher_name = '';
$teacher_created = 0;
$teacher_position = '';
$teacher_organization = '';
$teachers = array_values($DB->get_records_sql($teachers_sql));

foreach ($teachers as $teacher) {
    if (intval($teacher->teacher_created) > $teacher_created) {
        $teacher_created = $teacher->teacher_created;
        $teacher_name = $teacher->teacher_name;
        $teacher_position = $teacher->position;
        $teacher_organization = $teacher->organization_name;
    }
}

$units = get_course_contents($id);

$start_course_link = '';

if (!empty($units)) {
    foreach ($units as $unit) {
        $modules = $unit['modules'];
        if ($modules) {
            $start_course_link = $modules[0]['url'];
            break;
        }
    }
}

//echo $start_course_link;die;

$bodyattributes = 'id="page-course-view-topics" class="pagelayout-course course-' . $id . '"';

//
$permission_admin = false;
//Check permission edit course
$permission_edit = false;
$course_category = $course->category;

$sqlCheck = 'SELECT permission_slug, roles.name from `model_has_roles` as `mhr`
inner join `roles` on `roles`.`id` = `mhr`.`role_id`
left join `permission_slug_role` as `psr` on `psr`.`role_id` = `mhr`.`role_id`
inner join `mdl_user` as `mu` on `mu`.`id` = `mhr`.`model_id`
where `mhr`.`model_id` = ' . $USER->id . ' and `mhr`.`model_type` = "App/MdlUser"';

$check = $DB->get_records_sql($sqlCheck);

$permissions = array_values($check);

foreach ($permissions as $permission) {

    if (in_array($permission->name, ['root', 'admin'])) { //Nếu admin => full quyền
        $permission_edit = true;
        $permission_admin = true;
        break;
    }

    if ($permission->permission_slug == 'tms-educate-libraly-edit' && $course_category = 3) {
        $permission_edit = true;
        break;
    }
    if ($permission->permission_slug == 'tms-educate-exam-offline-edit' && $course_category = 5) {
        $permission_edit = true;
        break;
    }
    if ($permission->permission_slug == 'tms-educate-exam-online-edit' && $course_category != 3 && $course_category != 5) {
        $permission_edit = true;
        break;
    }
}
//Check section
$section_no = isset($_REQUEST['section_no']) ? $_REQUEST['section_no'] : '';
$source = isset($_REQUEST['source']) ? $_REQUEST['source'] : '';
if ($edit == 0) {
    $source = $id;
}

//Check to show popup congratulation
//select image badge active
$sqlGetBadge = 'select path from image_certificate where type = 2 and is_active = 1';
$getBadge = array_values($DB->get_records_sql($sqlGetBadge))[0];
$pathBadge = $getBadge->path;
$pathBadge = ltrim($pathBadge, $pathBadge[0]);
if (empty($pathBadge))
    $pathBadge = 'images/default_badge.png';
//$_SESSION["displayPopup"] = 1;
//-1 chưa xem. 0 chưa có bản ghi trong db, 1 xem, 2 đã xem
if ($course->numofmodule == 0) {
    $_SESSION["displayPopup"] = 0;
} else {
    $percentProgress = $course->numoflearned / $course->numofmodule;
    $displayVal = $course->display;
//if percent of progress = 1 is complete course => display popup congratulation
    if ($percentProgress == 1) {
        if ($displayVal == 0) {
            $DB->execute("INSERT INTO tms_course_congratulations (user_id, course_id, display) VALUES (" . $USER->id . ", " . $course->id . ", 1)");
            $_SESSION["displayPopup"] = 1;
        } else if ($displayVal == -1) {
            //get course congratulation
            $sqlGetCourseNotification = 'select count(*) as total from tms_course_congratulations WHERE user_id = ' . $USER->id . ' and course_id = ' . $course->id;
            $getCourseNotification = array_values($DB->get_records_sql($sqlGetCourseNotification))[0];
            $courseNotification = $getCourseNotification->total;
            if ($courseNotification == 0) {
                $test = $DB->execute("INSERT INTO tms_course_congratulations (user_id, course_id, display) VALUES (" . $USER->id . ", " . $course->id . ", 1)");
                $_SESSION["displayPopup"] = 1;
            } else {
                $DB->execute("UPDATE tms_course_congratulations SET display=1 WHERE user_id = " . $USER->id . " and course_id = " . $course->id);
                $_SESSION["displayPopup"] = 1;
            }

        } else {
            $_SESSION["displayPopup"] = 2;
        }
    } else {
        if ($displayVal == 0) {
            $DB->execute("INSERT INTO tms_course_congratulations (user_id, course_id, display) VALUES (" . $USER->id . ", " . $course->id . ", -1)");
            $_SESSION["displayPopup"] = 0;
        }
    }
}
//$_SESSION["displayPopup"] = 1;

//get content of competency framework
$sqlGetContentCompetency = 'select ttp.id, ttp.description from tms_traninning_programs ttp
join tms_traninning_users ttu on ttp.id = ttu.trainning_id
join tms_trainning_courses ttc on ttc.trainning_id = ttp.id
where ttp.deleted = 0 and  user_id = ' . $USER->id . ' and course_id = ' . $course->id;
$getContentCompetency = array_values($DB->get_records_sql($sqlGetContentCompetency))[0];

//get score for toeic
$sqlGetToeicScore = 'select listening, reading, total from tms_quiz_grades where userid =' . $USER->id . ' and courseid = ' . $course->id;
$toeicScore = array_values($DB->get_records_sql($sqlGetToeicScore))[0];

//check if is toeic course and is not admin => toeic result is last tab
$tab_unit = '';
$tab_toeic_result = '';
$tab_toeic_admin = '';
if ($course->is_toeic == 1 && $permission_admin) {
    $tab_toeic_admin = 'nav-tab-last';
} else if ($course->is_toeic == 1) {
    $tab_toeic_result = 'nav-tab-last';
}
else {
    $tab_unit = 'nav-tab-last';
}

//
$tab_introduction = '';
$tab_competency = '';
 if (!is_null($getContentCompetency->id)) {
    $tab_competency = 'nav-first-tab';
}else{
     $tab_introduction = 'nav-first-tab';
 }
//check if is toeic course and is admin => toeic result is last tab
//else unit list is last tab

//
?>
<body <?php echo $bodyattributes ?>>

<div class="wrapper"><!-- wrapper -->
    <?php echo $OUTPUT->header(); ?>

    <div id="app">
        <section class="section section--header"><!-- section -->
            <div class="container">
                <!--                progress info-->
                <div class="progress-info">
                    <div class="progress-info__title mt-2 mb-3"><span title="<?php echo $course->fullname; ?>"><a
                                class="prev-btn"><i
                                    class="fa fa-angle-left"
                                    aria-hidden="true"></i></a>  <?php echo $course->fullname; ?></span></div>
                    <div class="progress-info__content">
                        <div class="row">
                            <div class="col-4 info-course-detail">
                                <ul>
                                    <li class="teacher"><i class="fa fa-user"
                                                           aria-hidden="true"></i> <?php echo $teacher_name ?></li>
                                    <li class="units"><i class="fa fa-file"
                                                         aria-hidden="true"></i> <?php echo $course->numofmodule; ?>
                                        Units
                                    </li>
                                    <li class="units"><i class="fa fa-clock-o"
                                                         aria-hidden="true"></i> <?php echo $course->estimate_duration; ?>
                                        hours
                                    </li>
                                </ul>
                            </div>
                            <div class="col-6 row info-course-progress">
                                <span class="col-3">PROGRESS</span>

                                <div class="col-9">
                                    <hgroup class="speech-bubble">
                                            <span class="number-module" numoflearned="<?php echo $course_numoflearned; ?>" numofmodule="<?php echo $course_numofmodule; ?>"><?php echo $course_numoflearned; ?>
                                                / <?php echo $course_numofmodule; ?></span>
                                    </hgroup>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: <?php echo (int)($course->numoflearned * 100 / $course->numofmodule); ?>%;"
                                             aria-valuenow="<?php echo $percent_learned; ?>"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 info-course-btn">
                                <a href="<?php echo $start_course_link ?>"
                                   <?php if (strlen($start_course_link) == 0) { ?>onclick="return notifyNoContent()"
                                    <?php } ?>
                                   class="btn btn-start-course btn-click">start course</a>
                            </div>
                        </div>
                    </div>
                </div>
        </section>


        <section class="section section-nav">
            <div class="container">
                <!--                click tab - nav-->
                <div class="nav-course">
                    <ul class="nav nav-tabs-courses">
                        <?php if (!is_null($getContentCompetency->id)) { ?>
                            <li class="nav-item nav-click <?php echo $tab_competency; ?>">
                                <a id="unit-link" class="nav-link" data-toggle="tab" href="#contentcompetency"
                                   role="tab">General
                                    Competency Description</a>
                            </li>
                        <?php } ?>
                        <li class="nav-item nav-click nav-introduction <?php echo $tab_introduction; ?>">
                            <a class="nav-link" data-toggle="tab" href="#courseintroduction" role="tab">Course
                                introduction</a>
                        </li>
                        <li class="nav-item nav-click <?php echo $tab_unit; ?>">
                            <a id="unit-link" class="nav-link" data-toggle="tab" href="#courseunit" role="tab">Unit
                                List</a>
                        </li>
                        <?php if ($course->is_toeic == 1 && $permission_admin) { ?>
                            <li class="nav-item nav-click <?php echo $tab_toeic_admin; ?>">
                                <a id="toeic-result-link" class="nav-link" data-toggle="tab" href="#toeicadmin"
                                   role="tab">List
                                    Toeic Result</a>
                            </li>
                        <?php } else if ($course->is_toeic == 1) { ?>
                            <li class="nav-item nav-click <?php echo $tab_toeic_result; ?>">
                                <a id="toeic-admin-link" class="nav-link" data-toggle="tab" href="#toeicresult"
                                   role="tab">Toeic
                                    Result</a>
                            </li>
                        <?php } ?>
                        <?php if ($permission_edit) { ?>
                            <li class="nav-item nav-setting">
                                <a class="dropdown-toggle setting-link" id="menu-edit" data-toggle="dropdown">
                                    <i class="fa fa-cog" aria-hidden="true"></i>
                                    Edit course
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="menu-edit">
                                    <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1"
                                                               href="<?php echo $root_url . "/course/view.php?id=" . $id ?>&edit=on"><i
                                                class="icon fa fa-pencil fa-fw " aria-hidden="true"></i>Edit</a></li>
                                    <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1"
                                                               href="<?php echo $root_url . "/course/completion.php?id=" . $id ?>"><i
                                                class="icon fa fa-cog fa-fw" aria-hidden="true"></i>Course
                                            completion</a></li>
                                    <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1"
                                                               href="<?php echo $root_url . "/backup/import.php?id=" . $id ?>"><i
                                                class="icon fa fa-level-up fa-fw" aria-hidden="true"></i>Import</a></li>
                                    <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1"
                                                               href="<?php echo $root_url . "/course/admin.php?courseid=" . $id ?>"><i
                                                class="icon fa fa-cog fa-fw" aria-hidden="true"></i>More</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </section>

        <!--    body-->
        <section class="section section-content section-course-info">
            <div class="container">
                <div class="row col-12 course-content course-main" id="courseintroduction">
                    <div class="col-8 course-block-info">
                        <div class="course-block course-description">
                            <div class="course-block__content">
                                <?php echo $course->summary; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 course-block-img">
                        <img src="<?php echo $course->course_avatar; ?>" alt="">
                    </div>
                </div>

                <div class="row col-12 course-content" id="courseunit">
                    <div class="col-5 unit-info">
                        <div class="list-units">
                            <?php foreach ($units as $no => $unit) { ?>
                                <?php $modulCompletion = array_sum(array_map(function ($item) {
                                    return $item['iscompletion'];
                                }, $unit['modules']));
                                $totalModul = count($unit['modules']);
                                $icon = "pencil-square-o";
                                $addName = "";
                                if ($totalModul > 0 && $modulCompletion == $totalModul) {
                                    $icon = "check";
                                    $addName = 'unit-done';
                                } ?>
                                <div class="unit <?php echo $addName; ?>" id="unit_<?php echo $unit['id']; ?>"
                                     section-no="<?php echo $no ?>">
                                    <div class="unit__title"><p><?php echo $unit['name']; ?></p></div>
                                    <div class="unit__progress">
                                        <div class="unit__icon">
                                            <i class="fa fa-<?php echo $icon; ?>" aria-hidden="true"></i>
                                        </div>
                                        <div class="unit__progress-number">
                                            <!--                                                class="percent-get">-->
                                            <?php //echo count($unit['modules']['iscompletion']); ?><!--</span>-->
                                            <p><i class="fa fa-check" aria-hidden="true"></i> <span
                                                    class="percent-get"><?php echo $modulCompletion; ?>/<?php echo $totalModul; ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-7 unit-info unit-detail">
                        <?php foreach ($units as $unit) { ?>
                            <div class="main-detail" id="detail-<?php echo $unit['id']; ?>">
                                <div class="detail-title">
                                    <p><?php echo $unit['name']; ?></p>
                                </div>
                                <div class="detail-content">
                                    <?php if ($unit['modules'] && !empty($unit['modules'])) {
                                        foreach ($unit['modules'] as $module) { ?>
                                            <ul class="detail-list">
                                                <?php if ($module['iscompletion'] == 1) { ?>
                                                    <li class="li-module-done"><i class="fa fa-check"
                                                                                  aria-hidden="true"></i>
                                                        <a class="module-done"
                                                           href="<?php echo $module['url'] ?>"><?php echo $module['name']; ?></a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li><i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                        <a class="module-notyet"
                                                           href="<?php echo $module['url'] ?>"><?php echo $module['name']; ?></a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php }
                                    } else { ?>
                                        Unit has no content.
                                    <?php } ?>
                                </div>
                                <?php if ($unit['modules'][0] && $unit['modules'][0]['url'] && strlen($unit['modules'][0]['url']) != 0) { ?>
                                    <div class="detail-btn">
                                        <a href="<?php echo $unit['modules'][0]['url']; ?>"
                                           class="btn btn-click btn-start-unit">Start unit</a>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="row col-12 course-content course-main" id="contentcompetency">
                    <div class="container">
                        <p><?php echo $getContentCompetency->description; ?></p>
                    </div>
                </div>

                <div class="row col-12 course-content course-main" id="toeicresult">
                    <div class="container">
                        <ul class="list-style list-point-toeic">
                            <li><span class="title-part">Listening:</span><span
                                    class="score-part"> <?php if (is_null($toeicScore)) echo 0; else echo $toeicScore->listening; ?></span>
                            </li>
                            <li><span class="title-part">Reading:</span><span
                                    class="score-part"> <?php if (is_null($toeicScore)) echo 0; else echo $toeicScore->reading; ?></span>
                            </li>
                            <li><span class="title-part">Total:</span><span
                                    class="score-part score-total"> <?php if (is_null($toeicScore)) echo 0; else echo $toeicScore->total; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row col-12 course-content" id="toeicadmin">
                    <div class="import-student-score mb-3" style="background-color: #ffffff; width: 100%">
                        <div class="container">
                            <div class="div-import">
                                <div class="custom-file custom-file-select" style="width: 90%; margin-right: 2%">
                                    <input type="file" ref="file" name="file" class="custom-file-input"
                                           id="validatedCustomFile" required @change="selectedFile"/>
                                    <label class="custom-file-label" id="labelValidatedCustomFile"
                                           for="validatedCustomFile">Choose file...</label>
                                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                                </div>
                                <div class="custom-file custom-file-btn" style="width: 10%; margin: inherit;">
                                    <button type="button" class="btn btn-primary btn-up-file" @click="uploadFile">Upload
                                        file
                                    </button>
                                </div>
                            </div>
                            <div class="file-import mt-3">
                                <a :href="file_url" class="btn px-0 not_shadow"><i aria-hidden="true" class="fa fa-file"></i> Download Excel Form</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="background-color: #ffffff; padding: 2%">
                        <table class="table table-bordered table_res">
                            <thead>
                            <th style="min-width: 30px">No</th>
                            <th>Name</th>
                            <th class="d-none d-sm-table-cell">Email</th>
                            <th class="d-none d-sm-table-cell">Listening</th>
                            <th class="d-none d-sm-table-cell">Reading</th>
                            <th class="d-none d-sm-table-cell">Total</th>
                            </thead>
                            <tbody>
                            <tr v-if="toeicScores.length == 0">
                                <td colspan="6">No data</td>
                            </tr>
                            <tr v-else v-for="(item,index) in toeicScores">
                                <td>{{ (current-1)*recordPerPage+(index+1) }}</td>
                                <td>{{ item.fullname }}</td>
                                <td class="d-none d-sm-table-cell">{{ item.email }}</td>
                                <td class="d-none d-sm-table-cell">{{ item.listening }}</td>
                                <td class="d-none d-sm-table-cell">{{ item.reading }}</td>
                                <td class="d-none d-sm-table-cell">{{ item.total }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="pagination" v-if="totalPage > 1">
                            <v-pagination
                                v-model="current"
                                :page-count="totalPage"
                                :classes="bootstrapPaginationClasses"
                                :labels="customLabels"
                                @input="onPageChange"
                            ></v-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

</div>

<?php if ($_SESSION["displayPopup"] == 1) { ?>
    <!-- Modal congratulation -->
    <!--<div class="modal fade show" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" style="display: block;" aria-modal="true">-->
    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="exampleModalCenterTitle">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">I congratulate you on finishing the
                        course <?php echo $course->fullname; ?>!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="<?php echo $pathBadge; ?>" alt="">
                    <span class="sp-name-course"><?php echo $course->fullname; ?></span>
                </div>
                <div class="modal-footer" style="width: 100%">
                    <div style="margin: 0 auto">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }
$_SESSION["displayPopup"] = 2; ?>
<script>
    $(document).ready(function () {
        //get active li to show content
        $(".nav-click").each(function () {
            var getClasses = $(this).attr('class');
            if (getClasses.indexOf('active') > -1) {
                var getId = $(this).find("a").attr('href');
                $(getId).css('display', 'flex');
            }
            //remove all active class not current active class
            $('.nav-click').not($(this)).each(function () {
                $(this).removeClass('active');
            });
            //hide another content
            $('.course-content').not($('#courseintroduction')).each(function () {
                $(this).css('display', 'none');
            });
            $('.nav-tabs-courses .nav-introduction a').addClass('active');
        });

        $(".nav-click a").click(function () {
            //set active for first block
            var getHref = $(this).attr('href');
            if (getHref.indexOf('unit') > -1) {
                var getID = $(".unit").first().attr('id');
                if (getID) {
                    var ID = getID.substring(5, getID.length);
                    ClickNav(getID, ID);
                }
            }
            $('.nav-click a').not($(this)).each(function () {
                $(this).removeClass('active');
            });

            $('.course-content').not($(getHref)).each(function () {
                $(this).css('display', 'none');
            });
            $(getHref).css('display', 'flex');
        });

        var getPercent = $('.progress-bar').attr('aria-valuenow');
        //
        var numberMinus = 6;
        var numofmodule = $('.number-module').attr('numofmodule');

        if(numofmodule > 999)
            numberMinus = 11;
        else if(numofmodule > 99)
            numberMinus = 10;
        else if(numofmodule > 9)
            numberMinus = 7;

        var marginLeft = getPercent - numberMinus;
        var getScreenWidth = screen.width;
        if (getScreenWidth >= 1600) {
            marginLeft = getPercent - 3;
        } else if (getScreenWidth < 420) {
            marginLeft = getPercent - 14;
        } else if (getScreenWidth < 768) {
            marginLeft = getPercent - 10;
        }
        $('.speech-bubble').css('left', marginLeft + '%');

        //set height and line height
        var getHeight = $('.info-course-btn').innerHeight();
        $('.info-course-progress .col-3').css('height', getHeight + 'px');
        $('.info-course-progress .col-3').css('line-height', getHeight + 'px');
        $('.info-course-detail').css('height', getHeight + 'px');
        $('.info-course-detail').css('line-height', getHeight + 'px');


        //event click unit
        $('.unit').click(function () {
            var getID = $(this).attr('id');
            var ID = getID.substring(5, getID.length);
            ClickNav(getID, ID);
        });

        //return back url
        $('.prev-btn').click(function () {
            history.back();
        });

        //function click
        function ClickNav(getID, ID) {
            $('.unit').not($('#' + getID)).each(function () {
                $(this).removeClass('unit-click');
            });
            $('#' + getID).addClass('unit-click');
            $('#detail-' + ID).css('display', 'block');
            $('.main-detail').not($('#detail-' + ID)).each(function () {
                $(this).css('display', 'none');
                $(this).removeClass('unit-click');
            });
        }

        //Click tab unit list and curent unit by url params
        <?php if (strlen($section_no) != 0) { ?>
        $("#unit-link").trigger("click");
        $("#unit-link").addClass('active');
        $("[section-no=<?php echo $section_no ?>]").trigger("click");
        <?php } ?>
    });

    function notifyNoContent() {
        alert("Course has no content, please try again later");
        return false;
    }

    $("#myModal").on('hide.bs.modal', function () {
    });
</script>
<script>
    $(document).ready(function () {
        // Show modal on page load
        $("#myModal").modal('show');
    });
</script>

<script>
    //Notify tiếp tục module đang học dở
    $(document).ready(function () {

        $.getJSON("https://api.ipify.org?format=json",
            function (data) {
                var result_ip =  <?php echo $result_ip;  ?>;
                var count_ip = result_ip.list_access_ip.length;
                let check = <?php echo json_encode($userCheck); ?>;
                if (count_ip > 0) {
                    if (result_ip.list_access_ip.includes(data.ip) || check.length > 0) {
                        continue_learning();
                    } else {
                        var message_access = 'This course cannot be accessed outside of the office';
                        alert(message_access);
                        var url_next = '<?php echo $url_to_page = new moodle_url($root_url); ?>';
                        window.location.href = url_next;

                    }
                } else {
                    continue_learning();
                }


            });
    });


    function continue_learning() {
        <?php if ($id != $source) { ?> //Vào từ màn khóa học khác
        $('#page').css('margin-right', '0');
        var x = document.getElementsByTagName("BODY")[0];
        var classes = x.className.toString().split(/\s+/);
        let course_id = '0';

        //screen course detail
        if (classes.includes("pagelayout-course")) {
            classes.forEach(function (classItem) {
                if (classItem.startsWith('course-')) {
                    course_id = classItem.substring(7, classItem.length);
                }
            });
            $.ajax({
                url: '<?php echo $root_url ?>/pusher/resume.php',
                data: {
                    'course_id': course_id
                },
                type: 'POST',
                success: function (data) {
                    if (data.length !== 0) {
                        r = confirm("Do you want to continue last activity in course?");
                        if (r == true) {
                            window.location.href = data;
                        } else {
                            return;
                        }
                    }
                },
                error: function (e) {
                    console.log(e);
                }
            });
        }
        <?php } ?>
    }


    Vue.component('v-pagination', window['vue-plain-pagination'])
    var app = new Vue({
        el: '#app',
        data: {
            toeicScores: [],
            typeCourse: '',
            current: 1,
            totalPage: 0,
            file_url: 'files/TOEIC_grade.xlsx',
            typeToeic: 'get',
            recordPerPage: 10,
            bootstrapPaginationClasses: { // http://getbootstrap.com/docs/4.1/components/pagination/
                ul: 'pagination',
                li: 'page-item',
                liActive: 'active',
                liDisable: 'disabled',
                button: 'page-link'
            },
            customLabels: {
                first: false,
                prev: '<',
                next: '>',
                last: false
            },
        },
        methods: {
            onPageChange: function () {
                this.getListToeicScore(this.typeToeic, this.current);
            },
            selectedFile() {
                let file = this.$refs.file.files[0];
                document.getElementById('labelValidatedCustomFile').innerHTML = file.name;
            },
            getListToeicScore: function (type, page) {
                var _this = this;
                if (page == 1)
                    this.current = 1;
                let url = '<?php echo $CFG->wwwroot; ?>';
                const params = new URLSearchParams();
                params.append('type', type || this.typeToeic);
                params.append('current', page || this.current);
                params.append('recordPerPage', this.recordPerPage);
                params.append('courseid', <?php echo $course->id; ?>);

                axios({
                    method: 'post',
                    url: url + '/pusher/course_view.php',
                    data: params,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                    .then(response => {
                        this.toeicScores = response.data.toeicScore;
                        this.totalPage = response.data.totalPage;
                    })
                    .catch(error => {
                    });
            },
            uploadFile: function () {
                var _this = this;
                var file = this.$refs.file.files[0];
                var validate = this.validateFile(file);
                let url = '<?php echo $CFG->wwwroot; ?>';
                let formData = new FormData();
                formData.append('courseid', <?php echo $course->id; ?>);
                formData.append('file', this.$refs.file.files[0]);
                if (validate) {
                    axios({
                        method: 'post',
                        url: url + '/pusher/inputtoeic.php',
                        data: formData,
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    })
                        .then(response => {
                            console.log(response.data);
                            if (response.data.status) {
                                alert(response.data.msg);
                                location.reload();
                            } else
                                alert(response.data.msg);
                        })
                        .catch(error => {
                            console.log("Error ", error);
                        });
                }
            },
            validateFile: function (file) {
                //not selected file
                if (!file) {
                    alert("Please choose a excel file to import.");
                    return false;
                }
                //get variable
                var name = file.name;
                var size = file.size;
                var ext = name.toLowerCase().split('.');
                var fileExt = ext[ext.length - 1];
                var extensions = ["csv", "xlsx", "xls"];
                //validate
                if (extensions.indexOf(fileExt) < 0) {
                    alert("Extension not allowed, please choose a excel file.");
                    const input = this.$refs.file;
                    input.type = 'file';
                    this.$refs.file.value = '';
                    return false;
                }

                if (size > 2536715) {
                    alert('Maximum file size of 2.5MB');
                    return false;
                }
                return true;
            }
        },
        mounted() {
            this.getListToeicScore(this.typeToeic, this.current);
        }
    });

</script>

</body>
</html>


<?php
die;
?>
