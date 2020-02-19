<template>
    <div class="col-12 form-group">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <h6>{{trans.get('keys.cau_hoi_nhom')}} {{index_question + 1}}: <label
                v-html="question.question_content"></label></h6><br/>

        <div class="col-12 form-group" v-for="(ques_data,index) in question.lstQuesChild">

            <div class="row"><!--hien thi cau hoi theo dang hang ngang -->
                <div class="col-12 col-md-4"><h6 v-html="ques_data.question_content"></h6></div>
                <!--                <div class="col-12 col-md-8">-->
                <!--                    <div class="col-12 form-group">-->
                <!--                        <div class="custom-control custom-radio custom-control-inline mb-3 radio-primary"-->
                <!--                             v-for="(ans,index_ans) in ques_data.lstAnswers">-->
                <!--                                <span class="badge badge-primary badge-indicator"-->
                <!--                                      style="height: 12px; width: 12px; margin:4px 3px 0 0;"></span>-->
                <!--                            <label>{{trans.get('keys.dap_an')}} {{index_ans + 1}} ( {{ans.total_choice}} / {{ques_data.total_choice}}-->
                <!--                                )</label>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
            </div>
            <div class="row"><!--hien thi cau hoi theo dang hang ngang -->
<!--                <br/>-->
<!--                <button class="btn btn-sm btn-icon btn-icon-circle btn-danger btn-icon-style-2"><span-->
<!--                        class="btn-icon-wrap"><i class="fal fa-spin"></i></span></button>-->
                <div class="col-12">
                    <div class="col-12" v-if="ques_data.total_choice>0">
                        <chart_statistic :question="ques_data" :index_question="index"></chart_statistic>
                    </div>
                </div>
            </div>

        </div>
    </div>


</template>

<script>
    import {Chart} from 'highcharts-vue'

    export default {
        props: ['question', 'index_question'],
        components: {highcharts: Chart},
        data() {
            return {}
        },
        methods: {},
        mounted() {

        }
    }
</script>

<style scoped>

</style>
