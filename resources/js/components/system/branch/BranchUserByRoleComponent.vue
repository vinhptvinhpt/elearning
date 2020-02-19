<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li v-if="owner_type === 'master'" class="breadcrumb-item">
              <router-link :to="{ name: 'BranchIndexByRole'}">
                {{ trans.get('keys.dai_ly') }}
              </router-link>
            </li>
            <li class="breadcrumb-item">{{ branch_name }}</li>
            <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_nhan_vien') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="card">
        <div class="card-body">
          <div class="edit_city_form form-material">
            <h5 class="mb-20"><span class="text-uppercase">{{trans.get('keys.danh_sach_nhan_vien_cua_dai_ly')}} : </span>{{branch_name}}</h5>
            <div class="mb-20">
              <div class="accordion">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <a class="collapsed" id="formCreateNew" role="button" data-toggle="collapse"
                       href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                  </div>
                  <div id="collapse_1" class="collapse" role="tabpanel">
                    <div class="card-body">
                      <h6 class="mb-20">{{trans.get('keys.them_nhan_vien_moi_cho_dai_ly')}}</h6>
                      <branch-create-user :branch_id="id" :branch_type="branch_type" :saleroom_type="saleroom_type"></branch-create-user>
                    </div>
                  </div>
                </div>
                <div class="card">
                  <!--Keep for border bottom of top card-->
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-3 dataTables_wrapper">
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

              <div class="col-sm-2">
                <div class="d-flex flex-row form-group">
                  <input  v-model="keyword" type="text" class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa')+' ...'">
                </div>
              </div>

              <div class="col-sm-3">
                <div class="d-flex flex-row form-group">
                  <select class="form-control search_text" v-model="saleroom">
                    <option value="">-- {{trans.get('keys.noi_lam_viec')}} --</option>
                    <option v-for="saleroom_item in salerooms" :value="saleroom_item.sale_room_id" >{{saleroom_item.sale_room_name.name}}</option>
                    <option value="0">{{trans.get('keys.truc_thuoc_dai_ly')}}</option>
                  </select>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="d-flex flex-row form-group">
                  <select class="form-control search_text" v-model="working_status">
                    <option value="">-- {{trans.get('keys.tinh_trang_lam_viec')}} --</option>
                    <option :value="0">{{trans.get('keys.dang_cong_tac')}}</option>
                    <option :value="1">{{trans.get('keys.nghi_cong_tac')}}</option>
                  </select>
                </div>
              </div>

              <div class="col-sm-1">
                <form v-on:submit.prevent="getListUsers(1)">
                  <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm btn_fillter" @click="getListUsers(1)">{{trans.get('keys.tim')}}</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table_res">
                    <thead>
                    <tr>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ten_nhan_vien')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.noi_lam_viec')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length === 0">
                      <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(user,index) in posts">
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td>{{ user.fullname }}</td>
                      <td class=" mobile_hide">{{ user.cmtnd }}</td>
                      <td class=" mobile_hide">{{ user.phone }}</td>
                      <td class=" mobile_hide">{{ user.email }}</td>
                      <td class=" mobile_hide">{{ user.type === 'pos' ? user.saleroom_name : user.branch_name }}</td>
                      <td>
                        <router-link :title="trans.get('keys.xem')"
                                     class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                     :to="{
                                             name: 'BranchUserViewByRole',
                                             params: { branch_id: id, user_id: user.user_id },
                                             query: { type: owner_type }
                                     }">
                          <span class="btn-icon-wrap"><i class="fal fa-street-view"></i></span>
                        </router-link>
                        <!--<a href="javascript(0)" @click.prevent="removeUser(user.user_id)"
                           class="btn waves-effect waves-light btn-sm btn-danger">Gỡ</a>-->
                      </td>

                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ten_nhan_vien')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.noi_lam_viec')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                    </tr>
                    </tfoot>
                  </table>

                  <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

                </div>
                <div class="button-list">
                  <!--<a class="btn btn-primary btn-sm" :href="'/system/organize/saleroom/add_user/'+id">Thêm Nhân viên</a>-->
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
    import BranchCreateUser from '../user/CreateUserByBranchComponent'

    export default {
        props: [
          'id',
          'owner_type',
          'branch_type',
          'saleroom_type'
        ],
        //components: {vPagination},
        components: {BranchCreateUser},
        data() {
            return {
                data:{
                    branch:{
                        name:'',
                    },
                },
                saleroom:'',
                working_status: '',
                salerooms:[],
                posts: [
                    // {
                    //     fullname: "",
                    //     cmtnd: "",
                    //     phone: "",
                    //     email: "",
                    //     saleroom_name: "",
                    //     branch_name: "",
                    //     type: "",
                    //     user_id: 0
                    // }
                ],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
                branch_name: ''
            }
        },
        methods: {
            getListUsers(paged) {
                axios.post('/branch/list_user_by_branch', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    id: this.id,
                    saleroom: this.saleroom,
                    working_status: this.working_status
                })
                    .then(response => {
                        if (response.data) {
                            this.posts = response.data.data.data;
                            this.current = response.data.pagination.current_page;
                            this.totalPages = response.data.pagination.total;
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getListUsers();
            },
            removeUser(user_id, sale_room_id){
                swal({
                    title: "Bạn muốn gỡ User đã chọn",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/saleroom/remove_user',{
                        sale_room_id:sale_room_id,
                        user_id:user_id
                    })
                        .then(response => {
                            if(response.data === 'success'){
                                swal({
                                    title: "Thông báo",
                                    text: "Gỡ Nhân viên thành công.",
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }, function () {
                                    location.reload();
                                });
                            }else{
                                swal({
                                    title: "Lỗi hệ thốngs",
                                    text: "Thao tác thất bại.",
                                    type: "error",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                });
                            }
                        })
                        .catch(error => {
                            console.log(error.response.data);
                            swal({
                                title: "Lỗi hệ thống",
                                text: "Thao tác thất bại.",
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        });
                });
            },
            getSaleroom() {
                this.saleroom = '';
                axios.post('/branch/get_saleroom_by_branch', {
                    branch: this.id,
                })
                .then(response => {
                    this.salerooms = response.data;
                })
                .catch(error => {
                    console.log(error.response.data);
                });
            },
            fetch() {
              axios.post('/bridge/fetch', {
                branch_id: this.id,
                view: 'BranchUserIndexByRole'
              })
                .then(response => {
                  this.branch_name = response.data.branch_name;
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
            this.getSaleroom();
            this.fetch();
        }
    }
</script>

<style scoped>

</style>
