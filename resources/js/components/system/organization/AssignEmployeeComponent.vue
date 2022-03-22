<template>
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_2" aria-expanded="true">
          <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_nhieu')}}
        </a>
      </div>
      <div id="collapse_2" class="collapse" data-parent="#accordion_1" role="tabpanel">
        <div class="card-body">
          <div class="row">
            <div class="col-xl-12">
              <section class="hk-sec-wrapper">

                <div class="row">
                  <div class="col-sm">
                    <div class="table-wrap">
                      <div class="row">

                        <div class="col-8 dataTables_wrapper">
                          <div class="dataTables_length" style="display: inline-block;">
                            <label>{{trans.get('keys.hien_thi')}}
                              <select v-model="row_ass"
                                      class="custom-select custom-select-sm form-control form-control-sm"
                                      @change="getUserNeedAssign(1)">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="50">50</option>
                              </select>
                            </label>
                          </div>
                          <div class="fillterConfirm" style="display: inline-block;">
                            <label>
                              <select v-model="position_ass" class="custom-select custom-select-sm form-control form-control-sm" @change="getUserNeedAssign(1)">
                                <option value="">{{ trans.get('keys.chon_vi_tri') }}</option>
                                <option value="manager">{{ trans.get('keys.manager') }}</option>
                                <option value="leader">{{ trans.get('keys.leader') }}</option>
                                <option value="employee">{{ trans.get('keys.employee') }}</option>
                              </select>
                            </label>
                          </div>
                        </div>

                        <div class="col-4">
                            <form v-on:submit.prevent="getUserNeedAssign(1)">
                              <div class="d-flex flex-row form-group">
                                <input v-model="keyword_ass" type="text"
                                       class="form-control search_text"
                                       :placeholder="trans.get('keys.nhap_username_email_fullname') + ' ...'">
                                <button type="button" id="btnFilterUser"
                                        class="btn btn-primary btn-sm"
                                        @click="getUserNeedAssign(1)">
                                  {{trans.get('keys.tim')}}
                                </button>
                              </div>
                            </form>
                          </div>

                        <table id="datable_1" class="table_res">
                          <thead>
                          <tr>
                            <th>{{trans.get('keys.stt')}}</th>
                            <th>{{trans.get('keys.username')}}</th>
                            <th class=" mobile_hide" style="width: 30%;">
                              {{trans.get('keys.ho_ten')}}
                            </th>
                            <th>{{trans.get('keys.vi_tri')}}</th>
                            <th class="text-center">
                              <input class="selection-all" type="checkbox" v-model="allSelected" @click="selectAllAssgin()">
                            </th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr v-if="userNeedAssigns.length === 0">
                            <td colspan="5">{{ trans.get('keys.khong_tim_thay_du_lieu') }}</td>
                          </tr>
                          <tr v-else v-for="(user,index) in userNeedAssigns">
                            <td>{{ (current_ass-1)*row_ass+(index+1) }}</td>
                            <td>{{ user.username }}</td>
                            <td class=" mobile_hide">{{ user.fullname }}</td>
                            <td>{{ trans.get('keys.' + user.position) }}</td>
                            <td class="text-center">
                              <input class="selection-child" type="checkbox" :value="user.user_id + '/' + user.position" v-model="userAssigns" @change="onCheckboxEnrol()"/>
                            </td>
                          </tr>
                          </tbody>
                        </table>

                        <v-pagination v-if="userNeedAssigns.length !== 0" v-model="current_ass" @input="onPageChange" :page-count="totalPages_ass" :classes=$pagination.classes></v-pagination>

                        <div class="col-12">
                          <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary btn-sm" @click="assignEmployee()">{{trans.get('keys.them')}}</button>
                          </div>
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
</template>

<script>
    //import vPagination from 'vue-plain-pagination'

    export default {
        props: ['organization_id'],
        //components: {vPagination},
        data() {
            return {
                userNeedAssigns: [],
                keyword_ass: '',
                position_ass: '',
                current_ass: 1,
                totalPages_ass: 0,
                row_ass: 5,

                userAssigns: [],
                allSelected: false
            }
        },
        methods: {
            getUserNeedAssign(paged) {
                axios.post('/organization-employee/list-user', {
                    page: paged || this.current_ass,
                    keyword: this.keyword_ass,
                    row: this.row_ass,
                    position: this.position_ass
                })
                    .then(response => {
                        this.userNeedAssigns = response.data.data.data;
                        this.current_ass = response.data.pagination.current_page;
                        this.totalPages_ass = response.data.pagination.total;
                        this.uncheckAssignAll();
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getUserNeedAssign();
            },
            onSelectChange(event) {
                this.userAssigns = [];
                this.getUserNeedAssign();
            },
            selectAllAssgin: function () {
                this.userAssigns = [];
                this.allSelected = !this.allSelected;
                if (this.allSelected) {
                    var countEnrol = this.userNeedAssigns.length;
                    if (countEnrol > 0) {
                        for (var i = 0; i < countEnrol; i++) {
                            this.userAssigns.push(this.userNeedAssigns[i].user_id.toString() + '/' + this.userNeedAssigns[i].position.toString());
                        }
                    }
                }
            },
            onCheckboxEnrol() {
                this.allSelected = false;
            },
            assignEmployee() {
                let current_pos = this;
                if (this.userAssigns.length === 0) {
                    toastr['error'](this.trans.get('keys.ban_chua_chon_nhan_vien'), this.trans.get('keys.loi'));
                    return;
                }
                axios.post('/organization-employee/assign', {
                    users: this.userAssigns,
                    organization_id: this.organization_id
                })
                    .then(response => {
                        if (response.data.status) {
                          toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                          this.getUserNeedAssign(this.current_ass);
                          this.$parent.getDataList();
                        } else {
                          toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                      toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            },
            uncheckAssignAll() {
              this.allSelected = true;
              this.selectAllAssgin();
            },
        },
        mounted() {
            this.getUserNeedAssign();
        }
    }
</script>

<style scoped>

</style>
