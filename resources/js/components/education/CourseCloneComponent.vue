<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
            </li>
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
                      <div class="card-body">
                        <input type="file" ref="file" name="file" class="dropify"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-9">
                    <form action="" class="form-row">
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText1-1">{{trans.get('keys.danh_sach_thu_vien_khoa_hoc')}}
                          *</label>
                        <select v-model="sample" @change="onChange()" class="form-control"
                                id="sample">
                          <option value="">{{trans.get('keys.chon_thu_vien_khoa_hoc')}}
                          </option>
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
                        <label v-if="!fullname"
                               class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
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

                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="estimate_duration">{{trans.get('keys.thoi_gian_du_kien')}} (h)
                          *</label>
                        <input v-model="estimate_duration" id="estimate_duration"
                               :placeholder="trans.get('keys.nhap_so_gio_can_thiet')"
                               class="form-control mb-4">
                        <label v-if="!estimate_duration"
                               class="required text-danger estimate_duration_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>

                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_bat_dau')}}
                          *</label>
                        <input v-model="startdate" type="date"
                               id="inputText7"
                               class="form-control mb-4">
                        <label v-if="!startdate"
                               class="required text-danger startdate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText6">{{trans.get('keys.thoi_gian_ket_thuc')}}</label>
                        <input v-model="enddate" type="date"
                               id="inputText8"
                               class="form-control mb-4">
                        <!--                                                <label v-if="!enddate"-->
                        <!--                                                       class="required text-danger enddate_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                      </div>
                      <div class="col-sm-6 col-md-4 form-group">
                        <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}}</label>
                        <input v-model="pass_score" type="number" id="pass_score"
                               :placeholder="trans.get('keys.vi_du')+ ': 50'"
                               class="form-control mb-4">
                      </div>

                      <div class="col-md-4 col-sm-6 form-group">
                        <label for="course_budget">{{trans.get('keys.chi_phi')}} ($)</label>
                        <input v-model="course_budget" id="course_budget"
                               :placeholder="trans.get('keys.nhap_chi_phi')"
                               class="form-control mb-4">
                      </div>


                      <div class="col-md-4 col-sm-6 form-group" id="div-is_active">
                        <label for="is_toeic">{{trans.get('keys.toeic_course')}}</label>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="is_toeic"
                                 :checked="is_toeic==1?true:false" v-model="is_toeic">
                          <label v-if="is_toeic == 1" class="custom-control-label" for="is_toeic">Yes</label>
                          <label v-else class="custom-control-label" for="is_toeic">No</label>
                        </div>
                      </div>
                      <!--                                            <div class="col-sm-6 col-md-4 form-group">-->
                      <!--                                                <div class="pt-40 d-sm-block" style="display: none;">-->
                      <!--                                                </div>-->
                      <!--                                                <input v-model="allow_register" type="checkbox"-->
                      <!--                                                       style="width:20px; height:20px;"-->
                      <!--                                                       id="inputText9">-->
                      <!--                                                <label for="inputText9">{{trans.get('keys.cho_phep_hoc_vien_tu_dang_ky')}}</label>-->
                      <!--                                            </div>-->

                      <div class="col-sm-6 col-md-4 form-group" id="is_end_quiz"
                           style="display:none;">

                        <div class="pt-40 d-sm-block" style="display: none;">
                        </div>

                        <input v-model="is_end_quiz" type="checkbox"
                               style="width:20px; height:20px;">
                        <label>{{trans.get('keys.khoa_hoc_lam_bai_kiem_tra')}}</label>
                      </div>

                      <div class="col-12 form-group">
                        <label for="inputText6">{{trans.get('keys.dia_chi_ip_cho_phep')}} (<label for="inputText6">{{trans.get('keys.cac_dia_dia_chi_ngan_cach_nhau_boi_dau_phay')}}</label>)</label>
                        <input v-model="string_ip"
                               :placeholder="trans.get('keys.nhap_dia_chi_ip')" type="text"
                               class="form-control mb-4">
                      </div>


                      <div class="col-12 form-group">
                        <label for="inputText6">{{trans.get('keys.mo_ta')}}</label>
                        <ckeditor v-model="description" :config="editorConfig"></ckeditor>
                        <!--                        <textarea v-model="description" class="form-control" rows="3"-->
                        <!--                                  id="article_ckeditor"-->
                        <!--                                  :placeholder="trans.get('keys.noi_dung')"></textarea>-->
                      </div>
                    </form>
                    <div class="button-list text-right">
                      <button type="button" @click="goBack()" class="btn btn-secondary btn-sm">
                        {{trans.get('keys.huy')}}
                      </button>
                      <button @click="createCourse()" type="button"
                              class="btn btn-primary btn-sm">
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
  import CKEditor from 'ckeditor4-vue';

  export default {
    components: {
      CKEditor,
    },
    props: ['course_id'],
    data() {
      return {
        fullname: '',
        shortname: '',
        estimate_duration: '',
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
        is_toeic: false,
        string_ip: "",
        sample: '', //khóa học mẫu được chọn
        coursesamples: [], //danh sách khóa học mẫu,
        language: this.trans.get('keys.language'),
        course_budget: 0,
        editorConfig: {
          filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
          // The configuration of the editor.
          //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
          filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
          filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
          filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
          filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
        },
        existCodes: [],
      }
    },
    methods: {
      onChange: function () {
        this.libraryid = this.sample.shortname;
        this.fullname = this.sample.fullname;
        this.description = this.sample.description;
        this.avatar = this.sample.avatar;
        this.allow_register = this.sample.allow_register;
        this.total_date_course = this.sample.total_date_course;
        //CKEDITOR.instances.article_ckeditor.setData(this.description);
        this.description = this.sample.description;
        if (this.sample.pass_score)
          this.pass_score = Math.floor(this.sample.pass_score);
        this.is_end_quiz = this.sample.is_end_quiz;

        //this.shortname = this.convertToShortName(this.fullname);
        this.setShortName();
      },
      convertToShortName(words) {
        var text = '';
        words = words.split(' ');
        $.each(words, function () {
          text += this.substring(0, 1);
        });

        var d = new Date();
        var time = d.getHours() + '' + d.getMinutes() + '' + d.getSeconds() + '' + d.getDate() + '' + (d.getMonth() + 1) + '' + d.getFullYear();
        return this.replaceStringVN(text).toUpperCase() + time;
      },
      replaceStringVN(string) {
        string = string.replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a', string);
        string = string.replace(/[èéẹẻẽêềếệểễ]/g, 'e', string);
        string = string.replace(/[ìíịỉĩ]/g, 'i', string);
        string = string.replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o', string);
        string = string.replace(/[ùúụủũưừứựửữ]/g, 'u', string);
        string = string.replace(/[ỳýỵỷỹ]/g, 'y', string);
        string = string.replace(/[đ]/g, 'D', string);
        string = string.replace(/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/g, 'A', string);
        string = string.replace(/[ÈÉẸẺẼÊỀẾỆỂỄ]/g, 'E', string);
        string = string.replace(/[ÌÍỊỈĨ]/g, 'I', string);
        string = string.replace(/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/g, 'O', string);
        string = string.replace(/[ÙÚỤỦŨƯỪỨỰỬỮ]/g, 'U', string);
        string = string.replace(/[ỲÝỴỶỸ]/g, 'Y', string);
        string = string.replace(/[Đ]/g, 'D', string);

        string = string.replace(' ', '', string);

        return string;
      },
      onChangeCate(event) {
        if (event.target.value == 3) {
          $('#pass_score').attr("disabled", true);
          $('#is_end_quiz').show();
        } else {
          // if (this.is_toeic == 1) {
          //   $('#pass_score').attr("disabled", true);
          // } else {
          //   $('#pass_score').attr("disabled", false);
          // }
          $('#pass_score').attr("disabled", false);
          $('#is_end_quiz').hide();
        }

        if (event.target.value == 5) {
          this.is_toeic = false;
          // $('#pass_score').attr("disabled", false);
          $('#div-is_active').hide();
        } else {
          $('#div-is_active').show();
        }
      },
      onChangeToeic() {
        if (this.is_toeic == 1) {
          $('#pass_score').attr("disabled", true);
        } else {
          if (this.category_id == 3) {
            $('#pass_score').attr("disabled", true);
          } else {
            $('#pass_score').attr("disabled", false);
          }
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
            if (response.data.pass_score)
              this.pass_score = Math.floor(response.data.pass_score);
            this.shortname = response.data.shortname;
            this.fullname = response.data.fullname;
            this.description = response.data.description;
            this.allow_register = response.data.allow_register;
            this.total_date_course = response.data.total_date_course;
            this.is_end_quiz = response.data.is_end_quiz;
            //CKEDITOR.instances.article_ckeditor.setData(this.description);
            this.description = response.data.description;
            this.avatar = response.data.avatar;
          })
          .catch(error => {
            console.log(error);
          });

      },
      createCourse() {

        if (!this.sample) {
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

        if (!this.estimate_duration) {
          $('.estimate_duration_required').show();
          return;
        }

        if (!this.startdate) {
          $('.startdate_required').show();
          return;
        }

        //validate positive number
        var rePosNum = /^([0]{1}.{1}[0-9]+|[1-9]{1}[0-9]*.{1}[0-9]+|[0-9]+|0)$/;

        if (!rePosNum.test(this.estimate_duration)) {
          toastr['error'](this.trans.get('keys.dinh_dang_du_lieu_khong_hop_le') + '( ' + this.trans.get('keys.estimate_duration') + ' )', this.trans.get('keys.that_bai'));
          return;
        }

        if (!rePosNum.test(this.pass_score)) {
          toastr['error'](this.trans.get('keys.dinh_dang_du_lieu_khong_hop_le') + '( ' + this.trans.get('keys.pass_score') + ' )', this.trans.get('keys.that_bai'));
          return;
        }


        if (!rePosNum.test(this.course_budget)) {
          toastr['error'](this.trans.get('keys.dinh_dang_du_lieu_khong_hop_le') + '( ' + this.trans.get('keys.course_budget') + ' )', this.trans.get('keys.that_bai'));
          return;
        }

        // if (!this.enddate) {
        //     $('.enddate_required').show();
        //     return;
        // }

        // if (this.is_toeic == 0) {
        //   if (!this.pass_score) {
        //     $('.pass_score_required').show();
        //     return;
        //   }
        // }

        var allow_reg = 0;
        if (this.allow_register) {
          allow_reg = 1;
        }

        var quiz_test = 0;
        if (this.is_end_quiz) {
          quiz_test = 1;
        }

        if(this.pass_score == null)
          this.pass_score = '';

        //var editor_data = CKEDITOR.instances.article_ckeditor.getData();

        this.formData = new FormData();
        this.formData.append('file', this.$refs.file.files[0]);
        this.formData.append('fullname', this.fullname);
        this.formData.append('course_avatar', this.avatar);
        this.formData.append('shortname', this.shortname);
        this.formData.append('startdate', this.startdate);
        this.formData.append('enddate', this.enddate);
        this.formData.append('pass_score', this.pass_score);
        //this.formData.append('description', editor_data);
        this.formData.append('description', this.description == null ? '' : this.description);
        this.formData.append('category_id', this.category_id);
        this.formData.append('is_end_quiz', quiz_test);
        this.formData.append('total_date_course', this.total_date_course);
        this.formData.append('allow_register', allow_reg);
        this.formData.append('sample', 0);// truyền giá trị để nhận biết đây không phải khóa học mẫu
        this.formData.append('estimate_duration', this.estimate_duration);
        this.formData.append('course_budget', this.course_budget);
        this.formData.append('access_ip', this.string_ip);
        var is_toeic = this.is_toeic ? 1 : 0;
        this.formData.append('is_toeic', is_toeic);

        let current_pos = this;
        let loader = $('.preloader-it');
        loader.fadeIn();

        axios.post('/api/courses/clone', this.formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
        })
          .then(response => {
            loader.fadeOut();
            if (response.data.status) {
              toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
              //khóa học tập trung
              if (current_pos.category_id == 5)
                this.$router.push({name: 'CourseConcentrateIndex'});
              else
                this.$router.push({name: 'CourseIndex'});
            } else {
              toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            loader.fadeOut();
            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          });
      },
      goBack() {
        // window.location.href = '/education/course/list';
        // window.history.back();
        this.$router.push({name: 'SampleCourseIndex', params: {back_page: '1'}});

      },
      getExistedCodes() {
        axios.post('/api/courses/get_existed_codes', {})
          .then(response => {
            let codes = [];
            response.data.forEach(function (cityItem) {
              codes.push(cityItem.shortname);
            });
            this.existCodes = codes;
          })
          .catch(error => {
            console.log(error);
          });
      },
      setShortName() {
        let codes = this.existCodes;
        let prefix = this.libraryid;
        let biggest = 0;
        let curPos = this;
        codes.forEach(function (item) {
          if (item.indexOf(prefix) !== -1) {
            let lastNumberCode = parseInt(curPos.getLastNumber(item));
            if (lastNumberCode > biggest) {
              biggest = lastNumberCode;
            }
          }
        });
        let nextNumber = biggest + 1;
        let append = this.composeAppend(nextNumber);
        this.shortname = prefix + '_ONL' + append;
      },
      getLastNumber(str) {
        let arr = str.split('_ONL');
        let reverse = arr.reverse();
        if (isNaN(reverse[0])) {
          return '0';
        } else {
          return reverse[0];
        }
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
      setFileInput() {
        $('.dropify').dropify();
      }
    },
    mounted() {
      this.getCourseSamples();
      this.getCategories();
      this.getCourseDetail();
      this.getExistedCodes();
    },
    updated() {
      this.setFileInput();
    }
  }
</script>

<style scoped>

</style>
