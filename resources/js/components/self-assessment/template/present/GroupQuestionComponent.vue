<template>
    <div class="row">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <div class="col-12">
            <h6 style="display: flex;">{{trans.get('keys.cau_hoi')}} {{index_question + 1}}: <label
                    v-html="question.content"></label></h6>
        </div>

        <div class="col-12">
            <div class="row" v-for="(section,index_sec) in question.sections">
                <div class="col-12">
                    <h6>{{trans.get('keys.section')}} {{index_sec + 1}}: {{section.section_name}}</h6>
                </div>
                <div class="col-12 form-group">
                    <table class="tbl-survey">
                        <tr>
                            <th></th>
                            <th v-for="(ans_title,index_anstit) in section.lst_child_question[0].answers">
                                {{ans_title.content}}
                            </th>
                        </tr>
                        <tr v-for="(ques_data,index) in section.lst_child_question">
                            <td><h6 style="font-weight:400;" class="mb-2"
                                    v-html="ques_data.content"></h6></td>
                            <td v-for="(ans,index_ans) in ques_data.answers">
                                <input type="radio" name="row-1" data-col="1" class="ip-radio-survey" required
                                       :name="'customRadio-'+ques_data.id+index"
                                       :id="'customRadio-'+ans.id"
                                       :value="ans.id"
                                       @click="getAnswerChoose(ques_data.id,ques_data.type_question,ans.id,ans.content,section.id,question.id,ans.point)">
                            </td>
                        </tr>

                    </table>
                </div>

            </div>
        </div>

    </div>

</template>

<script>

    export default {
        props: ['question', 'index_question', 'question_answers', 'group_ques', 'minmax_gr'],
        data() {
            return {}
        },
        methods: {
            getAnswerChoose(ques_id, type_ques, ans_id, ans_content, section_id, ques_parent, point) {

                var data_answer = {
                    ques_id: ques_id,
                    type_ques: type_ques,
                    ans_id: ans_id,
                    ans_content: ans_content,
                    section_id: section_id,
                    ques_parent: ques_parent,
                    point: point
                };

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
                
                var gr = {
                    type_ques: type_ques,
                    section_id: section_id,
                    ques_parent: ques_parent
                };

                var count_gr = this.group_ques.length;
                if (count_gr > 0) {
                    for (var j = 0; j < count_gr; j++) {
                        if (this.group_ques[j].section_id === section_id && this.group_ques[j].ques_parent === ques_parent) {
                            this.group_ques.splice(j, 1);
                            break;
                        }
                    }
                }
                this.group_ques.push(gr);
            }

        },
        mounted() {
        }
    }
</script>

<style scoped>

    .tbl-survey tr th:empty {
        border: 0;
    }

</style>
