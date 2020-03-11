<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li v-if="organization_selected !== false" class="breadcrumb-item"><router-link :to="{ name: 'EditOrganization', params: {id: organization_id}}">{{ organization_name }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.nhan_vien') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="accordion" id="accordion_1">

            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true">
                  <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}
                </a>
              </div>
              <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                <div class="card-body">
                  <div class="row">

                    <div v-if="organization_selected === false" class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <div class="wrap_search_box">
                            <div class="btn_search_box" @click="selectOrganization()">
                              <span class="selected_organization_content">{{trans.get('keys.to_chuc')}}</span>
                            </div>
                            <div class="content_search_box">
                              <input @input="selectOrganization()" type="text" v-model="organization_keyword" class="form-control search_box">
                              <i class="fa fa-spinner" aria-hidden="true"></i>
                              <ul>
                                <li @click="selectOrganizationItem(item.id)" v-for="item in organization_list" :data-value="item.id">{{item.name}}</li>
                              </ul>
                            </div>
                          </div>
                          <label v-if="!employee.input_organization_id" class="text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <div class="wrap_search_box">
                            <div class="btn_search_box" @click="selectUser()">
                              <span class="selected_user_content">{{trans.get('keys.nguoi_dung')}}</span>
                            </div>
                            <div class="content_search_box">
                              <input @input="selectUser()" type="text" v-model="user_keyword" class="form-control search_box">
                              <i class="fa fa-spinner" aria-hidden="true"></i>
                              <ul>
                                <li @click="selectUserItem(item.user_id)" v-for="item in user_list" :data-value="item.user_id">{{item.fullname}}</li>
                              </ul>
                            </div>
                          </div>
                          <label v-if="!employee.input_user_id" class="text-danger user_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <select class="form-control" v-model="employee.position">
                            <option value="">{{trans.get('keys.vi_tri') + ' *'}}</option>
