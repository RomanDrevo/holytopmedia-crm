<template>
    <div :id="chartid" class="pie-chart">

    </div>
</template>

<script type="text/babel">
    import Highcharts from "highcharts";// Adds standard Highcharts to the window
    require('highcharts/highcharts-3d')(Highcharts);

    export default {

        name: "pie-chart",
        props: ["chartid", "maintitle", "series"],
        data() {
            return {
                convertion_chart: {}
            }
        },
        mounted(){
            if(this.series) {
                this.setupChart();
            }
        },
        methods: {
            setupChart(){
                let vm = this;
                this.convertion_chart = Highcharts.chart(this.chartid, {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45,
                            beta: 0
                        }
                    },
                    title: {
                        text: vm.maintitle
                    },
                    tooltip: {
                        pointFormat: '{series.name} - {point.count}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            depth: 35,
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}'
                            }
                        },
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
    .pie-chart {
        min-width: 310px;
        height: 450px;
        max-width: 650px;
    }


</style>