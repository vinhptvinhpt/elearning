<html>
<title>Thông tin khóa học <?php echo $course->fullname; ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

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
        font-size: 14px !important;
        font-family: Roboto-Bold;
    }

    ul {
        list-style: none;
    }

    a {
        text-decoration: none;
    }

    /*    view*/
    .alert-block {
        opacity: 1 !important;
    }

    #page {
        margin-right: 4%;
        /*margin-right: */
    <?//=$marginPage?> /*;*/
    }

    .alert {
        padding: 20px;
        background-color: #f44336;
        color: white;
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

    .prev-btn:hover {
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
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #FFFFFF !important;
        text-transform: uppercase;
    }

    .nav-course .nav {
        width: 100%;
        margin: auto;
    }

    .nav-introduction {
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

    .nav-unit {
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
        padding: 5%;
    }

    .course-block {
        margin-bottom: 2em;
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
        font-family: Roboto-Regular;
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

    #courseunit {
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
        background-image: url('lms/theme/image.php/bgtlms/page/1588135480/icon');
        background-repeat: no-repeat;
        background-position: left;
        padding-left: 4%;
        font-family: Roboto-Regular;
        font-size: 14px;
        letter-spacing: 0.99px;
        color: #333;
        background-size: 20px 16px;
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
        border: 2px solid<?=$_SESSION["color"]?>;
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
        width: 50px;
        padding: 1px 0px;
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
        .drawer-open-left, .over-wrap {
            opacity: 0 !important;
            display: none;
        }

        .block {
            display: contents;
        }

        .info-course-detail ul li {
            font-size: 13px !important;
        }
    }

    @media only screen and (max-width: 1024px) {
        .drawer-open-left, .over-wrap {
            opacity: 0 !important;
            display: none;
        }

        .block {
            display: contents;
        }

        .info-course-detail ul li {
            font-size: 12px !important;
        }
    }

    @media only screen and (max-width: 991px) {
        .drawer-open-left, .over-wrap {
            opacity: 0 !important;
            display: none;
        }

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
        .drawer-open-left, .over-wrap {
            opacity: 0 !important;
            display: none;
        }

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

$edit        = optional_param('edit', -1, PARAM_BOOL);
$notifyeditingon        = optional_param('notifyeditingon', -1, PARAM_BOOL);
$id = optional_param('id', 0, PARAM_INT);

if ($notifyeditingon == 1) {
} else if ($edit == 1) {
    //do nothing
    $USER->editing = 1;
    $url = new moodle_url("/course/viewedit.php?id=".$id, array('notifyeditingon' => 1));
    redirect($url);
}

function get_client_ip_server()
{
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

// [VinhPT][EAsia] Course IP address restrict

$result_ip = array_values($DB->get_records_sql("Select access_ip from mdl_course where id = ".$id))[0]->access_ip;
$root_url = $CFG->wwwroot;

if($result_ip){
    $list_access_ip = json_decode($result_ip)->list_access_ip;
    if ($list_access_ip){
        if(!in_array(getremoteaddr(), $list_access_ip)){
//        if(!in_array(get_client_ip_server(), $list_access_ip)){
            $url_to_page = new moodle_url($root_url);
            $message_ip_access = "You do not have permission to access this course";
            redirect($url_to_page, $message_ip_access, 10, \core\output\notification::NOTIFY_ERROR);
        }
    }
}
$sql = 'SELECT mc.id, mc.fullname, mc.category, mc.course_avatar, mc.estimate_duration, mc.summary, ( SELECT COUNT(mcs.id) FROM mdl_course_sections mcs WHERE mcs.course = mc.id AND mcs.section <> 0) AS numofsections, ( SELECT COUNT(cm.id) AS num FROM mdl_course_modules cm INNER JOIN mdl_course_sections cs ON cm.course = cs.course AND cm.section = cs.id WHERE cs.section <> 0 AND cm.course = mc.id) AS numofmodule, ( SELECT COUNT(cmc.coursemoduleid) AS num FROM mdl_course_modules cm INNER JOIN mdl_course_modules_completion cmc ON cm.id = cmc.coursemoduleid INNER JOIN mdl_course_sections cs ON cm.course = cs.course AND cm.section = cs.id INNER JOIN mdl_course c ON cm.course = c.id WHERE cs.section <> 0 AND cmc.completionstate <> 0 AND cm.course = mc.id AND cmc.userid = '.$USER->id.') AS numoflearned FROM mdl_course mc WHERE mc.id = '.$id;
$course = array_values($DB->get_records_sql($sql))[0];

$units = get_course_contents($id);

$start_course_link = '';
if (!empty($units)) {
    $first_unit_modules = $units[0]['modules'];
    if ($units[0]['modules'] && !empty($units[0]['modules'])) {
        $first_module = $units[0]['modules'][0];
        if ($first_module) {
            $start_course_link = $first_module['url'];
        }
    }
}

$bodyattributes = 'id="page-course-view-topics" class="pagelayout-course course-' . $id .'"';

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
?>
<body <?php echo $bodyattributes ?>>

<div class="wrapper"><!-- wrapper -->
    <?php echo $OUTPUT->header(); ?>
    <section class="section section--header"><!-- section -->
        <div class="container">
<!--                progress info-->
           <div class="progress-info">
               <div class="progress-info__title"><span title="<?php echo $course->fullname; ?>"><a class="prev-btn"><i class="fa fa-angle-left" aria-hidden="true"></i></a>  <?php echo $course->fullname; ?></span></div>
               <div class="progress-info__content">
                   <div class="row">
                       <div class="col-4 info-course-detail">
                           <ul>
                               <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                               <li class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</li>
                               <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</li>
                           </ul>
                       </div>
                       <div class="col-6 row info-course-progress">
                           <span class="col-3">PROGRESS</span>

                           <div class="col-9">
                               <?php if($course->id != 506){ ?>
                                   <hgroup class="speech-bubble">
                                       <h7><?php echo $course->numoflearned; ?>/<?php echo $course->numofmodule; ?></h7>
                                   </hgroup>
                                   <div class="progress">
                                       <div class="progress-bar" role="progressbar" style="width: <?php echo (int)($course->numoflearned*100/$course->numofmodule); ?>%;" aria-valuenow="<?php echo (int)($course->numoflearned*100/$course->numofmodule); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                   </div>
                               <?php }else{?>
                                   <hgroup class="speech-bubble">
                                       <h7><?php echo $course->numofmodule; ?>/<?php echo $course->numofmodule; ?></h7>
                                   </hgroup>
                                   <div class="progress">
                                       <div class="progress-bar" role="progressbar" style="width: <?php echo (int)($course->numofmodule*100/$course->numofmodule); ?>%;" aria-valuenow="<?php echo (int)($course->numofmodule*100/$course->numofmodule); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                   </div>

                               <?php } ?>
                           </div>
                       </div>
                       <div class="col-2 info-course-btn">
                           <a href="<?php echo $start_course_link ?>" <?php if(strlen($start_course_link) == 0) { ?>onclick="notifyNoContent()" <?php } ?> class="btn btn-start-course btn-click">start course</a>
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
                    <li class="nav-item nav-click nav-introduction">
                        <a class="nav-link" data-toggle="tab" href="#courseintroduction" role="tab">Course introduction</a>
                    </li>
                    <li class="nav-item nav-click nav-unit">
                        <a id="unit-link" class="nav-link" data-toggle="tab" href="#courseunit" role="tab">Unit List</a>
                    </li>
                    <?php if ($permission_edit) { ?>
                        <li class="nav-item nav-setting">
                            <a class="dropdown-toggle setting-link" id="menu-edit" data-toggle="dropdown">
                                <i class="fa fa-cog" aria-hidden="true"></i>
                                Edit course
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu-edit">
                                <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1" href="<?php echo $root_url . "/course/view.php?id=" . $id ?>&edit=on"><i class="icon fa fa-pencil fa-fw " aria-hidden="true"></i>Edit</a></li>
                                <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1" href="<?php echo $root_url . "/course/completion.php?id=" . $id ?>"><i class="icon fa fa-cog fa-fw" aria-hidden="true"></i>Course completion</a></li>
                                <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1" href="<?php echo $root_url . "/backup/import.php?id=" . $id ?>"><i class="icon fa fa-level-up fa-fw" aria-hidden="true"></i>Import</a></li>
                                <li role="presentation"><a class="setting-option" role="menuitem" tabindex="-1" href="<?php echo $root_url . "/course/admin.php?courseid=" . $id ?>"><i class="icon fa fa-cog fa-fw" aria-hidden="true"></i>More</a></li>
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
                        <?php foreach ($units as $no => $unit) {  ?>
                            <div class="unit" id="unit_<?php echo $unit['id']; ?>" section-no="<?php echo $no ?>">
                                <div class="unit__title"><p><?php echo $unit['name']; ?></p></div>
                                <div class="unit__progress">
                                    <div class="unit__icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></div>
                                    <div class="unit__progress-number">
                                        <p><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span class="percent-get">__</span>/<span class="percent-total">100</span></p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                    <div class="col-7 unit-info unit-detail">
                        <?php foreach ($units as $unit) {  ?>
                            <div class="main-detail" id="detail-<?php echo $unit['id']; ?>">
                                <div class="detail-title">
                                    <p><?php echo $unit['name']; ?></p>
                                </div>
                                <div class="detail-content">
                                    <?php if ($unit['modules'] && !empty($unit['modules'])) {
                                        foreach ($unit['modules'] as $module) {  ?>
                                        <ul class="detail-list">
                                            <li><a href="<?php echo $module['url'] ?>"><?php echo $module['name']; ?></a> </li>
                                        </ul>
                                        <?php }
                                    } else { ?>
                                        Unit has no content.
                                    <?php } ?>
                                </div>
                                <?php if($unit['modules'][0] && $unit['modules'][0]['url'] && strlen($unit['modules'][0]['url']) != 0) { ?>
                                <div class="detail-btn">
                                    <a href="<?php echo $unit['modules'][0]['url']; ?>" class="btn btn-click btn-start-unit">Start unit</a>
                                </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
            </div>
        </div>
    </section>
<!--    --><?php //echo $OUTPUT->footer(); ?>
</div>



<script>
    $(document).ready(function(){
        //get active li to show content
        $(".nav-click").each(function() {
            var getClasses = $(this).attr('class');
            if(getClasses.indexOf('active')>-1){
                var getId =  $(this).find("a").attr('href');
                $(getId).css('display', 'flex');
            }
            $('.nav-click').not($(this)).each(function () {
                $(this).removeClass('active');
            });
            $('.nav-tabs-courses .nav-introduction a').addClass('active');
        });

        $(".nav-click a").click(function() {
            //set active for first block
            var getHref = $(this).attr('href');
            if(getHref.indexOf('unit')>-1){
                var getID = $(".unit").first().attr('id');
                var ID = getID.substring(5, getID.length);
                ClickNav(getID, ID);
            }
            $('.nav-click a').not($(this)).each(function () {
                $(this).removeClass('active');
            });

            $('.course-content').not($(getHref)).each(function(){
                $(this).css('display', 'none');
            });
            $(getHref).css('display', 'flex');
        });

        // $(".nav-click a").click(function(){
        //     console.log(3);
        //     var getId =  $(this).attr('href');
        //     $('.course-content').not($(getId)).each(function(){
        //         $(this).css('display', 'none');
        //     });
        //     $(getId).css('display', 'flex');
        // });

        var getPercent = $('.progress-bar').attr('aria-valuenow');
        var marginLeft = getPercent - 6;
        var getScreenWidth = screen.width;
        if(getScreenWidth>=1600){
            marginLeft = getPercent - 3;
        }else if(getScreenWidth <420){
            marginLeft = getPercent - 14;
        }
        else if(getScreenWidth <768){
            marginLeft = getPercent - 10;
        }
        $('.speech-bubble').css('left', marginLeft+'%');

        //set height and line height
        var getHeight = $('.info-course-btn').innerHeight();
        $('.info-course-progress .col-3').css('height', getHeight+'px');
        $('.info-course-progress .col-3').css('line-height', getHeight+'px');
        $('.info-course-detail').css('height', getHeight+'px');
        $('.info-course-detail').css('line-height', getHeight+'px');


        //event click unit
        $('.unit').click(function(){
            var getID = $(this).attr('id');
            var ID = getID.substring(5, getID.length);
            ClickNav(getID, ID);
        });

        //return back url
        $('.prev-btn').click(function () {
            history.back();
        });

        //function click
        function ClickNav(getID, ID){
            $('.unit').not($('#'+getID)).each(function () {
                $(this).removeClass('unit-click');
            });
            $('#'+getID).addClass('unit-click');
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
    }
</script>

<script>
    //Notify tiếp tục module đang học dở
    $(document).ready(function() {
        <?php if ($id != $source) { ?> //Vào từ màn khóa học khác
            $('#page').css('margin-right', '0');
            var x = document.getElementsByTagName("BODY")[0];
            var classes = x.className.toString().split(/\s+/);
            let course_id = '0';

            //screen course detail
            if (classes.includes("pagelayout-course")) {
                classes.forEach(function(classItem) {
                    if (classItem.startsWith('course-')) {
                        course_id = classItem.substring(7, classItem.length);
                    }
                });
                $.ajax({
                    url:'<?php echo $root_url ?>/pusher/resume.php',
                    data: {
                        'course_id': course_id
                    },
                    type: 'POST',
                    success: function(data) {
                        if (data.length !== 0) {
                            r = confirm("Do you want to continue last activity in course?");
                            if (r == true) {
                                window.location.href = data;
                            } else {
                                return;
                            }
                        }
                    },
                    error: function(e){
                        console.log(e);
                    }
                });
            }
        <?php } ?>
    });
</script>

</body>
</html>


<?php
    die;
?>
