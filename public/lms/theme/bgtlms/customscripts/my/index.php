<?php
require_once(__DIR__ . '/../../../../config.php');
// Start the session
session_start();
$sql = 'select mc.id, mc.fullname, mc.category, mc.course_avatar, mc.estimate_duration, ( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections, ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule, ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate <> 0 and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned from mdl_course mc inner join mdl_enrol me on mc.id = me.courseid inner join mdl_user_enrolments mue on me.id = mue.enrolid where me.enrol = \'manual\' and mc.deleted = 0 and mc.visible = 1 and mc.category <> 2 and mue.userid = '.$USER->id;
$courses = array_values($DB->get_records_sql($sql));

$sqlGetInfoUser = 'select tud.fullname as fullname, SUBSTR(tud.avatar, 2) as avatar, toe.position, toe.description as exactlypostion from tms_user_detail tud left join tms_organization_employee toe on tud.user_id = toe.user_id where tud.user_id = '.$USER->id;
$profile = array_values($DB->get_records_sql($sqlGetInfoUser))[0];

$courses_current = array();
$courses_all_required = array();
$courses_optional = array();
$courses_completed = array();
foreach ($courses as $course){
    if($course->id == 506){
        array_push($courses_completed, $course);
    }
    else if($course->numofmodule == 0 || $course->numoflearned/$course->numofmodule == 0){
        array_push($courses_all_required, $course);
    }
    else if($course->numoflearned/$course->numofmodule == 1){
        array_push($courses_completed, $course);
    }
    else if($course->numoflearned/$course->numofmodule > 0 && $course->numoflearned/$course->numofmodule < 1){
        array_push($courses_current, $course);
    }
}
// Set session variables
$_SESSION["courses_current"] = $courses_current;
$_SESSION["courses_all_required"] = $courses_all_required;
$_SESSION["courses_completed"] = $courses_completed;
$_SESSION["totalCourse"] = count($courses);

//set for full page
$organization_id = 2;
$organizationCode = strtolower($_SESSION["organizationCode"]);
switch ($organizationCode) {
    case "easia":
        {
            $_SESSION["organizationName"] = 'Easia';
            $_SESSION["color"] = '#862055';
            $_SESSION["pathLogo"] = 'images/logo-black.png';
            $_SESSION["pathLogoWhite"] = 'images/logo-white.png';
            $_SESSION["component"] = 'images/cpn-easia.png';
            $_SESSION["pathBackground"] = 'images/bg-easia.png';
        }
        break;
    case "exoticvoyages":
        {
            $_SESSION["organizationName"] = 'Exotic voyages';
            $_SESSION["color"] = '#CAB143';
            $_SESSION["pathLogo"] = 'images/exoticvoyages.png';
            $_SESSION["pathLogoWhite"] = 'images/exoticvoyages-white.png';
            $_SESSION["component"] = 'images/cpn-exotic.png';
            $_SESSION["pathBackground"] = 'images/bg-exotic.png';
        }
        break;
    case "begodi":
        {
            $_SESSION["organizationName"] = 'Begodi';
            $_SESSION["color"] = '#FFFFFF';
            $_SESSION["pathLogo"] = 'images/begodi.png';
            $_SESSION["pathLogoWhite"] = 'images/begodi-white.png';
            $_SESSION["component"] = 'images/cpn-begodi.png';
            $_SESSION["pathBackground"] = 'images/bg-begodi.png';
        }
        break;
    case "avana":
        {
            $_SESSION["organizationName"] = 'Avana';
            $_SESSION["color"] = '#202020';
            $_SESSION["pathLogo"] = 'images/avana.png';
            $_SESSION["pathLogoWhite"] = 'images/avana-white.png';
            $_SESSION["component"] = 'images/cpn-avana.png';
            $_SESSION["pathBackground"] = 'images/bg-avana.png';
        }
        break;
    default:
        {
            $_SESSION["organizationName"] = 'Phh';
            $_SESSION["color"] = '#0080EF';
            $_SESSION["pathLogo"] = 'images/phh.png';
            $_SESSION["pathLogoWhite"] = 'images/phh-white.png';
            $_SESSION["component"] = 'images/cpn-phh.png';
            $_SESSION["pathBackground"] = 'images/bg-a-02.png';
        }
        break;
}
//#0e311a
//if($organization_id == 1){
//    $_SESSION["organizationName"] = 'Easia';
//    $_SESSION["color"] = '#862055';
//    $_SESSION["pathLogo"] = 'images/logo-black-1.png';
//}
//else if($organization_id == 2){
//    $_SESSION["organizationName"] = 'Begodi';
//    $_SESSION["color"] = '#CAB143';
//    $_SESSION["pathLogo"] = 'images/Begodi.jpg';
//}

