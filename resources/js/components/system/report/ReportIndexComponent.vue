
<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.bao_cao') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <!--Filter block-->
      <div class="card">
        <div class="card-body">

          <!--Filter-->
          <div class="row">

            <!--Region-->
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label><strong>{{trans.get('keys.chi_nhanh')}}</strong></label>
                <v-select
                  @input="getCity()"
                  :options="departmentSelectOptions"
                  :reduce="departmentSelectOption => departmentSelectOption.id"
                  :placeholder="this.trans.get('keys.chon_chi_nhanh')"
                  :filter-by="myFilterBy"
                  v-model="department">
                </v-select>
              </div>
            </div>
            <!--                    <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label><strong>{{trans.get('keys.chi_nhanh')}}</strong></label>
                                        <div class="input-group">
                                            <select class="form-control" v-model="district" @change="getCity()">
            &lt;!&ndash;                                    <option value="">&#45;&#45; {{trans.get('keys.chon_khu_vuc')}} &#45;&#45;</option>&ndash;&gt;
                                                <option value="">&#45;&#45; {{trans.get('keys.chon_chi_nhanh')}} &#45;&#45;</option>
                                                <option v-for="district_item in districts" :value="district_item.id">{{district_item.name}}</option>
            &lt;!&ndash;                                    <option value="MB" >{{trans.get('keys.mien_bac')}}</option>&ndash;&gt;
            &lt;!&ndash;                                    <option value="MT" >{{trans.get('keys.mien_trung')}}</option>&ndash;&gt;
            &lt;!&ndash;                                    <option value="MN" >{{trans.get('keys.mien_nam')}}</option>&ndash;&gt;
                                            </select>
                                        </div>
                                    </div>
                                </div>-->
            <!--End Region-->

            <!--City-->
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label><strong>{{trans.get('keys.tinh_thanh')}}</strong></label>
                <v-select
                  @input="getBranch()"
                  :options="citySelectOptions"
                  :reduce="citySelectOption => citySelectOption.id"
                  :placeholder="this.trans.get('keys.chon_tinh_thanh')"
                  :filter-by="myFilterBy"
                  v-model="city">
                </v-select>
              </div>
            </div>

            <!-- <div class="col-md-3 col-sm-6">
                 <div class="form-group">
                     <label><strong>{{trans.get('keys.tinh_thanh')}}</strong></label>
                     <div class="input-group">
                         <select class="form-control" v-model="city" @change="getBranch()" data-live-search="true">
                             <option value="">&#45;&#45; {{trans.get('keys.chon_tinh_thanh')}} &#45;&#45;</option>
                             <option v-for="city_item in citys" :value="city_item.id">{{city_item.name}}</option>
                         </select>
                     </div>
                 </div>
             </div>-->
            <!--End City-->

            <!--Branch-->
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label><strong>{{trans.get('keys.dai_ly')}}</strong></label>
                <v-select
                  @input="getSaleroom()"
                  :options="branchSelectOptions"
                  :reduce="branchSelectOption => branchSelectOption.id"
                  :placeholder="this.trans.get('keys.chon_dai_ly')"
                  :filter-by="myFilterBy"
                  v-model="branch">
                </v-select>
              </div>
            </div>
            <!--<div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label><strong>{{trans.get('keys.dai_ly')}}</strong></label>
                    <div class="input-group">
                        <select class="form-control" v-model="branch" @change="getSaleroom()">
                            <option value="">&#45;&#45; {{trans.get('keys.chon_dai_ly')}} &#45;&#45;</option>
                            <option v-for="branch_item in branchs" :value="branch_item.branch.id" >{{branch_item.branch.name}}</option>
                        </select>
                    </div>
                </div>
            </div>-->
            <!--End Branch-->

            <!--Saleroom-->
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label><strong>{{trans.get('keys.diem_ban')}}</strong></label>
                <v-select
                  :options="saleroomSelectOptions"
                  :reduce="saleroomSelectOption => saleroomSelectOption.id"
                  :placeholder="this.trans.get('keys.chon_diem_ban')"
                  :filter-by="myFilterBy"
                  v-model="saleroom">
                </v-select>
              </div>
            </div>
            <!-- <div class="col-md-3 col-sm-6">
                 <div class="form-group">
                     <label><strong>{{trans.get('keys.diem_ban')}}</strong></label>
                     <div class="input-group">
                         <select class="form-control" v-model="saleroom">
                             <option value="">&#45;&#45; {{trans.get('keys.chon_diem_ban')}} &#45;&#45;</option>
                             <option v-for="saleroom_item in salerooms" :value="saleroom_item.sale_room_name.id" >{{saleroom_item.sale_room_name.name}}</option>
                         </select>
                     </div>
                 </div>
             </div>-->
            <!--End Saleroom-->

          </div>

          <div class="row">
            <div class="col-12">
              <button id="buttonReport" class="btn btn-primary btn-sm hasLoading" @click="showReport()">{{trans.get('keys.xem')}}<i class="fa fa-spinner"></i></button>
            </div>
          </div>

        </div>
      </div>
      <!--End Filter block-->

      <!--Content Report-->
      <div class="card" id="requestReport">
        <!--Report block 1 search by city / branch / saleroom -->
        <div class="card-body" id="requestSaleRoom">
          <h5 class="text-center mb-20">{{trans.get('keys.thong_ke_nhan_vien_ban_hang')}}</h5>

          <div class="text-right mb-20">
            <a style="color: #fff" class="btn btn-sm btn-icon btn-primary btn-icon-style-2" v-on:click="expandAll()" :title="trans.get('keys.xem_chi_tiet')" v-if="report_data.selected_level !== 'city'">
              <span class="btn-icon-wrap"><i class="fal fa-eye"></i></span>
            </a>
            <a style="color: #fff" class="btn btn-sm btn-icon btn-primary btn-icon-style-2" v-on:click="exportExcel(report_data)" :title="trans.get('keys.xuat_excel')">
              <span class="btn-icon-wrap"><i class="fal fa-file-excel-o"></i></span>
            </a>
          </div>

          <div class="table-responsive">
            <table class="">
              <thead>
              <tr>
                <th>STT</th>
                <th v-if="report_data.selected_level === 'city'">{{trans.get('keys.dai_ly')}}</th>
                <th v-else>{{trans.get('keys.diem_ban')}}</th>
                <th>{{trans.get('keys.chua_hoan_thanh_dao_tao')}}</th>
                <th>{{trans.get('keys.da_hoan_thanh_dao_tao')}}</th>
                <th>{{trans.get('keys.da_co_giay_chung_nhan')}}</th>
                <th>{{trans.get('keys.tong_so')}}</th>
              </tr>
              </thead>
              <tbody>
              <!--show parent if not choose saleroom-->

              <!--Not show branch student list if only city is selected-->
              <template v-if="report_data.selected_level === 'city'">
                <tr style="background: #e0e3e4;">
                  <td>
                    <strong>
                      <span v-if="report_data.selected_level === 'city'">{{trans.get('keys.tinh_tp')}} </span>
                      <span v-else>{{trans.get('keys.dai_ly')}} </span>
                    </strong>
                  </td>
                  <td>
                    <strong>{{report_data.name}}</strong>
                  </td>
                  <td>
                    <strong>{{report_data.user_incomplete_count}}</strong>
                    <span v-if="report_data.user_count > 0">({{ parseInt(report_data.user_incomplete_count*100/report_data.user_count) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong>{{report_data.user_completed_count}}</strong>
                    <span v-if="report_data.user_count > 0">({{ parseInt(report_data.user_completed_count*100/report_data.user_count) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong>{{report_data.user_confirm_count}}</strong>
                    <span v-if="report_data.user_count > 0">({{ parseInt(report_data.user_confirm_count*100/report_data.user_count) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td><strong>{{report_data.user_count}}</strong></td>
                </tr>
              </template>
              <!--Show branch student list if branch and/or saleroom is selected, click row to show/hide-->
              <template v-if="report_data.selected_level === 'branch'">
                <tr v-on:click="toggleRow('branch_student_list', report_data.branch_user_count)" style="background: #e0e3e4;">
                  <td>
                    <strong>
                      <span v-if="report_data.selected_level === 'city'">{{trans.get('keys.tinh_tp')}} </span>
                      <span v-else>{{trans.get('keys.dai_ly')}} </span>
                    </strong>
                  </td>
                  <td>
                    <strong>{{report_data.name}}</strong>
                  </td>
                  <td>
                    <strong>{{report_data.user_incomplete_count}}</strong>
                    <span v-if="report_data.user_count > 0">({{ parseInt(report_data.user_incomplete_count*100/report_data.user_count) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong>{{report_data.user_completed_count}}</strong>
                    <span v-if="report_data.user_count > 0">({{ parseInt(report_data.user_completed_count*100/report_data.user_count) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong>{{report_data.user_confirm_count}}</strong>
                    <span v-if="report_data.user_count > 0">({{ parseInt(report_data.user_confirm_count*100/report_data.user_count) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td><strong>{{report_data.user_count}}</strong></td>
                </tr>
                <tr :id="'branch_student_list'" class="hidden" :has-content="report_data.branch_user_count > 0">
                  <td></td>
                  <td></td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_incomplete in report_data.branch_users[report_data.id].user_incomplete">{{user_single_incomplete.user_name}}</p>
                  </td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_complete in report_data.branch_users[report_data.id].user_completed">{{user_single_complete.user_name}}</p>
                  </td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_confirm in report_data.branch_users[report_data.id].user_confirm">{{user_single_confirm.user_name}}</p>
                  </td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_all in report_data.branch_users[report_data.id].user">{{user_single_all.user_name}}</p>
                  </td>
                </tr>
              </template>

              <!--Not show student list if only city is selected-->
              <template v-if="report_data.selected_level === 'city'" v-for="(detail_data,index) in report_data.items">
                <tr>
                  <td>{{index+1}}</td>
                  <td>{{detail_data.name}}</td>
                  <td>
                    <strong>{{detail_data.user_incomplete.length}}</strong>
                    <span v-if="detail_data.user.length > 0" >({{ parseInt(detail_data.user_incomplete.length*100/detail_data.user.length) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong>{{detail_data.user_completed.length}}</strong>
                    <span v-if="detail_data.user.length > 0" >({{ parseInt(detail_data.user_completed.length*100/detail_data.user.length) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong>{{detail_data.user_confirm.length}}</strong>
                    <span v-if="detail_data.user.length > 0">({{ parseInt(detail_data.user_confirm.length*100/detail_data.user.length) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong>{{detail_data.user.length}}</strong>
                  </td>
                </tr>
              </template>
              <!--Show student list if branch and/or saleroom is selected, click row to show/hide-->
              <template v-if="report_data.selected_level === 'branch' || report_data.selected_level === 'saleroom'" v-for="(detail_data,index) in report_data.items">
                <tr v-on:click="toggleRow('student_list' + index, detail_data.user.length)">
                  <td>{{index+1}}</td>
                  <td>{{detail_data.name}}</td>
                  <td>
                    <strong style="color: blue">{{detail_data.user_incomplete.length}}</strong>
                    <span v-if="detail_data.user.length > 0" >({{ parseInt(detail_data.user_incomplete.length*100/detail_data.user.length) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong style="color: blue">{{detail_data.user_completed.length}}</strong>
                    <span v-if="detail_data.user.length > 0" >({{ parseInt(detail_data.user_completed.length*100/detail_data.user.length) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong style="color: blue">{{detail_data.user_confirm.length}}</strong>
                    <span v-if="detail_data.user.length > 0">({{ parseInt(detail_data.user_confirm.length*100/detail_data.user.length) }}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td>
                    <strong style="color: blue">{{detail_data.user.length}}</strong>
                  </td>
                </tr>
                <tr :id="'student_list' + index" class="hidden" :has-content="detail_data.user.length > 0">
                  <td></td>
                  <td></td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_incomplete in detail_data.user_incomplete">{{user_single_incomplete.user_name}}</p>
                  </td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_complete in detail_data.user_completed">{{user_single_complete.user_name}}</p>
                  </td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_confirm in detail_data.user_confirm">{{user_single_confirm.user_name}}</p>
                  </td>
                  <td style="vertical-align: top">
                    <p v-for="user_single_all in detail_data.user">{{user_single_all.user_name}}</p>
                  </td>
                </tr>
              </template>

              </tbody>
            </table>
          </div>
          <p v-if="report_data.selected_level === 'branch' || report_data.selected_level === 'saleroom'">{{trans.get('keys.chu_y_click_moi_hang_de_xem_danh_sach_nhan_vien')}}</p>
        </div>
        <!--Report block 2 search by region-->
        <div class="card-body" id="requestCity">
          <h5 class="text-center mb-20">{{trans.get('keys.thong_ke_nhan_vien_ban_hang')}}</h5>

          <div class="text-right mb-20">
            <a style="color: #fff" class="btn btn-sm btn-icon btn-primary btn-icon-style-2" v-on:click="exportExcel(data_request)" :title="trans.get('keys.xuat_excel')">
              <span class="btn-icon-wrap"><i class="fal fa-file-excel-o"></i></span>
            </a>
          </div>

          <div class="table-responsive">
            <table>
              <thead>
              <tr>
                <th>{{trans.get('keys.stt')}}</th>
                <th>{{trans.get('keys.tinh_tp')}}</th>
                <th>{{trans.get('keys.chua_hoan_thanh_dao_tao')}}</th>
                <th>{{trans.get('keys.da_hoan_thanh_dao_tao')}}</th>
                <th>{{trans.get('keys.da_co_giay_chung_nhan')}}</th>
                <th>{{trans.get('keys.tong_so')}}</th>
              </tr>
              </thead>
              <tbody>
              <!--Region-->
              <template v-for="region_item in data_request.region">
                <tr style="background: #e0e3e4;">
                  <td><strong>{{trans.get('keys.chi_nhanh')}}</strong></td>
                  <td><strong>{{region_item.name}}</strong></td>
                  <td v-if="region_item.user_count !== 0 && region_item.user_incomplete_count !== 0"><strong>{{region_item.user_incomplete_count}}</strong><span>({{parseInt((region_item.user_incomplete_count/region_item.user_count)*100)}}%)</span></td>
                  <td v-else><strong>0</strong>(0%)</td>
                  <td v-if="region_item.user_count !== 0 && region_item.user_completed_count !== 0"><strong>{{region_item.user_completed_count}}</strong><span>({{parseInt((region_item.user_completed_count/region_item.user_count)*100)}}%)</span></td>
                  <td v-else><strong>0</strong>(0%)</td>
                  <td v-if="region_item.user_count !== 0 && region_item.user_confirm_count !== 0"><strong>{{region_item.user_confirm_count}}</strong><span>({{parseInt((region_item.user_confirm_count/region_item.user_count)*100)}}%)</span></td>
                  <td v-else><strong>0</strong>(0%)</td>
                  <td><strong>{{region_item.user_count}}</strong></td>
                </tr>
                <tr v-for="(city_item,index) in region_item.cities">
                  <td>{{index+1}}</td>
                  <td>{{city_item.name}}</td>
                  <td><strong>{{city_item.user_incomplete.length}}</strong>
                    <span v-if="city_item.user.length > 0 && city_item.user_incomplete.length > 0">({{parseInt((city_item.user_incomplete.length/city_item.user.length)*100)}}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td><strong>{{city_item.user_completed.length}}</strong>
                    <span v-if="city_item.user.length > 0 && city_item.user_completed.length > 0">({{parseInt((city_item.user_completed.length/city_item.user.length)*100)}}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td><strong>{{city_item.user_confirm.length}}</strong>
                    <span v-if="city_item.user.length > 0 && city_item.user_confirm.length > 0">({{parseInt((city_item.user_confirm.length/city_item.user.length)*100)}}%)</span>
                    <span v-else>(0%)</span>
                  </td>
                  <td><strong>{{city_item.user.length}}</strong></td>
                </tr>
              </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--End Content Report-->
    </div>
  </div>

</template>

<script>
    //import vSelect from 'vue-select'
    //import 'vue-select/dist/vue-select.css';

    //https://vue-select.org/api/events.html#input
    //https://vue-select.org/guide/values.html#transforming-selections

    export default {
    	//components: {vSelect},
    	data() {
            return {
                district:'',
                department: '',
                city:'',
                branch:'',
                saleroom:'',
                districts:[],
                citys:[],
                branchs:[],
                salerooms:[],
                report_data:{
                    id: 0,
                    branch_users: [
                        {
                            id: 0,
                            name: "",
                            user: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                            user_completed: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                            user_incomplete: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                            user_confirm: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                        }
                    ],
                    items: [
                        {
                            id: 0,
                            name: "",
                            user: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                            user_completed: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                            user_incomplete: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                            user_confirm: [
                                {
                                    user_id: '',
                                    user_name: '',
                                }
                            ],
                        }
                    ],
                    name: "",
                    user_completed_count: 0,
                    user_incomplete_count: 0,
                    user_confirm_count: 0,
                    branch_user_count: 0,
                    user_count: 0,
                    selected_level: 'city' //branch city
                },
                data_request:{
                	region:[
                        {
                            code: '',
                            name: '',
                            cities: [
                                {
                                    id: 0,
                                    name: '',
                                    user: [],
                                    user_confirm: [],
                                    user_completed: [],
                                    user_incomplete: [],
                                }
                            ],
                            user_count: 0,
                            user_confirm_count: 0,
                            user_completed_count: 0,
                            user_incomplete_count: 0
                        }
                    ],
                    selected: '' // MB MT MN
                },
                departmentSelectOptions: [],
                citySelectOptions: [],
                branchSelectOptions: [],
                saleroomSelectOptions: []
            }
        },
        methods: {
            preloadData() {
                this.callReportRegion();
                $('#requestSaleRoom').hide();
            },
            callReportCity() {
                axios.post('/report/show_report_by_city', {
                    district: this.district,
                    city: this.city,
                    branch: this.branch,
                    saleroom:  this.saleroom
                })
                    .then(response => {
                        this.report_data = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            callReportRegion() {
                axios.post('/report/show_report_by_region', {
                    district: this.district
                })
                    .then(response => {
                        this.data_request = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            showReport(){
                if (this.city == null || this.city.length === 0) { //this.city.toString().length > 0
                    //k chon khu vuc hien thi tat ca khu vuc MB, MT, MN
                    //k chon khu vuc hien thi theo 1 khu vuc MB/MT/MN
                    this.callReportRegion();
                    $('#requestCity').show();
                    $('#requestSaleRoom').hide();
                } else {
                    this.callReportCity();
                    $('#requestSaleRoom').show();
                    $('#requestCity').hide();
                }
                $('#indexReport').hide();
                $('#requestReport').show();
        	},
            getDistrict(){
                this.city = '';
                this.branch = '';
                this.saleroom = '';

                this.departmentSelectOptions = []; //reset after search again
                this.citySelectOptions = []; //reset after search again
                this.branchSelectOptions = []; //reset after search again
                this.saleroomSelectOptions = []; //reset after search again

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

                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getCity(){
        		this.city = '';
        		this.branch = '';
        		this.saleroom = '';

                this.citySelectOptions = []; //reset after search again
                this.branchSelectOptions = []; //reset after search again
                this.saleroomSelectOptions = []; //reset after search again

                axios.post('/report/get_city_by_department', { //'/report/get_city_by_district'
                    //district: this.district,
                    department: this.department
                })
                    .then(response => {

                        //this.citys = response.data;

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
            },
            getBranch(){
        		this.branch = '';
        		this.saleroom = '';

                this.branchSelectOptions = []; //reset after search again
                this.saleroomSelectOptions = []; //reset after search again


                axios.post('/report/get_branch_by_city', {
                    city: this.city,
                })
                    .then(response => {

                        //this.branchs = response.data;

                        let additionalBranches = [];
                        response.data.forEach(function(branchItem) {
                            let newBranch = {
                                label: branchItem.branch.name,
                                id: branchItem.branch.id
                            };
                            additionalBranches.push(newBranch);
                        });
                        this.branchSelectOptions = additionalBranches;

                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getSaleroom(){
        		this.saleroom = '';
                this.saleroomSelectOptions = []; //reset after search again

                axios.post('/report/get_saleroom_by_branch', {
                    branch: this.branch,
                })
                    .then(response => {
                        //this.salerooms = response.data;
                        let additionalSalerooms = this.saleroomSelectOptions;
                        let existedSalerooms = {};
                        response.data.forEach(function(saleroomItem) {
                                let label = saleroomItem.sale_room_name.name;
                                let id = saleroomItem.sale_room_name.id;
                                let code = saleroomItem.sale_room_name.code;
                                if (!label) { //Show mã điểm bán nếu điểm bán không có tên
                                    label = "Mã " + code;
                                } else { //Thêm số thứ tự nếu trùng tên, nếu không sẽ chỉ search ra đối tượng đầu tiên
                                    if (label in existedSalerooms) {
                                        existedSalerooms[label] += 1;
                                        label =  label + " " + existedSalerooms[label].toString();
                                    } else {
                                        existedSalerooms[label] = 1;
                                    }
                                }

                                let newSaleroom = {
                                    label: label,
                                    //label: saleroomItem.sale_room_name.id + " " + saleroomItem.sale_room_name.name,
                                    id: id
                                };
                                additionalSalerooms.push(newSaleroom);
                        });
                        this.saleroomSelectOptions = additionalSalerooms;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            toggleRow(id, row_count) {
                if (row_count > 0) {
                    $( '#' + id ).toggle( "slow", function() {
                        // Animation complete.
                    });
                }
            },
            expandAll() {
                $(document).ready(function(){
                    $("[has-content=true]").toggle( "slow", function() {
                        // Animation complete.
                    });
                });
            },
            exportExcel(data) {
                axios.post('/exportReport', {
                    data: data,
                })
                    .then(response => {
                        var a = $("<a>")
                            .prop("href", "/api/downloadExport/" + 'report_detail.xlsx')
                            .appendTo("body");
                        a[0].click();
                        a.remove();
                        //roam_message('success','Lỗi hệ thống. Thao tác thất bại');
                    })
                    .catch(error => {
                        console.log(error);
                        roam_message('error', this.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
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
            }
        },
        mounted() {
            this.preloadData();
            this.getDistrict()
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
    .hidden{
        display:none;
    }
</style>
