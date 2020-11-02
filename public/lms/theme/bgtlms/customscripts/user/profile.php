<?php
if (!isloggedin()) {
    require_login();
}
require_once(__DIR__ . '/../../../../config.php');

$sqlGetCategories = 'select id, name from mdl_course_categories where id NOT IN (7, 5, 2)';
$categories = array_values($DB->get_records_sql($sqlGetCategories));

$sqlGetCertificates = 'select tms_traninning_programs.name as name, student_certificate.timecertificate as timecertificate, student_certificate.code as code from student_certificate join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 2 and tms_traninning_programs.auto_certificate = 1 and student_certificate.userid = ' . $USER->id;
$certificates = array_values($DB->get_records_sql($sqlGetCertificates));

$sqlGetBadges = 'select tms_traninning_programs.name as name, student_certificate.timecertificate as timecertificate, student_certificate.code as code from student_certificate join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 2 and tms_traninning_programs.auto_badge = 1 and student_certificate.userid = ' . $USER->id;
$badges = array_values($DB->get_records_sql($sqlGetBadges));

/* SQL count courses */
//Get direct organization and recursive parent

$sqlGetOrganization = 'SELECT T2.id, T2.`name`, T2.`code`, T2.`parent_id`, T2.`level`
FROM (
    SELECT
        @r AS _id,
        (SELECT @r := parent_id FROM tms_organization WHERE id = _id) AS parent_id,
        @l := @l + 1 AS lvl
    FROM
        (SELECT @r := (select organization_id from tms_organization_employee where user_id= ' . $USER->id . '), @l := 0) vars,
        tms_organization m
    WHERE @r <> 0) T1
JOIN tms_organization T2
ON T1._id = T2.id
ORDER BY T1.lvl DESC';

$organizations = array_values($DB->get_records_sql($sqlGetOrganization));
$reverse_recursive_org_ids = [];
if (!empty($organizations)) {
    foreach ($organizations as $organization_item) {
        $reverse_recursive_org_ids[] = $organization_item->id;
    }
}

$student_role_id = 5;
$teacher_role_id = 4;
$sttTotalCourse = 0;
$couresIdAllow = array();
$courses_current = array();
$courses_required = array();
$courses_completed = array();

//Lấy khóa học mà user enrol theo training, khóa lẻ
$sql_training = 'select @s:=@s+1 stt,
mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
 ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 AND cm.completion <> 0 and cm.course = mc.id) as numofmodule,
  ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate in (1, 2) AND cm.completion <> 0 and  cm.course = mc.id and cmc.userid = tud.user_id) as numoflearned,
	(select count(id) from mdl_user_enrolments where userid = tud.user_id and enrolid in (select id from mdl_enrol where courseid = mc.id and mdl_enrol.roleid = ' . $student_role_id . ')) as enrol_count,

	muet.userid as teacher_id,
    tudt.fullname as teacher_name,
    tor.name as teacher_organization,
    muet.timecreated as teacher_created,
    toe.position as teacher_position,
    toe.description as teacher_description,

	ttp.id as training_id,
	ttp.name as training_name,
	ttp.deleted as training_deleted,
	ttp.style as training_style,

    ttc.order_no,
    GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers

  from tms_user_detail tud

  inner join tms_traninning_users ttu on ttu.user_id = tud.user_id
  inner join tms_trainning_courses ttc on ttu.trainning_id = ttc.trainning_id
  inner join tms_traninning_programs ttp on ttc.trainning_id = ttp.id
  inner join mdl_course mc on ttc.course_id = mc.id

  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . ' and met.enrol = "manual"
  left join mdl_user_enrolments muet on met.id = muet.enrolid
  left join tms_user_detail tudt on tudt.user_id = muet.userid
  left join tms_organization_employee toe on toe.user_id = muet.userid
  left join tms_organization tor on tor.id = toe.organization_id

  , (SELECT @s:= 0) AS s

  where ttc.deleted <> 1
  and mc.deleted = 0
  and mc.visible = 1
  and mc.category NOT IN (2,7)
  and ttp.style NOT IN (2)
  and tud.user_id = ' . $USER->id;

$sql_training .= ' group by mc.id'; //cần để tạo tên giáo viên
//$sql_training .= ' ORDER BY ttp.id, ttc.order_no'; // Remove when union

//Có enrol
$sql_training .=  ' having enrol_count > 0';

