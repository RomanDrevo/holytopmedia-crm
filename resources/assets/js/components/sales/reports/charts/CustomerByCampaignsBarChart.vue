<template>
    <div :id="chartid" class="chart">

    </div>
</template>

<script type="text/babel">
    import Highcharts from 'highcharts';
    export default {
        name: "campaigns-bar",
        props: ["chartid", "maintitle", "series", "categories"],
        data() {
            return {
                campaign_chart: {}
            }
        },
        mounted(){
            this.setupChart();
        },
        methods: {
            setupChart(){
                let vm = this;
                this.campaign_chart = Highcharts.chart(this.chartid, {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: vm.maintitle
                    },
                    xAxis: {
                        categories: vm.categories
                    },
                    yAxis: {
                        allowDecimals: false,
                        min: 0,
                        title: {
                            text: 'Customers Count'
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.x + '</b><br/>' +
                                    this.series.name + ': ' + this.y + '<br/>' +
                                    'Total: ' + this.point.stackTotal;
                        }
                    },

                    plotOptions: {
                        column: {
                            stacking: 'normal'
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