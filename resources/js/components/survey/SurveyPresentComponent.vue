<template>
  <div class="container-fluid mt-15">
    <div class="row">
      <div class="col-xl-12">
        <section class="hk-sec-wrapper">

          <div class="row">
            <div class="col-sm">
              <div class="button-list">
                <router-link :to="{name: 'SurveyIndex', params: {survey_id: survey_id}}" class="btn-sm btn-danger">
                  {{trans.get('keys.quay_lai')}}
                </router-link>
              </div>
            </div>
          </div>
          <br/>

          <h4 class="hk-sec-title">{{trans.get('keys.survey')}}: {{survey.code}} - {{survey.name}}</h4>
          <div class="hk-sec-title">{{trans.get('keys.thoi_gian_bat_dau')}}: {{survey.startdate |
            convertDateTime}}
          </div>
          <div class="hk-sec-title">{{trans.get('keys.thoi_gian_ket_thuc')}}: {{survey.enddate |
            convertDateTime}}
          </div>
          <br/>
          <div class="row">
            <div class="col-sm">
              <div class="table-wrap">
                <div v-for="(question,index) in survey.questions">
                  <div v-if="question.type_question=='multiplechoice'">
                    <multiple-choice :question="question" :index_question="index"
                                     :question_answers="question_answers"></multiple-choice>
                  </div>
                  <div v-else-if="question.type_question=='ddtotext'">
                    <d-d-to-text :question="question" :index_question="index"
                                 :question_answers="question_answers"></d-d-to-text>
                  </div>
                  <div v-else-if="question.type_question=='group'">
                    <group-question :question="question" :index_question="index"
                                    :question_answers="question_answers"></group-question>
                  </div>
                </div>
              </div>
              <div class="button-list">
                <button @click="submitAnswer()" type="button" class="btn btn-primary">
                  {{trans.get('keys.gui')}}
                </button>
                <router-link :to="{name: 'SurveyIndex', params: {survey_id: survey_id}}" class="btn btn-secondary">
                  {{trans.get('keys.huy')}}
                </router-link>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>

    import MultipleChoice from "./template/MultipleChoiceComponent";
    import DDToText from "./template/DDToTextComponent";
    import GroupQuestion from "./template/GroupQuestionComponent";

    export default {
        props: ['survey_id'],
        components: {
          MultipleChoice,
          DDToText,
          GroupQuestion
        },
        data() {
            return {
                survey: '',
                question_answers: []
            }
        },
        filters: {
            convertDateTime(value) {
                var time = new Date(value * 1000);
                return time.toLocaleDateString();
            }
        },
        methods: {
            getSurvey() {
                axios.get('/api/survey/viewlayout/' + this.survey_id)
                    .then(response => {
                        this.survey = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            submitAnswer() {
                axios.post('/api/survey/submit_result/' + this.survey_id, {
                    question_answers: this.question_answers,
                    ddtotext: this.survey
                })
                    .then(response => {
                        if (response.data.status) {
                            swal({
                                title: response.data.message,
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function () {
                                window.history.back();
                            });
                        } else {
                            swal({
                                title: response.data.message,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            });
                        }
                    })
                    .catch(error => {
                        swal({
                            title: "Thông báo",
                            text: " Lỗi hệ thống.",
                            type: "error",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        });
                    });
            },
            viewSurvey() {
              axios.post('/bridge/bonus', {
                survey_id: this.survey_id,
                view: 'SurveyPresent'
              })
                .then(response => {
                })
                .catch(error => {
                  console.log(error);
                });
            },
        },
        mounted() {
            this.getSurvey();
            this.viewSurvey();
        }
    }
</script>

<style scoped>

</style>
