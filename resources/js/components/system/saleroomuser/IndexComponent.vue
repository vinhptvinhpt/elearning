<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.diem_ban') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="card">
        <div class="card-body">
          <div class="edit_city_form form-material" v-if="saleroom_id != 0">
            <h5 class="mb-20 text-uppercase">{{trans.get('keys.danh_sach_nhan_vien_cua_diem_ban')}} : {{data.saleroom.name}}</h5>
            <div class="mb-20">
              <div class="accordion">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <a class="collapsed" id="formCreateNew" role="button" data-toggle="collapse"
                       href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                  </div>
                  <div id="collapse_1" class="collapse" role="tabpanel">
                    <div class="card-body">
                      <h6 class="mb-20">{{trans.get('keys.them_nhan_vien_moi_cho_diem_ban')}}</h6>
                      <create-user-by-sale-room :sale_room_id="saleroom_id" :type="type"></create-user-by-sale-room>
                    </div>
                  </div>
                </div>
                <div class="card">
                  <!--Keep for border bottom of top card-->
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-8 dataTables_wrapper">
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
                <div class="fillterConfirm mr-2" style="display: inline-block;">
                  <label>
                    <select class="form-control search_text" v-model="working_status">
                      <option value="">-- {{trans.get('keys.tinh_trang_lam_viec')}} --</option>
                      <option :value="0">{{trans.get('keys.dang_cong_tac')}}</option>
                      <option :value="1">{{trans.get('keys.nghi_cong_tac')}}</option>
                    </select>
                  </label>
                </div>
                <div class="fillterConfirm" style="display: inline-block;">
                  <label>
                    <select class="form-control search_text" v-model="pos" @change="getListUsers(1)">
                      <option value="0">-- {{trans.get('keys.diem_ban')}} --</option>
                      <option v-for="item in poslist" :value="item.id">{{item.name}}</option>
                    </select>
                  </label>
                </div>
              </div>
              <div class="col-sm-4">
                <form v-on:submit.prevent="getListUsers(1)">
                  <div class="d-flex flex-row form-group">
                    <input  v-model="keyword" type="text"
                            class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa')">
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
                    <th>{{trans.get('keys.ten_nhan_vien')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.diem_ban')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length == 0">
                      <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(user,index) in posts">
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td>{{ user.fullname }}</td>
                      <td class=" mobile_hide">{{ user.cmtnd }}</td>
                      <td class=" mobile_hide">{{ user.phone }}</td>
                      <td class=" mobile_hide">{{ user.email }}</td>
                      <td class=" mobile_hide">{{ user.posname }}</td>
                      <td>
                        <router-link class="btn waves-effect waves-light btn-sm btn-primary"
                                     :to="{ name: 'EditDetailUserById', params: { user_id: user.user_id }, query: {type: 'sale_room_user'} }">
                          {{trans.get('keys.xem')}}
                        </router-link>
                      </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ten_nhan_vien')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.diem_ban')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                    </tfoot>
                  </table>

                  <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

                </div>
              </div>
            </div>
          </div>
          <div class="edit_city_form form-material" v-else>
            <p style="color: black">{{trans.get('keys.ban_chua_co_diem_ban_hang_nao')}}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import CreateUserBySaleRoom from "../user/CreateUserBySaleRoomComponent";

    export default {
        components: {
          CreateUserBySaleRoom,
        },
        props: [
          //'saleroom_id',
          //'type'
        ],
        //components: {vPagination},
        data() {
            return {
                data:{
                    saleroom:{
                        name:'',
                        code:'',
                        user_id:0,
                        branch_id:0,
                        address:'',
                        description:'',
                    },
                },
                name_section : 'sale_room',
                posts: {},
                keyword: '',
                working_status: '',
                current: 1,
                totalPages: 0,
                row: 10,
                poslist:{},
                pos:0,
                saleroom_id: 0,
                type: 'pos'
            }
        },
        methods: {
            getListUsers(paged) {
                if(this.saleroom_id !== 0){
                    axios.post('/sale_room_user/list_users', {
                        page: paged || this.current,
                        keyword: this.keyword,
                        row: this.row,
                        id:this.saleroom_id,
                        working_status: this.working_status,
                        pos: this.pos
                    })
                    .then(response => {
                        this.posts = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
                }
            },
            getDataSaleRoom(){
                if(this.saleroom_id !== 0){
                    axios.post('/system/organize/saleroom/detail_data/'+this.saleroom_id)
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
                }
            },
            getPosList(){
                axios.post('/system/organize/saleroom/list_pos_by_manage_pos')
                    .then(response => {
                        this.poslist = response.data;
                    })
                    .catch(error => {
                        this.poslist = [];
                    })
            },
            onPageChange() {
               this.getListUsers();
            },
            async fetch() {
              axios.post('/bridge/fetch', {
                view: 'SaleRoomUserIndex'
              })
                .then(response => {
                  this.saleroom_id = response.data.saleroom_id;
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
            this.getDataSaleRoom();
            this.getPosList();
        },
        created () {
          // fetch the data when the view is created and the data is
          // already being observed
          this.fetch();
        },
    }
</script>

<style scoped>

</style>
