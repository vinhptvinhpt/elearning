
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


          <!--New filter-->
          <div class="row">
            <div class="col-md-6">
              <label for="organization_id">{{ trans.get('keys.to_chuc')}}</label>
              <treeselect v-model="organization_id" :multiple="false" :options="organization_options" id="organization_id"/>
            </div>
            <div class="col-md-6">
              <label for="training_select">{{ trans.get('keys.khung_nang_luc')}}</label>
              <select id="training_select" v-model="training_id" class="custom-select custom-select-sm form-control form-control-sm">
                <option v-for="training_option in training_options" :value="training_option.id">
                  {{training_option.name}}
                </option>
              </select>
            </div>
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
    export default {
    	data() {
            return {
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
                organization_id: 0,
                organization_options: [],
                organization_ready: false,
                training_id: 0,
                training_options: [],
                training_ready: false
            }
        },
        methods: {
            preloadData() {
                this.fetchOrganization();
                this.fetchTraining();
                this.listData();
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
            showReport() {
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
            listData() {
              axios.post('/report/list_detail', {
                organization_id: this.organization_id,
                training_id: this.training_id
              })
                .then(response => {
                  this.report_data = response.data;
                })
                .catch(error => {
                  console.log(error.response.data);
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
                            .prop("href", "/downloadExportReport")
                            //.prop("download", "newfile.txt")
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
            },
            fetchOrganization() {
              $('.content_search_box').addClass('loadding');
              axios.post('/organization/list', {
                keyword: this.parent_keyword,
                level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                paginated: 0 //không phân trang,
              })
                .then(response => {
                  //Set options recursive
                  this.organization_options = this.setOptions(response.data);
                  if(this.organization_options.length !== 0) {
                    this.organization_id = this.organization_options[0].id;
                  }
                  this.organization_ready = true;
                  $('.content_search_box').removeClass('loadding');
                })
                .catch(error => {
                  $('.content_search_box').removeClass('loadding');
                })
            },
            fetchTraining() {
              axios.post('/api/trainning/list', {
                paginated: 0
              })
                .then(response => {
                  this.training_options = response.data;
                  //set first options
                  if(this.training_options.length !== 0) {
                    this.training_id = this.training_options[0].id;
                  }
                  this.training_ready = true;
                })
                .catch(error => {
                  console.log(error.response.data);
                });
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
        },
        mounted() {
            this.preloadData();
        },
        computed: { //Phải gọi trên html nó mới trigger computed value
          data_ready: function() {
            return this.training_ready && this.organization_ready;
          }
        },
        watch: {
          data_ready: function(newVal, oldVal) {
            if (newVal === true && oldVal === false) {
              this.callReportRegion();
              $('#requestSaleRoom').hide();
            }
          }
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
    .hidden {
        display:none;
    }
</style>
