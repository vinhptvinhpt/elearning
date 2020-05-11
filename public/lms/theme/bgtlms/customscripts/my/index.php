<?php
require_once(__DIR__ . '/../../../../config.php');

$sql = 'select mc.id, mc.fullname, mc.category, mc.course_avatar, mc.estimate_duration, ( select count(mcs.id) from mdl_course_sections mcs where mcs.course = mc.id and mcs.section <> 0) as numofsections, ( select count(cm.id) as num from mdl_course_modules cm inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id where cs.section <> 0 and cm.course = mc.id) as numofmodule, ( select count(cmc.coursemoduleid) as num from mdl_course_modules cm inner join mdl_course_modules_completion cmc on cm.id = cmc.coursemoduleid inner join mdl_course_sections cs on cm.course = cs.course and cm.section = cs.id inner join mdl_course c on cm.course = c.id where cs.section <> 0 and cmc.completionstate <> 0 and cm.course = mc.id and cmc.userid = mue.userid) as numoflearned from mdl_course mc inner join mdl_enrol me on mc.id = me.courseid inner join mdl_user_enrolments mue on me.id = mue.enrolid where me.enrol = \'manual\' and mc.deleted = 0 and mc.visible = 1 and mc.category <> 2 and mue.userid = '.$USER->id;
$courses = array_values($DB->get_records_sql($sql));
$courses_current = array();
$courses_all_required = array();
$courses_optional = array();
$courses_completed = array();
foreach ($courses as $course){
    if($course->numofmodule == 0 || $course->numoflearned/$course->numofmodule == 0){
        array_push($courses_all_required, $course);
    }
    else if($course->numoflearned/$course->numofmodule == 1){
        array_push($courses_completed, $course);
    }
    else if($course->numoflearned/$course->numofmodule > 0 && $course->numoflearned/$course->numofmodule < 1){
        array_push($courses_current, $course);
    }
}

$countBlock = 1;
//echo shell_exec('select * from mdl_user limit 1');

?>

