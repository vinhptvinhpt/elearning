<?php
if (!isloggedin()) {
    require_login();
}
require_once(__DIR__ . '/../../../../config.php');
$type = optional_param('type', 0, PARAM_TEXT);
$progress = optional_param('progress', 0, PARAM_INT);

$home_selection = [
    'current' => 'Current courses',
    'required' => 'Required courses',
    'completed' => 'Completed courses',
    'other' => 'Optional courses',
];

$category_params = 0;

if (strlen($type) != 0) {
    $category_params = $type;
}

//Hide course library, client course
if ($progress != 1) {
    $sqlGetCategories = 'select id, name from mdl_course_categories where id NOT IN (7, 5, 2)';
    $categories = array_values($DB->get_records_sql($sqlGetCategories));
}

//get image badge
$sqlGetBadge = "select path from image_certificate where type =2 and is_active";
$pathBadge = array_values($DB->get_records_sql($sqlGetBadge))[0]->path;

?>

<html>
<title>Available Coures</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="shortcut icon" href="images/favicon.png">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="//unpkg.com/vue-plain-pagination@0.2.1"></script>

<style>
    @font-face {
        font-family: Nunito-Sans-Regular;
        src: url('fonts/NunitoSans-Regular.ttf');
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
        font-family: Nunito-Bold;
        src: url('fonts/Nunito-Bold.ttf');
    }

    @font-face {
        font-family: HelveticaLTStd-Bold;
        src: url('fonts/HelveticaLTStd-Bold.otf');
    }

    @font-face {
        font-family: HelveticaLTStd-Light;
        src: url('fonts/HelveticaLTStd-Light.otf');
    }

    img {
        width: 100%;
    }

    body {
        /*font-size: 14px !important;*/
        font-family: Roboto-Bold;
        background-color: #F1F1F1;
    }

    ul {
        list-style: none;
    }

    a {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
        opacity: 0.5;
    }

    select option {
        margin: 40px;
        background: rgba(0, 0, 0, 0.3);
        /*background-color: transparent !important;*/
        color: #fff;
        text-shadow: 0 1px 0 rgba(0, 0, 0, 0.4);
    }

    #category, .input-search, .btn-search {
        background-color: #211f1f7d !important;
    }

    textarea:focus, input:focus {
        outline: none;
    }

    #page {
        margin-top: 50px !important;
    }

    #region-main-box, #region-main {
        padding: 0 !important;
    }

    #page-wrapper .navbar {
        padding: 7px 1rem 9px .5rem !important;
    }

    .navbar .count-container {
        top: 2px !important;
    }

    .img-completed{
        width: 40px !important;
        height: 40px !important;
    }
    /*    view*/
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
        color: #ffff !important;
        font-size: 13px;
        margin-left: 80%;
    }

    .div-image-disable {
        background-color: #fdf2f285;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .course-info__detail {
        padding: 5% 0;
        min-height: 130px;
    }

    .course-info__detail ul {
        padding: 0;
        width: 100%;
        margin: 0;
    }

    .block-item__content {
        padding: 4% 3%;
    }

    .course-info {
        height: 80%;
    }

    .course-info__detail ul li {
        margin-right: 8%;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #737373;
        font-size: 14px;
        width: 100%;
    }

    .course-info__list-lessons ul {
        padding: 5% 0;
        padding-top: 0;
    }

    .course-info__list-lessons ul li {
        padding: 1% 0;
        margin-top: 3%;
        border-bottom: 1px solid #C7C7C7;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #3469FF;
        background-image: url('images/file-tick.png');
        background-repeat: no-repeat;
        background-position: right;
    }

    .course-info__list-lessons ul li a {
        display: inline-flex;
        width: 100%;
    }

    .course-info__list-lessons ul li a span {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        width: 90%;
    }


    .btn-click {
        background: <?=$_SESSION["color"]?> 0% 0% no-repeat padding-box !important;
        border-radius: 4px;
        opacity: 1;
    }

    .btn-click a {
        text-align: left;
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #FFFFFF !important;
        text-transform: uppercase;
        font-size: 13px;
    }

    .percent {
        width: 90px;
        height: 84px;
        transform: matrix(-0.91, 0.42, -0.42, -0.91, 0, 0);
        background: transparent linear-gradient(132deg, #862055 0%, #A30088 100%) 0% 0% no-repeat padding-box;
        opacity: 0.59;
    }

    .course-info__title a {
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
        opacity: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        /*font-family: Roboto-Bold !important;*/
        font-family: Roboto !important;
    }

    .course-info__title {
        padding: 2% 0;
    }

    .btn-page {
        text-align: right;
        padding-bottom: 15px;
    }

    .block {
        padding-bottom: 2%;
        max-width: 48%;
        padding-right: 0 !important;
    }


    .course-block {
        background-color: #ffffff;
        position: relative;
        padding: 0 !important;;
        box-shadow: 3px 3px 6px #00000029;
        margin: 0 !important;
        height: 100%;
    }

    .course-block-disable {
        pointer-events: none;
    }

    .course-block__image {
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        min-height: 150px;
        padding: 0 !important;
        position: relative;
    }

    .course-block__image img {
        /*width: 32%;*/
        /*height: 26%;*/
        /*position: absolute;*/
        /*top: 3%;*/
        /*right: 3%;*/
        width: 60px;
        height: 60px;
        position: absolute;
        top: 3%;
        right: 3%;
    }

    .course-block__image span {
        font-size: 13px;
        font-family: Nunito-Bold;
        color: #FFFFFF;
        position: absolute;
        top: 12%;
        right: 12%;
        letter-spacing: 1px;
    }

    .section-header {
        background-image: url('<?=$_SESSION["pathImgAvailableCourse"]?>');
        width: 100%;
        background-repeat: no-repeat;
        background-position: 100% 90%;
        background-size: cover;
        position: relative;
    }

    .section-header .container {
        padding-top: calc(100% - 95%);
    }

    .block-color {
        width: 100px;
        height: 100px;
        background-color: <?=$_SESSION["color"]?>;
        position: absolute;
        z-index: 1;
        left: 0%;
        top: -1%;
    }

    .header-block {
        position: absolute;
        z-index: 2;
        left: 75px;
    }

    .header-block__logo img {
        width: 25%;
        padding: 4% 0;
    }

    .header-block__search__title p {
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #ffffff;
        font-size: 16px;
    }


    .header-block__search__title h1 {
        font-family: HelveticaLTStd-Bold;
        color: #ffffff;
        font-size: 66px;
        bottom: 25%;
        letter-spacing: 3px;
        margin-bottom: 0;
    }

    .header-block__search__title span {
        font-family: HelveticaLTStd-Light;
        /*font-size: 45px;*/
    }

    .header-block__search__title .title-header {
        font-family: Roboto-Bold;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        color: #202020;
        font-size: 36px;
        padding-top: 2%;
    }

    .header-block__quick-filter__title p {
        font-family: Roboto-Bold;
        letter-spacing: 0.9px;
        color: #202020;
        font-size: 32px;
        padding: 2% 0;
    }

    .header-block__quick-filter__title h2 {
        font-family: HelveticaLTStd-Bold;
        font-size: 2rem;
    }

    .header-block__quick-filter__title span {
        font-family: HelveticaLTStd-Light;
    }

    .course-select, .input-search {
        font-size: 13px !important;
        letter-spacing: 0.45px !important;
        font-family: Roboto-Regular;
        border: 1px solid #ffffffad !important;
        border-radius: inherit !important;
        /*background-color: transparent !important;*/
        color: #ffffff !important;
    }

    .input-search {
        padding: .375rem .75rem;
        border-right: 0 !important;
        width: 85%;
    }
    ::placeholder {
        color: rgba(255, 255, 255, 0.75);
        opacity: 1; /* Firefox */
    }

    :-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: rgba(255, 255, 255, 0.75);
    }

    ::-ms-input-placeholder { /* Microsoft Edge */
        color: rgba(255, 255, 255, 0.75);
    }

    .block-search__select {
        padding-right: 0 !important;
        padding-left: 0 !important;
    }

    .block-search__btn {
        padding-left: .25rem !important;
        display: flex;
    }

    .btn-search {
        border: 1px solid #ffffffad;
        border-left: 0;
    }

    .btn-search i {
        position: absolute;
        color: #FFFFFF;
        top: 25%;
        padding: 3px 10px;
    }

    .btn-search input {
        background-color: transparent;
        width: 100%;
        border: none;
        padding: 5px 15px;
    }

    .btn-search input:hover, .btn-search i:hover {
        cursor: pointer;
    }

    .btn-click-course {
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 3px 3px 6px #00000029;
        border: 1px solid #c7c7c7bf !important;
        border-radius: 4px;
        /*font-family: Nunito-Bold;*/
        font-family: Roboto;
        font-weight: 700 !important;
        font-size: 13px !important;
        color: #737373 !important;
        letter-spacing: 0.45px;
        /*margin-right: 2%;*/
        min-width: 135px;
    }

    .btn-click-active {
        background: transparent linear-gradient(97deg, <?=$_SESSION["color"]?> 0%, <?=$_SESSION["color"]?> 100%) 0% 0% no-repeat padding-box;
        color: #FFFFFF !important;
        border: 1px solid <?=$_SESSION["color"]?> !important;
    }

    .block-search, .header-block__quick-filter__main ul {
        padding-left: 0;
    }

    .section-course-info {
        margin: 1rem 0;
    }

    /*footer*/
    .section-footer {
        background: #000000 0% 0% no-repeat padding-box;
        border: 1px solid #707070;
        opacity: 1;
        padding: 4% 0;
    }

    .footer-ul {
        padding: 0;
        /*padding-left: 5%;*/
    }

    .footer-ul li {
        list-style: none;
        text-align: left;
        font-family: Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        letter-spacing: 0.6px;
        opacity: 1;
        margin-top: 5%;
        font-size: 20px;
    }

    .footer-ul a {
        text-decoration: none;
    }

    .footer-ul li a {
        color: #FFFFFF;
        /*font-size: 13px;*/
    }

    .footer-title {
        text-align: left;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 0.6px;
        color: #FFFFFF;
        opacity: 1;
        font-size: 20px;
        margin: 0;
    }

    .footer-logo {
        height: auto;
        width: auto;
    }

    /*.footer-logo img {*/
    /*    width: 15%;*/
    /*    position: absolute;*/
    /*}*/

    #overlay {
        position: fixed; /* Sit on top of the page content */
        display: block; /* Hidden by default */
        width: 100%; /* Full width (cover the whole page) */
        height: 100%; /* Full height (cover the whole page) */
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
        z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
        z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
        cursor: pointer; /* Add a pointer on hover */
    }

    .div-text {
        position: relative;
        width: 100%;
        top: 0;
        left: 0;
        min-height: 400px;
        z-index: 1;
        /*background-color: rgba(0, 0, 0, 0.3);*/
    }


    .div-header {
        background-color: rgba(0, 0, 0, 0.3);
    }

    .title-course {
        /*margin: 0;*/
        text-align: left;
        letter-spacing: 0.6px;
        font-size: 17px;
        font-family: Roboto;
        color: #202020;
        opacity: 1;
        /* margin-bottom: 20px; */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 50px;
        font-weight: 700;
    }

    /*1920*/
    @media screen and (max-width: 1920px) and (min-width: 1369px) {
        .btn-click-course {
            min-width: 188px;
            font-size: 18px !important;
            padding: 13px !important;
        }

        .header-block__search__title p {
            font-size: 18px;
        }

        .course-select, .input-search {
            font-size: 18px !important;
        }

        .input-search {
            padding: 0 2%;
        }

        .btn-click-course {
            margin-bottom: 2%;
        }
    }

    .footer-logo {
        height: auto;
        width: auto;
    }

    @media screen and (min-width: 2000px) {
        .col-xxl-3 {
            flex: 0 0 25% !important;
            max-width: 25% !important;
        }
    }

    @media screen and (max-width: 425px) {
        .section-footer .container{
            padding: 3% 3%;
        }
    }

    @media screen and (max-width: 375px) {
        .header-block {
            left: 52px;
        }

        .title-course {
            min-height: 3.25rem;
        }
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
    <?php echo $OUTPUT->header(); ?>
    <div id="app">
        <section class="section section-header"><!-- section -->
            <div class="div-header">
                <div class="container">
                    <div class="div-text">
                        <div class="header-block">
                            <!--                    <div class="header-block__logo">-->
                            <!--                        <img src="-->
                            <?php //echo $_SESSION["pathLogo"]; ?><!--" alt="Logo">-->
                            <!--                    </div>-->
                            <div class="header-block__search">
                                <div class="header-block__search__title">
                                    <h1>Available <span>Courses</span></h1>
                                    <p>Search your target courses here</p>
                                </div>
                                <div class="header-block__search__btn-search">
                                    <div class="row col-12 block-search">
                                        <div class="col-5 col-md-4 block-search__select">
                                            <select name="category" id="category" class="form-control course-select"
                                                    @change="searchCourse(category, 1)"
                                                    v-model="category">
                                                <?php if ($progress == 1) {
                                                    foreach ($home_selection as $key => $value) {
                                                        ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php }
                                                } else { ?>
                                                    <option value="0">All courses</option>
                                                    <?php foreach ($categories as $category) { ?>
                                                        <option
                                                            value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-8 block-search__btn">
                                            <input type="text" class=" input-search" v-model="txtSearch" placeholder="Search by name course">
                                            <div class="btn-search" @click="searchCourse(category, 1)"><i
                                                    class="fa fa-search" aria-hidden="true"></i><input type="button">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="block-color"></div>
                    </div>
                </div>
            </div>
        </section>

        <!--    body-->
        <section class="section section-content section-course-info">
            <div class="container">
                <div class="header-block__quick-filter mt-5">
                    <div class="header-block__quick-filter__title mb-4"><h2>Quick <span>Filter</span></h2></div>
                    <div class="header-block__quick-filter__main">
                        <ul>
                            <?php if ($progress == 1) {
                                foreach ($home_selection as $key => $value) {
                                    ?>
                                    <li class="btn btn-click-course" @click="searchCourse('<?php echo $key; ?>', 1)"
                                        id="ctgr<?php echo $key; ?>"
                                        category="<?php echo $key; ?>"><?php echo $value; ?></li>
                                <?php }
                            } else { ?>
                                <li class="btn btn-click-course btn-click-active mb-2" id="ctgr0" category="0"
                                    @click="searchCourse(0, 1)">All Courses
                                </li>
                                <?php foreach ($categories as $category) { ?>
                                    <li class="btn btn-click-course mb-2"
                                        @click="searchCourse(<?php echo $category->id; ?>, 1)"
                                        id="ctgr<?php echo $category->id; ?>"
                                        category="<?php echo $category->id; ?>"><?php echo $category->name; ?></li>
                                <?php } ?>
                            <?php } ?>

                        </ul>
                    </div>
                </div>
                <div class="row pt-2">
                    <template v-if="courses.length == 0">
                        <div class="col-12 pt-1"><h3>No course to display</h3></div>
                    </template>
                    <template v-else-if="category == 'required'">
                        <div class="col-xxl-3 col-md-4 col-sm-6 col-xs-12 block clctgr0"
                             v-for="(course,index) in courses">
<!--                            Nếu training_deleted == 0 (khóa nằm trong khung năng lực) và stt show > 1 hoặc mã của trainning đó đã tồn tại trong current course => auto không cho học-->
                            <div v-if="course.training_deleted == 0 && (course.sttShow > 1 || (competency_exists.includes(course.training_id)))">
                                <div class="row course-block course-block-disable">
                                    <div class="col-5 course-block__image"
                                         v-bind:style="{ backgroundImage: 'url('+(urlImage+''+course.course_avatar)+')' }">
                                        <div class="div-image">
                                            <template v-if="course.numofmodule == 0">
                                                <img src="<?php echo $_SESSION['component'] ?>" alt=""><span>0%</span>
                                            </template>
                                            <template v-else>
                                                <img src="<?php echo $_SESSION['component'] ?>" alt=""><span>{{ Math.floor(course.numoflearned*100/course.numofmodule) }}%</span>
                                            </template>
                                        </div>
                                        <div class="div-image-disable"></div>
                                    </div>
                                    <div class="col-7">
                                        <div class="course-info">
                                            <div class="info-text">
                                                <div class="course-info__title">
                                                    <a :href="'lms/course/view.php?id='+course.id"
                                                       :title="course.fullname">
                                                        <p class="title-course"><i></i>{{course.fullname}}</p></a>
                                                </div>
                                                <div class="course-info__detail">
                                                    <ul>
                                                        <li class="teacher" v-if="course.teacher_name" title="Teacher name">
                                                            <i class="fa fa-user" aria-hidden="true"></i> {{ course.teacher_name }}
                                                        </li>

                                                        <li class="units" title="Competency name"><i class="fa fa-file" aria-hidden="true"></i>
                                                            {{course.training_name}}
                                                        </li>
                                                        <li class="units" v-if="course.estimate_duration" title="Estimate time">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                            {{course.estimate_duration}} hours
                                                        </li>
                                                    </ul>
                                                </div>
                                                <p v-if="course.training_deleted == 0" class="number-order">{{ course.sttShow }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
<!--                            Ngược lại: khóa học lẻ (training_deleted == 2), sttShow == 1 (Khung năng lực chưa có khóa nào học và đây là khóa đầu tiên), !competency_exists.includes(course.training_id)-->
                            <div v-else>
                                <div class="row course-block">
                                    <div class="col-5 course-block__image"
                                         v-bind:style="{ backgroundImage: 'url('+(urlImage+''+course.course_avatar)+')' }">
                                        <template v-if="course.numofmodule == 0"><img
                                                src="<?php echo $_SESSION['component'] ?>" alt=""><span>0%</span>
                                        </template>
                                        <template v-else><img src="<?php echo $_SESSION['component'] ?>" alt=""><span>{{ Math.floor(course.numoflearned*100/course.numofmodule) }}%</span>
                                        </template>
                                    </div>
                                    <div class="col-7">
                                        <div class="course-info">
                                            <div class="info-text">
                                                <div class="course-info__title">
                                                    <a :href="'lms/course/view.php?id='+course.id"
                                                       :title="course.fullname">
                                                        <p class="title-course"><i></i>{{course.fullname}}</p></a>
                                                </div>
                                                <div class="course-info__detail">
                                                    <ul>
                                                        <li class="teacher" v-if="course.teacher_name" title="Teacher name">
                                                            <i class="fa fa-user" aria-hidden="true"></i> {{ course.teacher_name }}
                                                        </li>
                                                        <li class="units" title="Competency name"><i class="fa fa-file" aria-hidden="true"></i>
                                                            {{course.training_name}}
                                                        </li>
                                                        <li class="units" v-if="course.estimate_duration" title="Estimate time">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                            {{course.estimate_duration}} hours
                                                        </li>
                                                    </ul>
                                                </div>
                                                <p v-if="course.training_deleted == 0" class="number-order">{{ course.sttShow }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else-if="category == 'other'">
                        <div class="col-xxl-3 col-md-4 col-sm-6 col-xs-12 block clctgr0"
                             v-for="(course,index) in courses">
                            <div class="row course-block">
                                <div class="col-5 course-block__image"
                                     v-bind:style="{ backgroundImage: 'url('+(urlImage+''+course.course_avatar)+')' }">
                                </div>
                                <div class="block-item__content col-7">
                                    <div class="course-info">
                                        <div class="info-text">
                                            <a :href="'lms/course/view.php?id='+course.id" :title="course.fullname">
                                                <p class="title-course"><i></i>{{course.fullname}}</p></a>
                                            <div class="course-info__detail">
                                                <ul>
                                                    <li class="teacher" v-if="course.teacher_name" title="Teacher name">
                                                        <i class="fa fa-user" aria-hidden="true"></i> {{ course.teacher_name }}
                                                    </li>
                                                    <li class="units" title="Estimate time">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                        {{course.estimate_duration}} hours
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else-if="category == 'completed'">
                        <div class="col-xxl-3 col-md-4 col-sm-6 col-xs-12 block clctgr0"
                             v-for="(course,index) in courses">
                            <div class="row course-block">
                                <div class="col-5 course-block__image"
                                     v-bind:style="{ backgroundImage: 'url('+(urlImage+''+course.course_avatar)+')' }">
                                    <div class="div-image">
                                            <img style="width: 40px !important; height: 40px !important;" src="<?php echo $CFG->wwwtmsbase.$pathBadge; ?>" alt="">
                                    </div>
                                </div>
                                <div class="block-item__content col-7">
                                    <div class="course-info">
                                        <div class="info-text">
                                            <a :href="'lms/course/view.php?id='+course.id" :title="course.fullname">
                                                <p class="title-course"><i></i>{{course.fullname}}</p></a>
                                            <div class="course-info__detail">
                                                <ul>
                                                    <li class="teacher" v-if="course.teacher_name" title="Teacher name">
                                                        <i class="fa fa-user" aria-hidden="true"></i> {{ course.teacher_name }}
                                                    </li>
                                                    <li class="units" v-if="course.training_name" title="Competency name"><i class="fa fa-file" aria-hidden="true"></i>
                                                        {{course.training_name}}
                                                    </li>
                                                    <li class="units" title="Estimate time">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                        {{course.estimate_duration}} hours
                                                    </li>
                                                </ul>
                                            </div>
                                            <p v-if="course.training_deleted == 0" class="number-order">{{ course.sttShow }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-xxl-3 col-md-4 col-sm-6 col-xs-12 block clctgr0"
                             v-for="(course,index) in courses">
                            <div v-if="course.category_type == 'required'">
                                <div v-if="!competency_exists.includes(course.training_id) && course.stt_count == 1">
                                    <div class="row course-block">
                                        <div class="col-5 course-block__image"
                                             v-bind:style="{ backgroundImage: 'url('+(urlImage+''+course.course_avatar)+')' }">
                                            <div class="div-image">
                                                <template v-if="course.numofmodule == 0">
                                                    <img src="<?php echo $_SESSION['component'] ?>"
                                                         alt=""><span>0%</span>
                                                </template>
                                                <template v-else>
                                                    <img src="<?php echo $_SESSION['component'] ?>" alt=""><span>{{ Math.floor(course.numoflearned*100/course.numofmodule) }}%</span>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div class="course-info">
                                                <div class="info-text">
                                                    <div class="course-info__title">
                                                        <a :href="'lms/course/view.php?id='+course.id"
                                                           :title="course.fullname">
                                                            <p class="title-course"><i></i>{{course.fullname}}</p></a>
                                                    </div>
                                                    <div class="course-info__detail">
                                                        <ul>
                                                            <li class="teacher" v-if="course.teacher_name" title="Teacher name">
                                                                <i class="fa fa-user" aria-hidden="true"></i> {{ course.teacher_name }}
                                                            </li>
                                                            <li class="units" title="Competency name"><i class="fa fa-file"
                                                                                 aria-hidden="true"></i>
                                                                {{course.training_name}}
                                                            </li>
                                                            <li class="units" title="Estimate time">
                                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                                {{course.estimate_duration}} hours
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <p v-if="course.training_deleted == 0" class="number-order">{{ course.sttShow }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="row course-block course-block-disable">
                                    <div class="col-5 course-block__image"
                                         v-bind:style="{ backgroundImage: 'url('+(urlImage+''+course.course_avatar)+')' }">
                                        <div class="div-image">
                                            <template v-if="course.numofmodule == 0">
                                                <img src="<?php echo $_SESSION['component'] ?>" alt=""><span>0%</span>
                                            </template>
                                            <template v-else>
                                                <img src="<?php echo $_SESSION['component'] ?>" alt=""><span>{{ Math.floor(course.numoflearned*100/course.numofmodule) }}%</span>
                                            </template>
                                        </div>
                                        <div class="div-image-disable"></div>
                                    </div>
                                    <div class="col-7">
                                        <div class="course-info">
                                            <div class="info-text">
                                                <div class="course-info__title">
                                                    <a :href="'lms/course/view.php?id='+course.id"
                                                       :title="course.fullname">
                                                        <p class="title-course"><i></i>{{course.fullname}}</p></a>
                                                </div>
                                                <div class="course-info__detail">
                                                    <ul>
                                                        <li class="teacher" v-if="course.teacher_name" title="Teacher name">
                                                            <i class="fa fa-user" aria-hidden="true"></i> {{ course.teacher_name }}
                                                        </li>
                                                        <li class="units" title="Competency name"><i class="fa fa-file" aria-hidden="true"></i>
                                                            {{course.training_name}}
                                                        </li>
                                                        <li class="units" title="Estimate time">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                            {{course.estimate_duration}} hours
                                                        </li>
                                                    </ul>
                                                </div>
                                                <p v-if="course.training_deleted == 0" class="number-order">{{ course.sttShow }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="row course-block">
                                <div class="col-5 course-block__image"
                                     v-bind:style="{ backgroundImage: 'url('+(urlImage+''+course.course_avatar)+')' }">
                                    <template v-if="course.numofmodule == 0"><img
                                            src="<?php echo $_SESSION['component']; ?>" alt=""><span>0%</span></template>
                                    <template v-else-if="course.numoflearned/course.numofmodule == 1">
                                        <img style="width: 40px !important; height: 40px !important;" src="<?php echo $CFG->wwwtmsbase.$pathBadge; ?>"
                                             alt="" class="img-completed">
                                    </template>
                                    <template v-else><img src="<?php echo $_SESSION['component']; ?>" alt=""><span>{{ Math.floor(course.numoflearned*100/course.numofmodule) }}%</span>
                                    </template>
                                </div>
                                <div class="col-7">
                                    <div class="course-info">
                                        <div class="info-text">
                                            <div class="course-info__title">
                                                <a :href="'lms/course/view.php?id='+course.id" :title="course.fullname">
                                                    <p class="title-course"><i></i>{{course.fullname}}</p></a>
                                            </div>
                                            <div class="course-info__detail">
                                                <ul>
                                                    <li class="teacher" v-if="course.teacher_name" title="Teacher name">
                                                        <i class="fa fa-user" aria-hidden="true"></i> {{ course.teacher_name }}
                                                    </li>
                                                    <li class="units" title="Competency name" v-if="course.training_name"><i class="fa fa-file"
                                                                                                     aria-hidden="true"></i>
                                                        {{course.training_name}}
                                                    </li>
                                                    <li class="units" title="Estimate time">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                        {{course.estimate_duration}} hours
                                                    </li>
                                                </ul>
                                            </div>
                                            <p v-if="course.training_deleted == 0" class="number-order">{{ course.sttShow }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

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
<!--                            <div class="footer-block__title"><p class="footer-title">Home</p></div>-->
                            <div class="footer-block__ul">
                                <ul class="footer-ul">
                                    <li><a href="lms/my">Home</a></li>
                                    <li><a href="lms/course/index.php">Courses</a></li>
                                    <li><a href="lms/user/profile.php?id=<?php echo $USER->id; ?>">Profile</a></li>
                                    <?php if($_SESSION["allowCms"]){ ?>
                                        <li><a href="/tms/dashboard">TMS</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <!--FAQs-->
                        <div class="footer-block col-12 col-sm-2 col-xs-6">
<!--                            <div class="footer-block__title"><p class="footer-title">FAQs</p></div>-->
                            <div class="footer-block__ul">
                                <ul class="footer-ul">
                                    <li><a href="lms/my">FAQs</a></li>
                                </ul>
                            </div>
                        </div>
                        <!--Contact-->
                        <div class="footer-block col-12 col-sm-8 col-xs-6">
<!--                            <div class="footer-block__title"><p class="footer-title">Contact</p></div>-->
                            <div class="footer-block__ul footer-block__address">
                                <ul class="footer-ul">
                                    <li style="margin-top: 1% !important;"><a style="color: #ffffff;">Contact</a></li>
                                </ul>
                                <ul class="nav nav-tabs">
                                    <?php $count = 1; $active = 'active';
                                    foreach ($_SESSION["footerAddressesTab"] as $footerAddressTab) { ?>
                                        <li class="li-address <?php echo $active; ?>"><a data-toggle="tab"
                                                                                         href="#menu<?php echo $count; ?>"><?php echo $footerAddressTab; ?></a>
                                        </li>
                                        <?php $count++; $active=''; }  ?>
                                </ul>
                                <div class="tab-content">
                                    <?php $count = 1;
                                    $active = 'active';
                                    foreach ($_SESSION["footerAddresses"] as $footerAddress) { ?>
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

    </div>
    <?php echo $OUTPUT->footer(); ?>
</div>


<script>
    $(document).ready(function () {
        //tab categories click
        $('.btn-click-course').click(function () {
            var category = $(this).attr('category');
            //add class active
            $(this).addClass('btn-click-active');
            $('.btn-click-course').not($('#ctgr' + category)).each(function () {
                $(this).removeClass(' btn-click-active');
            });
        });

        $('.nav-tabs li').click(function () {
            $('.li-address').removeClass('active');
            $(this).addClass('active');
        });


    });

    Vue.component('v-pagination', window['vue-plain-pagination'])
    var app = new Vue({
        el: '#app',
        data: {
            progress: '<?php echo $progress ?>',
            category: 0,
            txtSearch: '',
            courses: [],
            urlTms: '',
            urlImage: '<?php echo $CFG->wwwtmsbase; ?>',
            typeCourse: '',
            clctgr: true,
            competency_exists: [],
            current: 1,
            totalPage: 0,
            coursesSuggest: [],
            recordPerPage: 9,
            currentCoursesTotal: 0,
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
                this.searchCourse(this.category, this.current);
            },
            searchCourse: function (category, page) {
                var _this = this;
                this.category = category;
                if (page == 1)
                    this.current = 1;
                this.urlTms = 'http://localhost:8888/elearning-easia/public';
                let url = '<?php echo $CFG->wwwroot; ?>';
                const params = new URLSearchParams();
                params.append('category', this.category);
                params.append('txtSearch', this.txtSearch);
                params.append('current', page || this.current);
                // params.append('pageCount', this.total);
                params.append('recordPerPage', this.recordPerPage);
                params.append('progress', this.progress);

                axios({
                    method: 'post',
                    url: url + '/pusher/coursesearch.php',
                    data: params,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                    .then(response => {
                        this.courses = response.data.courses;
                        this.coursesSuggest = response.data.coursesSuggest;
                        this.currentCoursesTotal = this.courses.length;
                        this.totalPage = response.data.totalPage;
                        if (_this.category == 'required' || this.category == 0) {
                            _this.competency_exists = response.data.competency_exists;
                        }
                        activeCategogy(_this.category);
                    })
                    .catch(error => {
                    });
            }
        },
        mounted() {
            this.category = '<?php echo $category_params ?>';
            activeCategogy(this.category);
            this.searchCourse(this.category, this.current);
        }
    })

    function activeCategogy(category_selected) {
        //element
        var needtoclick = $("[category=" + category_selected + "]")
        //add class active
        needtoclick.addClass('btn-click-active');
        $('.btn-click-course').not($('#ctgr' + category_selected)).each(function () {
            $(this).removeClass(' btn-click-active');
        });
    }
</script>


</body>
</html>

<?php
die;
?>
