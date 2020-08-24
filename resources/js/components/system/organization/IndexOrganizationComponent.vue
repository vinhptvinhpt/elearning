<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.to_chuc') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="accordion" id="accordion_1">
            <div v-if="slug_can('tms-system-organize-add')" class="card">
              <div class="card-header d-flex justify-content-between">
                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i
                  class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi')}}</a>
              </div>
              <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                <div class="card-body">
                  <div class="row">

                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="organization_name" class="form-control form-control-line"
                                 :placeholder="trans.get('keys.ten_to_chuc')+' *'" required v-model="organization.name">
                        </div>
                        <label for="organization_name" v-if="!organization.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="organization_code" class="form-control form-control-line"
                                 :placeholder="trans.get('keys.ma_to_chuc')+' *'" required v-model="organization.code">
                        </div>
                        <label for="organization_code" v-if="!organization.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                        <em>{{ trans.get('keys.gom_chu_cai_(viet_lien_khong_dau),chu_so,ky_tu_dac_biet(-_/.)') }}</em>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="organization_description" class="form-control form-control-line"
                                 :placeholder="trans.get('keys.mo_ta')" required v-model="organization.description">
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">

                        <!--                        <div class="input-group">-->
                        <!--                          <div class="wrap_search_box">-->
                        <!--                            <div class="btn_search_box" @click="selectParent()">-->
                        <!--                              <span class="selected_content">{{trans.get('keys.truc_thuoc')}}</span>-->
                        <!--                            </div>-->
                        <!--                            <div class="content_search_box">-->
                        <!--                              <input @input="selectParent()" type="text" v-model="parent_keyword" class="form-control search_box">-->
                        <!--                              <i class="fa fa-spinner" aria-hidden="true"></i>-->
                        <!--                              <ul>-->
                        <!--                                <li @click="selectParentItem(item.id)" v-for="item in organization_parent_list" :data-value="item.id">{{item.name}}</li>-->
                        <!--                              </ul>-->
                        <!--                            </div>-->
                        <!--                          </div>-->
                        <!--                        </div>-->
                        <treeselect v-model="organization.parent_id" :multiple="false" :options="options"
                                    id="organization_parent_id"/>
                      </div>
                    </div>

                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary btn-sm" @click="createOrganization()">
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
                  <h5 class="mb-20">{{trans.get('keys.danh_sach_to_chuc')}}</h5>
                  <div class="row">
                    <div class="col-sm-7 dataTables_wrapper">
                      <div class="dataTables_length" style="display: inline-block;">
                        <label>{{trans.get('keys.hien_thi')}}
                          <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm"
                                  @change="getDataList(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </label>
                      </div>
                      <div v-if="max_level > 0" class="fillterConfirm" style="display: inline-block;">
                        <label>
                          <select v-if="max_level > 0" v-model="level"
                                  class="custom-select custom-select-sm form-control form-control-sm"
                                  @change="getDataList(1)">
                            <option value="0">{{ trans.get('keys.chon_cap') }}</option>
                            <option v-for="n in max_level" :value="n">{{ n }}</option>
                          </select>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-5">
                      <form v-on:submit.prevent="getDataList(1)">
                        <div class="d-flex flex-row form-group">
                          <input v-model="keyword" type="text" class="form-control search_text"
                                 :placeholder="trans.get('keys.nhap_ten_to_chuc')">
                          <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter"
                                  @click="getDataList(1)">
                            {{trans.get('keys.tim')}}
                          </button>
                          <a v-if="selected_role === 'root' || selected_role === 'admin'"  class="btn btn-primary btn-sm" @click="switchTreeView()">
                            <i v-if="display === 'grid'" class="fal fa-list" style="color: white; vertical-align: bottom !important;"></i>
                            <i v-if="display === 'tree'" class="fal fa-area-chart" style="color: white; vertical-align: bottom !important;"></i>
                          </a>
                        </div>
                      </form>

                    </div>
                  </div>
                  <template v-if="display === 'grid'">
                    <div class="mt-10 mb-20">
                      <strong>
                        {{trans.get('keys.tong_so')}} :{{totalRow}}
                      </strong>
                    </div>
                    <div class="table-responsive">
                      <table class="table_res">
                        <thead>
                        <th style="min-width: 30px;">{{trans.get('keys.stt')}}</th>
                        <th>{{trans.get('keys.ma_to_chuc')}}</th>
                        <th class="d-none d-sm-table-cell">{{trans.get('keys.ten_to_chuc')}}</th>
                        <th class="d-none d-sm-table-cell">{{trans.get('keys.truc_thuoc')}}</th>
