<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.tao_moi_khoa_hoc_tu_thu_vien') }}</li>
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
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_khoa_hoc_tu_thu_vien')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-lg-3 mb-2">
                    <div class="card">
                      <div href="" class="image-box ratio-16-9" v-if="avatar !=null && avatar.length > 0">
                        <img :src="avatar" class="image"/>
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
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText1-1">{{trans.get('keys.danh_sach_thu_vien_khoa_hoc')}} *</label>
                        <select v-model="sample" @change="onChange()" class="form-control"
                                id="sample">
                          <option value="">{{trans.get('keys.chon_thu_vien_khoa_hoc')}}</option>
                          <option v-for="course in coursesamples" :value="course">
                            {{course.shortname}} - {{course.fullname}}
                          </option>
                        </select>
                        <label v-if="!sample"
                               class="required text-danger sample_id_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText123">{{trans.get('keys.ma_thu_vien')}} *</label>
                        <input v-model="libraryid" type="text" id="inputText123"
                               :placeholder="trans.get('keys.ma_thu_vien')"
                               class="form-control mb-4" disabled="disabled">
                        <label v-if="!libraryid"
                               class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText1-1">{{trans.get('keys.ma_khoa_hoc')}} *</label>
                        <input v-model="shortname" type="text" id="inputText1-1"
                               :placeholder="trans.get('keys.nhap_ma')"
                               class="form-control mb-4">
                        <label v-if="!shortname"
                               class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText6">{{trans.get('keys.ten_khoa_hoc')}} *</label>
                        <input v-model="fullname" type="text" id="inputText6"
                               :placeholder="trans.get('keys.nhap_ten_khoa_hoc')"
                               class="form-control mb-4">
                        <label v-if="!fullname" class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText1-1">{{trans.get('keys.danh_muc_khoa_hoc')}} *</label>
                        <select v-model="category_id" class="form-control" id="category_id"
                                @change="onChangeCate($event)">
                          <option value="">{{trans.get('keys.chon_danh_muc_khoa_hoc')}}</option>
                          <option v-for="cate in categories" :value="cate.id">
                            {{cate.category_name}}
                          </option>
                        </select>
                        <label v-if="!category_id"
                               class="required text-danger category_id_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_bat_dau')}} *</label>
                        <input v-model="startdate" placeholder="mm/dd/YYYY" type="date"
                               id="inputText7"
                               class="form-control mb-4">
                        <label v-if="!startdate"
                               class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_ket_thuc')}} *</label>
                        <input v-model="enddate" placeholder="mm/dd/YYYY" type="date"
                               id="inputText8"
                               class="form-control mb-4">
                        <label v-if="!enddate" class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}} *</label>
                        <input v-model="pass_score" type="number" id="pass_score"
                               :placeholder="trans.get('keys.vi_du')+ ': 50'"
                               class="form-control mb-4">
                        <label v-if="!pass_score"
                               class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <div class="pt-40 d-sm-block" style="display: none;">

                        </div>
                        <input v-model="allow_register" type="checkbox"
                               style="width:20px; height:20px;"
                               id="inputText9">
                        <label for="inputText9">{{trans.get('keys.cho_phep_hoc_vien_tu_dang_ky')}}</label>
                      </div>

                      <div class="col-sm-6 col-md-4 form-group" id="is_end_quiz" style="display:none;">
                        <div class="pt-40 d-sm-block" style="display: none;">

                        </div>
                        <input v-model="is_end_quiz" type="checkbox"
                               style="width:20px; height:20px;">
                        <label for="inputText9">{{trans.get('keys.khoa_hoc_lam_bai_kiem_tra')}}</label>
                      </div>
                      <div class="col-12 form-group">
                        <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                        <textarea v-model="description" class="form-control" rows="3"
                                  id="article_ckeditor"
                                  :placeholder="trans.get('keys.noi_dung')"></textarea>
                      </div>
                    </form>
                    <div class="button-list text-right">
                      <button type="button" @click="goBack()" class="btn btn-secondary btn-sm">
                        {{trans.get('keys.huy')}}
                      </button>
                      <button @click="createCourse()" type="button" class="btn btn-primary btn-sm">
                        {{trans.get('keys.tao')}}
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
    export default {
        props: ['course_id'],
        data() {
            return {
                fullname: '',
                shortname: '',
                startdate: '',
                enddate: '',
                pass_score: '',
                description: '',
                avatar: '',
                category_id: '',
                categories: [],
                total_date_course: '',
                is_end_quiz: 0,
                allow_register: 1,
                libraryid: 0,
                sample: '', //khóa học mẫu được chọn
                coursesamples: [], //danh sách khóa học mẫu,
                language: this.trans.get('keys.language')
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
                        this.avatar = e.target.result;
                    };
                    // Start the reader job - read file as a data url (base64 format)
                    reader.readAsDataURL(input.files[0]);
                }
            },
            onChange: function () {
                this.libraryid = this.sample.shortname;
                this.fullname = this.sample.fullname;
                this.description = this.sample.description;
                this.avatar = this.sample.avatar;
                this.allow_register = this.sample.allow_register;
                this.total_date_course = this.sample.total_date_course;
                CKEDITOR.instances.article_ckeditor.setData(this.description);
                this.pass_score = Math.floor(this.sample.pass_score);
                this.is_end_quiz = this.sample.is_end_quiz;
            },
            onChangeCate(event) {
                if (event.target.value == 3) {
                    $('#pass_score').attr("disabled", true);
                    $('#is_end_quiz').show();
                } else {
                    $('#pass_score').attr("disabled", false);
                    $('#is_end_quiz').hide();
                }
            },
            getCourseSamples() {
                axios.get('/api/courses/get_list_sample')
                    .then(response => {
                        this.coursesamples = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            getCategories() {
                axios.post('/api/courses/get_list_category_clone')
                    .then(response => {
                        this.categories = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            getCourseDetail() { //lấy thông tin khóa học mẫu trong trường hợp gọi từ giao diện danh sách khóa học mẫu
                if (this.course_id === 'new') {
                    return;
                }
                axios.get('/api/courses/get_course_detail/' + this.course_id)
                    .then(response => {
                        this.pass_score = Math.floor(response.data.pass_score);
                        this.shortname = response.data.shortname;
                        this.fullname = response.data.fullname;
                        this.description = response.data.description;
                        this.allow_register = response.data.allow_register;
                        this.total_date_course = response.data.total_date_course;
                        this.is_end_quiz = response.data.is_end_quiz;
                        CKEDITOR.instances.article_ckeditor.setData(this.description);
                        this.avatar = response.data.avatar;
                    })
                    .catch(error => {
                        console.log(error);
                    });

            },
            createCourse() {

                if(!this.sample){
                    $('.sample_id_required').show();
                    return;
                }
                if (!this.shortname) {
                    $('.shortname_required').show();
                    return;
                }
                if (!this.fullname) {
                    $('.fullname_required').show();
                    return;
                }

                if (!this.category_id) {
                    $('.category_id_required').show();
                    return;
                }

                if (!this.startdate) {
                    $('.startdate_required').show();
                    return;
                }
                if (!this.enddate) {
                    $('.enddate_required').show();
                    return;
                }
                if (!this.pass_score) {
                    $('.pass_score_required').show();
                    return;
                }

                var allow_reg = 0;
                if (this.allow_register) {
                    allow_reg = 1;
                }

                var quiz_test = 0;
                if (this.is_end_quiz) {
                    quiz_test = 1;
                }

                var editor_data = CKEDITOR.instances.article_ckeditor.getData();
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.fullname);
                this.formData.append('course_avatar', this.avatar);
                this.formData.append('shortname', this.shortname);
                this.formData.append('startdate', this.startdate);
                this.formData.append('enddate', this.enddate);
                this.formData.append('pass_score', this.pass_score);
                this.formData.append('description', editor_data);
                this.formData.append('category_id', this.category_id);
                this.formData.append('is_end_quiz', quiz_test);
                this.formData.append('total_date_course', this.total_date_course);
                this.formData.append('allow_register', allow_reg);
                this.formData.append('sample', 0);// truyền giá trị để nhận biết đây không phải khóa học mẫu

                axios.post('/api/courses/clone', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        if (response.data.status) {
                            var language = this.language;
                            swal({
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }
                                , function () {
                                    this.$router.push({ name: 'CourseIndex'});
                                }
                            );
                        } else {
                            swal({
                                title: response.data.message,
                                // text: response.data.message,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }
                    })
                    .catch(error => {
                        swal({
                            title: "Thông báo",
                            text: " Lỗi hệ thống.",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    });


            },
            goBack() {
                // window.location.href = '/education/course/list';
                // window.history.back();
                this.$router.push({ name: 'SampleCourseIndex'});

            }
        },
        mounted() {
            this.getCourseSamples();
            this.getCategories();
            this.getCourseDetail();
        }
    }
</script>

<style scoped>

</style>
