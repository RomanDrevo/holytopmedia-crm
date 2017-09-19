<template>
    <div :id="chartid" class="bar-chart">

    </div>
</template>

<script type="text/babel">
    import Highcharts from "highcharts";// Adds standard Highcharts to the window

    export default {

        name: "bar-chart",
        props: ["chartid", "maintitle", "series", "categories"],
        data() {
            return {
                convertion_chart: {}
            }
        },
        mounted(){
            if(this.series && this.categories) {
                this.setupChart();
            }
        },
        methods: {
            setupChart(){
                let vm = this;
                this.convertion_chart = Highcharts.chart(this.chartid, {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: vm.maintitle
                    },

                    xAxis: {
                        categories: vm.categories
                    },
                    credits: {
                        enabled: false
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