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
				            text: 'Thống kê Nhân viên bán hàng'
				        }
				    },
				    tooltip: {
				        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				            '<td style="padding:0"><b>{point.y} Nhân viên</b></td></tr>',
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
            }
        },
        methods: {
            preloadData() {
                this.callStatistic();
            },

            callStatistic() { //Statistic
                axios.post('/report/show_statistic', {
                    district: this.district,
                    city: this.city,
                    branch: this.branch
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
                            name: 'Tỉ lệ',
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
                            name: 'Tỉ lệ',
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
                            name: 'Tỉ lệ',
                            colorByPoint: true,
                            data: saleroom_data
                        }];

                        // //Khóa học Update
                        this.courseOptions.series = [{
                            name: 'Tỉ lệ',
                            colorByPoint: true,
                            data: [{
                                name: 'Khóa học trực tuyến',
                                y: this.data.course_online,
                                //color: '#dc0511'
                                /*sliced: true,
                                selected: true*/
                            }, {
                                name: 'Khóa học tập trung',
                                y: this.data.course_offline,
                                //color: '#3a55b1'
                            }]
                        }];

                        let user_confirm_data = [];
                        let user_missing_data = [];
                        let user_district_title = [];

                        for (let [key, value] of Object.entries(this.data.userConfirm)) {
                            user_confirm_data.push(value.length);
                            user_missing_data.push(this.data.user[key].length - value.length);
                            user_district_title.push(this.data.district[key]);
                        }
                        this.userOptions.xAxis.categories = user_district_title;

                        // //Nhân viên update
                        this.userOptions.series = [{
                            name: 'Nhân viên đã có giấy chứng nhận',
                            data: user_confirm_data,
                            //color: '#3a55b1'
                        }, {
                            name: 'Nhân viên chưa có giấy chứng nhận',
                            data: user_missing_data,
                            //color: '#dc0511'
                        }];
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },

            showStatistic(){
                this.callStatistic();
                $('#indexReport').show();
                $('#requestReport').hide();
            },
        },
        mounted() {
            this.preloadData();
        }
    }
</script>

<style scoped>

</style>
