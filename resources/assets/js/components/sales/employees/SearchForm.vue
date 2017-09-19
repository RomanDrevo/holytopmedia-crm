<template>
    <form class="form-inline" method="GET" @submit.prevent="$emit('search', query)">
        <div class="form-group">
            <label for="query">Search:</label>
            <input type="text" class="form-control" name="query" id="query" value="" v-model="query">
        </div>
        <div class="form-group">

            <select class="form-control" name="field">
                <option value="id">by ID</option>
                <option value="customerId">By Customer ID</option>
                <option value="transactionId">By Transaction ID</option>
            </select>
        </div>

        <div class="form-group">
            <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>
</template>

<script type="text/babel">
    export default {
        props: {
            employees: {
                required: true
            }
        },

        name: 'search-form',

        data () {
            return {
                query: '',
            };
        }

    }
</script>

<style scoped>
    form {
        float: right;
        margin-bottom:30px;
    }




</style>
