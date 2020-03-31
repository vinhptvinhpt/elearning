<template>
    <div class="row">
        <div class="col-xl-12">
            <div class="hk-row">
                <div class="col-sm-8">
                    <div class="card card-sm" style="height: calc(100% - 15px);">
                        <div class="card-body text-center">
                            <span class="d-block font-18 font-weight-500 text-dark text-uppercase mb-10">{{trans.get('keys.theo_doi_so_nv_duoc_dao_tao_theo_thang')}}</span>
                            <div class="hk-row justify-content-center">
                                <div class="form-inline">
                                    <datepicker
                                      id="inputStart"
                                      :clear-button=true
                                      v-model="startdate"
                                      format="dd-MM-yyyy"
                                      input-class="form-control"
                                      @input="listData()"></datepicker>&nbsp;<datepicker
                                  id="inputEnd"
                                  :clear-button=true
                                  v-model="enddate"
                                  format="dd-MM-yyyy"
                                  input-class="form-control"
                                  @input="listData()"></datepicker>
<!--                                    <input type="date" id="inputStart" v-model="startdate" class="form-control" @change="listData()">&nbsp;<input type="date" id="inputEnd" v-model="enddate" class="form-control" @change="listData()">-->
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <p id="startdate-warning" class="text-danger code_required hide">
                                    {{trans.get('keys.vui_long_nhap_ngay_bat_dau')}}</p>
                                <p id="enddate-warning" class="text-danger code_error hide">
                                    {{trans.get('keys.vui_long_nhap_ngay_ket_thuc')}}</p>
                                <p id="logic-warning" class="text-danger code_error hide">
                                    {{trans.get('keys.vui_long_nhap_ngay_bat_dau_nho_hon_hoac_bang_ngay_ket_thuc')}}</p>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <highcharts :options="option1" style="height:294px;"></highcharts>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-sm" style="height: calc(100% - 15px);">
                        <div class="card-body text-center">
                            <!--                            <span class="d-block font-18 font-weight-500 text-dark text-uppercase mb-10">Học viên tham gia</span>-->
                            <span class="d-block font-18 font-weight-500 text-dark text-uppercase mb-10">{{trans.get('keys.thanh_phan_khoa_hoc')}}</span>
                            <div class="row">
                                <div class="col-sm">
                                    <highcharts :options="option2" style="height:294px;"></highcharts>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!--                <div class="col-sm-12">-->
