<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">

                    <h4 class="hk-sec-title">{{trans.get('keys.survey')}}: {{survey.code}} - {{survey.name}}</h4>
                    <div class="hk-sec-title" v-html="survey.description">
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div v-for="(question,index) in survey.questions">
                                    <div v-if="question.type_question==='multiplechoice'">
                                        <multiple-choice :question="question" :index_question="index"
                                                         :question_answers="question_answers"></multiple-choice>
                                    </div>
                                    <div v-else-if="question.type_question==='ddtotext'">
                                        <d-d-to-text :question="question" :index_question="index"
                                                     :question_answers="question_answers"></d-d-to-text>
                                    </div>
                                    <div v-else-if="question.type_question==='group'">
                                        <group-question :question="question" :index_question="index"
                                                        :question_answers="question_answers"></group-question>
                                    </div>
                                    <div v-else-if="question.type_question==='minmax'">
                                        <min-max-question :question="question" :index_question="index"
                                                          :question_answers="question_answers"></min-max-question>
                                    </div>
                                    <div v-else-if="question.type_question==='checkbox'">
                                        <checkbox-question :question="question" :index_question="index"
                                                           :question_answers="question_answers"></checkbox-question>
                                    </div>
                                </div>
                            </div>
                            <div class="button-list">
                                <button @click="submitAnswer()" type="button" class="btn btn-primary">
                                    {{trans.get('keys.gui')}}
                                </button>
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
    import MultipleChoice from "./template/MultipleChoiceComponent";
    import DDToText from "./template/DDToTextComponent";
    import GroupQuestion from "./template/GroupQuestionComponent";
    import MinMaxQuestion from "./template/MinMaxComponent"
    import CheckboxQuestion from "./template/CheckboxQuestionComponent"

    export default {
        props: ['survey_id'],
        components: {
            Ls,
            MultipleChoice,
            DDToText,
            GroupQuestion,
            MinMaxQuestion,
            CheckboxQuestion
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
                let current_pos = this;
                let obj = Ls.get('auth.user');
                let user_id = '';
                if (obj && obj !== 'undefined') {
                    var user_info = JSON.parse(obj);
                    user_id = user_info.id;
                }

                if (user_id === '') {
                    toastr['error'](current_pos.trans.get('keys.expired_session'), current_pos.trans.get('keys.that_bai'));
                    return;
                }


                axios.post('/api/survey/submit_resultlms/' + this.survey_id, {
                    question_answers: this.question_answers,
                    ddtotext: this.survey,
                    user_id: user_id
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            // window.history.back();
                            this.$router.push({name: 'SurveyResult', params: {survey_id: this.survey_id}});
                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
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
