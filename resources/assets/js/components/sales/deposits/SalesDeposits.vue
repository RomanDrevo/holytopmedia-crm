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
                        <div v-if="!isSearched" slot="additional-buttons" id="additional-buttons">
                            <button class="btn btn-primary btn-sm" @click.prevent="downloadDaily" >
                                <i class="fa fa-file-excel-o"></i> Download Daily Report
                            </button>
                            <button class="btn btn-primary btn-sm" @click.prevent="downloadMonthly" >
                                <i class="fa fa-file-excel-o"></i> Download Monthly Report
                            </button>
                        </div>
                    </filters-panel>
                    <div class="panel-heading">
                        <i class="fa fa-usd"></i> All deposits
                    </div>
                    <div class="panel-body">
                        <div id="deposits_wrapper">

                            <!--split modal-->
                            <modal v-if="split.showModal" :width="400">
                                <h3 slot="header">
                                    <button type="button" class="close" @click="closeSplit">&times;</button>
                                    Split with an employee:
                                </h3>

                                <div slot="body">
                                    <div class="form-group">
                                        <label>Other Employee</label>
                                        <multiselect v-model="selectedEmployee" :options="employees"
                                                     @input="selectSplitEmployee"
                                                     placeholder="No worker selected" label="name" track-by="name">
                                        </multiselect>
                                    </div>

                                    <div class="form-group">
                                        <label for="amount">Amount <span style="font-size: 12px;">(This amount will be taken from this employee)</span></label>
                                        <input type="text" name="amount" class="form-control" v-model="split.amount"
                                               id="amount">
                                    </div>
                                </div>
                                <div slot="footer">
                                    <p class="split-error" v-show="split.isError">{{ split.errorMessage }}</p>
                                    <button type="button" class="btn btn-success" @click="createSplit">Create
                                        Split
                                    </button>
                                </div>
                            </modal><!--split modal-->

                            <!--notes modal-->
                            <modal v-if="note.showModal" :width="400">
                                <h3 slot="header">
                                    <button type="button" class="close" @click="closeNotes">&times;</button>
                                    Deposit Notes
                                </h3>

                                <div slot="body">
                                    <ol class="notes">
                                        <li v-for="note in note.notes">
                                            <div>{{ note.content }}
                                                <div class="pull-right">
                                                    {{ posted(note) }}
                                                </div>
                                            </div>
                                            <hr style="clear:both"/>
                                        </li>
                                    </ol>

                                    <div class="form-group">
                                        <label for="deposit_note">Add Note:</label>
                                        <textarea id="deposit_note" class="form-control" name="deposit_note"
                                                  v-model="note.deposit_note"></textarea>
                                    </div>
                                </div>
                                <div slot="footer">
                                    <p class="note-error" v-show="note.isError">{{ note.errorMessage }}</p>
                                    <button type="button" class="btn btn-success" @click="addNote">Add Note</button>
                                </div>
                            </modal><!--notes modal-->

                            <table class="table table-striped table-bordered table-hover" id="dataTables-deposits">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer ID</th>
                                    <th>Customer Name</th>
                                    <th>Amount</th>
                                    <th>Amount USD</th>
                                    <th>Transaction ID</th>
                                    <th>Payment Method</th>
                                    <th>Cleared By</th>
                                    <th>Verification</th>
                                    <th>Status</th>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    <th>Table on assignment</th>
                                    <th>Confirm Time</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-for="deposit in deposits">

                                    <template v-if="!deposit.is_split">
                                        <deposit-row
                                                :deposit="deposit"
                                                :employees="employees"
                                                :currencies="currencies"
                                                :tables="tables"
                                                :can_edit="can_edit"
                                                @split="splitModal"
                                                @notes="notesModal"
                                                @employeeSelected="updateDepositEmployee"
                                                @tableSelected="updateDepositTable"
                                                @typeSelected="updateDepositType">
                                        </deposit-row>
                                    </template>
                                    <template v-else>
                                        <deposit-row
                                                :deposit="deposit"
                                                :employees="employees"
                                                :currencies="currencies"
                                                :tables="tables"
                                                :can_edit="can_edit"
                                                @split="splitModal"
                                                @notes="notesModal"
                                                @employeeSelected="updateDepositEmployee"
                                                @tableSelected="updateDepositTable"
                                                @typeSelected="updateDepositType">
                                        </deposit-row>
                                        <deposit-split-row
                                                v-for="split in deposit.splits"
                                                :deposit="deposit"
                                                :tables="tables"
                                                :currencies="currencies"
                                                :split="split"></deposit-split-row>
                                    </template>

                                </template>

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
    import DepositSplitRow from './DepositSplitRow.vue';
    import Pagination from '../../common/Pagination.vue';
    import Multiselect from 'vue-multiselect';

    import {exportToCsv} from '../../../helpers/functions.js';


    export default {
        name: "sales-deposits",
        props: {
            employees: {
                required: true
            },
            tables: {
                required: true
            },
            currencies: {
                required: true
            },
            can_edit: {
                required: true
            }
        },

        data() {
            return {
                isSearched: false,
                search_query: '',
                selectedEmployee: {},
                deposits_data: {},
                deposits: [],
                deposit: null,
                loading: false,

                split: {
                    showModal: false,
                    employee_id: 0,
                    amount: 0,
                    isError: false,
                    errorMessage: ''
                },
                note: {
                    showModal: false,
                    isError: false,
                    errorMessage: '',
                    notes: [],
                    deposit_note: ''
                },
                cvsFields: ['ID', 'Customer ID', 'Customer Name', 'Amount', 'Currency', 'Amount USD', 'Transaction ID', 'Payment Method', 'Cleared By', 'Verification',
                    'Is Approved', 'Employee', 'Type', 'Table', 'Confirm Time', 'Notes']
            }
        },
        created() {
            this.fetchDepositsData();
        },


        methods: {
            selectSplitEmployee(employee) {
                this.selectedEmployee = employee;
            },
            updateDepositEmployee(data) {
                Vue.http.post('/sales/assign-deposit-to-employee', {
                    deposit_id: data.deposit_id,
                    employee_id: data.employee_id
                }).then((response) => {
                    _.map(this.deposits, deposit => {
                        if (deposit.id == data.deposit_id) {
                            deposit.employee = response.body.employee;
                            deposit.receptionEmployeeId = response.body.receptionEmployeeId;
                            deposit.table_id = response.body.table_id;
                        }
                    })
                }).catch(errors => {
                    let error = Object.values(errors.body)[0];
                    swal("Error", error, "error");
                });
            },
            updateDepositTable(data) {
                Vue.http.post('/sales/assign-deposit-to-table', {
                    deposit_id: data.deposit_id,
                    table_id: data.table_id,
                    employee_id: data.employee_id

                }).then((response) => {
                    _.map(this.deposits, deposit => {

                        if (deposit.id == data.deposit_id) {

                            deposit.table = response.body.table;
                            deposit.deposit_type = response.body.deposit_type;
                            deposit.table_id = response.body.table_id;
                        }
                    });
                });
            },

            updateDepositType(data) {
                Vue.http.post('/sales/assign-deposit-to-type', {
                    deposit_id: data.deposit_id,
                    type: data.type
                }).then((response) => {
                    _.map(this.deposits, deposit => {
                        if (deposit.id == data.deposit_id) {
                            deposit.deposit_type = response.body.deposit_type;
                        }
                    });

                });

            },
            fetchDepositsData() {
                this.isSearched = false;
                Vue.http.get('/sales/deposits/get-data').then((response) => {
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
                Vue.http.get('/sales/deposits/search', {params: data}).then((response) => {
                    this.deposits_data = response.body;
                    this.deposits = response.body.data;
                    this.loading = false;
                });

            },
            splitModal(deposit) {
                this.deposit = deposit;
                this.split.showModal = true;
            },

            closeSplit() {
                this.split.isError = false;
                this.split.showModal = false;
                this.split.employee_id = 0;
                this.split.amount = 0;
                this.selectedEmployee = {};
            },

            createSplit () {
                this.$http.post('/sales/create-new-split', {
                    deposit_id: this.deposit.id,
                    split_employee_id: this.selectedEmployee.id,
                    split_amount: parseInt(this.split.amount)
                }).then((response) => {
                    this.closeSplit();

                    this.deposit.is_split = true;

                    this.deposit.splits.push(response.body.split);
                    _.map(this.deposits, (deposit) => {
                        if (deposit.id == this.deposit.id) {
                            deposit = this.deposit;
                        }
                    });

                })
                        .catch(errors => {
                            if (Object.values(errors.body)[0]) {
                                let error = Object.values(errors.body)[0][0];
                                swal("Oops...", error, "error");
                            } else {
                                swal("Oops...", errors.body, "error");
                            }
                        });
            },
            notesModal(deposit) {
                this.deposit = deposit;
                this.note.showModal = true;
                this.note.notes = deposit.notes;
            },

            closeNotes() {
                this.note.isError = false;
                this.note.errorMessage = '';
                this.note.showModal = false;
            },
            posted(note){
                return moment(note.created_at).fromNow();
            },

            addNote() {
                this.$http.post('/sales/add-note', {
                    deposit_id: this.deposit.id,
                    deposit_note: this.note.deposit_note
                }).then((response) => {
                    if (typeof(response.body) == 'string') {
                        this.note.isError = true;
                        this.note.errorMessage = response.body;
                    } else {
                        this.note.notes.push(response.body);
                        this.note.deposit_note = '';
                        this.note.isError = false;
                    }

                });
            },
            downloadFilterResults(data) {
                Vue.http.get('/sales/deposits/download-results', {params: data}).then((response) => {
                    let data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('deposits_result.csv', data);
                });
            },
            toCsv (filename, rows) {
                return exportToCsv(filename, rows);
            },

            downloadDaily() {
                let today = moment().format("DD-MM-YYYY");
                Vue.http.get('/sales/deposits/daily').then((response) => {
                    let data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('deposits_' + today + '.csv', data);
                });
            },
            downloadMonthly() {
                let date = moment().format("MM-YYYY");
                Vue.http.get('/sales/deposits/monthly').then((response) => {
                    let data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('deposits_' + date + '.csv', data);
                });
            },

            depositComputed(deposit){
                if (!deposit.is_split) {
                    return "deposit-row";
                }
                return "deposit-split-row";

            }

        },
        computed: {
            url() {
                if (this.search_query) {
                    return "/sales/deposits/search?" + this.search_query + '&';
                }
                return "/sales/deposits/get-data?"
            }


        },
        components: {
            FiltersPanel,
            DepositRow,
            DepositSplitRow,
            Pagination,
            Multiselect,
            Loader
        }
    }
</script>

<style>

    #additional-buttons {
        display:inline;
    }
    input#amount {
        width: 90%;
    }

    .notes {
        padding-left: 0;
    }

    .notes hr {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .split-error, .note-error {
        color: red;
    }



</style>