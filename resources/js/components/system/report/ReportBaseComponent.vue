<template>

  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.bao_cao_so_bo') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div>
      <!--Chart Statistic-->
      <div class="card" id="indexReport">
        <div class="card-body">

          <div class="row">
            <div class="col-md-6">
              <label for="organization_id">{{ trans.get('keys.to_chuc')}}</label>
              <treeselect v-model="organization_id" :multiple="false" :options="organization_options" id="organization_id" @input="callStatistic()"/>
            </div>
            <div class="col-md-6">
              <label for="training_select">{{ trans.get('keys.khung_nang_luc')}}</label>
                <select id="training_select" v-model="training_id" class="custom-select" @change="callStatistic()">
                  <option v-for="training_option in training_options" :value="training_option.id">
                    {{training_option.name}}
                  </option>
                </select>
            </div>
          </div>

          <div class="row" style="display: none">
            <div class="col-md-4 col-sm-6">
              <h6 class="text-center">{{trans.get('keys.thong_ke_so_luong_tinh_thanh')}}</h6>
              <!--						<p class="text-center mt-10">{{trans.get('keys.tong_so')}} <strong>{{data.city.MB.length + data.city.MT.length + data.city.MN.length}}</strong> {{trans.get('keys.tinh_thanh')}}</p>-->
              <highcharts :options="cityOptions"></highcharts>
            </div>
            <div class="col-md-4 col-sm-6">
              <h6 class="text-center">{{trans.get('keys.thong_ke_so_luong_dai_ly')}}</h6>
              <!--						<p class="text-center mt-10">{{trans.get('keys.tong_so')}} <strong>{{data.branch.MB.length + data.branch.MT.length + data.branch.MN.length}}</strong> {{trans.get('keys.dai_ly')}}</p>-->
              <highcharts :options="branchOptions"></highcharts>
            </div>
            <div class="col-md-4 col-sm-6">
              <h6 class="text-center">{{trans.get('keys.thong_ke_so_luong_diem_ban')}}</h6>
              <!--						<p class="text-center mt-10">{{trans.get('keys.tong_so')}} <strong>{{data.saleroom.MB.length + data.saleroom.MT.length + data.saleroom.MN.length}}</strong> {{trans.get('keys.diem_ban')}}</p>-->
              <highcharts :options="saleroomOptions"></highcharts>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <h5 class="text-center mt-20">{{trans.get('keys.thong_ke_nhan_vien_ban_hang')}}</h5>
              <highcharts :options="userOptions"></highcharts>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <h5 class="text-center mt-20">{{trans.get('keys.thong_ke_khoa_dao_tao')}}</h5>
              <p class="text-center mt-10">{{trans.get('keys.tong_so')}} <strong>{{data.course_online + data.course_offline}}</strong> {{trans.get('keys.khoa_hoc')}}</p>
              <highcharts :options="courseOptions"></highcharts>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

</template>

