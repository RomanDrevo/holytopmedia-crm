<template>
    <div class="panel panel-primary">
        <div class="panel-heading">Alerts</div>
        <div class="panel-body">
            <span class="label label-primary" v-for="alert in customerAlerts">{{ alert.content }}  </span>
        </div>
    </div>
</template>

<script type="text/babel">

    export default{
        props:{
            customerAlerts:{
                required: true
            }
        }
    }
</script>

<style scoped>
 span.label.label-primary {
        display: block;
        margin: 5px;
        height: 25px;
        font-size: 15px;
        line-height: 18px;
    }
</style>