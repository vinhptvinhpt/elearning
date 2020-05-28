<template>
    <div class="col-12 form-group">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <h6>{{trans.get('keys.cau_hoi_min_max')}} {{index_question + 1}}: <label
                v-html="question.question_content"></label></h6><br/>

        <div class="col-12 form-group" v-for="(ques_data,index) in question.lstQuesChild">

            <div class="row"><!--hien thi cau hoi theo dang hang ngang -->
                <div class="col-12 col-md-4"><h6 v-html="ques_data.question_content"></h6></div>
            </div>
            <div class="row"><!--hien thi cau hoi theo dang hang ngang -->
                <div class="col-12">
                    <div class="col-12" v-if="ques_data.total_choice>0">
                        <chart-statistics :question="ques_data" :index_question="index"
                                          :chart_type="chart_type"></chart-statistics>
                    </div>
                </div>
            </div>

        </div>
    </div>


</template>

<script>
    import {Chart} from 'highcharts-vue'
    import ChartStatistics from './StatisticsChartComponent'

    export default {
        props: ['question', 'index_question', 'chart_type'],
        components: {highcharts: Chart, ChartStatistics},
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
