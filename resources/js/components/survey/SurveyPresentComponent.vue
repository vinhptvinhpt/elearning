<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">

                    <div class="row">
                        <div class="col-sm">
                            <div class="button-list">
                                <router-link :to="{name: 'SurveyIndex', params: {survey_id: survey_id, back_page:'1'}}"
                                             class="btn-sm btn-danger">
                                    {{trans.get('keys.quay_lai')}}
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <br/>

                    <h4 class="hk-sec-title">{{trans.get('keys.survey')}}: {{survey.code}} - {{survey.name}}</h4>
                    <div class="hk-sec-title" v-html="survey.description">
                    </div>
                    <!--                    <div class="hk-sec-title">{{trans.get('keys.thoi_gian_ket_thuc')}}: {{survey.enddate |-->
                    <!--                        convertDateTime}}-->
                    <!--                    </div>-->
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
                                <router-link :to="{name: 'SurveyIndex', params: {survey_id: survey_id, back_page:'1'}}"
                                             class="btn btn-secondary">
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
    import MinMaxQuestion from "./template/MinMaxComponent"
    import CheckboxQuestion from "./template/CheckboxQuestionComponent"

    export default {
        props: ['survey_id'],
        components: {
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

                axios.post('/api/survey/submit_result/' + this.survey_id, {
                    question_answers: this.question_answers,
                    ddtotext: this.survey
                })
                    .then(response => {
                        if (response.data.status) {
                            toastr['success'](response.data.message, current_pos.trans.get('keys.thanh_cong'));
                            window.history.back();
                        } else {
                            toastr['error'](response.data.message, current_pos.trans.get('keys.that_bai'));
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            }
        },
        mounted() {
            this.getSurvey();
            // this.viewSurvey();
        }
    }
</script>

<style scoped>

</style>
