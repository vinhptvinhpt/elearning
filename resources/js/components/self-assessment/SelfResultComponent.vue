<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">
                    <h4 class="hk-sec-title">{{trans.get('keys.self')}}: {{self.code}} - {{self.name}}</h4>
                    <br/>
                    <h6>{{trans.get('keys.learner')}}: {{user.fullname}}</h6>
                    <h6>{{trans.get('keys.email')}}: {{user.email}}</h6><br/>

                    <div class="table-responsive">
                        <table class="table_res">
                            <thead>
                            <tr>
                                <th style="width: 15%; color: #007bff;">{{trans.get('keys.ques_name')}}</th>
                                <th style="width: 30%; color: #007bff;">{{trans.get('keys.ques_title')}}</th>
                                <th style="width: 15%; color: #007bff;">{{trans.get('keys.sec_name')}}</th>
                                <th style="width: 15%; color: #007bff;">{{trans.get('keys.total_point')}}</th>
                                <th style="width: 15%; color: #007bff;">{{trans.get('keys.avg_point')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(sur,index) in points">
                                <td>{{ sur.ques_name }}</td>
                                <td>
                                    <div v-html="sur.ques_content" style="font-weight: normal;"></div>
                                </td>
                                <td>{{ sur.section_name }}</td>
                                <td>{{ sur.total_point }}</td>
                                <td>{{ sur.avg_point.toFixed(2) }}</td>

                            </tr>
                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    </div>

                    <br/>

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
        props: ['self_id'],
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
                points: []
            }
        },
        methods: {
            getSurvey() {
                let obj = Ls.get('auth.user');
                let user_id = '';
                if (obj && obj !== 'undefined') {
                    var user_info = JSON.parse(obj);
                    user_id = user_info.id;
                }
                // user_id = 23973;
                let course_id = Ls.get('courseid');

                // course_id = 880;
                this.getLeanerInfo(user_id);

                axios.post('/api/self/view-result', {
                    self_id: this.self_id,
                    user_id: user_id,
                    course_id: course_id
                })
                    .then(response => {
                        this.self = response.data.survey;
                        this.question_answers = response.data.result;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getPointOfSection() {
                let obj = Ls.get('auth.user');
                let user_id = '';
                if (obj && obj !== 'undefined') {
                    var user_info = JSON.parse(obj);
                    user_id = user_info.id;
                }
                // user_id = 23973;
                let course_id = Ls.get('courseid');

                axios.post('/api/self/point-of-section', {
                    self_id: this.self_id,
                    user_id: user_id,
                    course_id: course_id
                })
                    .then(response => {
                        this.points = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getLeanerInfo(user_id) {

                axios.post('/api/system/user/info', {
                    user_id: user_id
                })
                    .then(response => {
                        this.user = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
        },
        mounted() {
            this.getPointOfSection();
            this.getSurvey();
        }
    }
</script>

<style scoped>

</style>
