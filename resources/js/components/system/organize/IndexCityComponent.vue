<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.tinh_thanh') }}</li>
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
                <a :class="department == 0 ? 'collapsed' : ''" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true">
                  <i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_thu_cong')}}
                </a>
              </div>
              <div id="collapse_1" :class="department == 0 ? 'collapse' : 'collapse show'" data-parent="#accordion_1" role="tabpanel">
                <div class="card-body">
                  <!--<input type="file" ref="file" name="file" class="dropify fileImport" />
                  <div class="button-list">
                      <button type="button" class="btn btn-primary btn-sm hasLoading" @click="importCity()">Import Tỉnh thành<i class="fa fa-spinner" aria-hidden="true"></i></button>
                  </div>-->
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <select class="form-control form-control-line" v-model.district="city.district" required>
                            <option value="">{{trans.get('keys.khu_vuc')}} *</option>
                            <option value="MB">{{trans.get('keys.mien_bac')}}</option>
                            <option value="MT">{{trans.get('keys.mien_trung')}}</option>
                            <option value="MN">{{trans.get('keys.mien_nam')}}</option>
                          </select>
                        </div>
                        <label v-if="!city.district" class="text-danger district_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <select class="form-control form-control-line" v-model="city.department" :disabled="department != 0 ? true : false">
                            <option value="0">{{trans.get('keys.chi_nhanh')}}</option>
                            <option v-for="item in department_list" :value="item.id">{{item.name}}</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="city_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten_tinh_thanh')+' *'" required v-model.name="city.name">
                        </div>
                        <label v-if="!city.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <div class="input-group">
                          <input type="text" id="city_code" class="form-control form-control-line" :placeholder="trans.get('keys.ma')+' *'" required v-model.code="city.code">
                        </div>
                        <em>{{ trans.get('keys.gom_chu_cai_(viet_lien_khong_dau),chu_so,ky_tu_dac_biet(-_/.)') }}</em>
                        <label v-if="!city.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group button-list text-right">
                        <router-link v-if="department != 0"
                                     :to="{
                             name: 'DepartmentCityIndex',
                             params: { id: department },
                           }" class="btn btn-secondary btn-sm">
                          {{trans.get('keys.quay_lai')}}
                        </router-link>
                        <button type="submit" class="btn btn-primary btn-sm" @click="createCity()">{{trans.get('keys.them')}}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card" v-if="department == 0">
              <div class="card-body">
                <div class="listData">
                  <h5 class="mb-20">{{trans.get('keys.danh_sach_tinh_thanh')}}</h5>
                  <div class="row">
                    <div class="col-sm-8 dataTables_wrapper">

                      <div class="dataTables_length" style="display: inline-block;">
                        <label>{{trans.get('keys.hien_thi')}}
                          <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getCityData(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                          </select>
                        </label>
                      </div>

                      <div class="fillterConfirm" style="display: inline-block;">
                        <label>
                          <select v-model="district" class="custom-select custom-select-sm form-control form-control-sm" @change="getCityData(1)">
                            <option value="">{{trans.get('keys.khu_vuc')}}</option>
                            <option value="MB">{{trans.get('keys.mien_bac')}}</option>
                            <option value="MT">{{trans.get('keys.mien_trung')}}</option>
                            <option value="MN">{{trans.get('keys.mien_nam')}}</option>
                          </select>
                        </label>
                      </div>

                      <!--<div class="fillterConfirm" style="display: inline-block;">
                          <label>
                              <select v-model="type" class="custom-select custom-select-sm form-control form-control-sm" @change="getCityData(1)">
                                  <option value="">Đơn vị hành chính</option>
                                  <option value="Thành phố Trung ương">Thành phố Trung ương</option>
                                  <option value="Thành phố">Thành phố</option>
                                  <option value="Tỉnh">Tỉnh</option>
                                  <option value="Quận">Quận</option>
                                  <option value="Huyện">Huyện</option>
                                  <option value="Xã">Xã</option>
                                  <option value="Phường">Phường</option>
                                  <option value="Thị xã">Thị xã</option>
                                  <option value="Thị trấn">Thị trấn</option>
                              </select>
                          </label>
                      </div>-->

                      <div class="fillterConfirm col-sm-6" style="display: inline-block;">
                        <v-select
                          @input="getCityData(1)"
                          :options="departmentSelectOptions"
                          :reduce="departmentSelectOption => departmentSelectOption.id"
                          :placeholder="this.trans.get('keys.chon_chi_nhanh')"
                          :filter-by="myFilterBy"
                          v-model="department_filter">
                        </v-select>
                      </div>

                    </div>

                    <div class="col-sm-4">
                      <form v-on:submit.prevent="getCityData(1)">
                        <div class="d-flex flex-row form-group">
                          <input  v-model="keyword" type="text"

                                  class="form-control search_text" :placeholder="trans.get('keys.nhap_ten_ma_tinh')+' ...'">
                          <button type="button" class="btn btn-primary btn-sm btn_fillter" id="btnFilter"

                                  @click="getCityData(1)">
                            {{trans.get('keys.tim')}}
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table_res">
                      <thead>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ma')}}</th>
                      <th>{{trans.get('keys.ten_tinh_thanh')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.khu_vuc')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.chi_nhanh')}}</th>
                      <!--<th>Đơn vị hành chính</th>-->
                      <th class="mobile_hide">{{trans.get('keys.dai_ly')}}</th>
                      <th>{{trans.get('keys.hanh_dong')}}</th>
                      </thead>
                      <tbody>
                      <tr v-if="posts.length == 0">
                        <td colspan="7">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                      </tr>
                      <tr v-else v-for="(city,index) in posts">
                        <td>{{ (current-1)*row+(index+1) }}</td>
                        <td>{{ city.code }}</td>
                        <td>{{ city.name }}</td>
                        <td class="mobile_hide">
                          <span v-if="city.district == 'MB'">{{trans.get('keys.mien_bac')}}</span>
                          <span v-else-if="city.district == 'MT'">{{trans.get('keys.mien_trung')}}</span>
                          <span v-else-if="city.district == 'MN'">{{trans.get('keys.mien_nam')}}</span>
                          <span v-else></span>
                        </td>
                        <td class="mobile_hide">
                          {{ city.department_name }}
                        </td>
                        <!--<td>
                            {{ city.type }}
                        </td>-->
                        <td class="mobile_hide">
                          <router-link :title="trans.get('keys.xem_danh_sach_dai_ly')"
                                       :to="{
                                         name: 'BranchIndexByCity',
                                         params: { city: city.id },
                                       }">
                            {{city.branch_count}} (<span class="text-underline">{{trans.get('keys.dai_ly')}}</span>)
                          </router-link>
                        </td>
                        <td>


                          <router-link :title="trans.get('keys.sua_tinh_thanh')"
                                       :to="{
                                           name: 'EditCity',
                                           params: { city_id: city.id },
                                         }"
                                       class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2">
                            <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                          </router-link>

                          <!--<a :title="trans.get('keys.them_dai_ly_vao_tinh_thanh')"
                             class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                             :href="trans.get('keys.language')+'/system/organize/city/add_branch/'+city.id"
                          ><span class="btn-icon-wrap"><i
                              class="fal fa-arrow-alt-right"></i></span></a>-->

                          <a href="javascript(0)" @click.prevent="deletePost('/system/organize/city/delete/'+city.id)" :title="trans.get('keys.xoa_tinh_thanh')"
                             class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2 delete-user">
                            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                          </a>
                        </td>
                      </tr>
                      </tbody>
                      <tfoot>
                      <th>{{trans.get('keys.stt')}}</th>
                      <th>{{trans.get('keys.ma')}}</th>
                      <th>{{trans.get('keys.ten_tinh_thanh')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.khu_vuc')}}</th>
                      <th class="mobile_hide">{{trans.get('keys.chi_nhanh')}}</th>
                      <!--<th>Đơn vị hành chính</th>-->
                      <th class="mobile_hide">{{trans.get('keys.dai_ly')}}</th>
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
    //import vSelect from 'vue-select'
    //import 'vue-select/dist/vue-select.css';

    //Vue.use(VSwitch);

    //import vPagination from 'vue-plain-pagination'

    export default {
        props: ['department'],
        //components: {vPagination, vSelect},
        data() {
            return {
                city: {
                    name: '',
                    district:'',
                    code:'',
                    user_id:0,
                    description:'',
                    department: this.department ? this.department : 0
                },
                data:[],
                posts: {},
                keyword: '',
                current: 1,
                totalPages: 0,
                district: '',
                department_filter: '',
                row: 10,
                type:'',
                department_list:[],
                departmentSelectOptions: [],
            }
        },
        methods: {
            importCity(){
                if(!$('button.hasLoading').hasClass('loadding')){
                    $('button.hasLoading').addClass('loadding');
                    this.formData = new FormData();
                    this.formData.append('file', this.$refs.file.files[0]);
                    axios.post('/system/organize/city/import_city', this.formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            this.data_import = response.data;
                            $('button.hasLoading').removeClass('loadding');
                            $('#btnFilter').trigger('click');
                            console.log(this.data_import);
                        })
                        .catch(error => {
                            $('button.hasLoading').removeClass('loadding');
                            swal({
                                title: "Thông báo",
                                text: " Lỗi hệ thống.Thao tác thất bại!",
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        });
                }
            },
            getDepartment() {
                //this.department = '';
                this.departmentSelectOptions = []; //reset after search again
                axios.post('/system/organize/city/get_department_list')
                    .then(response => {
                        this.department_list = response.data ? response.data : [];
                        let additionalDepartments = [];
                        response.data.forEach(function(departmentItem) {
                            let newDepartment = {
                                label: departmentItem.name,
                                id: departmentItem.id
                            };
                            additionalDepartments.push(newDepartment);
                        });
                        this.departmentSelectOptions = additionalDepartments;
                    })
                    .catch(error => {
                        this.department_list = [];
                    });
            },
            getCityData(paged) {
                axios.post('/system/organize/city/list_data', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    district: this.district,
                    department: this.department_filter,
                    type: this.type
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
            createCity(){
                if(!this.city.district){
                    $('.district_required').show();
                    return;
                }
                if(!this.city.name){
                    $('.name_required').show();
                    return;
                }
                if(!this.city.code){
                    $('.code_required').show();
                    return;
                }
                axios.post('/system/organize/city/create',{
                    name:this.city.name,
                    code:this.city.code,
                    department:this.city.department,
                    district:this.city.district,
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#city_'+response.data.key).addClass('error');
                        }else{
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status == 'success'){
                                this.city.district = this.city.name = this.city.code = '';
                                $('.form-control').removeClass('error');
                                if(this.department != 0){
                                  this.$router.push({ name: 'DepartmentCityIndex', params: { id: this.department }});
                                }
                            }
                        }

                    })
                    .catch(error => {
                        roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    })
            },
            onPageChange() {
                this.getCityData();
            },
            deletePost(url) {
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.tinh_thanh_ban_muon_xoa_co_the_co_du_lieu_dai_ly_di_kem_ban_co_chac_muon_xoa'),
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
            },
            myFilterBy: (option, label, search) => {
                if (!label) {
                    label = '';
                }
                let new_search = convertUtf8(search);
                let new_label = convertUtf8(label);
                //return this.filterBy(option, new_label, new_search); //can not call components function here
                return (new_label || '').toLowerCase().indexOf(new_search) > -1; // "" not working
            }
        },
        mounted() {
            this.getDepartment();
        }
    }
    function convertUtf8(str) {
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
        str = str.replace(/đ/g,"d");
        str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g," ");
        str = str.replace(/ + /g," ");
        str = str.trim();
        return str;
    }
</script>

<style scoped>

</style>
