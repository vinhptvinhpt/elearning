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
            <li class="breadcrumb-item active">{{ trans.get('keys.tao_moi_khoa_dao_tao_online') }}</li>
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
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.tao_moi_khoa_dao_tao')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
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
                        <label v-if="!fullname"
                               class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText1-1">{{trans.get('keys.danh_muc_khoa_hoc')}}
                          *</label>
                        <select v-model="category_id" class="form-control" id="category_id"
                                @change="onChangeCate($event)">
                          <option value="">{{trans.get('keys.chon_danh_muc_khoa_hoc')}}
                          </option>
                          <option v-for="cate in categories" :value="cate.id">
                            {{cate.category_name}}
                          </option>
                        </select>
                        <label v-if="!category_id"
                               class="required text-danger category_id_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}} *</label>
                        <input v-model="pass_score" type="number" id="pass_score"
                               :placeholder="trans.get('keys.vi_du')+': 50'"
                               class="form-control mb-4">
                        <label v-if="!pass_score"
                               class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="estimate_duration">{{trans.get('keys.thoi_gian_du_kien')}} (h)
                          *</label>
                        <input v-model="estimate_duration" id="estimate_duration" type="number"
                               :placeholder="trans.get('keys.nhap_so_gio_can_thiet')"
                               class="form-control mb-4">
                        <label v-if="!estimate_duration"
                               class="required text-danger estimate_duration_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_bat_dau')}}
                          *</label>
                        <input v-model="startdate" type="date"
                               id="inputText7"
                               class="form-control mb-4">
                        <label v-if="!startdate"
                               class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_ket_thuc')}}</label>
                        <input v-model="enddate" type="date"
                               id="inputText8"
                               class="form-control mb-4">
                        <!--                                                <label v-if="!enddate"-->
                        <!--                                                       class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="course_budget">{{trans.get('keys.chi_phi')}} ($)</label>
                        <input v-model="course_budget" id="course_budget"
                               :placeholder="trans.get('keys.nhap_chi_phi')" type="number"
                               step="0.01"
                               class="form-control mb-4">
                        <!--                                              <label v-if="!course_budget"-->
                        <!--                                                     class="required text-danger course_budget_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                      </div>

                      <!--<div class="col-md-4 col-sm-6 form-group">
                          <input v-model="allow_register" type="checkbox"
                                 style="width:20px; height:20px;"
                                 id="inputText9">
                          <label for="inputText9">{{trans.get('keys.cho_phep_hoc_vien_tu_dang_ky')}}</label>
                      </div>-->

                      <div class="col-md-4 col-sm-6 form-group" id="is_end_quiz"
                           style="display:none;">
                        <input v-model="is_end_quiz" type="checkbox"
                               style="width:20px; height:20px;">
                        <label for="inputText9">{{trans.get('keys.khoa_hoc_lam_bai_kiem_tra')}}</label>
                      </div>

                      <div class="col-12 form-group">
                        <label for="inputText6">{{trans.get('keys.dia_chi_ip_cho_phep')}}
                          (<label
                            for="inputText6">{{trans.get('keys.cac_dia_dia_chi_ngan_cach_nhau_boi_dau_phay')}}</label>)</label>
                        <input v-model="access_ip"
                               :placeholder="trans.get('keys.nhap_dia_chi_ip')" type="text"
                               class="form-control mb-4">
                      </div>

                      <div class="col-12 form-group">
                        <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>

                        <ckeditor v-model="description" :config="editorConfig"></ckeditor>

                        <!--<textarea v-model="description" class="form-control" rows="10"
                                    id="article_ckeditor"
                                    :placeholder="trans.get('keys.noi_dung')"></textarea>-->

                      </div>
                    </form>
                    <div class="button-list text-right">
                      <button type="button" @click="goBack()" class="btn btn-secondary btn-sm">
                        {{trans.get('keys.huy')}}
                      </button>
                      <button v-if="slug_can('tms-educate-exam-online-add')" @click="createCourse()" type="button"
                              class="btn btn-primary btn-sm">{{trans.get('keys.tao')}}
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
    props: ['slugs'],
    components: {
      CKEditor
    },
    data() {
      return {
        fullname: '',
        shortname: '',
        estimate_duration: '',
        startdate: '',
        enddate: '',
        pass_score: '',
        description: '',
        category_id: '',
        categories: [],
        allow_register: 1,
        is_end_quiz: 0,
        language: this.trans.get('keys.language'),
        course_budget: 0,
        access_ip: "",
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
      slug_can(permissionName) {
        return this.slugs.indexOf(permissionName) !== -1;
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
      onChangeCate(event) {
        if (event.target.value == 3) {
          this.pass_score = 0;
          $('#pass_score').attr("disabled", true);
          $('#is_end_quiz').show();
        } else {
          $('#pass_score').attr("disabled", false);
          $('#is_end_quiz').hide();
        }
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
        if (!this.category_id) {
          $('.category_id_required').show();
          return;
        }
        if (!this.estimate_duration) {
          $('.estimate_duration_required').show();
          return;
        }
        if (!this.startdate) {
          $('.startdate_required').show();
          return;
        }

        // if (!this.course_budget) {
        //   $('.course_budget_required').show();
        //   return;
        // }
        // if (!this.enddate) {
        //     $('.enddate_required').show();
        //     return;
        // }
        if (!this.pass_score && this.category_id != 3) {
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

        //var editor_data = CKEDITOR.instances.article_ckeditor.getData();

        this.formData = new FormData();
        this.formData.append('file', this.$refs.file.files[0]);
        this.formData.append('fullname', this.fullname);
        this.formData.append('shortname', this.shortname);
        this.formData.append('startdate', this.startdate);
        this.formData.append('enddate', this.enddate);
        this.formData.append('pass_score', this.pass_score);
        //this.formData.append('description', editor_data);
        this.formData.append('description', this.description == null ? '' : this.description);
        this.formData.append('category_id', this.category_id);
        this.formData.append('course_place', '');
        this.formData.append('is_end_quiz', quiz_test);
        this.formData.append('total_date_course', 0);// truyền giá trị để nhận biết đây không phải khóa học tập trung
        this.formData.append('allow_register', allow_reg);
        this.formData.append('sample', 0);// truyền giá trị để nhận biết đây không phải khóa học mẫu
        this.formData.append('estimate_duration', this.estimate_duration);
        this.formData.append('course_budget', this.course_budget);
        this.formData.append('access_ip', this.access_ip);
        let current_pos = this;


        //console.log(this.formData);

        for (var formDataKey in this.formData) {
          console.log(formDataKey);
        }
        return;

        axios.post('/api/courses/create', this.formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
        })
          .then(response => {
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              this.$router.push({
                name: 'CourseEnrol',
                params: {id: response.data.otherData, come_from: 'online'}
              });
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          });
      },
      goBack() {
        this.$router.push({name: 'CourseIndex'});
      },
      hintCode() {
        axios.get('/api/courses/hint_code')
          .then(response => {
            if (response.data.status) {
              this.shortname = response.data.otherData;
            }
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },

      setFileInput() {
        $('.dropify').dropify();
      }
    },
    mounted() {
      this.getCategories();
      this.hintCode();
    },
    updated() {
      this.setFileInput();
    }
  }
</script>

<style scoped>

</style>
