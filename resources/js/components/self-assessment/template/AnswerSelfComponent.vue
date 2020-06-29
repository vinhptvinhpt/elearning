<template>
    <div class="col-12 form-group">
        <label style="font-weight: bold;">{{trans.get('keys.dap_an')}} {{answer.index}}</label>
        <button :title="trans.get('keys.xoa')" data-toggle="modal" data-target="#delete-ph-modal" style="float: right;"
                @click="deleteAnswer()"
                class="btn btn-sm btn-icon btn-danger btn-icon-circle btn-icon-style-2">
            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
        <br/>
        <div class="row">
            <div class="col-6 col-sm-6"><label style="color: red;">*</label>
                <input v-model="answer.content" type="text" id="inputText1-1"
                       :placeholder="trans.get('keys.noi_dung')"
                       class="form-control mb-4">
            </div>
            <div class="col-6 col-sm-6"><label style="color: red;">*</label>
                <input v-model="answer.point" type="number" min="0" id="inputText1-2"
                       :placeholder="trans.get('keys.trong_so')"
                       class="form-control mb-4">
            </div>
        </div>
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