//Lấy khóa học pqdl
$sql_pqdl = 'select @s:=@s+1 stt,
mc.id,
mc.fullname,
mc.category,
mc.course_avatar,
mc.estimate_duration,
( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections,
 ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 AND cm.completion <> 0 and cm.course = mc.id) as numofmodule,
  ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate in (1, 2) AND cm.completion <> 0 and  cm.course = mc.id and cmc.userid = tud.user_id) as numoflearned,
	(select count(id) from mdl_user_enrolments where userid = tud.user_id and enrolid in (select id from mdl_enrol where courseid = mc.id and mdl_enrol.roleid = ' . $student_role_id . ')) as enrol_count,

	muet.userid as teacher_id,
    tudt.fullname as teacher_name,
    tort.name as teacher_organization,
    muet.timecreated as teacher_created,
    toet.position as teacher_position,
    toet.description as teacher_description,

	ttp.id as training_id,
	ttp.name as training_name,
	ttp.deleted as training_deleted,
	ttp.style as training_style,

    ttc.order_no,
    GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers

  from tms_user_detail tud

  inner join tms_organization_employee toe on toe.user_id = tud.user_id
  inner join tms_role_organization tro on tro.organization_id = toe.organization_id
  inner join tms_role_course trc on tro.role_id = trc.role_id
  inner join mdl_course mc on trc.course_id = mc.id

  left join tms_trainning_courses ttc on ttc.course_id = mc.id
  left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id

  left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . ' AND met.enrol = "manual"
  left join mdl_user_enrolments muet on met.id = muet.enrolid
  left join tms_user_detail tudt on tudt.user_id = muet.userid
  left join tms_organization_employee toet on toet.user_id = muet.userid
  left join tms_organization tort on tort.id = toet.organization_id

  , (SELECT @s:= 0) AS s

  where ttc.deleted <> 1
  and mc.deleted = 0
  and mc.visible = 1
  and mc.category NOT IN (2,7)
  and ttp.style NOT IN (2)
  and tud.user_id = ' . $USER->id;


$sql_pqdl .= ' group by mc.id'; //cần để tạo tên giáo viên

//Có enrol
$sql_pqdl .=  ' having enrol_count > 0';

//28102020 not works => move outside select alias
//$sql_pqdl .= ' ORDER BY ttp.id, ttc.order_no';
//$sql = '(' . $sql_training . ') UNION ALL (' . $sql_pqdl . ')';

$sql = 'select * from ((' . $sql_training . ') UNION ALL (' . $sql_pqdl . ')) courses order by training_id, order_no';

$courses = array_values($DB->get_records_sql($sql));

$courses_others_id = '(0';
foreach ($courses as &$course) {
    $course->is_optional = 0; //current, completed, required
    $courses_training[$course->training_id][$course->id] = $course;
    array_push($couresIdAllow, $course->id); //Lay data cho courses cho phep
    $courses_others_id .= ', ' . $course->id;
}
$courses_others_id .= ')';

//Optional courses
/* Chỉ lấy các khóa chưa enrol, các khóa đã enrol sẽ chuyển lên block learning, completed, required courses phía trên */


if (!empty($reverse_recursive_org_ids)) {

    $reverse_recursive_org_ids_string = implode(',', $reverse_recursive_org_ids);

    $sqlCourseNotEnrol = 'select @s:=@s+1 stt,
        mc.id,
        mc.fullname,
        mc.category,
        mc.course_avatar,
        (
        select
            count(cm.id) as num
        from
            mdl_course_modules cm
        inner join mdl_course_sections cs on
            cm.course = cs.course
            and cm.section = cs.id
        where
            cs.section <> 0
            and cm.completion <> 0
            and cm.course = mc.id) as numofmodule,
        (
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
            and cmc.completionstate in (1,
            2)
            and cm.completion <> 0
            and cm.course = mc.id
            and cmc.userid = '.$USER->id.') as numoflearned,

		(
		select count(id)
		from mdl_user_enrolments
		where userid = ' . $USER->id . '
		and enrolid in (
			select id
			from mdl_enrol
			where courseid = mc.id
			and mdl_enrol.roleid = ' . $student_role_id . '
			)
		) as enrol_count,

        mc.estimate_duration,

        muet.userid as teacher_id,
        tudt.fullname as teacher_name,
        toet.position as teacher_position,
        tort.name as teacher_organization,
        muet.timecreated as teacher_created,

		ttp.id as training_id,
		ttp.name as training_name,
		ttp.deleted as training_deleted,
		ttp.style as training_style,
        ttc.order_no,
        GROUP_CONCAT(CONCAT(tudt.fullname, " created_at ",  muet.timecreated)) as teachers

        from tms_optional_courses toc
        inner join mdl_course mc on toc.course_id = mc.id
        left join tms_trainning_courses ttc on mc.id = ttc.course_id
        left join tms_traninning_programs ttp on ttc.trainning_id = ttp.id

		left join mdl_enrol met on mc.id = met.courseid AND met.roleid = ' . $teacher_role_id . ' AND met.enrol = "manual"
		left join mdl_user_enrolments muet on met.id = muet.enrolid
		left join tms_user_detail tudt on tudt.user_id = muet.userid
		left join tms_organization_employee toet on toet.user_id = muet.userid
		left join tms_organization tort on tort.id = toet.organization_id, (SELECT @s:= 0) AS s

        where
        mc.deleted = 0
        and mc.category NOT IN (2,7)
        and mc.visible = 1
		and ttc.deleted <> 1
		and ttp.style <> 2
		AND toc.organization_id IN (' . $reverse_recursive_org_ids_string . ')
        and mc.id NOT IN ' . $courses_others_id;

    $sqlCourseNotEnrol .= ' group by mc.id'; //cần để tạo tên giáo viên
    $sqlCourseNotEnrol .= ' ORDER BY ttp.id, ttc.order_no';

    $allCoursesSuggest = array_values($DB->get_records_sql($sqlCourseNotEnrol));

    //Đẩy course option vào mảng chung
    foreach ($allCoursesSuggest as &$course_optional) {
        $course_optional->is_optional = 1;
        $courses_training[$course_optional->training_id][$course_optional->id] = $course_optional;
    }
}

