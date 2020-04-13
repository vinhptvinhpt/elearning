<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/system/user">{{ trans.get('keys.quan_tri_nguoi_dung') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.tai_khoan_bi_khoa') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title" v-if="type == 'system'">
            {{trans.get('keys.danh_sach_nguoi_dung_khoa_tai_khoan')}}</h5>
          <h5 class="hk-sec-title" v-else-if="type == 'teacher'">
            {{trans.get('keys.danh_sach_giang_vien_khoa_tai_khoan')}}</h5>
          <h5 class="hk-sec-title" v-else>{{trans.get('keys.danh_sach_hoc_vien_khoa_tai_khoan')}}</h5>
          <div class="row">
            <div class="col-sm">
              <div class="table-wrap">
                <div class="row">
                  <div class="col-sm-8 dataTables_wrapper">
                    <div class="dataTables_length" style="display: inline-block;">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row"
                                class="custom-select custom-select-sm form-control form-control-sm"
                                @change="getUser(1)">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                      </label>
                    </div>
                    <div class="fillterConfirm" style="display: inline-block;"
                         v-if="type != 'student' && type != 'teacher'">
                      <label>
                        <select v-model="role_name"
                                class="custom-select custom-select-sm form-control form-control-sm"
                                @change="getUser(1)">
                          <option value="">{{trans.get('keys.theo_quyen')}}</option>
                          <option v-for="role in listrole" :value="role.name">{{ role.name }}
                          </option>
                        </select>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <form v-on:submit.prevent="getUser(1)">
                      <div class="d-flex flex-row form-group">
                        <input v-model="keyword" type="text"
                               class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa') + ' ...'">
                        <button type="button" id="btnFilter" class="btn btn-primary btn-sm btn_fillter"
                                @click="getUser(1)">
                          {{trans.get('keys.tim')}}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table_res">
                    <thead>
                    <tr>
                      <th>
                        <input v-model="allSelected" @click="selectAllCheckbox()"
                               id="branch-select-all" type="checkbox"
                               class="filled-in chk-col-light-blue" name="select_all" value=""/>
                        <label for="branch-select-all"></label>
                      </th>
                      <th class=" mobile_hide">{{trans.get('keys.cmtnd')}}</th>
                      <th>{{trans.get('keys.tai_khoan')}}</th>
                      <th>{{trans.get('keys.ho_va_ten')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length == 0">
                      <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(user,index) in posts">
                      <td>
                        <input v-model="user_restore" :value="user.user_id" type="checkbox"
                               :id="'restore_user'+user.user_id"
                               class="filled-in chk-col-light-blue check_box_branch"><label
                        :for="'restore_user'+user.user_id"></label>
                      </td>
                      <td class=" mobile_hide">{{ user.cmtnd }}</td>
                      <td>

                        <router-link
                          :to="{ name: 'EditUserById', params: { user_id: user.user_id }, query: {type: type} }">
                          {{ user.username }}
                        </router-link>
                      </td>

                      <td>{{ user.fullname }}</td>
                      <td class=" mobile_hide">{{ user.email }}</td>
                      <td class="text-center">

                        <router-link
                            :title="trans.get('keys.sua')"
                            class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                            :to="{ name: 'EditDetailUserById', params: { user_id: user.user_id }, query: {type: type} }">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                        </router-link>

                        <!--<a title="Khôi phục tài khoản" href="#" class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2" @click="restoreUser(user.user_id)">
                            <span class="btn-icon-wrap"><i class="fa fa-refresh"></i></span>
                        </a>-->
                      </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                      <th>
                        <input v-model="allSelected" @click="selectAllCheckbox()"
                               id="branch-select-all2" type="checkbox"
                               class="filled-in chk-col-light-blue" name="select_all" value=""/>
                        <label for="branch-select-all2"></label>
                      </th>
                      <th class=" mobile_hide">{{trans.get('keys.cmtnd')}}</th>
                      <th>{{trans.get('keys.tai_khoan')}}</th>
                      <th>{{trans.get('keys.ho_va_ten')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                      <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </tfoot>
                  </table>
                  <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                  </div>
                  <div class="text-left">
                    <button :title="trans.get('keys.khoi_phuc_tai_khoan_da_chon')" type="button"
                            class="btn btn-sm btn-success mt-3" @click="restoreSelectUser()">
                      {{trans.get('keys.khoi_phuc_tai_khoan')}}
                    </button>
                    <button :title="trans.get('keys.xoa_vinh_vien_tai_khoan_da_chon')" type="button" style="float: right;"
                            class="btn btn-sm btn-danger mt-3" @click="clearSelectUser()">
                      {{trans.get('keys.xoa_vinh_vien')}}
                    </button>
                  </div>
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

    export default {
        props: ['type'],
        //components: {vPagination},
        data() {
            return {
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
                urlListUser: '/system/user/list_trash',
                user_restore: [],
                allSelected: false,
                role_name: '',
                listrole: []
            }
        },
        methods: {
            getListRole() {
                axios.post('/system/user/get_list_role')
                    .then(response => {
                        this.listrole = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            selectAllCheckbox() {
                this.user_restore = [];
                if (!this.allSelected) {
                    this.posts.forEach((select) => {
                        this.user_restore.push(select.user_id);
                    });
                }
            },
            clearSelectUser() {
                var user_restore = this.user_restore;
                let current_pos = this;
                if (this.user_restore.length == 0) {
                    swal({
                        title: current_pos.trans.get('keys.thong_bao'),
                        text: current_pos.trans.get('keys.ban_chua_chon_tai_khoan'),
                        type: "warning",
                        showCancelButton: false,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true
                    });
                    return;
                }
                swal({
                    title: current_pos.trans.get('keys.thong_bao'),
                    text: current_pos.trans.get('keys.ban_muon_xoa_vinh_vien_nhung_tai_khoan_da_chon'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/clear_list_user', {
                        user_clear: user_restore
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
            restoreSelectUser() {
                var user_restore = this.user_restore;
                let current_pos = this;
                if (this.user_restore.length == 0) {
                    swal({
                        title: this.trans.get('keys.thong_bao'),
                        text: this.trans.get('keys.ban_chua_chon_tai_khoan'),
                        type: "warning",
                        showCancelButton: false,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true
                    });
                    return;
                }
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_khoi_phuc_lai_tai_khoan_da_chon'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/restore_list_user', {
                        user_restore: user_restore
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
            getUser(paged) {
                /*if(this.type == 'teacher'){
                    this.urlListUser = '/education/user_teacher/list_trash';
                }
                if(this.type == 'student'){
                    this.urlListUser = '/education/user_student/list_trash';
                }*/
                axios.post(this.urlListUser, {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    role_name: this.role_name,
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getUser();
            },
            restoreUser(user_id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_khoi_phuc_lai_tai_khoan_nay'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/restore', {user_id: user_id})
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });

                return false;
            },
            deletePost(url) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });

                return false;
            }
        },
        mounted() {
            //this.getUser();
            this.getListRole();
        }
    }
</script>

<style scoped>

</style>
