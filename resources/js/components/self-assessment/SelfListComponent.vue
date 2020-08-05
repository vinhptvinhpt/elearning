<template>

    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.quan_tri_self') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_self')}}</h5>

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
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_or_ma_self')+' ...'"/>
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary btn-sm"
                                                        @click="getSurveys(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

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
                                                <router-link to="/tms/self/create">
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
                                            <th style="width: 20%;">{{trans.get('keys.ma_self')}}</th>
                                            <th style="width: 40%;">{{trans.get('keys.ten_self')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(sur,index) in surveys">
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>
                                                <router-link
                                                        :to="{name: 'SelfStatistic', params: {self_id: sur.id}}">{{
                                                    sur.code }}
                                                </router-link>
                                            </td>
                                            <td>{{ sur.name }}</td>
                                            <td class="text-center">

                                                <router-link
                                                        :title="trans.get('keys.giao_dien_trinh_bay_self')"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                        :to="{name: 'SelfPresent', params: {self_id: sur.id}}">
                                                    <span class="btn-icon-wrap"><i
                                                            class="fal fa-arrow-alt-right"></i></span>
                                                </router-link>

                                                <router-link
                                                        :title="trans.get('keys.them_cau_hoi_vao_self')"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                        :to="{name: 'SelfQuestionCreate', params: {self_id: sur.id}}">
                                                    <span class="btn-icon-wrap"><i class="fal fa-question"></i></span>
                                                </router-link>

                                                <router-link
                                                        :title="trans.get('keys.sua_thong_tin_self')"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                        :to="{name: 'SelfEdit', params: {self_id: sur.id}}">
                                                    <span class="btn-icon-wrap"><i class="fal fa-pencil"></i></span>
                                                </router-link>

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

    export default {
        components: {},
        data() {
            return {
                surveys: [],
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 5,
                startdate: '',
                enddate: ''
            }
        },
        methods: {
            getSurveys(paged) {
                axios.post('/api/self/list', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
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
                if(back == '1'){
                  this.current = Number(sessionStorage.getItem('selfListPage'));
                  this.row = Number(sessionStorage.getItem('selfListPageSize'));
                  this.keyword = sessionStorage.getItem('selfListKeyWord');

                  sessionStorage.clear();
                  this.$route.params.back_page= null;
                }
                this.getSurveys();
            },
            getParamsBackPage() {
              return this.$route.params.back_page;
            },
            setParamsBackPage(value) {
              this.$route.params.back_page = value;
            },
            deletePost(id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/self/delete', {id: id})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
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
          sessionStorage.setItem('selfListPage', this.current);
          sessionStorage.setItem('selfListPageSize', this.row);
          sessionStorage.setItem('selfListKeyWord', this.keyword);
        }
    }
</script>

<style scoped>

</style>
