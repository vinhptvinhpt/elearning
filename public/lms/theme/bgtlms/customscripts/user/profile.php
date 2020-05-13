<?php
require_once(__DIR__ . '/../../../../config.php');

$sqlGetCategories = 'select id, name from mdl_course_categories';
$categories = array_values($DB->get_records_sql($sqlGetCategories));

$sqlGetCertificates = 'select tms_traninning_programs.name as name, student_certificate.timecertificate as timecertificate, student_certificate.code as code from student_certificate join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 and tms_traninning_programs.auto_certificate = 1 and student_certificate.userid = '.$USER->id;
$certificates = array_values($DB->get_records_sql($sqlGetCertificates));

$sqlGetBadges = 'select tms_traninning_programs.name as name, student_certificate.timecertificate as timecertificate, student_certificate.code as code from student_certificate join tms_traninning_programs on tms_traninning_programs.id = student_certificate.trainning_id where student_certificate.status = 1 and tms_traninning_programs.auto_badge = 1 and student_certificate.userid = '.$USER->id;
$badges = array_values($DB->get_records_sql($sqlGetBadges));

session_start();
$percent = intval(count($_SESSION["courses_completed"])*100/$_SESSION["totalCourse"]);
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
    @font-face {
        font-family: Nunito-Bold;
        src: url('fonts/Nunito-Bold.ttf');
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

/*    paging*/
    .pagination{
        margin: 0 auto;
        padding: 1%;
    }
    .pagination li{
        margin: 0% 5% !important;
    }
    .pagination li button{
        background: #FFFFFF 0% 0% no-repeat padding-box;
        border-radius: 4px;
        font-family: Nunito-Bold;
        letter-spacing: 0.45px;
        color: #737373;
    }
    .page-item.active .page-link{
        background: #862055 0% 0% no-repeat padding-box !important;
        border-color: #862055 !important;
    }

    .table-select, .tr-title{
        max-width: 200px !important;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /*    View*/

    .col-6.block-content6{
        margin-top: 10px;
    }

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
        text-transform: capitalize;
    }

    .info-detail ul li p{
        margin: 0;
        display: contents;
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
        margin: 0;
    }

    .table thead th{
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
    .course-select{
        background: #E4E4E4 0% 0% no-repeat padding-box;
        border-radius: 4px;
        padding: .375em;
        width: 100%;
        border:none;
        font-size: 13px;
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
        max-height: 300px;
        max-width: 185px;
    }

    .item-image img{
        /*max-height: 134px;*/
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
        border-radius: 0 !important;
        padding: 0 !important;
    }
    .progress-bar{
        background-color: #862055 !important;
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
        width: 100%;
    }

    .percentage{
        font-family: Roboto-Regular;
        fill: #862055;
    }

    .block-content{
        width: 100%;
    }


    /*custom*/
    #region-main{
        font-size: 14px;
        font-family: Roboto-Bold;
        background-color: #f1f3f9 !important;
        /*background-color: #F1F1F1 !important;*/
        border: none !important;
    }

    /*.table-keep thead th {*/
    /*    !*position: sticky;*!*/
    /*    top: 0;*/
    /*}*/

    /*.table-keep {*/
    /*    display: flex;*/
    /*    flex-flow: column;*/
    /*    width: 100%;*/
    /*    overflow-y: auto;*/
    /*    height: 200px;*/
    /*    min-height: 200px;*/
    /*}*/

    /*.table-keep thead {*/
    /*    flex: 0 0 auto;*/
    /*}*/

    /*.table-keep tbody {*/
    /*    flex: 1 1 auto;*/
    /*    display: block;*/
    /*    overflow-y: auto;*/
    /*    overflow-x: hidden;*/
    /*}*/

    /*.table-keep tr {*/
    /*    width: 100%;*/
    /*    display: table;*/
    /*    table-layout: fixed;*/
    /*}*/


    /*Ipad ngang(1024 x 768)*/
    @media screen and (max-width: 1024px){

    }
    /*Ipad dọc(768 x 1024)*/
    @media screen and (max-width: 768px){
        .info-user{
            padding-left: 25%;
            margin-bottom: 15px;
        }
        .info-user, .info-learn{
            width: 100%;
        }

        .block-content12{
            display: block;
        }

        .block-content6{
            max-width: 96%;
        }
    }
    /*Tablet nhỏ(480 x 640)*/
    @media screen and (max-width: 480px){


        .item-content p{
            font-size: 10px;
        }
    }
    /*Iphone(480 x 640)*/
    @media screen and (max-width: 320px){

    }
    /*Smart phone nhỏ*/
    @media screen and (max-width: 240px){

    }
</style>
<body>
<!--<div id="container1" style="min-width: 300px; height: 400px; margin: 0 auto"></div>-->
<?php
?>
<div class="wrapper" ><!-- wrapper -->
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
                        <li>Position: {{ user.position }}</li>
                        <li>Department: {{ user.departmentname }}</li>
                        <li v-if="user.yearworking > 0">Experience: {{linemanager.yearworking}} years</li>
                        <li v-else>Experience: Under 1 year</li>
                        <li>Line Manager: <p v-for="(linemanager, index) in linemanagers"><span>{{linemanager.fullname}} </span></p></li>
                        <li>Company: Easia Travel</li>
                    </ul>
                </div>
            </div>
            <div class="info-learn">
                <div class="container">
                    <div class="block overall">
                        <div class="title"><p>Overall</p></div>
                        <div class="block-content">
                            <div class="row col-12 block-content12">
                                <div class="col-6 row block-content6">
                                    <div class="col-6">
                                        <div>
                                            <svg viewBox="0 0 36 36" width="150" class="circular-chart">
                                                <path class="that-circle" stroke="#C7C7C7" stroke-dasharray="100,100" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                <path class="that-circle" stroke="#FFC400" stroke-dasharray="0,100"  d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                <path class="that-circle" stroke="#862055" stroke-dasharray="<?php echo $percent; ?>,100" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" />




                                                <text x="18" y="20.35" class="percentage"><?php echo $percent; ?>%</text>
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

                                <div class="col-6 block-content6">
                                    <div class="row block-progress">
                                        <p>Current Courses</p>
                                        <div class="progress col-10">
                                            <div class="progress-bar progress-current" role="progressbar" style="width: 0%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="col-1 progress-number"><span>{{ progressCurrentCourse }}</span></div>
                                    </div>
                                    <div class="row block-progress">
                                        <p>Required Courses</p>
                                        <div class="progress col-10">
                                            <div class="progress-bar progress-required" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="col-1 progress-number"><span>{{ progressRequiredCourse }}</span></div>
                                    </div>

                                    <!--                                <div class="row block-progress">-->
                                    <!--                                    <p>Optional Courses</p>-->
                                    <!--                                    <div class="progress col-11">-->
                                    <!--                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="col-1 progress-number"><span>12/15</span></div>-->
                                    <!--                                </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block courses">
                        <div class="title"><p>your courses</p></div>
                        <div class="block-content table-responsive">
                            <table class="table borderless table-keep">
                                <thead>
                                <tr>
                                    <th scope="col" class="table-select">
                                        <select name="category" id="category" class="course-select" @change="searchCourse(category, 1)"
                                                v-model="category">
                                            <option value="0">All course</option>
                                            <?php foreach ($categories as $category) { ?>
                                                <option
                                                    value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </th>
                                    <th scope="col">Percent</th>
                                    <th scope="col">Point</th>
                                    <th scope="col" class="width10">Qualified</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(course,index) in courses">
                                    <th class="tr-title"><a :href="'lms/course/view.php?id='+course.id" :title="course.fullname">{{ course.fullname }}</a></th>
                                    <td v-if="course.id == 506"><span class="numberget">100</span></td>
                                    <td v-else-if="course.numofmodule == 0"><span class="numberget">0</span></td>
                                    <td v-else><span class="numberget">{{ Math.floor(course.numoflearned*100/course.numofmodule) }}</span></td>
                                    <td v-if="course.finalgrade == null"><span class="numberhave">0</span></td>
                                    <td v-else><span class="numberhave">{{ course.finalgrade }}</span></td>
                                    <td class="icon-circle" v-if="course.id == 506"><i class="fa fa-check-circle icon-circle-green" aria-hidden="true"></i></td>
                                    <td class="icon-circle" v-else-if="course.numofmodule == 0 || course.numoflearned/course.numofmodule == 0 || course.numoflearned/course.numofmodule > 0 || course.numoflearned/course.numofmodule < 1"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                                    <td class="icon-circle" v-else><i class="fa fa-check-circle icon-circle-green" aria-hidden="true"></i></td>
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
                                    <?php foreach ($certificates as $certificate) { ?>
                                        <div class="col-3">
                                            <div class="item-image">
                                                <img src="/elearning-easia/public/storage/upload/certificate/<?php echo $certificate->code; ?>_certificate.png" alt="">
                                            </div>
                                            <div class="item-content">
                                                <p class="item-content__name"><?php echo $certificate->name; ?></p>
                                                <p class="item-content__date"><?php echo date('m/d/Y', $certificate->timecertificate); ?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div id="badge" class="container tab-pane fade">
                                <div id="certificate" class="tab-pane active">
                                    <br/>
                                    <div class="row col-12">
                                        <?php foreach ($badges as $badge) { ?>
                                            <div class="col-3">
                                                <div class="item-image">
                                                    <img src="/elearning-easia/public/storage/upload/certificate/<?php echo $badge->code; ?>_badge.png" alt="">
                                                </div>
                                                <div class="item-content">
                                                    <p class="item-content__name"><?php echo $badge->name; ?></p>
                                                    <p class="item-content__date"><?php echo date('m/d/Y', $badge->timecertificate); ?></p>
                                                </div>
                                            </div>
                                        <?php } ?>
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

    <?php echo $OUTPUT->footer(); ?>
</div>

<script>
    $(document).ready(function(){
        // $(".nav-tabs a").click(function(){
        //     var getHref = $(this).attr('href').replace('#', '');
        //     $(getHref).addClass('active');
        // });
    });

    Vue.component('v-pagination', window['vue-plain-pagination'])
    var app = new Vue({
        el: '#app',
        data: {
            category: 0,
            txtSearch: '',
            courses: [],
            totalCourse: 0,
            requiredCourse: 0,
            currentCourse: 0,
            linemanagers: [],
            user: {},
            urlTms: '',
            clctgr: true,
            progressRequiredCourse: '0/0',
            progressCurrentCourse: '0/0',
            url: '<?php echo $CFG->wwwroot; ?>',
            user_id: <?php echo $USER->id; ?>,
            current: 1,
            totalPage: 0,
            recordPerPage: 3,
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
            }
        },
        methods: {
            onPageChange: function(){
                this.searchCourse(this.category, this.current);
            },
            searchCourse: function (category, page) {
                this.category = category || this.category;
                if(page == 1)
                    this.current = 1;
                this.urlTms = 'http://localhost:8888/elearning-easia/public';
                const params = new URLSearchParams();
                params.append('category', category);
                params.append('txtSearch', this.txtSearch);
                params.append('current', page || this.current);
                // params.append('pageCount', this.total);
                params.append('recordPerPage', this.recordPerPage);

                axios({
                    method: 'post',
                    url: this.url + '/coursesearch.php',
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
            getProfile: function(){
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
                        console.log(response.data);
                        this.user = response.data.profile;
                        this.linemanagers = response.data.linemanagers;

                        //set progress
                        this.progressCurrentCourse = response.data.currentcourses.length + "/"+response.data.totalCourse;
                        this.progressRequiredCourse = response.data.requiredcourses.length + "/"+response.data.totalCourse;

                        //
                        $('.progress-current').css('width', response.data.currentcourses.length*100/response.data.totalCourse+'%');
                        $('.progress-required').css('width', response.data.requiredcourses.length*100/response.data.totalCourse+'%');


                    })
                    .catch(error => {
                        console.log("Error ", error);
                    });
            }
        },
        mounted() {
            this.searchCourse();
            this.getProfile();
        }
    })

</script>
</body>
</html>


<?php
die;
?>
