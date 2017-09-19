<template>
    <tr>
        <td>{{ withdrawal.id }}</td>
        <td><a target="_blank" :href="'https://spotcrm.ivoryoption.com/crm/customers/page/' + withdrawal.customerId">
            {{ withdrawal.customerId }}</a></td>
        <td>
            {{ currencies[withdrawal.currency] + getNumberFormat(withdrawal.amount) + " (" + withdrawal.currency + ")" }}
        </td>
        <td>{{ amountUsd }}</td>
        <td>
            {{ withdrawal.transactionID.length > 10 ? withdrawal.transactionID.substr(0, 10) + "\n..." : withdrawal.transactionID
            }}
        </td>
        <td>{{ withdrawal.paymentMethod }}</td>
        <td>{{ withdrawal.clearedBy }}</td>
        <td>{{ verification }}</td>
        <td>{{ employee }}</td>
        <td>{{ type }}</td>
        <td>{{ table }}</td>
        <td>{{ date }}</td>
        <td class="notes-td">
            <ul class="system-notes">
                <li v-if="withdrawal.splits" v-for="split in withdrawal.splits">
                    Split with : {{ split.to_employee.name }} <br/>
                    For {{ currencies[withdrawal.currency]  + getNumberFormat(split.amount) }}
                </li>
                <li v-for="note in withdrawal.notes">{{note.content}}</li>
            </ul>
        </td>
    </tr>
</template>

<script type="text/babel">
    import moment from 'moment';
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
            employees: {
                required: true
            },
        },

        data(){
            return{

            }
        },
        computed: {
            verification() {
                return this.withdrawal.customer ? this.withdrawal.customer.verification : "N/A";
            },
            employee() {
                return this.withdrawal.employee ? this.withdrawal.employee.name : 'Unknown';
            },
            type() {
                if (this.withdrawal.withdrawal_type == 1) {
                    return 'FTD';
                } else if (this.withdrawal.withdrawal_type == 2) {
                    return 'RST';
                } else {
                    return 'Unknown';
                }
            },
            table() {
                return this.withdrawal.table ? this.withdrawal.table.name : 'Unknown';
            },
            date() {
                return this.withdrawal.confirmTime ? moment(this.withdrawal.confirmTime).format("DD-MM-YYYY HH:mm:ss") : 'N/A';
            },
            amountUsd() {
                return "$" + this.getNumberFormat(this.withdrawal.amount * this.withdrawal.rateUSD);
            }

        },
        methods: {
            getNumberFormat(num) {
                return numberFormat(num);
            }
        },
        components:{

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