<template>
    <div class="row">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <div class="col-12">
            <h6>{{trans.get('keys.cau_hoi')}} {{index_question + 1}}: <label
                    v-html="question.question_data[0].content"></label></h6>
        </div>

        <div v-if="is_result_or_raw=== '1'">
            <div class="col-12" v-for="(ans,index) in checkedQuestion" style="padding-left:30px;">
                <div>
                    <input type="checkbox" :id="'customRadio-'+ans.ans_id"
                           :value="ans.ans_id" v-if="ans.checked === '1'" checked>
                    <input type="checkbox" :id="'customRadio-'+ans.ans_id"
                           :value="ans.ans_id" v-else>
                    <label :for="'customRadio-'+ans.ans_id">{{ans.ans_content}}</label>
                </div>
                <br/>
            </div>
        </div>
        <div v-else>
            <div class="col-12" v-for="(ans,index) in question.question_data[0].answers" style="padding-left:30px;">
                <div>
                    <input type="checkbox" :id="'customRadio-'+ans.id"
                           @click="getAnswerChoose(question.question_data[0].id,question.question_data[0].type_question,ans.id,ans.content,question.id)"
                           :value="ans.id">
                    <label :for="'customRadio-'+ans.id">{{ans.content}}</label>
                </div>
                <br/>
            </div>
        </div>

        <br/>
    </div>
</template>

<script>

    export default {
        props: ['question', 'index_question', 'question_answers'],
        data() {
            return {
                checkedQuestion: [],
                index: 0,
                is_result_or_raw: '0'
            }
        },
        methods: {
            getLstCheckedQuestion() {
                let count_ques = this.question.question_data[0].answers.length;
                let count_ans = this.question_answers.length;

                let lstAns = this.question.question_data[0].answers;
                let lstRs = this.question_answers;
                let data_answer;

                if (count_ques > 0 && count_ans > 0) {
                    for (let i = 0; i < count_ques; i++) {
                        for (let j = 0; j < count_ans; j++) {
                            if (lstRs[j].type_ques === 'checkbox' && this.question.question_data[0].id === lstRs[j].ques_id && this.question.id === lstRs[j].ques_pr) {

                                if (lstAns[i].id === lstRs[j].ans_id) {
                                    data_answer = {
                                        ques_id: this.question.question_data[0].id,
                                        type_ques: lstRs[j].type_ques,
                                        ans_id: lstAns[i].id,
                                        ans_content: lstAns[i].content,
                                        ques_pr: this.question.id,
                                        checked: '1'
                                    };
                                    this.uniqueList(data_answer);
                                } else {
                                    data_answer = {
                                        ques_id: this.question.question_data[0].id,
                                        type_ques: lstRs[j].type_ques,
                                        ans_id: lstAns[i].id,
                                        ans_content: lstAns[i].content,
                                        ques_pr: this.question.id,
                                        checked: '0'
                                    };

                                    this.uniqueList(data_answer);
                                }


                            }
                        }
                    }
                }

                let count_datars = this.checkedQuestion.length;
                if (count_datars > 0) {
                    this.is_result_or_raw = '1';
                }
            },
            uniqueList(data) {
                let count_arr = this.checkedQuestion.length;
                let item;

                if (count_arr > 0) {
                    for (let i = this.index; i < count_arr; i++) {
                        item = this.checkedQuestion[i];
                        if (data.ques_id === item.ques_id && data.ques_pr === item.ques_pr) {
                            if (data.ans_id === item.ans_id) {
                                if (item.checked === '0' && data.checked === '1') {
                                    this.checkedQuestion.splice(i, 1);
                                    this.checkedQuestion.push(data);
                                }
                            } else {
                                this.checkedQuestion.push(data);
                                this.index++;
                            }
                        }
                    }
                } else {
                    this.checkedQuestion.push(data);
                }
            }
        },
        mounted() {
            this.getLstCheckedQuestion();
        }
    }
</script>

<style scoped>

</style>
