<template>
    <div>
        <div :id="chartid" class="chart">

        </div>
    </div>
</template>

<script type="text/babel">
    import Highcharts from 'highcharts';

    export default {
        name: "payment-method-pie",

        props: ["chartid", "maintitle", "series"],

        data(){
            return {
                payment_method_chart: {}
            }
        },
        mounted(){
            this.setupChart();
        },

        methods: {
            setupChart(){
                let vm = this;
                this.payment_method_chart = Highcharts.chart(this.chartid, {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: vm.maintitle
                    },

                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    series: [vm.series]
                });
            }
        },
        watch: {
            series(data) {
                this.setupChart();
            }
        }
    }
</script>

<style scoped>
.chart {
    min-width   : 310px;
    max-width   : 600px;
    height      : 400px;
}


</style>
