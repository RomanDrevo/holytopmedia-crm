<template>
    <div class="panel panel-primary">
        <div class="panel-heading">Deposits (Total: {{ customer.currency }} {{ totalDeposits }})</div>
        <div class="panel-body">
            <table id="deposits_table" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                    <th>Approved?</th>
                    <th>Approve Deposits</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                    <th>Approved?</th>
                    <th>Approve Deposit</th>
                </tr>
                </tfoot>
                <tbody>

                <tr v-if="!allCustomerDeposits.length">
                    <td colspan="7" class="text-center">No Deposits Available</td>
                </tr>

                <tr v-for="deposit in allCustomerDeposits">
                    <td>{{ deposit.id }}</td>
                    <td>{{ deposit.paymentMethod }}</td>
                    <td>{{ deposit.clearedBy }}</td>
                    <td>{{ deposit.amount }}</td>
                    <td>{{ deposit.confirmTime }}</td>
                    <td>{{ deposit.approved ? 'YES' : 'NO' }}</td>
                    <td>
                        <button class="approve_deposit btn btn-sm" :class="deposit.approved ? 'btn-success' : 'btn-danger'" :data-deposit-id="deposit.id" :value="deposit.id" @click.prevent="$emit('onStatusChange', deposit.id)">
                            <i :class="deposit.approved ? 'fa fa-check' : 'fa fa-times'"></i>
                        </button>
                    </td>
                </tr>

                </tbody>
            </table>

        </div>
    </div>
</template>

<script type="text/babel">

    export default{
        props:{
            customer:{
                required: true
            },
            totalDeposits:{
                required: true
            },
            totalCCdeposits:{
                required: true
            },
            allCustomerDeposits:{
                required: true
            }
        }
    }
</script>

