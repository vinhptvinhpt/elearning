<?php
require_once(__DIR__ . '/../../../../config.php');
//
if (!isloggedin()) {
    require_login();
}
// Start the session
session_start();

// [VinhPT] Get firstaccess to redirect
global $DB, $USER;

$first_login_sql = "SELECT 0, firstaccess, lastaccess from mdl_user where id =" . $USER->id;
$first_login_info = array_values($DB->get_records_sql($first_login_sql))[0];
if ($first_login_info->firstaccess == $first_login_info->lastaccess) {
    $update = "UPDATE mdl_user set firstaccess = ? where id = ?";
    $DB->execute($update, array($first_login_info->firstaccess + 1, $USER->id));
    redirect(new moodle_url('guideline.php'));
}

$sql_teacher = "select id, name, mdl_role_id, status from roles where name = 'teacher'";
$teacher = $DB->get_record_sql($sql_teacher);
$teacher_role_id = $teacher->mdl_role_id ? $teacher->mdl_role_id : 4;


$sqlGetInfoUser = 'select tud.fullname as fullname, SUBSTR(tud.avatar, 2) as avatar, toe.position, toe.description as exactlypostion from tms_user_detail tud left join tms_organization_employee toe on tud.user_id = toe.user_id where tud.user_id = ' . $USER->id;
$profile = array_values($DB->get_records_sql($sqlGetInfoUser))[0];

$sqlGetOrganization = 'SELECT f.id, f.level, f.code
            FROM (SELECT @id AS _id, (SELECT @id := parent_id FROM tms_organization WHERE id = _id)
            FROM (SELECT @id := (select organization_id from tms_organization_employee where user_id= ' . $USER->id . ')) tmp1
            JOIN tms_organization ON @id IS NOT NULL) tmp2
            JOIN tms_organization f ON tmp2._id = f.id
            where f.level = 1 limit 1';
$organization = array_values($DB->get_records_sql($sqlGetOrganization))[0];
$organization_id = $organization->id;

$organizationCodeGet = "";
$organizationLower = strtolower($organization->code);
if(strpos($organizationLower, 'bg') === 0 || strpos($organizationLower, 'begodi') === 0){
    $organizationCodeGet = "BG";
} else if(strpos($organizationLower,'ea') === 0 || strpos($organizationLower,'easia') === 0){
    $organizationCodeGet = "EA";
} else if(strpos($organizationLower, 'ev') === 0 || strpos($organizationLower, 'exotic') === 0){
    $organizationCodeGet = "EV";
}else if(strpos($organizationLower, 'AV') === 0 || strpos($organizationLower, 'avana') === 0){
    $organizationCodeGet = "AV";
}else{
    $organizationCodeGet = "PH";
}


//set for full page
$organization_id = is_null($organization) ? 0 : $organization->id;
//$organizationCodeGet
$organizationCode = is_null($organizationCodeGet) ? strtoupper($_SESSION["organizationCode"]) : $organizationCodeGet;
//$organizationCode = "BG";
switch ($organizationCode) {
    case "EA":
        {
            $_SESSION["organizationName"] = 'Easia';
            $_SESSION["color"] = '#862055';
            $_SESSION["pathLogo"] = 'images/logo-black.png';
            $_SESSION["pathLogoWhite"] = 'images/logo-white.png';
            $_SESSION["component"] = 'images/cpn-easia.png';
            $_SESSION["pathBackground"] = 'images/bg-a-02.jpg';
        }
        break;
    case "EV":
        {
            $_SESSION["organizationName"] = 'Exotic voyages';
            $_SESSION["color"] = '#CAB143';
            $_SESSION["pathLogo"] = 'images/exoticvoyages.png';
            $_SESSION["pathLogoWhite"] = 'images/exoticvoyages-white.png';
            $_SESSION["component"] = 'images/cpn-exotic.png';
            $_SESSION["pathBackground"] = 'images/bg-a-02.jpg';
        }
        break;
    case "BG":
        {
            $_SESSION["organizationName"] = 'Begodi';
            $_SESSION["color"] = '#333';
            $_SESSION["pathLogo"] = 'images/begodi.png';
            $_SESSION["pathLogoWhite"] = 'images/begodi-white.png';
            $_SESSION["component"] = 'images/cpn-begodi.png';
            $_SESSION["pathBackground"] = 'images/bg-a-02.jpg';
        }
        break;
    case "AV":
        {
            $_SESSION["organizationName"] = 'Avana';
            $_SESSION["color"] = '#202020';
            $_SESSION["pathLogo"] = 'images/avana.png';
            $_SESSION["pathLogoWhite"] = 'images/avana-white.png';
            $_SESSION["component"] = 'images/cpn-avana.png';
            $_SESSION["pathBackground"] = 'images/bg-a-02.jpg';
        }
        break;
    default:
        {
            $_SESSION["organizationName"] = 'PHH';
            $_SESSION["color"] = '#0080EF';
            $_SESSION["pathLogo"] = 'images/phh.png';
            $_SESSION["pathLogoWhite"] = 'images/phh-white.png';
            $_SESSION["component"] = 'images/cpn-phh.png';
            $_SESSION["pathBackground"] = 'images/bg-a-02.jpg';
        }
        break;
}

