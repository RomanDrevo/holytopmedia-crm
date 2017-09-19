<template>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Reports</h1>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-line-chart"></i> {{ table.name }} Reports
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <form method="POST" @submit.prevent="getReports">
                                    <div class="form-group">
                                        <label class="datepicker-label">Start Date:</label>
                                        <datepicker v-model="search.startDate"
                                                    placeholder="Start Date"></datepicker>
                                        <label class="datepicker-label">End Date:</label>
                                        <datepicker v-model="search.endDate"
                                                    placeholder="End Date"></datepicker>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"
                                                                                         aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <p v-if="message">{{ message }}</p>
                                <pie-chart
                                        chartid="payment_method"
                                        :maintitle="paymentMethodPie.title"
                                        :series="paymentMethodPie.series">
                                </pie-chart>
                            </div>
                            <div class="col-sm-6">
                                <h4 class="guage-h4">{{ current_month }} - Left to Goal Guage </h4>
                                <p style="text-align: center">This goal: {{ getNumberFormat(guageChartData.goal) }}</p>
                                <guage-chart
                                        chartid="left-to-goal"
                                        :maintitle="guageChartData.title"
                                        :seriesData="guageChartData.total"
                                        :goal="guageChartData.goal"
                                        suffix=" ">
                                </guage-chart>
                            </div>
                        </div>
                        <hr class="delimeter"/>
                        <div class="row">
                            <div class="col-sm-5">
                                <pie-chart
                                        chartid="employees_pie"
                                        :maintitle="employeesPie.title"
                                        :series="employeesPie.series">
                                </pie-chart>
                            </div>
                            <div class="col-sm-7">
                                <bar-chart
                                        chartid="employees_bar"
                                        :categories="employeesBar.categories"
                                        :maintitle="employeesBar.title"
                                        :series="employeesBar.series">
                                </bar-chart>
                            </div>
                        </div>
                    </div>


                    <hr class="delimeter"/>
                    <div class="row">
                        <div class="col-sm-6">
                            <pie-chart
                                    chartid="currencies"
                                    :maintitle="currenciesPie.title"
                                    :series="currenciesPie.series">
                            </pie-chart>
                        </div>
                        <div class="col-sm-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script type="text/babel">
    import Datepicker from 'vuejs-datepicker';
    import moment from 'moment';
    import GuageChart from './charts/SolidGuageChart.vue';
    import PieChart from './charts/PieChart.vue';
    import BarChart from './charts/BarChart.vue';
    import {numberFormat} from '../../../helpers/functions';

    export default {
        name: 'table-reports',

        props: {
            table: {
                required: true
            },
            left_to_goal: {
                required: true
            }
        },
        data(){
            return {
                search: {
                    startDate: "",
                    endDate: "",
                    tableId: this.table.id
                },
                paymentMethodPie: {},
                currenciesPie: {},
                employeesPie: {},
                message: '',

                employeesBar: {},
                guageChartData: {
                    title: 'Left to Goal',
                    total: 0,
                    goal: 0
                }
            }
        },
        created() {
            this.search.startDate = moment().startOf('month').format();
            this.search.endDate = moment().format();
            this.guageChartData = this.left_to_goal;
            this.getPaymentMethodReport();
            this.getCurrenciesReport();
            this.getDepositsByEmployeesPie();
            this.getDepositsByEmployeesBar();
        },
        methods: {
            getReports() {
                this.getPaymentMethodReport();
                this.getGoalData();
                this.getCurrenciesReport();

            },
            getPaymentMethodReport(){
                Vue.http.post("/sales/reports/payment-method", this.$data.search)
                        .then(response => {
                            if (typeof response.body == 'string') {
                                this.message = response.body;
                            } else {
                                this.paymnentMethodData = true;
                                this.paymentMethodPie = response.body.data;
                            }
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },
            getGoalData() {
                Vue.http.post("/sales/reports/goal-data", this.$data.search)
                        .then(response => {
                            this.guageChartData = response.body;
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },
            getCurrenciesReport() {
                Vue.http.post("/sales/reports/currencies", this.$data.search)
                        .then(response => {
                            this.currenciesPie = response.body.data;
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },
            getDepositsByEmployeesPie() {
                Vue.http.post("/sales/reports/mananger/employees-pie", this.$data.search)
                        .then(response => {
                            this.employeesPie = response.body.data;
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },
            getDepositsByEmployeesBar() {
                Vue.http.post("/sales/reports/mananger/employees-bar", this.$data.search)
                        .then(response => {
                            this.employeesBar = response.body.data;
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },

            getNumberFormat(num) {
                return numberFormat(num);
            }
        },
        computed: {
            current_month() {
                return moment().format('MMM YYYY');
            }
        },
        components: {
            Datepicker,
            "guage-chart": GuageChart,
            "pie-chart": PieChart,
            "bar-chart": BarChart
        }
    }
</script>
<style scoped>
 .guage-h4 {
        text-align: center;
   }


</style>