$countBlock = 1;
$percentCompleted = intval(count($courses_completed) * 100 / count($courses));
$percentStudying = intval(count($courses_current) * 100 / count($courses));
//echo shell_exec('select * from mdl_user limit 1');

?>

<html>
<title>Homepage</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
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
        src: url('fonts/Helvetica Neue LT Std 75 Bold.otf');
    }

    @font-face {
        font-family: HelveticaLTStd-Light.otf;
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

    .title {
        text-align: left;
        font-family: Nunito-Sans;
        font-size: 20px;
        letter-spacing: 0px;
        color: #202020;
        /*text-transform: uppercase;*/
        opacity: 1;
    }

    .title h2{
        font-family: HelveticaLTStd-Bold;
        font-size: 1.5em;
    }
    .title span{
        font-family: HelveticaLTStd-Light;
    }

    .btn-click {
        background: <?=$_SESSION["color"]?> 0% 0% no-repeat padding-box !important;
        border-radius: 4px;
        opacity: 1;
        padding: 9px 14px !important;
    }

    .btn-click a:hover, .btn-click:hover{
     opacity: 0.9;
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

    .courses {
        margin-bottom: 70px;
    }

    .info-course p {
        margin: 0 0 5px 0;
        font-family: Roboto-Regular;
        font-size: 14px !important;
    }

    img {
        width: 100%;
    }

    .carousel-control-prev, .carousel-control-next,
    .carousel-indicators {
        display:none !important;
    }

    .btn-show-all {
        text-align: right;
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
        margin-bottom: 2%;
    }

    .section-footer {
        position: relative;
    }

    .path-calendar .maincalendar .heightcontainer {
        height: auto !important;
    }

    .block-item__content_btn {
        width: 100%;
        text-align: right;
    }

    .title-course {
        text-align: left;
        letter-spacing: 0.6px;
        font-size: 17px;
        font-family: Roboto-Bold;
        color: #202020;
        opacity: 1;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 3.25rem;
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
        width: 32%;
        height: 26%;
        position: absolute;
        top: 3%;
        right: 3%;
    }

    .block-item__image span {
        font-size: 14px;
        font-family: Nunito-Sans-Bold;
        color: #FFFFFF;
        position: absolute;
        top: 10%;
        right: 10%;
        letter-spacing: 1px;
    }

    .info-user {
        background-color: <?=$_SESSION["color"]?>;
        display: flex;
        width: 100%;
    }

    .avatar {
        width: 100%;
        border-radius: 50%;
        margin: 0 !important;
        padding-right: 0 !important;
        height: fit-content;
    }

    .avatar img {
        margin-top: 15%;
        margin-bottom: 15%;
        border-radius: 50%;
    }

    .info {
        margin: 40px 0;
        box-shadow: 3px 3px 6px #00000029;
    }

    .info-user_info {
        padding: 10% 1% 1% 10% !important;
        width: 100% !important;
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
    }

    .info-statistic {
        width: 100%;
    }

    .no-padding-col {
        padding: 0 !important;
    }

    .progress-note ul li {
        list-style: none;
        padding: 5% 0;
        font-size: 14px;
    }

    .info-text {
        color: #202020;
        padding: 10px;
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

    .carousel-caption h1{
        font-family: HelveticaLTStd-Bold;
        color: #ffffff;
        position: absolute;
        font-size: 66px;
        position: absolute;
        bottom: 25%;
        letter-spacing: 3px;
    }

    .carousel-caption span{
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

    .section-footer {
        background: #202020 0% 0% no-repeat padding-box;
        border: 1px solid #707070;
        opacity: 1;
    }

    .footer-ul {
        padding: 0;
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
                    <div id="demo" class="carousel slide carousel-fade" data-ride="carousel">
                        <ul class="carousel-indicators">
                            <li data-target="#demo" data-slide-to="0" class="active"></li>
                            <li data-target="#demo" data-slide-to="1"></li>
                            <li data-target="#demo" data-slide-to="2"></li>
                        </ul>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?php echo $_SESSION["pathBackground"];  ?>" alt="Los Angeles" width="1100" class="bg-img">
<!--                                <div class="slide-logo">-->
<!--                                    <img src="--><?php //echo $_SESSION["pathLogo"];  ?><!--" alt="">-->
<!--                                </div>-->
                                <div class="carousel-caption">
                                    <h1><?php echo $_SESSION["organizationName"]; ?> <span>Academy</span></h1>
                                </div>
<!--                                <div class="slide-image">-->
<!--                                    <img src="images/1a-01.png" alt="">-->
<!--                                </div>-->
                            </div>
<!--                            <div class="carousel-item">-->
<!--                                <img src="--><?php //echo $_SESSION["pathBackground"];  ?><!--" alt="Los Angeles" width="1100" height="500" class="bg-img">-->
<!--                                <div class="slide-logo">-->
<!--                                    <img src="--><?php //echo $_SESSION["pathLogo"];  ?><!--" alt="">-->
<!--                                </div>-->
<!--                                <div class="carousel-caption">-->
<!--                                    <h3>--><?php //echo $_SESSION["organizationName"]; ?><!--</h3>-->
<!--                                    <p>ACADEMY</p>-->
<!--                                </div>-->
<!--                                <div class="slide-image">-->
<!--                                    <img src="images/1a-01.jpg" alt="">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="carousel-item">-->
<!--                                <img src="--><?php //echo $_SESSION["pathBackground"];  ?><!--" alt="Los Angeles" width="1100" height="500" class="bg-img">-->
<!--                                <div class="slide-logo">-->
<!--                                    <img src="--><?php //echo $_SESSION["pathLogo"];  ?><!--" alt="">-->
<!--                                </div>-->
<!--                                <div class="carousel-caption">-->
<!--                                    <h3>--><?php //echo $_SESSION["organizationName"]; ?><!--</h3>-->
<!--                                    <p>ACADEMY</p>-->
<!--                                </div>-->
<!--                                <div class="slide-image">-->
<!--                                    <img src="images/1a-01.jpg" alt="">-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                        <a class="carousel-control-prev" href="#demo" data-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </a>
                        <a class="carousel-control-next" href="#demo" data-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </a>
                    </div>
                </div>
        </header>
    </section>
    <!--    body-->
    <section class="section section-content">
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <div class="info row">
                            <div class="info-user">
                                <div class="avatar col-4">
                                    <img src="<?php if(is_null($profile->avatar)) echo 'images/avatar.png';  else echo $profile->avatar; ?>" alt="">
                                </div>
                                <div class="info-user_info col-8">
                                    <p class="username"><?php echo $profile->fullname; ?></p>
                                    <p class="userposition"><?php if(is_null($profile->exactlypostion)) echo $profile->position; else echo $profile->exactlypostion; ?></p>
                                </div>
                            </div>
                            <div class="info-progress mt-3">
                                <div class="circle-progress no-padding-col col-5">
                                    <svg viewBox="0 0 36 36" width="150" class="circular-chart">
                                        <path class="that-circle" stroke="#C7C7C7" stroke-dasharray="100,100" d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="that-circle" stroke="#FFC400" stroke-dasharray="<?php echo $percentCompleted+$percentStudying; ?>,100"  d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="that-circle" stroke="<?=$_SESSION["color"]?>" stroke-dasharray="<?php echo $percentCompleted; ?>,100" d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <text x="18" y="20.35" class="percentage"><?php echo $percentCompleted; ?> %</text>
                                    </svg>
                                </div>
                                <div class="progress-note no-padding-col col-7">
                                    <ul>
                                        <li><div class="block-note" style="background-color: <?=$_SESSION["color"]?>"></div> Completed</li>
                                        <li><div class="block-note" style="background-color: #FFC400"></div> Studying</li>
                                        <li><div class="block-note" style="background-color: #C7C7C7"></div> Not yet learned</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="info-statistic mb-3">
                                <div class="info-statistic__current-course">
                                    <a class="info-text">
                                        <div class="text-course">Current courses</div>
                                        <div class="text-number"><?php echo count($courses_current); ?></div>
                                    </a>
                                </div>
                                <div class="info-statistic__all-required">
                                    <a class="info-text">
                                        <div class="text-course">All required courses</div>
                                        <div class="text-number"><?php echo count($courses_all_required); ?></div>
                                    </a>
                                </div>
                                <div class="info-statistic__completed-courses">
                                    <a class="info-text">
                                        <div class="text-course">Completed courses</div>
                                        <div class="text-number"><?php echo count($courses_completed); ?></div>
                                    </a>
                                </div>
                                <div class="info-statistic__profile">
                                    <a class="info-text text-course" href="lms/user/profile.php?id=<?php echo $USER->id; ?>">
                                        Your Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 colsm-1"></div>
                    <div class="col-md-8 col-sm-7 col-xs-12">
                        <div class="courses">
                            <!--current course-->
                            <div class="courses-block">
                                <!--top-->
                                <div class="course-block__top">
                                    <div class="course-block__top-show row">
                                        <div class="col-6 title"><h2>Current <span>Courses</span></h2></div>
                                        <div class="col-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a href="lms/course/index.php?type=1">Show All</a></button>
                                        </div>
                                    </div>
                                </div>
                                <!--content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row course-row-mx-5">
                                        <?php if(count($courses_current) > 0) {  ?>
                                            <?php $countBlock = 1; foreach ($courses_current as $course) {  ?>
                                            <div class="col-xxl-4 col-md-6 col-sm-6 col-xs-12 mb-3 course-mx-5">
                                                <div class="block-items__item">
                                                    <div class="block-item__image col-5" style="background-image: url('<?php echo $CFG->wwwtmsbase . $course->course_avatar; ?>')">
                                                        <img src="<?php echo $_SESSION['component'] ?>" alt=""><span><?php echo intval($course->numoflearned*100/$course->numofmodule); ?>%</span>
                                                    </div>
                                                    <div class="block-item__content col-7">
                                                        <div class="block-item__content_text">
                                                            <a href="lms/course/view.php?id=<?php echo $course->id; ?>" title="<?php echo $course->fullname; ?>"><p class="title-course"><i></i><?php echo $course->fullname; ?></p></a>
                                                            <div class="info-course">
                                                                <p class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</p>
                                                                <p class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</p>
                                                                <p class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</p>
                                                            </div>
                                                        </div>
                                                        <!--                                                            <div class="block-item__content_btn">-->
                                                        <!--                                                                <button class="btn btn-click"><a href="lms/course/view.php?id=--><?php //echo $course->id; ?><!--">Learn More</a></button>-->
                                                        <!--                                                            </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <?php  $countBlock++; if($countBlock == 5) break; } ?>
                                        <?php } else { ?>
                                            <div class="col-12">
                                                <h3>No course to display</h3>
                                            </div>
                                        <?php }  ?>
                                    </div>
                                </div>
                            </div>

                            <!--all required courses-->
                            <div class="courses-block">
                                <!--top-->
                                <div class="course-block__top">
                                    <div class="course-block__top-show row">
                                        <div class="col-6 title"><h2>All required <span>Courses</span></h2></div>
                                        <div class="col-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a href="lms/course/index.php?type=2">Show All</a></button>
                                        </div>
                                    </div>
                                </div>
                                <!--content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row">
                                        <?php if(count($courses_all_required) > 0) {  ?>
                                            <?php $countBlock = 1; foreach ($courses_all_required as $course) {  ?>
                                                <div class="col-md-6 col-sm-6 col-xs-12 mb-3 course-mx-5">
                                                    <div class="block-items__item">
                                                        <div class="block-item__image col-5" style="background-image: url('<?php echo $CFG->wwwtmsbase . $course->course_avatar; ?>')">
                                                            <img src="<?php echo $_SESSION['component'] ?>" alt=""><span><?php echo intval($course->numoflearned*100/$course->numofmodule); ?>%</span>
                                                        </div>
                                                        <div class="block-item__content col-7">
                                                            <div class="block-item__content_text">
                                                                <a href="lms/course/view.php?id=<?php echo $course->id; ?>" title="<?php echo $course->fullname; ?>"><p class="title-course"><i></i><?php echo $course->fullname; ?></p></a>
                                                                <div class="info-course">
                                                                    <p class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</p>
                                                                    <p class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</p>
                                                                    <p class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</p>
                                                                </div>
                                                            </div>
                                                            <!--                                                            <div class="block-item__content_btn">-->
                                                            <!--                                                                <button class="btn btn-click"><a href="lms/course/view.php?id=--><?php //echo $course->id; ?><!--">Learn More</a></button>-->
                                                            <!--                                                            </div>-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php  $countBlock++; if($countBlock == 5) break; } ?>
                                        <?php } else { ?>
                                            <div class="col-12">
                                                <h3>No course to display</h3>
                                            </div>
                                        <?php }  ?>
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
                                            <button class="btn btn-click"><a href="lms/course/index.php?type=3">Show All</a></button>
                                        </div>
                                    </div>
                                </div>
                                <!--content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row">
                                        <?php if(count($courses_completed) > 0) {  ?>
                                            <?php $countBlock = 1; foreach ($courses_completed as $course) {  ?>
                                                <div class="col-md-6 col-sm-6 col-xs-12 mb-3">
                                                    <div class="block-items__item">
                                                        <div class="block-item__image col-5" style="background-image: url('<?php echo $CFG->wwwtmsbase.$course->course_avatar; ?>')">
                                                            <img src="images/Badge-examples 2.png" alt="">
                                                        </div>
                                                        <div class="block-item__content col-7">
                                                            <div class="block-item__content_text">
                                                                <a href="lms/course/view.php?id=<?php echo $course->id; ?>" title="<?php echo $course->fullname; ?>"><p class="title-course"><i></i><?php echo $course->fullname; ?></p></a>
                                                                <div class="info-course">
                                                                    <p class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</p>
                                                                    <p class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</p>
                                                                    <p class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</p>
                                                                </div>
                                                            </div>
                                                            <!--                                                            <div class="block-item__content_btn">-->
                                                            <!--                                                                <button class="btn btn-click"><a href="lms/course/view.php?id=--><?php //echo $course->id; ?><!--">Learn More</a></button>-->
                                                            <!--                                                            </div>-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php  $countBlock++; if($countBlock == 5) break; } ?>
                                        <?php } else { ?>
                                            <div class="col-12">
                                                <h3>No course to display</h3>
                                            </div>
                                        <?php }  ?>
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
                echo html_writer::start_tag('div', array('class'=>'heightcontainer'));
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
                    <!--Helps-->
                    <div class="footer-block col-sm-3 col-xs-6">
                        <div class="footer-block__title"><p class="footer-title">Helps & Support</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Courses</a></li>
                                <li><a href="/">Profile</a></li>
                                <li><a href="/">Profile</a></li>
                            </ul>
                        </div>
                    </div>
                    <!--FAQs-->
                    <div class="footer-block col-sm-3 col-xs-6">
                        <div class="footer-block__title"><p class="footer-title">FAQs</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Courses</a></li>
                                <li><a href="/">Profile</a></li>
                                <li><a href="/">Profile</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer-block col-sm-3 col-xs-6">
                        <div class="footer-block__title"><p class="footer-title">Contact</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Courses</a></li>
                                <li><a href="/">Profile</a></li>
                                <li><a href="/">Profile</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer-block col-sm-3 col-xs-6">
                        <div class="footer-block__title"><p class="footer-title">Sitemap</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Courses</a></li>
                                <li><a href="/">Profile</a></li>
                                <li><a href="/">Profile</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </section>
<!--    --><?php //echo $OUTPUT->footer(); ?>
</div>
<script>
    $(function() {
        localStorage.setItem('courses', '<?php echo json_encode($course); ?>');

        var heightTopBar = $('.navbar-expand').outerHeight();
        $('.content-slider').css('margin-top', heightTopBar+'px');

        $('.slider').slick({
            dots: false,
            prevArrow: false,
            nextArrow: false
        });
    });
</script>
</body>
</html>

<?php
die;
?>
