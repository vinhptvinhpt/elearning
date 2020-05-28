<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col">
                <nav class="breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent px-0">
                        <li class="breadcrumb-item">
                            <router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{ trans.get('keys.danh_sach_cau_hoi') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h5 class="hk-sec-title">{{trans.get('keys.danh_sach_cau_hoi')}}</h5>

                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div class="row">
                                    <!--                                <div class="col-6">-->
                                    <!--                                    <div class="dataTables_length">-->

                                    <!--                                    </div>-->
                                    <!--                                </div>-->
                                    <div class="col-6">
                                        <form v-on:submit.prevent="getQuestions(1)">
                                            <div class="d-flex flex-row form-group">
                                                <input v-model="keyword" type="text"
                                                       class="form-control"
                                                       :placeholder="trans.get('keys.nhap_thong_tin_tim_kiem_theo_ten_cau_hoi')+' ...'">
                                                <button type="button" id="btnFilter"
                                                        class="btn btn-primary d-none d-lg-block"
                                                        @click="getQuestions(1)">
                                                    {{trans.get('keys.tim')}}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <select v-model="survey_id" class="form-control" id="survey_id">
                                            <option value="">{{trans.get('keys.chon_survey')}}</option>
                                            <option v-for="sur in surveys" :value="sur.id">
                                                {{sur.code}} - {{sur.name}}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select v-model="type_question"
                                                class="form-control" id="type_question">
                                            <option value="">{{trans.get('keys.chon_loai_cau_hoi')}}</option>
                                            <option value="multiplechoice">{{trans.get('keys.lua_chon_dap_an')}}
                                            </option>
                                            <option value="ddtotext">{{trans.get('keys.dien_cau_tra_loi')}}</option>
                                            <option value="group">{{trans.get('keys.cau_hoi_nhom')}}</option>
                                            <option value="minmax">{{trans.get('keys.cau_hoi_min_max')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-6 dataTables_wrapper">
                                        <div class="dataTables_length">
                                            <label>{{trans.get('keys.hien_thi')}}
                                                <select v-model="row"
                                                        class="custom-select custom-select-sm form-control form-control-sm"
                                                        @change="getQuestions(1)">
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
                                                <router-link to="/tms/question/create/off">
                                                    <button type="button"
                                                            class="btn btn-success"
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
                                            <th style="width: 10%;">{{trans.get('keys.ten_cau_hoi')}}</th>
                                            <th style="width: 20%;">{{trans.get('keys.loai_cau_hoi')}}</th>
                                            <th>{{trans.get('keys.survey')}}</th>
                                            <th class="text-center">{{trans.get('keys.hanh_dong')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(ques,index) in questions">
                                            <td>{{ (current-1)*row+(index+1) }}</td>
                                            <td>{{ ques.name }}</td>
                                            <td>
                                                <span v-if="ques.type_question=='multiplechoice'">{{trans.get('keys.lua_chon_dap_an')}}</span>
                                                <span v-if="ques.type_question=='ddtotext'">{{trans.get('keys.dien_cau_tra_loi')}}</span>
                                                <span v-if="ques.type_question=='group'">{{trans.get('keys.cau_hoi_nhom')}}</span>
                                                <span v-if="ques.type_question=='minmax'">{{trans.get('keys.cau_hoi_min_max')}}</span>
                                            </td>
                                            <td>{{ ques.survey_code }} - {{ ques.survey_name }}</td>
                                            <td class="text-center">
                                                <router-link
                                                        :title="trans.get('keys.sua_thong_tin_khoa_hoc')"
                                                        class="btn btn-sm btn-icon btn-icon-circle btn-success btn-icon-style-2"
                                                        :to="{name:'QuestionDetail', params: {question_id: ques.id}}"><span
                                                        class="btn-icon-wrap"><i
                                                        class="fal fa-pencil"></i></span></router-link>

                                                <button :title="trans.get('keys.xoa')" data-toggle="modal"
                                                        data-target="#delete-ph-modal"
                                                        @click="deletePost(ques.id)"
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

    export default {
        //components: {vPagination},
        data() {
            return {
                survey_id: '',
                surveys: [],
                type_question: '',
                keyword: '',
                current: 1,
                totalPages: 0,
                row: 5,
                questions: []
            }
        },
        methods: {
            getSurveys() {
                axios.get('/api/question/listsurvey')
                    .then(response => {
                        this.surveys = response.data;

                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getQuestions(paged) {
                axios.post('/api/question/list', {
                    page: paged || this.current,
                    keyword: this.keyword,
                    row: this.row,
                    survey_id: this.survey_id,
                    type_question: this.type_question
                })
                    .then(response => {
                        this.questions = response.data.data.data;
                        this.current = response.data.pagination.current_page;
                        this.totalPages = response.data.pagination.total;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            onPageChange() {
                this.getQuestions();
            },
            deletePost(id) {
                let current_pos = this;
                swal({
                    title: this.trans.get('keys.ban_muon_xoa_muc_da_chon'),
                    text: this.trans.get('keys.chon_ok_de_thuc_hien_thao_tac'),
                    type: "success",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function () {
                    axios.post('/api/question/delete', {question_id: id})
                        .then(response => {
                            if (response.data.status) {
                                toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                                current_pos.getQuestions(this.current);

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
            this.getQuestions();
        }
    }
</script>

<style scoped>

</style>
