<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li v-if="owner_type === 'master'" class="breadcrumb-item">
              <router-link to="/tms/branch/list">
                {{ trans.get('keys.dai_ly') }}
              </router-link>
            </li>
            <li class="breadcrumb-item">{{ branch_name }}</li>
            <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_diem_ban') }}</li>
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
                <h5 class="mb-20"><span class="text-uppercase">{{trans.get('keys.danh_sach_diem_ban_cua_dai_ly')}} : </span>{{branch_name}}</h5>
                <div class="row">
                  <div class="col-sm-8 dataTables_wrapper">
                    <div class="dataTables_length" style="display: inline-block;">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getSaleRoomByBranch(1)">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <form v-on:submit.prevent="getSaleRoomByBranch(1)">
                      <div class="d-flex flex-row form-group">
                        <input  v-model="keyword" type="text"
                                class="form-control search_text" :placeholder="trans.get('keys.ten_diem_ban')+' ...'">
                        <button type="button" class="btn btn-primary btn-sm"
                                @click="getSaleRoomByBranch(1)">
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
                          <th>{{trans.get('keys.ten_diem_ban')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                          <th>{{trans.get('keys.hanh_dong')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="posts.length === 0">
                          <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                        </tr>
                        <tr v-else v-for="(room,index) in posts">
                          <td>{{ (current-1)*row+(index+1) }}</td>
                          <td>{{ room.name }}</td>
                          <td class=" mobile_hide" v-if="room.user">
                            <router-link :to="{ name: 'SaleroomUserViewByRole', params: { saleroom_id: room.id, user_id: room.user.id}, query: {type: owner_type} }">
                              {{ room.user.detail.fullname }}
                            </router-link>
                          </td>
                          <td class=" mobile_hide" v-else></td>
                          <td class=" mobile_hide">{{ room.address }}</td>
                          <td class=" mobile_hide">
                            <router-link :to="{ name: 'SaleroomUserIndexByRole', params: { saleroom_id: room.id }, query: {type: owner_type}}">
                              {{ room.saleroom_user.length }} ({{trans.get('keys.nhan_vien')}})
                            </router-link>
                          </td>
                          <td>

                            <router-link :title="trans.get('keys.sua')"
                                         :to="{
                                           name: 'SaleroomEditByRole',
                                           params: { saleroom_id: room.id },
                                           query: {type: owner_type}
                                         }"
                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                              <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                            </router-link>

                          </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                          <th>{{trans.get('keys.stt')}}</th>
                          <th>{{trans.get('keys.ten_diem_ban')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                          <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                          <th>{{trans.get('keys.hanh_dong')}}</th>
                        </tr>
                        </tfoot>
                      </table>

                      <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

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
        //components: {vPagination},
        props: ['branch_id'],
        data() {
            return {
                id: '',
                data:{
                    branch:{
                        name:'',
                    }
                },
                posts: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
                branch_name: '',
                owner_type: ''
            }
        },
        methods: {
            fetch() {
              axios.post('/bridge/fetch', {
                branch_id: this.branch_id,
                view: 'SaleroomIndexByRole'
              })
                .then(response => {
                  this.branch_name = response.data.branch_name;
                  this.owner_type = response.data.owner_type;
                  this.id = response.data.branch_id;
                  this.getSaleRoomByBranch();
                })
                .catch(error => {
                  console.log(error);
                })
            },
            getSaleRoomByBranch(paged) {
                axios.post('/saleroom/list_sale_room_by_branch', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    id: this.id,
                    row: this.row
                })
                    .then(response => {
                        if (response.data) {
                            this.posts = response.data.data.data;
                            this.current = response.data.pagination.current_page;
                            this.totalPages = response.data.pagination.total;
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            onPageChange() {
                this.fetch();
            },
            removeSaleRoom(sale_room_id){
                swal({
                    title: "Bạn muốn gỡ điểm bán đã chọn",
                    text: "Chọn 'ok' để thực hiện thao tác.",
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/branch/remove_sale_room',{
                        branch_id: this.id,
                        sale_room_id: sale_room_id
                    })
                        .then(response => {
                            if(response.data === 'success'){
                                swal({
                                    title: "Thông báo",
                                    text: "Gỡ điểm bán thành công.",
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
                            console.log(error);
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
            }
        },
        mounted() {
            //this.fetch();
            //ran at onPageChange()
        }
    }
</script>

<style scoped>

</style>
