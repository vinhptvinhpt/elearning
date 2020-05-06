<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link :to="{ name: 'CourseConcentrateIndex' }">{{
                                trans.get('keys.khoa_dao_tao_tap_trung') }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_khoa_hoc_tap_trung')
                            }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm">
                <div class="accordion" id="accordion_1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <a role="button" data-toggle="collapse" href="#collapse_1"
                               aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.chinh_sua_thong_tin_khoa_dao_tao')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-3 mb-2">
                                        <div class="card">
                                            <div href="" class="image-box ratio-16-9"
                                                 v-if="course.avatar !=null && course.avatar.length > 0">
                                                <img :src="course.avatar" class="image"/>
                                            </div>
                                            <div class="card-body">
                                                <input type="file" ref="file" name="file" class="dropify"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-9">
                                        <form action="" class="form-row">
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.ma_khoa_hoc')}} *</label>
                                                <input v-model="course.shortname" type="text" id="inputText1-1"
                                                       :placeholder="trans.get('keys.nhap_ma')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.shortname"
                                                       class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText6">{{trans.get('keys.ten_khoa_hoc')}} *</label>
                                                <input v-model="course.fullname" type="text" id="inputText6"
                                                       :placeholder="trans.get('keys.nhap_ten_khoa_hoc')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.fullname"
                                                       class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <!--                                        <div class="col-4 form-group">-->
                                            <!--                                            <label for="inputText1-1">Danh mục khóa học *</label>-->
                                            <!--                                            <select v-model="course.category" class="form-control" id="category_id">-->
                                            <!--                                                <option value="">Chọn danh mục khóa học</option>-->
                                            <!--                                                <option v-for="cate in categories" :value="cate.id">-->
                                            <!--                                                    {{cate.category_name}}-->
                                            <!--                                                </option>-->
                                            <!--                                            </select>-->
                                            <!--                                        </div>-->
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}} *</label>
                                                <input v-model="course.pass_score" type="number" id="inputText1-2"
                                                       :placeholder="trans.get('keys.vi_du')+': 50'"
                                                       class="form-control mb-4">
                                                <label v-if="!course.pass_score"
                                                       class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.thoi_gian_du_kien')}}
                                                    *</label>
                                                <input v-model="course.estimate_duration" id="estimate_duration"
                                                       type="number"
                                                       :placeholder="trans.get('keys.nhap_so_gio_can_thiet')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.estimate_duration"
                                                       class="required text-danger estimate_duration_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText6">{{trans.get('keys.thoi_gian_bat_dau')}}
                                                    *</label>
                                                <input v-model="course.startdate"
                                                       type="datetime-local"
                                                       id="inputText7"
                                                       class="form-control mb-4">
                                                <label v-if="!course.startdate"
                                                       class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText6">{{trans.get('keys.thoi_gian_ket_thuc')}}</label>
                                                <input v-model="course.enddate"
                                                       type="datetime-local"
                                                       id="inputText8"
                                                       class="form-control mb-4">
