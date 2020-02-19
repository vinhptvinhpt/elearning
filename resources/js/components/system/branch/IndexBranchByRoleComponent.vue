<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/system/branch_master">{{ trans.get('keys.chu_dai_ly') }}</router-link></li>
            <li class="breadcrumb-item">{{ fullname }}</li>
            <li class="breadcrumb-item active">{{ trans.get('keys.dai_ly') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="accordion" id="accordion_1">
            <div class="card">
              <div class="card-body">
                <div class="listData">
                  <h5 class="mb-20">{{trans.get('keys.danh_sach_dai_ly')}}</h5>
                  <div class="row">
                    <div class="col-sm-8 dataTables_wrapper">
                      <div class="dataTables_length" style="display: inline-block;">
                        <label>{{trans.get('keys.hien_thi')}}
                          <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getBranchData(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <form v-on:submit.prevent="getBranchData(1)">
                          <div class="d-flex flex-row">
                            <input  v-model="keyword" type="text"
                                    class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa') + ' ...'">
                            <button type="button" class="btn btn-primary btn-sm btn_fillter"
                                    @click="getBranchData(1)">
                              {{trans.get('keys.tim')}}
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table_res">
                      <thead>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ma')}}</th>
                      <th>{{trans.get('keys.ten_dai_ly')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.tinh')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.diem_ban')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                      </thead>
                      <tbody>
                      <tr v-if="posts.length == 0">
                        <td colspan="8">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                      </tr>
                      <tr v-else v-for="(branch,index) in posts">
                        <td>{{ (current-1)*row+(index+1) }}</td>
                        <td>{{ branch.code }}</td>
                        <td>{{ branch.name }}</td>
                        <td class=" mobile_hide">{{ branch.cityname }}</td>
                        <td class=" mobile_hide">
                          <router-link
                            :to="{ name: 'EditUserById', params: {user_id: branch.user_id} }"
                            :title="trans.get('keys.xem_thong_tin_quan_ly')">
                            <span class="text-underline">{{ branch.user_name }}</span>
                          </router-link>
                        </td>
                        <td class=" mobile_hide">{{ branch.address }}</td>
                        <td class=" mobile_hide">
                          <router-link :to="{ name: 'SaleroomIndexByRole', query: {branch_id: branch.id} }"
                                       :title="trans.get('keys.xem_danh_sach_diem_ban')">
                            {{ branch.saleroom_count }} (<span class="text-underline">{{trans.get('keys.diem_ban')}}</span>)
                          </router-link>
                        </td>
                        <td class=" mobile_hide">
                          <router-link :to="{ name: 'BranchUserIndexByRole', query: {branch_id: branch.id} }"
                                       :title="trans.get('keys.xem_danh_sach_nhan_vien')">
                            {{ branch.user_count }} (<span class="text-underline">{{trans.get('keys.nhan_vien')}}</span>)
                          </router-link>
                        </td>
                        <td>
                          <router-link :title="trans.get('keys.sua')"
                                       class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                       :to="{ name: 'BranchEditByRole', params: { branch_id: branch.id }}">
                            <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                          </router-link>
                        </td>
                      </tr>
                      </tbody>
                      <tfoot>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ma')}}</th>
                      <th>{{trans.get('keys.ten_dai_ly')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.tinh')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.diem_ban')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
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

</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    export default {
        props: ['master_id'],
        //components: {vPagination},
        data() {
            return {
                branch: {
                    name: '',
                    code: '',
                    user_id: 0,
                    //description:'',
                    address:'',
                    city: ''
                },
                data:[],
                posts: {},
                keyword: this.code,
                current: 1,
                totalPages: 0,
                row:10,
                user_id: 0,
                fullname: ''
            }
        },
        methods: {
            inputClearMessage(messClass){
                $('.'+messClass).hide();
            },
            getCityAllBranch(){
                axios.post('/system/organize/branch/get_city_all_branch')
                    .then(response => {
                        this.cityBranchs = response.data;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getBranchData(paged) {
                axios.post('/system/organize/branch/list_data', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    user_id: this.user_id
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
                this.fetch();
            },
            fetch() {
              axios.post('/bridge/fetch', {
                master_id: this.master_id,
                view: 'BranchIndexByRole'
              })
                .then(response => {
                  this.user_id = response.data.user_id;
                  this.fullname = response.data.fullname;
                  this.getBranchData();
                })
                .catch(error => {
                  console.log(error);
                })
            },
        },
        mounted() {
          //this.fetch();
        }
    }
</script>

<style scoped>

</style>