foreach ($courses_training as $courses) {
    foreach ($courses as &$course) {
        //current first
        if ($course->numofmodule > 0 && $course->numoflearned / $course->numofmodule > 0 && $course->numoflearned / $course->numofmodule < 1) {
            push_course($courses_current, $course);
        } //then complete
        elseif ($course->numofmodule > 0 && $course->numoflearned / $course->numofmodule == 1) {
            push_course($courses_completed, $course);
        } //then required = khoa hoc trong khung nang luc
        elseif ($course->training_name && ($course->training_deleted == 0 || $course->training_deleted == 2) && $course->is_optional == 0) {
            push_course($courses_required, $course);
        } elseif ($course->is_optional == 1) { // Duyệt optinal courses only
            if ($course->enrol_count > 0) { //Đã enrol => required courses
                push_course($courses_required, $course);
            } else { // Chưa enrol => optional courses
                $coursesSuggest[] = $course;
            }
        }
        $sttTotalCourse++;
        $couresIdAllow[] = $course->id;
    }
}

//Functions
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

session_start();

//Set necessary session data: home and this screen

$_SESSION["courses_current"] = $courses_current;
$_SESSION["courses_required"] = $courses_required;
$_SESSION["courses_completed"] = $courses_completed;
$_SESSION["coursesSuggest"] = $coursesSuggest;
$_SESSION["totalCourse"] = $sttTotalCourse;
$_SESSION["couresIdAllow"] = $couresIdAllow;

$percentCompleted = 0;
$percentStudying = 0;
if ($_SESSION["totalCourse"] > 0) {
    $percentCompleted = round(count($_SESSION["courses_completed"]) * 100 / $_SESSION["totalCourse"]);
    $percentStudying = round(count($_SESSION["courses_current"]) * 100 / $_SESSION["totalCourse"]);
}

$user_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : $USER->id;
?>

<html>
<title>User profile</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/highcharts.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="//unpkg.com/vue-plain-pagination@0.2.1"></script>

