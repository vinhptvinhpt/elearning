<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item"
                            v-if="selected_role === 'root' || selected_role === 'admin' || getSelectedRole === true">
                            <router-link :to="{name: 'IndexOrganization', params: {page: source_page}}">
                                {{ trans.get('keys.to_chuc') }}
                            </router-link>
                        </li>
                        <li v-else class="breadcrumb-item">
                            {{ trans.get('keys.to_chuc') }}
                        </li>
                        <li v-if="organization_selected !== false" class="breadcrumb-item">
                            <router-link :to="{ name: 'EditOrganization', params: {id: query_organization_id}}">{{ organization_name }}
                            </router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.nhan_vien') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div>
                    <div class="accordion" id="accordion_1">

                        <div v-if="slug_can('tms-system-employee-add')" class="card">
                            <div class="card-header d-flex justify-content-between">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1"
                                   aria-expanded="true">
                                    <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}
                                </a>
                            </div>
                            <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                                <div class="card-body">

                                    <system-user-create
                                            v-if="roles_ready === true"
                                            :organization_id="query_organization_id"
                                            :current_roles="current_roles"></system-user-create>

                                    <!-- Gán người dùng vào tổ chức one by one -->

                                    <!--                  <div class="row">-->
                                    <!--                    <div v-if="organization_selected === false" class="col-sm-6">-->
                                    <!--                      <div class="form-group">-->
                                    <!--                        <div class="input-group">-->
                                    <!--                          <div class="wrap_search_box">-->
                                    <!--                            <div class="btn_search_box" @click="selectOrganization()">-->
                                    <!--                              <span class="selected_organization_content">{{trans.get('keys.to_chuc')}}</span>-->
                                    <!--                            </div>-->
                                    <!--                            <div class="content_search_box">-->
                                    <!--                              <input @input="selectOrganization()" type="text" v-model="organization_keyword" class="form-control search_box">-->
                                    <!--                              <i class="fa fa-spinner" aria-hidden="true"></i>-->
                                    <!--                              <ul>-->
                                    <!--                                <li @click="selectOrganizationItem(item.id)" v-for="item in organization_list" :data-value="item.id">{{item.name}}</li>-->
                                    <!--                              </ul>-->
                                    <!--                            </div>-->
                                    <!--                          </div>-->
                                    <!--                          <label v-if="!employee.input_organization_id" class="text-danger organization_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                    <!--                        </div>-->
                                    <!--                      </div>-->
                                    <!--                    </div>-->
                                    <!--                    <div class="col-sm-6">-->
                                    <!--                      <div class="form-group">-->
                                    <!--                        <div class="input-group">-->
                                    <!--                          <div class="wrap_search_box">-->
                                    <!--                            <div class="btn_search_box" @click="selectUser()">-->
                                    <!--                              <span class="selected_user_content">{{trans.get('keys.nguoi_dung')}}</span>-->
                                    <!--                            </div>-->
                                    <!--                            <div class="content_search_box">-->
                                    <!--                              <input @input="selectUser()" type="text" v-model="user_keyword" class="form-control search_box">-->
                                    <!--                              <i class="fa fa-spinner" aria-hidden="true"></i>-->
                                    <!--                              <ul>-->
                                    <!--                                <li @click="selectUserItem(item.user_id)" v-for="item in user_list" :data-value="item.user_id">{{item.fullname}}</li>-->
                                    <!--                              </ul>-->
                                    <!--                            </div>-->
                                    <!--                          </div>-->
                                    <!--                          <label v-if="!employee.input_user_id" class="text-danger user_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                    <!--                        </div>-->
                                    <!--                      </div>-->
                                    <!--                    </div>-->
                                    <!--                    <div class="col-sm-6">-->
                                    <!--                      <div class="form-group">-->
                                    <!--                        <div class="input-group">-->
                                    <!--                          <select class="form-control" v-model="employee.position">-->
                                    <!--                            <option value="">{{trans.get('keys.vi_tri') + ' *'}}</option>-->
                                    <!--&lt;!&ndash;                            <option value="manager">Manager</option>&ndash;&gt;-->
                                    <!--&lt;!&ndash;                            <option value="leader">Leader</option>&ndash;&gt;-->
                                    <!--&lt;!&ndash;                            <option value="employee">Employee</option>&ndash;&gt;-->
                                    <!--                            <option v-for="item in position_list" :value="item.name">{{ item.name.charAt(0).toUpperCase() + item.name.slice(1) }}</option>-->
                                    <!--                          </select>-->
                                    <!--                        </div>-->
                                    <!--                        <label v-if="!employee.position" class="text-danger position_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>-->
                                    <!--                      </div>-->
                                    <!--                    </div>-->
                                    <!--                  </div>-->
                                    <!--                  <div class="row">-->
                                    <!--                    <div class="col-12">-->
                                    <!--                      <div class="form-group text-right">-->
                                    <!--                        <button type="submit" class="btn btn-primary btn-sm" @click="createEmployee()">{{trans.get('keys.them')}}</button>-->
                                    <!--                      </div>-->
                                    <!--                    </div>-->
                                    <!--                  </div>-->

                                </div>
                            </div>
                        </div>

                        <!-- Gán người dùng vào tổ chức batch -->
