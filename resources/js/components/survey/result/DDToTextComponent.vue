<template>
    <div class="form-group">
        <h6>{{trans.get('keys.cau_hoi')}} {{index_question+1}}: <label
                v-html="question.question_data[0].content"></label></h6>

        <div v-if="has_result === '1' && checkedQuestion.length > 0">
            <ckeditor v-model="checkedQuestion[0].ans_content" :config="editorConfig"></ckeditor>
        </div>

        <div v-else>
            <ckeditor v-model="question.question_data[0].answers[0]" :config="editorConfig"></ckeditor>
        </div>

    </div>
</template>

<script>

    import CKEditor from 'ckeditor4-vue';

    export default {
        components: {
            CKEditor
        },
        props: ['question', 'index_question', 'question_answers'],
        data() {
            return {
                answer: {
                    content: ''
                },
                editorConfig: {
                    filebrowserUploadMethod: 'form', //fix for response when uppload file is cause filetools-response-error
                    // The configuration of the editor.
                    //add responseType=json for original version of ckeditor 4, else cause filetools-response-error
                    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                    filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content')
                },

                checkedQuestion: [],
                index: 0,
                has_result: '0'
            }
        },
        methods: {
            getLstCheckedQuestion() {
                let count_ans = this.question_answers.length;

                let lstRs = this.question_answers;
                let data_answer;

                if (count_ans > 0) {
                    for (let j = 0; j < count_ans; j++) {
                        if (lstRs[j].type_ques === 'ddtotext' && this.question.question_data[0].id === lstRs[j].ques_id
                            && this.question.question_data[0].type_question === 'ddtotext') {

                            data_answer = {
                                ques_id: this.question.question_data[0].id,
                                type_ques: lstRs[j].type_ques,
                                ans_content: lstRs[j].ans_content
                            };
                            this.uniqueList(data_answer);
                        }
                    }

                }

                let count_datars = this.checkedQuestion.length;
                if (count_datars > 0) {
                    this.has_result = '1';
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
