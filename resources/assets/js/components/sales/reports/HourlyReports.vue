<template>
    <div class="row">
        <div class="col-sm-4">
            <form method="POST" @submit.prevent="getCustomers">
                <div class="form-group">
                    <label class="datepicker-label">Start Date:</label>
                    <datepicker v-model="search.startDate"
                                placeholder="Start Date"></datepicker>
                </div>
                <div class="form-group">
                    <label class="datepicker-label">End Date:</label>
                    <datepicker v-model="search.endDate"
                                placeholder="End Date"></datepicker>
                    <button type="submit" class="btn btn-success"><i class="fa fa-search"
                                                                     aria-hidden="true"></i>
                    </button>
                </div>
            </form>
            <table id="customers-filter" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th colspan="2">Filter By:</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="filters">Countries</td>
                    <td>
                        <multiselect
                                v-model="selectedCountries"
                                label="name"
                                track-by="name"
                                placeholder="All"
                                :options="countriesArr"
                                :multiple="true"
                                :searchable="true"
                                :close-on-select="false">
                            <span slot="noResult">Oops! No country found.</span>
                        </multiselect>

                    </td>
                </tr>
                <tr>
                    <td class="filters">Campaigns</td>
                    <td>
                        <multiselect
                                v-model="selectedCampaigns"
                                label="name"
                                track-by="name"
                                placeholder="All"
                                :options="campaigns"
                                :multiple="true"
                                :searchable="true"
                                :close-on-select="false">
                            <span slot="noResult">Oops! No campaign found.</span>
                        </multiselect>

                    </td>
                </tr>
                <tr>
                    <td class="filters">Day GMT</td>
                    <td>
                        <select class="form-control days-select" v-model="selectedDay">
                            <option :value="null">All</option>
                            <option :value="index" v-for="(day, index) in weekDays">{{ day }}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button class="btn btn-success" @click.prevent="filterData"><i class="fa fa-search"
                                                                                       aria-hidden="true"></i></button>
                        <button class="btn btn-default" @click.prevent="clear"><i class="fa fa-refresh"
                                                                                  aria-hidden="true"></i></button>

                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-8">
            <bar-chart-3d
                    chartid="customers"
                    maintitle="FTD GMT Time"
                    :categories="customersBar.categories"
                    :series="customersBar.series"
                    :subtitle="customersBar.series[0].sum +  ' Customers (' + start + ' - ' + end + ')'">
            </bar-chart-3d>
        </div>
    </div>
</template>

<script type="text/babel">
    import Datepicker from 'vuejs-datepicker';
    import moment from 'moment';
    import {numberFormat} from '../../../helpers/functions';
    import Multiselect from 'vue-multiselect';
    import BarChart3d from './charts/BarChart3D.vue';

    export default {
        name: 'hourly-report',

        props: {
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
        data() {
            return {
                search: {
                    startDate: "",
                    endDate: ""
                },
                selectedCampaigns: [],
                countriesArr: [],
                selectedCountries: [],
                weekDays: [],
                selectedDay: null,

                hourlyCustomers: [],
                customersData: [],
                customersBar: {
                    categories: [],
                    series: []
                }

            }
        },
        created() {
            this.search.startDate = moment().subtract(1, 'months').format();
            this.search.endDate = moment().format();
            this.weekDays = moment.weekdays();
            this.customersData = $.extend(true, [], this.customers);
            this.countriesToArray();
            this.buidCustomersData();


        },
        methods: {
            getCustomers() {
                Vue.http.post('/sales/reports/customers-per-hours', this.$data.search)
                        .then(response => {
                            this.customers = response.body;
                            this.buidCustomersData();
                        })
                        .catch(errors => {
                            console.log(errors);
                        });
            },
            filterData() {
                this.customersData = $.extend(true, [], this.customers);
                let data = [];

                let campaignIds = _.map(this.selectedCampaigns, 'campaign_crm_id');
                let countryIds = _.map(this.selectedCountries, 'id');
                let day = this.selectedDay;
                _.map(this.customersData, hour => {
                    //remove all irrelevant campaigns
                    if (campaignIds.length > 0) {
                        _.remove(hour, function (customer) {
                            return _.indexOf(campaignIds, customer.campaignId) == -1;
                        });
                    }
                    //remove all irrelevant countries
                    if (countryIds.length > 0) {
                        _.remove(hour, function (customer) {
                            return _.indexOf(countryIds, customer.Country) == -1;
                        });
                    }
                    //remove all irrelevant week days
                    if (day) {
                        _.remove(hour, function (customer) {
                            return day != customer.weekDay;
                        });
                    }
                    data.push(hour.length);
                });
                this.customersBar.series = [];
                this.customersBar.series.push({name: 'Customers', data: data, sum: _.sum(data)});
            },


            clear() {
                this.selectedCampaigns = [];
                this.selectedCountries = [];
                this.selectedDay = null;
                this.buidCustomersData();
            },
            countriesToArray() {
                let vm = this;
                _.map(this.countries, (value, index) => {
                    vm.countriesArr.push(
                            {id: parseInt(index), name: value}
                    )
                });
            },
            buidCustomersData() {
                var data = [];
                this.customersBar.series = [];
                this.customersData = $.extend(true, [], this.customers);
                _.map(this.customersData, (hour, index) => {
                    data.push(hour.length);

                    let cat = this.buildCategories(index, this.customersData);
                    this.customersBar.categories.push(cat);

                });
                this.customersBar.series.push({name: 'Customers', data: data, sum: _.sum(data)});
            },

            buildCategories(index, array) {
                let catItem = index.toString();

                if (catItem < array.length) {
                    var catItemNext = (index + 1).toString();
                } else {
                    catItemNext = '00:00';
                }

                if (catItem.lenght < 2) {
                    catItem = '0' + catItem;
                }
                if (catItemNext.lenght < 2) {
                    catItemNext = '0' + catItemNext;
                }
                return catItem + ':00 - ' + catItemNext + ':00';
            }


        },
        computed: {
            start() {
                return moment(this.search.startDate).format('DD MMMM');
            },

            end() {
                return moment(this.search.endDate).format('DD MMMM');
            }
        },
        components: {
            Datepicker,
            Multiselect,
            "bar-chart-3d": BarChart3d
        }

    }
</script>
<style scoped>
    #customers-filter {
        margin-bottom: 60px;
    }
    td.filters {
        font-weight: bold;
    }

    .multiselect, .days-select {
        width: 85%;
        display: inline-block;
    }





</style>