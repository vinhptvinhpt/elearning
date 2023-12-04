<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <router-link to="/tms/education/course/course_sample">{{
                                trans.get('keys.quan_tri_thu_vien_khoa_hoc') }}
                            </router-link>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_thu_vien_khoa_hoc')}}</h5>
                    <div class="row mb-4" v-if="slug_can('tms-educate-libraly-add')">
                        <div class="col-sm">
                            <div class="accordion" id="accordion_1">
                                <div class="card" style="border-bottom: 1px solid rgba(0,0,0,.125);">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                                           aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_vien_khoa_hoc_thu_cong')}}</a>
                                    </div>
                                    <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                        <div class="card-body">
                                            <!-- view thêm mới course-->
                                            <div class="row">
                                                <div class="col-12 col-lg-3 mb-2">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <input type="file" ref="file" name="file" class="dropify"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-9">
                                                    <form action="" class="form-row">

                                                        <div v-if="slug_can('tms-system-administrator-grant')"
                                                             class="col-sm-6 form-group">
                                                            <label>{{trans.get('keys.thu_vien_nguon_cho_to_chuc')}}</label>
                                                            <v-select
                                                                    @input="setShortName"
                                                                    :options="librarySelectOptions"
                                                                    :reduce="librarySelectOption => librarySelectOption.id"
                                                                    :placeholder="this.trans.get('keys.chon_thu_vien_nguon')"
                                                                    :filter-by="myFilterBy"
                                                                    v-model="library">
                                                            </v-select>
                                                            <em>{{
                                                                trans.get('keys.chon_mot_thu_vien_trong_day_he_thong_se_sinh_ma_tiep_theo_ma_cuoi_cung_cua_day')
                                                                }}</em>
                                                        </div>

                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-1">{{trans.get('keys.ma_khoa_hoc')}}
                                                                *</label>
                                                            <input v-model="shortname" type="text" id="inputText1-1"
                                                                   :disabled="library.length > 0"
                                                                   :placeholder="trans.get('keys.nhap_ma_thu_vien')"
                                                                   class="form-control mb-4">
                                                            <label v-if="!shortname"
                                                                   class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>

                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText6">{{trans.get('keys.ten_khoa_hoc')}}
                                                                *</label>
                                                            <input v-model="fullname" type="text" id="inputText6"
                                                                   :placeholder="trans.get('keys.nhap_ten_thu_vien')"
                                                                   class="form-control mb-4">
                                                            <label v-if="!fullname"
                                                                   class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                                        </div>

                                                        <div class="col-sm-6 form-group">
                                                            <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}}</label>
                                                            <input v-model="pass_score" type="number" id="pass_score"
                                                                   :placeholder="trans.get('keys.vi_du')+': 50'"
                                                                   class="form-control mb-4">
                                                        </div>

                                                        <div class="col-sm-6 form-group">
                                                            <label for="is_toeic">{{trans.get('keys.toeic_course')}}</label>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       id="is_toeic"
                                                                       :checked="is_toeic==1?true:false"
                                                                       v-model="is_toeic">
                                                                <label v-if="is_toeic == 1" class="custom-control-label"
                                                                       for="is_toeic">Yes</label>
                                                                <label v-else class="custom-control-label"
                                                                       for="is_toeic">No</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 form-group">
                                                            <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                                                            <!--                                                            <textarea v-model="description" class="form-control"-->
                                                            <!--                                                                      :config="editorConfig"-->
                                                            <!--                                                                      rows="3" id="article_ckeditor"-->
                                                            <!--                                                                      :placeholder="trans.get('keys.noi_dung')"></textarea>-->
                                                            <ckeditor v-model="description"
                                                                      :config="editorConfig"></ckeditor>
                                                        </div>
                                                    </form>
                                                    <div class="button-list text-right">
                                                        <!--                                                        <button type="button" @click="goBack()"-->
                                                        <!--                                                                class="btn btn-secondary btn-sm">-->
                                                        <!--                                                            {{trans.get('keys.huy')}}-->
                                                        <!--                                                        </button>-->
                                                        <button @click="createCourse()" type="button"
                                                                class="btn btn-primary btn-sm">{{trans.get('keys.tao')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--<div class="card">-->
                                <!--                                <div class="card-header d-flex justify-content-between">-->
                                <!--                                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2"-->
                                <!--                                       aria-expanded="false"><i class="fal fa-upload mr-3"></i>{{trans.get('keys.tai_len_file_excel')}}</a>-->
                                <!--                                </div>-->
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-4 dataTables_wrapper">
                                        <div class="dataTables_length" style="display: block;">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row"
                                                        class="custom-select custom-select-sm form-control form-control-sm"
                                                        @click="getCourses(1)">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="dataTables_length">
                                            <!--                                            <v-select-->
                                            <!--                                                    @input="selectCourse()"-->
                                            <!--                                                    :options="courseSelectOptions"-->
                                            <!--                                                    :reduce="courseSelectOption => courseSelectOption.id"-->
                                            <!--                                                    :placeholder="this.trans.get('keys.khoa_hoc')"-->
                                            <!--                                                    :filter-by="myFilterBy"-->
                                            <!--                                                    v-model="courseSearch">-->
                                            <!--                                            </v-select>-->
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <form v-on:submit.prevent="getCourses(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text" id="tags"
                                                       class="form-control search_text"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_hoac_ma_khoa')+' ...'">
                                                <button type="button" id="btnFilter" class="btn btn-primary btn-sm"
                                                        @click="getCourses(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 dataTables_wrapper">
                                        <div class="dataTables_length">
                                            <span style="color:#3a55b1; font-size: 20px; font-weight: 600;">{{trans.get('keys.tong_so_thu_vien_khoa_hoc_hien_tai')}}: {{total_course}}</span>
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
                                            <th style="width: 30%;">{{trans.get('keys.ten_khoa_hoc')}}</th>
                                            <!--                      <th class="text-center mobile_hide" style="width: 15%;">-->
                                            <!--                        {{trans.get('keys.diem_qua_mon')}}-->
                                            <!--                      </th>-->
                                            <th class="text-center mobile_hide">
                                                {{trans.get('keys.cap_nhat_lan_cuoi')}}
                                            </th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(course,index) in courses">
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>{{ course.shortname }}</td>
                                            <td>{{ course.fullname }}</td>
                                            <!--                      <td class="text-center mobile_hide">{{Math.floor(course.pass_score)}}</td>-->
                                            <td v-if="course.username && course.username.length > 0"
                                                class="text-center mobile_hide"><a
                                                    style="cursor: default; color: #007bff;"
                                                    :title="capitalizeFirstLetter(course.last_modify_action) + ' at ' + course.last_modify_time">{{
                                                course.username }}</a></td>
                                            <td v-else></td>
                                            <td class="text-center">
                                                <!--                                                <a :title="trans.get('keys.sua_noi_dung')"-->
                                                <!--                                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"-->
                                                <!--                                                   :href="lms_url + course.id"-->
                                                <!--                                                ><span class="btn-icon-wrap"><i class="fal fa-book-open"></i></span></a>-->

                                                <a :title="trans.get('keys.sua_noi_dung')"
                                                   v-if="slug_can('tms-educate-libraly-edit')"
                                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                   style="color: white;"
                                                   @click.left="openView(course.id,1)"
                                                   @click.right="openView(course.id,2)">
                                                    <span class="btn-icon-wrap"><i class="fal fa-book-open"></i></span>
                                                </a>

                                                <router-link v-if="slug_can('tms-educate-libraly-edit')"
                                                             :title="trans.get('keys.sua_thong_tin_khoa_hoc')"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                             :to="{ name: 'SampleCourseDetail', params: { id: course.id } }">
                                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                </router-link>

                                                <router-link v-if="slug_can('tms-educate-exam-clone-add')"
                                                             :title="trans.get('keys.clone_khoa_hoc_tu_thu_vien')"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                             :to="{ name: 'CloneCourseLibrary', params: { course_id: course.id } }">
                                                    <span class="btn-icon-wrap"><i class="fal fa-clone"></i></span>
                                                </router-link>

                                                <router-link v-if="slug_can('tms-educate-exam-clone-add')"
                                                             :title="trans.get('keys.tao_moi_khoa_hoc_tu_thu_vien')"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-outline-success btn-icon-style-2"
                                                             :to="{ name: 'CourseClone', params: { course_id: course.id } }">
                                                    <span class="btn-icon-wrap"><i class="fal fa-clone"></i></span>
                                                </router-link>

                                                <button :title="trans.get('keys.xoa')" data-toggle="modal"
                                                        v-if="slug_can('tms-educate-libraly-deleted')"
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
    // import CourseSampleCreate from './CourseCreateSampleComponent'
    import CKEditor from 'ckeditor4-vue';

    export default {
        props: ['file_url', 'type', 'slugs'],
        //components: {vPagination},
        //components: {CourseSampleCreate},
        components: {
            CKEditor
        },
        data() {
            return {
                fullname: '',
                shortname: '',
                pass_score: '',
                description: '',

                editorConfig: {
                    filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
                    // The configuration of the editor.
                    //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
                    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                    filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
                },

                courses: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                is_toeic: false,
                total_course: 0,
                row: 10,
                urlGetListUser: '/api/courses/list',
                lms_url: '',
                librarySelectOptions: [],
                library: '',
                libraryCodes: [],
                courseSearch: '',
                courseSelectOptions: [],
            }
        },
        filters: {
            convertDateTime(value) {
                var time = new Date(value * 1000);
                return time.toLocaleDateString();
            }
        },
        methods: {
            getCourseSelectOptions() {
                axios.post(this.urlGetListUser, {
                    row: 0,
                    sample: 1,
                })
                    .then(response => {
                        let additionalCities = [];
                        response.data.forEach(function (cityItem) {
                            let newCity = {
                                label: cityItem.shortname + ' - ' + cityItem.fullname,
                                data_search: cityItem.fullname,
                                id: cityItem.id
                            };
                            additionalCities.push(newCity);
                        });
                        this.courseSelectOptions = additionalCities;
                        this.loadAutoComplete();
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            loadAutoComplete() {
                $("#tags").autocomplete({
                    source: this.lightWell,
                    minLength: 2,
                    select: function (e, ui) {
                        this.keyword = ui.item.data_search;
                        window.location.href = '/lms/course/view.php?id=' + ui.item.id;
                    },
                    change: function (e, ui) {
                        // alert(ui.item.value);

                    }
                });
            },
            lightWell(request, response) {
                function hasMatch(s) {
                    return s.toLowerCase().indexOf(request.term.toLowerCase()) !== -1;
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

                var i, l, obj, matches = [];

                if (request.term === "") {
                    response([]);
                    return;
                }

                for (i = 0, l = this.courseSelectOptions.length; i < l; i++) {
                    obj = this.courseSelectOptions[i];
                    if (hasMatch(obj.label) || hasMatch(convertUtf8(obj.data_search))) {
                        matches.push(obj);
                    }
                }
                response(matches);
            },
            selectCourse() {
                window.location.href = '/lms/course/view.php?id=' + this.courseSearch;
            },
            slug_can(permissionName) {
                return this.slugs.indexOf(permissionName) !== -1;
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
            async getLibrary() {
                return await axios.post('/api/courses/get_library', {})
                    .then(response => {
                        let codes = [];
                        response.data.forEach(function (cityItem) {
                            codes.push(cityItem.shortname);
                        });
                        return codes;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getLibraryCodes() { //Data for select2, admin only
                this.librarySelectOptions = [];
                this.libraryCodes = [];
                axios.post('/api/courses/get_library_codes', {})
                    .then(response => {
                        let additionalCities = [];
                        response.data.forEach(function (cityItem) {
                            let newCity = {
                                label: cityItem.code,
                                id: cityItem.code
                            };
                            additionalCities.push(newCity);
                        });
                        this.librarySelectOptions = additionalCities;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            // onChangeToeic() {
            //     if (this.is_toeic == 1) {
            //         $('#pass_score').attr("disabled", true);
            //     } else {
            //         $('#pass_score').attr("disabled", false);
            //     }
            // },
            async setShortName() {
                if (this.library) {
                    let codesA = await this.getLibrary();
                    let prefix = this.library.replace("-", "_");
                    let biggest = 0;
                    let curPos = this;
                    codesA.forEach(function (item) {
                        if (item.indexOf(prefix) !== -1) {
                            let lastNumberCode = parseInt(curPos.getLastNumber(item));
                            if (lastNumberCode > biggest) {
                                biggest = lastNumberCode;
                            }
                        }
                    });
                    let nextNumber = biggest + 1;
                    let append = this.composeAppend(nextNumber);
                    this.shortname = prefix + '_' + append;
                } else {
                    this.shortname = '';
                    this.library = '';
                }
            },
            getLastNumber(str) {
                let arr = str.split('_');
                let reverse = arr.reverse();
                if (isNaN(reverse[0])) {
                    return '0';
                } else {
                    return reverse[0];
                }
            },
            capitalizeFirstLetter(string) {
                return string.length > 0 && string ? string[0].toUpperCase() + string.slice(1) : '';
            },
            createCourse() {
                if (!this.shortname) {
                    $('.shortname_required').show();
                    return;
                }
                if (!this.fullname) {
                    $('.fullname_required').show();
                    return;
                }

                //validate positive number
                var rePosNum = /^$|^([0]{1}.{1}[0-9]+|[1-9]{1}[0-9]*.{1}[0-9]+|[0-9]+|0)$/;

                if (!rePosNum.test(this.pass_score)) {
                    toastr['error'](this.trans.get('keys.dinh_dang_du_lieu_khong_hop_le') + '( ' + this.trans.get('keys.pass_score') + ' )', this.trans.get('keys.that_bai'));
                    return;
                }

                // if (this.is_active == 0) {
                //   if (!this.pass_score) {
                //     $('.pass_score_required').show();
                //     return;
                //   }
                // }

                // var editor_data = CKEDITOR.instances.article_ckeditor.getData();
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.fullname);
                this.formData.append('shortname', this.shortname);
                this.formData.append('startdate', '01-01-2019'); //gán cứng 2 giá trị do sử dụng cùng 1 api với tạo mới khóa đào tạo, không sử dụng 2 giá trị này trên server
                this.formData.append('enddate', '01-01-2119');
                this.formData.append('pass_score', this.pass_score);
                this.formData.append('description', this.description);
                this.formData.append('course_place', '');
                this.formData.append('allow_register', 1);
                this.formData.append('is_end_quiz', 0);
                this.formData.append('total_date_course', 0);// truyền giá trị để nhận biết đây không phải khóa học tập trung
                this.formData.append('category_id', 2); //gắn cứng giá trị quy định đây là id danh mục mãu
                this.formData.append('sample', 1);// truyền giá trị để nhận biết đây là khóa học mẫu
                this.formData.append('selected_org', this.library);// truyền giá trị để nhận biết thư viện được tạo cho một tổ chức cụ thể
                var is_toeic = this.is_toeic ? 1 : 0;
                this.formData.append('is_toeic', is_toeic);

                let current_pos = this;
                axios.post('/api/courses/create', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thong_bao'));
                            this.getCourses(this.current);
                            this.fullname = '';
                            this.shortname = '';
                            this.pass_score = '';
                            this.description = '';
                            this.avatar = '';
                            this.library = '';
                            this.hintCode();
                            this.getLibrary();
                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.thong_bao'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });


            },
            getCourses(paged) {
                axios.post(this.urlGetListUser, {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    sample: 1
                })
                    .then(response => {
                        this.courses = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                        this.total_course = response.data.total_course;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            hintCode() {
                axios.post('/api/courses/hint_code', {
                    type: 'sample'
                })
                    .then(response => {
                        if (response.data.status) {
                            this.shortname = response.data.otherData;
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            composeAppend(num) {
                let str = num.toString();
                let length = 3;
                if (str.length >= length) {
                    return num;
                } else {
                    let filler = '0';
                    return filler.repeat(length - str.length) + str;
                }
            },
            onPageChange() {
                // let back = this.getParamsBackPage();
                if (sessionStorage.getItem('SampleCourseBack') == '1') {
                    this.current = Number(sessionStorage.getItem('SampleCoursePage'));
                    this.row = Number(sessionStorage.getItem('SampleCoursePageSize'));
                    this.keyword = sessionStorage.getItem('SampleCourseKeyWord');

                    this.$route.params.back_page = null;
                }
                this.getCourses();
                this.getLibrary();
                this.getLibraryCodes();
            },
            getParamsBackPage() {
                return this.$route.params.back_page;
            },
            setParamsBackPage(value) {
                this.$route.params.back_page = value;
            },
            deletePost(id) {
                // sessionStorage.setItem('surveyPage', this.current);
                // sessionStorage.setItem('surveyPageSize', this.row);
                // sessionStorage.setItem('surveyKeyWord', this.keyword);
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.press_ok'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/courses/delete', {course_id: id})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thong_bao'));
                                if (current_pos.courses.length == 1) {
                                    current_pos.current = current_pos.current > 1 ? current_pos.current - 1 : 1;
                                }
                                // current_pos.onPageChange();
                                current_pos.getCourses(current_pos.current);
                            } else {
                                toastr['error'](response.data.message, current_pos.trans.get('keys.thong_bao'));
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
                    view: 'SampleCourseIndex'
                })
                    .then(response => {
                        this.lms_url = response.data.lms_url;
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            setFileInput() {
                $('.dropify').dropify();
            },
            openView(id,type) {
              let d = new Date();
              d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
              var expires = "expires="+d.toUTCString();
              let storage = localStorage.getItem('auth.user');
              let user_id = storage ? JSON.parse(storage).id : '';
              let domain = storage ? JSON.parse(storage).domain : '';
              let cookie_name = 'library' + '_' + id + '_' + user_id;
              document.cookie = cookie_name + "=" + 1 + ";" + expires + ";path=/";
              switch (type){
                case 1:
                  window.location.href = domain + "/lms/course/view.php?id=" + id;
                  break;
                case 2:
                  window.open(domain + "/lms/course/view.php?id=" + id, '_blank');
                  break;
              }
            }
        },
        mounted() {
            // this.getCourses();
            this.hintCode();
            // this.fetch();
            this.getLibraryCodes();
            this.getCourseSelectOptions();
            sessionStorage.clear();

        },
        updated() {
            this.setFileInput();
        },
        destroyed() {
            sessionStorage.setItem('SampleCourseBack', '1');
            sessionStorage.setItem('SampleCoursePage', this.current);
            sessionStorage.setItem('SampleCoursePageSize', this.row);
            sessionStorage.setItem('SampleCourseKeyWord', this.keyword);
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

</style>
