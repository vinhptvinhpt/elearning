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
                    <form action="">
                      <div class="form-row">
                        <div class="col-sm-6 form-group">
                          <label for="inputText1-2">{{trans.get('keys.ma_knl')}} *</label>
                          <input v-model="trainning.code" type="text" id="inputText1-2"
                                 :placeholder="trans.get('keys.ma_knl')"
                                 class="form-control">
                          <label v-if="!trainning.code"
                                 class="required text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                        </div>
                        <div class="col-sm-6 form-group">
                          <label for="inputText1-1">{{trans.get('keys.ten_knl')}} *</label>
                          <input v-model="trainning.name" type="text" id="inputText1-1"
                                 :placeholder="trans.get('keys.ten_knl')"
                                 class="form-control">
                          <label v-if="!trainning.name"
                                 class="required text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="col-sm-6 form-group">
                          <label for="inputRole">{{trans.get('keys.quyen')}}</label>
                          <select v-model="trainning.role_id" class="form-control selectpicker" id="inputRole" autocomplete="false">
                            <option value="0">{{trans.get('keys.chon_vai_tro')}}</option>
                            <option v-for="item in roles" :value="item.id">{{item.name}}</option>
                          </select>
                        </div>
                        <div class="col-sm-6 form-group">
                          <label>{{trans.get('keys.them_co_cau_to_chuc')}}</label>
                          <treeselect v-model="trainning.organization_id" :multiple="false" :options="tree_options"
                                      id="organization_parent_id"/>
                        </div>
                      </div>

