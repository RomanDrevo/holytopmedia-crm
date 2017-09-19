<template>
    <tr style="background-color: #e8e1e1;">
        <td>{{ deposit.id }}</td>
        <td>{{ deposit.customerId }}</td>

        <td>
            {{ deposit.customer ? deposit.customer.FirstName + ' ' + deposit.customer.LastName : "N/A" }}
        </td>
        <td>
            {{ currencies[deposit.currency] + getNumberFormat(this.split.amount) + " (" + deposit.currency + ")" }}
        </td>
        <td>{{ amountUSD }}</td>
        <td>{{ deposit.transactionID.length > 10 ? deposit.transactionID.substr(0, 10) + "\n..." : deposit.transactionID
            }}
        </td>
        <td>{{ deposit.paymentMethod }}</td>
        <td>{{ deposit.clearedBy }}</td>
        <td>
            {{ deposit.customer ? deposit.customer.verification : "N/A" }}
        </td>
        <td>{{ deposit.approved ? 'YES' : 'NO' }}</td>
        <td>{{ split.to_employee.name }}</td>
        <td>{{ type }}</td>
        <td>{{ table }}</td>
        <td>{{ assigned_at }}</td>
        <td>This is split for deposit ID: {{ deposit.id }}</td>
        <td></td>
    </tr>
</template>

<script type="text/babel">
    import moment from 'moment';

    import {numberFormat} from '../../../helpers/functions';
    export default {
        name: 'deposit-split-row',

        props: {
            deposit: {
                required: true
            },
            split: {
                required: true
            },
            currencies: {
                required: true
            },
            tables: {
                required: true
            },

        },
        computed: {
            type() {
                if (this.deposit.deposit_type == 1) {
                    return 'FTD';
                }
                if (this.deposit.deposit_type == 2) {
                    return 'RST';
                }
                return 'Unknown'
            },
            table() {
                let table_name = 'Unknown';
                if (this.split.to_employee.table_id) {
                    _.map(this.tables, table => {
                        if (table.id == this.split.to_employee.table_id) {
                            table_name = table.name;
                        }

                    })
                }
                return table_name;

            },
            assigned_at () {
                return this.deposit.assigned_at ? moment(this.deposit.assigned_at).format('DD-MM-YYYY HH:mm:ss') : 'N/A';
            },
            amountUSD() {
                return "$" + this.getNumberFormat(this.deposit.amount * this.deposit.rateUSD);
            }
        },

        methods: {
            getNumberFormat(num) {
                return numberFormat(num);
            }

        }
    }
</script>

<style scoped>

</style>