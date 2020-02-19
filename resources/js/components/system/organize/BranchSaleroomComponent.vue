<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/system/organize/branch">{{ trans.get('keys.danh_sach_dai_ly') }}</router-link>
            <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_diem_ban') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="card">
        <div class="card-body">
          <div class="edit_city_form form-material">
            <h5 class="mb-20"><span>{{trans.get('keys.danh_sach_diem_ban_cua_dai_ly')}} : </span>{{data.branch.name}}</h5>

            <div class="mb-20 mt-20">

              <div class="accordion" id="accordion_1" style="border-bottom: 1px solid rgba(0,0,0,.125);">
                <div class="card">
                  <div class="card-header d-flex justify-content-between">
                    <a class="collapsed"
                       :href="trans.get('keys.language')+'/system/organize/saleroom?branch_id='+id" >
                      <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}
                    </a>
                    <router-link :to="{name: 'SaleroomIndex', query: {'branch_id': id}}" class="collapsed">
                      <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}
                    </router-link>
                  </div>
                </div>
              </div>
            </div>

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
                            class="form-control search_text" :placeholder="trans.get('keys.ten_ma_diem_ban')+' ...'">
                    <button type="button" class="btn btn-primary btn-sm btn_fillter"
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
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ma')}}</th>
                    <th>{{trans.get('keys.ten_diem_ban')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                    </thead>
                    <tbody>
                    <tr v-if="posts.length == 0">
                      <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                    </tr>
                    <tr v-else v-for="(room,index) in posts">
                      <td>{{ (current-1)*row+(index+1) }}</td>
                      <td>{{ room.code }}</td>
                      <td>{{ room.name }}</td>
                      <td class=" mobile_hide">{{ room.user_name }}</td>
                      <td class=" mobile_hide">{{ room.address }}</td>

                      <td class=" mobile_hide">
                        <router-link :title="trans.get('keys.xem_danh_sach_nhan_vien')"
                                     :to="{
                                        name: 'ListUserBySaleroom',
                                        params: { saleroom_id: room.id },
                                        query: {branch_id: data.branch.id}
                                      }">
                          {{ room.usercount }} (<span class="text-underline">{{trans.get('keys.nhan_vien')}}</span>)
                        </router-link>
                      </td>
                      <td class="text-center">
                        <router-link :title="trans.get('keys.sua')"
                                     class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                     :to="{
                                        name: 'EditSaleroom',
                                        params: { saleroom_id: room.id },
                                        query: {branch_id: data.branch.id}
                                      }">
                          <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                        </router-link>
                        <a :title="trans.get('keys.xoa')"
                           class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"
                           href="javascript(0)"
                           @click.prevent="removeSaleRoom(room.id)"
                        ><span class="btn-icon-wrap"><i
                          class="fal fa-trash"></i></span></a>
                      </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ma')}}</th>
                    <th>{{trans.get('keys.ten_diem_ban')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                    </tfoot>
                  </table>
                  <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                  </div>
                </div>
                <div class="button-list">
                  <!--<a class="btn btn-primary btn-sm" :href="trans.get('keys.language')+'/system/organize/branch/add_saleroom/'+id">{{trans.get('keys.them_diem_ban')}}</a>-->
                  <a class="btn btn-secondary btn-sm" :href="trans.get('keys.language')+'/system/organize/branch'">{{trans.get('keys.quay_lai')}}</a>
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
        props: ['id'],
        //components: {vPagination},
        data() {
            return {
                data:{
                    branch:{
                        name:'',
                    }
                },
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
            }
        },
        methods: {
            getSaleRoomByBranch(paged) {
                axios.post('/system/organize/branch/list_sale_room_by_branch', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    id:this.id,
                    row:this.row
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
                this.getSaleRoomByBranch();
            },
            removeSaleRoom(sale_room_id){
                var branch_id = this.id;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_go_diem_ban_da_chon'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/branch/remove_sale_room',{
                        branch_id:branch_id,
                        sale_room_id:sale_room_id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
            getDataBranch(){
                axios.post('/system/organize/branch/detail_data/'+this.id)
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
        },
        mounted() {
            this.getDataBranch();
        }
    }
</script>

<style scoped>

</style>