<!--                      <div class="form-row">-->
<!--                        <div class="col-sm-6 form-group">-->
<!--                          <label>{{trans.get('keys.khung_nang_luc_theo_tg')}}</label>-->
<!--                          <div class="custom-control custom-switch">-->
<!--                            <input type="checkbox" class="custom-control-input" id="style"-->
<!--                                   :checked="trainning.style==1?true:false" v-model="trainning.style">-->
<!--                            <label v-if="trainning.style == 1" class="custom-control-label" for="style">{{trans.get('keys.khung_nang_luc_hoan_thanh_trong_khoang_tg')}}</label>-->
<!--                            <label v-else class="custom-control-label" for="style">{{trans.get('keys.khung_nang_luc_khong_gioi_han_tg')}}</label>-->
<!--                          </div>-->
<!--                        </div>-->
<!--                      </div>-->

                      <div class="form-row" v-if="trainning.style == 1">
                        <div class="col-12">
                          <label for="inputText1-1">{{trans.get('keys.thoi_gian_hoan_thanh')}}</label>
                        </div>
                        <div class="col-sm-6 form-group">
                          <date-picker v-model="trainning.time_start" :config="options"
                                       :placeholder="trans.get('keys.ngay_bat_dau')"></date-picker>
                          <label v-if="trainning.style == 1 && !trainning.time_start"
                                 class="required text-danger time_start_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                        </div>
                        <div class="col-sm-6 form-group">
                          <date-picker v-model="trainning.time_end" :config="options"
                                       :placeholder="trans.get('keys.ngay_ket_thuc')"></date-picker>
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="col-sm-6 form-group">
                          <label>{{trans.get('keys.tu_dong_chay_cron')}}</label>
                          <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="run_cron"
                                   :checked="trainning.run_cron==1?true:false" v-model="trainning.run_cron">
                            <label v-if="trainning.run_cron == 1" class="custom-control-label" for="run_cron">Yes</label>
                            <label v-else class="custom-control-label" for="run_cron">No</label>
                          </div>
                        </div>
                        <div class="col-sm-6 form-group">
                          <label v-if="trainning.style == 1">{{trans.get('keys.tu_dong_cap_huy_hieu')}}</label>
                          <label v-else>{{trans.get('keys.tu_dong_cap_chung_chi')}}</label>
                          <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="auto_certificate"
                                   :checked="trainning.auto_certificate==1?true:false" v-model="trainning.auto_certificate">
                            <label v-if="trainning.auto_certificate == 1" class="custom-control-label" for="auto_certificate">Yes</label>
                            <label v-else class="custom-control-label"
                                   for="auto_certificate">No</label>
                          </div>
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="col-12 form-group">
                          <div class="button-list text-right">
                            <router-link v-if="trainning.style == 1" :to="{name: 'TrainningIndex', query: {type: trainning.style}}" class="btn btn-danger btn-sm">
                              {{ trans.get('keys.huy') }}
                            </router-link>
                            <router-link v-else :to="{name: 'TrainningIndex'}" class="btn btn-danger btn-sm">
                              {{ trans.get('keys.huy') }}
                            </router-link>
                            <button @click="editTrainning()" type="button"
                                    class="btn btn-primary btn-sm">
                              {{trans.get('keys.sua')}}
                            </button>
                          </div>
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
                                  <input type="checkbox" :value="user.id" v-model="userEnrols"
                                         @change="onCheckboxEnrol()"/>
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

                            <v-pagination v-model="current_tn" @input="onPageChangeCurr" :page-count="totalPages_tn" :classes=$pagination.classes></v-pagination>
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
  import datePicker from 'vue-bootstrap-datetimepicker'

  export default {
    props: ['id'],
    components: {
      datePicker
    },
    data() {
      return {
        trainning: {
          code: '',
          name: '',
          style: 0,
          run_cron: 1,
          auto_certificate: 1,
          time_start: '',
          time_end: '',
        },

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

        date: new Date(),
        organization: {
          name: '',
          code: '',
          parent_id: 0,
          description: '',
        },
        options: {
          format: 'DD-MM-YYYY',
          useCurrent: false,
          showClear: true,
          showClose: true,
        },
        roles:{},
        //Treeselect options
        tree_options: [
          {
            id: 0,
            label: this.trans.get('keys.chon_to_chuc')
          }
        ],
        organization_parent_list:[],

        language: this.trans.get('keys.language')
      }
    },
    methods: {
      getRoles() {
        axios.post('/system/user/list_role')
          .then(response => {
            this.roles = response.data;
            this.$nextTick(function(){
              $('.selectpicker').selectpicker('refresh');
            });
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      listOrganization(){
        axios.post('/organization/list', {
          keyword: this.parent_keyword,
          level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
          paginated: 0 //không phân trang,
        })
          .then(response => {
            this.organization_parent_list = response.data;
            //Set options recursive
            this.tree_options = this.setOptions(response.data);
          })
          .catch(error => {

          })
      },
      setOptions(list) {
        let outPut = [];
        for (const [key, item] of Object.entries(list)) {
          let newOption = {
            id: item.id,
            label: item.name
          };
          if (item.children.length > 0) {
            newOption.children = this.setOptions(item.children);
          }
          outPut.push(newOption);
        }
        return outPut;
      },
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

        axios.post('/api/trainning/update', {
          id: this.id,
          code: this.trainning.code,
          name: this.trainning.name,
          style: this.trainning.style,
          auto_certificate: this.trainning.auto_certificate,
          run_cron: this.trainning.run_cron,
          time_start: this.trainning.time_start,
          time_end: this.trainning.time_end,
          role_id: this.trainning.role_id,
          organization_id: this.trainning.organization_id
        })
          .then(response => {
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
      },
      enrolUserToCourse() {
        if (this.userEnrols.length === 0) {
          toastr['warning']('Bạn chưa chọn khóa học', this.trans.get('keys.thong_bao'));
          return;
        }
        axios.post('/api/trainning/addcoursetotrainning', {
          lst_course: this.userEnrols,
          trainning_id: this.id
        })
          .then(response => {
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              $('#btnFilter1').trigger('click');
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
      },
      removeEnrolUserToCourse() {
        if (this.userRemoveEnrol.length === 0) {
          toastr['warning']('Bạn chưa chọn khóa học', this.trans.get('keys.thong_bao'));
          return;
        }
        axios.post('/api/trainning/removecoursetotrainning', {
          lst_course: this.userRemoveEnrol,
          trainning_id: this.id
        })
          .then(response => {
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              $('#btnFilter1').trigger('click');
            } else {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });

      },
      goBack() {
        if (this.trainning.style == 1) {
          this.$router.push({ name: 'TrainningIndex', query: {type: this.trainning.style} });
        } else {
          this.$router.push({ name: 'TrainningIndex' });
        }
      }
    },
    mounted() {
      this.getDetailTrainning();
      this.listOrganization();
      this.getRoles();
    }
  }
</script>

<style scoped>

</style>