<html>
<title>Trang chủ</title>
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
    @font-face {
        font-family: Awsome;
        src: url('fonts/fa-solid-900.ttf');
    }

    a{
        text-decoration: none;
    }
    a:hover{
        text-decoration: none;
    }

    .border-completed{
        color: #862055 !important;
    }

    .border-studying{
        color: #FFC400 !important;
    }
    .border-not-yet{
        color: #C7C7C7 !important;
    }

    .title{
        text-align: left;
        /*font: Black 145px/198px Nunito-Sans;*/
        font-family: Nunito-Sans;
        font-size: 20px;
        letter-spacing: 0px;
        color: #202020;
        text-transform: uppercase;
        opacity: 1;
        padding: 0;
    }

    .btn-click{
        background: #862055 0% 0% no-repeat padding-box;
        border-radius: 4px;
        opacity: 1;
    }
    .btn-click a{
        text-align: left;
        font: Bold 14px Roboto-Regular;
        letter-spacing: 0.45px;
        color: #FFFFFF;
        text-transform: uppercase;
        opacity: 1;
        /*font-family: Roboto;*/
    }
    .info-course p{
        margin: 0;
    }
    img{
        width: 100%;
    }
    .btn-show-all{
        text-align: right;
        padding: 0;
    }
    .block-items{
        margin: 0;
    }
    .block-items__item{
        background-color: white;
        -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        /*margin: 1%;*/
        max-width: 49%;
        margin-bottom: 5%;
        display: flex;
        padding: 0;
        overflow: hidden;

        height: 210px;
    }

    .block-items__item img{
        height: -webkit-fill-available;
    }

    .block-items__item-first{
        margin: 0 2% 1% 0;
    }

    .block-item__content_btn{
        width: 100%;
        text-align: right;
    }

    .title-course{
        text-align: left;
        font: Bold 15px Roboto-Bold;
        letter-spacing: 0.6px;
        color: #202020;
        opacity: 1;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .course-block__top{
        padding: 15px 0;
        margin: 20px 0;
        border-bottom: 1px solid #707070;
    }
    .course-block__top-show{
        display: flex;
        padding: 0;
    }
    .block-item__content{
        width: inherit;
        padding: 4% 3%;
    }

    .block-item__content_text{
        height: 80%;
    }

    .block-item__image{
        width: 80%;
        padding: 0;
        /*max-width: 45%;*/
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    .block-item__image img{
        width: 100%;
    }

    .info-user{
        background-color: #862055;
        width: 100%;
        /*height: 130px;*/
        display: flex;
        padding-right: 0;
    }
    .avatar{
        width: 100%;
        border-radius: 50%;
        margin: 0;
        padding: 0;
    }
    .avatar img{
        margin-top: 15%;
    }
    .info{
        /*padding: 15px 0;*/
        margin: 40px 0;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 3px 3px 6px #00000029;
        /*-webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);*/
        /*box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);*/
    }
    .info-user_info{
        padding: 10px;
        width: 100%;
    }
    .info-user_info p{
        text-align: left;
        color: #FFFFFF;
        text-transform: uppercase;
        opacity: 1;
        font-size: 14px;
    }
    .username{
        font: Bold 15px Roboto-Bold;
        letter-spacing: 0.6px;

    }
    .userposition{
        /*font: Light 12px Roboto-Light;*/
        letter-spacing: 0.5px;
        font-family: Roboto-Light;
    }

    .progress {
        width: 100px;
        height: 100px;
        background: none;
        position: relative;
        margin: 3%;
    }
    .info-progress{
        display: inline-flex;
        width: 100%;
    }

    .progress-note ul{
        padding: 0;
    }


    .progress-note ul li{
        list-style: none;
        padding: 5% 0;
        font-size: 14px;
    }

    .progress::after {
        content: "";
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 6px solid #eee;
        position: absolute;
        top: 0;
        left: 0;
    }

    .progress>span {
        width: 50%;
        height: 100%;
        overflow: hidden;
        position: absolute;
        top: 0;
        z-index: 1;
    }

    .progress .progress-left {
        left: 0;
    }

    .progress .progress-bar {
        width: 100%;
        height: 100%;
        background: none;
        border-width: 6px;
        border-style: solid;
        position: absolute;
        top: 0;
    }

    .progress .progress-left .progress-bar {
        left: 100%;
        border-top-right-radius: 80px;
        border-bottom-right-radius: 80px;
        border-left: 0;
        -webkit-transform-origin: center left;
        transform-origin: center left;
    }

    .progress .progress-right {
        right: 0;
    }

    .progress .progress-right .progress-bar {
        left: -100%;
        border-top-left-radius: 80px;
        border-bottom-left-radius: 80px;
        border-right: 0;
        -webkit-transform-origin: center right;
        transform-origin: center right;
    }

    .progress .progress-value {
        position: absolute;
        top: 0;
        left: 0;
    }

    /*
    *
    * ==========================================
    * FOR DEMO PURPOSE
    * ==========================================
    *
    */

    /*.info-progress {*/
    /*    background: #ff7e5f;*/
    /*    background: -webkit-linear-gradient(to right, #ff7e5f, #feb47b);*/
    /*    background: linear-gradient(to right, #ff7e5f, #feb47b);*/
    /*    min-height: 100vh;*/
    /*}*/

    .rounded-lg {
        border-radius: 1rem;
    }

    .text-gray {
        color: #aaa;
    }

    div.h4 {
        line-height: 1rem;
    }

    .info-text{
        color: #202020;
        padding: 10px;
    }
    .text-course{
        text-align: left;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        opacity: 1;
        width: 90%;
        font-size: 14px;
    }
    .text-number{
        text-align: right;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #737373;
        opacity: 1;
        width: 10%;
        font-size: 14px;

    }
    .info-text{
        display: flex;
    }
    .info-text:hover{
        cursor: pointer;
        text-decoration: none;
    }

    .carousel-caption h3{
        text-align: left;
        font-family: Nunito-Sans;
        letter-spacing: 0px;
        color: #862055;
        opacity: 1;
        font-size: 100px;
    }
    .carousel-caption p{
        text-align: left;
        font-family: Nunito-Sans;
        text-transform: uppercase;
        opacity: 1;
        color: white;
        font-size: 103px;
        -webkit-text-fill-color: white;
        -webkit-text-stroke-width: 2px;
        -webkit-text-stroke-color: #862055;
        letter-spacing: 7px;
        margin: -3% 0 0 10%;
    }

    .slide-logo img{
        position: absolute;
        width: 15%;
        top: 10%;
        left: 3%;
    }
    .slide-image img{
        position: absolute;
        width: 20%;
        bottom: 0;
        right: 6%;
        width: 30%;
    }

    .carousel-caption{
        left: 5%;
    }

    .section-footer{
        background: #202020 0% 0% no-repeat padding-box;
        border: 1px solid #707070;
        opacity: 1;
        padding: 1%;
    }
    .footer-ul{
        padding: 0;
    }
    .footer-ul li{
        list-style: none;
        text-align: left;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 0.45px;
        opacity: 1;
        margin-top: 5%;
    }
    .footer-ul a{
        text-decoration: none;
    }
    .footer-ul li a{
        color: #FFFFFF;
    }
    .footer-title{
        text-align: left;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 0.6px;
        color: #FFFFFF;
        opacity: 1;
        font-size: 20px;
    }
    .footer-logo {
        height: 11%;
        /*margin-top: 1%;*/
    }
    .footer-logo img{
        width: 15%;
        position: absolute;
    }

    .footer-full{
        padding-bottom: 1%;
        margin-top: 5%;
    }

    .content .container-fluid{
        padding: 4%;
    }

    .block-note{
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-flex;
    }

    .block-item__content_btn .btn{
        /*width: 100%;*/
    }

    .block-item__content_text a{
        text-decoration: none;
    }

    .info-course{
        color: rgba(115,115,115,1);
    }

    ..progress .progress-completed{
        left: 0;
    }

    .col-left{
        padding-right: 0;
    }

    .col-right{
        padding-left: 0;
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
        stroke-dashoffset:50;
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
    .percentage{
        font-family: Roboto-Regular;
        fill: #862055;
    }
</style>
<body>
<!--<div id="container1" style="min-width: 300px; height: 400px; margin: 0 auto"></div>-->
<?php
?>
<div class="wrapper"><!-- wrapper -->
    <section class="section section--header"><!-- section -->
        <header><!-- header -->
            <div class="content">
                <div class="slider">
                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#myCarousel" data-slide-to="1"></li>
                            <li data-target="#myCarousel" data-slide-to="2"></li>
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner">
                            <div class="item active">
                                <img src="images/17580-[Converted]-01-1.png" alt="Chania">
                                <div class="slide-logo">
                                    <img src="images/logo-black-1.png" alt="">
                                </div>
                                <div class="carousel-caption">
                                    <h3>Easia</h3>
                                    <p>ACADEMY</p>
                                </div>
                                <div class="slide-image">
                                    <img src="images/1a-01.png" alt="">
                                </div>
                            </div>

                            <!--                           <div class="item">-->
                            <!--                               <img src="images/17580-[Converted]-01.png" alt="Chicago">-->
                            <!--                               <div class="carousel-caption">-->
                            <!--                                   <h3>Easia</h3>-->
                            <!--                                   <p>ACADEMY</p>-->
                            <!--                               </div>-->
                            <!--                           </div>-->

                            <!--                           <div class="item">-->
                            <!--                               <img src="images/17580-[Converted]-01.png" alt="New York">-->
                            <!--                               <div class="carousel-caption">-->
                            <!--                                   <h3>Easia</h3>-->
                            <!--                                   <p>ACADEMY</p>-->
                            <!--                               </div>-->
                            <!--                           </div>-->
                            <!--                       </div>-->

                            <!-- Left and right controls -->
                            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
        </header>
    </section>

    <!--    body-->
    <section class="section section-content">
        <div class="content">
            <div class="container-fluid row">
                <div class="col-sm-3 col-left">
                    <div class="info">
                        <div class="info-user col-sm-12">
                            <div class="avatar col-sm-3">
                                <img src="images/avatar.png" alt="">
                            </div>
                            <div class="info-user_info col-sm-9">
                                <p class="username">Van Anh Tran</p>
                                <p class="userposition">Sales Senior Manager</p>
                            </div>
                        </div>
                        <div class="info-courses">
                            <div class="info-progress">
                                <div>
                                    <svg viewBox="0 0 36 36" width="150" class="circular-chart">
                                        <path class="that-circle" stroke="#C7C7C7" stroke-dasharray="100,100" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="that-circle" stroke="#FFC400" stroke-dasharray="0,100"  d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="that-circle" stroke="#862055" stroke-dasharray="0,100" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />

                                        <text x="18" y="20.35" class="percentage">0%</text>
                                    </svg>
                                </div>
<!--                                <div class="progress" data-value='--><?php //if(count($courses_all_required) == 0) echo 0; else echo count($courses_completed)*100/count($courses_all_required); ?><!--' data-value-studying='0'>-->
<!--                                    <span class="progress-left">-->
<!--                                        <span class="progress-bar border-completed"></span>-->
<!--                                      </span>-->
<!---->
<!--                                    <span class="progress-right">-->
<!--                                        <span class="progress-bar border-studying"></span>-->
<!--                                    </span>-->
<!--                                    <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">-->
<!--                                        <div class="h2 font-weight-bold">--><?php //if(count($courses_all_required) == 0) echo 0; else echo count($courses_completed)/count($courses_all_required); ?><!--<sup class="small">%</sup></div>-->
<!--                                    </div>-->
<!--                                </div>-->
                                <!-- END -->

                                <div class="progress-note">
                                    <ul>
                                        <li><div class="block-note" style="background-color: #862055"></div> Completed</li>
                                        <li><div class="block-note" style="background-color: #FFC400"></div> Studying</li>
                                        <li><div class="block-note" style="background-color: #C7C7C7"></div> Not yet learned</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="info-statistic">
                                <!--                                current courses-->
                                <div class="info-statistic__current-course">
                                    <a class="info-text">
                                        <div class="text-course">Current courses</div>
                                        <div class="text-number"><?php echo count($courses_current); ?></div>
                                    </a>
                                </div>

                                <!--                                next-required-->
                                <!--                                <div class="info-statistic__next-required">-->
                                <!--                                    <a class="info-text">-->
                                <!--                                        <div class="text-course">Next required course</div>-->
                                <!--                                        <div class="text-number">--><?php //count($courses_all_required) ?><!--</div>-->
                                <!--                                    </a>-->
                                <!--                                </div>-->

                                <!--                                all-required-->
                                <div class="info-statistic__all-required">
                                    <a class="info-text">
                                        <div class="text-course">All required courses</div>
                                        <div class="text-number"><?php echo count($courses_all_required); ?></div>
                                    </a>
                                </div>

                                <!--                                optional-courses-->
                                <div class="info-statistic__optional-courses">
                                    <a class="info-text">
                                        <div class="text-course">Optional courses</div>
                                        <div class="text-number"><?php echo count($courses_optional); ?></div>
                                    </a>
                                </div>

                                <!--                                completed-courses-->
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
                </div>
                <div class="col-sm-1"></div>
                <div class="col-sm-8 col-right">
                    <div class="courses">

                        <!--                        current course-->
                        <div class="courses-block">
                            <!--                            top-->
                            <div class="course-block__top row">
                                <div class="col-sm-12 course-block__top-show">
                                    <div class=" col-sm-6 title">CURRENT COURSES</div>
                                    <div class="col-sm-6 btn-show btn-show-all">
                                        <button class="btn btn-click"><a href="">Show All</a></button>
                                    </div>
                                </div>
                            </div>

                            <!--                            content-->
                            <div class="courses-block__content">
                                <div class="courses-block__content__item row">
                                    <!--                                    line 1-->
                                    <div class="col-sm-12 row block-items">
                                        <!--                                        block 1-->
                                        <?php $countBlock = 1; foreach ($courses_current as $course) {  ?>
                                            <div class="col-sm-6 block-items__item <?php if($countBlock % 2 != 0) echo "block-items__item-first"; ?>">
<!--                                            <div class="col-sm-6 block-items__item">-->
                                                <div class="block-item__image" style="background-image: url('/elearning-easia/public<?php echo $course->course_avatar; ?>')">
<!--                                                    <img src="/elearning-easia/public--><?php //echo $course->course_avatar; ?><!--" alt="">-->
                                                </div>
                                                <div class="block-item__content">
                                                    <div class="block-item__content_text">
                                                        <a href="lms/course/view.php?id=<?php echo $course->id; ?>"><p class="title-course"><i></i><?php echo $course->fullname; ?></p></a>
                                                        <div class="info-course">
                                                            <p class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</p>
                                                            <p class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</p>
                                                            <p class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</p>
                                                        </div>
                                                    </div>
                                                    <div class="block-item__content_btn">
                                                        <button class="btn btn-click"><a href="lms/course/view.php?id=<?php echo $course->id; ?>">Learn More</a></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php  $countBlock++; if($countBlock == 5) break; } ?>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <!--                        all required courses-->
                        <div class="courses-block">
                            <!--                            top-->
                            <div class="course-block__top row">
                                <div class="col-sm-12 course-block__top-show">
                                    <div class=" col-sm-6 title">ALL REQUIRED COURSES</div>
                                    <div class="col-sm-6 btn-show btn-show-all">
                                        <button class="btn btn-click"><a href="">Show All</a></button>
                                    </div>
                                </div>
                            </div>

                            <!--                            content-->
                            <div class="courses-block__content">
                                <div class="courses-block__content__item row">
                                    <!--                                    line 1-->
                                    <div class="col-sm-12 row block-items">
                                        <!--                                        block 1-->
                                        <?php  $countBlock = 1; foreach ($courses_all_required as $course) { ?>
                                            <div class="col-sm-6 block-items__item <?php if($countBlock % 2 != 0) echo "block-items__item-first"; ?>">
<!--                                            <div class="col-sm-6 block-items__item">-->
                                                <div class="block-item__image" style="background-image: url('/elearning-easia/public<?php echo $course->course_avatar; ?>')">
<!--                                                    <img src="/elearning-easia/public--><?php //echo $course->course_avatar; ?><!--" alt="">-->
                                                </div>
                                                <div class="block-item__content">
                                                    <div class="block-item__content_text">
                                                        <a href="lms/course/view.php?id=<?php echo $course->id; ?>"><p class="title-course"><i></i><?php echo $course->fullname; ?></p></a>
                                                        <div class="info-course">
                                                            <p class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</p>
                                                            <p class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</p>
                                                            <p class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</p>
                                                        </div>
                                                    </div>
                                                    <div class="block-item__content_btn">
                                                        <button class="btn btn-click"><a href="lms/course/view.php?id=<?php echo $course->id; ?>">Learn More</a></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php  $countBlock++; if($countBlock == 5) break; } ?>
                                    </div>

                                </div>
                            </div>

                            <!--                        optional courses-->
                            <div class="courses-block">
                                <!--                            top-->
                                <div class="course-block__top row">
                                    <div class="col-sm-12 course-block__top-show">
                                        <div class=" col-sm-6 title">OPTIONAL COURSES</div>
                                        <div class="col-sm-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a href="">Show All</a></button>
                                        </div>
                                    </div>
                                </div>

                                <!--                            content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row">
                                        <!--                                    line 1-->
                                        <div class="col-sm-12 row block-items">
                                            <!--                                        block 1-->
                                            <?php  $countBlock = 1; foreach ($courses_optional as $course) { ?>
                                                <div class="col-sm-6 block-items__item <?php if($countBlock % 2 != 0) echo "block-items__item-first"; ?>">
                                                    <!--                                            <div class="col-sm-6 block-items__item">-->
                                                    <div class="block-item__image">
                                                        <img src="/elearning-easia/public<?php echo $course->course_avatar; ?>" alt="">
                                                    </div>
                                                    <div class="block-item__content">
                                                        <div class="block-item__content_text">
                                                            <a href="lms/course/view.php?id=<?php echo $course->id; ?>"><p class="title-course"><i></i><?php echo $course->fullname; ?></p></a>
                                                            <div class="info-course">
                                                                <p class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</p>
                                                                <p class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</p>
                                                                <p class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</p>
                                                            </div>
                                                        </div>
                                                        <div class="block-item__content_btn">
                                                            <button class="btn btn-click"><a href="lms/course/view.php?id=<?php echo $course->id; ?>">Learn More</a></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php  $countBlock++; if($countBlock == 5) break; } ?>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!--                        completed courses-->
                            <div class="courses-block">
                                <!--                            top-->
                                <div class="course-block__top row">
                                    <div class="col-sm-12 course-block__top-show">
                                        <div class=" col-sm-6 title">COMPLETED COURSES</div>
                                        <div class="col-sm-6 btn-show btn-show-all">
                                            <button class="btn btn-click"><a href="">Show All</a></button>
                                        </div>
                                    </div>
                                </div>

                                <!--                            content-->
                                <div class="courses-block__content">
                                    <div class="courses-block__content__item row">
                                        <!--                                    line 1-->
                                        <div class="col-sm-12 row block-items">
                                            <!--                                        block 1-->
                                            <?php $countBlock = 1; foreach ($courses_completed as $course) {  ?>
                                                <div class="col-sm-6 block-items__item <?php if($countBlock % 2 != 0) echo "block-items__item-first"; ?>">
                                                    <div class="block-item__image" style="background-image: url('/elearning-easia/public<?php echo $course->course_avatar; ?>')">
                                                    </div>
                                                    <div class="block-item__content">
                                                        <div class="block-item__content_text">
                                                            <a href="lms/course/view.php?id=<?php echo $course->id; ?>"><p class="title-course"><i></i><?php echo $course->fullname; ?></p></a>
                                                            <div class="info-course">
                                                                <p class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</p>
                                                                <p class="units"><i class="fa fa-file" aria-hidden="true"></i> <?php echo $course->numofmodule; ?> Units</p>
                                                                <p class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $course->estimate_duration; ?> hours</p>
                                                            </div>
                                                        </div>
                                                        <div class="block-item__content_btn">
                                                            <button class="btn btn-click"><a href="lms/course/view.php?id=<?php echo $course->id; ?>">Learn More</a></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php  $countBlock++; if($countBlock == 5) break; } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </section>

    <section class="section-footer">
        <footer>
            <div class="container-fluid row">
                <div class="footer-logo">
                    <img src="images/logo-write.png" alt="">
                </div>
                <div class="col-12 row footer-full">
                    <!--            Helps-->
                    <div class="footer-block col-3">
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

                    <!--                   FAQs-->
                    <div class="footer-block col-3">
                        <div class="footer-block__title"><p class="footer-title">FQAs</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Courses</a></li>
                                <li><a href="/">Profile</a></li>
                                <li><a href="/">Profile</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="footer-block col-3">
                        <div class="footer-block__title"><p class="footer-title">Contact</p></div>
                        <div class="footer-block__ul">
                            <ul class="footer-ul">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Courses</a></li>
                                <li><a href="/">Profile</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="footer-block col-3">
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
</div>

<script>
    $(function() {

        $(".progress").each(function() {

            var value = $(this).attr('data-value');
            // var valueCompleted = $(this).attr('data-value-completed');
            var valueStudying = $(this).attr('data-value-studying');
            var left = $(this).find('.progress-left .progress-bar');
            var right = $(this).find('.progress-right .progress-bar');

            if (value > 0) {
                if (value <= 50) {
                    left.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)');
                }
                else {
                    left.css('transform', 'rotate(180deg)');
                    right.css('transform', 'rotate(-' + percentageToDegrees(value-50) + 'deg)');
                }
            }

            if (valueStudying > 0) {
                if (valueStudying <= 50) {
                    left.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)');
                }
                else {
                    left.css('transform', 'rotate(180deg)');
                    right.css('transform', 'rotate(-' + percentageToDegrees(value-50) + 'deg)');
                }
            }


            // if (valueCompleted > 0) {
            //     if (valueCompleted <= 50) {
            //         right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)');
            //     }
            //     else {
            //         right.css('transform', 'rotate(180deg)');
            //         left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)');
            //     }
            // }

            // if (valueStudying > 0) {
            //     if (valueStudying <= 50) {
            //         right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)');
            //     }
            //     else {
            //         right.css('transform', 'rotate(180deg)');
            //         left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)');
            //     }
            // }

        });

        function percentageToDegrees(percentage) {
            return percentage / 100 * 360
        }

        //localStorage.setItem('courses', <?php //json_decode($courses_current); ?>//);
    });

</script>
</body>
</html>


<?php
die;
?>
