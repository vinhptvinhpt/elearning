<template>
    <div class="row">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <div class="col-12">
            <h6>{{trans.get('keys.cau_hoi')}} {{index_question + 1}}: <label
                    v-html="question.question_data[0].content"></label></h6>
        </div>
        <div class="col-12" v-for="(ans,index) in question.question_data[0].answers" style="padding-left:30px;">
            <div>
                <input type="checkbox" :id="'customRadio-'+ans.id"
                       @click="getAnswerChoose(question.question_data[0].id,question.question_data[0].type_question,ans.id,ans.content)"
                       :value="ans.id"
                >
                <label :for="'customRadio-'+ans.id">{{ans.content}}</label>
            </div>
            <br/>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['question', 'index_question', 'question_answers'],
        data() {
            return {}
        },
        methods: {
            getAnswerChoose(ques_id, type_ques, ans_id, ans_content) {

                var data_answer = {ques_id: ques_id, type_ques: type_ques, ans_id: ans_id, ans_content: ans_content};
                // var count_ans = this.question_answers.length;
                // if (count_ans > 0) {
                //     for (var i = 0; i < count_ans; i++) {
                //         if (this.question_answers[i].ques_id === ques_id) {
                //             this.question_answers.splice(i, 1);
                //             break;
                //         }
                //     }
                // }
                this.question_answers.push(data_answer);
            }

        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
