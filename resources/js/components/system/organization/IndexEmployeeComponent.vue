<template>
  <div class="container-fluid mt-15">
    <div class="row">
    <div class="col">
      <nav class="breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
          <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
          <li class="breadcrumb-item active">{{ trans.get('keys.chi_nhanh') }}</li>
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
                <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}</a>
              </div>
              <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="department_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten_chi_nhanh')+' *'" required v-model="department.name">
                        </div>
                        <label v-if="!department.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="department_code" class="form-control form-control-line" :placeholder="trans.get('keys.ma_chi_nhanh')+' *'" required v-model="department.code">
                        </div>
                        <em>{{ trans.get('keys.gom_chu_cai_(viet_lien_khong_dau),chu_so,ky_tu_dac_biet(-_/.)') }}</em>
                        <label v-if="!department.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="department_des" class="form-control form-control-line" :placeholder="trans.get('keys.mo_ta')" required v-model="department.des">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <div class="wrap_search_box">
                            <div class="btn_search_box" @click="listDataSearchBoxUser()">
                              <span>{{trans.get('keys.giam_doc_chi_nhanh')}} *</span>
                            </div>
                            <div class="content_search_box">
                              <input @input="listDataSearchBoxUser()" type="text" v-model="search_box_user" class="form-control search_box">
                              <i class="fa fa-spinner" aria-hidden="true"></i>
                              <ul>
                                <li class="disable">{{trans.get('keys.giam_doc_chi_nhanh')}} *</li>
                                <li @click="selectSearchBoxUser(item.user_id)" v-for="item in data_search_box_user" :data-value="item.user_id">{{item.fullname}}</li>
                              </ul>
                            </div>
                          </div>
                          <label v-if="!department.manage" class="text-danger manage_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary btn-sm" @click="createDepartment()">{{trans.get('keys.them')}}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-body">
                <div class="listData">
                  <h5 class="mb-20">{{trans.get('keys.danh_sach_chi_nhanh')}}</h5>
                  <div class="row">
                    <div class="col-sm-8 dataTables_wrapper">
                      <div class="dataTables_length" style="display: inline-block;">
                        <label>{{trans.get('keys.hien_thi')}}
                          <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getDepartmentList(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <form v-on:submit.prevent="getDepartmentList(1)">
                        <div class="d-flex flex-row form-group">
                          <input  v-model="keyword" type="text"

                                  class="form-control search_text" :placeholder="trans.get('keys.nhap_ten_ma_chi_nhanh')">
                          <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter"

                                  @click="getDepartmentList(1)">
                            {{trans.get('keys.tim')}}
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                  <div class="mt-10 mb-20">
                    <strong>
                      {{trans.get('keys.tong_so_chi_nhanh_hien_tai')}} :{{totalRow}}
                    </strong>
                  </div>
                  <div class="table-responsive">
                    <table class="table_res">
                      <thead>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ma_chi_nhanh')}}</th>
                      <th>{{trans.get('keys.ten_chi_nhanh')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.giam_doc_chi_nhanh')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                      </thead>
                      <tbody>
                      <tr v-if="posts.length == 0">
                        <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                      </tr>
                      <tr v-else v-for="(item,index) in posts">
                        <td>{{ (current-1)*row+(index+1) }}</td>
                        <td>{{ item.code }}</td>
                        <td>{{ item.name }}</td>
                        <td class=" mobile_hide">
                          {{ item.fullname }}
                        </td>
                        <td class=" mobile_hide">
                          <router-link :title="trans.get('keys.xem_danh_sach_tinh_thanh')" :to="{ name: 'DepartmentCityIndex', params: { id: item.id } }">
                            {{ item.city_count }} (<span class="text-underline">{{trans.get('keys.tinh_thanh')}}</span>)
                          </router-link>
                        </td>
                        <td>
                          <router-link :title="trans.get('keys.sua_chi_nhanh')"
                                       class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                       :to="{ name: 'EditDepartment', params: { id: item.id }}">
                            <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                          </router-link>

                          <a href="javascript(0)" @click.prevent="deletePost('/system/organize/department/delete/'+item.id)" :title="trans.get('keys.xoa_chi_nhanh')"
                             class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user">
                            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                          </a>
                        </td>
                      </tr>
                      </tbody>
                      <tfoot>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ma_chi_nhanh')}}</th>
                      <th>{{trans.get('keys.ten_chi_nhanh')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.giam_doc_chi_nhanh')}}</th>
                      <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
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
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    //import VSwitch from 'v-switch-case'

    //Vue.use(VSwitch)
    //import vPagination from 'vue-plain-pagination'

    export default {
        //components: {vPagination},
        data() {
            return {
                department: {
                    name: '',
                    code:'',
                    manage:0,
                    des:'',
                },
                data:[],
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                totalRow:0,
                row:10,
                data_search_box_user:[],
                search_box_user:'',
            }
        },
        methods: {
            selectSearchBoxUser(input_search_box_id){
                this.department.manage = input_search_box_id;
            },
            listDataSearchBoxUser(){
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/department/data_search_box_user',{
                    keyword:this.search_box_user,
                })
                    .then(response => {
                        this.data_search_box_user = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                    })
            },
            getDepartmentList(paged) {
                axios.post('/system/organize/department/list_all_department', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : 1;
                        this.totalPages = response.data.pagination ? response.data.pagination.total : 0;
                        this.totalRow = response.data.pagination ? response.data.pagination.totalRow : 0;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },


            createDepartment(){
                if(!this.department.name){
                    $('.name_required').show();
                    return;
                }
                if(!this.department.code){
                    $('.code_required').show();
                    return;
                }
                if(this.department.manage == 0){
                    $('.manage_required').show();
                    return;
                }
                axios.post('/system/organize/department/create',{
                    name:this.department.name,
                    code:this.department.code,
                    manage:this.department.manage,
                    des:this.department.des
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#department_'+response.data.key).addClass('error');
                        }else{
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status == 'success'){
                                this.department.des = this.department.name = this.department.code = '';
                                this.department.manage = 0;
                                $('.form-control').removeClass('error');
                            }
                        }

                    })
                    .catch(error => {
                        roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    })
            },
            onPageChange() {
                this.getDepartmentList();
            },
            deletePost(url) {
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.notifi_delete_branch'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post(url)
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });

                return false;
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
