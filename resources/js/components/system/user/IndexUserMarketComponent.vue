<template>
  <div class="container-fluid mt-15">
    <div class="row">
    <div class="col">
      <nav class="breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
          <li class="breadcrumb-item">
            <router-link to="/tms/dashboard">
            {{ trans.get('keys.dashboard') }}
            </router-link>
          </li>
          <li class="breadcrumb-item active">{{ trans.get('keys.quan_ly_nhan_vien') }}</li>
        </ol>
      </nav>
    </div>
  </div>
    <div>
        <div class="role_organize">
            <div class="role_organize_content">
                <h6 class="mb-20">{{trans.get('keys.danh_sach_nhan_vien')}}</h6>
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="accordion" id="accordion_1">
                            <div class="card" style="    border: 1px solid rgba(0,0,0,.125);">
                                <div class="card-header d-flex justify-content-between">
                                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                                </div>
                                <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                    <div class="card-body">
                                        <system-user-create :type="'student'" :role_login="'user_market'"></system-user-create>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="fillterConfirm" style="display: inline-block;">
                            <label>
                                <select v-model="branch" @change="listDataSearchBox()"
                                        class="custom-select custom-select-sm form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="0">{{trans.get('keys.chon_dai_ly')}}</option>
                                    <option v-for="itembranch in listbranch" :value="itembranch.id">{{itembranch.name}}</option>
                                </select>
                            </label>
                        </div>
                        <div class="fillterConfirm" style="display: inline-block;" v-if="branch != 0">
                            <div class="wrap_search_box">
                                <div class="btn_search_box" style="height:33px;width:150px;">
                                    <span class="branch_search_span">{{trans.get('keys.chon_diem_ban')}}</span>
                                </div>
                                <div class="content_search_box">
                                    <input @input="listDataSearchBox()" type="text" v-model="saleroom_search" class="form-control search_box">
                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                    <ul>
                                        <li @click="changeSaleRoom(0)">{{trans.get('keys.chon_diem_ban')}}</li>
                                        <li @click="changeSaleRoom(item.id)" v-for="item in srByBranch">{{item.name}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="fillterConfirm" style="display: inline-block;">
                            <button type="button" class="btn btn-primary btn-sm"
                                    @click="listData(1)">
                                {{trans.get('keys.loc')}}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8 dataTables_wrapper">
                        <div class="dataTables_length" style="display: inline-block;">
                            <label>{{trans.get('keys.hien_thi')}}
                                <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="listData(1)">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                        <div class="" style="display: inline-block;">
                            <select v-model="style" style="margin-bottom: 0 !important;" class="custom-select custom-select-sm form-control form-control-sm" @change="listData(1)">
                                <option value="0">{{trans.get('keys.danh_sach_nhan_vien')}}</option>
                                <option value="1">{{trans.get('keys.danh_sach_quan_ly')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <form v-on:submit.prevent="listData(1)">
                                <div class="d-flex flex-row">
                                    <input  v-model="keyword" type="text"
                                            class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa')+ ' ...'">
                                    <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter"
                                            @click="listData(1)">
                                        {{trans.get('keys.tim')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="mt-10 mb-20">
                    <strong v-if="style == 0">
                        {{trans.get('keys.so_nhan_vien_hien_tai')}} : {{ total_user }}
                    </strong>
                    <strong v-else>
                        {{trans.get('keys.so_quan_ly_hien_tai')}} : {{ total_user }}
                    </strong>
                </div>
                <div class="table-responsive table_res">
                    <table class="table_res">
                        <thead>
                        <th>{{trans.get('keys.stt')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.cmtnd')}}</th>
                        <th>{{trans.get('keys.tai_khoan')}}</th>
                        <th>{{trans.get('keys.ho_ten')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.noi_lam_viec')}}</th>
                        <th>{{trans.get('keys.hanh_dong')}}</th>
                        </thead>
                        <tbody>
                        <tr v-if="posts.length == 0">
                            <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                        </tr>
                        <tr v-else v-for="(post,index) in posts">
                            <td>{{ (current-1)*row+(index+1) }}</td>
                            <td class=" mobile_hide">{{ post.cmtnd }}</td>
                            <td>{{ post.username }}</td>
                            <td>{{ post.fullname }}</td>
                            <td class=" mobile_hide">{{ post.email }}</td>
                            <td class=" mobile_hide">
                                <span v-if="style == 0">
                                    <span v-if="post.sale_room_name">
                                        ( {{trans.get('keys.diem_ban')}} ) - {{ post.sale_room_name }}
                                    </span>
                                    <span v-if="post.branch_name">
                                        ( {{trans.get('keys.dai_ly')}} ) - {{ post.branch_name }}
                                    </span>
                                </span>
                                <span v-else>
                                    <span v-if="post.branch_name && post.branch_id">
                                        ( {{trans.get('keys.dai_ly')}} ) - {{ post.branch_name }}
                                    </span>
                                    <span v-else-if="post.sale_room_name && post.sale_room_id">
                                        ( {{trans.get('keys.diem_ban')}} ) - {{ post.sale_room_name }}
                                    </span>
                                </span>
                            </td>
                            <td>
                                <router-link
                                  :title="trans.get('keys.xem')"
                                  class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                  :to="{name: 'EditUserById', params: {user_id: post.user_id}, query: {type: 'view_user_market'}}">
                                    <span class="btn-icon-wrap"><i class="fal fa-street-view"></i></span></router-link>

                                <router-link :title="trans.get('keys.sua')"
                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                             :to="{name: 'EditUserById', params: {user_id: post.user_id}, query: {type: 'view_user_market'}}">
                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                </router-link>
                                <a v-if="style == 0" :title="trans.get('keys.go_nguoi_dung_khoi_danh_sach')"
                                   class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"
                                   href="javascript(0)"
                                   @click.prevent="removeUser(post.id)"
                                ><span class="btn-icon-wrap"><i
                                        class="fal fa-remove"></i></span></a>
                                <!--<button @click.prevent="deletePost('/system/user/delete/'+user.user_id)" class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"><span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>-->
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <th>{{trans.get('keys.stt')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.cmtnd')}}</th>
                        <th>{{trans.get('keys.tai_khoan')}}</th>
                        <th>{{trans.get('keys.ho_ten')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.noi_lam_viec')}}</th>
                        <th>{{trans.get('keys.hanh_dong')}}</th>
                        </tfoot>
                    </table>
                    <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import SystemUserCreate from './CreateComponent'

    export default {
        //components: {vPagination},
        components: {SystemUserCreate},
        data(){
            return{
                posts: {},
                current: 1,
                totalPages: 0,
                total_user:0,
                row:10,
                keyword:'',
                branch:0,
                listbranch: {},
                saleroom:0,
                listsaleroom: {},
                srByBranch:{},
                saleroom_search:'',
                style:0
            }
        },
        methods:{
            changeSaleRoom(sr_id){
                this.saleroom = sr_id;
            },
            listDataSearchBox(){
                this.saleroom = 0;
                $('.content_search_box').addClass('loadding');
                axios.post('/system/user_market/saleroom_search_box',{
                    page: 1,
                    keyword:this.saleroom_search,
                    branch:this.branch,
                })
                    .then(response => {
                        this.srByBranch = response.data.data.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        console.log(error.response.data);
                    })
            },
            listBranch(){
                axios.post('/system/user_market/list_branch')
                    .then(response => {
                        this.listbranch  = response.data;
                        this.$nextTick(function(){
                            $('.selectpicker').selectpicker('refresh');
                        });
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            deletePost(url) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            swal({
                                title: current_pos.trans.get('keys.thong_bao'),
                                text: current_pos.trans.get('keys.xoa_thanh_cong'),
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function () {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            swal(current_pos.trans.get('keys.thong_bao'), current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), "error")
                            console.log(error);
                        });
                });

                return false;
            },
            removeUser(id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_go_user_da_chon'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/branch/remove_user', {
                        id: id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
            listData(paged){
                axios.post('/system/user_market/list_user_by_role', {
                    page: paged || this.current,
                    branch_id: this.branch,
                    saleroom_id: this.saleroom,
                    row: this.row,
                    keyword: this.keyword,
                    style:this.style
                })
                    .then(response => {
                        this.posts              = response.data.data.data;
                        this.current            = response.data.pagination.current_page;
                        this.totalPages         = response.data.pagination.total;
                        this.total_user         = response.data.pagination.total_user;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.listData();
            },
        },
        mounted() {
            this.listBranch();
            //this.getSaleRoom();
        }
    }
</script>

<style scoped>
</style>
