<?php
    require_once("courselib.php");
    $id = optional_param('id', 0, PARAM_INT);
    $sql = 'SELECT mc.id, mc.fullname, mc.category, mc.course_avatar, mc.estimate_duration, mc.summary, ( SELECT COUNT(mcs.id) FROM mdl_course_sections mcs WHERE mcs.course = mc.id AND mcs.section <> 0) AS numofsections, ( SELECT COUNT(cm.id) AS num FROM mdl_course_modules cm INNER JOIN mdl_course_sections cs ON cm.course = cs.course AND cm.section = cs.id WHERE cs.section <> 0 AND cm.course = mc.id) AS numofmodule, ( SELECT COUNT(cmc.coursemoduleid) AS num FROM mdl_course_modules cm INNER JOIN mdl_course_modules_completion cmc ON cm.id = cmc.coursemoduleid INNER JOIN mdl_course_sections cs ON cm.course = cs.course AND cm.section = cs.id INNER JOIN mdl_course c ON cm.course = c.id WHERE cs.section <> 0 AND cmc.completionstate <> 0 AND cm.course = mc.id AND cmc.userid = '.$USER->id.') AS numoflearned FROM mdl_course mc WHERE mc.id = '.$id;
    $course = array_values($DB->get_records_sql($sql))[0];

    $units = get_course_contents($id);

//    echo json_encode($units);
//    die;

    $bodyattributes = 'id="page-course-view-topics" class="pagelayout-course course-' . $id .'"';

//    $units = json_encode(get_course_contents($id));
//    echo $units;
//    die;

?>

<html>
<title>Thông tin khóa học <?php echo $course->fullname; ?></title>
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
        font-size: 14px !important;
        font-family: Roboto-Bold;
    }

    ul{
        list-style: none;
    }
    a{
        text-decoration: none;
    }
