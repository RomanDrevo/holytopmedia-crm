<template>
    <div :id="chartid" class="bar-3d">

    </div>
</template>

<script type="text/babel">
    import Highcharts from "highcharts";// Adds standard Highcharts to the window
    require('highcharts/highcharts-3d')(Highcharts);

    export default {

        name: "bar-chart-3d",
        props: ["chartid", "maintitle", "subtitle",  "series", "categories"],
        data() {
            return {
                bar_3D: {}
            }
        },
        mounted(){
            this.setupChart();
        },
        methods: {
            setupChart(){
                let vm = this;
                this.bar_3D = Highcharts.chart(this.chartid, {
                    chart: {
                        type: 'column',
                        options3d: {
                            enabled: true,
                            alpha: 0,
                            beta: 0,
                            depth: 70
                        }
                    },

                    title: {
                        text: vm.maintitle
                    },
                    subtitle: {
                        text: vm.subtitle
                    },
                    plotOptions: {
                        column: {
                            depth: 25
                        }
                    },

                    xAxis: {
                        categories: vm.categories
                    },
                    yAxis: {
                        title: {
                            text: null
                        }
                    },
                    series: vm.series
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

</style>