<!--                            <option value="manager">Manager</option>-->
<!--                            <option value="leader">Leader</option>-->
<!--                            <option value="employee">Employee</option>-->
                            <option v-for="item in position_list" :value="item.name">{{ item.name.charAt(0).toUpperCase() + item.name.slice(1) }}</option>
                          </select>
                        </div>
                        <label v-if="!employee.position" class="text-danger position_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>

                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary btn-sm" @click="createEmployee()">{{trans.get('keys.them')}}</button>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <assign-employee v-if="organization_id !== false" :key="assignBatch" :organization_id="organization_id"></assign-employee>

            <div class="card">
              <div class="card-body">
                <div class="listData">
                  <h5 v-if="organization_selected === false" class="mb-20">{{trans.get('keys.danh_sach_nhan_vien')}}</h5>
                  <h5 v-else class="mb-20">{{trans.get('keys.danh_sach_nhan_vien')}} - {{ organization_name }}</h5>
                  <div class="row">
                    <div class="col-sm-8 dataTables_wrapper">

                      <div class="dataTables_length" style="display: inline-block;">
                        <label>{{trans.get('keys.hien_thi')}}
                          <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getDataList(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </label>
                      </div>

                      <div class="fillterConfirm" style="display: inline-block;">
                        <label>
                          <select v-model="position" class="custom-select custom-select-sm form-control form-control-sm" @change="getDataList(1)">
                            <option value="0">{{ trans.get('keys.chon_vi_tri') }}</option>
                            <option value="manager">Manager</option>
                            <option value="leader">Leader</option>
                            <option value="employee">Employee</option>
                          </select>
                        </label>
                      </div>

                    </div>

                    <div class="col-sm-4">
                      <form v-on:submit.prevent="getDataList(1)">
                        <div class="d-flex flex-row form-group">
                          <input  v-model="keyword" type="text" class="form-control search_text" :placeholder="trans.get('keys.nhap_ten_nhan_vien')">
                          <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter" @click="getDataList(1)">
                            {{trans.get('keys.tim')}}
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                  <div class="mt-10 mb-20">
                    <strong>
                      {{trans.get('keys.tong_so_nhan_vien_hien_tai')}} :{{totalRow}}
                    </strong>
                  </div>
                  <div class="table-responsive">
                    <table class="table_res">
                      <thead>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.nguoi_dung')}}</th>
                      <th>{{trans.get('keys.to_chuc')}}</th>
                      <th>{{trans.get('keys.vi_tri')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                      </thead>
                      <tbody>
                      <tr v-if="posts.length == 0">
                        <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                      </tr>
                      <tr v-else v-for="(item,index) in posts">
                        <td>{{ (current-1)*row+(index+1) }}</td>
                        <td>{{ item.user ? item.user.fullname : '' }}</td>
                        <td>{{ item.organization ? item.organization.name : '' }}</td>
                        <td v-if="item.position === 'manager'">
                          <label class="badge badge-dark">{{ item.position }}</label>
                        </td>
                        <td v-else-if="item.position === 'leader'">
                          <label class="badge badge-warning">{{ item.position }}</label>
                        </td>
                        <td v-else>
                          <label class="badge badge-info">{{ item.position }}</label>
                        </td>
                        <td>
                          <router-link :title="trans.get('keys.sua_nhan_vien')"
                                       class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                       :to="{ name: 'EditEmployee', params: { id: item.id }, query: { source_page: current, organization_id: organization_id}}">
                            <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                          </router-link>

                          <a href="javascript(0)"
                             @click.prevent="deletePost('/organization-employee/delete/'+item.id)"
                             :title="trans.get('keys.xoa_nhan_vien')"
                             class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user">
                            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                          </a>
                        </td>
                      </tr>
                      </tbody>
                      <tfoot>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.nguoi_dung')}}</th>
                      <th>{{trans.get('keys.to_chuc')}}</th>
                      <th>{{trans.get('keys.vi_tri')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                      </tfoot>
                    </table>
                    <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                      <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                    </div>
                    <router-link :to="{ name: 'IndexOrganization', params: {page: source_page}}" class="btn btn-secondary btn-sm"  v-if="organization_id !== false">
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
  </div>
</template>

<script>
  //import VSwitch from 'v-switch-case'
  //Vue.use(VSwitch)
  //import vPagination from 'vue-plain-pagination'
  import AssignEmployee from './AssignEmployeeComponent'

  export default {
    components: {
      //vPagination,
      AssignEmployee
    },
    props: ['organization_id', 'source_page'],
    data() {
      return {
        employee: {
          input_organization_id: 0,
          input_user_id: 0,
          position: '',
        },
        data:[],
        posts: {},
        keyword: '',
        current: 1,
        totalPages: 0,
        totalRow: 0,
        row: 10,
        user_list:[],
        user_keyword: '',
        organization_list:[],
        organization_keyword: '',
        position: 0,
        organization_selected: false,
        organization_name: '',
        position_list: [],
        assignBatch: 0
      }
    },
    methods: {
      selectOrganizationItem(input_id){
        this.employee.input_organization_id = input_id;
      },
      selectUserItem(input_id) {
        this.employee.input_user_id = input_id;
        this.fetchRole();
      },
      selectOrganization(){
        $('.content_search_box').addClass('loadding');
        axios.post('/organization/list', {
          keyword: this.organization_keyword,
          paginated: 0 //không phân trang
        })
          .then(response => {
            this.organization_list = response.data;
            $('.content_search_box').removeClass('loadding');
          })
          .catch(error => {
            $('.content_search_box').removeClass('loadding');
          })
      },
      selectUser(){
        $('.content_search_box').addClass('loadding');
        axios.post('/organization-employee/list-user', {
          keyword: this.user_keyword,
          organization_id: this.organization_id, //không phân trang
        })
          .then(response => {
            this.user_list = response.data;
            $('.content_search_box').removeClass('loadding');
          })
          .catch(error => {
            $('.content_search_box').removeClass('loadding');
          })
      },
      getDataList(paged) {
        axios.post('/organization-employee/list', {
          page: paged || this.current,
          keyword: this.keyword,
          row: this.row,
          organization_id: this.organization_id,
          position: this.position
        })
          .then(response => {
            this.posts = response.data.data ? response.data.data.data : [];
            this.current = response.data.pagination ? response.data.pagination.current_page : 1;
            this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
            this.totalRow = response.data ? response.data.total : 0;
          })
          .catch(error => {
            console.log(error.response.data);
          });
      },
      createEmployee(){
        if(!this.employee.input_user_id){
          $('.user_required').show();
          return;
        }

        if((!this.organization_id || this.organization_id === 0) && !this.employee.organization_id){
          $('.organization_required').show();
          return;
        }

        if(!this.employee.position){
          $('.position_required').show();
          return;
        }

        axios.post('/organization-employee/create',{
          organization_id: this.employee.input_organization_id,
          user_id: this.employee.input_user_id,
          position: this.employee.position
        })
          .then(response => {
            if(response.data.key) {
              toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
              $('.form-control').removeClass('error');
              $('#employee_'+response.data.key).addClass('error');
            }else{
              if(response.data.status === 'success'){
                toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
                //reset form
                if (this.organization_selected === false) {
                  this.employee.input_organization_id = 0;
                }
                this.employee.input_user_id = 0;
                this.employee.position = '';
                this.getDataList(this.current);
                $(".selected_organization_content").text(this.trans.get('keys.to_chuc'));
                $(".selected_user_content").text(this.trans.get('keys.nhan_vien'));

                $('.form-control').removeClass('error');
              }
            }
          })
          .catch(error => {
            toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
          })
      },
      fetchRole() {
        axios.post('/organization-employee/get-user-detail/'+ this.employee.input_user_id)
          .then(response => {
            this.position_list = response.data;
            console.log(this.position_list);
          })
          .catch(error => {
            console.log(error.response.data);
          })
      },
      onPageChange() {
        let page = this.getParamsPage();
        this.getDataList(page);
      },
      getParamsPage() {
        return this.$route.params.page;
      },
      setParamsPage(value) {
        this.$route.params.page = value;
      },
      deletePost(url) {
        let current_pos = this;
        swal({
          title: this.trans.get('keys.thong_bao'),
          text: this.trans.get('keys.ban_co_muon_xoa_nhan_vien_nay'),
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: true,
          showLoaderOnConfirm: true
        }, function () {
          axios.post(url)
            .then(response => {
              toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
              current_pos.getDataList(current_pos.current);
              //reload assign batch
              this.assignBatch += 1;
            })
            .catch(error => {
              toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
            });
        });

        return false;
      },
      setOrganization() {
        if (this.organization_id && this.organization_id !== 0) {
          this.employee.input_organization_id = this.organization_id;
          this.organization_selected =  true;
          this.fetchOrganizationInfo(this.organization_id);
        }
      },
      fetchOrganizationInfo(organization_id) {
        axios.post('/organization/detail/' + organization_id)
          .then(response => {
            this.organization_name = response.data.name;
          })
          .catch(error => {
            console.log(error.response.data);
          })
      }
    },
    updated() {
      this.setParamsPage(false);
    },
    mounted() {
      //gán organization_id nếu có trong prop
      this.setOrganization();
    }
  }
</script>

<style scoped>

</style>
