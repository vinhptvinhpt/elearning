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
                            <router-link to="/tms/education/course/course_sample">{{ trans.get('keys.thu_vien_khoa_hoc')
                                }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_thu_vien_khoa_hoc')
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
                               aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.chinh_sua_thu_vien_khoa_hoc')}}</a>
                        </div>
                        <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-3 mb-2">
                                        <div class="card">
                                            <div href="" class="image-box ratio-16-9" v-if="course.avatar.length > 0">
                                                <img :src="course.avatar" class="image"/>
                                            </div>
                                            <div class="card-body">
                                                <input type="file" ref="file" name="file" class="dropify"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-9">
                                        <form action="" class="form-row">
                                            <div class="col-sm-4 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.ma_thu_vien')}} *</label>
                                                <input v-model="course.shortname" type="text" id="inputText1-1"
                                                       :placeholder="trans.get('keys.nhap_ma_thu_vien')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.shortname"
                                                       class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <div class="col-sm-4 form-group">
                                                <label for="inputText6">{{trans.get('keys.ten_thu_vien')}} *</label>
                                                <input v-model="course.fullname" type="text" id="inputText6"
                                                       :placeholder="trans.get('keys.nhap_ten_thu_vien')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.fullname"
                                                       class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <!--                <div class="col-4 form-group">-->
                                            <!--                    <label for="inputText1-1">Danh mục khóa học *</label>-->
                                            <!--                    <select v-model="course.category" class="form-control" id="category_id">-->
                                            <!--                        <option value="">Chọn danh mục khóa học</option>-->
                                            <!--                        <option v-for="cate in categories" :value="cate.id">{{cate.category_name}}</option>-->
                                            <!--                    </select>-->
                                            <!--                </div>-->
                                            <div class="col-sm-4 form-group">
                                                <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}} *</label>
                                                <input v-model="course.pass_score" type="number" id="inputText1-2"
                                                       :placeholder="trans.get('keys.vi_du')+': 50'"
                                                       class="form-control mb-4">
                                                <label v-if="!course.pass_score"
                                                       class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>
                                            <!--                <div class="col-4 form-group">-->
                                            <!--                    <label for="inputText6">Thời gian bắt đầu *</label>-->
                                            <!--                    <input v-model="course.startdate" type="text" id="inputText7"-->
                                            <!--                           class="form-control mb-4">-->
                                            <!--                    <label v-if="!course.startdate" class="required text-danger startdate_required hide">Trường bắt buộc-->
                                            <!--                        phải-->
                                            <!--                        nhập.</label>-->
                                            <!--                </div>-->
                                            <!--                <div class="col-4 form-group">-->
                                            <!--                    <label for="inputText6">Thời gian kết thúc *</label>-->
                                            <!--                    <input v-model="course.enddate" type="text" id="inputText8"-->
                                            <!--                           class="form-control mb-4">-->
                                            <!--                    <label v-if="!course.enddate" class="required text-danger enddate_required hide">Trường bắt buộc-->
                                            <!--                        phải-->
                                            <!--                        nhập.</label>-->
                                            <!--                </div>-->


                                            <!--                <div class="col-4 form-group">-->
                                            <!--                    <label for="inputText1-1">Chi phí trả cho giáo viên-->
                                            <!--                        (vnđ) *</label>-->
                                            <!--                    <input v-model="fee_teacher" type="text" id="inputText1-3"-->
                                            <!--                           placeholder="Ví dụ: 5.000.000" class="form-control mb-4">-->
                                            <!--                    <label v-if="!fee_teacher" class="required text-danger fee_teacher_required hide">Trường bắt buộc-->
                                            <!--                        phải-->
                                            <!--                        nhập.</label>-->
                                            <!--                </div>-->
                                            <div class="col-12 form-group">
                                                <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                                                <!--                        <textarea v-model="course.summary" class="form-control" rows="3"-->
                                                <!--                                  id="article_ckeditor"-->
                                                <!--                                  :placeholder="trans.get('keys.noi_dung')"></textarea>-->
                                                <ckeditor v-model="course.summary" :config="editorConfig"></ckeditor>
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
    </div>


</template>

<script>
    import CKEditor from 'ckeditor4-vue';

    export default {
        props: ['course_id'],
        components: {
            CKEditor
        },
        data() {
            return {
                course: {
                    avatar: ''
                },

                editorConfig: {
                    filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
                    // The configuration of the editor.
                    //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
                    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                    filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
                },
            }
        },
        methods: {
            getCourseDetail() {
                axios.get('/api/courses/get_course_detail/' + this.course_id)
                    .then(response => {
                        this.course = response.data;

                        this.course.pass_score = Math.floor(response.data.pass_score);
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
                // if (!this.course.startdate) {
                //     $('.startdate_required').show();
                //     return;
                // }
                // if (!this.course.enddate) {
                //     $('.enddate_required').show();
                //     return;
                // }
                if (!this.course.pass_score) {
                    $('.pass_score_required').show();
                    return;
                }
                // var editor_data = CKEDITOR.instances.article_ckeditor.getData();

                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.course.fullname);
                this.formData.append('shortname', this.course.shortname);
                this.formData.append('shortname', this.course.shortname);
                this.formData.append('startdate', this.course.startdate);
                this.formData.append('pass_score', this.course.pass_score);
                this.formData.append('description', this.course.summary);
                this.formData.append('category_id', this.course.category);
                this.formData.append('total_date_course', 0);// truyền giá trị để nhận biết đây không phải khóa học tập trung
                this.formData.append('allow_register', 1);
                this.formData.append('is_end_quiz', 0);
                this.formData.append('course_place', '');
                this.formData.append('offline', 0);//ko phai khoa hoc tap trung

                axios.post('/api/courses/update/' + this.course_id, this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, this.trans.get('keys.thong_bao'));
                            this.$router.push({name: 'SampleCourseIndex'});

                        } else {
                            toastr['error'](response.data.message, this.trans.get('keys.thong_bao'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](this.trans.get('keys.loi_he_thong'), this.trans.get('keys.thong_bao'));
                    });
            },
            goBack() {
                this.$router.push({name: 'SampleCourseIndex'});
            },
            setFileInput() {
                $('.dropify').dropify();
            }
        },
        mounted() {
            this.getCourseDetail();
            this.setFileInput();
        }
    }
</script>

<style scoped>

</style>
