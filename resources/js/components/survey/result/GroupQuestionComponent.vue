<template>
    <div class="row">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <div class="col-12">
            <h6>{{trans.get('keys.cau_hoi')}} {{index_question + 1}}: <label v-html="question.content"></label></h6>
        </div>


        <div class="col-12 form-group">
            <table class="tbl-survey">
                <tr>
                    <th></th>
                    <th v-for="(ans_title,index_anstit) in question.question_data[0].answers">
                        {{ans_title.content}}
                    </th>
                </tr>
                <tr v-for="(ques_data,index) in question.question_data">
                    <td><h6 style="font-weight:400;" class="mb-2"
                            v-html="ques_data.content"></h6></td>
                    <td v-for="(ans,index_ans) in ques_data.answers">
                        <div v-for="(ans_rs,index_rs) in question_answers">
                            <div v-if="ans_rs.type_ques === 'group' && ans_rs.ques_pr === question.id">
                                <div v-if="ans_rs.ques_id === ques_data.id">
                                    <input type="radio" name="row-1" data-col="1" class="ip-radio-survey"
                                           :name="'customRadio-'+ques_data.id+index"
                                           :id="'customRadio-'+ans.id"
                                           :value="ans.id"
                                           v-if="ques_data.id === ans_rs.ques_id && ans.id === ans_rs.ans_id"
                                           checked>

                                    <input type="radio" name="row-1" data-col="1" class="ip-radio-survey"
                                           :name="'customRadio-'+ques_data.id+index"
                                           :id="'customRadio-'+ans.id"
                                           :value="ans.id"
                                           v-else>
                                </div>

                            </div>
                        </div>


                    </td>
                </tr>

            </table>
        </div>
    </div>

</template>

<script>

    export default {
        props: ['question', 'index_question', 'question_answers'],
        data() {
            return {}
        },
        methods: {},
        mounted() {
        }
    }
</script>

<style scoped>

    .tbl-survey tr th:empty {
        border: 0;
    }

</style>
