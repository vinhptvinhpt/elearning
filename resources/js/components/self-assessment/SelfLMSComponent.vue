<template>
    <div class="container-fluid mt-15">
        <div class="row">
            <div class="col-xl-12">
                <section class="hk-sec-wrapper">

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
    import GroupQuestion from "./template/present/GroupQuestionComponent";
    import MinMaxQuestion from "./template/present/MinMaxComponent"

    export default {
        props: ['self_id'],
        components: {
            Ls,
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
            checkResult() {
                let current_pos = this;
                let obj = Ls.get('auth.user');
                let user_id = '';
                if (obj && obj !== 'undefined') {
                    var user_info = JSON.parse(obj);
                    user_id = user_info.id;
                }
                // user_id = 23973;
                if (user_id === '') {
                    toastr['error'](current_pos.trans.get('keys.expired_session'), current_pos.trans.get('keys.that_bai'));
                    return;
                }

                let course_id = Ls.get('courseid');
                // course_id = 880;
                if (course_id === '' || course_id === null || course_id === undefined) {
                    toastr['error'](current_pos.trans.get('keys.paste_link'), current_pos.trans.get('keys.that_bai'));
                    return;
                }

                axios.post('/api/self/check-result', {
                    self_id: this.self_id,
                    user_id: user_id,
                    course_id: course_id
                })
                    .then(response => {
                        var _this = this;
                        if (response.data.status) {
                            _this.$router.push({name: 'SelfResult', params: {self_id: current_pos.self_id}});
                        } else {
                            this.getSurvey();
                        }
                    })
                    .catch(error => {
                        toastr['error'](current_pos.trans.get('keys.loi_he_thong_thao_tac_that_bai'), current_pos.trans.get('keys.thong_bao'));
                    });
            },
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
                let obj = Ls.get('auth.user');
                let user_id = '';
                if (obj && obj !== 'undefined') {
                    var user_info = JSON.parse(obj);
                    user_id = user_info.id;
                }
                // user_id = 23973;
                if (user_id === '') {
                    toastr['error'](current_pos.trans.get('keys.expired_session'), current_pos.trans.get('keys.that_bai'));
                    return;
                }

                let course_id = Ls.get('courseid');
                // course_id = 880;
                if (course_id === '' || course_id === null || course_id === undefined) {
                    toastr['error'](current_pos.trans.get('keys.paste_link'), current_pos.trans.get('keys.that_bai'));
                    return;
                }

                axios.post('/api/self/submit_resultlms/' + this.self_id, {
                    user_id: user_id,
                    course_id: course_id,
                    question_answers: this.question_answers,
                    group_ques: this.group_ques,
                    minmax_gr: this.minmax_gr
                })
                    .then(response => {
                        var _this = this;
                        if (response.data.status) {
                            swal({
                                title: current_pos.trans.get('keys.thong_bao'),
                                text: current_pos.trans.get('keys.thanh_cong'),
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true
                            }, function () {
                                _this.$router.push({name: 'SelfResult', params: {self_id: current_pos.self_id}});
                            });
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
            this.checkResult();
        }
    }
</script>

<style scoped>

</style>
