<template>
    <div class="container-fluid">
        <div class="row">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Pending Deposits</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <loader v-if="loading"></loader>
                    <div class="row">
                        <div style="margin-bottom: 18px;">
                            <form class="pending-search">
                                <div class="form-group">
                                    <label>Customer ID</label>
                                    <input class="form-control customer_id_input" type="text" v-model="search.customerId" placeholder="ID"/>
                                    <label>Start & End Date</label>
                                    <datepicker v-model="search.startDate" placeholder="Start Date"></datepicker>
                                    <datepicker v-model="search.endDate" placeholder="End Date"></datepicker>


                                <button @click.prevent="getSearchResults" class="btn btn-success btn-sm">
                                    <i class="fa fa-search"></i>
                                </button>

                                <button v-if="isSearched" class="btn btn-default btn-sm" @click.prevent="clearResults" >
                                    <i aria-hidden="true" class="fa fa-refresh"></i>
                                </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">Customer ID</th>
                                <th class="column-title">Cleared By</th>
                                <th class="column-title">Confirm Time</th>
                                <th class="column-title">Payment Method</th>
                                <th class="column-title">Amount</th>
                                <th class="column-title">Currency</th>
                            </tr>
                            </thead>

                            <tbody>

                            <tr class="even pointer" v-for="deposit in deposits">
                                <td>{{ deposit.customerId }}</td>
                                <td>{{ deposit.clearedBy }}</td>
                                <td>{{ getConfirmTime(deposit) }}</td>
                                <td>{{ deposit.paymentMethod }}</td>
                                <td>{{ getNumberFormat(deposit.amount) }}</td>
                                <td>{{ deposit.currency }}</td>
                                <td>
                                    <a :href="'/compliance/customers/' + deposit.customerId" type="button"
                                       class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="bottom"
                                       :title="'View Customer ' +deposit.customerId">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <pagination :current_page="deposits_data.current_page"
                                    :prev_page_url="deposits_data.prev_page_url"
                                    :last_page="deposits_data.last_page"
                                    :next_page_url="deposits_data.next_page_url"
                                    :url_path="url"
                                    :total="deposits_data.total"
                                    name="deposits"
                                    :per_page="deposits_data.per_page"
                                    :updateRecords="updateRecords">
                        </pagination>
                    </div>

                </div>
            </div>
        </div>
    </div>


</template>

<script type="text/babel">

    import moment from 'moment';
    import Loader from '../../common/Loader.vue';
    import Pagination from '../../common/Pagination.vue';
    import {numberFormat} from '../../../helpers/functions';
    import Datepicker from 'vuejs-datepicker';


    export default {
        name: "pending-deposits",
        props: {

        },
        data(){
            return {
                deposits_data: {},
                deposits: [],
                isSearched: false,
                loading: false,
                search_query: '',
                search: {
                    customerId: '',
                    startDate: null,
                    endDate: null
                }
            }
        },
        created() {
            this.fetchDepositsData();
        },

        methods: {
            fetchDepositsData() {
                Vue.http.get('/compliance/pending/get-data').then((response) => {
                    this.deposits_data = response.body;
                    this.deposits = response.body.data;
                });
            },
            updateRecords(e) {
                let httpPath = e.target.getAttribute("href");
                Vue.http.get(httpPath).then((response) => {
                    this.deposits_data = response.body;
                    this.deposits = response.body.data;
                    window.scrollTo(0, 100);
                });
            },
            getSearchResults() {
                this.loading = true;
                this.isSearched = true;
                this.search_query = jQuery.param(this.search);
                Vue.http.get('/compliance/pending/search?' + this.search_query)
                        .then((response) => {
                            this.deposits_data = response.body;
                            this.deposits = response.body.data;
                            this.loading = false;
                });
            },
            clearResults() {
                this.search.customerId = '';
                this.search.startDate = null;
                this.search.endDate = null;
                this.isSearched = false;
                this.search_query = '';
                this.fetchDepositsData();
            },
            getConfirmTime(deposit) {
                return deposit.confirmTime ? moment(deposit.confirmTime).format("DD-MM-YYYY") : 'N/A';
            },

            getNumberFormat(num) {
                return numberFormat(num);
            }
        },
        computed: {
            url() {
                if (this.search_query) {
                    return "/compliance/pending/search?" + this.search_query + '&';
                }
                return "/compliance/pending/get-data?";
            }

        },
        components: {
            Pagination,
            Loader,
            Datepicker
        }
    }
</script>
<style scoped>
    .customer_id_input {
        display: inline-block;
        max-width: 150px;
        margin-right: 10px;
    }

    .pending-search{
        padding: 10px;
    }
</style>