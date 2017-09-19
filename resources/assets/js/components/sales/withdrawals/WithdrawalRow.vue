<template>
    <tr>
        <td>{{ withdrawal.id }}</td>
        <td>{{ withdrawal.customerId }}</td>
        <td>
            {{ withdrawal.customer ? withdrawal.customer.FirstName + ' ' + withdrawal.customer.LastName : "N/A" }}
        </td>
        <td>
            {{ currencies[withdrawal.currency] + getNumberFormat(this.withdrawal.amount) + " (" + withdrawal.currency +
            ")" }}
        </td>
        <td>{{ amountUsd }}</td>
        <td>{{ withdrawal.transactionID.length > 10 ? withdrawal.transactionID.substr(0, 10) + "\n..." :
            withdrawal.transactionID
            }}
        </td>
        <td>{{ withdrawal.paymentMethod }}</td>
        <td>{{ withdrawal.clearedBy }}</td>
        <td>
            {{ withdrawal.customer ? withdrawal.customer.verification : "N/A" }}
        </td>
        <td>{{ withdrawal.status }}</td>
        <td>
            <multiselect v-if="can_edit" v-model="selectedEmployee" @input="assignWithdrawalToEmployee" :options="employees"
                         placeholder="No worker selected" label="name" track-by="name" :multiple="false">
            </multiselect>
            <span v-else>{{ this.withdrawal.employee ? this.withdrawal.employee.name : 'No worker selected' }}</span>
        </td>
        <td>
            <multiselect v-if="can_edit" v-model="selectedType" @input="assignWithdrawalToType" :options="types" :searchable="false"
                         placeholder="Unknown" label="name" track-by="name" :multiple="false">
            </multiselect>
            <span v-else>{{ this.withdrawal.withdrawal_type ? selectedType.name : 'Unknown' }}</span>
        </td>
        <td>
            <multiselect v-if="can_edit" v-model="selectedTable" @input="assignWithdrawalToTable" :options="tables"
                         placeholder="No table selected" label="name" track-by="name" :multiple="false">
            </multiselect>
            <span v-else>{{ this.withdrawal.table ? this.withdrawal.table.name : 'No table selected' }}</span>
        </td>
        <td>{{ withdrawal.confirmTime ? date : "N/A" }}</td>
        <td>
            <ul v-if="withdrawal.is_split" class="splits-list">
                <li v-for="split in withdrawal.splits">
                    Split with : {{ split.to_employee.name }}<br/>
                    For: {{ currencies[withdrawal.currency] + getNumberFormat(split.amount) }}
                    <button v-if="can_edit" @click.prevent="removeSplit(split.id)" class="btn btn-xs btn-danger" data-toggle="tooltip"
                            data-placement="bottom"
                            title="Remove split"><i class="fa fa-trash-o"></i></button>
                </li>
            </ul>
        </td>
        <td style="text-align: center;">
            <button v-if="can_edit" class="btn btn-xs btn-success split_withdrawal withdrawal_actions"
                    v-show="(withdrawal.receptionEmployeeId != 0)"
                    @click="showSplitModal"data-toggle="tooltip"
                    data-placement="bottom"
                    title="Create split"><i class="fa fa-users"></i>
            </button>

            <button v-if="can_edit" class="btn btn-xs btn-success withdrawal_note withdrawal_actions" @click="showNotesModal" data-toggle="tooltip"
                    data-placement="bottom"
                    title="Read/Add note"><i class="fa fa-comment"></i>
            </button>
        </td>
    </tr>
</template>

<script type="text/babel">
    import moment from 'moment';
    import Multiselect from 'vue-multiselect';
    import {numberFormat} from '../../../helpers/functions';

    export default {
        name: 'withdrawal-row',

        props: {
            withdrawal: {
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
        data() {
            return {
                types: [{id: 1, name: 'FTD'}, {id: 2, name: 'RST'}]
            }
        },
        created() {
            this.withdrawal.table_id = this.withdrawal.table_id ? this.withdrawal.table_id : 0;
        },
        computed: {
            date() {
                return moment(this.withdrawal.confirmTime).format("DD-MM-YYYY HH:mm");
            },
            selectedEmployee(){
                if (!this.withdrawal.employee) {
                    return {};
                }
                return this.withdrawal.employee;
            },

            selectedTable() {
                if (!this.withdrawal.table) {
                    return {};
                }
                return this.withdrawal.table;

            },
            selectedType() {
                if (!this.withdrawal.withdrawal_type) {
                    return {};
                }
                if (this.withdrawal.withdrawal_type == 1) {
                    var name = 'FTD';
                } else {
                    name = 'RST';
                }
                return {id: this.withdrawal.withdrawal_type, name: name};
            },
            amountUsd() {
                return "$" + this.getNumberFormat(this.withdrawal.amount * this.withdrawal.rateUSD);
            }
        },
        methods: {
            getNumberFormat(num) {
                return numberFormat(num);
            },
            assignWithdrawalToEmployee(employee) {
                this.$emit("employeeSelected", {
                    employee_id: employee.id,
                    withdrawal_id: this.withdrawal.id
                });

            },
            assignWithdrawalToTable(table) {
                this.$emit('tableSelected', {
                    withdrawal_id: this.withdrawal.id,
                    table_id: table.id,
                    employee_id: this.withdrawal.receptionEmployeeId
                });
            },
            assignWithdrawalToType(type) {
                this.$emit('typeSelected', {
                    withdrawal_id: this.withdrawal.id,
                    type: type.id
                });
            },
            showSplitModal() {
                this.$emit('split', this.withdrawal)
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
                    Vue.http.post('/sales/remove-withdrawal-split', {
                        withdrawal_id: this.withdrawal.id,
                        split_id: split_id
                    }).then((response) => {
                        this.withdrawal.is_split = response.body.is_split;
                        this.withdrawal.splits = response.body.splits;
                        swal('Deleted!', 'Your file has been deleted.', 'success');
                    }).catch(errors => {
                        let error = Object.values(errors.body)[0][0];
                        swal("Oops...", error, "error");
                    });


                });
            },

            showNotesModal() {
                this.$emit('notes', this.withdrawal)
            }
        },
        components: {
            Multiselect
        }
    }
</script>
<style scoped>
    .withdrawal_actions {
        margin-bottom: 5px;
    }
    .splits-list {
        padding-left: 5px;
    }

    .splits-list li {
        list-style: none;
        margin-bottom: 10px;
    }
</style>