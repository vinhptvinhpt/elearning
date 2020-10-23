<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h4 class="hk-sec-title">{{trans.get('keys.self')}}: {{self.code}} - {{self.name}}

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
                    <div class="hk-sec-title" v-html="self.description">
                    </div>

                    <br/>
                    <div class="row">
                        <div class="col-sm">
                            <div class="table-wrap">
                                <div v-for="(question,index) in self.questions">
                                    <div v-if="question.type_question==='group'">
                                        <group-question :question="question" :index_question="index"
                                                        :question_answers="question_answers"></group-question>
                                    </div>
                                    <div v-else-if="question.type_question==='minmax'">
                                        <min-max-question :question="question" :index_question="index"
                                                          :question_answers="question_answers"></min-max-question>
                                    </div>
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
    import GroupQuestion from "./result/GroupResultComponent";
    import MinMaxQuestion from "./result/MinMaxComponent"

    export default {
        props: ['self_id', 'user_id', 'course_id'],
        components: {
            Ls,
            GroupQuestion,
            MinMaxQuestion
        },
        data() {
            return {
                self: '',
                question_answers: [],
                user: '',
            }
        },
        methods: {
            getSurvey() {

                this.getLeanerInfo(this.user_id);

                axios.post('/api/self/view-result', {
                    self_id: this.self_id,
                    user_id: this.user_id,
                    course_id: this.course_id
                })
                    .then(response => {
                        this.self = response.data.survey;
                        this.question_answers = response.data.result;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getLeanerInfo(user_id) {

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
            this.getSurvey();
        }
    }
</script>

<style scoped>

</style>
