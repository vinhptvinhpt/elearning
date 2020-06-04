<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.diem_ban') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <div class="accordion" id="accordion_1">
        <div class="card not_overflow">
          <div class="card-header d-flex justify-content-between">
            <a :class="(branch_id != 0 ? '' : 'collapsed')" role="button" data-toggle="collapse" href="#collapse_1" aria-expanded="true"><i
              class="fal fa-plus mr-3"></i>{{trans.get('keys.them_moi_diem_ban')}}</a>
          </div>
          <div id="collapse_1" :class="(branch_id != 0 ? 'collapse show' : 'collapse')" data-parent="#accordion_1" role="tabpanel">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="text" id="saleroom_name" class="form-control form-control-line" :placeholder="trans.get('keys.ten_diem_ban')+' *'"
                             required v-model.name="saleroom.name">
                    </div>
                    <label v-if="!saleroom.name" class="text-danger name_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="text" id="saleroom_code" class="form-control form-control-line" :placeholder="trans.get('keys.ma')+' *'"
                             required v-model.code="saleroom.code"
                             @input="inputClearMessage('code_error')">
                    </div>
                    <em>{{trans.get('keys.gom_chu_cai_khong_dau_chu_so_ky_tu_dac_biet_-_/_.')}}</em>
                    <label v-if="!saleroom.code" class="text-danger code_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                    <label v-if="saleroom.code" class="text-danger code_error hide">{{trans.get('keys.ma_diem_ban_da_ton_tai')}}</label>
                  </div>
                </div>
              </div>
              <div class="row">

                <!--Select region-->
                <div class="col-sm-6" v-if="!is_user_market && branch_id == 0">
                  <div class="form-group">

                    <!--                                    <div class="input-group">-->
                    <!--                                        <select class="form-control" v-model="district"-->
                    <!--                                                @change="listDataSearchBoxCity()" data-live-search="true">-->
                    <!--                                            <option value="">&#45;&#45; {{trans.get('keys.chon_khu_vuc')}} &#45;&#45;</option>-->
                    <!--                                            <option value="MB" >{{trans.get('keys.mien_bac')}}</option>-->
                    <!--                                            <option value="MT" >{{trans.get('keys.mien_trung')}}</option>-->
                    <!--                                            <option value="MN" >{{trans.get('keys.mien_nam')}}</option>-->
                    <!--                                        </select>-->
                    <!--                                    </div>-->

                    <v-select
                      @input="listDataSearchBoxCity()"
                      :options="departmentSelectOptions"
                      :reduce="departmentSelectOption => departmentSelectOption.id"
                      :placeholder="this.trans.get('keys.chon_chi_nhanh')"
                      :filter-by="myFilterBy"
                      v-model="department"
                      data-live-search="true"
                    >
                    </v-select>

                  </div>
                </div>

                <!--Select City-->
                <div class="col-sm-6" v-if="!is_user_market && branch_id == 0">
                  <div class="form-group">
                    <!--                                    <div class="input-group">-->
                    <!--                                        <select class="form-control selectpicker" v-model="city"-->
                    <!--                                                @change="listDataSearchBox()" data-live-search="true">-->
                    <!--                                            <option value="">{{trans.get('keys.chon_tinh_thanh')}}</option>-->
                    <!--                                            <option v-for="city in data_form_city" :value="city.id">-->
                    <!--                                                {{city.name}}-->
                    <!--                                            </option>-->
                    <!--                                        </select>-->
                    <!--                                    </div>-->

                    <v-select
                      @input="listDataSearchBox()"
                      :options="citySelectOptions"
                      :reduce="citySelectOption => citySelectOption.id"
                      :placeholder="this.trans.get('keys.chon_tinh_thanh')"
                      :filter-by="myFilterBy"
                      v-model="city"
                      data-live-search="true"
                    >
                    </v-select>

                  </div>
                </div>

                <!--Select Branch -->
                <div class="col-sm-6">
                  <div class="form-group">

                    <!--                                    <div class="input-group">-->
                    <!--                                        <div class="wrap_search_box" @click="listDataSearchBox()">-->
                    <!--                                            <div :class="branch_id == 0 ? 'btn_search_box search_branch' : 'btn_search_box search_branch disabled'">-->
                    <!--                                                <span class="select-default-branch-span" >{{branch_name}}</span>-->
                    <!--                                            </div>-->
                    <!--                                            <div class="content_search_box">-->
                    <!--                                                <input @input="listDataSearchBox()" type="text" v-model="search_box"-->
                    <!--                                                       class="form-control search_box">-->
                    <!--                                                <i class="fa fa-spinner" aria-hidden="true"></i>-->
                    <!--                                                <ul>-->
                    <!--                                                    <li @click="selectSearchBox(0)">{{trans.get('keys.chon_dai_ly')}}</li>-->
                    <!--                                                    <li @click="selectSearchBox(item.id)"-->
                    <!--                                                        v-for="item in data_search_box" :data-value="item.id">-->
                    <!--                                                        {{item.name}} ( {{ item.code }} )-->
                    <!--                                                    </li>-->
                    <!--                                                </ul>-->
                    <!--                                            </div>-->
                    <!--                                        </div>-->
                    <!--                                    </div>-->

                    <v-select
                      :options="branchSelectOptions"
                      :reduce="branchSelectOption => branchSelectOption.id"
                      :placeholder="this.trans.get('keys.chon_dai_ly') + ' *'"
                      :filter-by="myFilterBy"
                      v-model="saleroom.branch"
                      data-live-search="true"
                    >
                    </v-select>

                    <label v-if="!saleroom.branch || saleroom.branch === 0" class="text-danger branch_required hide">{{trans.get('keys.truong_bat_buoc_phai_nhap')}}</label>
                  </div>
                </div>

                <!--Select User -->
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="wrap_search_box" @click="listDataSearchBoxUser()">
                        <div class="btn_search_box search_user">
                          <span>{{trans.get('keys.chon_quan_ly')}}</span>
                        </div>
                        <div class="content_search_box">
                          <input @input="listDataSearchBoxUser()" type="text"
                                 v-model="search_box_user" class="form-control search_box">
                          <i class="fa fa-spinner" aria-hidden="true"></i>
                          <ul>
                            <li @click="selectSearchBoxUser(0)">{{trans.get('keys.chon_quan_ly')}}</li>
                            <li @click="selectSearchBoxUser(item.user_id)"
                                v-for="item in data_search_box_user" :data-value="item.user_id">
                              {{item.fullname}}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div :class="!is_user_market ? 'col-sm-6' : 'col-12'">
                  <div class="form-group">
                    <div class="input-group">
                      <input id="saleroom_address" type="text" class="form-control form-control-line" :placeholder="trans.get('keys.dia_diem')"
                             v-model.address="saleroom.address">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary btn-sm" @click="createSaleRoom()">
                      {{trans.get('keys.them_diem_ban')}}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card" v-if="branch_id == 0">
          <div class="card-body">
            <div class="listData">
              <div class="row">
                <div class="col-8">
                  <h5 class="mb-20">{{trans.get('keys.danh_sach_diem_ban')}}</h5>
                </div>
                <!--Search box-->
                <div class="col-4">
                  <form v-on:submit.prevent="getSaleRoomData(1)">
                    <div class="d-flex flex-row form-group">
                      <input v-model="keyword" type="text"
                             class="form-control search_text" :placeholder="trans.get('keys.ten_ma_diem_ban')+' ...'">
                      <button type="button" class="btn btn-primary btn-sm btn_fillter"
                              @click="getSaleRoomData(1)">
                        {{trans.get('keys.tim')}}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
              <!--Filter -->
              <div class="row">
                <div class="col-12 dataTables_wrapper">
                  <!--Page size-->
                  <div class="dataTables_length" style="display: inline-block;">
                    <label>{{trans.get('keys.hien_thi')}}
                      <select v-model="row" class="custom-select custom-select-sm form-control form-control-sm"  @change="getSaleRoomData(1)">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </label>
                  </div>

                  <!--Show for admin only-->
                  <!--Filter district -->
                  <div class="fillterConfirm col-sm-3" style="display: inline-block;" v-if="!is_user_market">

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

                    <v-select
                      @input="getCity()"
                      :options="departmentFilterSelectOptions"
                      :reduce="departmentFilterSelectOption => departmentFilterSelectOption.id"
                      :placeholder="this.trans.get('keys.chi_nhanh')"
                      :filter-by="myFilterBy"
                      v-model="departmentSearch">
                    </v-select>

                  </div>
                  <!--Filter city -->
                  <div class="fillterConfirm col-sm-3" style="display: inline-block;" v-if="!is_user_market">


                    <!--                                    <label>-->
                    <!--                                        <select class="custom-select custom-select-sm form-control form-control-sm selectpicker"-->
                    <!--                                                v-model="citySearch" @change="getSaleRoomData(1,'','city')"-->
                    <!--                                                data-live-search="true">-->
                    <!--                                            <option value="">{{trans.get('keys.tinh_thanh')}}</option>-->
                    <!--                                            <option v-for="city in data_filter_city" :value="city.id">-->
                    <!--                                                {{city.name}}-->
                    <!--                                            </option>-->
                    <!--                                        </select>-->
                    <!--                                    </label>-->

                    <v-select
                      @input="listDataSearchBox('filter')"
                      :options="cityFilterSelectOptions"
                      :reduce="cityFilterSelectOption => cityFilterSelectOption.id"
                      :placeholder="this.trans.get('keys.tinh_thanh')"
                      :filter-by="myFilterBy"
                      v-model="citySearch">
                    </v-select>

                  </div>
                  <!--Filter branch -->
                  <!--Only show when select city? v-if="citySearch" -->
                  <div class="fillterConfirm col-sm-3" style="display: inline-block;" v-if="!is_user_market">

                    <!--                                    <div class="wrap_search_box" @click="listDataSearchBoxBranch()">-->
                    <!--                                        <div class="btn_search_box" style="height:33px;width:150px;">-->
                    <!--                                            <span class="branch_search_span">{{trans.get('keys.chon_dai_ly')}}</span>-->
                    <!--                                        </div>-->
                    <!--                                        <div class="content_search_box">-->
                    <!--                                            <input @input="listDataSearchBoxBranch()" type="text" v-model="branchSearch"-->
                    <!--                                                   class="form-control search_box">-->
                    <!--                                            <i class="fa fa-spinner" aria-hidden="true"></i>-->
                    <!--                                            <ul>-->
                    <!--                                                <li @click="getSaleRoomData(1,0)">{{trans.get('keys.chon_dai_ly')}}</li>-->
                    <!--                                                <li @click="getSaleRoomData(1,'all')">{{trans.get('keys.chua_them_dai_ly')}}</li>-->
                    <!--                                                <li @click="getSaleRoomData(1,item.id)" v-for="item in branchByCity">-->
                    <!--                                                    {{item.name}}-->
                    <!--                                                </li>-->
                    <!--                                            </ul>-->
                    <!--                                        </div>-->
                    <!--                                    </div>-->

                    <v-select
                      @input="getSaleRoomData(1)"
                      :options="branchFilterSelectOptions"
                      :reduce="branchFilterSelectOption => branchFilterSelectOption.id"
                      :placeholder="this.trans.get('keys.dai_ly')"
                      :filter-by="myFilterBy"
                      v-model="branchSearch">
                    </v-select>

                  </div>

                  <!--Show for manage market employee only-->
                  <!-- getSaleRoomData(1, branchFillter) -->
                  <div class="fillterConfirm" style="display: inline-block;" v-if="is_user_market">
                    <label>
                      <select class="custom-select custom-select-sm form-control form-control-sm selectpicker"
                              v-model="branchFillter"
                              @change="getSaleRoomData(1)"
                              data-live-search="true">
                        <option value="">{{trans.get('keys.chon_dai_ly')}}</option>
                        <option v-for="bf in branchFillterList" :value="bf.id">
                          {{bf.name}}
                        </option>
                      </select>
                    </label>
                  </div>
                </div>
              </div>
              <!--Data table-->
              <div class="table-responsive">
                <table class="table_res">
                  <thead>
                  <tr>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ma')}}</th>
                    <th>{{trans.get('keys.ten_diem_ban')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dai_ly')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-if="posts.length === 0">
                    <td colspan="9">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                  </tr>
                  <tr v-else v-for="(room,index) in posts">
                    <td>{{ (current-1)*row+(index+1) }}</td>
                    <td>{{ room.code }}</td>
                    <td>{{ room.name }}</td>
                    <td class=" mobile_hide">
                      <router-link :title="trans.get('keys.xem_thong_tin_quan_ly')"
                                   :to="{ name: 'EditUserById', params: { user_id: room.user_id } }">
                        <span class="text-underline">{{ room.user_name }}</span>
                      </router-link>
                    </td>
                    <td class=" mobile_hide">
                      <router-link :title="trans.get('keys.xem_thong_tin_dai_ly')"
                                   :to="{ name: 'BranchIndex', query: { code: room.branch_code } }">
                        <span class="text-underline">{{ room.branch_name }}</span>
                      </router-link>
                    </td>
                    <td class=" mobile_hide">
                      {{ room.city_name }}
                    </td>
                    <td class=" mobile_hide">{{ room.address }}</td>
                    <td class=" mobile_hide">
                      <router-link :title="trans.get('keys.xem_danh_sach_nhan_vien')"
                                   :to="{
                                        name: 'ListUserBySaleroom',
                                        params: { saleroom_id: room.id },
                                      }">
                        {{ room.usercount }} (<span class="text-underline">{{trans.get('keys.nhan_vien')}}</span>)
                      </router-link>
                    </td>
                    <td>

                      <router-link :title="trans.get('keys.sua')"
                                   class="btn btn-sm btn-icon btn-icon-circle btn-primary btn-icon-style-2"
                                   :to="{ name: 'EditSaleroom', params: { saleroom_id: room.id }}">
                        <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                      </router-link>

                      <!--<a :title="trans.get('keys.them_nhan_vien_vao_diem_ban')"
                         class="btn btn-sm btn-icon btn-icon-circle btn-info btn-icon-style-2"
                         :href="trans.get('keys.language')+'/system/organize/saleroom/add_user/'+room.id">
                          <span class="btn-icon-wrap"><i class="fal fa-arrow-alt-right"></i></span></a>-->

                      <a :title="trans.get('keys.xoa')"
                         class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"
                         href="javascript(0)"
                         @click.prevent="deletePost('/system/organize/saleroom/delete/'+room.id)">
                        <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></a>

                    </td>
                  </tr>
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{trans.get('keys.stt')}}</th>
                    <th>{{trans.get('keys.ma')}}</th>
                    <th>{{trans.get('keys.ten_diem_ban')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.quan_ly')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dai_ly')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.tinh_thanh')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.dia_diem')}}</th>
                    <th class=" mobile_hide">{{trans.get('keys.nhan_vien')}}</th>
                    <th>{{trans.get('keys.hanh_dong')}}</th>
                  </tr>
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
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    //import vSelect from 'vue-select'
    //import 'vue-select/dist/vue-select.css';

    export default {
        props: ['code', 'branch_id'],
        //components: {vPagination, vSelect},
        data() {
            return {
                saleroom: {
                    name: '',
                    code: '',
                    branch: 0,
                    description:'',
                    address: '',
                    user_id: 0,
                },
                data: [],
                posts: [],
                keyword: this.code,
                current: 1,
                totalPages: 0,
                branchSearch: '',
                department: '',
                departmentSearch: '',
                district: '',
                districtSearch: '',
                row: 10,
                branchSaleRooms: [],
                data_search_box: [],
                search_box: '',
                data_form_city: [],
                data_search_box_city: [],
                data_filter_city: [],
                city: '',
                citySearch: '',
                data_search_box_user: [],
                search_box_user: '',
                branchByCity: [],
                branchFillterList:[],
                branchFillter: '',
                branch_name:this.trans.get('keys.chon_dai_ly') +' *',
                branch_input_search: 0,
                departmentSelectOptions: [],
                departmentFilterSelectOptions: [],
                citySelectOptions: [],
                cityFilterSelectOptions: [],
                branchSelectOptions: [],
                branchFilterSelectOptions: [],
                is_user_market: '',
            }
        },
        methods: {
            getBranchName(){
                if(this.branch_id != 0){
                    axios.post('/system/organize/branch/get_branch_name', {
                        branch_id: this.branch_id,
                    })
                        .then(response => {
                            this.branch_name = response.data;
                        })
                        .catch(error => {
                            this.branch_name = this.trans.get('keys.chon_dai_ly');
                        })
                }

            },
            inputClearMessage(messClass) {
                $('.' + messClass).hide();
            },
            selectSearchBoxUser(input_search_box_id) {
                this.saleroom.user_id = input_search_box_id;
            },
            listDataSearchBoxUser() {
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/saleroom/data_search_box_user', {
                    keyword: this.search_box_user,
                })
                    .then(response => {
                        this.data_search_box_user = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        console.log(error.response.data);
                    })
            },
            selectSearchBox(input_search_box_id) { //filter nguoc len city
                this.saleroom.branch = input_search_box_id;
                axios.post('/system/organize/saleroom/get_city_by_branch', {
                    input_search_box_id: input_search_box_id,
                })
                    .then(response => {
                        this.city = response.data.city_id;
                        this.$nextTick(function () {
                            $('.selectpicker').selectpicker('refresh')
                        });
                        // this.listDataSearchBoxCity();
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        console.log(error.response.data);
                    })
            },
            // fetch branch data
            listDataSearchBox(destination) {
                if(parseInt(this.branch_id) === 0) { //default no branch selected
                    // $('.select-default-branch-span').html(this.branch_name);
                    // $('.content_search_box ul li').removeClass('active');
                    // $('.content_search_box').addClass('loadding');

                    // reset current filter
                    this.branchSearch = '';
                    this.branch = '';
                    this.branchSelectOptions = [];
                    this.branchFilterSelectOptions = [];

                    axios.post('/system/organize/saleroom/data_branch_for_saleroom', { //axios.post('/system/organize/saleroom/data_search_box'
                        // keyword: this.search_box,
                        district: this.district,
                        city: this.city,
                        citySearch: this.citySearch,
                        department: this.department,
                        departmentSearch: this.departmentSearch,
                    })
                        .then(response => {

                            let additionalBranches = [];

                            response.data.forEach(function(cityItem) {
                                let newBranch = {
                                    label: cityItem.name,
                                    id: cityItem.id
                                };
                                additionalBranches.push(newBranch);
                            });

                            if(destination === "form") {
                                this.branchSelectOptions = additionalBranches;
                            } else if(destination === "filter") {
                                this.branchFilterSelectOptions = additionalBranches;
                                this.getSaleRoomData();
                            } else {
                                this.branchSelectOptions = additionalBranches;

                                let saleroomNoBranch = {
                                    label: this.trans.get('keys.chua_them_dai_ly'),
                                    id: 'all'
                                };
                                additionalBranches.unshift(saleroomNoBranch);
                                this.branchFilterSelectOptions = additionalBranches;
                            }

                            //this.data_search_box = response.data;
                            //$('.content_search_box').removeClass('loadding');

                        })
                        .catch(error => {
                            //$('.content_search_box').removeClass('loadding');
                            console.log(error);
                        })
                }


            },
            listDataSearchBoxBranch() {
                $('.content_search_box').addClass('loadding');
                axios.post('/system/organize/saleroom/data_search_box', {
                    keyword: this.branchSearch,
                    city: this.citySearch,
                })
                    .then(response => {
                        this.branchByCity = response.data;
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                        console.log(error.response.data);
                    })
            },
            //fetch city data for form
            listDataSearchBoxCity() {

                let linkCityData = '/system/organize/city/data'; //All city
                this.city = '';
                this.citySelectOptions = [];

                if (this.department && this.department.length !== 0) {
                    linkCityData = '/system/organize/branch/get_city_by_department';
                    axios.post(linkCityData, {
                        department: this.department
                    })
                        .then(response => {
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

                    //fetch branch data
                    this.listDataSearchBox('form');
                }
                else {
                    axios.get(linkCityData)
                        .then(response => {
                            let additionalCities = [];
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

                    //fetch branch data
                    this.listDataSearchBox('all');
                }

                // if(!this.is_user_market){
                //     axios.post('/system/organize/saleroom/data_search_box_city', {
                //         keyword: this.district,
                //     })
                //         .then(response => {
                //             this.data_form_city = response.data;
                //             this.$nextTick(function () {
                //                 $('.selectpicker').selectpicker('refresh')
                //             });
                //         })
                //         .catch(error => {
                //             console.log(error.response.data);
                //         });
                // }
            },
            //fetch city data for filter
            getCity() {
                this.citySearch = '';
                this.cityFilterSelectOptions = [];

                let linkGetCityData = '';
                let triggerCall = false;

                if (this.districtSearch.length > 0) {
                    linkGetCityData = '/system/organize/branch/get_city_by_district'
                    triggerCall = true;
                }
                if (this.departmentSearch && this.departmentSearch.toString().length > 0) {
                    linkGetCityData = '/system/organize/branch/get_city_by_department';
                    triggerCall = true;
                }

                if (triggerCall) {
                    axios.post(linkGetCityData, {
                        //district: this.districtSearch,
                        department: this.departmentSearch
                    })
                        .then(response => {

                            //this.data_filter_city = response.data;

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
                    this.listDataSearchBox('filter')
                } else {

                    this.getCityAllBranch();
                    this.listDataSearchBox('all')

                }
                this.getSaleRoomData(1);
            },
            listDataBranchFilter() {
                if(this.is_user_market){
                    axios.post('/system/organize/saleroom/branch_by_user_market')
                        .then(response => {
                            this.branchFillterList = response.data;
                            this.$nextTick(function () {
                                $('.selectpicker').selectpicker('refresh')
                            });
                        })
                        .catch(error => {
                            console.log(error.response.data);
                        })
                }
            },
            //fetch saleroom data
            //paged, branch_input_id, type_change
            getSaleRoomData(paged) {
                // if (type_change === 'city') {
                //     $('.branch_search_span').html(this.trans.get('keys.chon_dai_ly'));
                //     $('.content_search_box ul li').removeClass('active');
                // }
                // if(branch_input_id != null){
                //     this.branch_input_search = branch_input_id;
                // }

                if (this.branchFillter && this.branchFillter.toString().length > 0) {
                    this.branchSearch = this.branchFillter;
                }

                axios.post('/system/organize/saleroom/list_data', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    //branchSearch: this.branch_input_search,
                    branchSearch: this.branchSearch,
                    citySearch: this.citySearch,
                    districtSearch: this.districtSearch,
                    departmentSearch: this.departmentSearch
                })
                    .then(response => {
                        this.posts = response.data.data ? response.data.data.data : [];
                        this.current = response.data.pagination ? response.data.pagination.current_page : [];
                        this.totalPages = response.data.pagination ? response.data.pagination.total : [];
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            //fetch all branch
            getBranchAllSaleRoom() {
                axios.post('/system/organize/saleroom/get_branch_all_saleroom')
                    .then(response => {
                        this.branchSaleRooms = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            createSaleRoom() {
                if(this.branch_id != 0){
                    this.saleroom.branch = this.branch_id;
                }
                if (!this.saleroom.name) {
                    $('.name_required').show();
                    return;
                }
                if (!this.saleroom.code) {
                    $('.code_required').show();
                    return;
                }
                if (this.saleroom.branch == 0) {
                    $('.branch_required').show();
                    return;
                }
                axios.post('/system/organize/saleroom/create', {
                    name: this.saleroom.name,
                    code: this.saleroom.code,
                    branch: this.saleroom.branch,
                    address: this.saleroom.address,
                    user_id: this.saleroom.user_id,
                })
                    .then(response => {
                        if(response.data.key) {
                            roam_message('error',response.data.message);
                            $('.form-control').removeClass('error');
                            $('#saleroom_'+response.data.key).addClass('error');
                        }else{
                            roam_message(response.data.status,response.data.message);
                            if(response.data.status === 'success'){
                                if(this.branch_id != 0) {
                                    this.$router.push({ name: 'SaleroomIndexByBranch', params: {branch_id: this.branch_id} });
                                }
                                this.saleroom.name = '';
                                this.saleroom.code = '';
                                this.saleroom.address = '';
                                this.saleroom.branch = 0;
                                this.saleroom.user_id = 0;
                                this.$nextTick(function () {
                                    $('.selectpicker').selectpicker('refresh');
                                });
                                $('.btn_search_box span').html(this.trans.get('keys.chon_quan_ly'));
                                $('.search_branch span').html(this.trans.get('keys.chon_dai_ly'));
                                $('.form-control').removeClass('error');
                            }
                        }
                    })
                    .catch(error => {
                        roam_message('error',this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    })
            },
            onPageChange() {
                this.getSaleRoomData();
            },
            listDataField() {
                axios.get('/system/organize/saleroom/data')
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    })
            },
            deletePost(url) {
                swal({
                    title: this.trans.get('keys.thong_bao'),
                    text: this.trans.get('keys.du_lieu_diem_ban_ban_muon_xoa_co_du_lieu_dai_ly_nhan_vien_di_kem_ban_co_xac_nhan_muon_xoa_diem_ban_nay_khong'),
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
            getCityAllBranch(){
                axios.post('/system/organize/branch/get_city_all_branch')
                    .then(response => {

                        //this.data_filter_city = response.data;

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

                        // this.$nextTick(function () {
                        //     $('.selectpicker').selectpicker('refresh')
                        // });
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
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
            fetch() {
              axios.post('/bridge/fetch', {
                view: 'IndexSaleroom'
              })
              .then(response => {
                this.is_user_market = response.data.is_user_market;
              })
              .catch(error => {
                console.log(error);
              })
            },
        },
        mounted() {
            this.getAllDepartment();
            // this.getBranchAllSaleRoom();
            this.listDataSearchBoxCity();
            this.getCity();
            this.listDataSearchBox('all');
            //this.listDataSearchBoxUser();
            this.listDataBranchFilter();
            this.getBranchName();
            this.fetch();
        },
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
