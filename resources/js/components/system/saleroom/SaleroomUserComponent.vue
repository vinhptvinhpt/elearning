<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li v-if="owner_type === 'master'" class="breadcrumb-item"><router-link to="/tms/branch/list">{{ trans.get('keys.dai_ly') }}</router-link></li>
            <li v-if="owner_type === 'master'" class="breadcrumb-item">{{ branch_name }}</li>
            <li class="breadcrumb-item"><router-link to="/tms/saleroom/list">{{ trans.get('keys.danh_sach_diem_ban') }}</router-link></li>
            <li class="breadcrumb-item"><router-link :to="{name: 'SaleroomEditByRole', params: {saleroom_id: id}}">{{ data.saleroom.name }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_nhan_vien') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="card">
            <div class="card-body">
              <div class="edit_city_form form-material">
                <h5 class="mb-20"><span class="text-uppercase">{{trans.get('keys.danh_sach_nhan_vien_cua_diem_ban')}} : </span>{{data.saleroom.name}}</h5>
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
                          <saleroom-user-create :sale_room_id="id" :type="type"></saleroom-user-create>
                        </div>
                      </div>
                    </div>
                    <div class="card">
                      <!--Keep for border bottom of top card-->
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-5 dataTables_wrapper">
                    <div class="dataTables_length" style="display: inline-block;">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row"
                                class="custom-select custom-select-sm form-control form-control-sm"
                                @change="getListUsers(1)">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                      </label>
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
                  <div class="col-sm-4">
                    <form v-on:submit.prevent="getListUsers(1)">
                      <div class="d-flex flex-row form-group">
                        <input v-model="keyword" type="text"
                               class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa')+' ...'">
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
                        <tr>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th>{{trans.get('keys.ten_nhan_vien')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
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
                          <td>

                            <router-link
                              :title="trans.get('keys.xem')"
                              class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                              :to="{ name: 'SaleroomUserViewByRole', params: { saleroom_id: data.saleroom.id , user_id:  user.user_id}, query: {type: owner_type} }">
                              <span class="btn-icon-wrap"><i class="fal fa-street-view"></i></span>
                            </router-link>

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
                          <th>{{trans.get('keys.hanh_dong')}}</th>
                        </tr>
                        </tfoot>
                      </table>

                      <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

                    </div>
                  </div>
                </div>
                <div class="row mt-30">
                  <div class="col-12">
                    <div class="button-list">
                      <router-link
                        :to="{ name: 'SaleroomIndexByRole', query: { branch_id: branchId } }">
                        <span class="btn btn-secondary btn-sm">{{ trans.get('keys.quay_lai') }}</span>
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
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import SaleroomUserCreate from '../user/CreateUserBySaleRoomComponent'

    export default {
        props: ['id', 'type', 'owner_type'],
        //components: {vPagination},
        components: {SaleroomUserCreate},
        data() {
            return {
                branchId: 0,
                branch_name: '',
                branch_id: 0,
                data: {
                    saleroom: {
                        name: '',
                        code: '',
                        user_id: 0,
                        branch_id: 0,
                        address: '',
                        description: '',
                    },
                },
                posts: {},
                keyword: '',
                working_status: '',
                current: 1,
                totalPages: 0,
                row: 10,
            }
        },
        methods: {
            getListUsers(paged) {
                axios.post('/system/organize/saleroom/list_user_by_sale_room', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    id: this.id,
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
            removeUser(user_id) {
                let current_pos = this;
                var sale_room_id = this.id;
                swal({
                    title: this.trans.get('keys.ban_muon_go_user_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/saleroom/remove_user', {
                        sale_room_id: sale_room_id,
                        user_id: user_id
                    })
                        .then(response => {
                            if (response.data === 'success') {
                                swal({
                                    title: current_pos.trans.get('keys.thong_bao'),
                                    text: current_pos.trans.get('keys.go_nhan_vien_thanh_cong'),
                                    type: "success",
                                    showCancelButton: false,
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true
                                }, function () {
                                    location.reload();
                                });
                            } else {
                                swal({
                                    title: current_pos.trans.get('keys.loi_he_thong'),
                                    text: current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'),
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
                                title: current_pos.trans.get('keys.loi_he_thong'),
                                text: current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'),
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        });
                });
            },
            getDataSaleRoom() {
                axios.post('/system/organize/saleroom/detail_data/' + this.id)
                    .then(response => {
                        this.data = response.data;
                        this.setBranchId();
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            setBranchId() {
                if(this.owner_type === "master") {
                    this.branchId = this.branch_id
                }
            },
            fetch() {
              axios.post('/bridge/fetch', {
                branch_id: this.saleroom.branch_id,
                view: 'SaleroomUserIndexByRole'
              })
                .then(response => {
                  this.branch_id = response.data.branchId;
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
            this.getDataSaleRoom();
        }
    }
</script>

<style scoped>

</style>
