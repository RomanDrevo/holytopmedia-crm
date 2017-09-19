<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Deposits</h1>
            </div>
        </div>
        <div class="row">
            <loader v-if="loading"></loader>
            <div class="col-lg-12">
                <div class="panel">

                    <filters-panel
                            name="Deposit"
                            :employees="employees"
                            :tables="tables"
                            @search="getSearchResults"
                            @fetch="fetchDepositsData"
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
                        <i class="fa fa-usd"></i> All deposits
                    </div>
                    <div class="panel-body">
                        <div id="deposits_wrapper">

                            <table class="table table-striped table-bordered table-hover" id="dataTables-deposits">
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
                                    <deposit-row
                                            v-for="deposit in deposits"
                                            :deposit="deposit"
                                            :currencies="currencies"
                                            :employees="employees">
                                    </deposit-row>
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

    import DepositRow from './DepositRow.vue';
    import Pagination from '../../common/Pagination.vue';

    import {exportToCsv} from '../../../helpers/functions.js';

    export default{
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
        data() {
            return {
                deposits_data: {},
                deposits: [],
                search_query: '',
                loading: false,
                isSearched : false,
                cvsFields: ['ID', 'Customer ID', 'Customer Name', 'Amount', 'Currency', 'Amount USD', 'Transaction ID', 'Payment Method', 'Cleared By', 'Verification',
                    'Is Approved', 'Employee', 'Type', 'Table', 'Confirm Time', 'Notes']
            }
        },
        created() {
            this.fetchDepositsData();
        },
        computed: {
            url() {
                if (this.search_query) {
                    return "/system/deposits/search?" + this.search_query + '&';
                }
                return "/system/deposits/get-data?";
            }
        },

        methods: {
            fetchDepositsData() {
                this.isSearched = false;
                Vue.http.get('/system/deposits/get-data').then((response) => {
                    this.deposits_data = response.body;
                    this.deposits = response.body.data;
                });
                this.search_query = '';
            },
            updateRecords(e){
                let httpPath = e.target.getAttribute("href");
                Vue.http.get(httpPath).then((response) => {
                    this.deposits_data = response.body;
                    this.deposits = response.body.data;
                    window.scrollTo(0, 100);
                });
            },
            getSearchResults(data) {
                this.loading = true;
                this.isSearched = true;
                this.search_query = jQuery.param(data);
                Vue.http.get('/system/deposits/search', {params: data}).then((response) => {
                    this.deposits_data = response.body;
                    this.deposits = response.body.data;
                    this.loading = false;
                });
            },
            toCsv (filename, rows) {
                return exportToCsv(filename, rows);
            },

            downloadFilterResults(data) {
                Vue.http.get('/system/deposits/download-results', {params: data}).then((response) => {
                    var data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('deposits_result.csv', data);
                });
            },

            downloadDaily() {
                let today = moment().format("DD-MM-YYYY");
                Vue.http.get('/system/deposits/daily').then((response) => {
                    let data = response.body;
                    console.log(data);
                    data.unshift(this.cvsFields);
                    this.toCsv('deposits_' + today + '.csv', data);
                });
            },
            downloadMonthly() {
                let date = moment().format("MM-YYYY");
                Vue.http.get('/system/deposits/monthly').then((response) => {
                    let data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('deposits_' + date + '.csv', data);
                });

            },

        },
        components: {
            FiltersPanel,
            DepositRow,
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