<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.user_exam') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">
          <h5 class="hk-sec-title">
            {{trans.get('keys.danh_sach_nguoi_dung_thi_lai')}}</h5>
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
                  </div>
                  <div class="col-sm-4">
                    <form v-on:submit.prevent="getUser(1)">
                      <div class="d-flex flex-row form-group">
                        <input v-model="keyword" type="text"
                               class="form-control search_text"
                               :placeholder="trans.get('keys.nhap_tu_khoa') + ' ...'">
                        <button type="button" id="btnFilter"
                                class="btn btn-primary btn-sm btn_fillter"
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
                      <th class="text-center">{{trans.get('keys.diem_cuoi_cung')}}</th>
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
                          :to="{name: 'EditUserById', params: { user_id: user.user_id }, query: {type: 'user-exam'} }">
                          {{ user.username }}
                        </router-link>
                      </td>

                      <td>{{ user.fullname }}</td>
                      <td class=" mobile_hide">{{ user.email }}</td>
                      <td class="text-center">
                        <div v-if="user.finalgrade!=undefined">
                          {{parseFloat(user.finalgrade).toFixed(2)}}
                        </div>
                        <div v-else>
                          0
                        </div>
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
                      <th class="text-center">{{trans.get('keys.diem_cuoi_cung')}}</th>
                    </tr>
                    </tfoot>
                  </table>
                  <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                  :classes=$pagination.classes
                                  :labels=$pagination.labels></v-pagination>
                  </div>
                  <div class="text-left">
                    <button :title="trans.get('keys.cho_thi_lai')" type="button"
                            class="btn btn-sm btn-success mt-3" @click="resetSelectUser()">
                      {{trans.get('keys.cho_thi_lai')}}
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
        //components: {vPagination},
        data() {
            return {
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
                user_restore: [],
                allSelected: false,
                role_name: '',
                listrole: []
            }
        },
        methods: {
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
                if (this.user_restore.length == 0) {
                    swal({
                        title: "Thông báo",
                        text: "Bạn chưa chọn tài khoản.",
                        type: "warning",
                        showCancelButton: false,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true
                    });
                    return;
                }
                swal({
                    title: "Thông báo",
                    text: "Bạn muốn xóa vĩnh viễn những tài khoản đã chọn.",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/clear_list_user', {
                        user_clear: user_restore
                    })
                        .then(response => {
                            roam_message(response.data.status, response.data.message);
                        })
                        .catch(error => {
                            roam_message('error', 'Lỗi hệ thống. Thao tác thất bại');
                        });
                });
            },
            resetSelectUser() {
                var user_restore = this.user_restore;
                if (this.user_restore.length == 0) {
                    swal({
                        title: "Thông báo",
                        text: "Bạn chưa chọn tài khoản.",
                        type: "warning",
                        showCancelButton: false,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true
                    });
                    return;
                }
                swal({
                    title: "Thông báo",
                    text: "Cho phép người dùng được thi lại.",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/education/resetexam/resetuser', {
                        user_restore: user_restore
                    })
                        .then(response => {
                            roam_message(response.data.status, response.data.message);
                        })
                        .catch(error => {
                            roam_message('error', 'Lỗi hệ thống. Thao tác thất bại');
                        });
                });
            },
            getUser(paged) {
                axios.post('/education/resetexam/getlistuser', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
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
                swal({
                    title: "Bạn muốn khôi phục lại tài khoản này",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/user/restore', {user_id: user_id})
                        .then(response => {
                            roam_message(response.data.status, response.data.message);
                        })
                        .catch(error => {
                            roam_message('error', 'Lỗi hệ thống. Thao tác thất bại');
                        });
                });

                return false;
            },
            deletePost(url) {
                swal({
                    title: "Bạn muốn xóa mục đã chọn",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            roam_message(response.data.status, response.data.message);
                        })
                        .catch(error => {
                            roam_message('error', 'Lỗi hệ thống. Thao tác thất bại');
                        });
                });

                return false;
            }
        },
        mounted() {
            //this.getUser();
            //this.getListRole();
        }
    }
</script>

<style scoped>

</style>
