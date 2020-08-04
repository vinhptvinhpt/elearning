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
              <router-link to="/tms/education/course/course_sample">{{ trans.get('keys.quan_tri_thu_vien_khoa_hoc') }}</router-link>
            </li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_thu_vien_khoa_hoc')}}</h5>
          <div class="row mb-4">
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
                            <div class="col-sm-4 form-group">
                              <label for="inputText1-1">{{trans.get('keys.ma_thu_vien')}}
                                *</label>
                              <input v-model="shortname" type="text" id="inputText1-1"
                                     :placeholder="trans.get('keys.nhap_ma_thu_vien')"
                                     class="form-control mb-4">
                              <label v-if="!shortname"
                                     class="required text-danger shortname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                            </div>
                            <div class="col-sm-4 form-group">
                              <label for="inputText6">{{trans.get('keys.ten_thu_vien')}}
                                *</label>
                              <input v-model="fullname" type="text" id="inputText6"
                                     :placeholder="trans.get('keys.nhap_ten_thu_vien')"
                                     class="form-control mb-4">
                              <label v-if="!fullname"
                                     class="required text-danger fullname_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                            </div>

                            <div class="col-sm-4 form-group">
                              <label for="inputText1-1">{{trans.get('keys.diem_qua_mon')}}
                                *</label>
                              <input v-model="pass_score" type="number" id="inputText1-2"
                                     :placeholder="trans.get('keys.vi_du')+': 50'"
                                     class="form-control mb-4">
                              <label v-if="!pass_score"
                                     class="required text-danger pass_score_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
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
                  <div class="col-md-6 col-sm-5 dataTables_wrapper">
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
                  <div class="col-md-6 col-sm-7">
                    <form v-on:submit.prevent="getCourses(1)">
                      <div class="d-flex flex-row form-group">
                        <input v-model="keyword" type="text"
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
                      <th class="text-center mobile_hide">{{trans.get('keys.cap_nhat_lan_cuoi')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(course,index) in courses">
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td>{{ course.shortname }}</td>
                      <td>{{ course.fullname }}</td>
<!--                      <td class="text-center mobile_hide">{{Math.floor(course.pass_score)}}</td>-->
                      <td v-if="course.username && course.username.length > 0" class="text-center mobile_hide"><a style="cursor: default; color: #007bff;" :title="capitalizeFirstLetter(course.last_modify_action) + ' at ' + course.last_modify_time">{{ course.username }}</a></td>
                      <td v-else></td>
                      <td class="text-center">
                        <!--                                                <a :title="trans.get('keys.sua_noi_dung')"-->
                        <!--                                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"-->
                        <!--                                                   :href="lms_url + course.id"-->
                        <!--                                                ><span class="btn-icon-wrap"><i class="fal fa-book-open"></i></span></a>-->

                        <a :title="trans.get('keys.sua_noi_dung')"
                           class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                           :href="'/lms/course/view.php?id='+course.id">
                          <span class="btn-icon-wrap"><i class="fal fa-book-open"></i></span>
                        </a>

                        <router-link
                          :title="trans.get('keys.sua_thong_tin_khoa_hoc')"
                          class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                          :to="{ name: 'SampleCourseDetail', params: { id: course.id } }">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                        </router-link>

                        <router-link
                          :title="trans.get('keys.tao_moi_khoa_hoc_tu_thu_vien')"
                          class="btn btn-sm btn-icon btn-icon-circle btn-outline-success btn-icon-style-2"
                          :to="{ name: 'CourseClone', params: { course_id: course.id } }">
                          <span class="btn-icon-wrap"><i class="fal fa-clone"></i></span>
                        </router-link>

                        <button :title="trans.get('keys.xoa')" data-toggle="modal"
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
    props: ['file_url', 'type'],
    //components: {vPagination},
    // components: {CourseSampleCreate},
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
        total_course: 0,
        row: 10,
        urlGetListUser: '/api/courses/list',
        lms_url: ''
      }
    },
    filters: {
      convertDateTime(value) {
        var time = new Date(value * 1000);
        return time.toLocaleDateString();
      }
    },
    methods: {
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

        if (!this.pass_score) {
          $('.pass_score_required').show();
          return;
        }


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
              this.hintCode();
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
            console.log(error.response.data);
          });
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
      onPageChange() {
        this.getCourses();
      },
      deletePost(id) {
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
                current_pos.getCourses(this.current);
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
      }
    },
    mounted() {
      // this.getCourses();
      this.hintCode();
      // this.fetch();
    },
    updated() {
      this.setFileInput();
    }
  }
</script>

<style scoped>

</style>
