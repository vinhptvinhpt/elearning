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
<title>Thông tin người dùng</title>
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
        font-family: Nunito-Sans-Bold;
        src: url('fonts/NunitoSans-Bold.ttf');
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
        font-family: Roboto-Italic;
        src: url('fonts/Roboto-Italic.ttf');
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
    ul li{
        list-style: none;
    }
    a{
        text-decoration: none;
    }
    .clear-fix{
        clear: both;
    }

/*    View*/
    .info-user{
        background-color: #FFFFFF;
        width: 25%;
        padding: 3% 1% 1% 6%;
        font-size: 13px;
        float: left;
    }

    .avatar{
        width: 35%;
    }
    .address{
        margin: 25px 0;
    }

    .address p{
        letter-spacing: 0.45px;
        color: #202020;
        text-transform: uppercase;
        margin-bottom: auto;
    }

    .address-detail{
        font-family: Roboto-Italic;
        text-transform: unset !important;
    }

    .info-detail ul{
    padding: 0;
    }

    .info-detail ul li{
        font-family: Roboto-Regular;
    }

    .info-learn{
        float: left;
        width: 70%;
    }

    .block{
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 0px 3px 6px #00000029;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    .title{
        background: #862055 0% 0% no-repeat padding-box;
        border-radius: 10px 10px 0px 0px;
    }
    .title p{
        text-align: center;
        letter-spacing: 0.8px;
        color: #FFFFFF;
        text-transform: uppercase;
        font-size: 23px;
    }

    .table thead th{
        vertical-align: middle;
        border: none;
    }
    .borderless td, .borderless th {
        border: none;
        font-size: 13px;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        font-weight: 300;
        padding: .5rem;
    }
    .course-select{
        background: #E4E4E4 0% 0% no-repeat padding-box;
        border-radius: 4px;
        padding: .375em;
        width: 100%;
    }

    .icon-circle{
        color: #737373;
        font-size: 16px !important;
        text-align: center;
    }

    .icon-circle-green{
        color: #00EB37 !important;
    }
    .width10{
        width: 10%;
    }

    .numberget{
        font-family: Nunito-Sans-Bold;
        letter-spacing: 1.1px;
        color: #862055;
    }
    .numberhave{
        color: #737373;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 1.1px;
    }
    .nav-tabs .nav-link{
        font-size: 23px;
        letter-spacing: 0.8px;
        color: #737373;
        text-transform: uppercase;
        border: 0;
    }
    .nav-link:hover{
        color: #202020;
    }
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        border: 0;
    }
    .nav-tabs, .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active, .nav-tabs .nav-link:focus, .nav-tabs .nav-link:hover{
        border: none;
    }
    .nav-tabs .active a{
        color: #202020;
    }


    .item-image{
        box-shadow: 3px 3px 6px #0000004D;
        border-radius: 10px;
        max-height: 185px;
        max-width: 185px;
    }

    .item-content{
        text-align: center;
        margin-top: 10%;
        padding: 0;
    }
    .item-content__name{
        font-family: Roboto-Regular;
        font-size: 17px;
        letter-spacing: 0.6px;
        color: #202020;
        margin: 0;
    }
    .item-content__date{
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


    .progress-note ul{
        padding: 0;
    }


    .progress-note ul li{
        list-style: none;
        padding: 5% 0;
        font-size: 14px;
    }
    .block-note{
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-flex;
    }

    .progress{
        height: .75rem;
        border-radius: 0;
        padding: 0;
    }
    .progress-bar{
        background-color: #862055;
    }

    .progress-number span{
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        line-height: 13px;
    }

    .block-progress{
        margin-bottom: 10px;
    }

    .block-progress p{
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #202020;
        margin: 0;
    }
</style>
<body>
<!--<div id="container1" style="min-width: 300px; height: 400px; margin: 0 auto"></div>-->
<?php
?>
<div class="wrapper"><!-- wrapper -->

    <!--    body-->
    <section class="section section-content">
        <div class="info-user">
            <div class="avatar"><img src="images/avatar.png" alt=""></div>
            <div class="address">
                <p>Van Anh Tran</p>
                <p class="address-detail">Vietnam, Hanoi, Hoan Kiem</p>
            </div>
            <div class="info-detail">
                <ul>
                    <li>Position: Sales Senior Manager</li>
                    <li>Department: Sales</li>
                    <li>Experience: 3 years</li>
                    <li>Line Manager: Doan Thi Thanh Loan</li>
                    <li>Company: Easia Travel</li>
                </ul>
            </div>
        </div>
        <div class="info-learn">
            <div class="container">
                <div class="block overall">
                    <div class="title"><p>Overall</p></div>
                    <div class="block-content">
                        <div class="row col-12">
                            <div class="col-6 row">
                               <div class="col-6">
                                   <div>
                                       <svg viewBox="0 0 36 36" width="150" class="circular-chart">

                                           <path class="that-circle" stroke="#862055" stroke-dasharray="0,100" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />

                                           <path class="that-circle" stroke="#FFC400" stroke-dasharray="0,100"  d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />

                                           <path class="that-circle" stroke="#C7C7C7" stroke-dasharray="100,100" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />
                                           <text x="18" y="20.35" class="percentage">0%</text>
                                       </svg>
                                   </div>
                               </div>
                                <div class="col-6">
                                    <div class="progress-note">
                                        <ul>
                                            <li><div class="block-note" style="background-color: #862055"></div> Completed</li>
                                            <li><div class="block-note" style="background-color: #FFC400"></div> Studying</li>
                                            <li><div class="block-note" style="background-color: #C7C7C7"></div> Not yet learned</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="row block-progress">
                                    <p>Required Courses</p>
                                    <div class="progress col-11">
                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="col-1 progress-number"><span>70/123</span></div>
                                </div>
                                <div class="row block-progress">
                                    <p>Current Courses</p>
                                    <div class="progress col-11">
                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="col-1 progress-number"><span>110/212</span></div>
                                </div>
                                <div class="row block-progress">
                                    <p>Optional Courses</p>
                                    <div class="progress col-11">
                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="col-1 progress-number"><span>12/15</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block courses">
                    <div class="title"><p>your courses</p></div>
                    <div class="block-content">
                        <table class="table borderless">
                            <thead>
                            <tr>
                                <th scope="col">
                                    <select name="" id="" class="course-select">
                                        <option value="">All course</option>
                                        <option value="">Training by department</option>
                                        <option value="">Job skills courses</option>
                                        <option value="">Soft skills courses</option>
                                        <option value="">Client courses</option>
                                        <option value="">Language courses</option>
                                    </select>
                                </th>
                                <th scope="col">Progress</th>
                                <th scope="col">Test</th>
                                <th scope="col" class="width10">Qualified</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th><a href="">Product Core Values</a></th>
                                <td><span class="numberget">9</span>/<span class="numberhave">9</span></td>
                                <td><span class="numberget">100</span>/<span class="numberhave">100</span></td>
                                <td class="icon-circle icon-circle-green"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            </tr>
                            <tr>
                                <th><a href="">Market Segment</a></th>
                                <td><span class="numberget">9</span>/<span class="numberhave">9</span></td>
                                <td><span class="numberget">100</span>/<span class="numberhave">100</span></td>
                                <td class="icon-circle icon-circle-green"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            </tr>
                            <tr>
                                <th><a href="">Overall Sales Working Process</a></th>
                                <td><span class="numberget">9</span>/<span class="numberhave">9</span></td>
                                <td><span class="numberget">100</span>/<span class="numberhave">100</span></td>
                                <td class="icon-circle"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            </tr>
                            <tr>
                                <th><a href="">Synery with other departments</a></th>
                                <td><span class="numberget">9</span>/<span class="numberhave">9</span></td>
                                <td><span class="numberget">100</span>/<span class="numberhave">100</span></td>
                                <td class="icon-circle"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            </tr>
                            <tr>
                                <th><a href="">Market Segment</a></th>
                                <td><span class="numberget">9</span>/<span class="numberhave">9</span></td>
                                <td><span class="numberget">100</span>/<span class="numberhave">100</span></td>
                                <td class="icon-circle icon-circle-green"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            </tr>
                            <tr>
                                <th><a href="">Product Core Values</a></th>
                                <td><span class="numberget">9</span>/<span class="numberhave">9</span></td>
                                <td><span class="numberget">100</span>/<span class="numberhave">100</span></td>
                                <td class="icon-circle"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="block certificate-badge">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#certificate">certificate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#badge">badge</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div id="certificate" class="tab-pane active">
                            <br/>
                            <div class="row col-12">
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="item-image">
                                        <img src="images/f238867fec8cf8586683a9d563b50e1e.png" alt="">
                                    </div>
                                    <div class="item-content">
                                        <p class="item-content__name">Certificate name</p>
                                        <p class="item-content__date">2020/04/25</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="badge" class="container tab-pane fade">
                            <div id="certificate" class="tab-pane active">
                                <br/>
                                <div class="row col-12">
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="item-image">
                                            <img src="images/badge.png" alt="">
                                        </div>
                                        <div class="item-content">
                                            <p class="item-content__name">Certificate name</p>
                                            <p class="item-content__date">2020/04/25</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="clear-fix"></div>
    </section>

</div>

<script>
    $(document).ready(function(){
        $(".nav-tabs a").click(function(){
            var getHref = $(this).attr('href').replace('#', '');
            $(getHref).addClass('active');
        });
    });

</script>
</body>
</html>


<?php
die;
?>