//get course list
$sql = 'select @s:=@s+1 stt,
mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
 ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule,
  ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate <> 0 and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned,
    muet.userid as teacher_id,
    tud.fullname as teacher_name,
    tor.name as teacher_organization,
    muet.timecreated as teacher_created,
    toe.position as teacher_position,
    toe.description as teacher_description,
    ttp.id as training_id,
    ttp.name as training_name,
    ttp.deleted as training_deleted,
    ttc.order_no,
    GROUP_CONCAT(CONCAT(tud.fullname, \' created_at \',  muet.timecreated)) as teachers
  from mdl_course mc
  inner join mdl_enrol me on mc.id = me.courseid
  inner join mdl_user_enrolments mue on me.id = mue.enrolid
  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
  left join mdl_user_enrolments muet on met.id = muet.enrolid
  left join tms_user_detail tud on tud.user_id = muet.userid
  left join tms_organization_employee toe on toe.user_id = muet.userid
  left join tms_trainning_courses ttc on mc.id = ttc.course_id
  left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
  left join tms_organization tor on tor.id = toe.organization_id, (SELECT @s:= 0) AS s
  where me.enrol = \'manual\'
  and ttc.deleted <> 1
  and mc.deleted = 0
  and mc.visible = 1
  and mc.category NOT IN (2,7)
  and mue.userid = ' . $USER->id;
$sql .= ' group by mc.id ORDER BY ttp.id, ttc.order_no'; //cần để tạo tên giáo viên

$courses = array_values($DB->get_records_sql($sql));

$courses_current = array();
$courses_required = array();
$courses_completed = array();
$courses_others = array();
$courses_others_id = '(0';
$courses_soft_skills = array();
//
$courses_training = array();
//
$courses_required_list = array();
//
$competency_exists = array();
$competency_completed = array();
$countRequiredCourses = 0;
$sttTotalCourse = 0;
foreach ($courses as $course) {
    $courses_training[$course->training_id][$course->order_no] = $course;
}

foreach ($courses_training as $courses){
    $stt = 1;
    foreach ($courses as &$course) {
        $course->sttShow = $stt;
        //current first
        if ($course->numofmodule > 0 && $course->numoflearned / $course->numofmodule > 0 && $course->numoflearned / $course->numofmodule < 1) {
            array_push($competency_exists, $course->training_id);
            push_course($courses_current, $course);
        } //then complete
        elseif ($course->numoflearned / $course->numofmodule == 1) {
            array_push($competency_completed, $course->training_id);
            push_course($courses_completed, $course);
        } //then required = khoa hoc trong khung nang luc
        elseif ($course->training_name && ($course->training_deleted == 0 || $course->training_deleted == 2)) {
            $courses_required[$course->training_id][$course->order_no] = $course;
            if ($course->training_deleted == 2) {
                $courses_others_id .= ', ' . $course->id;
            }
            $countRequiredCourses++;
            $courses_required_list[] = $course;
        }
        $stt++;
        $sttTotalCourse++;
    } //the last is other courses
//    else {
//        push_course($courses_others, $course);
////        $courses_others_id .= ', '.$course->id;
//    }
    // }
}

$courses_others_id .= ')';
function push_course(&$array, $course)
{
    if (array_key_exists($course->id, $array)) {//đã có, check created date mới nhất thì overwwrite
        $old_created = $array[$course->id]->teacher_created;
        if ($course->teacher_created > intval($old_created)) {
            $array[$course->id] = $course;
        }
    } else {//mới
        $array[$course->id] = $course;
    }
}


// Set session variables
$countBlock = 1;
$_SESSION["courses_current"] = 0;
$_SESSION["courses_required"] = 0;
$_SESSION["courses_completed"] = 0;
$_SESSION["totalCourse"] = 0;
$percentCompleted = 0;
$percentStudying = 0;
if($sttTotalCourse > 0){
    $_SESSION["courses_current"] = $courses_current;
    $_SESSION["courses_required"] = $courses_required_list;
    $_SESSION["courses_completed"] = $courses_completed;
    $_SESSION["totalCourse"] = $sttTotalCourse;
    $percentCompleted = round(count($courses_completed) * 100 / $sttTotalCourse);
    $percentStudying = round(count($courses_current) * 100 / $sttTotalCourse);
}

//get course can not enrol
$sqlCourseNotEnrol = 'select mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
muet.userid as teacher_id,
tud.fullname as teacher_name,
toe.position as teacher_position,
tor.name as teacher_organization,
muet.timecreated as teacher_created
from mdl_course mc
inner join tms_trainning_courses ttc on mc.id = ttc.course_id
  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . '
  left join mdl_user_enrolments muet on met.id = muet.enrolid
left join tms_user_detail tud on tud.user_id = muet.userid
  left join tms_organization_employee toe on toe.user_id = muet.userid
  left join tms_organization tor on tor.id = toe.organization_id
  inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id and ttp.deleted = 2 and mc.id not in ' . $courses_others_id;
$coursesSuggest = array_values($DB->get_records_sql($sqlCourseNotEnrol));

//get image badge
$sqlGetBadge = "select path from image_certificate where type =2 and is_active";
$pathBadge = array_values($DB->get_records_sql($sqlGetBadge))[0]->path;

//$organization_id = 0;
//get footer address
$sqlGetFooterAddresses = "select id, organization_id, country, name, address, tel, fax from tms_organization_addresses where organization_id = " . $organization_id . " group by id";
$getFooterAddresses = array_values($DB->get_records_sql($sqlGetFooterAddresses));
$footerAddresses = [];
$footerAddressesTab = [];
foreach ($getFooterAddresses as $footerAddress) {
    $footerAddresses[$footerAddress->country][] = $footerAddress;
    $footerAddressesTab[$footerAddress->country] = $footerAddress->country;
}

$_SESSION["OrganizationID"] = $organization_id;
$_SESSION["footerAddressesTab"] = $footerAddressesTab;
$_SESSION["footerAddresses"] = $footerAddresses;


//get permission
$sqlCheckPermission = 'SELECT permission_slug, roles.name from `model_has_roles` as `mhr`
inner join `roles` on `roles`.`id` = `mhr`.`role_id`
left join `permission_slug_role` as `psr` on `psr`.`role_id` = `mhr`.`role_id`
inner join `mdl_user` as `mu` on `mu`.`id` = `mhr`.`model_id`
where `mhr`.`model_id` = ' . $USER->id . ' and `mhr`.`model_type` = "App/MdlUser"';

$getPermissions = $DB->get_records_sql($sqlCheckPermission);

$allowCms = false;
$permissions = array_values($getPermissions);
foreach ($permissions as $permission) {
    if (!in_array($permission->name, ['student', 'employee'])) {
        $allowCms = true;
        break;
    }
}
$_SESSION["allowCms"] = $allowCms;
?>

<html>
<title>Home</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/highcharts.js"></script>

<style>
    @font-face {
        font-family: Nunito-Sans;
        src: url('fonts/NunitoSans-Black.ttf');
    }

    @font-face {
        font-family: HelveticaLTStd-Bold;
        src: url('fonts/HelveticaLTStd-Bold.otf');
    }

    @font-face {
        font-family: HelveticaLTStd-Light;
        src: url('fonts/HelveticaLTStd-Light.otf');
    }

    @font-face {
        font-family: Nunito-Sans-Bold;
        src: url('fonts/NunitoSans-Bold.ttf');
    }

    @font-face {
        font-family: Nunito-Sans-Regular;
        src: url('fonts/NunitoSans-Regular.ttf');
    }

    @font-face {
        font-family: Roboto-Bold;
        src: url('fonts/Roboto-Bold.ttf');
    }

    @font-face {
        font-family: Roboto-Light;
        src: url('fonts/Roboto-Light.ttf');
    }

    @font-face {
        font-family: Roboto-Regular;
        src: url('fonts/Roboto-Regular.ttf');
    }

    body {
        font-size: 14px;
    }

    a {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    #page-wrapper .navbar {
        padding: 7px 1rem 9px .5rem !important;
    }

    .navbar .count-container {
        top: 2px !important;
    }

    .block-color {
        width: 90px;
        height: 90px;
        background-color: <?=$_SESSION["color"]?>;
        position: absolute;
        bottom: 25%;
        z-index: 1;
        left: -7%;
    }

    .title {
        text-align: left;
        font-family: Nunito-Sans;
        font-size: 20px;
        letter-spacing: 0px;
        color: #202020;
        /*text-transform: uppercase;*/
        opacity: 1;
    }

    .title h2 {
        font-family: HelveticaLTStd-Bold;
        font-size: 1.5em;
    }

    .title span {
        font-family: HelveticaLTStd-Light;
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
        color: #000000 !important;
    }

    .carousel-inner .carousel-item {
        background-repeat: no-repeat;
        background-position: 100% 50%;
        background-size: cover;
        min-height: 500px;
    }

    .li-progress:hover {
        cursor: pointer;
    }

    .btn-click {
        background: <?=$_SESSION["color"]?> 0% 0% no-repeat padding-box !important;
        border-radius: 4px;
        opacity: 1;
        padding: 9px 14px !important;
    }

    .btn-click a:hover, .btn-click:hover {
        opacity: 0.8;
        color: #ffffff !important;
    }

    .btn-click a {
        text-align: left;
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #FFFFFF;
        text-transform: uppercase;
        opacity: 1;
    }

    .info-center {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .courses {
        margin-bottom: 70px;
    }

    .info-course p {
        margin: 0 0 5px 0;
        font-family: Roboto-Regular;
        font-size: 14px !important;
    }

    .info-course a {
        margin: 0 0 5px 0;
        font-family: Roboto-Regular !important;
        font-size: 14px !important;
    }

    .info-course a:hover {
        cursor: pointer;
    }

    img {
        width: 100%;
    }

    .block-data .disable {
        pointer-events: none;
    }

    .div-info-progress-disable img {
        /*height: 24% !important;*/
    }

    .div-info-progress-disable span {
        /*top: 11%;*/
        /*right: 14%;*/
    }

    .div-info-progress-disable span, .div-info-progress-enable span {
        top: 11% !important;
    }

    .number-order {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: <?=$_SESSION["color"]?>;
        text-align: center;
        vertical-align: middle;
        /*position: absolute;*/
        /*bottom: 0%;*/
        /*right: 12%;*/
        color: #ffff;
        font-size: 13px;
        margin-left: 80%;
    }

    .number-order-hide {
        opacity: 0 !important;
    }

    .div-disable {
        background-color: #fdf2f285;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .carousel-control-prev, .carousel-control-next,
    .carousel-indicators {
        display: none !important;
    }

    .btn-show-all {
        text-align: right;
        margin: auto;
    }

    .block-items__item {
        box-shadow: 3px 3px 6px #00000029;
        display: flex;
        overflow: hidden;
        height: 100%;
    }

    .block-items__item img {
        height: -webkit-fill-available;
    }

    .path-calendar {
        position: inherit;
        /*margin-bottom: 2%;*/
    }

    .path-calendar #page-wrapper #page {
        margin: 0 !important;
        padding: 3% 3% 2.5% 3%;
    }

    .path-calendar #page-wrapper #page #page-content #region-main {
        padding: 0;
        margin: 0 !important;
    }

    .path-calendar #page-wrapper #page #page-content {
        padding-bottom: 0% !important;
    }

    .section-footer {
        position: relative;
        background: #000000 0% 0% no-repeat padding-box;
        border: 1px solid #707070;
        opacity: 1;
    }

    .section-footer .container {
        padding: 3% 0;
    }

    .path-calendar .maincalendar .heightcontainer {
        height: auto !important;
        padding: 3%;
    }

    .block-item__content_btn {
        width: 100%;
        text-align: right;
    }

    .title-course {
        text-align: left;
        letter-spacing: 0.6px;
        font-size: 17px;
        font-family: Roboto;
        color: #202020;
        opacity: 1;
        /*margin-bottom: 20px;*/
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 50px;
        font-weight: 700;
    }

    .course-block__top {
        padding: 15px 0;
        margin: 20px 0;
        border-bottom: 1px solid #707070;
    }

    .course-block__top-show {
        display: flex;
        padding: 0 !important;
    }

    .block-item__content {
        width: inherit;
        padding: 4% 3%;
    }

    .block-item__content_text {
        height: 80%;
    }

    .block-item__image {
        width: 80%;
        padding: 0;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        position: relative;
    }

    .block-item__image img {
        width: 60px;
        height: 60px;
        position: absolute;
        top: 3%;
        right: 3%;
    }

    .block-item__image_complete img {
        width: 40px !important;
        height: 40px !important;
    }

    .block-item__image span {
        font-size: 14px;
        font-family: Nunito-Sans-Bold;
        color: #FFFFFF;
        position: absolute;
        top: 12%;
        right: 11%;
        letter-spacing: 1px;
    }

    .info-user {
        background-color: <?=$_SESSION["color"]?>;
        display: flex;
        width: 100%;
        padding: 3% 6%;
        margin-bottom: 3%;
    }

    .info-user .avatar {
        width: 100%;
        border-radius: 50%;
        margin: 0 !important;
        padding: 0;
        text-align: right;
        width: calc(100% - 10px);
        height: 100%;
    }

    .info-user .avatar img {
        border-radius: 50%;
        width: 85px;
        height: 85px;
    }

    .info {
        margin: 40px 0;
        box-shadow: 3px 3px 6px #00000029;
    }

    .info-user_info {
        /*padding: 10% 1% 1% 10% !important;*/
        /*width: 100% !important;*/
    }

    .info-user_info p {
        text-align: left;
        color: #FFFFFF;
        text-transform: uppercase;
        opacity: 1;
        font-size: 13px;
        margin-bottom: 1%;
        margin-top: 4%;
    }

    .username {
        font: Bold 15px Roboto-Bold;
        letter-spacing: 0.6px;

    }

    .userposition {
        letter-spacing: 0.5px;
        font-family: Roboto-Light;
    }

    .info-progress {
        display: inline-flex;
        width: 100%;
        padding: 3% 6%;
    }

    .circle-progress svg {
        margin: 0 auto !important;
    }

    .info-statistic {
        width: 100%;
        padding: 3% 6%;
    }

    .no-padding-col {
        padding: 0 !important;
    }

    .progress-note {
        margin: auto;
    }

    .progress-note ul {
        margin: 0;
    }

    .progress-note ul li {
        list-style: none;
        padding: 5px 0;
        font-size: 14px;
    }

    .info-text {
        color: #202020;
        padding: 5px;
    }

    .text-course {
        text-align: left;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        opacity: 1;
        width: 90%;
        font-size: 14px;
    }

    .text-number {
        text-align: right;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #737373;
        opacity: 1;
        width: 10%;
        font-size: 14px;

    }

    .info-text {
        display: flex;
    }

    .info-text:hover {
        cursor: pointer;
        text-decoration: none;
    }

    .carousel-caption {
        bottom: 0 !important;
    }

    .carousel-caption h3 {
        text-align: left;
        font-family: HelveticaLTStd-Bold;
        letter-spacing: 0px;
        color: <?=$_SESSION["color"]?>;
        opacity: 1;
        font-size: 100px;
    }

    .carousel-caption p {
        text-align: left;
        font-family: HelveticaLTStd-Light;
        text-transform: uppercase;
        opacity: 1;
        color: white;
        font-size: 103px;
        -webkit-text-fill-color: white;
        -webkit-text-stroke-width: 2px;
        -webkit-text-stroke-color: <?=$_SESSION["color"]?>;
        letter-spacing: 7px;
        margin: -3% 0 0 10%;
    }

    .carousel-caption h1 {
        font-family: HelveticaLTStd-Bold;
        color: #ffffff;
        position: absolute;
        font-size: 60px;
        bottom: 25%;
        letter-spacing: 3px;
        z-index: 2;
    }

    .carousel-caption span {
        font-family: HelveticaLTStd-Light;
        /*font-size: 45px;*/
    }

    .slide-logo img {
        position: absolute;
        width: 15%;
        top: 15%;
        left: 3%;
    }

    .slide-image img {
        position: absolute;
        bottom: 0;
        right: 6%;
        width: 25%;
    }

    .carousel-caption {
        left: 5%;
        top: 28%;
    }

    .footer-ul {
        padding: 0;
        padding-left: 5%;
    }

    .footer-ul li {
        list-style: none;
        text-align: left;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 0.45px;
        opacity: 1;
        margin-top: 5%;
    }

    .footer-ul a {
        text-decoration: none;
    }

    .footer-ul li a {
        color: #FFFFFF;
    }

    .footer-title {
        text-align: left;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 0.6px;
        color: #FFFFFF;
        opacity: 1;
        font-size: 20px;
    }

    .footer-logo {
        width: 246px;
        height: 75px;
    }

    .block-note {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-flex;
    }

    .block-item__content_text a {
        text-decoration: none;
    }

    .info-course {
        color: rgba(115, 115, 115, 1);
        min-height: 90px;
    }

    .circular-chart {
        display: block;
        margin: 5% auto;
        max-width: 80%;
        max-height: 240px;
        margin-bottom: 15%;
    }

    .that-circle {
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-dashoffset: 50;
        animation: progress 1s ease-out forwards;
        box-shadow: 0 8px 25px 0 #e5e5e5;
    }

    @keyframes progress {
        100% {
            stroke-dashoffset: 0;
        }

    }

    .percentage {
        fill: #4285f4;
        font-size: 0.375em;
        text-anchor: middle;
        font-family: AvenirNext;
        font-weight: bold;
        font-style: normal;
        font-stretch: normal;
        line-height: 1;
        letter-spacing: normal;
    }

    .percentage {
        font-family: Roboto-Regular;
        fill: <?=$_SESSION["color"]?>;
    }

    .course-mx-5 {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

    .course-row-mx-5 {
        margin-right: -5px !important;
        margin-left: -5px !important;
    }

    .block-items__item:hover {
        box-shadow: 3px 3px 6px #00000069;
    }

    @media screen and (min-width: 2000px) {
        .col-xxl-4 {
            flex: 0 0 33.333333% !important;
            max-width: 33.333333% !important;
        }

        .block-color {
            width: 130px !important;
            bottom: 23%;
        }

        .info-user .avatar img {
            width: 150px;
            height: 150px;
        }
    }

    @media screen and (max-width: 1920px) {
        .info-user .avatar img {
            width: 105px !important;
            height: 105px !important;
            bottom: 26% !important;
        }
    }

    @media screen and (max-width: 1440px) {
        .info-user .avatar img {
            width: 85px !important;
            height: 85px !important;
            bottom: 26% !important;
        }
    }

    @media screen and (max-width: 1024px) {
        .block-color {
            width: 80px !important;
            height: 80px !important;
            bottom: 26% !important;
        }
    }

    @media screen and (max-width: 768px) {
        .title h2 {
            font-size: 20px !important;
        }

        .block-color {
            width: 65px !important;
            height: 65px !important;
            bottom: 28% !important;
        }

        .info-user .avatar img {
            width: 150px !important;
            height: 150px !important;
        }
    }

    @media screen and (max-width: 425px) {
        .block-color {
            bottom: 48% !important;
            width: 49px !important;
        }

        .info-user .avatar img {
            width: 130px !important;
            height: 130px !important;
        }

        .section-footer .container{
            padding: 3% 3%;
        }
    }

    @media screen and (max-width: 375px) {
        .info-user .avatar img {
            width: 100px !important;
            height: 100px !important;
        }
    }

    .silde-carousel {
        background-repeat: no-repeat;
        background-position: 100% 50%;
        background-size: cover;
        background-size: cover;
    }

    .circular-chart {
        margin: 0 !important;
        max-width: 100% !important;
    }

    .footer-block__address {

    }

    .footer-block__address .nav-tabs {
        border: none;
    }

    .footer-block__address .nav-tabs li {
        /*padding: 1% 2%;*/
        display: block;
    }

    .footer-block__address .nav-tabs li.active, .tab-content .active {
        background-color: #222126;
        color: #ffffff;
    }

    .footer-block__address .nav-tabs li a {
        color: #ffffff;
        position: relative;
        display: block;
        padding: 10px 15px;
    }

    .tab-content > .tab-pane {
        padding: 2%;
    }

    .cls::after, .cls::before, .clearfix::after, .clearfix::before {
        content: '';
        display: block;
        clear: both;
    }

    .regions {
        color: #fff;
        margin-bottom: 20px;
        list-style: none;
        font-size: 13px;
        display: inline-grid;
        /*width: 32%;*/
    }

    .regions .name {
        padding-bottom: 4px;
        border-bottom: 1px solid #3a3a3a;
        font-size: 16px;
    }

    .regions .address, .regions .name {
        letter-spacing: 1px;
    }

</style>
<body>
<div class="wrapper"><!-- wrapper -->
    <!--    --><?php //echo $OUTPUT->header(); ?>
    <!--    --><?php //echo  ?>
    <section class="section section--header"><!-- section -->
        <header><!-- header -->
            <div class="content content-slider">
                <div class="slider">
                    <div class="">
                        <div id="demo" class="carousel slide carousel-fade silde-carousel" data-ride="carousel"
                             style="background-image: url('<?php echo $_SESSION["pathBackground"]; ?>')">
                            <div class="container" style="padding: 0">
                                <ul class="carousel-indicators">
                                    <li data-target="#demo" data-slide-to="0" class="active"></li>
                                    <li data-target="#demo" data-slide-to="1"></li>
                                    <li data-target="#demo" data-slide-to="2"></li>
                                </ul>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <div class="carousel-caption">
                                            <h1><?php echo $_SESSION["organizationName"]; ?> <span>Academy</span></h1>
                                            <div class="block-color"></div>
                                        </div>
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#demo" data-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </a>
                                <a class="carousel-control-next" href="#demo" data-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        </header>
    </section>
    <!--    body-->

    <section class="section section-content">
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                        <div class="info row">
                            <div class="info-user info-center">
                                <div class="avatar col-4 info-center">
                                    <img
                                        src="<?php if (is_null($profile->avatar)) echo 'images/avatar.png'; else echo $profile->avatar; ?>"
                                        alt="">
                                </div>
                                <div class="info-user_info col-8">
                                    <p class="username"><?php echo $profile->fullname; ?></p>
                                    <p class="userposition"><?php if (is_null($profile->exactlypostion)) echo $profile->position; else echo $profile->exactlypostion; ?></p>
                                </div>
                            </div>
                            <div class="info-progress">
                                <div class="circle-progress no-padding-col col-4" style="margin: auto">
                                    <svg viewBox="0 0 36 36" width="150" class="circular-chart">
                                        <path class="that-circle" stroke="#C7C7C7" stroke-dasharray="100,100" d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="that-circle" stroke="#FFC400"
                                              stroke-dasharray="<?php echo $percentCompleted + $percentStudying; ?>,100"
                                              d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="that-circle" stroke="<?= $_SESSION["color"] ?>"
                                              stroke-dasharray="<?php echo $percentCompleted; ?>,100" d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <text x="18" y="20.35" class="percentage"><?php echo $percentCompleted; ?> %
                                        </text>
                                    </svg>
                                </div>
                                <div class="progress-note no-padding-col col-8">
                                    <ul>
                                        <li class="li-progress completed">
                                            <div class="block-note"
                                                 style="background-color: <?= $_SESSION["color"] ?>"></div>
                                            Completed
                                        </li>
                                        <li class="li-progress studying">
                                            <div class="block-note" style="background-color: #FFC400"></div>
                                            Studying
                                        </li>
                                        <li class="li-progress not-learn">
                                            <div class="block-note" style="background-color: #C7C7C7"></div>
                                            Not yet learned
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="info-statistic pb-2">
                                <div class="info-statistic__current-course">
                                    <a class="info-text" href="lms/course/index.php?progress=1&type=current">
                                        <div class="text-course">Current courses</div>
                                        <div class="text-number"><?php echo count($courses_current); ?></div>
                                    </a>
                                </div>
                                <div class="info-statistic__all-required">
                                    <a class="info-text" href="lms/course/index.php?progress=1&type=required">
                                        <div class="text-course">Required courses</div>
                                        <div class="text-number"><?php echo $countRequiredCourses; ?></div>
                                    </a>
                                </div>
                                <div class="info-statistic__completed-courses">
                                    <a class="info-text" href="lms/course/index.php?progress=1&type=completed">
                                        <div class="text-course">Completed courses</div>
                                        <div class="text-number"><?php echo count($courses_completed); ?></div>
                                    </a>
                                </div>
                                <div class="info-statistic__completed-courses">
                                    <a class="info-text" href="lms/course/index.php?progress=1&type=other">
                                        <div class="text-course">Optional courses</div>
                                        <div class="text-number"><?php echo count($coursesSuggest); ?></div>
                                    </a>
                                </div>
                                <div class="info-statistic__profile">
                                    <a class="info-text text-course"
                                       href="lms/user/profile.php?id=<?php echo $USER->id; ?>">
                                        Your Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12" style="padding: 0 0 0 5%;">
                        <div class="courses">
                            <!--current course-->
                            <div class="courses-block">
                                <!--top-->
                                <div class="course-block__top">
                                    <div class="course-block__top-show row">
                                        <div class="col-6 title"><h2>Current <span>Courses</span></h2></div>
                                        <div class="col-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a
                                                    href="lms/course/index.php?progress=1&type=current">Show All</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!--content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row course-row-mx-5">
                                        <?php if (count($courses_current) > 0) { ?>
                                            <?php $countBlock = 1;
                                            //get first training id of list course
                                            $training_id = array_values($courses_current)[0]->training_id;
                                            foreach ($courses_current as $course) { ?>
                                                <div class="col-xxl-4 col-md-6 col-sm-6 col-xs-12 mb-3 course-mx-5">
                                                    <div class="block-items__item">
                                                        <div class="block-item__image col-5"
                                                             style="background-image: url('<?php echo $CFG->wwwtmsbase . $course->course_avatar; ?>')">
                                                            <img src="<?php echo $_SESSION['component'] ?>"
                                                                 alt=""><span><?php echo intval($course->numoflearned * 100 / $course->numofmodule); ?>%</span>
                                                        </div>
                                                        <div class="block-item__content col-7">
                                                            <div class="block-item__content_text">
                                                                <a href="lms/course/view.php?id=<?php echo $course->id; ?>"
                                                                   title="<?php echo $course->fullname; ?>"><p
                                                                        class="title-course">
                                                                        <i></i><?php echo $course->fullname; ?></p></a>
                                                                <div class="info-course">
                                                                    <?php if (!empty($course->teacher_name)) { ?>
                                                                        <a class="teacher" data-toggle="modal"
                                                                           data-target="#exampleModal"
                                                                           data-teacher-name="<?php echo $course->teacher_name; ?>"
                                                                           data-teacher-position="<?php echo ucfirst($course->teacher_position) ?>"
                                                                           data-teacher-organization="<?php echo $course->teacher_organization ?>"
                                                                           data-teacher-description="<?php echo $course->teacher_description ?>">
                                                                            <i class="fa fa-user"
                                                                               aria-hidden="true"></i>&nbsp;<?php if (!empty($course->teacher_name)) echo $course->teacher_name; else echo "No teacher assign"; ?>
                                                                        </a>
                                                                    <?php } ?>
                                                                    <?php if (!empty($course->training_name)) { ?>
                                                                        <p class="units"><i class="fa fa-file"
                                                                                            aria-hidden="true"></i> <?php echo $course->training_name; ?>
                                                                        </p>
                                                                    <?php } ?>
                                                                    <p class="units"><i class="fa fa-clock-o"
                                                                                        aria-hidden="true"></i> <?php echo $course->estimate_duration; ?>
                                                                        hours</p>
                                                                </div>
                                                            </div>
                                                            <?php if ($course->training_deleted == 0) { ?>
                                                                <p class="number-order"><?php echo $course->sttShow; ?></p>
                                                            <?php } else { ?>
                                                                <p class="number-order number-order-hide"></p>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $countBlock++;
                                                if ($countBlock == 5) break;
                                            } ?>
                                        <?php } else { ?>
                                            <div class="col-12">
                                                <h3>No course to display</h3>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!--all required courses-->
                            <div class="courses-block">
                                <!--top-->
                                <div class="course-block__top">
                                    <div class="course-block__top-show row">
                                        <div class="col-6 title"><h2>Required <span>Courses</span></h2></div>
                                        <div class="col-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a
                                                    href="lms/course/index.php?progress=1&type=required">Show All</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!--content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row course-row-mx-5">
                                        <?php if (count($courses_required) > 0) { ?>
                                            <?php $countBlock = 1;
                                            foreach ($courses_required as $courses_traning) {
                                                //defined enable
                                                $enable = 'enable';
                                                $allow = true;
                                                //get first training id of liest course
                                                $training_id = array_values($courses_traning)[0]->training_id;
                                                //if exists in list competency => it learning => disable to learn
                                                if (in_array($training_id, $competency_exists) || in_array($training_id, $competency_completed)) {
                                                    $enable = 'disable';
                                                    $allow = false;
                                                }
                                                foreach ($courses_traning as $course) {
                                                    if ($course->training_deleted == 2 || !$allow) continue; ?>
                                                    <div class="col-xxl-4 col-md-6 col-sm-6 col-xs-12 mb-3 course-mx-5">
                                                        <div class="block-data">
                                                            <div class="block-items__item <?php echo $enable; ?>">
                                                                <div class="block-item__image col-5"
                                                                     style="background-image: url('<?php echo $CFG->wwwtmsbase . $course->course_avatar; ?>')">
                                                                    <div
                                                                        class="div-info-progress-<?php echo $enable; ?>">
                                                                        <img src="<?php echo $_SESSION['component'] ?>"
                                                                             alt=""><span><?php echo intval($course->numoflearned * 100 / $course->numofmodule); ?>%</span>
                                                                    </div>
                                                                    <div class="div-<?php echo $enable; ?>"></div>
                                                                </div>
                                                                <div class="block-item__content col-7">
                                                                    <div class="block-item__content_text">
                                                                        <a href="lms/course/view.php?id=<?php echo $course->id; ?>"
                                                                           title="<?php echo $course->fullname; ?>"><p
                                                                                class="title-course">
                                                                                <i></i><?php echo $course->fullname; ?>
                                                                            </p></a>
                                                                        <div class="info-course">
                                                                            <?php if (!empty($course->teacher_name)) { ?>
                                                                                <a class="teacher" data-toggle="modal"
                                                                                   data-target="#exampleModal"
                                                                                   data-teacher-name="<?php echo $course->teacher_name; ?>"
                                                                                   data-teacher-position="<?php echo ucfirst($course->teacher_position) ?>"
                                                                                   data-teacher-organization="<?php echo $course->teacher_organization ?>"
                                                                                   data-teacher-description="<?php echo $course->teacher_description ?>">
                                                                                    <i class="fa fa-user"
                                                                                       aria-hidden="true"></i>&nbsp;<?php if (!empty($course->teacher_name)) echo $course->teacher_name; else echo "No teacher assign"; ?>
                                                                                </a>
                                                                            <?php } ?>
                                                                            <?php if (!empty($course->training_name)) { ?>
                                                                                <p class="units"><i class="fa fa-file"
                                                                                                    aria-hidden="true"></i> <?php echo $course->training_name; ?>
                                                                                </p>
                                                                            <?php } ?>
                                                                            <p class="units"><i class="fa fa-clock-o"
                                                                                                aria-hidden="true"></i> <?php echo $course->estimate_duration; ?>
                                                                                hours</p>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ($course->training_deleted == 0) { ?>
                                                                        <p class="number-order"><?php echo $course->sttShow; ?></p>
                                                                    <?php } else { ?>
                                                                        <p class="number-order number-order-hide"></p>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php $countBlock++;
                                                    $enable = 'disable';
                                                    break;
//                                                    if ($countBlock == 5) break;
                                                }
                                                if ($countBlock == 5) break;
                                            }
                                        ;
                                            if ($countBlock < 5) {
                                                foreach ($courses_required as $courses_traning) {
                                                    $enable = 'enable';
                                                    $course_traning = array_values($courses_traning)[0];
                                                    //get first training id of liest course
                                                    $training_id = array_values($courses_traning)[0]->training_id;
                                                    if (in_array($training_id, $competency_exists)){
                                                        $enable = 'disable';
                                                        //do nothing
                                                    }
                                                    elseif($course_traning->training_deleted == 0){
                                                        array_shift($courses_traning);
                                                        $enable = 'disable';
                                                    }
                                                    foreach ($courses_traning as $course) { ?>
                                                        <div
                                                            class="col-xxl-4 col-md-6 col-sm-6 col-xs-12 mb-3 course-mx-5">
                                                            <div class="block-data">
                                                                <div class="block-items__item <?php echo $enable; ?>">
                                                                    <div class="block-item__image col-5"
                                                                         style="background-image: url('<?php echo $CFG->wwwtmsbase . $course->course_avatar; ?>')">
                                                                        <div
                                                                            class="div-info-progress-<?php echo $enable; ?>">
                                                                            <img
                                                                                src="<?php echo $_SESSION['component'] ?>"
                                                                                alt=""><span><?php echo intval($course->numoflearned * 100 / $course->numofmodule); ?>%</span>
                                                                        </div>
                                                                        <div class="div-<?php echo $enable; ?>"></div>
                                                                    </div>
                                                                    <div class="block-item__content col-7">
                                                                        <div class="block-item__content_text">
                                                                            <a href="lms/course/view.php?id=<?php echo $course->id; ?>"
                                                                               title="<?php echo $course->fullname; ?>">
                                                                                <p
                                                                                    class="title-course">
                                                                                    <i></i><?php echo $course->fullname; ?>
                                                                                </p></a>
                                                                            <div class="info-course">
                                                                                <?php if (!empty($course->teacher_name)) { ?>
                                                                                    <a class="teacher"
                                                                                       data-toggle="modal"
                                                                                       data-target="#exampleModal"
                                                                                       data-teacher-name="<?php echo $course->teacher_name; ?>"
                                                                                       data-teacher-position="<?php echo ucfirst($course->teacher_position) ?>"
                                                                                       data-teacher-organization="<?php echo $course->teacher_organization ?>"
                                                                                       data-teacher-description="<?php echo $course->teacher_description ?>">
                                                                                        <i class="fa fa-user"
                                                                                           aria-hidden="true"></i>&nbsp;<?php if (!empty($course->teacher_name)) echo $course->teacher_name; else echo "No teacher assign"; ?>
                                                                                    </a>
                                                                                <?php } ?>
                                                                                <?php if (!empty($course->training_name)) { ?>
                                                                                    <p class="units"><i
                                                                                            class="fa fa-file"
                                                                                            aria-hidden="true"></i> <?php echo $course->training_name; ?>
                                                                                    </p>
                                                                                <?php } ?>
                                                                                <p class="units"><i
                                                                                        class="fa fa-clock-o"
                                                                                        aria-hidden="true"></i> <?php echo $course->estimate_duration; ?>
                                                                                    hours</p>
                                                                            </div>
                                                                        </div>
                                                                        <?php if ($course->training_deleted == 0) { ?>
                                                                            <p class="number-order"><?php echo $course->sttShow; ?></p>
                                                                        <?php } else { ?>
                                                                            <p class="number-order number-order-hide"></p>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php $countBlock++;
                                                        $enable = 'disable';
                                                        if ($countBlock == 5) break;
                                                    }
                                                    if ($countBlock == 5) break;
                                                }
                                            }
                                            ?>
                                        <?php } else { ?>
                                            <div class="col-12">
                                                <h3>No course to display</h3>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!--completed courses-->
                            <div class="courses-block">
                                <!--top-->
                                <div class="course-block__top">
                                    <div class="course-block__top-show row">
                                        <div class="col-6 title"><h2>Completed <span>Courses</span></h2></div>
                                        <div class="col-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a
                                                    href="lms/course/index.php?progress=1&type=completed">Show All</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!--content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row course-row-mx-5">
                                        <?php if (count($courses_completed) > 0) { ?>
                                            <?php $countBlock = 1;
                                            foreach ($courses_completed as $course) { ?>
                                                <div class="col-xxl-4 col-md-6 col-sm-6 col-xs-12 mb-3 course-mx-5">
                                                    <div class="block-items__item">
                                                        <div class="block-item__image block-item__image_complete col-5"
                                                             style="background-image: url('<?php echo $CFG->wwwtmsbase . $course->course_avatar; ?>')">
                                                            <img src="<?php echo $CFG->wwwtmsbase . $pathBadge; ?>"
                                                                 alt="">
                                                            <!--                                                            <span class="sp-name-course">-->
                                                            <?php //echo $course->fullname; ?><!--</span>-->
                                                        </div>
                                                        <div class="block-item__content col-7">
                                                            <div class="block-item__content_text">
                                                                <a href="lms/course/view.php?id=<?php echo $course->id; ?>"
                                                                   title="<?php echo $course->fullname; ?>"><p
                                                                        class="title-course">
                                                                        <i></i><?php echo $course->fullname; ?></p></a>
                                                                <div class="info-course">
                                                                    <?php if (!empty($course->teacher_name)) { ?>
                                                                        <a class="teacher" data-toggle="modal"
                                                                           data-target="#exampleModal"
                                                                           data-teacher-name="<?php echo $course->teacher_name; ?>"
                                                                           data-teacher-position="<?php echo ucfirst($course->teacher_position) ?>"
                                                                           data-teacher-organization="<?php echo $course->teacher_organization ?>"
                                                                           data-teacher-description="<?php echo $course->teacher_description ?>">
                                                                            <i class="fa fa-user"
                                                                               aria-hidden="true"></i>&nbsp;<?php if (!empty($course->teacher_name)) echo $course->teacher_name; else echo "No teacher assign"; ?>
                                                                        </a>
                                                                    <?php } ?>
                                                                    <?php if (!empty($course->training_name)) { ?>
                                                                        <p class="units"><i class="fa fa-file"
                                                                                            aria-hidden="true"></i> <?php echo $course->training_name; ?>
                                                                        </p>
                                                                    <?php } ?>
                                                                    <p class="units"><i class="fa fa-clock-o"
                                                                                        aria-hidden="true"></i> <?php echo $course->estimate_duration; ?>
                                                                        hours</p>
                                                                </div>
                                                            </div>
                                                            <?php if ($course->training_deleted == 0) { ?>
                                                                <p class="number-order"><?php echo $course->sttShow; ?></p>
                                                            <?php } else { ?>
                                                                <p class="number-order number-order-hide"></p>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $countBlock++;
                                                if ($countBlock == 5) break;
                                            } ?>
                                        <?php } else { ?>
                                            <div class="col-12">
                                                <h3>No course to display</h3>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!--other courses-->
                            <div class="courses-block">
                                <!--top-->
                                <div class="course-block__top">
                                    <div class="course-block__top-show row">
                                        <div class="col-6 title"><h2>Optional <span>Courses</span></h2></div>
                                        <div class="col-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a
                                                    href="lms/course/index.php?progress=1&type=other">Show All</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!--content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row course-row-mx-5">
                                        <?php if (count($coursesSuggest) > 0) { ?>
                                            <?php $countBlock = 1;
                                            foreach ($coursesSuggest as $course) { ?>
                                                <div class="col-xxl-4 col-md-6 col-sm-6 col-xs-12 mb-3 course-mx-5">
                                                    <div class="block-items__item">
                                                        <div class="block-item__image col-5"
                                                             style="background-image: url('<?php echo $CFG->wwwtmsbase . $course->course_avatar; ?>')">
                                                        </div>
                                                        <div class="block-item__content col-7">
                                                            <div class="block-item__content_text">
                                                                <a href="lms/course/view.php?id=<?php echo $course->id; ?>"
                                                                   title="<?php echo $course->fullname; ?>"><p
                                                                        class="title-course">
                                                                        <i></i><?php echo $course->fullname; ?></p></a>
                                                                <div class="info-course">
                                                                    <?php if (!empty($course->teacher_name)) { ?>
                                                                        <a class="teacher" data-toggle="modal"
                                                                           data-target="#exampleModal"
                                                                           data-teacher-name="<?php echo $course->teacher_name; ?>"
                                                                           data-teacher-position="<?php echo ucfirst($course->teacher_position) ?>"
                                                                           data-teacher-organization="<?php echo $course->teacher_organization ?>"
                                                                           data-teacher-description="<?php echo $course->teacher_description ?>">
                                                                            <i class="fa fa-user"
                                                                               aria-hidden="true"></i>&nbsp;<?php if (!empty($course->teacher_name)) echo $course->teacher_name; else echo "No teacher assign"; ?>
                                                                        </a>
                                                                    <?php } ?>
                                                                    <p class="units"><i class="fa fa-clock-o"
                                                                                        aria-hidden="true"></i> <?php echo $course->estimate_duration; ?>
                                                                        hours</p>
                                                                </div>
                                                            </div>
                                                            <p class="number-order number-order-hide"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $countBlock++;
                                                if ($countBlock == 5) break;
                                            } ?>
                                        <?php } else { ?>
                                            <div class="col-12">
                                                <h3>No course to display</h3>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="path-calendar">
        <div class="container">
            <div class="row">
                <?php

                require_once(__DIR__ . '/../../../../config.php');
                require_once(__DIR__ . '/../../../../course/lib.php');
                require_once(__DIR__ . '/../../../../calendar/lib.php');

                $categoryid = optional_param('category', null, PARAM_INT);
                $courseid = optional_param('course', SITEID, PARAM_INT);
                $view = optional_param('view', 'month', PARAM_ALPHA);
                $time = time();
                $lookahead = optional_param('lookahead', null, PARAM_INT);

                $calendar = calendar_information::create($time, $courseid, $categoryid);

                $PAGE->navbar->add(userdate($time, get_string('strftimemonthyear')));
                // Print title and header
                $PAGE->set_pagelayout('standard');
                //        $PAGE->set_title("$course->shortname: $strcalendar: $pagetitle");
                //        $PAGE->set_heading($COURSE->fullname);

                $renderer = $PAGE->get_renderer('core_calendar');
                $calendar->add_sidecalendar_blocks($renderer, true, $view);
                echo $OUTPUT->header();
                echo $renderer->start_layout();
                echo html_writer::start_tag('div', array('class' => 'heightcontainer'));
                echo $OUTPUT->heading(get_string('calendar', 'calendar'));
                list($data, $template) = calendar_get_view($calendar, $view, true, false, $lookahead);
                echo $renderer->render_from_template($template, $data);

                echo html_writer::end_tag('div');

                list($data, $template) = calendar_get_footer_options($calendar);
                echo $renderer->render_from_template($template, $data);

                echo $renderer->complete_layout();
                echo $OUTPUT->footer();

                ?>
            </div>
        </div>
    </section>

    <section class="section-footer">
        <footer>
            <div class="container">
                <div class="row">
                    <img src="<?php echo $_SESSION["pathLogoWhite"]; ?>" alt="Logo" class="footer-logo mt-1">
                </div>
                <div class="row mb-3">
                    <!--Home-->
                    <div class="footer-block col-12 col-sm-2 col-xs-6">
                        <div class="footer-block__title"><p class="footer-title">Home</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                                <li><a href="lms/course/index.php">Courses</a></li>
                                <li><a href="lms/user/profile.php?id=<?php echo $USER->id; ?>">Profile</a></li>
                                <?php if($allowCms){ ?>
                                    <li><a href="/tms/dashboard">CMS</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <!--FAQs-->
                    <div class="footer-block col-12 col-sm-2 col-xs-6">
                        <div class="footer-block__title"><p class="footer-title">FAQs</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                            </ul>
                        </div>
                    </div>
                    <!--Contact-->
                    <div class="footer-block col-12 col-sm-8 col-xs-6">
                        <div class="footer-block__title"><p class="footer-title">Contact</p></div>
                        <div class="footer-block__ul footer-block__address">
                            <ul class="nav nav-tabs">
                                <?php $count = 1; $active = 'active';
                                foreach ($footerAddressesTab as $footerAddressTab) { ?>
                                        <li class="li-address <?php echo $active; ?>"><a data-toggle="tab"
                                                                         href="#menu<?php echo $count; ?>"><?php echo $footerAddressTab; ?></a>
                                        </li>
                                    <?php $count++; $active=''; }  ?>
                            </ul>
                            <div class="tab-content">
                                <?php $count = 1;
                                $active = 'active';
                                foreach ($footerAddresses as $footerAddress) { ?>
                                    <div id="menu<?php echo $count; ?>" class="tab-pane in <?php echo $active; ?>">
                                        <div class="content-address cls">
                                            <?php foreach ($footerAddress as $footer) { ?>
                                                <ul class="regions">
                                                    <li class="name"><?php echo $footer->name; ?></li>
                                                    <li class="address"><i class="fa fa-map-marker"
                                                                           aria-hidden="true"></i>
                                                        <?php echo $footer->address; ?>
                                                    </li>
                                                </ul>
                                            <?php }
                                            $count++;
                                            $active = ''; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </section>
    <!--    --><?php //echo $OUTPUT->footer(); ?>
</div>
<script>
    $(function () {
        localStorage.setItem('courses', '<?php echo json_encode($course); ?>');

        // var heightTopBar = $('.navbar-expand').outerHeight();
        // $('.content-slider').css('margin-top', heightTopBar+'px');

        $('.li-progress').click(function () {
            var classes = $(this).attr('class');
            if (classes.indexOf('studying') > 0) {
                $('.percentage').text(<?php echo $percentStudying; ?> +' %');
                $('.percentage').css('fill', '#FFC400');
            } else if (classes.indexOf('not-learn') > 0) {
                $('.percentage').text(<?php echo(100 - $percentStudying - $percentCompleted); ?> +' %');
                $('.percentage').css('fill', '#C7C7C7');
            } else if (classes.indexOf('completed') > 0) {
                $('.percentage').text(<?php echo $percentCompleted; ?> +' %');
                $('.percentage').css('fill', '<?php echo $_SESSION["color"]; ?>');
            }
        });

        $('.nav-tabs li').click(function () {
            $('.li-address').removeClass('active');
            $(this).addClass('active');
        });

    });

    $(document).on('show.bs.modal', '#exampleModal', function (event) {
        let button = $(event.relatedTarget) // Button that triggered the modal
        //var teacher_name = button.attr("data-teacher-name");
        let teacher_name = button.data('teacher-name');// Extract info from data-* attributes
        let teacher_position = button.data('teacher-position').length > 0 ? button.data('teacher-position') : 'N/A';
        let teacher_organization = button.data('teacher-organization').length > 0 ? button.data('teacher-organization') : 'PHH Group';
        let teacher_description = button.data('teacher-description').length > 0 ? button.data('teacher-description') : 'N/A';
        $("span.teacher-name").html(teacher_name);
        $("span.teacher-position").html(teacher_position);
        $("span.teacher-organization").html(teacher_organization);
        $("span.teacher-description").html(teacher_description);
    })
</script>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">Instructor Info</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Name: <span class="teacher-name" style="font-weight: bold"></span><br>
                Position: <span class="teacher-position" style="font-weight: bold"></span><br>
                Description: <span class="teacher-description" style="font-weight: bold"></span><br>
                Organization: <span class="teacher-organization" style="font-weight: bold"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
die;
?>
