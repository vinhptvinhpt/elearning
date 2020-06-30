<template>
    <div class="col-12 form-group">
        <label style="font-weight: bold;">{{trans.get('keys.section')}}
            {{section.index}}<label style="color: red;">*</label></label>
        <button :title="trans.get('keys.xoa')" data-toggle="modal" data-target="#delete-ph-modal" style="float: right;"
                @click="deleteSection()"
                class="btn btn-sm btn-icon btn-danger btn-icon-circle btn-icon-style-2">
            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
        <input v-model="section.sec_name" type="text" id="inputText1-1"
               :placeholder="trans.get('keys.nhap_ten_section')"
               class="form-control mb-4">
        <br/>
        <div class="button-list">
            <button @click="addQuestion()" type="button"
                    class="btn-sm btn-primary">
                {{trans.get('keys.them_cau_hoi_section')}}
            </button>
        </div>
        <div v-for="ques in section.lst_child_question"
             :key="ques.index">
            <question-child-section :question="ques"
                                    :lstQuestion="section.lst_child_question"></question-child-section>
        </div>
    </div>
</template>

<script>
    import QuestionChildSection from './QuestionChildSectionComponent';

    export default {
        components: {QuestionChildSection},
        props: ['section', 'lstSection'],
        data() {
            return {
                index_question_child: 0
            }
        },
        methods: {
            addQuestion() {
                this.index_question_child++;
                var question = {index: this.index_question_child, content: ''};
                this.section.lst_child_question.push(question);
            },
            deleteSection() {
                this.lstSection.splice(this.lstSection.indexOf(this.section), 1);
            }
        },
        mounted() {
            // if (this.section.lst_child_question.length > 0) {
            //     this.index_question_child = this.section.lst_child_question.length;
            // }
        }
    }
</script>

<style scoped>

</style>
