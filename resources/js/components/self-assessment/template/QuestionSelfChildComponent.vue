<template>
    <div class="col-12 form-group">
        <label style="font-weight: bold;">{{trans.get('keys.cau_hoi')}} {{grSec.index}}</label>
        <button :title="trans.get('keys.xoa')" data-toggle="modal" data-target="#delete-ph-modal" style="float: right;"
                @click="deleteQuestion()"
                class="btn btn-sm btn-icon btn-danger btn-icon-circle btn-icon-style-2">
            <span class="btn-icon-wrap"><i class="fal fa-trash"></i></span></button>
        <br/>

        <div v-for="section in grSec.lstSection"
             :key="section.index">
            <question-section :section="section" :lstSection="grSec.lstSection"></question-section>
        </div>

        <div class="button-list" style="text-align: center;">
            <button @click="addSection()" type="button"
                    class="btn-sm btn-primary">
                {{trans.get('keys.them_section')}}
            </button>
        </div>
    </div>
</template>

<script>
    import QuestionSection from './QuestionSectionComponent';

    export default {
        components: {QuestionSection},
        props: ['grSec', 'group_sections'],
        data() {
            return {
                index_section: 0
            }
        },
        methods: {
            deleteQuestion() {
                this.group_sections.splice(this.group_sections.indexOf(this.grSec), 1);
            },
            addSection() {
                this.index_section++;
                var section = {index: this.index_section, sec_name: '', lstChildQuestion: []};
                this.grSec.lstSection.push(section);
            }
        },
        mounted() {

        }
    }
</script>

<style scoped>

</style>
