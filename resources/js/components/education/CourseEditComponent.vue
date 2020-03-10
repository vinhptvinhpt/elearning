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
                            <router-link to="/tms/education/course/list">{{ trans.get('keys.khoa_dao_tao_online') }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_khoa_dao_tao_online')
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
                                                <p>
                                                    <input type="file" @change="previewImage()" ref="file" name="file"
                                                           class="dropify"/>
                                                </p>
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
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.danh_muc_khoa_hoc')}}
                                                    *</label>
                                                <select v-model="course.category" class="form-control" id="category_id"
                                                        @change="onChangeCate($event)">
                                                    <option value="">{{trans.get('keys.chon_danh_muc_khoa_hoc')}}
                                                    </option>
                                                    <option v-for="cate in categories" :value="cate.id">
                                                        {{cate.category_name}}
                                                    </option>
                                                </select>
                                                <label v-if="!course.category"
                                                       class="required text-danger category_id_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}} *</label>
                                                <input v-model="course.pass_score" type="number" id="pass_score"
                                                       :placeholder="trans.get('keys.vi_du')+': 50'"
                                                       class="form-control mb-4">
                                                <label v-if="!course.pass_score"
                                                       class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="estimate_duration">{{trans.get('keys.thoi_gian_du_kien')}}
                                                    *</label>
                                                <input v-model="course.estimate_duration" id="estimate_duration"
                                                       type="number"
                                                       :placeholder="trans.get('keys.nhap_so_gio_can_thiet')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.estimate_duration"
                                                       class="required text-danger estimate_duration_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText7">{{trans.get('keys.thoi_gian_bat_dau')}}
                                                    *</label>
                                                <input v-model="course.startdate"
                                                       type="date"
                                                       id="inputText7"
                                                       class="form-control mb-4">
                                                <label v-if="!course.startdate"
                                                       class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="inputText8">{{trans.get('keys.thoi_gian_ket_thuc')}}
                                                    *</label>
                                                <input v-model="course.enddate"
                                                       type="date"
                                                       id="inputText8"
                                                       class="form-control mb-4">
                                                <label v-if="!course.enddate"
                                                       class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="course_budget">{{trans.get('keys.chi_phi')}}</label>
                                                <input v-model="course.course_budget" id="course_budget" type="number"
                                                       step="0.01" :placeholder="trans.get('keys.nhap_chi_phi')"
                                                       class="form-control mb-4">
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <input v-model="course.allow_register" type="checkbox"
                                                       style="width:20px; height:20px;"
                                                       id="inputText9">
                                                <label for="inputText9">{{trans.get('keys.cho_phep_hoc_vien_tu_dang_ky')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group" id="is_end_quiz">
                                                <input v-model="course.is_end_quiz" type="checkbox" id="inputText10"
                                                       style="width:20px; height:20px;">
                                                <label for="inputText10">{{trans.get('keys.khoa_hoc_lam_bai_kiem_tra')}}</label>
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
                </div>
            </div>
        </div>
        <enrol-teacher :course_id="course_id"></enrol-teacher>
    </div>
</template>

<script>
    import CKEditor from 'ckeditor4-vue';
    import EnrolTeacher from './EnrolTeacherComponent'

    export default {
        components: {
            CKEditor,
            EnrolTeacher
        },
        props: ['course_id'],
        data() {
            return {
                course: {
                    avatar: ''
                },
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
                }
            }
        },
        methods: {
            previewImage: function (event) {
                var input = event.target;
                // Ensure that you have a file before attempting to read it
                if (input.files && input.files[0]) {
                    // create a new FileReader to read this image and convert to base64 format
                    var reader = new FileReader();
                    // Define a callback function to run, when FileReader finishes its job
                    reader.onload = (e) => {
                        // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
                        // Read image as base64 and set to imageData
                        this.course.avatar = e.target.result;
                    };
                    // Start the reader job - read file as a data url (base64 format)
                    reader.readAsDataURL(input.files[0]);
                }
            },
            onChangeCate(event) {
                if (event.target.value == 3) {
                    $('#pass_score').attr("disabled", true);
                } else {
                    $('#pass_score').attr("disabled", false);
                }
            },
            getCategories() {
                axios.post('/api/courses/get_list_category_edit')
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

                        var startdate = new Date(response.data.startdate * 1000);

                        var ten = function (i) {
                            return (i < 10 ? '0' : '') + i;
                        };
                        var YYYY = startdate.getFullYear();
                        var MM = ten(startdate.getMonth() + 1);
                        var DD = ten(startdate.getDate());

                        this.course.startdate = YYYY + '-' + MM + '-' + DD;

                        var endate = new Date(response.data.enddate * 1000);

                        var YYYY_end = endate.getFullYear();
                        var MM_end = ten(endate.getMonth() + 1);
                        var DD_end = ten(endate.getDate());

                        this.course.enddate = YYYY_end + '-' + MM_end + '-' + DD_end;

                        this.course.pass_score = Math.floor(response.data.pass_score);

                        if (this.course.category == 3) {
                            $('#pass_score').attr("disabled", true);
                            $('#is_end_quiz').show();
                        } else {
                            $('#pass_score').attr("disabled", false);
                            $('#is_end_quiz').hide();
                        }
                        //Convert text-area to ck editor
                        this.setEditor();
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

                if (!this.course.category) {
                    $('.category_id_required').show();
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

                if (!this.course.enddate) {
                    $('.enddate_required').show();
                    return;
                }

                if (!this.course.pass_score && this.course.category != 3) {
                    $('.pass_score_required').show();
                    return;
                }

                var allow_reg = 0;
                if (this.course.allow_register) {
                    allow_reg = 1;
                }

                var quiz_test = 0;
                if (this.course.is_end_quiz) {
                    quiz_test = 1;
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
                this.formData.append('description', this.course.summary);
                this.formData.append('category_id', this.course.category);
                this.formData.append('course_place', '');
                this.formData.append('is_end_quiz', quiz_test);
                this.formData.append('total_date_course', 0);// truyền giá trị để nhận biết đây không phải khóa học tập trung
                this.formData.append('allow_register', allow_reg);
                this.formData.append('offline', 0); //ko phai khoa hoc tap trung
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
                            this.$router.push({name: 'CourseIndex'});
                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](this.trans.get('keys.loi_he_thong'), this.trans.get('keys.that_bai'));
                    });
            },
            goBack() {
                this.$router.push({name: 'CourseIndex'});
            },
            setEditor() {
                // var CSRFToken = $('meta[name="csrf-token"]').attr('content');
                // var options = {
                //   filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                //   filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=' + CSRFToken,
                //   filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                //   filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token=' + CSRFToken
                // };
                // CKEDITOR.replace('article_ckeditor', options);
            },
            setFileInput() {
                $('.dropify').dropify();
            }
        },
        mounted() {
            this.getCategories();
            this.getCourseDetail();
        }
    }
</script>

<style scoped>

</style>
