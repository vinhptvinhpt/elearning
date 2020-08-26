<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.quan_tri_survey') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_survey')}}</h5>

                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <div class="col-6"></div>
                                    <div class="col-6">
                                        <form v-on:submit.prevent="getSurveys(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_survey')+' ...'"/>
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary btn-sm"
                                                        @click="getSurveys(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!--                                <div class="row">-->
                                <!--                                    &lt;!&ndash;                                <div class="col-6">&ndash;&gt;-->
                                <!--                                    &lt;!&ndash;                                    <div class="dataTables_length">&ndash;&gt;-->

                                <!--                                    &lt;!&ndash;                                    </div>&ndash;&gt;-->
                                <!--                                    &lt;!&ndash;                                </div>&ndash;&gt;-->
                                <!--                                    <div class="col-sm-6">-->
                                <!--                                        <div class="dataTables_length">-->
                                <!--                                            <label>{{trans.get('keys.ngay_bat_dau')}}</label>-->
                                <!--                                            <date-picker v-model="startdate"-->
                                <!--                                                         :config="{format: 'DD-MM-YYYY'}"></date-picker>-->
                                <!--                                        </div>-->
                                <!--                                    </div>-->
                                <!--                                    <div class="col-sm-6">-->
                                <!--                                        <div class="dataTables_length">-->
                                <!--                                            <label>{{trans.get('keys.ngay_ket_thuc')}}</label>-->
                                <!--                                            <date-picker v-model="enddate"-->
                                <!--                                                         :config="{format: 'DD-MM-YYYY'}"></date-picker>-->
                                <!--                                        </div>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                                <div class="row pt-3">
                                    <div class="col-6 dataTables_wrapper">
                                        <div class="dataTables_length d-block">
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

                                    <div class="col-6">
                                        <div id="datable_1_filter" class="dataTables_filter" style="float: right;">
                                            <label>
                                                <router-link to="/tms/survey/create">
                                                    <button type="button"
                                                            class="btn btn-success btn-sm"
                                                            :placeholder="trans.get('keys.tao_moi')"
                                                            value="Tạo mới"
                                                            aria-controls="datable_1">{{trans.get('keys.tao_moi')}}
                                                    </button>
                                                </router-link>
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
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>
                                                <router-link
                                                        :to="{name: 'SurveyStatistic', params: {survey_id: sur.id}}">{{
                                                    sur.code }}
                                                </router-link>
                                            </td>
                                            <td>{{ sur.name }}</td>
                                            <!--                                            <td class=" mobile_hide">{{ sur.startdate |convertDateTime}}</td>-->
                                            <!--                                            <td class=" mobile_hide">{{ sur.enddate |convertDateTime}}</td>-->
                                            <td class="text-center">

                                                <router-link
                                                        :title="trans.get('keys.giao_dien_trinh_bay_survey')"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                        :to="{name: 'SurveyPresent', params: {survey_id: sur.id}}">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-arrow-alt-right"></i></span>
                                                </router-link>

                                                <router-link
                                                        :title="trans.get('keys.them_cau_hoi_vao_survey')"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                        :to="{name: 'QuestionCreate', params: {survey_id: sur.id}}">
                                                    <span class="btn-icon-wrap"><i class="fal fa-question"></i></span>
                                                </router-link>

                                                <router-link
                                                        :title="trans.get('keys.sua_thong_tin_khao_sat')"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                        :to="{name: 'SurveyDetail', params: {survey_id: sur.id}}">
                                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                </router-link>

                                                <button :title="trans.get('keys.copy_link')" data-toggle="modal"
                                                        data-target="#delete-ph-modal1"
                                                        @click="getLink(sur.id)"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2">
                                                    <span class="btn-icon-wrap"><i class="fal fa-copy"></i></span>
                                                </button>

                                                <button :title="trans.get('keys.xoa')" data-toggle="modal"
                                                        data-target="#delete-ph-modal"
                                                        @click="deletePost(sur.id)"
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
    import Ls from './../../services/ls'
    import datePicker from 'vue-bootstrap-datetimepicker'

    export default {
        components: {
            Ls,
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
                axios.post('/api/survey/list', {
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
                let back = this.getParamsBackPage();
                if (back == '1') {
                    this.current = Number(sessionStorage.getItem('surveyPage'));
                    this.row = Number(sessionStorage.getItem('surveyPageSize'));
                    this.keyword = sessionStorage.getItem('surveyKeyWord');

                    sessionStorage.clear();
                    this.$route.params.back_page = null;
                }
                this.getSurveys();
            },
            getLink(id) {
                let current_pos = this;
                let obj = Ls.get('auth.user');
                if (obj && obj !== 'undefined') {
                    var user_info = JSON.parse(obj);
                    this.domain = user_info.domain;
                }

                var $temp = $("<input>");

                var url = this.domain + 'survey/present/' + id;
                $("body").append($temp);
                $temp.val(url).select();
                document.execCommand("copy");
                $temp.remove();
                alert("Copied to clipboard");
            },
            getParamsBackPage() {
                return this.$route.params.back_page;
            },
            setParamsBackPage(value) {
                this.$route.params.back_page = value;
            },
            deletePost(id) {
                // sessionStorage.setItem('surveyPage', this.current);
                // sessionStorage.setItem('surveyPageSize', this.row);
                // sessionStorage.setItem('surveyKeyWord', this.keyword);
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/survey/delete', {survey_id: id})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                                if (current_pos.surveys.length == 1) {
                                    current_pos.current = current_pos.current > 1 ? current_pos.current - 1 : 1;
                                }
                                // current_pos.onPageChange();
                                current_pos.getSurveys(current_pos.current);
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
            // this.getSurveys();
        },
        destroyed() {
            sessionStorage.setItem('surveyPage', this.current);
            sessionStorage.setItem('surveyPageSize', this.row);
            sessionStorage.setItem('surveyKeyWord', this.keyword);
        }
    }
</script>

<style scoped>

</style>
