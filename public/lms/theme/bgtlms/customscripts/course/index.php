<?php
require_once(__DIR__ . '/../../../../config.php');
$type = optional_param('type', 0, PARAM_INT);
if($type != 0){

}
$sqlGetCategories = 'select id, name from mdl_course_categories';
$categories = array_values($DB->get_records_sql($sqlGetCategories));
?>

<html>
<title>Danh sách các khóa học</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="../../">
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
    img {
        width: 100%;
    }

    body {
        font-size: 14px !important;
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

    /*    view*/
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
    .course-info__detail {
        padding: 5% 0;
    }

    .course-info__detail ul {
        padding: 0;
        width: 100%;
    }

    .course-info__detail ul li {
        margin-right: 8%;
        font-family: Roboto-Regular;
        letter-spacing: 0.5px;
        color: #737373;
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
        height: 3.25rem;
        font-family: Roboto-Bold !important;
    }

    .course-info__title {
        padding: 2% 0;
    }

    .btn-page {
        text-align: right;
        padding-bottom: 15px;
    }

    .block {
        padding-bottom: 4%;
        max-width: 48%;
        padding-right: 0 !important;
    }

    .block-first {
        margin-right: 4%;
    }

    .course-block {
        background-color: #ffffff;
        position: relative;
        padding: 0 !important;;
        box-shadow: 3px 3px 6px #00000029;
        margin: 0 !important;
    }

    .course-block__image {
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        min-height: 150px;
        padding: 0 !important;
        position: relative;
    }

    .course-block__image img{
        width: 32%;
        height: 26%;
        position: absolute;
        top: 3%;
        right: 3%;
    }

    .course-block__image span{
        font-size: 13px;
        font-family: Nunito-Bold;
        color: #FFFFFF;
        position: absolute;
        top: 11%;
        right: 8%;
        letter-spacing: 1px;
    }

    .section--header {
        background-image: url('images/course/list/bg.png');
        width: 100%;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        padding-bottom: 1%;
    }

    .header-block__logo img {
        width: 25%;
        padding: 4% 0;
    }

    .header-block__search__title p {
        font-family: Roboto-Regular;
        letter-spacing: 0.45px;
        color: #202020;
        font-size: 13px;
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

    .course-select, .input-search {
        font-size: 13px !important;
        letter-spacing: 0.45px !important;
        color: #3E3E3E !important;
        font-family: Roboto-Regular;
        border: 1px solid #707070 !important;
        border-radius: inherit !important;
    }

    .input-search {
        padding: 2%;
        border-right: 0 !important;
        width: 85%;
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
        border: 1px solid #707070;
        border-left: 0;
    }

    .btn-search i {
        position: absolute;
        color: #FFFFFF;
        top: 25%;
        padding: 3px 10px;
    }

    .btn-search input {
        background-color: <?=$_SESSION["color"]?>;
        width: 100%;
        border: 2px solid #FFFFFF;
        padding: 5px 15px;
    }

    .btn-search input:hover, .btn-search i:hover {
        cursor: pointer;
    }

    .btn-click-course {
        background: #FFFFFF 0% 0% no-repeat padding-box;
        box-shadow: 3px 3px 6px #00000029;
        border: 1px solid #C7C7C7;
        border-radius: 4px;
        font-family: Nunito-Bold;
        font-size: 13px !important;
        color: #737373 !important;
        letter-spacing: 0.45px;
        margin-right: 2%;
        min-width: 135px;
    }

    .btn-click-active {
        background: transparent linear-gradient(97deg, <?=$_SESSION["color"]?> 0%, <?=$_SESSION["color"]?> 100%) 0% 0% no-repeat padding-box;
        color: #FFFFFF !important;
    }

    .block-search, .header-block__quick-filter__main ul {
        padding-left: 0;
    }

    .section-course-info {
        margin-top: 3%;
    }

    /*footer*/
    .section-footer {
        background: #202020 0% 0% no-repeat padding-box;
        border: 1px solid #707070;
        opacity: 1;
        padding: 1%;
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
        font-size: 13px;
    }

    .footer-title {
        text-align: left;
        font-family: Nunito-Sans-Regular;
        letter-spacing: 0.6px;
        color: #FFFFFF;
        opacity: 1;
        font-size: 17px;
    }

    .footer-logo {
        height: 11%;
    }

    .footer-logo img {
        width: 15%;
        position: absolute;
    }

    .footer-full {
        padding-bottom: 1%;
        margin-top: 5%;
    }

    .nodata{

    }

    /*1920*/
    @media screen and (max-width: 1920px) and (min-width: 1369px){
        .btn-click-course{
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

    @media screen and (max-width: 1368px){
        .drawer-open-left .over-wrap{
            opacity: 0 !important;
            display: none;
        }
        .btn-click-course{
            margin-top: 2%;
        }
    }

    @media screen and (max-width: 1024px){
        .drawer-open-left .over-wrap{
            opacity: 0 !important;
            display: none;
        }
        .btn-click-course{
            margin-top: 2%;
        }
    }


    @media only screen and (max-width: 768px) {
        .drawer-open-left .over-wrap{
            opacity: 0 !important;
            display: none;
        }
        .block{
            display: contents;
        }
        .btn-click-course{
            margin-top: 2%;
        }
        .course-block {
            margin: 1% 0 !important;
        }
        .section-course-info {
            margin-bottom: 5%;
        }
    }

    @media only screen and (max-width: 480px) {
        .drawer-open-left .over-wrap{
            opacity: 0 !important;
            display: none;
        }
        .block{
            display: contents;
        }
    }


    @media only screen and (max-width: 320px) {
        .drawer-open-left .over-wrap{
            opacity: 0 !important;
            display: none;
        }
        .block{
            display: contents;
        }
    }

</style>
<body>
<div class="wrapper"><!-- wrapper -->
    <?php echo $OUTPUT->header(); ?>
    <div id="app">
        <section class="section section--header"><!-- section -->
            <div class="container">
                <div class="header-block">
                    <div class="header-block__logo">
                        <img src="<?php echo $_SESSION["pathLogo"]; ?>" alt="">
                    </div>
                    <div class="header-block__search">
                        <div class="header-block__search__title">
                            <p class="title-header">Available courses</p>
                            <p>Search your target courses here</p>
                        </div>
                        <div class="header-block__search__btn-search">
                            <div class="row col-12 block-search">
                                <div class="col-2 block-search__select">
                                    <select name="category" id="category" class="form-control course-select" @change="searchCourse(category, 1)"
                                            v-model="category">
                                        <option value="0">All course</option>
                                        <?php foreach ($categories as $category) { ?>
                                            <option
                                                value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-5 block-search__btn">
                                    <input type="text" class=" input-search" v-model="txtSearch">
                                    <div class="btn-search" @click="searchCourse(category, 1)"><i class="fa fa-search"
                                                                                                 aria-hidden="true"></i><input
                                            type="button"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header-block__quick-filter">
                        <div class="header-block__quick-filter__title"><p>Quick Filter</p></div>
                        <div class="header-block__quick-filter__main">
                            <ul>
                                <li class="btn btn-click-course btn-click-active" id="ctgr0" category="0" @click="searchCourse(0, 1)">All Course</li>
                                <?php foreach ($categories as $category) { ?>
                                    <li class="btn btn-click-course"  @click="searchCourse(<?php echo $category->id; ?>, 1)" id="ctgr<?php echo $category->id; ?>"
                                        category="<?php echo $category->id; ?>"><?php echo $category->name; ?></li>
                                <?php } ?>
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
                    <template v-if="courses.length == 0">
                        <div class="row col-12"><p class="nodata">No data</p></div>
                    </template>
                    <template v-else>
                        <div class="col-4 block clctgr0" v-for="(course,index) in courses">
                        <div class="row col-12 course-block">
                            <div class="col-5 course-block__image" v-bind:style="{ backgroundImage: 'url('+(course.course_avatar)+')' }">
                                <template v-if="course.id == 506"><img src="images/Badge-examples 2.png" alt=""></template>
                                <template v-else-if="course.numofmodule == 0"><img src="<?php echo $_SESSION['component'] ?>" alt=""><span>0%</span></template>
                                <template v-else><img src="<?php echo $_SESSION['component'] ?>" alt=""><span>{{ Math.floor(course.numoflearned*100/course.numofmodule) }}%</span></template>
                            </div>
                            <div class="col-7">
                                <div class="course-info">
                                    <div class="info-text">
                                        <div class="course-info__title">
                                            <a :href="'lms/course/view.php?id='+course.id"
                                               :title="course.fullname"><p class="title-course">
                                                    <i></i>{{course.fullname}}</p></a>
                                        </div>
                                        <div class="course-info__detail">
                                            <ul>
                                                <li class="teacher">
                                                    <i class="fa fa-user" aria-hidden="true"></i> Ngo Ngoc
                                                </li>
                                                <li class="units"><i class="fa fa-file" aria-hidden="true"></i> {{course.numofmodule}} Units
                                                </li>
                                                <li class="units">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>  {{course.estimate_duration}} hours
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="btn-show btn-show-all btn-page">
                                        <button class="btn btn-click"><a
                                                :href="'lms/course/view.php?id='+course.id">Learn more</a>
                                        </button>
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
                <div class="container-fluid row">
                    <div class="footer-logo">
                        <img src="<?php echo $_SESSION["pathLogo"]; ?>" alt="">
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


    });

    Vue.component('v-pagination', window['vue-plain-pagination'])
    var app = new Vue({
        el: '#app',
        data: {
            category: 0,
            txtSearch: '',
            courses: [],
            urlTms: '',
            clctgr: true,
            current: 1,
            totalPage: 0,
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
            }
        },
        methods: {
            onPageChange: function(){
                // console.log(this.category);
                this.searchCourse(this.category, this.current);
            },
            searchCourse: function (category, page) {
                this.category = category || this.category;
                if(page == 1)
                    this.current = 1;
                this.urlTms = 'http://localhost:8888/elearning-easia/public';
                let url = '<?php echo $CFG->wwwroot; ?>';
                const params = new URLSearchParams();
                params.append('category', category);
                params.append('txtSearch', this.txtSearch);
                params.append('current', page || this.current);
                // params.append('pageCount', this.total);
                params.append('recordPerPage', this.recordPerPage);

                axios({
                    method: 'post',
                    url: url + '/coursesearch.php',
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
                    });
            }
        },
        mounted() {
            this.searchCourse();
        }
    })
</script>


</body>
</html>

<?php
die;
?>
