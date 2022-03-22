<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item">
              <router-link :to="{ name: 'DepartmentIndex' }">
                {{ trans.get('keys.danh_sach_tinh_thanh') }}
              </router-link>
            </li>
            <li class="breadcrumb-item active">{{ trans.get('keys.dai_ly') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div>
          <div class="card">
            <div class="card-body">
              <div class="edit_city_form form-material">
                <h5 class="mb-20">{{trans.get('keys.danh_sach_tinh_thanh_cua')}} : {{department.name}}</h5>
                <div class="mb-20 mt-20">
                  <div class="accordion" id="accordion_1">
                    <div class="card">
                      <div class="card-header d-flex justify-content-between">

                        <router-link :to="{ name: 'CityIndex', query: {department_id: id}  }" class="collapsed" role="button">
                          <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}
                        </router-link>

                      </div>
                    </div>
                    <div class="card">
                      <div class="card-header d-flex justify-content-between">
                        <a class="collapsed" id="formCreateNew" role="button" data-toggle="collapse"
                           href="#collapse_1" aria-expanded="true"><i class="fal fa-upload mr-3"></i>{{trans.get('keys.gan_tinh_thanh')}}</a>
                      </div>
                      <div id="collapse_1" class="collapse" data-parent="#accordion_1" role="tabpanel">
                        <div class="card-body">
                          <h6 class="mb-20">{{trans.get('keys.danh_sach_tinh_thanh_co_the_them')}}</h6>
                          <department-city-create :id="id"></department-city-create>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-8 dataTables_wrapper">
                    <div class="dataTables_length" style="display: inline-block;">
                      <label>{{trans.get('keys.hien_thi')}}
                        <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getCityByDepartment(1)">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <form v-on:submit.prevent="getCityByDepartment(1)">
                      <div class="d-flex flex-row form-group">
                        <input  v-model="keyword" type="text"
                                class="form-control search_text" :placeholder="trans.get('keys.ten_ma_tinh_thanh')+' ...'">
                        <button type="button" class="btn btn-primary btn-sm btn_fillter"
                                @click="getCityByDepartment(1)">
                          {{trans.get('keys.tim')}}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="mt-10 mb-20">
                  <strong>
                    {{trans.get('keys.tong_so_tinh_thanh')}} :{{totalRow}}
                  </strong>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table class="table_res">
                        <thead>
                        <th>{{trans.get('keys.stt')}}</th>
                        <th>{{trans.get('keys.ma_tinh_thanh')}}</th>
                        <th>{{trans.get('keys.ten_tinh_thanh')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.khu_vuc')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.dai_ly')}}</th>
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
                            <span v-if="item.district == 'MB'">{{trans.get('keys.mien_bac')}}</span>
                            <span v-else-if="item.district == 'MT'">{{trans.get('keys.mien_trung')}}</span>
                            <span v-else-if="item.district == 'MN'">{{trans.get('keys.mien_nam')}}</span>
                            <span v-else></span>
                          </td>
                          <td class=" mobile_hide">

                            <router-link :title="trans.get('keys.xem_danh_sach_dai_ly')"
                                         :to="{
                                         name: 'BranchIndexByCity',
                                         params: { city: item.city_id },
                                       }">
                              {{item.branch_count}} (<span class="text-underline">{{trans.get('keys.dai_ly')}}</span>)
                            </router-link>


                          </td>
                          <td>

                            <router-link :title="trans.get('keys.sua_tinh_thanh')"
                                         :to="{
                                           name: 'EditCity',
                                           params: { city_id: item.city_id },
                                         }"
                                         class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                              <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                            </router-link>


                            <a href="javascript(0)" @click.prevent="removeCity(item.tcd_id)" :title="trans.get('keys.go_tinh_thanh')"
                               class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                              <span class="btn-icon-wrap"><i class="fa fa-eraser"></i></span>
                            </a>
                          </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <th>{{trans.get('keys.stt')}}</th>
                        <th>{{trans.get('keys.ma_tinh_thanh')}}</th>
                        <th>{{trans.get('keys.ten_tinh_thanh')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.khu_vuc')}}</th>
                        <th class=" mobile_hide">{{trans.get('keys.dai_ly')}}</th>
                        <th>{{trans.get('keys.hanh_dong')}}</th>
                        </tfoot>
                      </table>
                      <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                        <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                      </div>
                    </div>
                    <div class="button-list">
                      <router-link to="/tms/system/organize/department" class="btn btn-secondary btn-sm">
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
  </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import DepartmentCityCreate from './AddCityFromDepartmentComponent'

    export default {
        props: ['id'],
        //components: {vPagination},
        components: {DepartmentCityCreate},
        data() {
            return {
                department:{
                    name: '',
                    code:'',
                    manage:0,
                    des:'',
                    fullname:'',
                },
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                totalRow:0,
                row: 10,
            }
        },
        methods: {
            getCityByDepartment(paged) {
                axios.post('/system/organize/department/list_city_by_department', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    id:this.id
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
            onPageChange() {
                this.getCityByDepartment();
            },
            getDataDepartment(){
                axios.post('/system/organize/department/detail_data/'+this.id)
                    .then(response => {
                        this.department = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            removeCity(id){
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.ban_muon_go_tinh_thanh_da_chon'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/system/organize/department/remove_city',{
                        id:id
                    })
                        .then(response => {
                            roam_message(response.data.status,response.data.message);
                        })
                        .catch(error => {
                            roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                        });
                });
            },
        },
        mounted() {
            this.getDataDepartment();
        }
    }
</script>

<style scoped>

</style>
