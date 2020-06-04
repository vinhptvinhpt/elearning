<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.chu_dai_ly') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.chu_dai_ly')}}</h5>
                    <div class="mb-20 mt-20">
                        <div class="accordion" id="accordion_1">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <a class="collapsed" role="button" id="formCreateNew" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.phan_quyen_chu_dai_ly')}}</a>
                                </div>
                                <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                    <div class="card-body">
                                        <assign-branch-master :key="masterAssign"></assign-branch-master>
                                    </div>
                                </div>
                            </div>
                            <div class="card"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-sm-8 dataTables_wrapper">
                                        <div class="dataTables_length" style="display: inline-block;">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getUser(1)">
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
                                                       class="form-control search_text" :placeholder="trans.get('keys.nhap_ten_tai_khoan_email_cmtnd') + ' ...'">
                                                <button type="button" id="btnFilter" class="btn btn-primary btn-sm btn_fillter"
                                                        @click="getUser(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-10 mb-20">
                                    <strong>
                                        {{trans.get('keys.tong_so_chu_dai_ly_hien_tai')}} : {{ total_user }}
                                    </strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table_res">
                                        <thead>
                                        <tr>
                                            <th class="text-center">{{trans.get('keys.stt')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                                            <th>{{trans.get('keys.tai_khoan')}}</th>
                                            <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                            <th class="text-center mobile_hide">{{trans.get('keys.so_dai_ly')}}</th>
                                            <th>{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="posts.length === 0">
                                            <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                        </tr>
                                        <tr v-else v-for="(user,index) in posts">
                                            <td class="text-center">{{ (current-1)*row+(index+1) }}</td>
                                            <td class=" mobile_hide">{{ user.cmtnd }}</td>
                                            <td>
                                              <router-link :to="{ name: 'EditUserById', params: { user_id: user.user_id } }">
                                                {{ user.username }}
                                              </router-link>
                                            </td>

                                            <td>{{ user.fullname }}</td>
                                            <td class=" mobile_hide">{{ user.email }}</td>
                                            <td class="text-center mobile_hide">
                                              <router-link :title="trans.get('keys.xem_dai_ly')" :to="{ name: 'BranchIndexByRole', query: { master_id: user.user_id } }">
                                                {{ user.agents_length }}
                                              </router-link>
                                            </td>
                                            <td>

                                                <router-link :title="trans.get('keys.xem_dai_ly')"
                                                             class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                             :to="{ name: 'BranchIndexByRole', query: { master_id: user.user_id } }">
                                                  <span class="btn-icon-wrap"><i class="fal fa-search"></i></span>
                                                </router-link>


                                                <a href="javascript(0)" @click.prevent="removeMaster('/system/branch_master/remove/'+user.user_id)" :title="trans.get('keys.huy_quyen_chu_dai_ly')"
                                                   class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user">
                                                    <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                                </a>

                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th class="text-center">{{trans.get('keys.stt')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.so_cmtnd')}}</th>
                                            <th>{{trans.get('keys.tai_khoan')}}</th>
                                            <th>{{trans.get('keys.ten_nguoi_dung')}}</th>
                                            <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
                                            <th class="text-center mobile_hide">{{trans.get('keys.so_dai_ly')}}</th>
                                            <th>{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import AssignBranchMaster from '../user/AssignBranchMaster'

    export default {
        //components: {vPagination},
        components: {AssignBranchMaster},
        data() {
            return {
                masterAssign: 0,
                checkMaster: 0,
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                total_user:0,
                row:10,
                user_select:[],
                allSelected:false,
                importType:0,
                data_import: {},
            }
        },
        methods: {
            getUser(paged) {
                axios.post('/system/branch_master/list_user', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row:this.row,
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                        this.total_user = response.data.pagination ? response.data.pagination.total_user : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getUser();
            },
            clearResults() {
                this.masterAssign = 0;
            },
            removeMaster(url) {
                this.clearResults();
                var vm = this;
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.huy_quyen_chu_dai_ly'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                            vm.masterAssign += 1;
                        })
                        .catch(error => {
                            roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
                this.masterAssign = vm.masterAssign;
                return false;
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