<!--                    <div class="card card-sm">-->
<!--                        <div class="card-body text-center">-->
<!--                            <span class="d-block font-18 font-weight-500 text-dark text-uppercase mb-10">{{trans.get('keys.nvbh_moi_nvbh_cu')}}</span>-->
<!--                            <div class="row mt-35">-->
<!--                                <div class="col-sm">-->
<!--                                    <highcharts :options="option3" style="height:294px;"></highcharts>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
        <div class="col-xl-12">
            <section class="hk-sec-wrapper">
                <div class="card-body text-center">
                    <span class="d-block font-18 font-weight-500 text-dark text-uppercase mb-10">{{trans.get('keys.cac_khoa_hoc_dang_tien_hanh')}}</span>
                </div>
              <div class="row">
                <div class="col-sm-3">
                  <div class="dataTables_length">
                    <date-picker v-model="startdateSearch" :config="options"
                                 :placeholder="trans.get('keys.ngay_bat_dau')" class="txtSearch"></date-picker>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="dataTables_length">
                    <date-picker v-model="enddatesearch" :config="options"
                                 :placeholder="trans.get('keys.ngay_ket_thuc')" class="txtSearch"></date-picker>
                  </div>
                </div>
                <div class="col-sm-6">
                  <form v-on:submit.prevent="tableData(1)">
                    <div class="d-flex flex-row form-group">
                      <input v-model="keyword" type="text" class="form-control txtSearch"
                             :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ma_hoac_ten_khoa_dao_tao')+' ...'">
                      <button type="button" id="btnFilter" class="btn btn-primary"
                              style="margin-left: 5px" @click="tableData(1)">
                        {{trans.get('keys.tim')}}
                      </button>
                    </div>
                  </form>
                </div>

              </div>

              <div class="row">
                <div class="col-12">
                  <div class="dataTables_length" style="display: block;">
                    <label>{{trans.get('keys.hien_thi')}}
                      <select v-model="row"
                              class="custom-select custom-select-sm form-control form-control-sm"
                              @change="tableData(1)">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                      </select>
                    </label>
                  </div>
                </div>
              </div>

                <div class="row">
                    <div class="col-sm">
                        <div class="table-wrap table-responsive">
                            <table id="datable_1" class="table_res table table-hover w-100 pb-30">
                                <thead>
                                <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ten_khoa_hoc')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.ngay_bat_dau')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.ngay_ket_thuc')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.phong_hoc')}}</th>
                                    <th>{{trans.get('keys.hinh_thuc_dt')}}</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr v-if="posts.length === 0">
                                    <td colspan="6">{{trans.get('keys.khong_tim_thay_du_lieu')}}</td>
                                </tr>
                                <tr v-else v-for="(course_object, index) in posts">
                                    <td>{{index+1}}</td>
                                    <td>{{course_object.fullname}}</td>
                                    <td class="mobile_hide">{{course_object.start}}</td>
                                    <td class="mobile_hide">{{course_object.end}}</td>
                                    <td class="mobile_hide">{{course_object.course_place}}</td>
                                    <template v-if="course_object.category === 5">
                                        <td><span class="badge badge-grey">Offline</span></td>
                                    </template>
                                    <template v-else-if="course_object.category !== 5 && course_object.category !== 2">
                                        <td><span class="badge badge-primary">Online</span></td>
                                    </template>
                                    <template v-else>
                                        <td><span class="badge badge-primary"></span></td>
                                    </template>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>{{trans.get('keys.stt')}}</th>
                                    <th>{{trans.get('keys.ten_khoa_hoc')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.ngay_bat_dau')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.ngay_ket_thuc')}}</th>
                                    <th class="mobile_hide">{{trans.get('keys.phong_hoc')}}</th>
                                    <th>{{trans.get('keys.hinh_thuc_dt')}}</th>
                                </tr>
                                </tfoot>
                            </table>

                            <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                          :classes=$pagination.classes :labels=$pagination.labels></v-pagination>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
    import datePicker from 'vue-bootstrap-datetimepicker'
    import {Chart} from 'highcharts-vue'

    //import vPagination from 'vue-plain-pagination'
    export default {
        components: {
            highcharts: Chart,
            datePicker
            //vPagination,
        },
        data() {
            return {
                chart_data: {
                    // 'completed': [
                    //     {
                    //         'mth': 0,
                    //         'total': 0
                    //     }
                    // ],
                    // 'enrolled': [
                    //     {
                    //         'mth': 0,
                    //         'total': 0
                    //     }
                    // ],
                    'online': 0,
                    'offline': 0,
                    'registered': [
                        {
                            'mthyr': 0,
                            'total': 0
                        }
                    ],
                    'stack_registered': [
                        {
                            'mthyr': 0,
                            'total': 0
                        }
                    ],
                    'confirmed': [
                        {
                            'mthyr': 0,
                            'total': 0
                        }
                    ],
                    'quit': [
                        {
                            'mthyr': 0,
                            'total': 0
                        }
                    ]
                },
                total1: 0,
                option1: {
                    title: {
                        useHTML: true,
                        text: ''
                    },
                    subtitle: {
                        text: this.trans.get('keys.so_luong_nhan_vien_hoan_thanh_dao_tao_tong_so_nhan_vien')
                    },
                    color: ['#3a55b1', '#adb3b6'],
                    tooltip: {
                        show: true,
                        trigger: 'axis',
                        backgroundColor: '#fff',
                        borderRadius: 6,
                        padding: 6,
                        axisPointer: {
                            lineStyle: {
                                width: 0,
                            }
                        },
                        textStyle: {
                            color: '#324148',
                            fontFamily: '"Poppins", sans-serif',
                            fontSize: 12
                        }
                    },
                    grid: {
                        top: '3%',
                        left: '3%',
                        right: '3%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        categories: [this.trans.get('keys.stt') + ' 5', this.trans.get('keys.stt') + ' 6', this.trans.get('keys.stt') + ' 7', this.trans.get('keys.stt') + ' 8', this.trans.get('keys.stt') + ' 9', this.trans.get('keys.stt') + ' 10'],
                        //crosshair: true
                    },
                    yAxis: [
                        {
                            allowDecimals: false,
                            title: {
                                text: this.trans.get('keys.so_luong')
                            },
                            type: 'value',
                            axisLine: {
                                show: false
                            },
                            axisTick: {
                                show: false
                            },
                            axisLabel: {
                                textStyle: {
                                    color: '#6f7a7f'
                                }
                            },
                            splitLine: {
                                lineStyle: {
                                    color: '#eaecec',
                                }
                            }
                        }
                    ],
                    series: [
                        {
                            name: this.trans.get('keys.nhan_vien_hoan_thanh_dao_tao'),
                            type: 'column',
                            data: [0, 0, 0, 0, 0, 0],
                        },
                        {
                            name: this.trans.get('keys.tong_so_nhan_vien'),
                            type: 'column',
                            data: [0, 0, 0, 0, 0, 0],
                        }
                    ]
                },
                option2: {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        useHTML: true,
                        text: ''
                    },
                    subtitle: {
                        text: this.trans.get('keys.tong_so') + ' 99'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            },
                            showInLegend: true
                        }
                    },
                    series: [
                        {
                            // name: 'Học viên tham gia trong tháng',
                            name: this.trans.get('keys.so_luong'),
                            colorByPoint: true,
                            data: [
                                {
                                    name: this.trans.get('keys.khoa_online'),
                                    y: 66
                                },
                                {
                                    name: this.trans.get('keys.khoa_offline'),
                                    y: 33
                                },
                            ]
                        }
                    ]
                },
                option3: {
                    chart: {
                        type: 'area'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: [this.trans.get('keys.stt') + ' 5', this.trans.get('keys.stt') + ' 6', this.trans.get('keys.stt') + ' 7', this.trans.get('keys.stt') + ' 8', this.trans.get('keys.stt') + ' 9', this.trans.get('keys.stt') + ' 10'],
                        //crosshair: true
                    },
                    yAxis: {
                        labels: {
                            format: '{value}'
                        },
                        title: {
                            enabled: false
                        }
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                        split: true
                    },
                    plotOptions: {
                        area: {
                            lineColor: '#ffffff',
                            lineWidth: 1,
                            marker: {
                                lineWidth: 1,
                                lineColor: '#ffffff'
                            }
                        }
                    },
                    series: [
                        {
                            name: this.trans.get('keys.nvbh_moi'),
                            data: [502, 635, 809, 947, 1402]
                        },
                        {
                            name: this.trans.get('keys.nvbh_cu'),
                            data: [106, 107, 111, 133, 221]
                        }
                    ]
                },
                current: 1,
                totalPages: 0,
                row: 5,
                posts: {},
                startdate: '',
                enddate: '',
                startdateSearch : '',
                enddatesearch: '',
                keyword: '',
                options: {
                    format: 'DD-MM-YYYY',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                },
            }
        },
        methods: {
            setTimeDefault() {
                let currentDate = new Date();
                let now = Date.now();
                let x_month = currentDate.setMonth(currentDate.getMonth() - 4);
                let startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                this.startdate = this.convertTime(startDate);
                this.enddate = this.convertTime(now);
            },
            convertTime(timestamp) {
                let a = new Date(timestamp);
                let year = a.getFullYear();
                //Jan->Dec
                // let months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                // let month = months[a.getMonth()+1];
                //1->12
                let month = a.getMonth();
                month = ('0' + (month + 1)).slice(-2);
                let date = a.getDate();
                // let hour = a.getHours();
                // let min = a.getMinutes();
                // let sec = a.getSeconds();
                return year + '-' + month + '-' + ('0' + date).slice(-2);
            },
            listData() {
                if (this.startdate === null || this.startdate === 'undefined') {
                  $('#startdate-warning').show();
                  return;
                } else if (this.enddate === null || this.enddate === 'undefined') {
                  $('#enddate-warning').show();
                }
                else if (this.startdate.length === 0 || this.enddate.length === 0) {
                    $('#logic-warning').hide();
                    if (this.startdate.length === 0) {
                        $('#startdate-warning').show();
                        return;
                    }
                    if (this.enddate.length === 0) {
                        $('#startdate-warning').hide();
                        $('#enddate-warning').show();
                    }
                } else if (this.startdate > this.enddate) {
                    $('#startdate-warning').hide();
                    $('#enddate-warning').hide();
                    $('#logic-warning').show();
                } else {
                    $('#startdate-warning').hide();
                    $('#enddate-warning').hide();
                    $('#logic-warning').hide();

                    this.startdate = this.convertTime(this.startdate);
                    this.enddate = this.convertTime(this.enddate);

                    axios.post('/dashboard/chart_data', {
                        startdate: this.startdate,
                        enddate: this.enddate,
                    })
                        .then(response => {
                            let chart_data = response.data;
                            let label_array = [];
                            //let completed_array = [];
                            //let enrolled_array = [];
                            let registered_array = [];
                            let stack_registered_array = [];
                            let confirmed_array = [];
                            let quit_array = [];
                            let label_array2 = [];


                            // chart_data.completed.forEach(val => {
                            //     label_array.push('Tháng ' + val.mth);
                            //     completed_array.push(val.total);
                            // });
                            // chart_data.enrolled.forEach(val => {
                            //     enrolled_array.push(val.total);
                            // });
                            chart_data.confirmed.forEach(val => {
                                label_array.push('T ' + val.mthyr);
                                confirmed_array.push(val.total);
                            });
                            chart_data.stack_registered.forEach(val => {
                                stack_registered_array.push(val.total);
                            });

                            chart_data.registered.forEach(val => {
                                label_array2.push('T ' + val.mthyr);
                                registered_array.push(val.total);
                            });
                            chart_data.quit.forEach(val => {
                                quit_array.push(val.total);
                            });
                            this.option1.xAxis.categories = label_array;
                            this.option1.series[0]['data'] = confirmed_array;
                            this.option1.series[1]['data'] = stack_registered_array;

                            this.option2.series[0]['data'][0].y = chart_data.online;
                            this.option2.series[0]['data'][1].y = chart_data.offline;
                            this.total1 = chart_data.online + chart_data.offline;
                            this.option2.subtitle.text = this.trans.get('keys.tong_so') + ': ' + this.total1;

                            this.option3.xAxis.categories = label_array2;
                            this.option3.series[0]['data'] = registered_array;
                            this.option3.series[1]['data'] = quit_array;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            },
            tableData(paged) {
                axios.post('/dashboard/table_data', {
                    page: paged || this.current,
                    row: this.row,
                    keyword: this.keyword,
                    startdate: this.startdateSearch,
                    enddate: this.enddatesearch,
                })
                    .then(response => {
                        this.posts = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            onPageChange() {
                this.tableData();
            },
            customFormatter(date) {
              return moment(date).format('dd-mm-yyyy');
            }
        },
        mounted() {
            this.setTimeDefault();
            this.listData();
        }
    }
</script>

<style scoped>
  .txtSearch{
    height: calc(1.5em + 0.75rem + 6px);
  }
</style>
