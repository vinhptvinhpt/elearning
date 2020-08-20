<template>
    <div class="col-12 form-group">
        <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
        <h6>{{trans.get('keys.cau_hoi')}} {{index_question + 1}}: <label v-html="question.question_content"></label>
        </h6><br/>
        <div class="col-12">
            <div class="col-12">
                <div class="col-12" v-if="question.total_choice>0">
                    <highcharts :options="answerOptions"></highcharts>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Chart} from 'highcharts-vue'


    export default {
        props: ['question', 'index_question', 'chart_type'],
        components: {highcharts: Chart},
        data() {
            return {
                answerOptions: {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: this.chart_type
                    },
                    title: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.percentage:.1f} %</b>'
                            },
                            showInLegend: true
                        }
                    },
                    series: [{}]
                },
            }
        },
        methods: {
            getDataChartQuestion() {
                var count_ans = this.question.lstAnswers.length;
                var data_ans = [];
                if (count_ans > 0) {
                    var data = {};
                    for (var i = 0; i < count_ans; i++) {
                        data = {
                            name: '(' + this.question.lstAnswers[i].total_choice + '/' + this.question.total_choice + ') ' + this.question.lstAnswers[i].answer_content,
                            y: parseFloat(((this.question.lstAnswers[i].total_choice * 100) / this.question.total_choice).toFixed(2))
                        };

                        data_ans.push(data);
                    }
                }

                this.answerOptions.series = [{
                    name: this.trans.get('keys.ratio'),
                    colorByPoint: true,
                    data: data_ans
                }];
            }
        },
        mounted() {
            this.getDataChartQuestion();
        }
    }
</script>

<style scoped>

</style>