<style>
    @font-face {
        font-family: Nunito-Sans;
        src: url('fonts/NunitoSans-Black.ttf');
    }

    @font-face {
        font-family: Nunito-Sans-Regular;
        src: url('fonts/NunitoSans-Regular.ttf');
    }

    @font-face {
        font-family: Nunito-Sans-Bold;
        src: url('fonts/NunitoSans-Bold.ttf');
    }

    @font-face {
        font-family: Roboto-Bold;
        src: url('fonts/Roboto-Bold.ttf');
    }

    @font-face {
        font-family: Roboto-Regular;
        src: url('fonts/Roboto-Regular.ttf');
    }

    @font-face {
        font-family: Roboto-Italic;
        src: url('fonts/Roboto-Italic.ttf');
    }

    @font-face {
        font-family: Nunito-Bold;
        src: url('fonts/Nunito-Bold.ttf');
    }

    a {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    img {
        width: 100%;
    }

    body {
        font-size: 14px;
        font-family: Roboto-Bold;
        background-color: #F1F1F1;
    }

    ul {
        list-style: none;
    }

    ul li {
        list-style: none;
    }

    a {
        text-decoration: none;
    }

    .clear-fix {
        clear: both;
    }

    .li-progress:hover {
        cursor: pointer;
    }

    #page-wrapper .navbar {
        padding: 7px 1rem 9px .5rem !important;
    }

    .navbar .count-container {
        top: 2px !important;
    }

    .item-btn .btn{
        background-color: <?=$_SESSION["color"]?>;
        border-color: <?=$_SESSION["color"]?>;
    }
    .item-btn .btn:hover{
        background-color: <?=$_SESSION["color"]?>;
        border-color: <?=$_SESSION["color"]?>;
    }
    /*    paging*/
    .pagination {
        margin: 0 auto;
        padding: 1%;
    }

    .pagination li {
        margin: 0% 5% !important;
    }

    .pagination li button {
        background: #FFFFFF 0% 0% no-repeat padding-box;
        border-radius: 4px;
        font-family: Nunito-Bold;
        letter-spacing: 0.45px;
        color: #737373;
    }

    .page-item.active .page-link {
        background: <?=$_SESSION["color"]?> 0% 0% no-repeat padding-box !important;
        border-color: <?=$_SESSION["color"]?> !important;
    }

    .table-select, .tr-title {
        max-width: 200px !important;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .tr-title a {
        color: #3469FF;
    }

    /*    View*/
    .col-6.block-content6 {
        margin-top: 10px;
    }

    .info-user {
        background-color: #FFFFFF;
        width: 25%;
        padding: 3% 1% 1% 6%;
        font-size: 13px;
        float: left;
    }

    .avatar {
        width: 35%;
    }

    .address {
        margin: 25px 0;
    }

    .address p {
        letter-spacing: 0.45px;
        color: #202020;
        text-transform: uppercase;
        margin-bottom: auto;
    }

    .address-detail {
        font-family: Roboto-Italic;
        text-transform: unset !important;
    }

    .info-detail ul {
        padding: 0;
    }

    .info-detail ul li {
        font-family: Roboto-Regular;
        text-transform: capitalize;
    }

    .info-detail ul li p {
        margin: 0;
        display: contents;
    }
    .info-detail ul li span, .info-detail ul li p{
        font-weight: bold;
    }

    .info-learn {
        float: left;
        width: 70%;
    }

    .block {
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 0px 3px 6px #00000029;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .title {
        background: <?=$_SESSION["color"]?> 0% 0% no-repeat padding-box;
        border-radius: 10px 10px 0px 0px;
    }

    .title p {
        text-align: center;
        letter-spacing: 0.8px;
        color: #FFFFFF;
        text-transform: uppercase;
        font-size: 23px;
        margin: 0;
    }

    .table thead th {
        vertical-align: middle;
        border: none;
    }

    .borderless td, .borderless th {
        border: none !important;
        font-size: 13px;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        font-weight: 300;
        padding: .5rem;
    }

    .course-select {
        background: #E4E4E4 0% 0% no-repeat padding-box;
        border-radius: 4px;
        padding: .375em;
        width: 90%;
        border: none;
        font-size: 13px;
    }

    .icon-circle {
        color: #737373;
        font-size: 16px !important;
        text-align: center;
    }

    .icon-circle-green {
        color: #00EB37 !important;
    }

    .width10 {
        width: 10%;
    }

    .numberget {
        font-family: Nunito-Sans-Bold;
        letter-spacing: 1.1px;
        color: <?=$_SESSION["color"]?>;
    }

    .numberhave {
        color: #737373;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 1.1px;
    }

    .nav-tabs .nav-link {
        font-size: 23px;
        letter-spacing: 0.8px;
        color: #737373;
        text-transform: uppercase;
        border: 0;
    }

    .nav-tabs .nav-item {
        margin-bottom: 0 !important;
    }

    .nav-tabs .nav-show:hover + .nav-item {
        margin-bottom: 0 !important;
    }

    .nav-tabs .nav-item.show .nav-show, .nav-tabs .nav-show.active {
        color: #0c0c0c !important;
    }

    .nav-link:hover {
        color: #202020;
    }

    .nav-name {
        font-size: 20px;
        letter-spacing: 0.8px;
        color: #737373;
        text-transform: uppercase;
        padding: .5rem 1rem;
        display: block;
    }

    .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
        border: 0;
    }

    .nav-tabs, .nav-tabs .nav-item.show .nav-show, .nav-tabs .nav-show.active, .nav-tabs .nav-show:focus, .nav-tabs .nav-show:hover {
        border: none;
    }

    .nav-tabs .active a {
        color: #202020;
    }


    .item-image {
        box-shadow: 3px 3px 6px #0000004D;
        border-radius: 10px;
        /*max-height: 300px;*/
        max-width: 70%;
        margin: auto;
    }

    .item-image img {
        /*max-height: 134px;*/
    }

    .item-content {
        text-align: center;
        /*margin-top: 10%;*/
        padding: 0;
    }

    .item-content__name {
        font-family: Roboto-Regular;
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
        margin: 0;
    }

    .item-content__date {
        font-family: Roboto-Regular;
        font-size: 13px;
        letter-spacing: 0.45px;
        color: #737373;
    }


    /*svg{border:1px solid}*/
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

    .percentage_done {
        fill: #9b9b9b;
        font-size: 0.2em;
        font-family: AvenirNext;
        font-weight: 500;
        font-style: normal;
        font-stretch: normal;
        line-height: normal;
        letter-spacing: 0.1px;
    }


    .progress-note ul {
        padding: 0;
    }


    .progress-note ul li {
        list-style: none;
        padding: 5% 0;
        font-size: 14px;
    }

    .block-note {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-flex;
    }

    .progress {
        height: .75rem;
        border-radius: 0 !important;
        padding: 0 !important;
    }

    .progress-bar {
        background-color: <?=$_SESSION["color"]?> !important;
    }

    .progress-number span {
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        line-height: 13px;
    }

    .block-progress {
        margin-bottom: 10px;
    }

    .block-progress p {
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        margin: 0;
        width: 100%;
    }

    .percentage {
        font-family: Roboto-Regular;
        fill: <?=$_SESSION["color"]?>;
    }

    .block-content {
        width: 100%;
    }

    /*custom*/
    #region-main {
        font-size: 14px;
        font-family: Roboto-Bold;
        background-color: #f1f3f9 !important;
        border: none !important;
    }


    /*Ipad dọc(768 x 1024)*/
    @media screen and (max-width: 768px) {
        .info-user {
            padding-left: 25%;
            margin-bottom: 15px;
        }

        .info-user, .info-learn {
            width: 100%;
        }

        .block-content12 {
            display: block;
        }

        .block-content6 {
            max-width: 96%;
        }
    }

    /*Tablet nhỏ(480 x 640)*/
    @media screen and (max-width: 480px) {
        .item-content p {
            font-size: 10px;
        }

        .block-content12 {
            display: block !important;
        }

        .col-6.block-content6 {
            max-width: 100%;
            margin: 0 15px;
            padding-bottom: 15px;
        }
    }

    /*Iphone(480 x 640)*/
    @media screen and (max-width: 320px) {
        .info-user_info {
            margin-top: 3%;
        }
    }
