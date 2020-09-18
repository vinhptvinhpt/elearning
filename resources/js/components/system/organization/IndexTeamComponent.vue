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
                        <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_team') }}</li>
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
                                <div class="row">
                                  <div class="col-sm-6">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" id="team_name" class="form-control form-control-line"
                                               :placeholder="trans.get('keys.ten_team')+' *'" required v-model="team.name">
                                      </div>
                                      <label for="team_name" v-if="!team.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" id="team_code" class="form-control form-control-line"
                                               :placeholder="trans.get('keys.ma_team')+' *'" required v-model="team.code">
                                      </div>
                                      <label for="team_code" v-if="!team.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                      <em>{{ trans.get('keys.gom_chu_cai_(viet_lien_khong_dau),chu_so,ky_tu_dac_biet(-_/.)') }}</em>
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" id="team_description" class="form-control form-control-line"
                                               :placeholder="trans.get('keys.mo_ta')" required v-model="team.description">
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="form-group">
                                      <treeselect :disabled="true" v-model="organization_id" :multiple="false" :options="optionsOrganize" id="team_organization_id"/>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-12">
                                    <div class="form-group text-right">
                                      <button type="submit" class="btn btn-primary btn-sm" @click="createTeam()">
                                        {{trans.get('keys.them')}}
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="listData">
                                    <h5 class="mb-20">{{trans.get('keys.danh_sach_team')}} - {{ organization_name }}</h5>
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
                                        </div>
                                    </div>
                                    <div class="mt-10 mb-20">
                                        <strong>
                                            {{trans.get('keys.tong_so_team_hien_tai')}} :{{totalRow}}
                                        </strong>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table_res">
                                            <thead>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.ten')}}</th>
                                            <th>{{trans.get('keys.ma')}}</th>
                                            <th class="text-center">{{trans.get('keys.so_luong_thanh_vien')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                            </thead>
                                            <tbody>
                                            <tr v-if="posts.length == 0">
                                                <td colspan="5">{{ trans.get('keys.khong_tim_thay_du_lieu') }}</td>
                                            </tr>
                                            <tr v-else v-for="(item,index) in posts">
                                                <td>{{ (current-1)*row+(index+1) }}</td>
                                                <td>{{ item.name }}</td>
                                                <td>{{ item.code }}</td>
                                                <td class="text-center" v-if="slug_can('tms-system-employee-view')">
                                                  <router-link
                                                    :title="trans.get('keys.xem_thanh_vien')"
                                                    :to="{ name: 'EditTeam', params: { id: item.id, source_page: current }}">
                                                    {{ item.employees.length }}
                                                  </router-link>
                                                </td>
                                                <td class="text-center">
                                                    <router-link :title="trans.get('keys.sua_team')"
                                                                 :class="checkEditPermission(user_role, item.position) ? 'btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2' : 'btn disabled btn-sm btn-icon btn-icon-circle btn-grey btn-icon-style-2'"
                                                                 :to="{ name: 'EditTeam', params: { id: item.id, source_page: current }}">
                                                        <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                    </router-link>

                                                    <a href="javascript(0)"
                                                       @click.prevent="deletePost('/organization-team/delete/'+item.id)"
                                                       :title="trans.get('keys.xoa_team')"
                                                       :class="checkEditPermission(user_role, item.position) ? 'btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user' : 'btn disabled btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user'">
                                                        <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th>{{trans.get('keys.ten')}}</th>
                                            <th>{{trans.get('keys.ma')}}</th>
                                            <th class="text-center">{{trans.get('keys.so_luong_thanh_vien')}}</th>
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

    export default {
        components: {},
        props: {
            source_page: Number,
            current_roles: Object,
            roles_ready: Boolean,
            organization_id: {
                type: [String, Number],
                default: ''
            },
            slugs: Array,
        },
        data() {
            return {
                team: {
                  name: '',
                  code: '',
                  description: '',
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
                organization_name: '',
                position_list: [],
                assignBatch: 0,
                selected_role: 'user',
                organization_selected: false,
                user_role: '',
                //Treeselect options
                optionsOrganize: []
            }
        },
        mounted() {
            this.selectOrganization();
        },
        destroyed() {
            sessionStorage.setItem('teamPage', this.current);
            sessionStorage.setItem('teamPageSize', this.row);
            sessionStorage.setItem('teamKeyWord', this.keyword);
            sessionStorage.setItem('teamPosition', this.position);
        },
        methods: {
            createTeam() {
              if (!this.team.name) {
                $('.name_required').show();
                return;
              }
              if (!this.team.code) {
                $('.code_required').show();
                return;
              }
              axios.post('/organization-team/create', {
                name: this.team.name,
                code: this.team.code,
                organization_id: this.organization_id,
                description: this.team.description
              })
                .then(response => {
                  if (response.data.key) {
                    toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                    $('.form-control').removeClass('error');
                    $('#organization_' + response.data.key).addClass('error');
                  } else {
                    if (response.data.status === 'success') {
                      //reset form
                      this.team.description = this.team.name = this.team.code = '';
                      $('.form-control').removeClass('error');
                    }
                    toastr[response.data.status](response.data.message, this.trans.get('keys.thanh_cong'));
                    this.getDataList();
                  }
                })
                .catch(error => {
                  toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
                })
            },
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
            getDataList(paged) {
              this.$route.params.page = undefined;
              axios.post('/organization-team/list', {
                    page: paged || this.current,
                    row: this.row,
                    organization_id: this.organization_id
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                        this.totalRow = response.data ? response.data.total : 0;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            onPageChange() {
                let back = this.getParamsBackPage();
                if(back == '1') {
                  this.current = Number(sessionStorage.getItem('teamPage'));
                  this.row = Number(sessionStorage.getItem('teamPageSize'));
                  this.keyword = sessionStorage.getItem('teamKeyWord');
                  this.position = sessionStorage.getItem('teamPosition');

                  sessionStorage.removeItem('teamPage');
                  sessionStorage.removeItem('teamPageSize');
                  sessionStorage.removeItem('teamKeyWord');
                  sessionStorage.removeItem('teamPosition');
                  this.$route.params.back_page= null;
                }

                this.fetchOrganizationInfo(this.organization_id);
                this.getDataList();
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
                    text: this.trans.get('keys.ban_co_muon_xoa'),
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
            }
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
