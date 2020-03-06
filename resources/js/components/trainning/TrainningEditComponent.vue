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
              <router-link :to="{ name: 'TrainningIndex' }">
                {{ trans.get('keys.quan_tri_khung_nang_luc') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chinh_sua_thong_tin_knl') }}</li>
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
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.sua_thong_tin_knl')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-lg-12">
                    <form action="" class="form-row">
                      <div class="col-sm-6 form-group">
                        <label for="inputText1-2">{{trans.get('keys.ma_knl')}} *</label>
                        <input v-model="trainning.code" type="text" id="inputText1-2"
                               :placeholder="trans.get('keys.ma_knl')"
                               class="form-control mb-4">
                        <label v-if="!trainning.code"
                               class="required text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-sm-6 form-group">
                        <label for="inputText1-1">{{trans.get('keys.ten_knl')}} *</label>
                        <input v-model="trainning.name" type="text" id="inputText1-1"
                               :placeholder="trans.get('keys.ten_knl')"
                               class="form-control mb-4">
                        <label v-if="!trainning.name"
                               class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                      <div class="col-12 form-group">
                        <div class="button-list text-right">
                          <button type="button" @click="goBack()"
                                  class="btn btn-danger btn-sm">
                            {{trans.get('keys.huy')}}
                          </button>
                          <button @click="editTrainning()" type="button"
                                  class="btn btn-primary btn-sm">
                            {{trans.get('keys.sua')}}
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <br/>
                <div class="row">
                  <div class="col-sm">
                    <div class="table-wrap">

                      <div class="row">
                        <div class="col-lg-5 col-sm-10 mb-3">
                          <label>
                            <h6 class="hk-sec-title">
                              {{trans.get('keys.danh_sach_khoa_hoc_mau')}}</h6>
                          </label>
                          <div class="row">
                            <div class="col-12">
                              <form v-on:submit.prevent="getListSampleCourse(1)">
                                <div class="d-flex flex-row form-group">
                                  <input v-model="keyword" type="text"
                                         class="form-control search_text"
                                         :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                  <button type="button" id="btnFilter"
                                          class="btn btn-primary btn-sm"
                                          @click="getListSampleCourse(1)">
                                    {{trans.get('keys.tim')}}
                                  </button>
                                </div>
                              </form>

                            </div>
                          </div>

                          <div class="row">
                            <div class="col-6 dataTables_wrapper">
                              <div class="dataTables_length" style="display: inline-block;">
                                <label>{{trans.get('keys.hien_thi')}}
                                  <select v-model="row"
                                          class="custom-select custom-select-sm form-control form-control-sm"
                                          @change="getListSampleCourse(1)">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                  </select>
                                </label>
                              </div>
                            </div>
                          </div>
                          <br/>

                          <div class="table-responsive table-sm">
                            <table id="datable_1" class="table_res">
                              <thead>
                              <tr>
                                <th>{{trans.get('keys.stt')}}</th>
                                <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                <th style="width: 30%;">
                                  {{trans.get('keys.ten_khoa_hoc')}}
                                </th>
                                <th class="text-center">
                                  <input type="checkbox" v-model="allSelected" @click="selectAllEnrol()">
                                </th>
                              </tr>
                              </thead>
                              <tbody>
                              <tr v-for="(user,index) in sampleCourses">
                                <td>{{ (current-1)*row+(index+1) }}</td>
                                <td>{{ user.shortname }}</td>
                                <td>{{ user.fullname }}</td>
                                <td class="text-center">
                                  <input type="checkbox" :value="user.id" v-model="userEnrols" @change="onCheckboxEnrol()"/>
                                </td>
                              </tr>
                              </tbody>
                              <tfoot>

                              </tfoot>
                            </table>

                            <v-pagination v-model="current" @input="onPageChange"
                                          :page-count="totalPages"
                                          :classes=$pagination.classes></v-pagination>
                          </div>

                        </div>
                        <div class="col-sm-2" style="text-align: center;">
                          <button :title="trans.get('keys.dua_vao_knl')"
                                  data-toggle="modal"
                                  style="margin-top: 30px;"
                                  data-target="#delete-ph-modal"
                                  @click="enrolUserToCourse()"
                                  class="btn btn-icon btn-primary btn-icon-style-2 mt-sm-15">
                            <span class="btn-icon-wrap"><i class="fal fa-arrow-alt-right"></i></span>
                          </button>
                          <br/><br/>
                          <button :title="trans.get('keys.go_khoi_knl')"
                                  data-toggle="modal"
                                  data-target="#delete-ph-modal"
                                  style="margin-bottom: 30px;"
                                  @click="removeEnrolUserToCourse()"
                                  class="btn btn-icon btn-danger btn-icon-style-2  mb-sm-0">
                            <span class="btn-icon-wrap"><i class="fal fa-arrow-alt-left"></i></span>
                          </button>

                        </div>
                        <div class="col-lg-5">
                          <label>
                            <h6 class="hk-sec-title">
                              {{trans.get('keys.danh_sach_khoa_hoc_trong_knl')}}</h6>
                          </label>
                          <div class="row">
                            <div class="col-12">
                              <form v-on:submit.prevent="getListCourseTrainning(1)">
                                <div class="d-flex flex-row form-group">
                                  <input v-model="keyword_tn" type="text"
                                         class="form-control search_text"
                                         :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                                  <button type="button" id="btnFilter1"
                                          class="btn btn-primary btn-sm"
                                          @click="getListCourseTrainning(1)">
                                    {{trans.get('keys.tim')}}
                                  </button>
                                </div>
                              </form>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-6 dataTables_wrapper">
                              <div class="dataTables_length">
                                <label>{{trans.get('keys.hien_thi')}}
                                  <select v-model="row_tn"
                                          class="custom-select custom-select-sm form-control form-control-sm"
                                          @change="getListCourseTrainning(1)">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="50">50</option>
                                  </select>
                                </label>
                              </div>
                            </div>
                          </div>
                          <br/>

                          <div class="table-responsive table-sm">
                            <table id="datable_2" class="table_res">
                              <thead>
                              <tr>
                                <th>{{trans.get('keys.stt')}}</th>
                                <th>{{trans.get('keys.ma_khoa_hoc')}}</th>
                                <th style="width: 30%;">
                                  {{trans.get('keys.ten_khoa_hoc')}}
                                </th>
                                <th class="text-center"><input type="checkbox"
                                                               v-model="allSelectedRemove"
                                                               @click="selectAllRemoveEnrol()"
                                ></th>
                              </tr>
                              </thead>
                              <tbody>
                              <tr v-for="(user,index) in trainningCourses">
                                <td>{{ (current_tn-1)*row_tn+(index+1) }}</td>
                                <td>{{ user.shortname }}</td>
                                <td>{{ user.fullname }}</td>
                                <td class="text-center">
                                  <input type="checkbox" :value="user.id"
                                         v-model="userRemoveEnrol"
                                         @change="onCheckboxRemoveEnrol()"/>

                                </td>
                              </tr>
                              </tbody>
                              <tfoot>

                              </tfoot>
                            </table>

                            <v-pagination v-model="current_tn" @input="onPageChangeCurr"
                                          :page-count="totalPages_tn"
                                          :classes=$pagination.classes></v-pagination>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


</template>

<script>
  //import vPagination from 'vue-plain-pagination'

  export default {
    props: ['id'],
    //components: {vPagination},
    data() {
      return {
        trainning: {},

        sampleCourses: [],
        keyword: '',
        current: 1,
        totalPages: 0,
        row: 10,

        trainningCourses: [],
        keyword_tn: '',
        current_tn: 1,
        totalPages_tn: 0,
        row_tn: 10,


        userEnrols: [],
        userRemoveEnrol: [],
        allSelected: false,
        allSelectedRemove: false,

        language: this.trans.get('keys.language')
      }
    },
    methods: {
      onPageChange() {
        this.getListSampleCourse();
      },
      onPageChangeCurr() {
        this.getListCourseTrainning();
      },

      selectAllEnrol: function () {
        this.userEnrols = [];
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
          var countEnrol = this.sampleCourses.length;
          if (countEnrol > 0) {
            for (var i = 0; i < countEnrol; i++) {
              this.userEnrols.push(this.sampleCourses[i].id.toString());
            }
          }
        }
      },
      selectAllRemoveEnrol: function () {
        this.userRemoveEnrol = [];
        this.allSelectedRemove = !this.allSelectedRemove;
        if (this.allSelectedRemove) {
          var countEnrol = this.trainningCourses.length;
          if (countEnrol > 0) {
            for (var i = 0; i < countEnrol; i++) {
              this.userRemoveEnrol.push(this.trainningCourses[i].id.toString());
            }
          }
        }
      },
      onCheckboxEnrol() {
        this.allSelected = false;
      },
      onCheckboxRemoveEnrol() {
        this.allSelectedRemove = false;
      },

      getDetailTrainning() {
        axios.get('/api/trainning/detail/' + this.id)
          .then(response => {
            this.trainning = response.data;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      getListSampleCourse(paged) {
        axios.post('/api/trainning/getlstsamplecourse', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          trainning_id: this.id
        })
          .then(response => {
            this.sampleCourses = response.data.data.data;
            this.current = response.data.pagination.current_page;
            this.totalPages = response.data.pagination.total;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      getListCourseTrainning(paged) {
        axios.post('/api/trainning/getlstcoursetrainning', {
          page: paged || this.current,
          keyword: this.keyword_tn,
          row: this.row_tn,
          trainning_id: this.id
        })
          .then(response => {
            this.trainningCourses = response.data.data.data;
            this.current_tn = response.data.pagination.current_page;
            this.totalPages_tn = response.data.pagination.total;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      editTrainning() {

        if (!this.trainning.code) {
          $('.code_required').show();
          return;
        }

        if (!this.trainning.name) {
          $('.name_required').show();
          return;
        }

        axios.post('/api/trainning/edit/' + this.id, {
          code: this.trainning.code,
          name: this.trainning.name
        })
          .then(response => {
            var language = this.language;
            if (response.data.status) {
              swal({
                  title: response.data.message,
                  type: "success",
                  showCancelButton: false,
                  closeOnConfirm: false,
                  showLoaderOnConfirm: true
                }
              );
            } else {
              swal({
                title: response.data.message,
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

      enrolUserToCourse() {
        if (this.userEnrols.length === 0) {
          swal({
            title: 'Bạn chưa chọn khóa học',
            type: "error",
            showCancelButton: false,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
          });
          return;
        }
        axios.post('/api/trainning/addcoursetotrainning', {
          lst_course: this.userEnrols,
          trainning_id: this.id
        })
          .then(response => {
            if (response.data.status) {
              swal({
                  title: response.data.message,
                  type: "success",
                  showCancelButton: false,
                  closeOnConfirm: true,
                  showLoaderOnConfirm: false
                }
                , function () {
                  $('#btnFilter1').trigger('click');
                }
              );
            } else {
              swal({
                title: response.data.message,
                type: "error",
                showCancelButton: false,
                closeOnConfirm: true,
                showLoaderOnConfirm: false
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

      removeEnrolUserToCourse() {
        if (this.userRemoveEnrol.length === 0) {
          swal({
            title: 'Bạn chưa chọn khóa học',
            type: "error",
            showCancelButton: false,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
          });
          return;
        }

        // swal({
        //     title: "Khóa học đang có học viên tham gia học, bạn có chắc chắn muốn gỡ bỏ khóa học khỏi khung năng lực",
        //     text: "Chọn 'ok' để thực hiện thao tác.",
        //     type: "error",
        //     showCancelButton: true,
        //     closeOnConfirm: false,
        //     showLoaderOnConfirm: true
        // }, function () {
        axios.post('/api/trainning/removecoursetotrainning', {
          lst_course: this.userRemoveEnrol,
          trainning_id: this.id
        })
          .then(response => {
            if (response.data.status) {
              swal({
                  title: response.data.message,
                  type: "success",
                  showCancelButton: false,
                  closeOnConfirm: true,
                  showLoaderOnConfirm: false
                }
                , function () {
                  $('#btnFilter1').trigger('click');
                }
              );
            } else {
              swal({
                title: response.data.message,
                type: "error",
                showCancelButton: false,
                closeOnConfirm: true,
                showLoaderOnConfirm: false
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
        // });


      },

      goBack() {
        window.location.href = this.trans.get('keys.language') + '/tms/trainning/list';
      }
    },
    mounted() {
      this.getDetailTrainning();
    }
  }
</script>

<style scoped>

</style>
