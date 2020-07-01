<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.khoi_phuc_khoa_dao_tao') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <!--                <h5 class="hk-sec-title">{{trans.get('keys.khoi_phuc_khoa_dao_tao')}}</h5>-->

                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>{{trans.get('keys.tu_ngay')}}</label>
                                        <date-picker v-model="startdate" :config="{format: 'DD-MM-YYYY'}"></date-picker>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>{{trans.get('keys.den_ngay')}}</label>
                                        <date-picker v-model="enddate" :config="{format: 'DD-MM-YYYY'}"></date-picker>
                                    </div>
                                    <div class="col-sm-6">
                                        <select v-model="category_id"
                                                class="form-control" id="category_id">
                                            <option value="">{{trans.get('keys.chon_danh_muc_khoa_dao_tao')}}</option>
                                            <option v-for="cate in categories" :value="cate.id">
                                                {{cate.category_name}}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <form v-on:submit.prevent="getCourses(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                                <button type="button" id="btnFilter" class="btn btn-primary"
                                                        @click="getCourses(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-12 dataTables_wrapper">
                                        <div class="dataTables_length d-block">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row"
                                                        class="custom-select form-control" @change="getCourses(1)">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="50">50</option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="dataTables_length d-block">
                                            <span style="color:#3a55b1; font-size: 20px; font-weight: 600;">{{trans.get('keys.tong_so_khoa_dao_tao_muon_khoi_phuc')}}: {{total_course}}</span>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <div class="table-responsive">
                                    <table id="datable_1" class="table_res">
                                        <thead>
                                        <tr>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                            <th style="width: 20%;">{{trans.get('keys.ten_khoa_hoc')}}</th>
                                            <th class="text-center mobile_hide" style="width: 15%;">
                                                {{trans.get('keys.danh_muc')}}
                                            </th>
                                            <th class="text-center mobile_hide">{{trans.get('keys.ngay_xoa')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(course,index) in courses">
                                            <!--                                        <td>{{index+1}}</td>-->
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>{{ course.shortname }}</td>
                                            <td>{{ course.fullname }}</td>
                                            <td class=" mobile_hide">{{ course.category_name }}</td>
                                            <td class="text-center mobile_hide">{{ course.timecreated}}</td>

                                            <td class="text-center">

                                                <button :title="trans.get('keys.khoi_phuc_khoa_hoc')"
                                                        data-toggle="modal"
                                                        data-target="#delete-ph-modal"
                                                        @click="restoreCourse(course.id,course.categoryid)"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-trash-restore"></i></span>
                                                </button>

                                                <button :title="trans.get('keys.xoa_khoa_hoc')" data-toggle="modal"
                                                        data-target="#delete-ph-modal"
                                                        @click="deleteCourse(course.id,course.categoryid)"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>

                                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
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
</template>

<script>
    // import vPagination from 'vue-plain-pagination'
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        components: {
            //vPagination,
            datePicker
        },
        data() {
            return {
                courses: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                total_course: 0,
                category_id: '',
                startdate: '',
                enddate: '',
                row: 5,
                urlGetListUser: '/api/courses/get_list_restore',
                categories: [],
                date: new Date(),
                options: {
                    format: 'DD/MM/YYYY',
                    useCurrent: false,
                }
            }
        },
        methods: {
            getCategories() {
                axios.post('/api/courses/get_list_category_restore')
                    .then(response => {
                        this.categories = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },

            getCourses(paged) {
                axios.post(this.urlGetListUser, {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    category_id: this.category_id,
                    timecreated: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        this.courses = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                        this.total_course = response.data.total_course;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getCourses();
            },
            restoreCourse(id, instance_id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_co_chac_chan_muon_khoi_phuc_khoa_dao_tao_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    let loader = $('.preloader-it');
                    loader.fadeIn();
                    axios.post('/api/courses/restore', {course_id: id, instance_id: instance_id, action: 'restore'})
                        .then(response => {
                            loader.fadeOut();
                            if (response.data.status) {
                                swal({
                                    title: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }, function () {
                                    location.reload();
                                });
                            } else {
                                swal({
                                    title: response.data.message,
                                    type: "error",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                });
                            }

                        })
                        .catch(error => {
                            loader.fadeOut();
                            swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error")
                            //console.log(error);
                        });
                });

                return false;
            },
            deleteCourse(id, instance_id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_co_chac_chan_muon_xoa_khoa_dao_tao_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    let loader = $('.preloader-it');
                    loader.fadeIn();
                    axios.post('/api/courses/delete_forever', {course_id: id})
                        .then(response => {
                            loader.fadeOut();
                            if (response.data.status) {
                                swal({
                                    title: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }, function () {
                                    location.reload();
                                });
                            } else {
                                swal({
                                    title: response.data.message,
                                    type: "error",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                });
                            }

                        })
                        .catch(error => {
                            loader.fadeOut();
                            swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error")
                            //console.log(error);
                        });
                });

                return false;
            }
        },
        mounted() {
            this.getCategories();
            this.getCourses();
        }
    }
</script>

<style scoped>

</style>
