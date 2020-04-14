<template>
    <div class="col-12 form-group">
        <label for="article_ckeditor" style="font-weight: bold;">{{trans.get('keys.dap_an')}} {{answer.index}}</label>
        <button :title="trans.get('keys.xoa')" data-toggle="modal" data-target="#delete-ph-modal" style="float: right;"
                @click="deleteAnswer()"
                class="btn btn-sm btn-icon btn-danger btn-icon-circle btn-icon-style-2">
            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
        <br/>
        <textarea class="form-control" rows="3" v-model="answer.content"
                  id="article_ckeditor"
                  :placeholder="trans.get('keys.noi_dung')"></textarea>
    </div>
</template>

<script>

    export default {
        props: ['answer', 'lstAnswer'],
        data() {
            return {}
        },
        methods: {
            deleteAnswer() {
                this.lstAnswer.splice(this.lstAnswer.indexOf(this.answer), 1);
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
