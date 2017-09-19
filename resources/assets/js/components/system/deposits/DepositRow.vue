<template>
    <tr>
        <td>{{ deposit.id }}</td>
        <td><a target="_blank" :href="'https://spotcrm.ivoryoption.com/crm/customers/page/' + deposit.customerId">
            {{ deposit.customerId }}</a></td>
        <td>
            {{ currencies[deposit.currency] + getNumberFormat(deposit.amount) + " (" + deposit.currency + ")" }}
        </td>
        <td>{{ amountUsd }}</td>
        <td>
            {{ deposit.transactionID.length > 10 ? deposit.transactionID.substr(0, 10) + "\n..." : deposit.transactionID
            }}
        </td>
        <td>{{ deposit.paymentMethod }}</td>
        <td>{{ deposit.clearedBy }}</td>
        <td>{{ verification }}</td>
        <td>{{ employee }}</td>
        <td>{{ type }}</td>
        <td>{{ table }}</td>
        <td>{{ date }}</td>
        <td class="notes-td">
            <ul class="system-notes">
                <li v-if="deposit.splits" v-for="split in deposit.splits">
                    Split with : {{ split.to_employee.name }} <br/>
                    For {{ currencies[deposit.currency]  + getNumberFormat(split.amount) }}
                </li>
                <li v-for="note in deposit.notes">{{note.content}}</li>
            </ul>
        </td>
    </tr>
</template>

<script type="text/babel">
    import moment from 'moment';
    import {numberFormat} from '../../../helpers/functions';

    export default{
        name: 'deposit-row',

        props: {
            deposit: {
                required: true
            },
            currencies: {
                required: true
            },
            employees: {
                required: true
            },
        },
        data() {
            return {}
        },

        computed: {
            verification() {
                return this.deposit.customer ? this.deposit.customer.verification : "N/A";
            },
            employee() {
                return this.deposit.employee ? this.deposit.employee.name : 'Unknown';
            },
            type() {
                if (this.deposit.deposit_type == 1) {
                    return 'FTD';
                } else if (this.deposit.deposit_type == 2) {
                    return 'RST';
                } else {
                    return 'Unknown';
                }
            },
            table() {
                return this.deposit.table ? this.deposit.table.name : 'Unknown';
            },
            date() {
                return this.deposit.assigned_at ? moment(this.deposit.assigned_at).format("DD-MM-YYYY HH:mm:ss") : 'N/A';
            },
            amountUsd() {
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
    .system-notes {
       padding-left: 15px;
    }
    .notes-td {
        max-width: 180px;
    }

</style>