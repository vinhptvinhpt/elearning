<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
            </li>
            <li v-if="come_from === 'online'" class="breadcrumb-item">
              <router-link :to="{ name: 'CourseIndex' }">
                {{ trans.get('keys.khoa_dao_tao_online') }}
              </router-link>
            </li>
            <li v-else class="breadcrumb-item">
              <router-link :to="{ name: 'CourseConcentrateIndex' }">
                {{ trans.get('keys.khoa_dao_tao_tap_trung') }}
              </router-link>
            </li>
            <li class="breadcrumb-item">
              <router-link :to="{ name: 'CourseDetail', params: {id: course_id} }">
                {{ course_name }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.moi_ghi_danh_khoa_hoc') }}</li>
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
                 aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.moi_ghi_danh_khoa_hoc')}}</a>
            </div>
            <div id="collapse_1" class="collapse show" data-parent="#accordion_1" role="tabpanel">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-12">
                    <section class="hk-sec-wrapper">
                      <div class="row">
                        <div class="col-sm">
                          <div class="table-wrap">

                            <div class="row">
                              <div class="col-lg-5 col-sm-10 mb-3">
                                <h6 class="hk-sec-title">{{trans.get('keys.danh_sach_nhan_su')}}</h6>
                                <div class="row">
                                  <div class="col-12">
                                    <form v-on:submit.prevent="getUserNeedEnrol(1)">
                                      <div class="d-flex flex-row form-group">
                                        <input v-model="keyword" type="text"
                                               class="form-control search_text"
                                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_fullname')+' ...'">
                                        <button type="button" id="btnFilter"
                                                class="btn btn-primary btn-sm"
                                                @click="getUserNeedEnrol(1)">
                                          {{trans.get('keys.tim')}}
                                        </button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                                <div class="row mb-3">
                                  <div class="col-6 dataTables_wrapper">
                                    <div class="dataTables_length"
                                         style="display:block;">
                                      <label>{{trans.get('keys.hien_thi')}}
                                        <select v-model="row"
                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                @change="getUserNeedEnrol(1)">
                                          <option value="5">5</option>
                                          <option value="10">10</option>
                                          <option value="50">50</option>
                                        </select>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <treeselect v-model="organization_id_1" :multiple="false" :options="options" @input="getUserNeedEnrol(1)"/>
                                  </div>
                                </div>
                                <table id="datable_1" class="table_res">
                                  <thead>
                                  <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.username')}}</th>
                                    <th class=" mobile_hide" style="width: 30%;">
                                      {{trans.get('keys.ho_ten')}}
                                    </th>
                                    <th class="text-center"><input type="checkbox" v-model="allSelected" @click="selectAllEnrol()"></th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr v-for="(user,index) in userNeedEnrols">
                                    <td>{{ (current-1)*row+(index+1) }}</td>
                                    <td>{{ user.username }}</td>
                                    <td class=" mobile_hide">{{ user.lastname }} {{ user.firstname }}
                                    </td>
                                    <td class="text-center">
                                      <input type="checkbox" :value="user.id" v-model="userEnrols" @change="onCheckboxEnrol()"/>
                                    </td>
                                  </tr>
                                  </tbody>
                                  <tfoot>

                                  </tfoot>
                                </table>
                                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes></v-pagination>
                              </div>

                              <div class="col-sm-1" style="text-align: center; margin-top: 11rem;">
                                <button :title="trans.get('keys.chon_vao_danh_sach')"
                                        data-toggle="modal"
                                        data-target="#delete-ph-modal"
                                        @click="enrolUserToCourse()"
                                        class="btn btn-icon btn-primary btn-icon-style-2">
                                  <span class="btn-icon-wrap"><i class="fal fa-arrow-alt-right"></i></span>
                                </button>
                                <button :title="trans.get('keys.huy_khoi_danh_sach')"
                                        data-toggle="modal"
                                        data-target="#delete-ph-modal"
                                        @click="removeEnrolUserToCourse()"
                                        class="btn btn-icon btn-danger btn-icon-style-2">
                                  <span class="btn-icon-wrap"><i class="fal fa-arrow-alt-left"></i></span>
                                </button>
                              </div>

                              <div class="col-lg-6">
                                <h6 class="hk-sec-title">{{trans.get('keys.danh_sach_moi_ghi_danh')}}</h6>
                                <div class="row">
                                  <div class="col-12">
                                    <form v-on:submit.prevent="getCurrentUserEnrol(1)">
                                      <div class="d-flex flex-row form-group">
                                        <input v-model="keyword_curr"
                                               type="text"
                                               class="form-control search_text"
                                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_fullname')+' ...'">
                                        <button type="button" id="btnFilter1"
                                                class="btn btn-primary btn-sm"
                                                @click="getCurrentUserEnrol(1)">
                                          {{trans.get('keys.tim')}}
                                        </button>
                                        <a style="color: #fff" class="btn btn-sm btn-primary" v-on:click="exportExcel()" :title="trans.get('keys.xuat_excel')">
                                          <span class="btn-icon-wrap"><i class="fal fa-file-excel-o"></i>&nbsp;{{trans.get('keys.excel')}}</span>
                                        </a>
                                      </div>
                                    </form>
                                  </div>
                                </div>

                                <div class="row mb-3">
                                  <div class="col-6 dataTables_wrapper">
                                    <div class="dataTables_length" style="display:block;">
                                      <label>{{trans.get('keys.hien_thi')}}
                                        <select v-model="row_crr"
                                                class="custom-select custom-select-sm form-control form-control-sm"
                                                @change="getCurrentUserEnrol(1)">
                                          <option value="5">5</option>
                                          <option value="10">10</option>
                                          <option value="50">50</option>
                                        </select>
                                      </label>
                                      <label>{{trans.get('keys.trang_thai')}}
                                        <select v-model="invite_status" style="min-width: 150px" class="custom-select custom-select-sm form-control form-control-sm" @change="getCurrentUserEnrol(1)">
                                          <option value="">{{trans.get('keys.tat_ca')}}</option>
                                          <option value="noreply">{{trans.get('keys.chua_tra_loi')}}</option>
                                          <option value="accepted">{{trans.get('keys.dong_y')}}</option>
                                          <option value="denied">{{trans.get('keys.tu_choi')}}</option>
                                        </select>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <treeselect v-model="organization_id_2" :multiple="false" :options="options" @input="getCurrentUserEnrol(1)"/>
                                  </div>
                                </div>

                                <table id="datable_2" class="table_res">
                                  <thead>
                                  <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.username')}}</th>
                                    <th class=" mobile_hide" style="width: 30%;">
                                      {{trans.get('keys.ho_ten')}}
                                    </th>
                                    <th class="text-center">{{trans.get('keys.trang_thai')}}</th>
                                    <th class="text-center"><input type="checkbox" v-model="allSelectedRemove" @click="selectAllRemoveEnrol()"></th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr v-for="(user,index) in currentUserEnrols">
                                    <td>{{ (current_page-1)*row_crr+(index+1) }}
                                    </td>
                                    <td>{{ user.username }}</td>
                                    <td class=" mobile_hide">{{ user.lastname }} {{ user.firstname }}
                                    </td>
                                    <td class="text-center">
                                      <span v-if="user.replied === 0" class="fas fa-check text-secondary"></span>
                                      <span v-else-if="user.replied === 1 && user.accepted === 1" class="fas fa-check text-success"></span>
                                      <a v-else class="fas fa-times text-danger" v-on:click="showReply(user)" :title="trans.get('keys.ly_do') + ': ' + user.reason"></a>
                                    </td>
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

                                <v-pagination v-model="current_page" @input="onPageChangeCurr" :page-count="totalPages_crr" :classes=$pagination.classes></v-pagination>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xl-12">
              <section class="hk-sec-wrapper">
                <div class="row">
                  <div class="col-3">
                    <button class="btn btn-danger btn-sm" @click="goBack()">
                      {{trans.get('keys.quay_lai')}}
                    </button>
                  </div>
                </div>
              </section>
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
    props: {
      course_id: {
        required: true
      },
      come_from: String,
      course_name: {
        type: String,
        default: 'Course Name'
      },
    },
    //components: {vPagination},
    data() {
      return {
        userNeedEnrols: [],
        keyword: '',
        current: 1,
        totalPages: 0,
        row: 5,

        currentUserEnrols: [],
        keyword_curr: '',
        current_page: 1,
        totalPages_crr: 0,
        row_crr: 5,
        invite_status: '',

        role_id: 5,
        userEnrols: [],
        userRemoveEnrol: [],
        allSelected: false,
        allSelectedRemove: false,

        //Treeselect options
        options: [
          {
            id: 0,
            label: this.trans.get('keys.chon_to_chuc')
          }
        ],
        organization_id_1: 0,
        organization_id_2: 0
      }
    },
    methods: {
      getUserNeedEnrol(paged) {
        axios.post('/api/course/user_need_invite', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          role_id: this.role_id,
          course_id: this.course_id,
          organization_id: this.organization_id_1
        })
          .then(response => {
            this.userNeedEnrols = response.data.data.data;
            this.current = response.data.pagination.current_page;
            this.totalPages = response.data.pagination.total;
            this.uncheckEnrolAll();
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      getCurrentUserEnrol(paged) {
        axios.post('/api/course/current_user_invite', {
          page: paged || this.current_page,
          keyword: this.keyword_curr,
          row: this.row_crr,
          role_id: this.role_id,
          course_id: this.course_id,
          organization_id: this.organization_id_2,
          invite_status: this.invite_status
        })
          .then(response => {
            this.currentUserEnrols = response.data.data.data;
            this.current_page = response.data.pagination.current_page;
            this.totalPages_crr = response.data.pagination.total;
            this.uncheckRemoveEnrolAll();
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      onPageChange() {
        this.getUserNeedEnrol();
      },
      onPageChangeCurr() {
        this.getCurrentUserEnrol();
      },
      selectAllEnrol: function () {
        this.userEnrols = [];
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
          var countEnrol = this.userNeedEnrols.length;
          if (countEnrol > 0) {
            for (var i = 0; i < countEnrol; i++) {
              this.userEnrols.push(this.userNeedEnrols[i].id.toString());
            }
          }
        }
      },
      selectAllRemoveEnrol: function () {
        this.userRemoveEnrol = [];
        this.allSelectedRemove = !this.allSelectedRemove;
        if (this.allSelectedRemove) {
          var countEnrol = this.currentUserEnrols.length;
          if (countEnrol > 0) {
            for (var i = 0; i < countEnrol; i++) {
              this.userRemoveEnrol.push(this.currentUserEnrols[i].id.toString());
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
      enrolUserToCourse() {
        let current_pos = this;
        if (this.userEnrols.length === 0) {
          toastr['error'](current_pos.trans.get('keys.ban_chua_chon_nguoi_dung'), current_pos.trans.get('keys.loi'));
          return;
        }
        let loader = $('.preloader-it');
        loader.fadeIn();
        axios.post('/api/course/invite_user_to_course', {
          users: this.userEnrols,
          role_id: this.role_id,
          course_id: this.course_id
        })
          .then(response => {
            loader.fadeOut();
            if(!response.data.send){
              if (response.data.status) {
                toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                current_pos.getCurrentUserEnrol(current_pos.current_page);
                current_pos.getUserNeedEnrol(current_pos.current_page);
              } else {
                toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
              }
            }
          })
          .catch(error => {
            loader.fadeOut();
            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
          });
      },
      removeEnrolUserToCourse() {
        let current_pos = this;
        if (this.userRemoveEnrol.length === 0) {
          toastr['error'](current_pos.trans.get('keys.ban_chua_chon_nguoi_dung'), current_pos.trans.get('keys.loi'));
          return;
        }
        let loader = $('.preloader-it');
        loader.fadeIn();
        axios.post('/api/course/remove_invite_user_to_course', {
          users: this.userRemoveEnrol,
          role_id: this.role_id,
          course_id: this.course_id
        })
          .then(response => {
            loader.fadeOut();
            if (response.data.status) {
              toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
              current_pos.getCurrentUserEnrol(current_pos.current_page);
              current_pos.getUserNeedEnrol(current_pos.current_page);
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
        if (this.come_from === 'online') {
          this.$router.push({name: 'CourseIndex', params:{back_page: '1'}});
        } else {
          this.$router.push({name: 'CourseConcentrateIndex', params:{back_page: '1'}});
        }
      },
      uncheckEnrolAll() {
        this.allSelected = true;
        this.selectAllEnrol();
      },
      uncheckRemoveEnrolAll() {
        this.allSelectedRemove = true;
        this.selectAllRemoveEnrol();
      },
      selectOrganization(current_id) {
        $('.content_search_box').addClass('loadding');
        axios.post('/organization/list',{
          keyword: this.organization_keyword,
          level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
          paginated: 0 //không phân trang
        })
          .then(response => {
            this.organization_list = response.data;
            //Set options recursive
            this.options = this.setOptions(response.data, current_id);
            $('.content_search_box').removeClass('loadding');
          })
          .catch(error => {
            $('.content_search_box').removeClass('loadding');
          })
      },
      setOptions(list, current_id) {
        let outPut = [];
        for (const [key, item] of Object.entries(list)) {
          let newOption = {
            id: item.id,
            label: item.name,
          };
          if (item.children.length > 0) {
            for (const [key, child] of Object.entries(item.children)) {
              if (child.id === current_id) {
                newOption.isDefaultExpanded = true;
                break;
              }
            }
            newOption.children = this.setOptions(item.children, current_id);
          }
          outPut.push(newOption);
        }
        return outPut;
      },
      exportExcel() {
        axios.post('/api/exportInvite', {
          keyword: this.keyword_curr,
          course_id: this.course_id,
          organization_id: this.organization_id_2,
          course_name: this.course_name
        })
          .then(response => {
            let file_name = response.data;
            let a = $("<a>")
              .prop("href", "/api/downloadExport/" + file_name)
              .appendTo("body");
            a[0].click();
            a.remove();
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          });
      },
      showReply(invited) {
        swal({
          title: this.trans.get('keys.nguoi_dung_tu_choi_tham_gia_khoa_hoc_voi_ly_do'),
          text: invited.reason,
          type: "info",
          showCancelButton: true,
          closeOnConfirm: true,
          showLoaderOnConfirm: false
        });
      }
    },
    mounted() {
      this.selectOrganization();
      $('[data-toggle="tooltip"]').tooltip()
    }
  }

</script>

<style scoped>

</style>
