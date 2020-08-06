<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <div class="row">
                        <div class="col-sm">
                            <div class="button-list">
                                <router-link :to="{name: 'SelfIndex',params: {back_page: '1',self_id: self_id}}"
                                             class="btn-sm btn-danger">
                                    {{trans.get('keys.quay_lai')}}
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <br/>

                    <h4 class="hk-sec-title">{{trans.get('keys.self')}}: {{survey.code}} -
                        {{survey.name}}</h4>
                    <div class="hk-sec-title" v-html="survey.description">
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div v-for="(question,index) in survey.questions">

                                    <div v-if="question.type_question==='group'">
                                        <group-question :question="question" :index_question="index"
                                                        :question_answers="question_answers"
                                                        :group_ques="group_ques"
                                                        :minmax_gr="minmax_gr"></group-question>
                                    </div>
                                    <div v-else-if="question.type_question==='minmax'">
                                        <min-max-question :question="question" :index_question="index"
                                                          :question_answers="question_answers"
                                                          :group_ques="group_ques"
                                                          :minmax_gr="minmax_gr"></min-max-question>
                                    </div>
                                </div>
                            </div>
                            <div class="button-list">
                                <button @click="submitAnswer()" type="button" class="btn btn-primary">
                                    {{trans.get('keys.gui')}}
                                </button>
                                <router-link :to="{name: 'SelfIndex',params: {back_page: '1',  self_id: self_id}}"
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

    import GroupQuestion from "./template/present/GroupQuestionComponent";
    import MinMaxQuestion from "./template/present/MinMaxComponent"

    export default {
        props: ['self_id'],
        components: {
            GroupQuestion,
            MinMaxQuestion
        },
        data() {
            return {
                survey: '',
                question_answers: [],
                group_ques: [],
                minmax_gr: []
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
                axios.get('/api/self/viewlayout/' + this.self_id)
                    .then(response => {
                        this.survey = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data);
                    });
            },
            submitAnswer() {
                let current_pos = this;

                axios.post('/api/self/submit_result/' + this.self_id, {
                    question_answers: this.question_answers,
                    group_ques: this.group_ques,
                    minmax_gr: this.minmax_gr
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
            },
            viewSurvey() {
                axios.post('/bridge/bonus', {
                    survey_id: this.self_id,
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
            // this.viewSurvey();
        }
    }
</script>

<style scoped>

</style>
