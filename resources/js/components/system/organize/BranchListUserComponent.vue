<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link to="/tms/system/organize/branch">
                {{ trans.get('keys.danh_sach_dai_ly') }}
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
          <div class="edit_city_form form-material mb-20">
            <h5 class="mb-20">{{trans.get('keys.danh_sach_nhan_vien_cua_dai_ly')}} :
              {{data.branch.name}}</h5>

            <div class="accordion" id="accordion_1">
              <div class="card">
                <div class="card-header d-flex justify-content-between">
                  <a class="collapsed" id="formCreateNew" role="button" data-toggle="collapse"
                     href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
                </div>
                <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                  <div class="card-body">
                    <h6 class="mb-20">{{trans.get('keys.them_nhan_vien_moi_cho_dai_ly')}}</h6>
                    <saleroom-user-create :sale_room_id="id" :type="'agents'"></saleroom-user-create>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header d-flex justify-content-between">

                  <router-link
                    class="collapsed"
                    role="button"
                    :to="{ name: 'AddUserByBranch', params: { branch_id: id }}">
                    <i class="fal fa-upload mr-3"></i>{{trans.get('keys.them_nhan_vien_co_san')}}
                  </router-link>

                </div>
              </div>

            </div>
          </div>
          <div class="row">
            <div class="col-sm-8 dataTables_wrapper">
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
                  <th class=" mobile_hide">{{trans.get('keys.noi_lam_viec')}}</th>
                  <th>{{trans.get('keys.hanh_dong')}}</th>
                  </thead>
                  <tbody>
                  <tr v-if="posts.length == 0">
                    <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                  </tr>
                  <tr v-else v-for="(user,index) in posts">
                    <td>{{ index+1 }}</td>
                    <td class=" mobile_hide">{{ user.cmtnd }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.fullname }}</td>
                    <td class=" mobile_hide">{{ user.email }}</td>
                    <td class=" mobile_hide">{{ user.saleroom_name ? trans.get('keys.diem_ban')+': '+user.saleroom_name : trans.get('keys.thuoc_dai_ly') }}</td>
                    <td>
                      <!--                                                <a :href="'/system/user/edit/'+user.user_id"-->
                      <!--                                                   class="btn waves-effect waves-light btn-sm btn-primary">Xem</a>-->
                      <!--                                                <a href="javascript(0)" @click.prevent="removeUser(user.user_id)"-->
                      <!--                                                   class="btn waves-effect waves-light btn-sm btn-danger">Gỡ</a>-->

                      <router-link :title="trans.get('keys.xem')"
                                   :to="{name: 'EditUserById', params: { user_id: user.user_id }}"
                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                        <span class="btn-icon-wrap"><i
                          class="fal fa-street-view"></i></span>
                      </router-link>

                      <a :title="trans.get('keys.go_nguoi_dung_khoi_danh_sach')"
                         class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"
                         href="javascript(0)"
                         @click.prevent="removeUser(user.id)"
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
                  <th class=" mobile_hide">{{trans.get('keys.noi_lam_viec')}}</th>
                  <th>{{trans.get('keys.hanh_dong')}}</th>
                  </tfoot>
                </table>
                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
              </div>
              <div class="button-list">
                <router-link to="/tms/system/organize/branch" class="btn btn-secondary btn-sm">
                  {{trans.get('keys.quay_lai')}}
                </router-link>
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
        props: ['id'],
        //components: {vPagination},
        components: {SaleroomUserCreate},
        data() {
            return {
                data: {
                    branch: {
                        name: '',
                    },
                },
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 10,
            }
        },
        methods: {
            getListUsers(paged) {
                axios.post('/system/organize/branch/get_list_user_by_branch', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    id: this.id
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
            getDataBranch() {
                axios.post('/system/organize/branch/detail_data/' + this.id)
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
