<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link :to="{name: 'RoleEdit', params: {role_id: role_id}}">{{ trans.get('keys.chi_tiet_quyen') }}</router-link></li>
            <li class="breadcrumb-item active">{{ role_name }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="row mb-4">
            <div class="col-sm">
              <div class="accordion" id="accordion_1">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <a class="" role="button" data-toggle="collapse" href="#collapse_2" aria-expanded="true"><i class="fal fa-upload mr-3"></i>{{trans.get('keys.gan_them_user')}}</a>
                  </div>
                  <div id="collapse_2" class="collapse show" data-parent="#accordion_1">
                    <div class="card-body">
                      <h5 class="mb-20">{{trans.get('keys.them_nguoi_dung')}}</h5>
                      <div class="row">
                        <div class="col-sm-5 col-md-8 dataTables_wrapper">
                          <div class="dataTables_length" style="display: inline-block;">
                            <label>{{trans.get('keys.hien_thi')}}
                              <select v-model="row2" class="custom-select custom-select-sm form-control form-control-sm" @change="getListAddUsers(1)">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                              </select>
                            </label>
                          </div>
                        </div>
                        <div class="col-sm-7 col-md-4">
                          <form v-on:submit.prevent="getListAddUsers(1)">
                            <div class="d-flex flex-row form-group">
                              <input  v-model="keyword2" type="text"
                                      class="form-control search_text" :placeholder="trans.get('keys.ten_nguoi_dung_so_cmtnd_email_so_dien_thoai')+ ' ...'">
                              <button type="button" class="btn btn-primary btn-sm btn_fillter"
                                      @click="getListAddUsers(1)">
                                {{trans.get('keys.tim')}}
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                      <div class="table-responsive">
                        <table class="table_res">
                          <thead>
                          <th>
                            <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                            <label for="branch-select-all"></label>
                          </th>
                          <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                          <th>{{trans.get('keys.tai_khoan')}}</th>
                          <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.sdt')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                          </thead>
                          <tbody>
                          <tr v-if="posts2.length == 0">
                            <td colspan="5">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                          </tr>
                          <tr v-for="add_user in posts2">
                            <td>
                              <input v-model="user_add" :value="add_user.user_id" type="checkbox" :id="'add_user'+add_user.user_id" class="filled-in chk-col-light-blue check_box_branch"><label :for="'add_user'+add_user.user_id"></label>
                            </td>
                            <td class=" mobile_hide">{{ add_user.cmtnd }}</td>
                            <td>{{ add_user.username }}</td>
                            <td>{{ add_user.fullname }}</td>
                            <td class=" mobile_hide">{{ add_user.phone }}</td>
                            <td class=" mobile_hide">{{ add_user.email }}</td>
                          </tr>
                          </tbody>
                          <tfoot>
                          <th>
                            <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all2" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                            <label for="branch-select-all2"></label>
                          </th>
                          <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                          <th>{{trans.get('keys.tai_khoan')}}</th>
                          <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.sdt')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                          </tfoot>
                        </table>
                        <v-pagination v-model="current2" @input="onPageChange" :page-count="totalPages2" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                      </div>
                      <div class="text-right mt-2">
                        <button @click="addUserRole()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.them_nguoi_dung')}}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <h5 class="mb-20">{{trans.get('keys.danh_sach_nguoi_dung')}}</h5>
                  <p class="mb-10">{{trans.get('keys.tong_so_nguoi_dung_thuoc_quyen_hien_tai_la')}} : {{user_length}} {{trans.get('keys.nguoi_dung')}}.</p>
                  <div class="row">
                    <div class="col-sm-5 col-md-8 dataTables_wrapper">
                      <div class="dataTables_length" style="display: inline-block;">
                        <label>{{trans.get('keys.hien_thi')}}
                          <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getListUsers(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-7 col-md-4">
                      <form v-on:submit.prevent="getListUsers(1)">
                        <div class="d-flex flex-row form-group">
                          <input  v-model="keyword" type="text"
                                  class="form-control search_text" :placeholder="trans.get('keys.ten_nguoi_dung_so_cmtnd_email_so_dien_thoai')+ ' ...'">
                          <button type="button" class="btn btn-primary btn-sm btn_fillter"
                                  @click="getListUsers(1)">
                            {{trans.get('keys.tim')}}
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <table class="table_res">
                          <thead>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                          <th>{{trans.get('keys.tai_khoan')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.sdt')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                          <th>{{trans.get('keys.hanh_dong')}}</th>
                          </thead>
                          <tbody>
                          <tr v-if="posts.length == 0">
                            <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                          </tr>
                          <tr v-else v-for="(user,index) in posts">
                            <td>{{ (current-1)*row+(index+1) }}</td>
                            <td class=" mobile_hide">{{ user.cmtnd }}</td>
                            <td>{{ user.username }}</td>
                            <td class=" mobile_hide">{{ user.fullname }}</td>
                            <td class=" mobile_hide">{{ user.phone }}</td>
                            <td class=" mobile_hide">{{ user.email }}</td>
                            <td>
                              <router-link
                                :title="trans.get('keys.sua')"
                                :to="{ name: 'EditUserById', params: { user_id: user.user_id }}"
                                class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                                <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                              </router-link>
                              <a href="javascript(0)" @click.prevent="removeUser(user.user_id)"
                                 class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                                <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                              </a>
                            </td>
                          </tr>
                          </tbody>
                          <tfoot>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                          <th>{{trans.get('keys.tai_khoan')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.ten_nguoi_dung')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.sdt')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                          <th>{{trans.get('keys.hanh_dong')}}</th>
                          </tfoot>
                        </table>
                        <v-pagination v-model="current"
                                      @input="onPageChange"
                                      :page-count="totalPages"
                                      :classes=$pagination.classes
                                      :labels=$pagination.labels>
                        </v-pagination>
                      </div>
                      <div class="text-right mt-2">
                        <router-link :to="{name: 'RoleEdit', params: {role_id: role_id}}" class="btn btn-secondary btn-sm">
                          {{trans.get('keys.quay_lai')}}
                        </router-link>
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
        props: ['role_id'],
        //components: {vPagination},
        data() {
            return {
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
                posts2: {},
                keyword2: '',
                current2: 1,
                totalPages2: 0,
                row2:10,
                user_add:[],
                allSelected: false,
                user_length:0,
                role_name: ''
            }
        },
        methods: {
            fetch() {
              axios.post('/bridge/fetch', {
                role_id: this.role_id,
                view: 'RoleUserIndex'
              })
                .then(response => {
                  this.role_name = response.data.role_name;
                })
                .catch(error => {
                  console.log(error);
                })
            },
            selectAllCheckbox(){
                this.user_add = [];
                if(!this.allSelected){
                    this.posts2.forEach((select) => {
                        this.user_add.push(select.id);
                    });
                }
            },
            addUserRole(){
                let loader = $('.preloader-it');
                loader.fadeIn();
                if (this.user_add.length > 0) {
                    axios.post('/role/list_user/add_user_by_role',{
                        user_add:this.user_add,
                        role_id:this.role_id
                    })
                        .then(response => {
                            loader.fadeOut();
                            toastr[response.data.status](response.data.message, this.trans.get('keys.thong_bao'));
                            this.getListAddUsers(this.current2);
                            this.getListUsers(this.current);
                        })
                        .catch(error => {
                            loader.fadeOut();
                            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                        });
                }
            },
            getListAddUsers(paged2) {
                axios.post('/role/list_user/list_add_user', {
                    page: paged2 || this.current2,
                    keyword: this.keyword2,
                    row: this.row2,
                    role_id:this.role_id
                })
                    .then(response => {
                        this.posts2 = response.data.data ? response.data.data.data : [];
                        this.current2 = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages2 = response.data.pagination ? response.data.pagination.total : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getListUsers(paged) {
                axios.post('/role/list_user/list_user_role', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    id:this.role_id
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                        this.user_length = response.data ? response.data.length : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getListUsers();
                this.getListAddUsers();
            },
            removeUser(user_id){
                var role_id = this.role_id;
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_go_user_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: false
                }, function () {
                    let loader = $('.preloader-it');
                    loader.fadeIn();
                    axios.post('/role/list_user/remove_user_role',{
                        role_id:role_id,
                        user_id:user_id
                    })
                        .then(response => {
                            loader.fadeOut();
                            roam_message(response.data.status,response.data.message);
                            current_pos.getListUsers(current_pos.current);
                        })
                        .catch(error => {
                            loader.fadeOut();
                            roam_message('error', current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
        },
        mounted() {
          this.fetch();
        }
    }
</script>

<style scoped>

</style>
