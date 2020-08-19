<template>
    <div class="row">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <div class="col-12">
            <h6 style="display: flex;">{{trans.get('keys.cau_hoi')}} {{index_question + 1}}: <label
                    v-html="question.question_data[0].content"></label></h6>
        </div>
        <div class="col-12" v-if="has_result === '1'">
            <div v-for="(ans_rs,index_rs) in question_answers" style="padding-left:30px;">
                <div v-if="ans_rs.type_ques ==='multiplechoice'">
                    <div v-for="(ans,index) in question.question_data[0].answers">
                        <div class="custom-control custom-radio custom-control-inline mb-2 radio-primary"
                             v-if="ans_rs.ques_pr === question.id">
                            <input type="radio" :id="'customRadio-'+ans.id"
                                   :name="'customRadio-'+question.id+index_question"
                                   :value="ans.id" checked
                                   v-if="question.question_data[0].id === ans_rs.ques_id && ans.id ===ans_rs.ans_id"
                                   class="custom-control-input">

                            <input type="radio" :id="'customRadio-'+ans.id"
                                   :name="'customRadio-'+question.id+index_question"
                                   :value="ans.id" v-else
                                   class="custom-control-input">
                            <label class="custom-control-label" :for="'customRadio-'+ans.id">{{ans.content}}</label>
                        </div>
                    </div>
                </div>
            </div>

            <br/>
        </div>
        <div v-else class="col-12" v-for="(ans,index) in question.question_data[0].answers" style="padding-left:30px;">
            <div class="custom-control custom-radio custom-control-inline mb-2 radio-primary">
                <input type="radio" :id="'customRadio-'+ans.id" :name="'customRadio-'+question.id+index_question"
                       @click="getAnswerChoose(question.question_data[0].id,question.question_data[0].type_question,ans.id,ans.content,question.id)"
                       :value="ans.id"
                       class="custom-control-input">
                <label class="custom-control-label" :for="'customRadio-'+ans.id">{{ans.content}}</label>
            </div>
            <br/>
        </div>
    </div>

</template>

<script>

    export default {
        props: ['question', 'index_question', 'question_answers'],
        data() {
            return {
                has_result: '0'
            }
        },
        methods: {
            checkHasResult() {
                let count_ques = this.question.question_data[0].answers.length;
                let count_ans = this.question_answers.length;

                let lstAns = this.question.question_data[0].answers;
                let lstRs = this.question_answers;

                if (count_ques > 0 && count_ans > 0) {
                    for (let i = 0; i < count_ques; i++) {
                        for (let j = 0; j < count_ans; j++) {
                            if (lstRs[j].type_ques === 'multiplechoice' && this.question.question_data[0].id === lstRs[j].ques_id
                                && this.question.id === lstRs[j].ques_pr && lstAns[i].id === lstRs[j].ans_id) {
                                this.has_result = '1';
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.checkHasResult();
        }
    }
</script>

<style scoped>

</style>
