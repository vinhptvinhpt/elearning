<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/system/organize/branch">{{ trans.get('keys.danh_sach_dai_ly') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link :to="{ name: 'ListUserByBranch', params: { branch_id: id } }">
                {{ trans.get('keys.danh_sach_nhan_vien') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.them_nhan_vien') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="card">
        <div class="card-body">
          <h5 class="mb-20 ">{{trans.get('keys.them_nhan_vien_cho_dai_ly')}} : {{data.branch.name}}</h5>
          <div class="row">
            <div class="col-sm-8 dataTables_wrapper">
              <div class="dataTables_length" style="display: inline-block;">
                <label>{{trans.get('keys.hien_thi')}}
                  <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getListAddUsers(1)">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </label>
              </div>
            </div>
            <div class="col-sm-4">
              <form v-on:submit.prevent="getListAddUsers(1)">
                <div class="d-flex flex-row form-group">
                  <input  v-model="keyword" type="text"
                          class="form-control search_text" :placeholder="trans.get('keys.nhap_tu_khoa')+' ...'">
                  <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter"
                          @click="getListAddUsers(1)">
                    {{trans.get('keys.tim')}}
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table_res">
              <thead>
              <th>
                <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                <label for="branch-select-all"></label>
              </th>
              <th>{{trans.get('keys.ten_nhan_vien')}}</th>
              <th>{{trans.get('keys.so_cmtnd')}}</th>
              <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
              <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
              </thead>
              <tbody>
              <tr v-if="posts.length == 0">
                <td colspan="5">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
              </tr>
              <tr v-else v-for="add_user in posts">
                <td>
                  <input v-model="user_add" :value="add_user.user_id" type="checkbox" :id="'add_user'+add_user.user_id" class="filled-in chk-col-light-blue check_box_branch"><label :for="'add_user'+add_user.user_id"></label>
                </td>
                <td>{{ add_user.fullname }}</td>
                <td>{{ add_user.cmtnd }}</td>
                <td class=" mobile_hide">{{ add_user.phone }}</td>
                <td class=" mobile_hide">{{ add_user.email }}</td>
              </tr>
              </tbody>
              <thead>
              <th>
                <input v-model="allSelected" @click="selectAllCheckbox()" id="branch-select-all2" type="checkbox" class="filled-in chk-col-light-blue" name="select_all" value=""/>
                <label for="branch-select-all2"></label>
              </th>
              <th>{{trans.get('keys.ten_nhan_vien')}}</th>
              <th>{{trans.get('keys.so_cmtnd')}}</th>
              <th class=" mobile_hide">{{trans.get('keys.dien_thoai')}}</th>
              <th class=" mobile_hide">{{trans.get('keys.email')}}</th>
              </thead>
            </table>
            <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
          </div>
          <div class="text-right button-list">
            <button @click="addUserBranch()" type="button" class="btn btn-primary btn-sm">{{trans.get('keys.them')}}</button>
            <router-link class="btn btn-secondary btn-sm" :to="{ name: 'ListUserByBranch', params: { branch_id: id } }">
              {{ trans.get('keys.quay_lai') }}
            </router-link>
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
                    },
                },
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                user_add:[],
                allSelected: false,
                row: 10,
            }
        },
        methods: {
            selectAllCheckbox(){
                this.user_add = [];
                if(!this.allSelected){
                    this.posts.forEach((select) => {
                        this.user_add.push(select.user_id);
                    });
                }
            },
            addUserBranch(){
                if(this.user_add.length  > 0){
                    axios.post('/system/organize/branch/add_user_by_branch',{
                        user_add:this.user_add,
                        branch_id:this.id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                            this.user_add = [];
                        })
                        .catch(error => {
                            roam_message('error','Lỗi hệ thống. Thao tác thất bại');
                        });
                }
            },
            getListAddUsers(paged) {
                axios.post('/system/organize/branch/list_add_user', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    branch_id:this.id
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
                this.getListAddUsers();
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
