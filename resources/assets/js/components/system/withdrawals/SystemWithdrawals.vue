<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Withdrawals</h1>
            </div>
        </div>
        <div class="row">
            <loader v-if="loading"></loader>
            <div class="col-lg-12">
                <div class="panel">

                    <filters-panel
                            name="Withdrawal"
                            defaultStatus="approved"
                            :employees="employees"
                            :tables="tables"
                            @search="getSearchResults"
                            @fetch="fetchWithdrawalsData"
                            @download="downloadFilterResults">
                        <div slot="additional-buttons" id="additional-buttons">
                            <button v-if="!isSearched" class="btn btn-primary btn-sm" @click.prevent="downloadDaily" >
                                <i class="fa fa-file-excel-o"></i> Download Daily Report
                            </button>
                            <button v-if="!isSearched" class="btn btn-primary btn-sm" @click.prevent="downloadMonthly" >
                                <i class="fa fa-file-excel-o"></i> Download Monthly Report
                            </button>
                        </div>
                    </filters-panel>

                    <div class="panel-heading">
                        <i class="fa fa-usd"></i> All Withdrawals
                    </div>
                    <div class="panel-body">
                        <div id="withdrawals_wrapper">

                            <table class="table table-striped table-bordered table-hover" id="dataTables-withdrawals">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer ID</th>
                                    <th>Amount</th>
                                    <th>Amount USD</th>
                                    <th>Transaction ID</th>
                                    <th>Payment Method</th>
                                    <th>Cleared By</th>
                                    <th>Verification</th>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    <th>Table on assignment</th>
                                    <th>Confirm Time</th>
                                    <th>Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                <withdrawal-row v-for="withdrawal in withdrawals"
                                        :withdrawal="withdrawal"
                                        :currencies="currencies"
                                        :employees="employees">
                                </withdrawal-row>
                                </tbody>
                            </table>
                            <pagination :current_page="withdrawals_data.current_page"
                                        :prev_page_url="withdrawals_data.prev_page_url"
                                        :last_page="withdrawals_data.last_page"
                                        :next_page_url="withdrawals_data.next_page_url"
                                        :url_path="url"
                                        :total="withdrawals_data.total"
                                        name="withdrawals"
                                        :per_page="withdrawals_data.per_page"
                                        :updateRecords="updateRecords">
                            </pagination>
                        </div>
                    </div>
                </div>  <!-- /.panel-body -->

                <!-- /.panel -->
            </div>
        </div>

    </div>


</template>

<script type="text/babel">
    import moment from 'moment';

    import Loader from '../../common/Loader.vue';
    import FiltersPanel from '../../common/FiltersPanel.vue';

    import WithdrawalRow from './WithdrawalRow.vue';
    import Pagination from '../../common/Pagination.vue';

    import {exportToCsv} from '../../../helpers/functions.js';

    export default{
        name: 'system-withdrawals',
        props: {
            currencies: {
                required: true
            },
            employees: {
                required: true
            },
            tables: {
                required: true
            }
        },
        data(){
            return {
                withdrawals_data: {},
                withdrawals: [],
                search_query: '',
                loading: false,
                isSearched : false,
                cvsFields: ['ID', 'Customer ID', 'Customer Name', 'Amount', 'Currency', 'Amount USD', 'Transaction ID', 'Payment Method', 'Cleared By', 'Verification',
                    'Status', 'Employee', 'Type', 'Table', 'Confirm Time', 'Notes']
            }
        },
        created() {
            this.fetchWithdrawalsData();

        },

        computed: {
            url() {
                if (this.search_query) {
                    return "/system/withdrawals/search?" + this.search_query + '&';
                }
                return "/system/withdrawals/get-data?";
            }
        },
        methods: {
            fetchWithdrawalsData() {
                this.isSearched = false;
                Vue.http.get('/system/withdrawals/get-data').then((response) => {
                    this.withdrawals_data = response.body;
                    this.withdrawals = response.body.data;
                });
                this.search_query = '';
            },
            updateRecords(e){
                let httpPath = e.target.getAttribute("href");
                Vue.http.get(httpPath).then((response) => {
                    this.withdrawals_data = response.body;
                    this.withdrawals = response.body.data;
                    window.scrollTo(0, 100);
                });
            },
            getSearchResults(data) {
                this.loading = true;
                this.isSearched = true;
                this.search_query = jQuery.param(data);
                Vue.http.get('/system/withdrawals/search', {params: data}).then((response) => {
                    this.withdrawals_data = response.body;
                    this.withdrawals = response.body.data;
                    this.loading = false;
                });
            },
            downloadFilterResults(data) {
                Vue.http.get('/system/withdrawals/download-results', {params: data}).then((response) => {
                    var data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('withdrawals_result.csv', data);
                });
            },
            downloadDaily() {
                let today = moment().format("DD-MM-YYYY");
                Vue.http.get('/system/withdrawals/daily').then((response) => {
                    let data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('withdrawals_' + today + '.csv', data);
                });
            },
            downloadMonthly() {
                let date = moment().format("MM-YYYY");
                Vue.http.get('/system/withdrawals/monthly').then((response) => {
                    let data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('withdrawals_' + date + '.csv', data);
                });
            },
            toCsv (filename, rows) {
                return exportToCsv(filename, rows);
            },
        },

        components:{
            FiltersPanel,
            WithdrawalRow,
            Pagination,
            Loader
        }
    }
</script>
<style scoped>
    #additional-buttons {
        display: inline;
    }
</style>