<!--                        <th class="text-center">{{trans.get('keys.nhan_vien_truc_thuoc')}}</th>-->
                        <th class="text-center d-none d-sm-table-cell">{{trans.get('keys.nhan_vien')}}</th>
                        <th v-if="slug_can('tms-system-organize-edit') || slug_can('tms-system-organize-deleted')" class="text-center" style="min-width: 130px;">{{trans.get('keys.hanh_dong')}}</th>
                        </thead>
                        <tbody>
                        <tr v-if="posts.length == 0">
                          <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                        </tr>
                        <tr v-else v-for="(item,index) in posts">
                          <td>{{ (current-1)*row+(index+1) }}</td>
                          <td>{{ item.code }}</td>
                          <td class="d-none d-sm-table-cell">{{ item.name }}</td>
                          <td class="d-none d-sm-table-cell" v-if="item.parent">{{ item.parent.code }} - {{
                            item.parent.name }}
                          </td>
                          <td class="d-none d-sm-table-cell" v-else></td>
<!--                          <td class="text-center d-none d-sm-table-cell" v-if="slug_can('tms-system-employee-view')">-->
<!--                            <router-link :title="trans.get('keys.xem_nhan_vien')"-->
<!--                                         :to="{ name: 'IndexEmployee', query: { organization_id: item.id}, params: {source_page: current}}">-->
<!--                              {{ item.employees.length }}-->
<!--                            </router-link>-->
<!--                          </td>-->
                          <td class="text-center" v-if="slug_can('tms-system-employee-view')">
                            <router-link :title="trans.get('keys.xem_nhan_vien')"
                                         :to="{ name: 'IndexEmployee', query: { organization_id: item.id, view_mode: 'recursive'}, params: {source_page: current}}">
                              {{ item.recusive_employees }}
                            </router-link>
                          </td>
                          <td class="text-center" v-if="slug_can('tms-system-organize-edit') || slug_can('tms-system-organize-deleted')">

<!--                            <router-link v-if="slug_can('tms-system-employee-view')" :title="trans.get('keys.xem_nhan_vien')"-->
<!--                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"-->
<!--                                         :to="{ name: 'IndexEmployee', query: { organization_id: item.id}, params: {source_page: current}}">-->
<!--                              <span class="btn-icon-wrap"><i class="fal fa-users"></i></span>-->
<!--                            </router-link>-->

                            <router-link v-if="slug_can('tms-system-organize-edit')"
                                         :title="trans.get('keys.sua_to_chuc')"
                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                         :to="{ name: 'EditOrganization', params: { id: item.id}}">
                              <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                            </router-link>

                            <a v-if="slug_can('tms-system-organize-deleted')" href="javascript(0)"
                               @click.prevent="deletePost('/organization/delete/'+item.id)"
                               :title="trans.get('keys.xoa_to_chuc')"
                               class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user">
                              <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                            </a>

                          </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <th style="min-width: 30px;">{{trans.get('keys.stt')}}</th>
                        <th>{{trans.get('keys.ma_to_chuc')}}</th>
                        <th class="d-none d-sm-table-cell">{{trans.get('keys.ten_to_chuc')}}</th>
                        <th class="d-none d-sm-table-cell">{{trans.get('keys.truc_thuoc')}}</th>
