<template>
    <div class="stats">
        <div class="well">
            <table>
                <tr>
                    <td>Today FTD</td>
                    <td>{{ stats.todayFtd }}</td>
                </tr>
                <tr>
                    <td>Monthly FTD</td>
                    <td>{{ stats.monthlyFtd }}</td>
                </tr>
                <tr>
                    <td>Today deposits</td>
                    <td>{{ stats.todayDeposits }}</td>
                </tr>
                <tr>
                    <td>Total deposits</td>
                    <td>{{ stats.totalDeposits }}</td>
                </tr>
                <tr>
                    <td>This month goal</td>
                    <td>{{ stats.monthGoal }}</td>
                </tr>
            </table>
        </div>
    </div>
</template>
<style>

</style>
<script>
    export default{

        props: {
            channel: {
                required: true
            },
            broker: {
                required: true
            }
        },

        created(){
            this.registerToPusher();
        },

        mounted(){
            this.updateStats();
        },

        data(){
            return{
                stats: {
                    todayFtd: 0,
                    monthlyFtd: 0,
                    todayDeposits: 0,
                    totalDeposits: 0,
                    monthGoal: ""
                }
            }
        },


        methods: {
             updateStats(){
                this.$http.post('/screens/get-stats', { broker: this.broker })
                .then(response => {

                    this.stats = response.data;
                });
             },

             registerToPusher(){

                this.channel.bind('stats', (data) => {
                    this.stats = data;
                    console.log(this.stats);

                });
            }
        }
    }
</script>