/*    view*/
    .prev-btn:hover{
        cursor: pointer;
    }

    .progress-bar{
        background-color: #862055 !important;
    }

    .progress-info{
        overflow: hidden;
    }

    .progress-info__title span{
        text-align: left;
        letter-spacing: 0.8px;
        color: #202020;
        font-size: 23px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .info-course-detail{
        padding: 0;
    }

    .info-course-detail ul{
        display: inline-flex;
        padding: 0;
        width: 100%;
    }
    .info-course-detail ul li{
        margin-right: 8%;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #737373;
        font-size: 14px !important;
    }

    .info-course-progress > span{
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #202020;
        text-transform: uppercase;
    }

    .progress-info__content{
        /*margin-top: 2%;*/
    }
    .btn-click{
        background: #862055 0% 0% no-repeat padding-box !important;
        border-radius: 4px;
        text-align: left;
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #FFFFFF !important;
        text-transform: uppercase;
    }

    .nav-course .nav{
        width: 30%;
        margin: auto;
    }


    .nav-course .nav .nav-item a{
        text-align: left;
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #737373;
    }

    .nav-course .nav li.active a{
        color: #202020;
    }

    .nav-course .nav .nav-item a.active{
        color: #202020;
    }

    .section-nav{
        border-top: 1px solid #C7C7C7;
        margin: 1% 0;
    }

    .section-course-info{
        background-color: #F1F1F1;
        padding: 2% 0;
    }

    .course-main{
        background-color: #FFFFFF;
        padding: 5%;
    }

    .course-block{
        margin-bottom: 2em;
    }
    .course-block__title{
        font-size: 23px;
        letter-spacing: 0.8px;
        color: #202020;
    }
    .course-block__title p{
        margin-bottom: 0.5em;
    }

    .course-block__content, .course-block__content p, .course-block__content ul li{
        font-family: Roboto-Regular;
        font-size: 13px;
        letter-spacing: 0.99px;
        color: #202020;
        opacity: 1;
        margin: 0;
    }
    .course-block__content ul li{
        list-style: disc;
    }

    .list-outcome{
        padding: 0 15px;
    }

    .course-content{
        display: none;
    }

    #courseunit{
        display: none;
    }

    .main-detail{
        display: none;
        display: none;
    }

    .detail-list li{
        font-family: Roboto-Regular;
        font-size: 14px;
        letter-spacing: 0.99px;
        margin: 2% 0;
    }
    .detail-list li i{
        font-size: 23px;
        margin-right: 1%;
        color: #862055;
    }
    .detail-list li a{
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

    .detail-btn{
        text-align: right;
        width: 100%;
        padding-bottom: 3%;
    }

    .detail-content{
        padding: 2% 0;
        border-top: 1px solid #C7C7C7;
        border-bottom: 1px solid #C7C7C7;
        margin-bottom: 3%;
    }

    .detail-title{
    padding: 2% 0;
    }

    .detail-title p{
        /*font-family: Roboto-Bold;*/
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
        opacity: 1;
        margin: 0;
    }

    .unit{
        border-radius: 4px;
        margin-bottom: 3%;
        position: relative;
        border: 2px solid #FFFFFF;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        overflow: hidden;
    }
    .unit:hover{
        cursor: pointer;
        box-shadow: 3px 3px 6px #00000029;
    }
    .unit-click{
        border: 2px solid #862055;
    }
    .unit-click .unit__title{
        background: #862055 0% 0% no-repeat;
    }
    .unit-click .unit__title p{
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #FFFFFF;
    }
    .unit-click .unit__icon i{
        color: #00A426;
    }

    .unit-learning .unit__title p{
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
    }
    .unit-learning .unit__icon i{
        color: #ff000e;
    }
    .unit__title{
        padding: 2%;
        background-color: #FFFFFF;
    }

    .unit__title p{
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #737373;
    }


    .unit__progress{
        border-top: 1px solid #3333;
    }

    .unit__icon i{
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

    .unit__progress-number{
        padding: 2%;
        color: #737373;
        font-family: Roboto-Regular;
    }
    .unit__progress-number p{
        margin: 0;
    }
    .unit__progress-number i{
        letter-spacing: 0.5px;
        font-weight: 700;
    }
    .percent-get{
        letter-spacing: 0.5px;
    }

    .percent-total{
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
    }
    .unit-detail{
        background-color: #FFFFFF;
        height: fit-content;
    }
    .prev-btn{
        font-size: 23px;
        font-weight: 700;
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 3px 3px 6px #0000002E;
        border: 1px solid #707070;
        border-radius: 4px;
        /*opacity: 0.4;*/
        /*color: #3E3E3E;*/
    }
    .prev-btn i{
        padding: 1%;
        color: #3E3E3E;
        /*opacity: 1;*/
    }

    .course-block__content-answer{
        margin-top: 3%;
    }

    .speech-bubble {
        position: relative;
        background: #862055;
        border-radius: 4px;
        width: 50px;
        padding: 1px 0px;
        /*margin: 0.7em 0;*/
        margin: 0;
        margin-bottom: 1em;
        text-align: center;
        color: white;
        font-weight: bold;
        text-shadow: 0 -0.05em 0.1em rgba(0,0,0,.3);
    }

    .speech-bubble:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 60%;
        width: 0;
        height: 0;
        border: 15px solid transparent;
        border-top-color: #862055;
        border-bottom: 0;
        margin-left: -20px;
        margin-bottom: -10px;
    }

    .progress{
        height: 0.5em !important;
        border-radius: 0 !important;
    }

    .info-course-btn{
        padding: 2% 1%;
    }

    .course-block-img img{
        border-radius: 3%;
    }
    @media only screen and (max-width: 777px) {
        .progress-info, .btn-click {
            font-size: 10px;
        }

        .info-course-progress{
            display: block;
        }

        .unit__title p, .unit-learning .unit__title p, .unit-click .unit__title p{
            font-size: 14px;
        }
        .info-course-detail{
            height: 0 !important;
            line-height: 1 !important;
        }
        .info-course-progress .col-3{
            height: 0 !important;
            line-height: 1 !important;
        }

        #user-notifications .alert-warning{
            opacity: 1 !important;
        }
    }