<!--                        <th class="text-center">{{trans.get('keys.nhan_vien_truc_thuoc')}}</th>-->
                        <th class="text-center d-none d-sm-table-cell">{{trans.get('keys.nhan_vien')}}</th>
                        <th v-if="slug_can('tms-system-organize-edit') || slug_can('tms-system-organize-deleted')" class="text-center" style="min-width: 130px;">{{trans.get('keys.hanh_dong')}}</th>
                        </tfoot>
                      </table>
                      <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                        <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                      :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                      </div>
                    </div>
                  </template>
                  <template v-else-if="tree.length !== 0 && display === 'tree'">
                    <organization-chart :tree="tree"></organization-chart>
                  </template>
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
  import OrganizationChart from "./OrganizationChartComponent";

  export default {
    components: {
      //vPagination,
      OrganizationChart,
    },
    props: ['slugs', 'selected_role'],
    data() {
      return {
        organization: {
          name: '',
          code: '',
          parent_id: 0,
          description: '',
        },
        data: [],
        posts: [],
        tree: [],
        keyword: '',
        current: 1,
        totalPages: 0,
        totalRow: 0,
        row: 10,
        //organization_parent_list:[],
        parent_keyword: '',
        max_level: 0,
        level: 0,
        //Treeselect options
        options: [
          {
            id: 0,
            label: this.trans.get('keys.chon_to_chuc')
          }
        ],
        display: 'grid'
      }
    },
    methods: {
          slug_can(permissionName) {
            return this.slugs.indexOf(permissionName) !== -1;
          },
          selectParentItem(parent_id) {
            this.organization.parent_id = parent_id;
          },
          selectParent() {
            $('.content_search_box').addClass('loadding');
            axios.post('/organization/list', {
              keyword: this.parent_keyword,
              level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
              paginated: 0 //không phân trang,
            })
              .then(response => {
                //this.organization_parent_list = response.data;
                this.tree = response.data;
                //Set options recursive
                this.options = this.setOptions(response.data);
                $('.content_search_box').removeClass('loadding');
              })
              .catch(error => {
                $('.content_search_box').removeClass('loadding');
              })
          },
          setOptions(list) {
            let outPut = [];
            for (const [key, item] of Object.entries(list)) {
              let newOption = {
                id: item.id,
                label: item.name
              };
              if (item.children.length > 0) {
                newOption.children = this.setOptions(item.children);
              }
              outPut.push(newOption);
            }
            return outPut;
          },
          getDataList(paged) {
            if (this.display === 'tree') {
              this.display = 'grid';
            }
            axios.post('/organization/list', {
              page: paged || this.current,
              keyword: this.keyword,
              row: this.row,
              level: this.level,
              paginated: 1 // có phân trang
            })
              .then(response => {
                if (response.data.status === 'warning') {
                  toastr['warning'](response.data.message, this.trans.get('keys.thong_bao'));
                }
                let list = response.data.data ? response.data.data.data : [];
                list = this.employeeRecursive(list);
                this.posts = list;
                this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                this.totalRow = response.data ? response.data.total : 0;
                this.max_level = response.data.max_level ? response.data.max_level : 0;
              })
              .catch(error => {
                console.log(error);
              });
          },
          createOrganization() {
            if (!this.organization.name) {
              $('.name_required').show();
              return;
            }
            if (!this.organization.code) {
              $('.code_required').show();
              return;
            }
            axios.post('/organization/create', {
              name: this.organization.name,
              code: this.organization.code,
              parent_id: this.organization.parent_id,
              description: this.organization.description
            })
              .then(response => {
                if (response.data.key) {
                  toastr['error'](response.data.message, this.trans.get('keys.that_bai'));
                  $('.form-control').removeClass('error');
                  $('#organization_' + response.data.key).addClass('error');
                } else {
                  if (response.data.status === 'success') {
                    //reset form
                    this.organization.description = this.organization.name = this.organization.code = '';
                    this.organization.parent_id = 0;
                    $(".selected_content").text(this.trans.get('keys.truc_thuoc'));
                    $('.form-control').removeClass('error');
                    //reload form
                    this.selectParent();
                  }
                  toastr[response.data.status](response.data.message, this.trans.get('keys.thanh_cong'));
                }
              })
              .catch(error => {
                toastr['error'](this.trans.get('keys.loi_he_thong_thao_tac_that_bai'), this.trans.get('keys.thong_bao'));
              })
          },
          onPageChange() {
            let back = this.getParamsBackPage();
            if(back == '1') {
              this.current = Number(sessionStorage.getItem('organizationPage'));
              this.row = Number(sessionStorage.getItem('organizationPageSize'));
              this.keyword = sessionStorage.getItem('organizationKeyWord');
              this.level = Number(sessionStorage.getItem('organizationLevel'));

              sessionStorage.clear();
              this.$route.params.back_page= null;
            }
            this.getDataList();
            this.selectParent();
          },
          getParamsBackPage() {
            return this.$route.params.back_page;
          },
          setParamsBackPage(value) {
            this.$route.params.back_page = value;
          },
          deletePost(url) {
            sessionStorage.setItem('organizationPage', this.current);
            sessionStorage.setItem('organizationPageSize', this.row);
            sessionStorage.setItem('organizationKeyWord', this.keyword);
            sessionStorage.setItem('organizationLevel', this.level);
            this.$route.params.back_page = '1';
            let current_pos = this;
            swal({
              title: this.trans.get('keys.thong_bao'),
              text: this.trans.get('keys.ban_co_muon_xoa_to_chuc_nay'),
              type: "warning",
              showCancelButton: true,
              closeOnConfirm: true,
              showLoaderOnConfirm: true
            }, function () {
              axios.post(url)
                .then(response => {
                  roam_message(response.data.status, response.data.message);
                  if(current_pos.posts.length == 1){
                    current_pos.current = current_pos.current > 1 ? current_pos.current -1 : 1 ;
                  }
                  current_pos.onPageChange();
                })
                .catch(error => {
                  roam_message('error', current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                });
            });

            return false;
          },
          switchTreeView() {
            if (this.display === 'grid') {
              this.display = 'tree';
            } else {
              this.display = 'grid';
            }
          },
          employeeRecursive(list) {
            for (const [key, item] of Object.entries(list)) {
              item.recusive_employees = item.employees.length;
              if (item.children.length > 0) {
                item.recusive_employees = item.recusive_employees + this.countEmployee(item.children);
              }
            }
            return list;
          },
          countEmployee(list) {
                let total = 0;
                for (const [key, item] of Object.entries(list)) {
                  total += item.employees.length;
                  if (item.children.length > 0) {
                    total += this.countEmployee(item.children);
                  }
                }
                return total;
              }
      },
      mounted() {
      },
      destroyed() {
        sessionStorage.setItem('organizationPage', this.current);
        sessionStorage.setItem('organizationPageSize', this.row);
        sessionStorage.setItem('organizationKeyWord', this.keyword);
        sessionStorage.setItem('organizationLevel', this.level);
      }
    }
</script>

<style scoped>

</style>
