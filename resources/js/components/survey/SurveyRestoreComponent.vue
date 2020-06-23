<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/tms/survey/list">{{ trans.get('keys.quan_tri_survey') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.khoi_phuc_survey') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_survey_can_khoi_phuc')}}</h5>

                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="dataTables_length">

                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <form v-on:submit.prevent="getSurveys(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_survey')+' ...'">
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary d-none d-lg-block"
                                                        @click="getSurveys(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!--                                <div class="row">-->
                                <!--                                    <div class="col-6">-->
                                <!--                                        <div class="dataTables_length">-->
                                <!--                                            <label>{{trans.get('keys.ngay_bat_dau')}}</label>-->
                                <!--                                            <date-picker v-model="startdate"-->
                                <!--                                                         :config="{format: 'DD-MM-YYYY'}"></date-picker>-->
                                <!--                                        </div>-->
                                <!--                                    </div>-->
                                <!--                                    <div class="col-6">-->
                                <!--                                        <div class="dataTables_length">-->
                                <!--                                            <label>{{trans.get('keys.ngay_ket_thuc')}}</label>-->
                                <!--                                            <date-picker v-model="enddate"-->
                                <!--                                                         :config="{format: 'DD-MM-YYYY'}"></date-picker>-->
                                <!--                                        </div>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
<!--                                <br/>-->
                                <div class="row">
                                    <div class="col-6 dataTables_wrapper">
                                        <div class="dataTables_length">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row"
                                                        class="custom-select custom-select-sm form-control form-control-sm"
                                                        @change="getSurveys(1)">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="50">50</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <br/>
                                <div class="table-responsive">
                                    <table id="datable_1" class="table_res">
                                        <thead>
                                        <tr>
                                            <th>{{trans.get('keys.stt')}}</th>
                                            <th style="width: 20%;">{{trans.get('keys.ma_survey')}}</th>
                                            <th style="width: 40%;">{{trans.get('keys.ten_survey')}}</th>
                                            <!--                                            <th class=" mobile_hide">{{trans.get('keys.bat_dau')}}</th>-->
                                            <!--                                            <th class=" mobile_hide">{{trans.get('keys.ket_thuc')}}</th>-->
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(sur,index) in surveys">
                                            <td>{{index+1}}</td>
                                            <td>{{ sur.code }}</td>
                                            <td>{{ sur.name }}</td>
                                            <!--                                            <td class=" mobile_hide">{{ sur.startdate |convertDateTime}}</td>-->
                                            <!--                                            <td class=" mobile_hide">{{ sur.enddate |convertDateTime}}</td>-->
                                            <td class="text-center">
                                                <button :title="trans.get('keys.khoi_phuc_survey')" data-toggle="modal"
                                                        data-target="#delete-ph-modal"
                                                        @click="restoreSurvey(sur.id)"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-trash-restore"></i></span>
                                                </button>
                                                <button :title="trans.get('keys.xoa')" data-toggle="modal"
                                                        data-target="#delete-ph-modal"
                                                        @click="deleteSurvey(sur.id)"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>

                                    <v-pagination v-model="current" @input="onPageChange" :page-count="totalPages"
                                                  :classes=$pagination.classes
                                                  :labels=$pagination.labels></v-pagination>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        components: {
            //vPagination,
            datePicker
        },
        data() {
            return {
                surveys: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 5,
                startdate: '',
                enddate: '',
                date: new Date(),
                options: {
                    format: 'DD/MM/YYYY',
                    useCurrent: false,
                }
            }
        },
        filters: {
            convertDateTime(value) {
                var time = new Date(value * 1000);
                return time.toLocaleDateString();
            }
        },
        methods: {
            getSurveys(paged) {
                axios.post('/api/survey/getlistrestore', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        this.surveys = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getSurveys();
            },
            restoreSurvey(id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_khoi_phuc_survey_nay'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/survey/restore', {survey_id: id})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                                current_pos.getSurveys(this.current);

                            } else {
                                toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                            }
                            swal.close();
                        })
                        .catch(error => {
                            swal.close();
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });

                return false;
            },
            deleteSurvey(id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_khoi_phuc_survey_nay'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/survey/del_restore', {survey_id: id})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                                current_pos.getSurveys(this.current);
                            } else {
                                toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                            }
                            swal.close();
                        })
                        .catch(error => {
                            swal.close();
                            toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                        });
                });

                return false;
            }
        },
        mounted() {
            this.getSurveys();
        }
    }
</script>

<style scoped>

</style>
