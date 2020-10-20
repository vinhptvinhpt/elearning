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
                                                    <input type="file" ref="file" name="file" class="dropify"/>
                                                </p>
                                                <div v-if="Object.entries(last_update).length !== 0"
                                                     class="mt-3 last-edited">
                                                    {{trans.get('keys.cap_nhat_lan_cuoi')}}
                                                    <hr>
                                                    <p>{{trans.get('keys.nguoi_cap_nhat')}}: <span
                                                            class="last-edited-text">{{last_update.user_fullname}}</span>
                                                    </p>
                                                    <p>{{trans.get('keys.vao_luc')}}: <span
                                                            class="last-edited-text">{{last_update.updated_at}}</span>
                                                    </p>
                                                </div>
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
                                                <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}}</label>
                                                <input v-model="course.pass_score" type="number" id="pass_score"
                                                       :placeholder="trans.get('keys.vi_du')+': 50'"
                                                       class="form-control mb-4" min="0">
                                                <!--                        <label v-if="!course.pass_score" class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="estimate_duration">{{trans.get('keys.thoi_gian_du_kien')}}
                                                    (h)
                                                    *</label>
                                                <input v-model="course.estimate_duration" id="estimate_duration"
                                                       type="number"
                                                       :placeholder="trans.get('keys.nhap_so_gio_can_thiet')"
                                                       class="form-control mb-4">
                                                <label v-if="!course.estimate_duration"
                                                       class="required text-danger estimate_duration_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label>{{trans.get('keys.thoi_gian_bat_dau')}} *</label>
                                                <!--                        <date-picker v-model="course.startdate" :config="options"-->
                                                <!--                                     :placeholder="trans.get('keys.ngay_bat_dau')"></date-picker>-->
                                                <input v-model="course.startdate" type="date"
                                                       id="inputText7"
                                                       class="form-control mb-4">
                                                <!--                        <input v-model="course.startdate" type="datetime-local" class="form-control mb-4">-->
                                                <label v-if="!course.startdate"
                                                       class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label>{{trans.get('keys.thoi_gian_ket_thuc')}}</label>
                                                <input v-model="course.enddate" type="date"
                                                       id="inputText8"
                                                       class="form-control mb-4">
                                                <!--                        <date-picker v-model="course.enddate" :config="options"-->
                                                <!--                                     :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>-->
                                                <!--                        <input v-model="course.enddate" type="datetime-local" class="form-control mb-4">-->
                                                <!--                        <label v-if="!course.enddate" class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="course_budget">{{trans.get('keys.chi_phi')}} ($)</label>
                                                <input v-model="course.course_budget" id="course_budget" type="number"
                                                       step="0.01" :placeholder="trans.get('keys.nhap_chi_phi')"
                                                       class="form-control mb-4">
                                                <!--                                              <label v-if="!course.course_budget"-->
                                                <!--                                                     class="required text-danger course_budget_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group">
                                                <label for="is_toeic">{{trans.get('keys.toeic_course')}}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="is_toeic"
                                                           :checked="course.is_toeic==1?true:false"
                                                           v-model="course.is_toeic">
                                                    <label v-if="course.is_toeic == 1" class="custom-control-label"
                                                           for="is_toeic">Yes</label>
                                                    <label v-else class="custom-control-label" for="is_toeic">No</label>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group d-none">
                                                <input v-model="course.allow_register" type="checkbox"
                                                       style="width:20px; height:20px;"
                                                       id="inputText9">
                                                <label for="inputText9">{{trans.get('keys.cho_phep_hoc_vien_tu_dang_ky')}}</label>
                                            </div>

                                            <div class="col-md-4 col-sm-6 form-group d-none" id="is_end_quiz">
                                                <input v-model="course.is_end_quiz" type="checkbox" id="inputText10"
                                                       style="width:20px; height:20px;">
                                                <label for="inputText10">{{trans.get('keys.khoa_hoc_lam_bai_kiem_tra')}}</label>
                                            </div>

                                            <div class="col-12 form-group">
                                                <label for="inputText6">{{trans.get('keys.dia_chi_ip_cho_phep')}}
                                                    (<label for="inputText6">{{trans.get('keys.cac_dia_dia_chi_ngan_cach_nhau_boi_dau_phay')}}</label>)</label>
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
                                            <button v-if="slug_can('tms-educate-exam-online-edit')"
                                                    @click="editCourse()" type="button"
                                                    class="btn btn-primary btn-sm">
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
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        components: {
            CKEditor,
            EnrolTeacher,
            datePicker
        },
        props: ['course_id', 'slugs'],
        data() {
            return {
                course: {
                    avatar: '',
                    summary: ''
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
                last_update: {},
                options: {
                    //http://eonasdan.github.io/bootstrap-datetimepicker/Options/
                    format: 'DD-MM-YYYY hh:mm A',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                    date: 'moment'
                }
            }
            this.course.startdate = this.parseDateFromTimestamp(response.data.startdate);
            if (response.data.enddate) {
              this.course.enddate = this.parseDateFromTimestamp(response.data.enddate);
            } else {
              this.course.enddate = "";
            }
            if (response.data.pass_score)
              //this.course.pass_score = Math.floor(response.data.pass_score);
              this.course.pass_score = parseFloat(response.data.pass_score);

            if (this.course.category === 3) {
              //$('#pass_score').attr("disabled", true);
              //$('#is_end_quiz').show();
            } else {
              // if (this.course.is_toeic === 1) {
              //   $('#pass_score').attr("disabled", true);
              // } else {
              //   $('#pass_score').attr("disabled", false);
              // }

              //$('#pass_score').attr("disabled", false);
              //$('#is_end_quiz').hide();
            }
            //Convert text-area to ck editor
            this.setEditor();

            //Set last update
            this.last_update.user_id = response.data.last_modify_user;
            this.last_update.user_fullname = response.data.username;
            this.last_update.updated_at = response.data.last_modify_time;

          })
          .catch(error => {
            console.log(error.response.data);
          });

      },
      // getLastUpdated() {
      //   axios.get('/api/courses/get_last_update/' + this.course_id)
      //     .then(response => {
      //       if (response.data.last) {
      //         this.last_update.user_id = response.data.last.userid;
      //         this.last_update.user_fullname = response.data.last.user_detail.fullname;
      //         this.last_update.updated_at = response.data.last.created_at;
      //       }
      //     })
      //     .catch(error => {
      //       console.log(error);
      //     });
      // },
      parseDateFromTimestamp(timestamp) {
        let ten = function (i) {
          return (i < 10 ? '0' : '') + i;
        };
        let jstimestamp = new Date(timestamp * 1000);
        let YYYY = jstimestamp.getFullYear();
        let MM = ten(jstimestamp.getMonth() + 1);
        let DD = ten(jstimestamp.getDate());
        let HH = ten(jstimestamp.getHours());
        let II = ten(jstimestamp.getMinutes());
        return YYYY + '-' + MM + '-' + DD;
        // return MM + '/' + DD + '/' + YYYY;
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

        //validate positive number
        var rePosNum = /^$|^([0]{1}.{1}[0-9]+|[1-9]{1}[0-9]*.{1}[0-9]+|[0-9]+|0)$/;

        if(this.course.pass_score && !rePosNum.test(this.course.pass_score)){
          toastr['error'](this.trans.get('keys.dinh_dang_du_lieu_khong_hop_le') + '( ' + this.trans.get('keys.pass_score') + ' )', this.trans.get('keys.that_bai'));
          return;
        }

        if(!rePosNum.test(this.course.estimate_duration) || this.course.estimate_duration <= 0) {
          toastr['error'](this.trans.get('keys.dinh_dang_du_lieu_khong_hop_le') + '( ' + this.trans.get('keys.estimate_duration') + ' )', this.trans.get('keys.that_bai'));
          return;
        }

        if(!rePosNum.test(this.course.course_budget)) {
          toastr['error'](this.trans.get('keys.dinh_dang_du_lieu_khong_hop_le') + '( ' + this.trans.get('keys.course_budget') + ' )', this.trans.get('keys.that_bai'));
          return;
        }

        // if (!this.course.course_budget) {
        //   $('.course_budget_required').show();
        //   return;
        // }

        // if (!this.course.enddate) {
        //     $('.enddate_required').show();
        //     return;
        // }

        // if (!this.course.pass_score && this.course.category != 3) {
        //     $('.pass_score_required').show();
        //     return;
        // }

        var allow_reg = 0;
        if (this.course.allow_register) {
          allow_reg = 1;
        }

        var quiz_test = 0;
        if (this.course.is_end_quiz) {
          quiz_test = 1;
        }

        if(this.course.pass_score == null)
          this.course.pass_score = '';

        if(this.course.enddate == null)
          this.course.enddate = '';

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
        this.formData.append('description', this.course.summary == null ? '' : this.course.summary);
        this.formData.append('category_id', this.course.category);
        this.formData.append('course_place', '');
        this.formData.append('is_end_quiz', quiz_test);
        this.formData.append('total_date_course', 0);// truyền giá trị để nhận biết đây không phải khóa học tập trung
        this.formData.append('allow_register', allow_reg);
        this.formData.append('offline', 0); //ko phai khoa hoc tap trung
        this.formData.append('course_budget', this.course.course_budget ? this.course.course_budget : '');
        this.formData.append('access_ip', this.string_ip);
        var is_toeic = this.course.is_toeic ? 1 : 0;
        this.formData.append('is_toeic', is_toeic);

        let current_pos = this;
        axios.post('/api/courses/update/' + this.course_id, this.formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
        })
          .then(response => {
            var language = this.language;
            if (response.data.status) {
              toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
              this.$router.push({name: 'CourseIndex', params: {back_page: '1'}});
            } else {
              if (response.data.otherData) {
                toastr['error'](response.data.otherData.message, current_pos.trans.get('keys.that_bai'));
              } else {
                toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
              }
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          });
      },
      goBack() {
        this.$router.push({name: 'CourseIndex', params: {back_page: '1'}});
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
      //this.getLastUpdated();
    },
    updated() {
      this.setFileInput();
    }
  }
</script>

<style scoped>

</style>
