<html>
<title>Danh sách các khóa học</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

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

    img{
        width: 100%;
    }

    body {
        font-size: 14px;
        font-family: Roboto-Bold;
        background-color: #F1F1F1;
    }

    ul{
        list-style: none;
    }
    a{
        text-decoration: none;
    }
    a:hover{
        text-decoration: none;
        opacity: 0.5;
    }

/*    view*/
    .course-info__detail{
        padding: 5% 0;
    }

    .course-info__detail ul{
        display: inline-flex;
        padding: 0;
        width: 100%;
    }
    .course-info__detail ul li{
        margin-right: 8%;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #737373;
    }

    .course-info__list-lessons{

    }

    .course-info__list-lessons ul{
        padding: 5% 0;
        padding-top: 0;
    }


    .course-info__list-lessons ul li{
        /*display: -webkit-box;*/
        /*-webkit-line-clamp: 1;*/
        /*-webkit-box-orient: vertical;*/
        /*overflow: hidden;*/
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

    .course-info__list-lessons ul li a{
        display: inline-flex;
        width: 100%;
    }


    .course-info__list-lessons ul li a span{
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        width: 90%;
    }


    .btn-click{
        background: #862055 0% 0% no-repeat padding-box;
        border-radius: 4px;
        opacity: 1;
    }
    .btn-click a{
        text-align: left;
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #FFFFFF;
        text-transform: uppercase;
        opacity: 1;
        /*font-family: Roboto;*/
    }

    .percent{
        width: 90px;
        height: 84px;
        transform: matrix(-0.91, 0.42, -0.42, -0.91, 0, 0);
        background: transparent linear-gradient(132deg, #862055 0%, #A30088 100%) 0% 0% no-repeat padding-box;
        opacity: 0.59;
    }

    .course-info__title a{
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
        opacity: 1;
    }

    .course-info__title{
        padding: 2% 0;
    }

    .btn-page{
        position: absolute;
        bottom: -8%;
        right: 0;
    }

    .block{
        padding-bottom: 4%;
        max-width: 48%;
    }

    .block-first{
        margin-right: 4%;
    }

    .course-block{
        background-color: #ffffff;
        position: relative;
        padding-left: 0;
        box-shadow: 3px 3px 6px #00000029;
    }

    .course-block__image{
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    .course-block__image img{

    }

    .section--header{
        background-image: url('images/course/list/bg.png');
        width: 100%;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        padding-bottom: 1%;
    }

    .header-block__logo img{
        width: 25%;
        padding: 4% 0;
    }

    .header-block__search__title p{
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #202020;
        font-size: 13px;
    }


    .header-block__search__title .title-header{
        font-family: Roboto-Bold;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        color: #202020;
        font-size: 36px;
        padding-top: 2%;
    }

    .header-block__quick-filter__title p{
        font-family: Roboto-Bold;
        letter-spacing: 0.9px;
        color: #202020;
        font-size: 32px;
        padding: 2% 0;
    }

    .course-select, .input-search{
        font-size: 13px;
        letter-spacing: 0.45px;
        color: #3E3E3E;
        font-family: Roboto-Regular;
        border: 1px solid #707070;
        border-radius: inherit;
    }

    .input-search{
    padding: 2%;
        border-right: 0;
    }
    .block-search__select{
        padding-right: 0;
    }

    .block-search__btn{
    padding-left: 5px;
        display: flex;
    }

    .btn-seach{
        border: 1px solid #707070;
        border-left: 0;
    }

    .btn-seach i{
        position: absolute;
        color: #FFFFFF;
        top: 25%;
        padding: 3px 10px;
    }

    .btn-seach input{
        background-color: #A30088;
        width: 100%;
        border: 2px solid #FFFFFF;
        padding: 6px 15px;
    }
    .btn-seach input:hover, .btn-seach i:hover{
        cursor: pointer;
    }

    .btn-click-course{
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 3px 3px 6px #00000029;
        border: 1px solid #C7C7C7;
        border-radius: 4px;
        font-family: Nunito-Sans-Regular;
        font-size: 13px;
        color: #737373;
        letter-spacing: 0.45px;
        margin-right: 2%;
        min-width: 135px;
    }

    .btn-click-active{
        background: transparent linear-gradient(97deg, #A30088 0%, #862055 100%) 0% 0% no-repeat padding-box;
        color: #FFFFFF;
    }

    .block-search, .header-block__quick-filter__main ul{
        padding-left: 0;
    }

    .section-course-info{
        margin-top: 3%;
    }

    /*footer*/
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
        font-size: 13px;
    }
    .footer-title{
        text-align: left;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 0.6px;
        color: #FFFFFF;
        opacity: 1;
        font-size: 17px;
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
    /*.course-info__list-lessons ul li a{*/
    /*    */
    /*}*/
</style>
<body>

<div class="wrapper"><!-- wrapper -->
    <section class="section section--header"><!-- section -->
        <div class="container">
            <div class="header-block">
                <div class="header-block__logo">
                    <img src="images/logo-black-course.png" alt="">
                </div>
                <div class="header-block__search">
                    <div class="header-block__search__title">
                        <p class="title-header">Available courses</p>
                        <p>Search your target courses here</p>
                    </div>
                    <div class="header-block__search__btn-search">
                        <div class="row col-12 block-search">
                            <div class="col-2 block-search__select">
                                <select name="" id="" class="form-control course-select">
                                    <option value="">All course</option>
                                    <option value="">Training by department</option>
                                    <option value="">Job skills courses</option>
                                    <option value="">Soft skills courses</option>
                                    <option value="">Client courses</option>
                                    <option value="">Language courses</option>
                                </select>
                            </div>
                            <div class="col-5 block-search__btn">
                                <input type="text" class="form-control input-search">
                                <div class="btn-seach"><i class="fa fa-search" aria-hidden="true"></i><input type="button"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-block__quick-filter">
                    <div class="header-block__quick-filter__title"><p>Quick Filter</p></div>
                    <div class="header-block__quick-filter__main">
                        <ul>
                            <li class="btn btn-click-course btn-click-active">All Course</li>
                            <li class="btn btn-click-course">Trainings by department</li>
                            <li class="btn btn-click-course">Job skills courses</li>
                            <li class="btn btn-click-course">Soft skills courses</li>
                            <li class="btn btn-click-course">Client courses</li>
                            <li class="btn btn-click-course">Language courses</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!--    body-->
    <section class="section section-content section-course-info">
        <div class="container">
            <div class="col-12 row">

                <div class="col-6 block block-first">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
<!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
<!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 block">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
                                        <!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 block block-first">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
                                        <!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 block">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
                                        <!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 block block-first">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
                                        <!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 block">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
                                        <!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 block block-first">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
                                        <!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 block">
                    <div class="row col-12 course-block">
                        <div class="col-4 course-block__image" style="background-image: url('images/course/list/01.png')">
                        </div>
                        <div class="col-8">
                            <div class="course-info">
                                <div class="course-info__title">
                                    <a href="">Time management</a>
                                </div>
                                <div class="course-info__detail">
                                    <ul>
                                        <li class="teacher"><i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc</li>
                                        <li class="units"><i class="fa fa-file" aria-hidden="true"></i> 5 Units</li>
                                        <li class="units"><i class="fa fa-clock-o" aria-hidden="true"></i> 2 hours</li>
                                    </ul>
                                </div>
                                <div class="course-info__list-lessons">
                                    <ul>
                                        <!--                                        <li><a href=""><span>Lesson 1: Introduction</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <!--                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span> <i class="fa fa-file" aria-hidden="true"></i></a></li>-->
                                        <li><a href=""><span>Lesson 1: Introduction</span></a></li>
                                        <li><a href=""><span>Lesson 2: Skill to write minutes on the board</span></a></li>
                                    </ul>
                                </div>
                                <div class="btn-show btn-show-all btn-page">
                                    <button class="btn btn-click"><a href="">Learn more</a></button>
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
    $(document).ready(function(){

    });
</script>
</body>
</html>


<?php
die;
?>