</style>
<body>
<div class="wrapper"><!-- wrapper -->
    <?php echo $OUTPUT->header(); ?>
    <!--    body-->
    <div id="app">
        <section class="section section-content">
            <div class="info-user">
                <div class="avatar"><img :src="user.avatar" alt=""></div>
                <div class="address">
                    <p>{{ user.fullname }}</p>
                    <p class="address-detail">{{ user.address }}</p>
                </div>
                <div class="info-detail">
                    <ul>
                        <li v-if="user.position">Position: <span>{{ user.exactlypostion }}</span></li>
                        <li v-else>Position: <span>Not yet update</span></li>
                        <li v-if="user.departmentname">Department: <span>{{ user.departmentname }}</span></li>
                        <li v-else>Department: <span>Not yet update</span></li>
                        <li v-if="user.yearworking > 1">Experience: <span>{{ user.yearworking }} years</span></li>
                        <li v-else-if="user.yearworking < 0">Experience: <span>Not yet update</span></li>
                        <li v-else>Experience: <span>Under 1 year</span></li>
                        <li v-if="linemanagers && linemanagers.length > 0">Line Manager: <p>{{ linemanagersStr }}</p></li>
                        <li v-else>Line Manager: <span>Not yet update</span></li>
                        <li>Company: <span>{{ user.company }}</span></li>
                    </ul>
                </div>
                <div><a href="lms/user/edit.php"
                        style="font-size: 14px; font-style: italic; color: <?= $_SESSION["color"] ?>">Edit profile</a>
                </div>
            </div>
            <div class="info-learn">
                <div class="container">
                    <div class="block overall">
                        <div class="title"><p>Overall</p></div>
                        <div class="block-content">
                            <div class="row col-12 block-content12">
                                <div class="col-lg-6 col-md-6 row block-content6">
                                    <div class="col-lg-6 col-md-6">
                                        <div>
                                            <svg viewBox="0 0 36 36" width="150" class="circular-chart">
                                                <path class="that-circle" stroke="#C7C7C7" stroke-dasharray="100,100"
                                                      d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                <path class="that-circle" stroke="#FFC400"
                                                      stroke-dasharray="<?php echo($percentCompleted + $percentStudying); ?>,100"
                                                      d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                <path class="that-circle" stroke="<?= $_SESSION["color"] ?>"
                                                      stroke-dasharray="<?php echo $percentCompleted; ?>,100" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"/>


                                                <text x="18" y="20.35"
                                                      class="percentage"><?php echo $percentCompleted; ?> %
                                                </text>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="progress-note">
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
                                </div>

                                <div class="col-6 col-md-6 block-content6">
                                    <div class="row block-progress">
                                        <p>Current Courses</p>
                                        <div class="progress col-10">
                                            <div class="progress-bar progress-current" role="progressbar"
                                                 style="width: 0%" aria-valuenow="30" aria-valuemin="0"
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <div class="col-1 progress-number"><span>{{ progressCurrentCourse }}</span>
                                        </div>
                                    </div>
                                    <div class="row block-progress">
                                        <p>Required Courses</p>
                                        <div class="progress col-10">
                                            <div class="progress-bar progress-required" role="progressbar"
                                                 style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <div class="col-1 progress-number"><span>{{ progressRequiredCourse }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block courses">
                        <div class="title"><p>Your courses</p></div>
                        <div class="block-content table-responsive">
                            <table class="table borderless table-keep">
                                <thead>
                                <tr>
                                    <th scope="col" class="table-select">
                                        <select name="category" id="category" class="course-select"
                                                @change="searchCourse(category, 1)"
                                                v-model="category">
                                            <option value="0">All course</option>
                                            <?php foreach ($categories as $category) { ?>
                                                <option
                                                    value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </th>
                                    <th scope="col" style="text-align: center">Percent</th>
                                    <th scope="col" class="width10" style="text-align: center">Qualified</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-if="courses && courses.length !== 0">
                                    <tr v-for="(course,index) in courses">
                                        <th class="tr-title"><a :href="'lms/course/view.php?id='+course.id" :title="course.fullname">{{ course.fullname }}</a></th>
                                        <td style="text-align: center">
                                            <span class="numberget" v-if="course.numofmodule == 0">0</span>
                                            <span class="numberget" v-else>{{ Math.round(course.numoflearned*100/course.numofmodule) }}</span>
                                        </td>
                                        <td class="icon-circle" style="text-align: center" v-if="course.numofmodule == 0 || course.numoflearned/course.numofmodule == 0 || (course.numoflearned/course.numofmodule > 0 && course.numoflearned/course.numofmodule < 1)">
                                            <i class="fa fa-circle-o" aria-hidden="true"></i>
                                        </td>
                                        <td class="icon-circle" style="text-align: center" v-else><i class="fa fa-check-circle icon-circle-green" aria-hidden="true"></i></td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3">
                                            No course found
                                        </td>
                                    </tr>
                                </template>
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
                    <div class="block courses">
                        <div class="title"><p>Your competency courses</p></div>
                        <div class="block-content table-responsive">
                            <table class="table borderless table-keep">
                                <thead>
                                <tr>
                                    <th scope="col" class="table-select">
                                        <select name="category" id="category" class="course-select"
                                                @change="searchCourseTraining(training, 1)"
                                                v-model="training">
                                            <option value="0">All competency</option>
                                            <template v-for="(training, index) in trainingList">
                                                <option :value="training.id">{{ training.name }}</option>
                                            </template>
                                        </select>
                                        <a class="nav-link d-inline-block position-relative" style="max-width: 50px" :title="trainingDescription">
                                            <i class="fa fa-info-circle"></i>
                                        </a>
                                    </th>
                                    <th scope="col" style="text-align: center">Percent</th>
                                    <th scope="col" class="width10" style="text-align: center">Qualified</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-if="coursesTraining && coursesTraining.length !== 0">
                                    <tr v-for="(course,index) in coursesTraining">
                                        <th class="tr-title"><a :href="'lms/course/view.php?id='+course.id"
                                                                :title="course.fullname">{{ course.fullname }}</a></th>
                                        <td style="text-align: center">
                                            <span class="numberget" v-if="course.numofmodule == 0">0</span>
                                            <span class="numberget" v-else>{{ Math.round(course.numoflearned*100/course.numofmodule) }}</span>
                                        </td>
                                        <td class="icon-circle" style="text-align: center"
                                            v-if="course.numofmodule == 0 || course.numoflearned/course.numofmodule == 0 || (course.numoflearned/course.numofmodule > 0 && course.numoflearned/course.numofmodule < 1)">
                                            <i class="fa fa-circle-o" aria-hidden="true"></i></td>
                                        <td class="icon-circle" style="text-align: center" v-else>
                                            <i class="fa fa-check-circle icon-circle-green" aria-hidden="true"></i>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3">
                                            No course found
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                            <div class="pagination" v-if="totalPageTraining > 1">
                                <v-pagination
                                    v-model="currentTraining"
                                    :page-count="totalPageTraining"
                                    :classes="bootstrapPaginationClasses"
                                    :labels="customLabels"
                                    @input="onPageChange"
                                ></v-pagination>
                            </div>
                        </div>
                    </div>
                    <div class="block certificate-badge">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs justify-content-center">
                            <li class="nav-item">
                                <a class="nav-show nav-name active" data-toggle="tab"
                                   href="#certificate">certificate</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-show nav-name" data-toggle="tab" href="#badge">badge</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <?php if (count($certificates) != 0) { ?>
                                <div id="certificate" class="tab-pane active">
                                    <br/>
                                    <div class="row col-lg-12 pb-3">
                                        <?php foreach ($certificates as $certificate) { ?>
                                            <div class="col-lg-3 mb-3">
                                                <div class="item-image">
                                                    <img src="storage/upload/certificate/<?php echo $certificate->code; ?>_certificate.jpeg"
                                                         alt="">
                                                </div>
                                                <div class="item-content mt-2">
                                                    <p class="item-content__name"><?php echo $certificate->name; ?></p>
                                                    <p class="item-content__date"><?php echo date('m/d/Y', $certificate->timecertificate); ?></p>
                                                </div>
                                                <div class="item-btn" style="text-align: center;color: #fff;">
                                                    <a class="btn btn-primary img-view" data-toggle="modal" imgSrc="storage/upload/certificate/<?php echo $certificate->code; ?>_certificate.jpeg" data-target="#exampleModalCenter" nameImg="<?php echo $certificate->name; ?>">Preview</a>
                                                    <a class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy link" onclick="copyToClipboard('<?php echo $CFG->wwwtmsbase; ?>storage/upload/certificate/<?php echo $certificate->code; ?>_certificate.jpeg')">Copy link</a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div id="certificate" class="container tab-pane active">
                                    <br/>
                                    <div class="row col-lg-12 pb-3">
                                        No certificate found
                                    </div>
                                </div>
                            <?php } ?>
                            <div id="badge" class="container tab-pane fade">
                                <br/>
                                <div class="row col-lg-12 pb-3">
                                    <?php
                                    if (count($badges) != 0) {
                                        foreach ($badges as $badge) { ?>
                                            <div class="col-lg-3 mb-3">
                                                <div class="item-image">
                                                    <img class="img-view" src="storage/upload/certificate/<?php echo $badge->code; ?>_badge.jpeg"
                                                         alt="">
                                                </div>
                                                <div class="item-content mt-2">
                                                    <p class="item-content__name"><?php echo $badge->name; ?></p>
                                                    <p class="item-content__date"><?php echo date('m/d/Y', $badge->timecertificate); ?></p>
                                                </div>
                                                <div class="item-btn" style="text-align: center;color: #fff;">
                                                    <a class="btn btn-primary img-view" data-toggle="modal" imgSrc="storage/upload/certificate/<?php echo $badge->code; ?>_badge.jpeg" data-target="#exampleModalCenter" nameImg="<?php echo $badge->name; ?>">Preview</a>
                                                    <a class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy link" onclick="copyToClipboard('<?php echo $CFG->wwwtmsbase; ?>storage/upload/certificate/<?php echo $badge->code; ?>_badge.jpeg')">Copy link</a>
                                                </div>
                                            </div>
                                        <?php }
                                    } else {
                                        echo "No badge found";
                                    }
                                    ?>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="clear-fix"></div>
        </section>
    </div>

    <?php echo $OUTPUT->footer(); ?>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle" style="font-size: 16px"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="" class="img-preview" alt="">
            </div>
            <div class="modal-footer">
                <div class="btn-list" style="margin: auto">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
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

        $('.img-view').click(function(){
            var src = $(this).attr('imgSrc');
            $('.img-preview').attr('src', src);
            var name = $(this).attr('nameImg');
            $('#exampleModalCenterTitle').text(name);
        });
    });

    function copyToClipboard(url){
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Copied to clipboard");
        return false;
    }

    Vue.component('v-pagination', window['vue-plain-pagination'])
    var app = new Vue({
        el: '#app',
        data: {
            category: 0,
            training: 0,

            txtSearch: '',
            txtSearchTraining: '',

            courses: [],
            coursesTraining: [],

            totalCourse: 0,
            requiredCourse: 0,
            currentCourse: 0,

            linemanagers: [],
            linemanagersStr: '',

            trainingList: [],

            user: {},
            clctgr: true,
            progressRequiredCourse: '0/0',
            progressCurrentCourse: '0/0',
            url: '<?php echo $CFG->wwwroot; ?>',
            user_id: <?php echo $user_id ?>,

            current: 1,
            totalPage: 0,
            recordPerPage: 5,
            currentCoursesTotal: 0,

            currentTraining: 1,
            totalPageTraining: 0,
            recordPerPageTraining: 5,
            currentCoursesTotalTraining: 0,

            trainingDescription: '',

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
            }
        },
        methods: {
            onPageChange: function () {
                this.searchCourse(this.category, this.current);
                this.searchCourseTraining(this.training, this.currentTraining);
            },
            searchCourse: function (category, page) {
                this.category = category || this.category;
                if (page == 1)
                    this.current = 1;
                const params = new URLSearchParams();
                params.append('category', category);
                params.append('txtSearch', this.txtSearch);
                params.append('current', page || this.current);
                // params.append('pageCount', this.total);
                params.append('recordPerPage', this.recordPerPage);
                params.append('pageRequest', 'profile');

                axios({
                    method: 'post',
                    url: this.url + '/pusher/coursesearch.php',
                    data: params,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                    .then(response => {
                        this.courses = response.data.courses;
                        this.currentCoursesTotal = this.courses.length;
                        this.totalPage = response.data.totalPage;
                    })
                    .catch(error => {
                        console.log("Error");
                    });
            },
            getListUserTraining: function () {
                axios({
                    method: 'get',
                    url: this.url + '/pusher/user_training.php',
                })
                    .then(response => {
                        this.trainingList = response.data.list;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            searchCourseTraining: function (traning, page) {
                if (page === 1)
                    this.currentTraining = 1;
                const params = new URLSearchParams();
                params.append('training', this.training);
                params.append('txtSearch', this.txtSearchTraining);
                params.append('current', page || this.currentTraining);
                // params.append('pageCount', this.total);
                params.append('recordPerPage', this.recordPerPageTraining);
                axios({
                    method: 'post',
                    url: this.url + '/pusher/user_course_training.php',
                    data: params,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                    .then(response => {
                        this.coursesTraining = response.data.courses;
                        this.currentCoursesTotalTraining = this.coursesTraining.length;
                        this.totalPageTraining = response.data.totalPage;
                        this.trainingDescription = response.data.trainingDescription;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getProfile: function () {
                const params = new URLSearchParams();
                params.append('user_id', this.user_id);
                axios({
                    method: 'post',
                    url: this.url + '/pusher/profile.php',
                    data: params,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                    .then(response => {
                        this.user = response.data.profile;

                        this.linemanagers = response.data.linemanagers;
                        // if(response.data.linemanagers.length > 0){
                        //     var lineStr = '';
                        //     $.each(response.data.linemanagers, function(key, value) {
                        //         lineStr += value.fullname+', ';
                        //     });
                        //     lineStr = lineStr.substring(0, lineStr.length - 2);
                        //     this.linemanagersStr = lineStr;
                        // }
                        this.linemanagersStr = this.linemanagers;

                        //set progress
                        var numCurrentCourses = Object.keys(response.data.currentcourses).length;
                        var numRequiredCourses = Object.keys(response.data.requiredcourses).length;
                        var totalCourse = response.data.totalCourse;

                        if (totalCourse == 0) {
                            $('.progress-current').css('width', '0%');
                            $('.progress-required').css('width', '0%');
                        } else {
                            this.progressCurrentCourse = numCurrentCourses + "/" + totalCourse;
                            this.progressRequiredCourse = numRequiredCourses + "/" + totalCourse;

                            //
                            $('.progress-current').css('width', numCurrentCourses * 100 / totalCourse + '%');
                            $('.progress-required').css('width', numRequiredCourses * 100 / totalCourse + '%');
                        }
                    })
                    .catch(error => {
                        console.log("Error ", error);
                    });
            },
        },
        mounted() {
            this.searchCourse();
            this.searchCourseTraining();
            this.getListUserTraining();
            this.getProfile();
        }
    })

</script>
</body>
</html>


<?php
die;
?>
