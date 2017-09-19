<template>
    <div class="panel panel-primary">
        <div class="panel-heading">Withdrawals</div>
        <div class="panel-body">
            <table id="withdrawals_table" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Requested at</th>
                    <th>Confirmed at</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Requested at</th>
                    <th>Confirmed at</th>
                </tr>
                </tfoot>
                <tbody>

                <tr>
                    <td colspan="5" class="text-center">No Withdrawals Available</td>
                </tr>

                <tr v-for="withdrawal in withdrawals">
                    <td>{{ withdrawal.id }}</td>
                    <td>{{ withdrawal.paymentMethod }}</td>
                    <td>{{ withdrawal.amount }}</td>
                    <td>{{ withdrawal.requestTime }}</td>
                    <td>{{ withdrawal.confirmTime}}</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</template>

<script type="text/babel">
    export default{
        props:{
            withdrawals:{
                required: true
            }
        }
    }
</script>
