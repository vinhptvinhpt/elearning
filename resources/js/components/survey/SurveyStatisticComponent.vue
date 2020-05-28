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
                        <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_thong_tin_khao_sat') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mx-0">

            <div class="col-12 hk-sec-wrapper">
                <div class="row">
                    <div class="col-sm">
                        <div class="button-list">
                            <router-link :to="{name: 'SurveyIndex', params: {survey_id: survey_id}}"
                                         class="btn-sm btn-danger">
                                {{trans.get('keys.quay_lai')}}
                            </router-link>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-12">
                        <section class="hk-sec-wrapper">
                            <h4>{{trans.get('keys.tong_so_nguoi_tham_gia_danh_gia_khao_sat')}}: {{total_view}}</h4>
                            <br/>
                            <h5 class="hk-sec-title">
                                {{trans.get('keys.thong_ke_ket_qua_khao_sat_so_nguoi_chon_dap_an_tong_so')}}</h5>

                            <div class="row">
                                <div class="col-6 form-group">
                                    <div class="d-flex flex-row form-group">
                                        <treeselect v-model="organization.parent_id" :multiple="false"
                                                    :options="options" id="organization_parent_id"/>
                                    </div>
                                </div>
                                <div class="col-3 form-group">

                                    <div class="d-flex flex-row form-group">
                                        <button type="button" id="btnFilter"
                                                class="btn btn-primary d-none d-lg-block"
                                                @click="search()">
                                            {{trans.get('keys.tim')}}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-3 form-group">
                                    <label>{{trans.get('keys.ngay_bat_dau')}}</label>
                                    <form v-on:submit.prevent="search()">
                                        <div class="d-flex flex-row form-group">
                                            <input type="date" id="inputStart" v-model="startdate" class="form-control">&nbsp;
                                        </div>
                                    </form>
                                </div>
                                <div class="col-3 form-group">
                                    <label>{{trans.get('keys.ngay_ket_thuc')}} </label>
                                    <form v-on:submit.prevent="search()">
                                        <div class="d-flex flex-row form-group">
                                            <input
                                                    type="date" id="inputEnd" v-model="enddate" class="form-control">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2 form-group">

                                    <!--                                <a :href="'/survey/export_file/'+survey_id+'/'+branch_id+'/'+saleroom_id+'/pdf'">-->
                                    <button type="button" id="btnExportPdf"
                                            class="btn-sm btn-pdf btn-primary d-none d-lg-block"
                                            @click="exportFileData('pdf')">
                                        {{trans.get('keys.xuat_file_pdf')}} <i class="fa fa-spinner"
                                                                               aria-hidden="true"></i>
                                    </button>
                                    <!--                                </a>-->

                                </div>
                                <div class="col-2 form-group">
                                    <!--                                <a :href="'/survey/export_file/'+survey_id+'/'+branch_id+'/'+saleroom_id+'/excel'">-->
                                    <button type="button" id="btnExportExcel"
                                            class="btn-sm btn-excel btn-primary d-none d-lg-block"
                                            @click="exportFileData('excel')">
                                        {{trans.get('keys.xuat_file_excel')}} <i class="fa fa-spinner"
                                                                                 aria-hidden="true"></i>
                                    </button>
                                    <!--                                </a>-->
                                </div>
                                <div class="col-2 form-group">
                                    <!--                                <a :href="'/survey/export_file/'+survey_id+'/'+branch_id+'/'+saleroom_id+'/excel'">-->

                                    <button type="button" v-if="chart_type==='bar'"
                                            class="btn-sm btn-excel btn-primary d-none d-lg-block"
                                            @click="changeChartFormat('pie')">
                                        {{trans.get('keys.pie_chart')}} <i class="fa fa-spinner"
                                                                           aria-hidden="true"></i>
                                    </button>
                                    <button type="button" v-else
                                            class="btn-sm btn-excel btn-primary d-none d-lg-block"
                                            @click="changeChartFormat('bar')">
                                        {{trans.get('keys.bar_chart')}} <i class="fa fa-spinner"
                                                                           aria-hidden="true"></i>
                                    </button>


                                    <!--                                </a>-->
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="table-wrap">
                                        <!--                                    <div v-if="survey_exam.length>0">-->
                                        <div v-for="(question,index) in survey_exam">
                                            <div v-if="question.type_question=='multiplechoice'">
                                                <question-statistic :question="question.lstQuesChild[0]"
                                                                    :index_question="index" :chart_type="chart_type"
                                                ></question-statistic>
                                            </div>
                                            <div v-if="question.type_question=='minmax'">
                                                <min-max-question-statistic :question="question" :index_question="index"
                                                                            :chart_type="chart_type"
                                                ></min-max-question-statistic>
                                            </div>
                                            <div v-else>
                                                <group-question-statistic :question="question" :index_question="index"
                                                                          :chart_type="chart_type"
                                                ></group-question-statistic>
                                            </div>
                                        </div>
                                        <!--                                    </div>-->

                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    //import vPagination from 'vue-plain-pagination'
    import QuestionStatistic from "./template/StatisticQuestionComponent";
    import GroupQuestionStatistic from "./template/StatisticsGroupQuesComponent";
    import MinMaxQuestionStatistic from "./template/StatisticMinMaxQuesComponent"

    export default {
        props: ['survey_id'],
        components: {QuestionStatistic, GroupQuestionStatistic, MinMaxQuestionStatistic},
        //components: {vPagination},
        data() {
            return {
                organization: {
                    name: '',
                    code: '',
                    parent_id: 0,
                    description: '',
                },
                organization_parent_list: [],
                //Treeselect options
                options: [
                    {
                        id: 0,
                        label: this.trans.get('keys.chon_to_chuc')
                    }
                ],

                keyword: '',
                row: 5,
                current: 1,
                total_view: 0,
                totalPages: 1,
                survey_exam: [],

                startdate: '',
                enddate: '',
                base_url: '',
                chart_type: 'pie'
            }
        },
        methods: {
            changeChartFormat(type) {
                this.chart_type = type;
                this.survey_exam = [];
                this.getStatictisSurveyExam();
            },

            selectParentItem(parent_id) {
                this.organization.parent_id = parent_id;
            },
            selectParent() {
                $('.content_search_box').addClass('loadding');
                axios.post('/organization/list', {
                    keyword: this.parent_keyword,
                    level: 1, // lấy cấp lơn nhất only, vì đã đệ quy
                    paginated: 0 //không phân trang,
                })
                    .then(response => {
                        this.organization_parent_list = response.data;
                        //Set options recursive
                        this.options = this.setOptions(response.data);
                        $('.content_search_box').removeClass('loadding');
                    })
                    .catch(error => {
                        $('.content_search_box').removeClass('loadding');
                    })
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

            fetch() {
                axios.post('/bridge/fetch', {
                    view: 'SurveyStatistic'
                })
                    .then(response => {
                        this.base_url = response.data.base_url;
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },

            getPropertyName(arrData) {
                return Object.getOwnPropertyNames(arrData);
            },
            search() {
                this.getStatictisSurveyView();
                this.getStatictisSurveyExam();
            },
            getStatictisSurveyExam() {
                axios.post('/api/survey/statistic_exam', {
                    survey_id: this.survey_id,
                    organization_id: this.organization.parent_id,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        this.survey_exam = [];
                        var data_result = response.data;
                        var keys = this.getPropertyName(data_result);
                        var count_key = keys.length;
                        if (count_key > 0) {
                            for (var i = 0; i < count_key; i++) {
                                var objData = data_result[keys[i]];
                                this.survey_exam.push(objData);
                            }
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            getStatictisSurveyView() {
                axios.post('/api/survey/statistic_view', {
                    survey_id: this.survey_id,
                    organization_id: this.organization.parent_id,
                    startdate: this.startdate,
                    enddate: this.enddate
                }).then(response => {
                    this.total_view = response.data.length;
                })
                    .catch(error => {
                        console.log(error.response.data);
                    });

            },
            exportFileData(type_file) {
                if (type_file === 'pdf') {
                    $('button.btn-pdf i').css("display", "inline-block");
                } else {
                    $('button.btn-excel i').css("display", "inline-block");
                }
                axios.post('/api/survey/export_file', {
                    survey_id: this.survey_id,
                    organization_id: this.organization.parent_id,
                    type_file: type_file,
                    startdate: this.startdate,
                    enddate: this.enddate
                })
                    .then(response => {
                        var a = $("<a>")
                            .prop("href", "/downloadexcelsurvey/" + type_file)
                            .appendTo("body");
                        a[0].click();
                        a.remove();

                        if (type_file === 'pdf') {
                            $('button.btn-pdf i').css("display", "none");
                        } else {
                            $('button.btn-excel i').css("display", "none");
                        }


                    })
                    .catch(error => {
                        if (type_file === 'pdf') {
                            $('button.btn-pdf i').css("display", "none");
                        } else {
                            $('button.btn-excel i').css("display", "none");
                        }
                    });
            }
        },
        mounted() {
            this.selectParent();
            this.getStatictisSurveyExam();
            this.getStatictisSurveyView();
            this.fetch();
        }
    }
</script>

<style scoped>

</style>
