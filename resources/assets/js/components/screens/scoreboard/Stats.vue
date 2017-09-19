<template>
    <div class="stats">
        <div class="well">
            <table>
                <tr>
                    <td>Table</td>
                    <td>{{ table.name }}</td>
                </tr>
                <tr>
                    <td>Manager</td>
                    <td>{{ table.manager ? table.manager.name : '' }}</td>
                </tr>
                <tr>
                    <td>Daily Goal</td>
                    <td>{{ statsFormat(table.daily_goal) }}</td>
                </tr>
                <tr>
                    <td>Monthly Goal</td>
                    <td>{{ statsFormat(table.monthly_goal) }}</td>
                </tr>
                <tr>
                    <td>Daily Total</td>
                    <td>{{ statsFormat(table.dailyTotal) }}</td>
                </tr>
                <tr>
                    <td>Monthly Total</td>
                    <td>{{ statsFormat(table.monthlyTotal) }}</td>
                </tr>
                <tr>
                    <td>Left For Goal</td>
                    <td>{{ statsFormat(leftToGoal(table.monthlyTotal, table.monthly_goal)) }}</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script type="text/babel">
    import {numberFormat} from '../../../helpers/functions';
    export default {
        props: {
            table: {
                required: true
            }
        },

        data(){
            return {}
        },
        methods: {
            statsFormat(num) {
                if (this.table.type == 1) {
                    return num;
                } else {
                    return '$' + numberFormat(num);
                }
            },
            leftToGoal(total, goal){
                if ((goal - total) < 0) {
                    return "0";
                }

                return (goal - total);
            }
        }
    }
</script>
<style scoped>
    body {
        background-color: black;
    }
    .stats {
        margin-bottom: 290px;
    }
    .stats table tr {
        font-size:28px;
    }

</style>