</style>

<body <?php echo $bodyattributes ?>>

<div class="wrapper"><!-- wrapper -->
    <?php echo $OUTPUT->header(); ?>
    <section class="section section--header"><!-- section -->
        <div class="container">
<!--                progress info-->
           <div class="progress-info">
               <div class="progress-info__title"><span title="<?php echo $course->fullname; ?>"><a class="prev-btn"><i class="fa fa-angle-left" aria-hidden="true"></i></a>  <?php echo $course->fullname; ?></span></div>
               <div class="progress-info__content">
                   <div class="row col-12">
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
                           <a href="" class="btn btn-start-course btn-click">start course</a>
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
                    <li class="nav-item nav-click">
                        <a class="nav-link" data-toggle="tab" href="#courseunit" role="tab">Unit List</a>
                    </li>
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
<!--                        <div class="course-block__title"><p>Course description</p></div>-->
                        <div class="course-block__content">
                            <?php echo $course->summary; ?>
<!--                            <p>Do you usually leave the office late?</p>-->
<!--                            <p>Do you often miss your deadlines?</p>-->
<!--                            <p>Do you feel guilty that you can’t find enough time for your family?</p>-->
<!--                            <p>Do you wish that you had 48 hours in every day to do everything you want to do?</p>-->
<!--                            <p class="course-block__content-answer">If your answer to any of these is yes, then this is your chance to learn how to make time work for you. We invite you to attend the first “Time management” training workshop</p>-->
                        </div>
                    </div>
<!--                    <div class="course-block course-outcome">-->
<!--                        <div class="course-block__title"><p>Learning outcomes</p></div>-->
<!--                        <div class="course-block__content">-->
<!--                            <p>At the end of this training course, you will be able to </p>-->
<!--                            <ul class="list-outcome">-->
<!--                                <li>Prioritize</li>-->
<!--                                <li>Schedule your day</li>-->
<!--                                <li>And stay focused to shorten working time</li>-->
<!--                                <li>Increase productivity</li>-->
<!--                            </ul>-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
                <div class="col-4 course-block-img">
                    <img src="/elearning-easia/public<?php echo $course->course_avatar; ?>" alt="">
                </div>
            </div>


            <div class="row col-12 course-content" id="courseunit">
                <div class="col-5 unit-info">
                    <div class="list-units">
                        <?php foreach ($units as $unit) {  ?>
                            <div class="unit" id="unit_<?php echo $unit['id']; ?>">
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
                                    <ul class="detail-list">
                                        <?php foreach ($unit['modules'] as $module) {  ?>
                                            <li><a href="<?php echo $module['url'] ?>"><?php echo $module['name']; ?></a> </li>
                                        <?php } ?>
                                    </ul>
                                </div>

                                <div class="detail-btn">
                                    <a href="<?php echo $unit['modules'][0]['url']; ?>" class="btn btn-click btn-start-unit">Start unit</a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
            </div>
        </div>
    </section>
    <?php echo $OUTPUT->footer(); ?>
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
        });

        $(".nav-tabs-courses a").click(function(){
            var getId =  $(this).attr('href');
            $('.course-content').not($(getId)).each(function(){
                $(this).css('display', 'none');
            });
            $(getId).css('display', 'flex');
        });

        var getPercent = $('.progress-bar').attr('aria-valuenow');
        var marginLeft = getPercent - 7;
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
    });


</script>
<script>
    $(document).ready(function() {
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
                url:'/elearning-easia/public/lms/pusher/resume.php',
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
    });
</script>

</body>
</html>


<?php
die;
?>
