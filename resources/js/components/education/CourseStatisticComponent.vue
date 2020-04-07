<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li v-if="come_from === 'online'" class="breadcrumb-item">
                            <router-link :to="{ name: 'CourseIndex' }">
                                {{ trans.get('keys.khoa_dao_tao_online') }}
                            </router-link>
                        </li>
                        <li v-else class="breadcrumb-item">
                            <router-link :to="{ name: 'CourseConcentrateIndex' }">
                                {{ trans.get('keys.khoa_dao_tao_tap_trung') }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_khoa_hoc') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mx-0">
            <div class="col-12 hk-sec-wrapper">
                <h5 class="hk-sec-title">{{trans.get('keys.thong_tin_khoa_hoc')}}</h5>
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div style="padding: 10px">
                                <img :src="course.avatar ? course.avatar : '/assets/dist/img/img-kh.jpg'" alt="">
                            </div>
                            <div class="card-body">
                                <h6 class="mb-5" style="text-transform: uppercase"><strong>{{course.shortname}}</strong>
                                </h6>
                                <p>{{trans.get('keys.ten_khoa_hoc')}}: <strong>{{course.fullname}}</strong></p>
                                <!--                            <p>Mã số nhân viên bán hàng: <strong>{{users.code ? users.code : 'Chưa cập nhật'}}</strong>-->
                                <!--                            </p>-->
                                <!--                            <div v-if="users.confirm == 0 && type == 'student'">-->
                                <!--                                <hr>-->
                                <!--                                <p>Thời gian hết hạn</p>-->
                                <!--                                <p><strong :class="users.diff_time_class">{{users.diff_time}}</strong></p>-->
                                <!--                            </div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="table-wrap">
                            <div class="">
                                <table class="dev-table table-sm table-hover display mb-0">
                                    <tbody>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <th scope="row">{{trans.get('keys.ma_khoa_hoc')}}</th>
                                            </div>
                                            <div class="col-8">
                                                <td>{{ course.shortname }}</td>
                                            </div>
                                        </div>


                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <th scope="row">{{trans.get('keys.ten_khoa_hoc')}}</th>
                                            </div>
                                            <div class="col-8">
                                                <td>{{ course.fullname}}</td>
                                            </div>
                                        </div>


                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <th scope="row">{{trans.get('keys.danh_muc_khoa_hoc')}}</th>
                                            </div>
                                            <div class="col-8">
                                                <td>{{ course.category_name }}</td>
                                            </div>
                                        </div>


                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <th scope="row">{{trans.get('keys.diem_qua_mon')}}</th>
                                            </div>
                                            <div class="col-8">
                                                <td>{{ course.pass_score }}</td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <th scope="row">{{trans.get('keys.thoi_gian_bat_dau')}}</th>
                                            </div>
                                            <div class="col-8">
                                                <td>{{ course.startdate }}</td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <th scope="row">{{trans.get('keys.thoi_gian_ket_thuc')}}</th>
                                            </div>
                                            <div class="col-8">
                                                <td>{{ course.enddate}}</td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <th scope="row">{{trans.get('keys.mo_ta')}}</th>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <div v-html="course.summary"></div>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <router-link v-if="this.come_from === 'online'"
                                             :to="{name: 'CourseDetail', params: {id: course_id}}"
                                             class="btn btn-primary btn-sm">{{trans.get('keys.edit')}}
                                </router-link>
                                <router-link v-else
                                             :to="{name: 'CourseConcentrateDetail', params: {id: course_id}}"
                                             class="btn btn-primary btn-sm">{{trans.get('keys.edit')}}
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 hk-sec-wrapper">
                <div class="row">
                    <div class="col-12">
                        <section class="hk-sec-wrapper">
                            <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_hoc_vien_trong_khoa_hoc')}}</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="table-wrap">
                                        <div class="row">
                                            <div class="col-6 dataTables_wrapper">
                                                <div class="dataTables_length">
                                                    <label>{{trans.get('keys.hien_thi')}}
                                                        <select v-model="row"
                                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                                @change="getStatictisUserCourse(1)">
                                                            <option value="5">5</option>
                                                            <option value="10">10</option>
                                                            <option value="20">20</option>
                                                            <option value="50">50</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <form v-on:submit.prevent="getStatictisUserCourse(1)">
                                                    <div class="d-flex flex-row form-group">
                                                        <input v-model="keyword" type="text"
                                                               class="form-control"
                                                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                                        <button type="button" id="btnFilter"
                                                                class="btn btn-primary d-none d-lg-block"
                                                                @click="getStatictisUserCourse(1)">
                                                            {{trans.get('keys.tim')}}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="datable_1" class="table_res">
                                                <thead>
                                                <tr>
                                                    <th>{{trans.get('keys.stt')}}</th>
                                                    <th>{{trans.get('keys.tai_khoan')}}</th>
                                                    <th class=" mobile_hide" style="width: 20%;">
                                                        {{trans.get('keys.ho_ten')}}
                                                    </th>
                                                    <th class="text-center">{{trans.get('keys.tien_do_hoc_%')}}</th>
                                                    <th class="text-center mobile_hide">
                                                        {{trans.get('keys.diem_hoc_vien')}}
                                                    </th>
                                                    <th class="text-center">{{trans.get('keys.trang_thai')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(cu,index) in courseUsers">
                                                    <td>{{ (current-1)*row+(index+1) }}</td>
                                                    <td>
                                                        {{ cu.username }}
                                                    <td class=" mobile_hide">{{ cu.lastname }} {{cu.firstname}}</td>
                                                    <td class="text-center">
                                                        <div v-if="total_course>0">
                                                            <div v-if="cu.user_course_learn==0">
                                                                0
                                                            </div>
                                                            <div v-else>
                                                                {{
                                                                ((cu.user_course_learn*100)/total_course).toFixed(2)}}
                                                            </div>

                                                        </div>
                                                        <div v-else>
                                                            0
                                                        </div>
                                                    </td>
                                                    <td class="text-center mobile_hide">
                                                        <div v-if="cu.finalgrade>0">{{ Math.floor(cu.finalgrade)}}</div>
                                                    </td>

                                                    <td class="text-center">
                                                        <!--                                                <div v-if="total_course==0">-->
                                                        <!--                                                    <span class="badge badge-yellow">Chưa học</span>-->
                                                        <!--                                                </div>-->
                                                        <!--                                                <div v-else>-->
                                                        <!--                                                    <span v-if="cu.finalgrade>=course.pass_score  && (cu.user_course_learn/total_course)>=1"-->
                                                        <!--                                                          class="badge badge-success">Pass</span>-->
                                                        <!--                                                    <span v-else class="badge badge-danger">Failed</span>-->
                                                        <!--                                                </div>-->

                                                        <div v-if="cu.finalgrade>0">
                                                            <div v-if="cu.user_course_learn>total_course">
                                                             <span v-if="cu.finalgrade>=course.pass_score"
                                                                   class="badge badge-success">Pass</span>
                                                                <span v-else class="badge badge-danger">Failed</span>
                                                            </div>
                                                            <div v-else>
                                                                <span class="badge badge-yellow">{{trans.get('keys.chua_hoan_thanh')}}</span>
                                                            </div>
                                                        </div>
                                                        <div v-else>
                                                            <span class="badge badge-yellow">{{trans.get('keys.chua_hoan_thanh')}}</span>
                                                        </div>
                                                    </td>

                                                </tr>
                                                </tbody>
                                                <tfoot>

                                                </tfoot>
                                            </table>

                                            <v-pagination v-model="current" @input="onPageChange"
                                                          :page-count="totalPages"
                                                          :classes=$pagination.classes
                                                          :labels=$pagination.labels></v-pagination>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <!--        Danh sách học viên điểm danh-->
            <div v-if="this.come_from === 'offline'" class="col-12 hk-sec-wrapper">
                <div class="row">
                    <div class="col-12">
                        <section class="hk-sec-wrapper">
                            <h5 class="hk-sec-title">
                                {{trans.get('keys.danh_sach_diem_danh_hoc_vien_trong_khoa_hoc')}}</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="table-wrap">
                                        <div class="row">
                                            <div class="col-6 dataTables_wrapper">
                                                <div class="dataTables_length">
                                                    <label>{{trans.get('keys.hien_thi')}}
                                                        <select v-model="rowAtt"
                                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                                @click="getStatictisUserAttendance(1)">
                                                            <option value="5">5</option>
                                                            <option value="10">10</option>
                                                            <option value="20">20</option>
                                                            <option value="50">50</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <form v-on:submit.prevent="getStatictisUserAttendance(1)">
                                                    <div class="d-flex flex-row form-group">
                                                        <input v-model="keywordAtt" type="text"
                                                               class="form-control search_text"
                                                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+'...'">
                                                        <button type="button" id="btnFilterAtt"
                                                                class="btn btn-primary btn-sm"
                                                                @click="getStatictisUserAttendance(1)">
                                                            {{trans.get('keys.tim')}}
                                                        </button>
                                                        <a style="color: #fff" class="btn btn-primary btn-sm"
                                                           v-on:click="exportExcelAttendance()"
                                                           :title="trans.get('keys.xuat_excel')">
                                                            <span class="btn-icon-wrap"><i
                                                                    class="fal fa-file-excel-o"></i>&nbsp;{{trans.get('keys.excel')}}</span>
                                                        </a>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="datable_1Att" class="table_res">
                                                <thead>
                                                <tr>
                                                    <th>{{trans.get('keys.stt')}}</th>
                                                    <th>{{trans.get('keys.tai_khoan')}}</th>
                                                    <th class=" mobile_hide" style="width: 20%;">
                                                        {{trans.get('keys.ho_ten')}}
                                                    </th>
                                                    <th class="text-center">{{trans.get('keys.so_buoi_diem_danh')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(att,index) in attendanceUsers">
                                                    <td>{{ (currentAtt-1)*rowAtt+(index+1) }}</td>
                                                    <td>
                                                        {{ att.username }}
                                                    <td class=" mobile_hide">{{ att.lastname }} {{att.firstname}}</td>
                                                    <td class="text-center">
                                                        {{ att.count_attendance }} / {{ att.total_date_course }}
                                                    </td>

                                                </tr>
                                                </tbody>
                                                <tfoot>

                                                </tfoot>
                                            </table>
                                            <v-pagination v-model="currentAtt" @input="onPageChange"
                                                          :page-count="totalPagesAtt"
                                                          :classes=$pagination.classes
                                                          :labels=$pagination.labels></v-pagination>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <!--        Danh sách tài liệu trong khóa học -->
            <div class="col-12 hk-sec-wrapper">
                <div class="row">
                    <div class="col-12">
                        <section class="hk-sec-wrapper">
                            <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_tai_lieu_trong_khoa_hoc')}}</h5>
                            <label style="font-size: 12px;">Chọn loại học liệu để hiển thị danh sách tài liệu theo loại
                                học liệu được
                                chọn</label>
                            <div class="row">
                                <!--                Danh sách module trong course -->
                                <div class="col-sm-3">
                                    <div class="table-responsive">
                                        <table id="datable_course_module" class="table_res">
                                            <thead>
                                            <tr>
                                                <th>{{trans.get('keys.stt')}}</th>
                                                <th>{{trans.get('keys.ten_module')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(att,index) in modules">
                                                <td>{{ index+1 }}</td>
                                                <td><a style="color: #007bff;cursor: pointer;"
                                                       @click="getLogCourse(att.id, 1)">{{ att.name
                                                    }}</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!--                Danh sách tài liệu -->
                                <div class="col-sm-8 m-auto">
                                    <div class="table-wrap">
                                        <div class="row">
                                            <div class="col-3 dataTables_wrapper">
                                                <div class="dataTables_length">
                                                    <label>{{trans.get('keys.kieu_hanh_dong')}}
                                                        <select v-model="actionDoc"
                                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                                @click="getLogCourse(module_id, 1)">
                                                            <option value="">Chọn kiểu</option>
                                                            <option value="viewed">viewed</option>
                                                            <option value="created">created</option>
                                                            <option value="updated">updated</option>
                                                            <option value="deleted">deleted</option>
                                                            <option value="uploaded">uploaded</option>
                                                            <option value="submitted">submitted</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3 dataTables_wrapper">
                                                <div class="dataTables_length">
                                                    <label>{{trans.get('keys.hien_thi')}}
                                                        <select v-model="rowDoc"
                                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                                @click="getLogCourse(module_id, 1)">
                                                            <option value="5">5</option>
                                                            <option value="10">10</option>
                                                            <option value="20">20</option>
                                                            <option value="50">50</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex flex-row form-group">
                                                    <input v-model="keywordDoc" type="text"
                                                           class="form-control search_text"
                                                           :placeholder="trans.get('keys.tim_kiem_theo_ten_du_lieu_hoac_nguoi_tao')+'...'">
                                                    <button type="button" id="btnFilterDoc"
                                                            class="btn btn-primary btn-sm" style="margin-left: 2px;"
                                                            @click="getLogCourse(module_id, 1)">
                                                        {{trans.get('keys.tim')}}
                                                    </button>
                                                    <button type="button" id="btnResetDoc"
                                                            class="btn btn-primary btn-sm" style="margin-left: 2px;"
                                                            @click="reset()">
                                                        {{trans.get('keys.lam_moi_bang_tai_lieu')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="datable_1Att1" class="table_res">
                                                <thead>
                                                <tr>
                                                    <th>{{trans.get('keys.stt')}}</th>
                                                    <th>{{trans.get('keys.ten_module')}}</th>
                                                    <th>{{trans.get('keys.ten_du_lieu')}}</th>
                                                    <th>{{trans.get('keys.kieu_hanh_dong')}}</th>
                                                    <th>{{trans.get('keys.nguoi_tao')}}</th>
                                                    <th>{{trans.get('keys.ngay_tao')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(att,index) in documents">
                                                    <td>{{ index+1 }}</td>
                                                    <td>{{ JSON.parse(att.other).modulename }}</td>
                                                    <!--                          <td>{{ isset(JSON.parse(att.other).name) ? JSON.parse(att.other).name : att.name }}</td>-->
                                                    <td v-if="JSON.parse(att.other) !== undefined">{{
                                                        JSON.parse(att.other).name }}
                                                    </td>
                                                    <td v-else>{{ att.name }}</td>
                                                    <td>{{ att.action }}</td>
                                                    <td>{{ att.username }}</td>
                                                    <td>{{ att.timecreated | convertDateTime }}</td>
                                                </tr>
                                                </tbody>
                                                <tfoot>

                                                </tfoot>
                                            </table>
                                            <v-pagination v-model="currentDoc" @input="onPageChange"
                                                          :page-count="totalPagesDoc"
                                                          :classes=$pagination.classes
                                                          :labels=$pagination.labels></v-pagination>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

        </div>

        <course-infra v-if="this.come_from === 'offline'" :course_id="course_id"></course-infra>
    </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'

    import CourseInfra from './CourseInfrastructureComponent';

    export default {
        props: ['course_id', 'come_from'],
        components: {CourseInfra},
        data() {
            return {
                course: {
                    avatar: ''
                },
                keyword: '',
                row: 5,
                keywordAtt: '',
                rowAtt: 5,
                current: 1,
                currentAtt: 1,
                courseUsers: [],
                attendanceUsers: [],
                total_course: 0,
                total_attendance: 0,
                totalPages: 1,
                totalPagesAtt: 1,
                modules: [],
                documents: [],
                keywordDoc: '',
                rowDoc: 5,
                currentDoc: 1,
                total_document: 0,
                totalPagesDoc: 1,
                module_id: 0,
                actionDoc: ''
            }
        },
        filters: {
            convertPercent(value) {
                return (value * 100);
            },
            convertDateTime(value) {
                var time = new Date(value * 1000);
                return time.toLocaleDateString();
            }
        },
        methods: {
            reset() {
                this.keywordDoc = '';
                this.rowDoc = 5;
                this.actionDoc = '';
                this.getLogCourse(0, 1);
            },
            getLogCourse(idModule, paged) {
                if (idModule != undefined) {
                    this.module_id = idModule;
                }
                axios.post('/api/courses/get_list_document_course', {
                    course_id: this.course_id,
                    module_id: this.module_id,
                    row: this.rowDoc,
                    keyword: this.keywordDoc,
                    page: paged || this.currentDoc,
                    action: this.actionDoc
                })
                    .then(response => {
                        this.total_document = response.data.total_course;
                        this.documents = response.data.data;
                        this.currentDoc = response.data.pagination.current_page;
                        this.totalPagesDoc = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getModule() {
                axios.get('/api/courses/get_list_module_course/' + this.course_id)
                    .then(response => {
                        this.modules = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getCourseDetail() {
                axios.get('/api/courses/get_course_detail/' + this.course_id)
                    .then(response => {
                        this.course = response.data;

                        var startdate = new Date(response.data.startdate * 1000);

                        var ten = function (i) {
                            return (i < 10 ? '0' : '') + i;
                        };
                        var YYYY = startdate.getFullYear();
                        var MM = ten(startdate.getMonth() + 1);
                        var DD = ten(startdate.getDate());


                        this.course.startdate = DD + '/' + MM + '/' + YYYY;

                        var endate = new Date(response.data.enddate * 1000);

                        var YYYY_end = endate.getFullYear();
                        var MM_end = ten(endate.getMonth() + 1);
                        var DD_end = ten(endate.getDate());

                        this.course.enddate = DD_end + '/' + MM_end + '/' + YYYY_end;

                        this.course.pass_score = Math.floor(response.data.pass_score);
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            onPageChange() {
                this.getStatictisUserCourse();
                this.getStatictisUserAttendance();
                this.getLogCourse();
            },
            getStatictisUserCourse(paged) {
                axios.post('/api/course/statistic', {
                    course_id: this.course_id,
                    row: this.row,
                    keyword: this.keyword,
                    page: paged || this.current,
                })
                    .then(response => {
                        this.total_course = response.data.total_course;
                        this.courseUsers = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            getStatictisUserAttendance(paged) {
                axios.post('/course/student/attendance', {
                    course_id: this.course_id,
                    row: this.rowAtt,
                    keyword: this.keywordAtt,
                    page: paged || this.currentAtt,
                })
                    .then(response => {
                        this.total_attendance = response.data.total_attendance;
                        this.attendanceUsers = response.data.data.data;
                        this.currentAtt = response.data.pagination.current_page;
                        this.totalPagesAtt = response.data.pagination.total;

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            exportExcelAttendance() {
                axios.post('/api/exportAttendance', {
                    keyword: this.keywordAtt,
                    course_id: this.course_id,
                    course_name: this.course.fullname
                })
                    .then(response => {
                        let file_name = response.data;
                        let a = $("<a>")
                            .prop("href", "/api/downloadExport/" + file_name)
                            .appendTo("body");
                        a[0].click();
                        a.remove();
                    })
                    .catch(error => {
                        toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                    });
            },
        },
        mounted() {
            this.getCourseDetail();
            this.getStatictisUserCourse(1);
            this.getStatictisUserAttendance(1);
            this.getModule();
            this.getLogCourse(0, 1);
        }
    }
</script>

<style scoped>

</style>
