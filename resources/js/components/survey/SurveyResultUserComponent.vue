<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h4 class="hk-sec-title">{{trans.get('keys.survey')}}: {{survey.code}} - {{survey.name}}

                        <button :title="trans.get('keys.print')" data-toggle="modal" style="float: right;"
                                data-target="#delete-ph-modal"
                                @click="printSurvey()"
                                class="btn btn-sm btn-icon btn-primary btn-icon-style-2">
                            <span class="btn-icon-wrap"><i class="fal fa-print"></i></span>
                        </button>

                    </h4>
                    <br/>
                    <h6>{{trans.get('keys.learner')}}: {{user.fullname}}</h6>
                    <h6>{{trans.get('keys.email')}}: {{user.email}}</h6><br/>
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
                                    <br/>
                                </div>
                            </div>
                            <div class="button-list">
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>

    import MultipleChoice from "./result/MultipleChoiceComponent";
    import DDToText from "./result/DDToTextComponent";
    import GroupQuestion from "./result/GroupQuestionComponent";
    import MinMaxQuestion from "./result/MinMaxComponent"
    import CheckboxQuestion from "./result/CheckboxQuestionComponent"

    export default {
        props: ['survey_id', 'user_id'],
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
                question_answers: [],
                user: ''
            }
        },
        methods: {
            getSurvey() {
                axios.post('/api/survey/view_result', {
                    survey_id: this.survey_id,
                    user_id: this.user_id
                })
                    .then(response => {
                        this.survey = response.data.survey;
                        this.question_answers = response.data.result;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getLeanerInfo() {
                axios.post('/api/system/user/info', {
                    user_id: this.user_id
                })
                    .then(response => {
                        this.user = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            printSurvey() {
                window.print();
            }
        },
        mounted() {
            this.getLeanerInfo();
            this.getSurvey();
        }
    }
</script>

<style scoped>

</style>
