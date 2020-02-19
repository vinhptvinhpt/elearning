<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link :to="{ name: 'CourseConcentrateIndex' }">
                {{ trans.get('keys.khoa_dao_tao_tap_trung') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.tao_moi_khoa_dao_tao_tap_trung') }}</li>
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
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_khoa_dao_tao_tap_trung')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-lg-3 mb-2">
                    <div class="card">
                      <div href="" class="image-box ratio-16-9" v-if="avatar.length > 0">
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
                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText1-1">{{trans.get('keys.ma_khoa_hoc')}} *</label>
                        <input v-model="shortname" type="text" id="inputText1-1"
                               :placeholder="trans.get('keys.nhap_ma')"
                               class="form-control mb-4">
                        <label v-if="!shortname"
                               class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText6">{{trans.get('keys.ten_khoa_hoc')}} *</label>
                        <input v-model="fullname" type="text" id="inputText6"
                               :placeholder="trans.get('keys.nhap_ten_khoa_hoc')"
                               class="form-control mb-4">
                        <label v-if="!fullname" class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <!--                                        <div class="col-4 form-group">-->
                      <!--                                            <label for="inputText1-1">Danh mục khóa học *</label>-->
                      <!--                                            <select v-model="category_id" class="form-control" id="category_id">-->
                      <!--                                                <option value="">Chọn danh mục khóa học</option>-->
                      <!--                                                <option v-for="cate in categories" :value="cate.id">-->
                      <!--                                                    {{cate.category_name}}-->
                      <!--                                                </option>-->
                      <!--                                            </select>-->
                      <!--                                        </div>-->
                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}} *</label>
                        <input v-model="pass_score" type="number" id="inputText1-2"
                               :placeholder="trans.get('keys.vi_du')+': 50'"
                               class="form-control mb-4">
                        <label v-if="!pass_score"
                               class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_bat_dau')}} *</label>
                        <input v-model="startdate" placeholder="mm/dd/YYYY" type="datetime-local"
                               id="inputText7"
                               class="form-control mb-4">
                        <label v-if="!startdate"
                               class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_ket_thuc')}} *</label>
                        <input v-model="enddate" placeholder="mm/dd/YYYY" type="datetime-local"
                               id="inputText8"
                               class="form-control mb-4">
                        <label v-if="!enddate" class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText6">{{trans.get('keys.phong_hoc')}} *</label>
                        <input v-model="course_place" type="text" id="inputText9"
                               :placeholder="trans.get('keys.nhap_phong_hoc')"
                               class="form-control mb-4">
                        <label v-if="!course_place"
                               class="required text-danger course_place_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText6">{{trans.get('keys.so_buoi_hoc_trong_khoa')}} *</label>
                        <input v-model="total_date_course" type="number" id="inputText101"
                               :placeholder="trans.get('keys.so_buoi_hoc_trong_khoa')"
                               class="form-control mb-4">
                        <label v-if="!total_date_course"
                               class="required text-danger total_date_course_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <div class="d-sm-block pt-40" style="display:none;"></div>
                        <input v-model="allow_register" type="checkbox"
                               style="width:20px; height:20px;"
                               id="inputText10">
                        <label for="inputText10">{{trans.get('keys.cho_phep_hoc_vien_tu_dang_ky')}}</label>
                      </div>

                      <div class="col-12 form-group">
                        <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                        <textarea v-model="description" class="form-control" rows="10"
                                  id="article_ckeditor"
                                  :placeholder="trans.get('keys.noi_dung')"></textarea>
                      </div>
                    </form>
                    <div class="button-list text-right">
                      <button type="button" @click="goBack()" class="btn btn-secondary btn-sm">{{trans.get('keys.huy')}}</button>
                      <button @click="createCourse()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.tao')}}
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
        data() {
            return {
                fullname: '',
                shortname: '',
                startdate: '',
                enddate: '',
                pass_score: '',
                description: '',
                course_place: '',
                avatar: '',
                category_id: '',
                categories: [],
                allow_register: 1,
                total_date_course: '',
                language : this.trans.get('keys.language')
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
            getCategories() {
                axios.post('/api/courses/get_list_category')
                    .then(response => {
                        this.categories = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

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

                if (!this.course_place) {
                    $('.course_place_required').show();
                    return;
                }

                if (!this.total_date_course) {
                    $('.total_date_course_required').show();
                    return;
                }

                var allow_reg = 0;
                if (this.allow_register) {
                    allow_reg = 1;
                }

                var editor_data = CKEDITOR.instances.article_ckeditor.getData();
                this.formData = new FormData();
                this.formData.append('file', this.$refs.file.files[0]);
                this.formData.append('fullname', this.fullname);
                this.formData.append('shortname', this.shortname);
                this.formData.append('startdate', this.startdate);
                this.formData.append('enddate', this.enddate);
                this.formData.append('pass_score', this.pass_score);
                this.formData.append('description', editor_data);
                this.formData.append('course_place', this.course_place);
                this.formData.append('is_end_quiz', 0);
                this.formData.append('category_id', 5);
                this.formData.append('total_date_course', this.total_date_course);// truyền giá trị để nhận biết đây không phải khóa học tập trung
                this.formData.append('allow_register', allow_reg);
                this.formData.append('sample', 0);// truyền giá trị để nhận biết đây không phải khóa học mẫu

                axios.post('/api/courses/create', this.formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                })
                    .then(response => {
                        var language =  this.language;
                        if (response.data.status) {
                            swal({
                                    title: response.data.message,
                                    // text: response.data.message,
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }
                                , function () {
                                    this.$router.push({ name: 'CourseEnrol', params: {id: response.data.otherData, come_from: 'offline'} });
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
               this.$router.push({ name: 'CourseConcentrateIndex' });
            }
        },
        mounted() {
            // this.getCategories();
        }
    }
</script>

<style scoped>

</style>
