<template>
  <div class="container-fluid mt-15">
    <div class="row">
    <div class="col">
      <nav class="breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
          <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
          <li v-if="root === 'branch'" class="breadcrumb-item">
            <router-link :to="{ name: 'SaleroomIndexByBranch', params: { branch_id: branch_id } }">
              {{ trans.get('keys.danh_sach_diem_ban') }}
            </router-link>
          </li>
          <li v-else class="breadcrumb-item">
            <router-link to="/tms/system/organize/saleroom">
              {{ trans.get('keys.danh_sach_diem_ban') }}
            </router-link>
          </li>
          <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_nhan_vien') }}</li>
        </ol>
      </nav>
    </div>
  </div>
    <div>
        <div class="card">
            <div class="card-body">
                <div class="edit_city_form form-material">
                    <h5 class="mb-20">{{trans.get('keys.danh_sach_nhan_vien_cua_diem_ban')}} :
                        {{data.saleroom.name}}</h5>

                    <div class="mb-20 mt-20">
                        <!--<button class="btn btn-success btn-sm show_form formCreateNew" @click="showForm('formCreateNew')">
                            <span><i class="fa fa-plus-circle"></i> Tạo mới nhân viên</span>
                            <i class="fa fa-times hide"></i>
                        </button>
                        <a v-if="root === 'branch'" class="btn btn-primary btn-sm" :href="'/system/organize/branch/'+data.saleroom.branch_id+'/saleroom/'+id+'/add_user'">Thêm nhân
                            viên có sẵn</a>
                        <a v-else class="btn btn-primary btn-sm" :href="'/system/organize/saleroom/add_user/'+id">Thêm nhân
                            viên có sẵn</a>
                        <div class="hide border_box mt-20 bradius-4 formHide" id="formCreateNew" style="max-width:100%;">
                            <h6 class="mb-20">Thêm nhân viên mới cho đại lý</h6>
                            <create_user_by_saleroom :sale_room_id="id"></create_user_by_saleroom>
                        </div>-->

                        <div class="accordion" id="accordion_1">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <a class="collapsed" id="formCreateNew" role="button" data-toggle="collapse"
                                       href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                                </div>
                                <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                    <div class="card-body">
                                        <h6 class="mb-20">{{trans.get('keys.them_nhan_vien_moi_cho_diem_ban')}}</h6>
                                        <saleroom-user-create :sale_room_id="id"></saleroom-user-create>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
<!--                                <div class="card-header d-flex justify-content-between">-->
<!--                                    <a v-if="root === 'branch'" class="collapsed" role="button"-->
<!--                                       :href="trans.get('keys.language')+'/system/organize/branch/'+data.saleroom.branch_id+'/saleroom/'+id+'/add_user'"><i-->
<!--                                            class="fal fa-upload mr-3"></i>{{trans.get('keys.them_nhan_vien_co_san')}}</a>-->
<!--                                    <a v-else class="collapsed" role="button"-->
<!--                                       :href="trans.get('keys.language')+'/system/organize/saleroom/add_user/'+id"><i-->
<!--                                            class="fal fa-upload mr-3"></i>{{trans.get('keys.them_nhan_vien_co_san')}}</a>-->
<!--                                </div>-->
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
                                <select class="form-control search_text" v-model="working_status" @change="getListUsers(1)">
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
                                    <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter"
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
                                    <th>{{trans.get('keys.ten_nhan_vien')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                    <th>{{trans.get('keys.hanh_dong')}}</th>
                                    </thead>
                                    <tbody>
                                    <tr v-if="posts.length == 0">
                                        <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                    </tr>
                                    <tr v-else v-for="(user,index) in posts">
                                        <td>{{ (current-1)*row+(index+1) }}</td>
                                        <td class=" mobile_hide">{{ user.cmtnd }}</td>
                                        <td>{{ user.username }}</td>
                                        <td>{{ user.fullname }}</td>
                                        <td class=" mobile_hide">{{ user.email }}</td>
                                        <td>
                                            <!--                                                <a :href="'/system/user/edit/'+user.user_id"-->
                                            <!--                                                   class="btn waves-effect waves-light btn-sm btn-primary">Xem</a>-->
                                            <!--                                                <a href="javascript(0)" @click.prevent="removeUser(user.user_id)"-->
                                            <!--                                                   class="btn waves-effect waves-light btn-sm btn-danger">Gỡ</a>-->


                                            <a :title="trans.get('keys.xem')"
                                               class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                               :href="trans.get('keys.language')+'/system/user/edit/'+user.user_id"
                                            ><span class="btn-icon-wrap"><i
                                                    class="fal fa-street-view"></i></span></a>

                                            <a :title="trans.get('keys.go')"
                                               class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"
                                               href="javascript(0)"
                                               @click.prevent="removeUser(user.user_id)"
                                            ><span class="btn-icon-wrap"><i
                                                    class="fal fa-remove"></i></span></a>

                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                                    <th>{{trans.get('keys.tai_khoan')}}</th>
                                    <th>{{trans.get('keys.ten_nhan_vien')}}</th>
                                    <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                    <th>{{trans.get('keys.hanh_dong')}}</th>
                                    </tfoot>
                                </table>
                                <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                                </div>
                            </div>
                            <div class="button-list">
                              <router-link v-if="is_user_market || root === 'branch'"
                                           :to="{
                                             name: 'SaleroomIndexByBranch',
                                             params: { branch_id: data.saleroom.branch_id },
                                           }"
                                           class="btn btn-secondary btn-sm">
                                {{trans.get('keys.quay_lai')}}
                              </router-link>
                              <router-link v-else
                                           to="/tms/system/organize/saleroom"
                                           class="btn btn-secondary btn-sm">
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
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import SaleroomUserCreate from '../user/CreateUserBySaleRoomComponent'

    export default {
        props: ['id', 'root', 'branch_id'],
        //components: {vPagination},
        components: {SaleroomUserCreate},
        data() {
            return {
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
                is_user_market: ''
            }
        },
        methods: {
            showForm(id_form) {
                var parent = $('#' + id_form).parent();
                if ($('.' + id_form, parent).hasClass('open')) {
                    $('.show_form', parent).removeClass('open');
                    $('.formHide').hide();
                } else {
                    $('.show_form', parent).removeClass('open');
                    $('.formHide').hide();
                    $('#' + id_form).show();
                    $('.' + id_form, parent).addClass('open');
                }
            },
            getListUsers(paged) {
                axios.post('/system/organize/saleroom/list_user_by_sale_room', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    id: this.id,
                    working_status: this.working_status
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
                this.getListUsers();
            },
            removeUser(user_id) {
                var sale_room_id = this.id;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_go_nhan_vien_da_chon'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/saleroom/remove_user', {
                        sale_room_id: sale_room_id,
                        user_id: user_id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
            getDataSaleRoom() {
                axios.post('/system/organize/saleroom/detail_data/' + this.id)
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            fetch() {
              axios.post('/bridge/fetch', {
                view: 'ListUserBySaleroom'
              })
                .then(response => {
                  this.is_user_market = response.data.is_user_market;
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
            this.getDataSaleRoom();
            this.fetch();
        }
    }
</script>

<style scoped>

</style>
