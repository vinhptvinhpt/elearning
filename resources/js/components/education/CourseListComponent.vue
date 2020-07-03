<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.khoa_dao_tao_online') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_khoa_dao_tao')}}</h5>

                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="dataTables_length">

                                            <v-select
                                                    :options="filterSelectOptions"
                                                    :reduce="filterSelectOption => filterSelectOption.id"
                                                    :placeholder="this.trans.get('keys.chon_danh_muc_khoa_hoc')"
                                                    :filter-by="myFilterBy"
                                                    v-model="category_id">
                                            </v-select>

                                            <!--                      <select v-model="category_id"-->
                                            <!--                              class="form-control" id="category_id">-->
                                            <!--                        <option value="">{{trans.get('keys.chon_danh_muc_khoa_hoc')}}</option>-->
                                            <!--                        <option v-for="cate in categories" :value="cate.id">-->
                                            <!--                          {{cate.category_name}}-->
                                            <!--                        </option>-->
                                            <!--                      </select>-->

                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="dataTables_length">
                                            <select v-model="status_course"
                                                    class="form-control" id="status_course">
                                                <option value="">{{trans.get('keys.chon_tinh_trang_khoa_hoc')}}</option>
                                                <option value="1">{{trans.get('keys.sap_dien_ra')}}</option>
                                                <option value="2">{{trans.get('keys.dang_dien_ra')}}</option>
                                                <option value="3">{{trans.get('keys.ket_thuc')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="dataTables_length">
                                            <date-picker v-model="startdate" :config="options"
                                                         :placeholder="trans.get('keys.ngay_bat_dau')"></date-picker>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="dataTables_length">
                                            <date-picker v-model="enddate" :config="options"
                                                         :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>
                                        </div>
                                    </div>
                                    <!--                  <div class="fillterConfirm col-sm-3" style="display: inline-block;">-->
                                    <!--                    <v-select-->
                                    <!--                      :options="filterSelectOptions"-->
                                    <!--                      :reduce="filterSelectOption => filterSelectOption.id"-->
                                    <!--                      :placeholder="this.trans.get('keys.chon_khoa_hoc')"-->
                                    <!--                      :filter-by="myFilterBy"-->
                                    <!--                      v-model="filter_select">-->
                                    <!--                    </v-select>-->
                                    <!--                  </div>-->
                                    <div class="col-sm-6">
                                        <form v-on:submit.prevent="getCourses(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text" class="form-control"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                                <button type="button" id="btnFilter" class="btn btn-primary"
                                                        style="margin-left: 5px" @click="getCourses(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="dataTables_length" style="display: block;">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row"
                                                        class="custom-select custom-select-sm form-control form-control-sm"
                                                        @change="getCourses(1)">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="50">50</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <span style="color:#3a55b1; font-size: 18px; font-weight: 600;">{{trans.get('keys.tong_so_khoa_dao_tao_hien_tai')}}: {{total_course}}</span>
                                    </div>
                                    <div class="col-6">
                                        <div id="datable_1_filter" class="dataTables_filter" style="float: right;">
                                            <label>
                                                <router-link v-if="slug_can('tms-educate-exam-online-add')"
                                                             :to="{name: 'CourseCreate'}">
                                                    <button type="button"
                                                            class="btn btn-success btn-md"
                                                            :placeholder="trans.get('keys.tao_moi')"
                                                            value="Tạo mới"
                                                            aria-controls="datable_1">{{trans.get('keys.tao_moi')}}
                                                    </button>
                                                </router-link>
                                            </label>
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
                                            <th class="text-center mobile_hide">{{trans.get('keys.bat_dau')}}</th>
                                            <th class="text-center mobile_hide">{{trans.get('keys.ket_thuc')}}</th>
                                            <th class="text-center mobile_hide">{{trans.get('keys.diem_qua_mon')}}</th>
                                            <th class="text-center mobile_hide">{{trans.get('keys.trang_thai')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(course,index) in courses">
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>
                                                <router-link
                                                        :to="{ name: 'CourseStatistic', params: { id: course.id, come_from: 'online' } }">
                                                    {{ course.shortname }}
                                                </router-link>
                                            </td>
                                            <td>{{ course.fullname }}</td>
                                            <td class="text-center mobile_hide">{{ course.startdate |convertDateTime}}
                                            </td>
                                            <td class="text-center mobile_hide">{{ course.enddate |convertDateTime}}
                                            </td>
                                            <td class="text-center mobile_hide">{{Math.floor(course.pass_score)}}</td>
                                            <td class="text-center mobile_hide">
                                              <span v-if="course.visible == 1">
                                             <i class="fa fa-toggle-on" @click="approveCourse(course.id,course.visible)"
                                                style="color:#3a55b1; cursor: pointer; font-size: 25px;"
                                                aria-hidden="true"></i>
                                                 </span>
                                                <span v-if="course.visible == 0">
                                             <i class="fa fa-toggle-off"
                                                @click="approveCourse(course.id,course.visible)"
                                                style="color:#6f7a7f; cursor: pointer; font-size: 25px;"
                                                aria-hidden="true"></i>
                                                </span>
                                            </td>
                                            <td class="text-center">

                                                <!--                                                <a v-if="slug_can('tms-educate-exam-online-edit')" :title="trans.get('keys.sua_noi_dung')"-->
                                                <!--                                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"-->
                                                <!--                                                   :href="lms_url + course.id">-->
                                                <!--                                                    <span class="btn-icon-wrap"><i class="fal fa-book-open"></i></span>-->
                                                <!--                                                </a>-->

                                                <a v-if="slug_can('tms-educate-exam-online-edit')"
                                                   :title="trans.get('keys.sua_noi_dung')"
                                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                   :href="'/lms/course/view.php?id='+course.id">
                                                    <span class="btn-icon-wrap"><i class="fal fa-book-open"></i></span>
                                                </a>

                                                <router-link
                                                        :title="trans.get('keys.moi_tham_gia_khoa_hoc')"
                                                        :class="course.visible == 1 ? 'btn btn-sm btn-icon btn-icon-circle btn-warning btn-icon-style-2' : 'btn disabled btn-sm btn-icon btn-icon-circle btn-grey btn-icon-style-2'"
                                                        :to="{ name: 'InviteStudent', params: { id: course.id, come_from: 'online', course_name: course.fullname } }">
                                                      <span class="btn-icon-wrap"><i
                                                              class="fal fa-arrow-alt-right"></i></span>
                                                </router-link>

                                                <router-link
                                                        :title="trans.get('keys.ghi_danh_khoa_hoc')"
                                                        :class="course.visible == 1 ? 'btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2' : 'btn disabled btn-sm btn-icon btn-icon-circle btn-grey btn-icon-style-2'"
                                                        :to="{ name: 'CourseEnrol', params: { id: course.id, come_from: 'online' } }">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-arrow-alt-right"></i></span>
                                                </router-link>

                                                <router-link v-if="slug_can('tms-educate-exam-online-edit')"
                                                             :title="trans.get('keys.sua_thong_tin_khoa_hoc')"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                             :to="{ name: 'CourseDetail', params: { id: course.id } }">
                                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                </router-link>

                                                <button v-if="slug_can('tms-educate-exam-online-deleted')"
                                                        :title="trans.get('keys.xoa')" data-toggle="modal"
                                                        data-target="#delete-ph-modal"
                                                        @click="deletePost(course.id)"
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
    //import vPagination from 'vue-plain-pagination'
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        props: ['slugs'],
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
                status_course: '',
                row: 5,
                urlGetList: '/api/courses/list',
                categories: [],
                date: new Date(),
                options: {
                    format: 'DD-MM-YYYY',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                },
                lms_url: '',
                filter_select: '',
                filterSelectOptions: []
            }
        },
        filters: {
            convertDateTime(value) {
                if (value) {
                    var time = new Date(value * 1000);
                    return time.toLocaleDateString();
                }
                return "";
            }
        },
        methods: {
            slug_can(permissionName) {
                return this.slugs.indexOf(permissionName) !== -1;
            },
            getCategories() {
                axios.post('/api/courses/get_list_category')
                    .then(response => {
                        let additionalSelections = [];
                        response.data.forEach(function (selectItem) {
                            let newItem = {
                                label: selectItem.category_name,
                                id: selectItem.id
                            };
                            additionalSelections.push(newItem);
                        });
                        this.filterSelectOptions = additionalSelections;
                        //this.categories = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            importExcel() {
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                axios.post('/system/user/import_user', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then(response => {
                        toastr['success'](this.trans.get('keys.import_tai_khoan_thanh_cong'), this.trans.get('keys.thanh_cong'));
                        this.$router.push({name: 'CourseIndex'});
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getCourses(paged) {
                axios.post(this.urlGetList, {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    sample: 0,
                    category_id: this.category_id,
                    startdate: this.startdate,
                    enddate: this.enddate,
                    status_course: this.status_course
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
            deletePost(id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    let loader = $('.preloader-it');
                    loader.fadeIn();
                    axios.post('/api/courses/delete', {course_id: id})
                        .then(response => {
                            loader.fadeOut();
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                                current_pos.getCourses(this.current);
                            } else {
                                toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                            }
                            swal.close();
                        })
                        .catch(error => {
                            loader.fadeOut();
                            swal.close();
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });
                return false;
            },
            approveCourse(course_id, status) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_chuyen_trang_thai_khoa_hoc'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/courses/approve', {course_id: course_id, current_status: status})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                                current_pos.getCourses(this.current);

                            } else {
                                toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                            }
                            swal.close();
                        })
                        .catch(error => {
                            swal.close();
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });

                return false;
            },
            fetch() {
                axios.post('/bridge/fetch', {
                    type: this.type,
                    view: 'CourseIndex'
                })
                    .then(response => {
                        this.lms_url = response.data.lms_url;
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            myFilterBy: (option, label, search) => {
                if (!label) {
                    label = '';
                }
                let new_search = convertUtf8(search);
                let new_label = convertUtf8(label);
                //return this.filterBy(option, new_label, new_search); //can not call components function here
                return (new_label || '').toLowerCase().indexOf(new_search) > -1; // "" not working
            },
            getDataForFilter() {
                this.user = '';
                this.userSelectOptions = []; //reset after search again

                axios.post('/system/filter/fetch', {
                    type: 'course-online'
                })
                    .then(response => {
                        let additionalSelections = [];
                        response.data.forEach(function (selectItem) {
                            let newItem = {
                                label: selectItem.label,
                                id: selectItem.id
                            };
                            additionalSelections.push(newItem);
                        });
                        this.filterSelectOptions = additionalSelections;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },
        mounted() {
            this.getCategories();
            this.getCourses();
            // this.fetch();
            //this.getDataForFilter();
        }
    }

    function convertUtf8(str) {
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
        str = str.replace(/đ/g, "d");
        str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g, " ");
        str = str.replace(/ + /g, " ");
        str = str.trim();
        return str;
    }

</script>

<style scoped>
    .v-select .dropdown-toggle {
    @extend . form-control;
    }
</style>
