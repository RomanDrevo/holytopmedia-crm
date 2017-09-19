<template>
    <tr>
        <td>{{ deposit.id }}</td>
        <td>{{ deposit.customerId }}</td>
        <td>
            {{ deposit.customer ? deposit.customer.FirstName + ' ' + deposit.customer.LastName : "N/A" }}
        </td>
        <td>
            {{ currencies[deposit.currency] + getNumberFormat(this.deposit.amount) + " (" + deposit.currency + ")" }}
        </td>
        <td>{{ amountUsd }}</td>
        <td>{{ deposit.transactionID.length > 10 ? deposit.transactionID.substr(0, 10) + "\n..." : deposit.transactionID
            }}
        </td>
        <td>{{ deposit.paymentMethod }}</td>
        <td>{{ deposit.clearedBy }}</td>
        <td>
            {{ deposit.customer ? deposit.customer.verification : "N/A" }}
        </td>
        <td>{{ deposit.status }}</td>
        <td>

            <multiselect v-if="can_edit" v-model="selectedEmployee" @input="assignDepositToEmployee" :options="employees"
                         placeholder="No worker selected" label="name" track-by="name" :multiple="false">
            </multiselect>
            <span v-else>{{ this.deposit.employee ? this.deposit.employee.name : 'No worker selected' }}</span>
        </td>
        <td>
            <multiselect v-if="can_edit" v-model="selectedType" @input="assignDepositToType" :options="types" :searchable="false"
                         placeholder="Unknown" label="name" track-by="name" :multiple="false">
            </multiselect>
            <span v-else>{{ this.deposit.deposit_type ? selectedType.name : 'Unknown' }}</span>

        </td>
        <td>
            <multiselect v-if="can_edit" v-model="selectedTable" @input="assignDepositToTable" :options="tables"
                         placeholder="No table selected" label="name" track-by="name" :multiple="false">
            </multiselect>
            <span v-else>{{ this.deposit.table ? this.deposit.table.name : 'No table selected' }}</span>
        </td>
        <td>
            <datepicker v-if="can_edit" v-model="deposit.assigned_at" :format="format" wrapper-class="assign-date" input-class="assign-input" v-on:selected="assignDepositToTime"></datepicker>
            <p>{{ formatted }}</p>
        </td>
        <td>
            <ul v-if="deposit.is_split" class="splits-list">
                <li v-for="split in deposit.splits">
                    Split with : {{ split.to_employee.name }}<br/>
                    For: {{ currencies[deposit.currency] + getNumberFormat(split.amount) }}
                    <button v-if="can_edit" @click.prevent="removeSplit(split.id)" class="btn btn-xs btn-danger" data-toggle="tooltip"
                            data-placement="bottom"
                            title="Remove split"><i class="fa fa-trash-o"></i></button>
                </li>
            </ul>
        </td>
        <td style="text-align: center;">
            <button v-if="can_edit" class="btn btn-xs btn-success split_deposit deposit_actions"
                    v-show="(deposit.receptionEmployeeId != 0)"
                    @click="showSplitModal" data-toggle="tooltip"
                    data-placement="bottom"
                    title="Create split"><i class="fa fa-users"></i>
            </button>
            <button v-if="can_edit" class="btn btn-xs btn-success deposit_note deposit_actions" @click="showNotesModal"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Read/Add note"><i class="fa fa-comment"></i>
            </button>
        </td>
    </tr>
</template>

<script type="text/babel">
    import moment from 'moment';
    import Datepicker from 'vuejs-datepicker'
    import Multiselect from 'vue-multiselect';
    import {numberFormat} from '../../../helpers/functions';
    export default {
        name: 'deposit-row',

        props: {
            deposit: {
                required: true
            },
            currencies: {
                required: true
            },
            tables: {
                required: true
            },
            employees: {
                required: true
            },
            can_edit: {
                required: true
            }

        },

        mounted(){
            this.deposit.table_id = this.deposit.table_id ? this.deposit.table_id : 0;
        },

        computed: {
            selectedEmployee(){
                if (!this.deposit.employee) {
                    return {};
                }
                return this.deposit.employee;
            },

            selectedTable() {
                if (!this.deposit.table) {
                    return {};
                }
                return this.deposit.table;

            },

            selectedType() {
                if (!this.deposit.deposit_type) {
                    return {};
                }
                if (this.deposit.deposit_type == 1) {
                    var name = 'FTD';
                } else {
                    name = 'RST';
                }
                return {id: this.deposit.deposit_type, name: name};
            },

            splitEmployee () {
                let deposit = this.deposit;
                let name = '';
                if (deposit.split) {
                    this.employees.forEach(function (employee) {
                        if (employee.employee_crm_id == deposit.split.employee_id) {
                            name = employee.name;
                            return name;
                        }
                    });
                }
                return name;
            },

            formatted() {
                return this.deposit.assigned_at ? moment(this.deposit.assigned_at).format('DD-MM-YYYY HH:mm:ss') : 'N/A';
            },

            amountUsd() {
                return "$" + this.getNumberFormat(this.deposit.amount * this.deposit.rateUSD);
            },
        },

        data() {
            return {
                types: [{id: 1, name: 'FTD'}, {id: 2, name: 'RST'}],
                format: 'dd-MM-yyyy',
            }
        },


        components: {
            Multiselect,
            Datepicker
        },


        methods: {
            showSplitModal() {
                this.$emit('split', this.deposit)
            },
            showNotesModal() {
                this.$emit('notes', this.deposit)
            },

            removeSplit(split_id) {
                swal({
                    title: 'Are you sure?',
                    text: "You are cancelling the split",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(() => {

                    Vue.http.post('/sales/remove-split', {
                        deposit_id: this.deposit.id,
                        split_id: split_id
                    }).then((response) => {
                        this.deposit.is_split = response.body.is_split;
                        this.deposit.splits = response.body.splits;
                        swal('Deleted!', 'Your file has been deleted.', 'success');
                    }).catch(errors => {
                        let error = Object.values(errors.body)[0][0];
                        swal("Oops...", error, "error");
                    });


                });
            },

            assignDepositToTable(table) {
                this.$emit('tableSelected', {
                    deposit_id: this.deposit.id,
                    table_id: table.id,
                    employee_id: this.deposit.receptionEmployeeId
                });

            },

            assignDepositToEmployee(employee) {
                this.$emit("employeeSelected", {
                    employee_id: employee.id,
                    deposit_id: this.deposit.id
                });

            },
            assignDepositToType(type) {
                this.$emit('typeSelected', {
                    deposit_id: this.deposit.id,
                    type: type.id
                });

            },
            assignDepositToTime() {
                swal({
                    title: 'Are you sure?',
                    text: "You are changing deposit assign date",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then(() => {
                    Vue.http.post('/sales/assign-deposit-to-time', {
                        deposit_id: this.deposit.id,
                        assigned_at: this.deposit.assigned_at
                    }).then((response) => {
                        this.formatted = moment(response.body).format('DD-MM-YYYY HH:mm:ss');
                        this.deposit.assigned_at = response.body;
                    }).catch(errors => {
                        swal("Oops...", errors.body.message, "error");
                    });
                });
            },

            getNumberFormat(num) {
                return numberFormat(num);
            }

        }
    }
</script>

<style scoped>
    #deposit-type {
        padding: 6px;
    }
    .splits-list {
        padding-left: 5px;
    }

    .splits-list li {
        list-style: none;
        margin-bottom: 10px;
    }


    .assign-date {
        width:130px;
    }

</style>