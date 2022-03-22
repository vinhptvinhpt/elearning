<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
              <router-link to="/tms/dashboard">
                {{ trans.get('keys.dashboard') }}
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
            <div class="accordion" id="accordion_1">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <a :class="(city_id != 0 ? '' : 'collapsed')" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_dai_ly')}}</a>
                    </div>
                    <div id="collapse_1" :class="(city_id != 0 ? 'collapse show' : 'collapse')" data-parent="#accordion_1" role="tabpanel">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" id="branch_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten')+' *'" required v-model.name="branch.name">
                                        </div>
                                        <label v-if="!branch.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" id="branch_code" class="form-control form-control-line" :placeholder="trans.get('keys.ma') + ' *'" v-model="branch.code" @input="inputClearMessage('code_error')">
                                        </div>
                                        <em>{{trans.get('keys.gom_chu_cai_khong_dau_chu_so_ky_tu_dac_biet_-_/_.')}}</em>
                                        <label v-if="!branch.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                                        <label v-if="branch.code" class="text-danger code_error hide">{{trans.get('keys.ma_dai_ly_da_ton_tai')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <v-select
                                            @input="listData()"
                                            :options="departmentSelectOptions"
                                            :reduce="departmentSelectOption => departmentSelectOption.id"
                                            :placeholder="this.trans.get('keys.chon_chi_nhanh')"
                                            :filter-by="myFilterBy"
                                            v-model="branch.department"
                                            data-live-search="true"
                                            :disabled="city_id == 0 ? false : true"
                                        >
                                        </v-select>

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <v-select
                                            :options="citySelectOptions"
                                            :reduce="citySelectOption => citySelectOption.id"
                                            :placeholder="this.trans.get('keys.chon_tinh_thanh') + ' *'"
                                            :filter-by="myFilterBy"
                                            v-model="branch.city"
                                            data-live-search="true"
                                            :disabled="city_id == 0 ? false : true"
                                        >
                                        </v-select>

    <!--                                    <div class="input-group">-->
    <!--                                        <select class="form-control selectpicker"  v-model="branch.city" data-live-search="true" :disabled="city_id == 0 ? false : true">-->
    <!--                                            <option value="0">&#45;&#45; {{trans.get('keys.tinh_thanh')}} * &#45;&#45;</option>-->
    <!--                                            <option v-for="city in data.city" :value="city.id">{{city.name}}</option>-->
    <!--                                        </select>-->
    <!--                                    </div>-->

                                        <label v-if="!branch.city" class="text-danger city_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>


                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="wrap_search_box" @click="listDataSearchBoxUser()">
                                                <div class="btn_search_box">
                                                    <span>{{trans.get('keys.chon_quan_ly')}}</span>
                                                </div>
                                                <div class="content_search_box">
                                                    <input @input="listDataSearchBoxUser()" type="text" v-model="search_box_user" class="form-control search_box">
                                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                                    <ul>
                                                        <li @click="selectSearchBoxUser(0)" >{{trans.get('keys.chon_quan_ly')}}</li>
                                                        <li @click="selectSearchBoxUser(item.user_id)" v-for="item in data_search_box_user" :data-value="item.user_id">{{item.fullname}}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" id="branch_address" class="form-control form-control-line" :placeholder="trans.get('keys.dia_diem')" v-model.address="branch.address">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary btn-sm" @click="createBranch()">{{trans.get('keys.them_dai_ly')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" v-if="city_id == 0">
                    <div class="card-body">
                        <div class="listData">
                            <h5 class="mb-20">{{trans.get('keys.danh_sach_dai_ly')}}</h5>
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="datadataTables_wrapperTables_length" style="display: inline-block;">
                                        <label>{{trans.get('keys.hien_thi')}}
                                            <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm" @change="getBranchData(1)">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </label>
                                    </div>

    <!--                                <div v-if="!is_user_market" class="fillterConfirm" style="display: inline-block;">-->
    <!--                                    <label>-->
    <!--                                        <select v-model="districtSearch"-->
    <!--                                                class="custom-select custom-select-sm form-control form-control-sm"-->
    <!--                                                data-live-search="true"-->
    <!--                                                @change="getCity()">-->
    <!--                                            <option value="">&#45;&#45; {{trans.get('keys.khu_vuc')}} &#45;&#45;</option>-->
    <!--                                            <option value="MB" >{{trans.get('keys.mien_bac')}}</option>-->
    <!--                                            <option value="MT" >{{trans.get('keys.mien_trung')}}</option>-->
    <!--                                            <option value="MN" >{{trans.get('keys.mien_nam')}}</option>-->
    <!--                                        </select>-->
    <!--                                    </label>-->
    <!--                                </div>-->

                                    <div v-if="!is_user_market" class="fillterConfirm col-sm-4" style="display: inline-block;">
                                        <v-select
                                            @input="getCity()"
                                            :options="departmentFilterSelectOptions"
                                            :reduce="departmentFilterSelectOption => departmentFilterSelectOption.id"
                                            :placeholder="this.trans.get('keys.chi_nhanh')"
                                            :filter-by="myFilterBy"
                                            v-model="departmentSearch">
                                        </v-select>
                                    </div>

                                    <div v-if="!is_user_market" class="fillterConfirm col-sm-4" style="display: inline-block;">

                                        <v-select
                                            @input="getBranchData(1)"
                                            :options="cityFilterSelectOptions"
                                            :reduce="cityFilterSelectOption => cityFilterSelectOption.id"
                                            :placeholder="this.trans.get('keys.tinh_thanh')"
                                            :filter-by="myFilterBy"
                                            v-model="citySearch">
                                        </v-select>

    <!--                                    <label>-->
    <!--                                        <select v-model="citySearch" class="custom-select custom-select-sm form-control form-control-sm selectpicker" -->
    <!--                                                data-live-search="true" -->
    <!--                                                @change="getBranchData(1)">-->
    <!--                                            <option value="">&#45;&#45; {{trans.get('keys.tinh_thanh')}} &#45;&#45;</option>-->
    <!--                                            <option value="all">{{trans.get('keys.chua_them_tinh_thanh')}}</option>-->
    <!--                                            <option v-for="cityBranch in cityBranchs" :value="cityBranch.id">{{cityBranch.name}}</option>-->
    <!--                                        </select>-->
    <!--                                    </label>-->

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
                                          <router-link :title="trans.get('keys.xem_thong_tin_quan_ly')"
                                                       :to="{ name: 'EditUserById', path: 'system/user/edit/:user_id', params: { user_id: branch.user_id }, query: { type: 'system' }}">
                                            <span class="text-underline">{{ branch.user_name }}</span>
                                          </router-link>
<!--                                            <a :href="trans.get('keys.language')+'/system/user/edit/'+branch.user_id" :title="trans.get('keys.xem_thong_tin_quan_ly')">-->
<!--                                                <span class="text-underline">{{ branch.user_name }}</span>-->
<!--                                            </a>-->
                                        </td>
                                        <td class=" mobile_hide">{{ branch.address }}</td>
                                        <td class=" mobile_hide">
                                          <router-link :title="trans.get('keys.xem_danh_sach_diem_ban')"
                                                       :to="{ name: 'SaleroomIndexByBranch', params: { branch_id: branch.id } }">
                                            {{ branch.saleroom_count }} (<span class="text-underline">{{trans.get('keys.diem_ban')}}</span>)
                                          </router-link>
                                        </td>
                                        <td class=" mobile_hide">
                                          <router-link :title="trans.get('keys.xem_danh_sach_nhan_vien')"
                                                       :to="{ name: 'ListUserByBranch', params: { branch_id: branch.id } }">
                                            {{ branch.user_count }} (<span class="text-underline">{{trans.get('keys.nhan_vien')}}</span>)
                                          </router-link>
                                        </td>
                                        <td>
                                            <router-link :title="trans.get('keys.sua')"
                                               class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                                         :to="{ name: 'EditBranch',
                                                         params: { branch_id: branch.id },
                                                         query: { city_id: city_id}
                                            }">
                                              <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                            </router-link>

                                            <!--<a :title="trans.get('keys.them_diem_ban_cho_dai_ly')"
                                               class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                               :href="trans.get('keys.language')+'/system/organize/branch/add_saleroom/'+branch.id"
                                            ><span class="btn-icon-wrap"><i
                                                class="fal fa-arrow-alt-right"></i></span></a>


                                            <a :title="trans.get('keys.them_nhan_vien_vao_dai_ly')"
                                               class="btn btn-sm btn-icon btn-icon-circle btn-info btn-icon-style-2"
                                               :href="trans.get('keys.language')+'/system/organize/branch/add_user/'+branch.id"
                                            ><span class="btn-icon-wrap"><i
                                                class="fal fa-arrow-alt-right"></i></span></a>-->

                                            <a :title="trans.get('keys.xoa')"
                                               class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"
                                               href="javascript(0)"
                                               @click.prevent="deletePost('/system/organize/branch/delete/'+branch.id)"
                                            ><span class="btn-icon-wrap"><i
                                                    class="fal fa-trash"></i></span></a>

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
                                <div :style="posts.length == 0 ? 'display:none;' : 'display:block;'">
                                <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages" :classes=$pagination.classes :labels=$pagination.labels></v-pagination>
                                </div>
                            </div>
                            <!--<a v-if="is_user_market" href="/system/view_user_market" class="btn btn-sm btn-primary mt-3">Tất cả nhân viên</a>-->
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
    //import vSelect from 'vue-select'
    //import 'vue-select/dist/vue-select.css';

    export default {
        //components: {vPagination, vSelect},
        props: ['code','is_user_market','city_id'],
        data() {
            return {
                branch: {
                    name: '',
                    code: '',
                    user_id: 0,
                    //description:'',
                    address: '',
                    city: this.city_id,
                    department: '',
                },
                data:[],
                posts: {},
                keyword: this.code,
                current: 1,
                totalPages: 0,
                district:'',
                citys:[],
                citySearch:'',
                districtSearch:'',
                departmentSearch: '',
                row:10,
                cityBranchs:[],
                data_search_box_user:[],
                search_box_user:'',
                departmentSelectOptions: [],
                citySelectOptions: [],
                cityFilterSelectOptions: [],
                departmentFilterSelectOptions: []
            }
        },
        methods: {
            inputClearMessage(messClass){
                $('.'+messClass).hide();
            },
            selectSearchBoxUser(input_search_box_id){
                this.branch.user_id = input_search_box_id;
            },
            listDataSearchBoxUser(){
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/branch/data_search_box_user',{
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
            getCityAllBranch() {
                axios.post('/system/organize/branch/get_city_all_branch')
                    .then(response => {

                        //this.cityBranchs = response.data;

                        let additionalCities = [];
                        response.data.forEach(function(cityItem) {
                            let newCity = {
                                label: cityItem.name,
                                id: cityItem.id
                            };
                            additionalCities.push(newCity);
                        });
                        this.citySelectOptions = additionalCities;
                        this.cityFilterSelectOptions = additionalCities;

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
                    citySearch: this.citySearch,
                    districtSearch: this.districtSearch,
                    departmentSearch: this.departmentSearch
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
            createBranch() {
                if(!this.branch.name){
                    $('.name_required').show();
                    return;
                }
                if(!this.branch.code){
                    $('.code_required').show();
                    return;
                }
                if(!this.branch.city){
                    $('.city_required').show();
                    return;
                }
                    axios.post('/system/organize/branch/create',{
                    name:this.branch.name,
                    code:this.branch.code,
                    user_id:this.branch.user_id,
                    //description:this.branch.description,
                    city:this.branch.city,
                    address:this.branch.address,
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#branch_'+response.data.key).addClass('error');
                        }else{
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status == 'success'){
                                if(this.city_id != 0){
                                    this.$router.push({ name: 'BranchIndexByCity', params: {city: this.city_id} });
                                }
                                this.branch.code = '';
                                this.branch.name = '';
                                this.branch.address = '';
                                this.branch.user_id = 0;
                                this.branch.city = 0;
                                this.$nextTick(function () {
                                    $('.selectpicker').selectpicker('refresh');
                                });
                                $('.btn_search_box span').html(this.trans.get('keys.chon_quan_ly'));
                                $('.form-control').removeClass('error');
                            }
                        }
                    })
                    .catch(error => {
                        roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    })
            },
            onPageChange() {
                this.getBranchData();
            },
            listData(){

                let linkCityData = '/system/organize/city/data'; //All city
                this.branch.city = '';
                this.citySelectOptions = [];

                if (this.branch.department && this.branch.department.length !== 0) {
                    linkCityData = '/system/organize/branch/get_city_by_department';
                    axios.post(linkCityData, {
                        department: this.branch.department
                    })
                        .then(response => {
                            //this.data = response.data;
                            let additionalCities = [];
                            response.data.forEach(function(cityItem) {
                                let newCity = {
                                    label: cityItem.name,
                                    id: cityItem.id
                                };
                                additionalCities.push(newCity);
                            });
                            this.citySelectOptions = additionalCities;

                            this.$nextTick(function(){ $('.selectpicker').selectpicker('refresh'); });
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
                else {
                    axios.get(linkCityData)
                        .then(response => {
                            //this.data = response.data;
                            let additionalCities = this.citySelectOptions;
                            response.data.city.forEach(function(cityItem) {
                                let newCity = {
                                    label: cityItem.name,
                                    id: cityItem.id
                                };
                                additionalCities.push(newCity);
                            });
                            this.citySelectOptions = additionalCities;

                            this.$nextTick(function () {
                                $('.selectpicker').selectpicker('refresh');
                            });
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        })
                }

            },
            deletePost(url) {
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.dai_ly_ban_muon_xoa_co_du_lieu_tinh_thanh_diem_ban_di_kem_ban_co_xa_nhan_muon_xoa_dai_ly_nay_khong'),
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
            getCity(){

                this.citySearch = '';
                this.cityFilterSelectOptions = [];

                let linkGetCityData = '/system/organize/branch/get_city_by_district';
                let triggerCall = false;
                if (this.districtSearch.length > 0) {
                    triggerCall = true;
                }
                if (this.departmentSearch && this.departmentSearch.toString().length > 0) {
                    linkGetCityData = '/system/organize/branch/get_city_by_department';
                    triggerCall = true;
                }

                if (triggerCall) { //
                    axios.post(linkGetCityData, { // /report/get_city_by_district
                        //district: this.districtSearch,
                        department: this.departmentSearch
                    })
                        .then(response => {

                            //this.cityBranchs = response.data;

                            let additionalCities = [];
                            response.data.forEach(function(cityItem) {
                                let newCity = {
                                    label: cityItem.name,
                                    id: cityItem.id
                                };
                                additionalCities.push(newCity);
                            });

                            this.cityFilterSelectOptions = additionalCities;

                            this.$nextTick(function(){ $('.selectpicker').selectpicker('refresh'); });
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        });
                } else {
                    this.getCityAllBranch();
                }
                this.getBranchData();
            },
            getAllDepartment() {
                axios.post('/report/get_district')
                    .then(response => {

                        //this.districts = response.data;

                        let additionalDepartments = [];
                        response.data.forEach(function(departmentItem) {
                            let newDepartment = {
                                label: departmentItem.name,
                                id: departmentItem.id
                            };
                            additionalDepartments.push(newDepartment);
                        });
                        this.departmentSelectOptions = additionalDepartments;
                        this.departmentFilterSelectOptions = additionalDepartments;

                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            myFilterBy: (option, label, search) => {
                if (!label) {
                    label = '';
                }
                let new_search = convertUtf8(search);
                let new_label = convertUtf8(label);
                //return this.filterBy(option, new_label, new_search); //can not call components function here
                return (new_label || '').toLowerCase().indexOf(new_search) > -1; // "" not working
            },
            setDisabledDefaultSelection() {
                if (this.city_id) {
                    //k can thiet
                }
            }
        },
        mounted() {
            this.getAllDepartment();
            this.getCityAllBranch();
            this.setDisabledDefaultSelection();
            //this.listDataSearchBoxUser();
            //this.getBranchData();
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
