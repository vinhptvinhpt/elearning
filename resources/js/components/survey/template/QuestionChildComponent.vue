<template>
    <div class="col-12 form-group">
        <label for="article-ckeditor" style="font-weight: bold;">{{trans.get('keys.cau_hoi')}} {{question.index}}</label>
        <button :title="trans.get('keys.xoa')" data-toggle="modal" data-target="#delete-ph-modal" style="float: right;"
                @click="deleteQuestion()"
                class="btn btn-sm btn-icon btn-danger btn-icon-circle btn-icon-style-2">
            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
        <br/>
        <textarea class="form-control" rows="3" v-model="question.content"
                  id="article-ckeditor"
                  :placeholder="trans.get('keys.noi_dung')"></textarea>
    </div>
</template>

<script>

    export default {
        props: ['question', 'lstQuestion'],
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
