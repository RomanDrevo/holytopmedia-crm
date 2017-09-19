<template>
    <!--<h1>Daser</h1>-->

    <div class="card">
        <div class="card-block">
            <h4 class="card-title">Campaign name: {{ campaignInfo.name }}</h4>
            <h4 class="card-title">Campaign ID: {{ campaignInfo.id }}</h4>
            <h4 class="card-title">Registrated customers: {{ campaignInfo.total_registrations }}</h4>
            <h4 class="card-title">Depositors: {{ campaignInfo.depositors }}</h4>
            <h4 class="card-title">Player value: {{ campaignInfo.player_value }}</h4>
            <h4 class="card-title">Total deposits: {{ campaignInfo.total_deposits }}</h4>

            <!--<a href="#" class="card-link">Another link</a>-->
        </div>
    </div>
</template>

<script type="text/babel">

    export default{
        name: "campaign-info",
        props: {
            campaignInfo:{
                required: true
            }
        }
    }
</script>

<style scoped>
    .card{

    }
</style>