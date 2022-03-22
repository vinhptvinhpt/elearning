<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_truy_cap') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mx-0">
            <div class="col-12 hk-sec-wrapper">
                <h5 class="hk-sec-title">{{trans.get('keys.tong_so_luot_truy_cap')}}</h5>
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 style="font-size:100px; color:#3a55b1; text-align:center;">{{total_course}}
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="table-wrap">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="dataTables_length">

                                        <date-picker v-model="startdate" :config="options"></date-picker>
                                        <label>{{trans.get('keys.tu_ngay')}}</label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="dataTables_length">

                                        <date-picker v-model="enddate" :config="options"></date-picker>
                                        <label>{{trans.get('keys.den_ngay')}}</label>
                                    </div>
                                </div>
                                <br/>
                                <div class="col-6">
                                    <div class="d-flex flex-row form-group">
                                        <input v-model="keyword" type="text"
                                               class="form-control search_text"
                                               :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_fullname')+'...'">
                                        <button type="button" id="btnFilterDoc"
                                                class="btn btn-primary btn-sm" style="margin-left: 2px;"
                                                @click="getLogLogin(1)">
                                            {{trans.get('keys.tim')}}
                                        </button>

                                        <a style="color: #fff; margin-left: 5px;"
                                           class="btn btn-sm btn-icon btn-primary btn-icon-style-2"
                                           v-on:click="exportExcel(logs)" :title="trans.get('keys.xuat_excel')">
                                            <span class="btn-icon-wrap"><i class="fal fa-file-excel-o"></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3 dataTables_wrapper">
                                    <div class="dataTables_length">
                                        <label>{{trans.get('keys.hien_thi')}}
                                            <select v-model="row"
                                                    class="custom-select custom-select-sm form-control form-control-sm"
                                                    @click="getLogLogin(1)">
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="datable_1Att1" class="table_res">
                                    <thead>
                                    <tr>
                                        <th>{{trans.get('keys.stt')}}</th>
                                        <th>{{trans.get('keys.username')}}</th>
                                        <th>{{trans.get('keys.fullname')}}</th>
                                        <th>{{trans.get('keys.thoigian')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(att,index) in logs">
                                        <td>{{ index+1 }}</td>
                                        <td><a :href="'/tms/system/user/edit/'+att.id">
                                            {{ att.username }}
                                        </a></td>
                                        <td>{{ att.fullname }}</td>
                                        <td>{{ att.timecreated | convertDateTime }}</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>

                                <v-pagination v-model="current" @input="onPageChange"
                                              :page-count="totalPages" :classes=$pagination.classes
                                              :labels=$pagination.labels></v-pagination>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- /Row -->

    </div>
</template>

<script>
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        components: {datePicker},
        data() {
            return {
                logs: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                total_course: 0,
                row: 10,
                startdate: '',
                enddate: '',
                options: {
                    format: 'DD-MM-YYYY',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                }
            }
        },
        filters: {
            convertDateTime(value) {
                if (value) {
                    var time = new Date(value * 1000);

                    var ten = function (i) {
                        return (i < 10 ? '0' : '') + i;
                    };

                    var YYYY = time.getFullYear();
                    var MM = ten(time.getMonth() + 1);
                    var DD = ten(time.getDate());

                    var hour = ten(time.getHours());
                    var minu = ten(time.getMinutes());
                    var sed = ten(time.getSeconds());

                    return DD + '/' + MM + '/' + YYYY + ' ' + hour + ':' + minu + ':' + sed;
                }
                return "";
            }
        },
        methods: {
            getLogLogin(paged) {
                axios.post('/api/system/user/login_statistic', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        this.logs = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                        this.total_course = response.data.total_course;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            exportExcel(data) {
                let current_pos = this;
                axios.post('/exportLoginReport', {
                    keyword: this.keyword,
                    row: this.row,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        var a = $("<a>")
                            .prop("href", "/downloadExportLoginReport")
                            //.prop("download", "newfile.txt")
                            .appendTo("body");
                        a[0].click();
                        a.remove();
                        //roam_message('success','Lỗi hệ thống. Thao tác thất bại');
                    })
                    .catch(error => {
                        console.log(error);
                        roam_message('error', current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'));
                    });
            },
            onPageChange() {
                this.getLogLogin();
            }
        },
        mounted() {
            // this.getLogLogin();
        }
    }
</script>

<style scoped>

</style>
