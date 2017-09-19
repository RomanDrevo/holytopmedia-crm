<template>
    <div class="panel panel-primary">
        <div class="panel-heading">Bonuses</div>
        <div class="panel-body">
            <table id="bonuses_table" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                </tr>
                </tfoot>
                <tbody>

                <tr v-if="customerBonuses.length == 0">
                    <td colspan="5" class="text-center">No Bonuses Available</td>
                </tr>


                <tr v-for="bonus in customerBonuses">
                    <td>{{ bonus.id }}</td>
                    <td>{{ bonus.paymentMethod }}</td>
                    <td>{{ bonus.clearedBy }}</td>
                    <td>{{ bonus.amount }}</td>
                    <td>{{ bonus.confirmTime }}</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</template>
<style>

</style>
<script type="text/babel">

    export default{
        props:{
            customerBonuses:{
                required: true
            }
        }
    }
</script>
