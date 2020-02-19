<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col">
        <nav class="breadcrumb" aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item"><router-link to="/tms/dashboard">{{ trans.get('keys.dashboard') }}</router-link></li>
            <li class="breadcrumb-item"><router-link to="/tms/survey/list">{{ trans.get('keys.quan_tri_survey') }}</router-link></li>
            <li class="breadcrumb-item active">{{ trans.get('keys.thong_ke_thong_tin_khao_sat') }}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row mx-0">

      <div class="col-12 hk-sec-wrapper">
        <div class="row">
          <div class="col-12">
            <section class="hk-sec-wrapper">
              <h4>{{trans.get('keys.tong_so_nguoi_tham_gia_danh_gia_khao_sat')}}: {{total_view}}</h4>
              <br/>
              <h5 class="hk-sec-title">
                {{trans.get('keys.thong_ke_ket_qua_khao_sat_so_nguoi_chon_dap_an_tong_so')}}</h5>
              <div class="row">
                <div class="col-3 form-group">
                  <label for="province_id">{{trans.get('keys.danh_sach_tinh_thanh')}} </label>
                  <select v-model="province_id" class="form-control" id="province_id"
                          @change="getLstBranch(province_id)">
                    <option value="">{{trans.get('keys.chon_tinh_thanh')}}</option>
                    <option v-for="pro in provinces" :value="pro.id">
                      {{pro.name}}
                    </option>
                  </select>
                </div>
                <div class="col-3 form-group">
                  <label for="branch_id">{{trans.get('keys.danh_sach_dai_ly')}} </label>
                  <select v-model="branch_id" class="form-control" id="branch_id"
                          @change="getLstSaleRoom(branch_id)">
                    <option value="nohave">{{trans.get('keys.chon_dai_ly')}}</option>
                    <option v-for="br in branchs" :value="br.id">
                      {{br.name}}
                    </option>
                  </select>
                </div>
                <div class="col-3 form-group">
                  <label for="branch_id">{{trans.get('keys.danh_sach_diem_ban')}} </label>
                  <select v-model="saleroom_id" class="form-control" id="saleroom_id">
                    <option value="nohave">{{trans.get('keys.chon_diem_ban')}}</option>
                    <option v-for="sr in salerooms" :value="sr.id">
                      {{sr.name}}
                    </option>
                  </select>
                </div>
                <div class="col-2 form-group">
                  <label for="branch_id"> </label>
                  <form v-on:submit.prevent="search()">
                    <div class="d-flex flex-row form-group">
                      <button type="button" id="btnFilter" class="btn btn-primary d-none d-lg-block"
                              style="margin-top:10px;"
                              @click="search()">
                        {{trans.get('keys.tim')}}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
              <div class="row">
                <div class="col-3 form-group">
                  <label for="branch_id">{{trans.get('keys.ngay_bat_dau')}}</label>
                  <form v-on:submit.prevent="search()">
                    <div class="d-flex flex-row form-group">
                      <input type="date" id="inputStart" v-model="startdate" class="form-control">&nbsp;
                    </div>
                  </form>
                </div>
                <div class="col-3 form-group">
                  <label for="branch_id">{{trans.get('keys.ngay_ket_thuc')}} </label>
                  <form v-on:submit.prevent="search()">
                    <div class="d-flex flex-row form-group">
                      <input
                        type="date" id="inputEnd" v-model="enddate" class="form-control">
                    </div>
                  </form>
                </div>
              </div>
              <div class="row">
                <div class="col-2">

                  <a :href="'/survey/export_file/'+survey_id+'/'+branch_id+'/'+saleroom_id+'/pdf'">
                    <button type="button" id="btnExportPdf" class="btn-sm btn-primary d-none d-lg-block"
                            style="margin-top:10px;">
                      {{trans.get('keys.xuat_file_pdf')}}
                    </button>
                  </a>

                </div>
                <div class="col-2 ">
                  <a :href="'/survey/export_file/'+survey_id+'/'+branch_id+'/'+saleroom_id+'/excel'">
                    <button type="button" id="btnExportExcel"
                            class="btn-sm btn-primary d-none d-lg-block"
                            style="margin-top:10px;">
                      {{trans.get('keys.xuat_file_excel')}}
                    </button>
                  </a>
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
                                            :index_question="index"
                        ></question-statistic>
                      </div>
                      <div v-else>
                        <group-question-statistic :question="question" :index_question="index"
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

    export default {
        props: ['survey_id'],
        components: {QuestionStatistic, GroupQuestionStatistic},
        //components: {vPagination},
        data() {
            return {
                keyword: '',
                row: 5,
                current: 1,
                total_view: 0,
                totalPages: 1,
                survey_exam: [],
                provinces: [],
                branchs: [],
                salerooms: [],
                province_id: '',
                branch_id: 'nohave',
                saleroom_id: 'nohave',
                startdate: '',
                enddate: '',
                base_url: ''
            }
        },
        methods: {
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
            onPageChange() {
                this.getStatictisSurveyView();
            },
            getPropertyName(arrData) {
                return Object.getOwnPropertyNames(arrData);
            },
            getLstProvince() {
                axios.get('/api/survey/getlstprovinces')
                    .then(response => {
                        this.provinces = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getLstBranch(province_id) {
                axios.get('/api/survey/getlstbranchs/' + province_id)
                    .then(response => {
                        this.branchs = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            getLstSaleRoom(branch_id) {
                axios.get('/api/survey/getlstsalerooms/' + branch_id)
                    .then(response => {
                        this.salerooms = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            search() {
                this.getStatictisSurveyView();
                this.getStatictisSurveyExam();
            },
            getStatictisSurveyExam() {
                axios.post('/api/survey/statistic_exam', {
                    survey_id: this.survey_id,
                    province_id: this.province_id,
                    branch_id: this.branch_id,
                    saleroom_id: this.saleroom_id,
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
                axios.post('/api/survey/export_file', {
                    survey_id: this.survey_id,
                    province_id: this.province_id,
                    branch_id: this.branch_id,
                    saleroom_id: this.saleroom_id,
                    type_file: type_file
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
            }
        },
        mounted() {
            this.getLstProvince();
            this.getStatictisSurveyExam();
            this.getStatictisSurveyView();
            this.fetch();
        }
    }
</script>

<style scoped>

</style>