<script>
	import {Chart} from 'highcharts-vue'
    export default {
    	components: {highcharts: Chart},
    	data() {
            return {
              data:{
                  district:{},
                  city:{},
                  branch:{},
                  saleroom:{},
                  user:{},
                  userConfirm:{},
              },
              //Statistic data
              courseOptions: {
                chart: {
                      plotBackgroundColor: null,
                      plotBorderWidth: null,
                      plotShadow: false,
                      type: 'pie'
                  },
                  title: {
                      text: ''
                  },
                  tooltip: {
                      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                  },
                  plotOptions: {
                      pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                              enabled: false
                          },
                          showInLegend: true
                      }
                  },
                  series: [{}]
              },
              userOptions: {
                chart: {
                  type: 'column'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: this.trans.get('keys.thong_ke_nhan_vien')
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y} ' +this.trans.get('keys.nhan_vien')+'</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: []
              },
              cityOptions: {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{}]
              },
              branchOptions: {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{}]
              },
              saleroomOptions: {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{}]
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
            selectOrganizationItem(id){
              this.organization_id = id;
            },
            preloadData() {
                this.fetchOrganization();
                this.fetchTraining();
            },
            callStatistic() { //Statistic
                axios.post('/report/show_statistic', {
                    organization_id: this.organization_id,
                    training_id: this.training_id
                })
                    .then(response => {
                        this.data = response.data;

                        let city_data = [];
                        for (let [key, value] of Object.entries(this.data.city)) {
                            city_data.push({
                                name: this.data.district[key],
                                y: value.length
                            })
                        }

                        //City update
                        this.cityOptions.series = [{
                            name: this.trans.get('keys.ratio'),
                            colorByPoint: true,
                            data: city_data
                        }];

                        let branch_data = [];
                        for (let [key, value] of Object.entries(this.data.branch)) {
                            branch_data.push({
                                name: this.data.district[key],
                                y: value.length
                            })
                        }

                        // //Đại lý update
                        this.branchOptions.series = [{
                            name: this.trans.get('keys.ratio'),
                            colorByPoint: true,
                            data: branch_data
                        }];

                        let saleroom_data = [];
                        for (let [key, value] of Object.entries(this.data.saleroom)) {
                            saleroom_data.push({
                                name: this.data.district[key],
                                y: value.length
                            })
                        }

                        // //Điểm bán Update
                        this.saleroomOptions.series = [{
                            name: this.trans.get('keys.ratio'),
                            colorByPoint: true,
                            data: saleroom_data
                        }];

                        // //Khóa học Update
                        this.courseOptions.series = [{
                            name: this.trans.get('keys.ratio'),
                            colorByPoint: true,
                            data: [{
                                name:  this.trans.get('keys.khoa_hoc_truc_tuyen'),
                                y: this.data.course_online,
                                //color: '#dc0511'
                                /*sliced: true,
                                selected: true*/
                            }, {
                                name:  this.trans.get('keys.khoa_hoc_tap_trung'),
                                y: this.data.course_offline,
                                //color: '#3a55b1'
                            }]
                        }];

                        // let user_confirm_data = [];
                        // let user_missing_data = [];
                        // let user_district_title = [];
                        //
                        // for (let [key, value] of Object.entries(this.data.userConfirm)) {
                        //     user_confirm_data.push(value.length);
                        //     user_missing_data.push(this.data.user[key].length - value.length);
                        //     user_district_title.push(this.data.district[key]);
                        // }
                        // this.userOptions.xAxis.categories = user_district_title;
                        //
                        // // //Nhân viên update
                        // this.userOptions.series = [{
                        //     name: 'Nhân viên đã có giấy chứng nhận',
                        //     data: user_confirm_data,
                        //     //color: '#3a55b1'
                        // }, {
                        //     name: 'Nhân viên chưa có giấy chứng nhận',
                        //     data: user_missing_data,
                        //     //color: '#dc0511'
                        // }];
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            listData() {
            axios.post('/report/list_base', {
              organization_id: this.organization_id,
              training_id: this.training_id,
            })
              .then(response => {
                let list = response.data;


                let user_confirm_data = [];
                let user_missing_data = [];
                let user_district_title = [];

                for (let [id, object] of Object.entries(list)) {

                  let certificated_count = Array.isArray(object.certificated) ? object.certificated.length : Object.keys(object.certificated).length;
                  user_confirm_data.push(certificated_count);

                  let certificated_mising_count = Array.isArray(object.certificated_missing) ? object.certificated_missing.length : Object.keys(object.certificated_missing).length;
                  user_missing_data.push(certificated_mising_count);

                  user_district_title.push(object.name);
                }
                this.userOptions.xAxis.categories = user_district_title;

                // //Nhân viên update
                this.userOptions.series = [{
                  name: this.trans.get('keys.nhan_vien_da_co_giay_chung_nhan'),
                  data: user_confirm_data,
                  //color: '#3a55b1'
                }, {
                  name: this.trans.get('keys.nhan_vien_chua_co_giay_chung_nhan'),
                  data: user_missing_data,
                  //color: '#dc0511'
                }];

              })
              .catch(error => {
                console.log(error);
              });
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
                  // this.training_options = response.data;
                    this.training_options = response.data.data.data;
                  //set first options
                  if(this.training_options.length !== 0) {
                    this.training_id = this.training_options[0].id;
                  }
                  this.training_ready = true;
                })
                .catch(error => {
                  console.log(error);
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
        computed: { //Phải gọi trên html nó mới trigger computed value
          data_ready: function() {
            return this.training_ready && this.organization_ready;
          }
        },
        mounted() {
            this.preloadData();
        },
        watch: {
          data_ready: function(newVal, oldVal) {
            if (newVal === true && oldVal === false) {
              this.callStatistic();
              this.listData();
            }
          }
        }
    }
</script>

<style scoped>

</style>
