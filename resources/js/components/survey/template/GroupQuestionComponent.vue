<template>
    <div class="row">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <div class="col-12">
            <h6>{{trans.get('keys.cau_hoi')}} {{index_question + 1}}: <label v-html="question.content"></label></h6>
        </div>
        <div class="col-12 form-group" v-for="(ques_data,index) in question.question_data">
            <div class="col-12">
                <!--                <div class="col-12 form-group">-->
                <div class="row"><!--hien thi cau hoi theo dang hang ngang -->
                    <div class="col-12 col-md-6 "><h6 style="font-weight:400;" class="mb-2" v-html="ques_data.content"></h6></div>
                    <div class="col-12 col-md-6 ">
                        <div class="custom-control custom-radio custom-control-inline mb-2 radio-primary sm-width-100"
                             v-for="(ans,index_ans) in ques_data.answers">
                            <input type="radio" :id="'customRadio-'+ans.id"
                                   :name="'customRadio-'+ques_data.id+index" :value="ans.id"
                                   @click="getAnswerChoose(ques_data.id,ques_data.type_question,ans.id,ans.content)"
                                   class="custom-control-input">
                            <label class="custom-control-label"
                                   :for="'customRadio-'+ans.id">{{ans.content}}</label>
                        </div>
                    </div>
                </div>
                <!--                </div>-->
            </div>
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
                var count_ans = this.question_answers.length;
                if (count_ans > 0) {
                    for (var i = 0; i < count_ans; i++) {
                        if (this.question_answers[i].ques_id === ques_id) {
                            this.question_answers.splice(i, 1);
                            break;
                        }
                    }
                }
                this.question_answers.push(data_answer);

            }

        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
