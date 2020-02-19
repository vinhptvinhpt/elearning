<template>
    <div class="form-group">
        <h6>{{trans.get('keys.cau_hoi')}} {{index_question+1}}: <label v-html="question.question_data[0].content"></label></h6>
        <textarea class="form-control" rows="3" v-model="question.question_data[0].answers[0]"
                  placeholder="Nội dung"></textarea>
    </div>
</template>

<script>

    export default {
        props: ['question', 'index_question', 'question_answers'],
        data() {
            return {
                answer: {
                    content: ''
                }
            }
        },
        methods: {},
        mounted() {

        }
    }
</script>

<style scoped>

</style>
