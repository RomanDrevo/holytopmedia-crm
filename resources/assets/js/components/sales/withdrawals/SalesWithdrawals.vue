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
                    </filters-panel>

                    <div class="panel-heading">
                        <i class="fa fa-usd"></i> All Withdrawals
                    </div>
                    <div class="panel-body">
                        <div id="withdrawals_wrapper">

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
                                    Withdrawal Notes
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
                                        <label for="withdrawal_note">Add Note:</label>
                                        <textarea id="withdrawal_note" class="form-control" name="withdrawal_note"
                                                  v-model="note.withdrawal_note"></textarea>
                                    </div>
                                </div>
                                <div slot="footer">
                                    <p class="note-error" v-show="note.isError">{{ note.errorMessage }}</p>
                                    <button type="button" class="btn btn-success" @click="addNote">Add Note</button>
                                </div>
                            </modal><!--notes modal-->

                            <table class="table table-striped table-bordered table-hover" id="dataTables-withdrawals">
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
                                <withdrawal-row
                                        v-for="withdrawal in withdrawals"
                                        :employees="employees"
                                        :withdrawal="withdrawal"
                                        :currencies="currencies"
                                        :tables="tables"
                                        :can_edit="can_edit"
                                        @split="splitModal"
                                        @notes="notesModal"
                                        @employeeSelected="updateWithdrawalEmployee"
                                        @tableSelected="updateWithdrawalTable"
                                        @typeSelected="updateWithdrawalType">
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
    import Pagination from '../../common/Pagination.vue';
    import Multiselect from 'vue-multiselect';
    import WithdrawalRow from './WithdrawalRow.vue';

    import {exportToCsv} from '../../../helpers/functions.js';

    export default {
        name: 'sales-withdrawals',
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
                search_query: '',
                withdrawals_data: {},
                withdrawals: [],
                withdrawal: null,
                selectedEmployee: {},
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
                    withdrawal_note: ''
                },
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
                    return "/sales/withdrawals/search?" + this.search_query + '&';
                }
                return "/sales/withdrawals/get-data?"
            }
        },

        methods: {
            fetchWithdrawalsData() {
                Vue.http.get('/sales/withdrawals/get-data').then((response) => {
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
                this.search_query = jQuery.param(data);
                Vue.http.get('/sales/withdrawals/search', {params: data}).then((response) => {
                    this.withdrawals_data = response.body;
                    this.withdrawals = response.body.data;
                    this.loading = false;
                });
            },

            downloadFilterResults(data) {
                Vue.http.get('/sales/withdrawals/download-results', {params: data}).then((response) => {
                    let data = response.body;
                    data.unshift(this.cvsFields);
                    this.toCsv('withdrawals_result.csv', data);
                });
            },
            updateWithdrawalEmployee(data) {
                Vue.http.post('/sales/assign-withdrawal-to-employee', {
                    withdrawal_id: data.withdrawal_id,
                    employee_id: data.employee_id
                }).then((response) => {
                    _.map(this.withdrawals, withdrawal => {
                        if (withdrawal.id == data.withdrawal_id) {
                            withdrawal.employee = response.body.employee;
                            withdrawal.receptionEmployeeId = response.body.receptionEmployeeId;
                            withdrawal.table_id = response.body.table_id;
                        }
                    })
                }).catch(errors => {
                    let error = Object.values(errors.body)[0];
                    swal("Error", error, "error");
                });

            },
            updateWithdrawalTable(data) {
                Vue.http.post('/sales/assign-withdrawal-to-table', {
                    withdrawal_id: data.withdrawal_id,
                    table_id: data.table_id,
                    employee_id: data.employee_id
                }).then((response) => {
                    _.map(this.withdrawals, withdrawal => {
                        if (withdrawal.id == data.withdrawal_id) {
                            withdrawal.table = response.body.table;
                            withdrawal.withdrawal_type = response.body.withdrawal_type;
                            withdrawal.table_id = response.body.table_id;
                        }
                    });
                });
            },
            updateWithdrawalType(data) {
                Vue.http.post('/sales/assign-withdrawal-to-type', {
                    withdrawal_id: data.withdrawal_id,
                    type: data.type
                }).then((response) => {
                    _.map(this.withdrawals, withdrawal => {
                        if (withdrawal.id == data.withdrawal_id) {
                            withdrawal.withdrawal_type = response.body.withdrawal_type;
                        }
                    });

                });
            },
            selectSplitEmployee(employee) {
                this.selectedEmployee = employee;
            },
            splitModal(withdrawal) {
                this.withdrawal = withdrawal;
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
                this.$http.post('/sales/create-new-withdrawal-split', {
                    withdrawal_id: this.withdrawal.id,
                    split_employee_id: this.selectedEmployee.id,
                    split_amount: parseInt(this.split.amount)
                }).then((response) => {
                    this.closeSplit();

                    this.withdrawal.is_split = true;
                    this.withdrawal.splits.push(response.body.split);
                    _.map(this.withdrawals, (withdrawal) => {
                        if (withdrawal.id == this.withdrawal.id) {
                            withdrawal = this.withdrawal;
                        }
                    });

                }).catch(errors => {
                            if (Object.values(errors.body)[0]) {
                                let error = Object.values(errors.body)[0][0];
                                swal("Oops...", error, "error");
                            } else {
                                swal("Oops...", errors.body, "error");
                            }
                        });
            },

            notesModal(withdrawal) {
                this.withdrawal = withdrawal;
                this.note.showModal = true;
                this.note.notes = withdrawal.notes;
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
                this.$http.post('/sales/add-withdrawal-note', {
                    withdrawal_id: this.withdrawal.id,
                    withdrawal_note: this.note.withdrawal_note
                }).then((response) => {
                    if (typeof(response.body) == 'string') {
                        this.note.isError = true;
                        this.note.errorMessage = response.body;
                    } else {
                        this.note.notes.push(response.body);
                        this.note.withdrawal_note = '';
                        this.note.isError = false;
                    }

                });
            },
            toCsv (filename, rows) {
                return exportToCsv(filename, rows);
            }
        },
        components: {
            Loader,
            FiltersPanel,
            WithdrawalRow,
            Pagination,
            Multiselect
        }
    }
</script>
<style scoped>
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