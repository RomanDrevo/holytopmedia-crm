<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Trackable lists</div>

                    <div class="panel-body">
                        <ul class="list-group">
                            <li v-for="list in lists" class="list-group-item">
                                <a :href="'/marketing/lists/' + list.id">{{ list.name }}</a>
                                <span class="pull-right">ID: {{ list.id }}</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <a href="/marketing/lists/create" class="btn btn-primary">Create a new list</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['lists']
    }
</script>
