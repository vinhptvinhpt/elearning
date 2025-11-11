<template>
    <div class="form-group">
        <h6>{{trans.get('keys.cau_hoi')}} {{index_question+1}}: <label
                v-html="question.question_data[0].content"></label></h6>

        <ckeditor v-model="question.question_data[0].answers[0]" :config="editorConfig"></ckeditor>
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
                    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&responseType=json&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    versionCheck: false
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