<!--                                      <assign-employee-->
<!--                                        v-if="selected_role === 'root' || selected_role === 'admin' || selected_role === 'manager'"-->
<!--                                        :key="assignBatch"-->
<!--                                        :organization_id="query_organization_id"-->
<!--                                      ></assign-employee>-->

                        <div class="card">
                            <div class="card-body">
                                <div class="listData">
                                    <h5 v-if="organization_selected === false" class="mb-20">
                                        {{trans.get('keys.danh_sach_nhan_vien')}}</h5>
                                    <h5 v-else class="mb-20">{{trans.get('keys.danh_sach_nhan_vien')}} - {{ organization_name }}</h5>
                                    <div class="row">
                                        <div class="col-sm-8 dataTables_wrapper">

                                            <div class="dataTables_length" style="display: inline-block;">
                                                <label>{{trans.get('keys.hien_thi')}}
                                                    <select v-model="row" style="height: 35px;"
                                                            class="custom-select custom-select-sm form-control form-control-sm"
                                                            @change="getDataList(1)">
                                                        <option value="10">10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                </label>
                                            </div>

                                            <div class="fillterConfirm" style="display: inline-block;">
                                                <label>
                                                    <select v-model="position" style="height: 35px;"
                                                            class="custom-select custom-select-sm form-control form-control-sm"
                                                            @change="getDataList(1)">
                                                        <option value="">{{ trans.get('keys.chon_vi_tri') }}</option>
                                                        <option v-for="position in filterPosition"
                                                                :value="position.key">
                                                            {{position.value}}
                                                        </option>
                                                    </select>
                                                </label>
                                            </div>
                                            <div class="col-6" style="width: auto; height: 35px; display: inline-block; position: absolute;">
                                                <label>
                                                    <treeselect v-model="organization_id1"
                                                                :multiple="false" :options="optionsOrganize"
                                                                @input="getDataList(1)"/>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <form v-on:submit.prevent="getDataList(1)">
                                                <div class="d-flex flex-row form-group">
                                                    <input v-model="keyword" type="text"
                                                           class="form-control search_text"
                                                           :placeholder="trans.get('keys.nhap_ten_nhan_vien')">
                                                    <button type="button" class="btn btn-primary btn-sm btn_fillter"
                                                            id="btnFilter" @click="getDataList(1)">
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
                                            <th class="text-center">{{trans.get('keys.team')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                            </thead>
                                            <tbody>
                                            <tr v-if="posts.length === 0">
                                                <td colspan="6">{{ trans.get('keys.khong_tim_thay_du_lieu') }}</td>
                                            </tr>
                                            <tr v-else v-for="(item,index) in posts">
                                                <td>{{ (current-1)*row+(index+1) }}</td>
                                                <td>
                                                  <template v-if="slug_can('tms-system-user-view')">
                                                    <router-link
                                                      :to="{ name: 'EditUserById', params: { user_id: item.user_id }, query: {type: 'system'} }">
                                                      {{ item.user ? item.user.fullname : '' }}
                                                    </router-link>
                                                  </template>
                                                  <template v-else>
                                                    {{ item.user ? item.user.fullname : '' }}
                                                  </template>
                                                </td>
                                                <td>{{ item.organization ? item.organization.name : '' }}</td>
                                                <td v-if="item.position === 'manager'">
                                                    <label class="badge badge-dark">{{ trans.get('keys.manager') }}</label>
                                                </td>
                                                <td v-else-if="item.position === 'leader'">
                                                    <label class="badge badge-warning">{{ trans.get('keys.leader') }}</label>
                                                </td>
                                                <td v-else-if="item.position === 'employee'">
                                                    <label class="badge badge-info">{{ trans.get('keys.employee') }}</label>
                                                </td>
                                                <td class="text-center">
                                                  <template v-for="(team,index) in item.teams">
                                                    <label class="badge badge-danger">{{ team.team.name }}</label>&nbsp;
                                                  </template>
                                                </td>
                                                <td class="text-center">
                                                    <router-link :title="trans.get('keys.sua_nhan_vien')"
                                                                 :class="checkEditPermission(user_role, item.position) ? 'btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2' : 'btn disabled btn-sm btn-icon btn-icon-circle btn-grey btn-icon-style-2'"
                                                                 :to="{ name: 'EditEmployee', params: { id: item.id, source_page: current, view_mode: view_mode }, query: {organization_id: query_organization_id}}">
                                                        <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                    </router-link>
                                                    <a href="javascript(0)"
                                                       @click.prevent="deletePost('/organization-employee/delete/'+item.id)"
                                                       :title="trans.get('keys.xoa_nhan_vien')"
                                                       :class="checkEditPermission(user_role, item.position) ? 'btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user' : 'btn disabled btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user'">
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
                                            <th class="text-center">{{trans.get('keys.team')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                            </tfoot>
                                        </table>
                                        <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                                            <v-pagination v-model="current" @input="onPageChange"
                                                          :page-count="totalPages" :classes=$pagination.classes
                                                          :labels=$pagination.labels></v-pagination>
                                        </div>
                                        <router-link v-if="organization_id.length !== 0"
                                                     :to="{ name: 'IndexOrganization', params: {page: source_page, back_page:'1'}}"
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
    </div>
</template>

<script>
    //import VSwitch from 'v-switch-case'
    //Vue.use(VSwitch)
    //import vPagination from 'vue-plain-pagination'
    import AssignEmployee from './AssignEmployeeComponent'
    import SystemUserCreate from '../user/CreateComponent'

    export default {
        components: {
            //vPagination,
            AssignEmployee,
            SystemUserCreate
        },
        props: {
            source_page: Number,
            current_roles: Object,
            roles_ready: Boolean,
            organization_id: {
                type: [String, Number],
                default: ''
            },
            view_mode: String,
            slugs: Array,
        },
        data() {
            return {
                employee: {
                    input_organization_id: 0,
                    input_user_id: 0,
                    position: '',
                },
                data: [],
                posts: {},
                organization: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                totalRow: 0,
                row: 10,
                user_list: [],
                user_keyword: '',
                organization_list: [],
                organization_keyword: '',
                position: '',
                query_organization_id: 0,
                organization_name: '',
                position_list: [],
                assignBatch: 0,
                selected_role: 'user',
                organization_selected: false,
                user_role: '',
                //Treeselect options
                optionsOrganize: [
                  {
                    id: 0,
                    label: this.trans.get('keys.chon_to_chuc')
                  }
                ],
                organization_id1: this.organization_id,
            }
        },
        mounted() {
            sessionStorage.clear();
            this.selectOrganization();
        },
        destroyed() {
            sessionStorage.setItem('employeeBack', '1');
            sessionStorage.setItem('employeePage', this.current);
            sessionStorage.setItem('employeePageSize', this.row);
            sessionStorage.setItem('employeeKeyWord', this.keyword);
            sessionStorage.setItem('employeePosition', this.position);
        },
        methods: {
            slug_can(permissionName) {
              return this.slugs.indexOf(permissionName) !== -1;
            },
            checkEditPermission(role, row_role) {
                if (role === 'admin' || role === 'root' || this.slug_can('tms-system-administrator-grant')) {
                  return true
                }
                if (role === 'manager') {
                  if (row_role === 'leader' || row_role === 'employee') {
                    return true
                  }
                } else if (role === 'leader') {
                  if (row_role === 'employee') {
                    return true
                  }
                }
                return false;
            },
            // selectOrganizationItem(input_id){
            //   this.employee.input_organization_id = input_id;
            // },
            // selectUserItem(input_id) {
            //   this.employee.input_user_id = input_id;
            //   this.fetchRole();
            // },
            // selectOrganization() {
            //     $('.content_search_box').addClass('loadding');
            //     axios.post('/organization/list', {
            //         keyword: this.organization_keyword,
            //         paginated: 0 //không phân trang
            //     })
            //         .then(response => {
            //             this.organization_list = response.data;
            //             $('.content_search_box').removeClass('loadding');
            //         })
            //         .catch(error => {
            //             $('.content_search_box').removeClass('loadding');
            //         })
            // },
            selectOrganization(current_id) {
              $('.content_search_box').addClass('loadding');
              axios.post('/organization/list', {
                keyword: this.organization_keyword,
                level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                paginated: 0 //không phân trang
              })
                .then(response => {
                  this.organization_list = response.data;
                  //Set options recursive
                  this.optionsOrganize = this.setOptions(response.data, current_id);
                  $('.content_search_box').removeClass('loadding');
                })
                .catch(error => {
                  $('.content_search_box').removeClass('loadding');
                })
            },
            setOptions(list, current_id) {
              let outPut = [];
              for (const [key, item] of Object.entries(list)) {
                let newOption = {
                  id: item.id,
                  label: item.name,
                };
                if (item.children.length > 0) {
                  for (const [key, child] of Object.entries(item.children)) {
                    if (child.id === current_id) {
                      newOption.isDefaultExpanded = true;
                      break;
                    }
                  }
                  newOption.children = this.setOptions(item.children, current_id);
                }
                outPut.push(newOption);
              }
              return outPut;
            },
            // selectUser(){
            //   $('.content_search_box').addClass('loadding');
            //   axios.post('/organization-employee/list-user', {
            //     keyword: this.user_keyword,
            //     organization_id: this.query_organization_id
            //   })
            //     .then(response => {
            //       this.user_list = response.data;
            //       $('.content_search_box').removeClass('loadding');
            //     })
            //     .catch(error => {
            //       $('.content_search_box').removeClass('loadding');
            //     })
            // },
            getDataList(paged) {
              this.$route.params.page = undefined;
              axios.post('/organization-employee/list', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    organization_id: this.organization_id1,
                    position: this.position,
                    role: this.selected_role,
                    view_mode: this.view_mode || 'recursive'
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.user_role = response.data.role;
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                        this.totalRow = response.data ? response.data.total : 0;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            // createEmployee(){
            //   if(!this.employee.input_user_id){
            //     $('.user_required').show();
            //     return;
            //   }
            //
            //   if((!this.organization_id || this.organization_id === 0) && !this.employee.organization_id){
            //     $('.organization_required').show();
            //     return;
            //   }
            //
            //   if(!this.employee.position){
            //     $('.position_required').show();
            //     return;
            //   }
            //
            //   axios.post('/organization-employee/create',{
            //     organization_id: this.employee.input_organization_id,
            //     user_id: this.employee.input_user_id,
            //     position: this.employee.position
            //   })
            //     .then(response => {
            //       if(response.data.key) {
            //         toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
            //         $('.form-control').removeClass('error');
            //         $('#employee_'+response.data.key).addClass('error');
            //       }else{
            //         if(response.data.status === 'success'){
            //           toastr['success'](response.data.message, this.trans.get('keys.thanh_cong'));
            //           //reset form
            //           if (this.organization_selected === false) {
            //             this.employee.input_organization_id = 0;
            //           }
            //           this.employee.input_user_id = 0;
            //           this.employee.position = '';
            //           this.getDataList(this.current);
            //           $(".selected_organization_content").text(this.trans.get('keys.to_chuc'));
            //           $(".selected_user_content").text(this.trans.get('keys.nhan_vien'));
            //
            //           $('.form-control').removeClass('error');
            //         }
            //       }
            //     })
            //     .catch(error => {
            //       toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
            //     })
            // },
            // fetchRole() {
            //   axios.post('/organization-employee/get-user-detail/'+ this.employee.input_user_id)
            //     .then(response => {
            //       this.position_list = response.data;
            //     })
            //     .catch(error => {
            //       console.log(error.response.data);
            //     })
            // },
            onPageChange() {
                // let back = this.getParamsBackPage();
                if(sessionStorage.getItem('employeeBack') == '1') {
                  this.current = Number(sessionStorage.getItem('employeePage'));
                  this.row = Number(sessionStorage.getItem('employeePageSize'));
                  this.keyword = sessionStorage.getItem('employeeKeyWord');
                  this.position = sessionStorage.getItem('employeePosition');
                  this.$route.params.back_page= null;
                }
                let organization_id_string = this.organization_id.toString();
                if (this.organization_id.length !== 0) {
                    //Có truyền organization_id
                    this.query_organization_id = parseInt(organization_id_string);
                    this.fetchOrganizationInfo(this.query_organization_id);
                } else if (this.query_organization_id !== 0 && typeof this.query_organization_id !== 'undefined') {
                    //Chuyển trang khi đã có query_organization_id
                    this.fetchOrganizationInfo(this.query_organization_id);
                } else {
                  this.getDataList();
                }
            },
            getParamsBackPage() {
              return this.$route.params.back_page;
            },
            setParamsBackPage(value) {
              this.$route.params.back_page = value;
            },
            deletePost(url) {
                sessionStorage.setItem('employeePage', this.current);
                sessionStorage.setItem('employeePageSize', this.row);
                sessionStorage.setItem('employeeKeyWord', this.keyword);
                sessionStorage.setItem('employeePosition', this.position);
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
                            if(current_pos.posts.length == 1){
                              current_pos.current = current_pos.current > 1 ? current_pos.current -1 : 1 ;
                            }
                            current_pos.onPageChange();
                            //reload assign batch
                            current_pos.assignBatch += 1;
                        })
                        .catch(error => {
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });

                return false;
            },
            fetchOrganizationInfo(organization_id) {
                axios.post('/organization/detail/' + organization_id, {
                    role: this.selected_role
                })
                    .then(response => {
                        this.organization_name = response.data.name;
                        this.query_organization_id = response.data.id ? response.data.id : 0;
                        if (this.query_organization_id !== 0) {
                            this.organization_selected = true;
                        }
                        //this.employee.input_organization_id = response.data.id;
                        //gọi list sau trong trường hợp manager / leader
                        if (this.roles_ready) {
                            //let page = this.getParamsPage();
                            //this.getDataList(page);
                            this.getDataList();
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            getRoleFromCurrentRoles(current_roles) {
                if (current_roles.root_user === true) {
                    this.selected_role = 'root';
                } else if (current_roles.has_role_admin === true) {
                    this.selected_role = 'admin';
                } else if (current_roles.has_role_manager === true) {
                    this.selected_role = 'manager';
                } else if (current_roles.has_role_leader === true) {
                    this.selected_role = 'leader';
                } else if (current_roles.has_user_market === true) {
                    this.selected_role = 'user_market';
                } else {
                    this.selected_role = 'user';
                }
            },
            // updatePositionFilter() {
            //   if (this.selected_role === 'manager') {
            //     this.filterPosition = [
            //       {
            //         key: 'leader',
            //         value: this.trans.get('keys.leader')
            //       },
            //       {
            //         key: 'employee',
            //         this.trans.get('keys.employee')
            //       }
            //     ]
            //   }
            //   if (this.selected_role === 'leader') {
            //     this.filterPosition = [
            //       {
            //         key: 'employee',
            //         this.trans.get('keys.employee')
            //       }
            //     ]
            //   }
            // }
        },
        computed: { //Phải gọi trên html nó mới trigger computed value
            filterPosition: function () {
                let default_response = [
                    {
                        key: 'manager',
                        value: this.trans.get('keys.manager')
                    },
                    {
                        key: 'leader',
                        value: this.trans.get('keys.leader')
                    },
                    {
                        key: 'employee',
                        value: this.trans.get('keys.employee')
                    }
                ];

                if (this.roles_ready) {
                    this.getRoleFromCurrentRoles(this.current_roles);
                    //overwrite filter for manager / leader

                    if (this.selected_role === 'root' || this.selected_role === 'admin') {
                        return default_response;
                    }

                    let response = [];
                    if (this.selected_role === 'manager') {
                        response.push({
                            key: 'leader',
                            value: this.trans.get('keys.leader')
                        });
                    }
                    if (this.selected_role === 'manager' || this.selected_role === 'leader') {
                        response.push({
                            key: 'employee',
                            value: this.trans.get('keys.employee')
                        });
                    }
                    return response;
                } else {
                    return default_response;
                }
            },
            getSelectedRole: function () {
                if (this.roles_ready) {
                    let organization_id_string = this.organization_id.toString();
                    this.getRoleFromCurrentRoles(this.current_roles);
                    if (this.organization_id.length === 0) {
                        this.fetchOrganizationInfo(0);
                    } else {
                        this.fetchOrganizationInfo(parseInt(organization_id_string));
                    }
                }
            }
        },
        // watch: {
        //   roles_ready: function(newVal, oldVal) {
        //     if (newVal === true && oldVal === false) {
        //         let organization_id_string = this.organization_id.toString();
        //       this.getRoleFromCurrentRoles(this.current_roles);
        //       if (this.organization_id.length === 0) {
        //           this.fetchOrganizationInfo(0);
        //       } else {
        //         this.fetchOrganizationInfo(parseInt(organization_id_string));
        //       }
        //     }
        //   }
        // }
    }
</script>

<style scoped>

</style>
