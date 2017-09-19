<template>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Reports</h1>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-line-chart"></i> Sales Reports
                    </div>
                    <div class="panel-body">
                        <div id="sales-reports">
                            <div class="row" id="upsale-report">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5" style="text-align: right">
                                    <datepicker v-model="upsale" placeholder="Start Date"></datepicker>
                                    <button @click.prevent="getUpsaleReport" class="btn btn-sm btn-success"><i class="fa fa-file-excel-o"></i> Upsale report</button>
                                </div>
                            </div>
                            <hourly-report
                                    :customers="customers"
                                    :countries="countries"
                                    :campaigns="campaigns">
                            </hourly-report>


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
                                    <h4 class="guage-h4">{{ current_month}} - Left to Goal Guage </h4>
                                    <p style="text-align: center">The goal: {{ getNumberFormat(guageChartData.goal)
                                        }}$</p>
                                    <guage-chart
                                            chartid="left-to-goal"
                                            :maintitle="guageChartData.title"
                                            :seriesData="guageChartData.total"
                                            :goal="guageChartData.goal"
                                            suffix="$">
                                    </guage-chart>
                                </div>
                            </div>
                            <hr class="delimeter"/>
                            <div class="row">
                                <div class="col-sm-6">
                                    <pie-chart
                                            chartid="campaigns"
                                            :maintitle="campaignsPie.title"
                                            :series="campaignsPie.series">
                                    </pie-chart>

                                </div>
                                <div class="col-sm-6">
                                    <pie-chart
                                            chartid="countries"
                                            :maintitle="countriesPie.title"
                                            :series="countriesPie.series">
                                    </pie-chart>
                                </div>
                            </div>
                            <hr class="delimeter"/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <bar-chart
                                            chartid="campaigns_bar"
                                            :categories="campaignsBar.categories"
                                            :maintitle="campaignsBar.title"
                                            :series="campaignsBar.series">
                                    </bar-chart>
                                </div>
                            </div>
                            <hr class="delimeter"/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <bar-chart
                                            chartid="countries_bar"
                                            :categories="countriesBar.categories"
                                            :maintitle="countriesBar.title"
                                            :series="countriesBar.series">
                                    </bar-chart>
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
        </div>

</template>

<script type="text/babel">
    import Datepicker from 'vuejs-datepicker';
    import moment from 'moment';
    import GuageChart from './charts/SolidGuageChart.vue';
    import PieChart from './charts/PieChart.vue';
    import BarChart from './charts/BarChart.vue';
    import HourlyReports from './HourlyReports.vue';
    import {numberFormat} from '../../../helpers/functions';
    import {exportToCsv} from '../../../helpers/functions.js';

    export default{
        name: "sales-reports",
        props: {
            left_to_goal: {
                required: true
            },
            customers: {
                required: true
            },
            countries: {
                required: true
            },
            campaigns: {
                required: true
            }

        },

        created(){
            this.search = this.setDates();
            this.upsale =  moment().format();
            this.guageChartData.total = this.left_to_goal.total;
            this.guageChartData.goal = parseInt(this.left_to_goal.goal);
            this.getPaymentMethodReport();
            this.getCampaignsReport();
            this.getContriesReport();
            this.getCurrenciesReport();
            this.getDepositsAndNetByCampaign();
            this.getDepositsAndNetByContry();
        },
        data(){
            return {
                search: {
                    startDate: "",
                    endDate: ""
                },
                upsale: "",
                paymentMethodPie: {},
                message: '',

                campaignsPie: {},
                campaignsBar: {},

                countriesPie: {},
                countriesBar: {},
                currenciesPie: {},

                guageChartData: {
                    title: 'Left to Goal',
                    total: 0,
                    goal: 0
                }
            };
        },
        methods: {
            getReports() {
                this.getPaymentMethodReport();
                this.getGoalData();
                this.getCampaignsReport();
                this.getContriesReport();
                this.getCurrenciesReport();
                this.getDepositsAndNetByCampaign();
                this.getDepositsAndNetByContry();
            },

            getUpsaleReport() {
                Vue.http.post("/sales/reports/upsale", {startDate: this.upsale})
                        .then(response => {
                            this.toCsv('upsales_report.csv', response.body);
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
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
            getCampaignsReport() {
                Vue.http.post("/sales/reports/campaigns", this.$data.search)
                        .then(response => {
                            this.campaignsPie = response.body.data;
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },
            getContriesReport() {
                Vue.http.post("/sales/reports/countries", this.$data.search)
                        .then(response => {
                            this.countriesPie = response.body.data;
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
            getDepositsAndNetByCampaign() {
                Vue.http.post("/sales/reports/deposits-and-net/campaign", this.$data.search)
                        .then(response => {
                            this.campaignsBar = response.body.data;
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },
            getDepositsAndNetByContry() {
                Vue.http.post("/sales/reports/deposits-and-net/country", this.$data.search)
                        .then(response => {
                            this.countriesBar = response.body.data;
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },

            setDates() {
                return {
                    startDate: moment().startOf('month').format(),
                    endDate: moment().format(),
                }
            },
            getNumberFormat(num) {
                return numberFormat(num);
            },
            toCsv (filename, rows) {
                return exportToCsv(filename, rows);
            },
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
            "bar-chart": BarChart,
            "hourly-report": HourlyReports
        }
    }
</script>
<style scoped>
    #sales-reports {
        margin-top: 60px;
    }
    #upsale-report {
        margin: 35px 0;
    }
    .guage-h4 {
        text-align: center;
   }
   .datepicker-label {
        font-weight: normal;
   }
   hr.delimeter {
        border-top: 1px solid #CCCCCC;
        margin-top: 25px;
        margin-bottom: 25x;
   }







</style>