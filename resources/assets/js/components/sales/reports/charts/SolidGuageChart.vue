<template>
    <div>
        <div :id="chartid" class="chart">

        </div>
    </div>
</template>

<script type="text/babel">
    import Highcharts from 'highcharts';
    require('highcharts-more')(Highcharts);
    require('highcharts-solid-gauge')(Highcharts);

    import {numberFormat} from '../../../../helpers/functions';
    export default {
        name: "guage-chart",

        props: ["chartid", "maintitle", "seriesData", "goal", "suffix"],
        data() {
            return {
                guage_chart: {}
            }
        },
        mounted() {
            this.setupChart();
        },
        methods: {
            setupChart(){
                let vm = this;
                Highcharts.chart(this.chartid, {
                    chart: {
                        type: 'solidgauge'
                    },
                    title: 'Monthly Goal',
                    yAxis: {
                        min: 0,
                        max: vm.goal,
                        stops: [
                            [0.1, '#DF5353'], // green
                            [0.5, '#DDDF0D'], // yellow
                            [0.9, '#55BF3B'] // red
                        ],
                        lineWidth: 0,
                        title: {
                            y: -100,
                            text: 'Monthly Goal'
                        },
                        labels: {
                            y: 16
                        }
                    },
                    pane: {
                        center: ['50%', '85%'],
                        size: '140%',
                        startAngle: -90,
                        endAngle: 90,
                        background: {
                            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                            innerRadius: '60%',
                            outerRadius: '100%',
                            shape: 'arc'
                        }
                    },


                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        solidgauge: {
                            dataLabels: {
                                y: 5,
                                borderWidth: 0,
                                useHTML: true
                            }
                        }
                    },

                    series: [{
                        name: 'Current Amount',
                        data: [vm.seriesData],
                        dataLabels: {
                            format: '<div style="text-align:center"><span style="font-size:18px;color:' +
                            ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}{suffix}</span></div>'
                        },

                        tooltip: {
                            enabled: false,
                            valueSuffix: vm.suffix
                        }
                    }]

                })
            },

           getNumberFormat(num) {
                return numberFormat(num);
            }

        },
        computed: {

            redZone() {
                return [0, this.goal * 0.33];
            },
            yellowZone() {
                return [this.goal * 0.33, this.goal * 0.66];
            },
            greenZone() {
                return [this.goal * 0.66, this.goal];
            }
        }
    }
</script>
<style coped>
   #left-to-goal {
    max-width: 350px;
    height: 250px;
    margin: 0 auto;
   }

</style>