<!--                                                <label v-if="!course.enddate"-->
<!--                                                       class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                            </div>


                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText6">{{trans.get('keys.phong_hoc')}} *</label>
                                                <input v-model="course.course_place" type="text" id="inputText9"
                                                       :placeholder="trans.get('keys.nhap_phong_hoc')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.course_place"
                                                       class="required text-danger course_place_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText6">{{trans.get('keys.so_buoi_hoc_trong_khoa')}}
                                                    *</label>
                                                <input v-model="course.total_date_course" type="number"
                                                       id="inputText101"
                                                       :placeholder="trans.get('keys.so_buoi_hoc_trong_khoa')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.total_date_course"
                                                       class="required text-danger total_date_course_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="course_budget">{{trans.get('keys.chi_phi')}}</label>
                                                <input v-model="course.course_budget" id="course_budget" type="number"
                                                       step="0.01" :placeholder="trans.get('keys.nhap_chi_phi')"
                                                       class="form-control mb-4">
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <div class="d-sm-block pt-40" style="display:none;"></div>
                                                <input v-model="course.allow_register" type="checkbox"
                                                       style="width:20px; height:20px;"
                                                       id="inputText10">
                                                <label for="inputText10">{{trans.get('keys.cho_phep_hoc_vien_tu_dang_ky')}}</label>
                                            </div>

                                          <div class="col-12 form-group">
                                            <label for="inputText6">{{trans.get('keys.dia_chi_ip_cho_phep')}} (<label for="inputText6">{{trans.get('keys.cac_dia_dia_chi_ngan_cach_nhau_boi_dau_phay')}}</label>)</label>
                                            <input v-model="string_ip"
                                                   :placeholder="trans.get('keys.nhap_dia_chi_ip')" type="text"
                                                   class="form-control mb-4">
                                          </div>


                                            <div class="col-12 form-group">
                                                <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                                                <ckeditor v-model="course.summary" :config="editorConfig"></ckeditor>

                                                <!--                        <textarea-->
                                                <!--                          v-model="course.summary"-->
                                                <!--                          class="form-control"-->
                                                <!--                          rows="3"-->
                                                <!--                          id="article_ckeditor"-->
                                                <!--                          :placeholder="trans.get('keys.noi_dung')"></textarea>-->

                                            </div>
                                        </form>
                                        <div class="button-list text-right">
                                            <button type="button" @click="goBack()" class="btn btn-secondary btn-sm">
                                                {{trans.get('keys.huy')}}
                                            </button>
                                            <button @click="editCourse()" type="button" class="btn btn-primary btn-sm">
                                                {{trans.get('keys.sua')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <!--                        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2"-->
                            <!--                           aria-expanded="false"><i class="fal fa-upload mr-3"></i>Tải lên file Excel</a>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <enrol-teacher :course_id="course_id" :course_type="course_type"></enrol-teacher>
        <course-infra :course_id="course_id"></course-infra>
    </div>
</template>

<script>
    import CKEditor from 'ckeditor4-vue';
    import EnrolTeacher from './EnrolTeacherComponent'
    import CourseInfra from './CourseInfrastructureComponent'

    export default {
        components: {
            CKEditor,
            EnrolTeacher,
            CourseInfra
        },
        props: ['course_id'],
        data() {
            return {
                course: {
                    avatar: ''
                },
                string_ip: "",
                categories: [],
                language: this.trans.get('keys.language'),
                editorConfig: {
                    filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
                    // The configuration of the editor.
                    //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
                    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                    filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
                },
                course_type: 1
            }
        },
        methods: {
            setFileInput() {
                $('.dropify').dropify();
            },
            getCategories() {
                axios.post('/api/courses/get_list_category')
                    .then(response => {
                        this.categories = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            getCourseDetail() {
                axios.get('/api/courses/get_course_detail/' + this.course_id)
                    .then(response => {
                        this.course = response.data;

                        if(response.data.access_ip){
                            var js_ip = JSON.parse(response.data.access_ip);
                            js_ip['list_access_ip'].forEach(item => this.string_ip += item + ', ');
                            this.string_ip = this.string_ip.substr(0, this.string_ip.length - 2);
                        }

                        var startdate = new Date(response.data.startdate * 1000);

                        var ten = function (i) {
                            return (i < 10 ? '0' : '') + i;
                        };
                        var YYYY = startdate.getFullYear();
                        var MM = ten(startdate.getMonth() + 1);
                        var DD = ten(startdate.getDate());
                        var HH = ten(startdate.getHours());
                        var II = ten(startdate.getMinutes());

                        this.course.startdate = YYYY + '-' + MM + '-' + DD + 'T' + HH + ':' + II;

                       if(response.data.enddate){
                           var endate = new Date(response.data.enddate * 1000);

                           var YYYY_end = endate.getFullYear();
                           var MM_end = ten(endate.getMonth() + 1);
                           var DD_end = ten(endate.getDate());
                           var HH_end = ten(endate.getHours());
                           var II_end = ten(endate.getMinutes());

                           this.course.enddate = YYYY_end + '-' + MM_end + '-' + DD_end + 'T' + HH_end + ':' + II_end;
                       }else{
                           this.course.enddate = "";
                       }

                        this.course.pass_score = Math.floor(response.data.pass_score);

                        this.setFileInput();
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            editCourse() {
                if (!this.course.shortname) {
                    $('.shortname_required').show();
                    return;
                }
                if (!this.course.fullname) {
                    $('.fullname_required').show();
                    return;
                }

                if (!this.course.estimate_duration) {
                    $('.estimate_duration_required').show();
                    return;
                }

                if (!this.course.startdate) {
                    $('.startdate_required').show();
                    return;
                }
                // if (!this.course.enddate) {
                //     $('.enddate_required').show();
                //     return;
                // }
                if (!this.course.pass_score) {
                    $('.pass_score_required').show();
                    return;
                }

                if (!this.course.course_place) {
                    $('.course_place_required').show();
                    return;
                }

                if (!this.course.total_date_course) {
                    $('.total_date_course_required').show();
                    return;
                }

                var allow_reg = 0;
                if (this.course.allow_register) {
                    allow_reg = 1;
                }

                //var editor_data = CKEDITOR.instances.article_ckeditor.getData();

                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.course.fullname);
                this.formData.append('shortname', this.course.shortname);
                this.formData.append('estimate_duration', this.course.estimate_duration);
                this.formData.append('startdate', this.course.startdate);
                this.formData.append('enddate', this.course.enddate);
                this.formData.append('pass_score', this.course.pass_score);
                //this.formData.append('description', editor_data);
                this.formData.append('description', this.course.summary === null ? '' : this.course.summary);
                this.formData.append('is_end_quiz', 0);
                this.formData.append('course_place', this.course.course_place);
                this.formData.append('category_id', this.course.category);
                this.formData.append('total_date_course', this.course.total_date_course);
                this.formData.append('allow_register', allow_reg);
                this.formData.append('offline', 1);//la khoa hoc tap trung
                this.formData.append('course_budget', this.course.course_budget);

                axios.post('/api/courses/update/' + this.course_id, this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        var language = this.language;
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                            this.$router.push({name: 'CourseConcentrateIndex'});
                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](this.trans.get('keys.loi_he_thong'), this.trans.get('keys.that_bai'));
                    });
            },
            goBack() {
                this.$router.push({name: 'CourseConcentrateIndex'});
            }

        },
        mounted() {
            // this.getCategories();
            this.getCourseDetail();
        }
    }
</script>

<style scoped>

</style>
