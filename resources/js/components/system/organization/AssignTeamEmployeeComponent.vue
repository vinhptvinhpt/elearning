<template>
  <div class="row">
    <div class="col-12">
      <div class="accordion" id="accordion_2">
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <a role="button" data-toggle="collapse" href="#collapse_2" aria-expanded="true">
              <i class="fal fa-plus mr-3"></i>{{trans.get('keys.gan_thanh_vien_vao_team')}}
            </a>
          </div>
          <div id="collapse_2" class="collapse show" data-parent="#accordion_2" role="tabpanel">
            <div class="card-body">
              <div class="row">
                <div class="col-xl-12">
                  <section class="hk-sec-wrapper">

                    <div class="row">
                      <div class="col-sm">
                        <div class="table-wrap">
                          <div class="row">
                            <div class="col-lg-5 col-sm-10 mb-3">
                              <h6 class="hk-sec-title">
                                {{trans.get('keys.nhan_vien_trong_to_chuc')}}</h6>

                              <div class="row">
                                <div class="col-12">
                                  <form v-on:submit.prevent="getUserNeedEnrol(1)">
                                    <div class="d-flex flex-row form-group">
                                      <input v-model="keyword" type="text"
                                             class="form-control search_text"
                                             :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_username')+' ...'">
                                      <button type="button" id="btnFilter"
                                              class="btn btn-primary btn-sm"
                                              @click="getUserNeedEnrol(1)">
                                        {{trans.get('keys.tim')}}
                                      </button>
                                    </div>
                                  </form>
                                </div>
                              </div>

                              <div class="row">
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
                              </div>

                              <table id="datable_1" class="table_res">
                                <thead>
                                <tr>
                                  <th>{{trans.get('keys.stt')}}</th>
                                  <th>{{trans.get('keys.username')}}</th>
                                  <th class=" mobile_hide" style="width: 30%;">
                                    {{trans.get('keys.ho_ten')}}
                                  </th>
                                  <th class="text-center">
                                    <input class="selection-all" type="checkbox"
                                           v-model="allSelected"
                                           @click="selectAllEnrol()">
                                  </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user,index) in userNeedEnrols">
                                  <td>{{ (current-1)*row+(index+1) }}</td>
                                  <td>{{ user.username }}</td>
                                  <td class=" mobile_hide">{{ user.lastname }} {{
                                    user.firstname }}
                                  </td>
                                  <td class="text-center">
                                    <input class="selection-child" type="checkbox"
                                           :value="user.id" v-model="userEnrols"
                                           @change="onCheckboxEnrol()"/>
                                  </td>
                                </tr>
                                </tbody>
                              </table>

                              <v-pagination v-model="current" @input="onPageChange"
                                            :page-count="totalPages"
                                            :classes=$pagination.classes></v-pagination>

                            </div>

                            <div class="col-sm-2"
                                 style="text-align: center; margin-top: 11rem;">
                              <button :title="trans.get('keys.ghi_danh')"
                                      data-toggle="modal"
                                      data-target="#delete-ph-modal"
                                      @click="enrolUserToCourse()"
                                      class="btn btn-icon btn-primary btn-icon-style-2">
                                                                <span class="btn-icon-wrap"><i
                                                                  class="fal fa-arrow-alt-right"></i></span>
                              </button>
                              <button :title="trans.get('keys.huy_ghi_danh')"
                                      data-toggle="modal"
                                      data-target="#delete-ph-modal"
                                      @click="removeEnrolUserToCourse()"
                                      class="btn btn-icon btn-danger btn-icon-style-2">
                                                                <span class="btn-icon-wrap"><i
                                                                  class="fal fa-arrow-alt-left"></i></span>
                              </button>
                            </div>

                            <div class="col-lg-5">
                              <h6 class="hk-sec-title">
                                {{trans.get('keys.thanh_vien_trong_team')}}</h6>
                              <div class="row">
                                <div class="col-12">
                                  <form v-on:submit.prevent="getCurrentUserEnrol(1)">
                                    <div class="d-flex flex-row form-group">
                                      <input v-model="keyword_curr" type="text"
                                             class="form-control search_text"
                                             :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_username')+'...'">
                                      <button type="button" id="btnFilter1"
                                              class="btn btn-primary btn-sm"
                                              @click="getCurrentUserEnrol(1)">
                                        {{trans.get('keys.tim')}}
                                      </button>
                                    </div>
                                  </form>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-6 dataTables_wrapper">
                                  <div class="dataTables_length"
                                       style="display:block;">
                                    <label>{{trans.get('keys.hien_thi')}}
                                      <select v-model="row_crr"
                                              class="custom-select custom-select-sm form-control form-control-sm"
                                              @change="getCurrentUserEnrol(1)">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="50">50</option>
                                      </select>
                                    </label>
                                  </div>
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
                                  <th class="text-center">
                                    <input class="selection-all" type="checkbox"
                                           v-model="allSelectedRemove"
                                           @click="selectAllRemoveEnrol()">
                                  </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user,index) in currentUserEnrols">
                                  <td>{{ (current_page-1)*row_crr+(index+1) }}</td>
                                  <td>{{ user.username }}</td>
                                  <td class=" mobile_hide">{{ user.lastname }} {{
                                    user.firstname }}
                                  </td>
                                  <td class="text-center">
                                    <input class="selection-child" type="checkbox"
                                           :value="user.id"
                                           v-model="userRemoveEnrol"
                                           @change="onCheckboxRemoveEnrol()"/>
                                  </td>
                                </tr>
                                </tbody>
                              </table>

                              <v-pagination v-model="current_page"
                                            @input="onPageChangeCurr"
                                            :page-count="totalPages_crr"
                                            :classes=$pagination.classes></v-pagination>

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
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['organization_id', 'team_id'],
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

        userEnrols: [],
        userRemoveEnrol: [],
        allSelected: false,
        allSelectedRemove: false,
      }
    },
    methods: {
      getUserNeedEnrol(paged) {
        axios.post('/api/team/user_out', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          organization_id: this.organization_id,
          team_id: this.team_id
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
        axios.post('/api/team/user_in', {
          page: paged || this.current_page,
          keyword: this.keyword_curr,
          row: this.row_crr,
          team_id: this.team_id
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
          toastr['error'](current_pos.trans.get('keys.ban_chua_chon_thanh_vien'), current_pos.trans.get('keys.loi'));
          return;
        }
        let loader = $('.preloader-it');
        loader.fadeIn();
        axios.post('/api/team/assign_member', {
          users: this.userEnrols,
          team_id: this.team_id,
          organization_id: this.organization_id
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
      removeEnrolUserToCourse() {
        let current_pos = this;
        if (this.userRemoveEnrol.length === 0) {
          toastr['error'](current_pos.trans.get('keys.ban_chua_chon_giao_vien'), current_pos.trans.get('keys.loi'));
          return;
        }
        let loader = $('.preloader-it');
        loader.fadeIn();
        axios.post('/api/team/remove_member', {
          users: this.userRemoveEnrol,
          team_id: this.team_id
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
      uncheckEnrolAll() {
        this.allSelected = true;
        this.selectAllEnrol();
      },
      uncheckRemoveEnrolAll() {
        this.allSelectedRemove = true;
        this.selectAllRemoveEnrol();
      }
    },
    mounted() {
      this.getUserNeedEnrol();
      this.getCurrentUserEnrol();
    }
  }
</script>

<style scoped>

</style>
