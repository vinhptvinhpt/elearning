<template>
    <div class="col-12 form-group">
        <label style="font-weight: bold;">{{trans.get('keys.cau_hoi')}}
            {{question.index}}</label>
        <button :title="trans.get('keys.xoa')" data-toggle="modal" data-target="#delete-ph-modal" style="float: right;"
                @click="deleteQuestion()"
                class="btn btn-sm btn-icon btn-danger btn-icon-circle btn-icon-style-2">
            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
        <br/>
        <ckeditor id="article-ckeditor" v-model="question.content"
                  :config="editorConfig"></ckeditor>
    </div>
</template>

<script>

    export default {
        props: ['question', 'lstQuestion', 'editorConfig'],
        data() {
            return {}
        },
        methods: {
            deleteQuestion() {
                this.lstQuestion.splice(this.lstQuestion.indexOf(this.question), 1);
            }
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
