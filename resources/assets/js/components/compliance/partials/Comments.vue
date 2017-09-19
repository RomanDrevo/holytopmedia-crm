<template>
    <div class="panel panel-primary">
        <div class="panel-heading">Comments</div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-center">User</th>
                    <th class="text-center">Created at</th>
                    <th class="text-center">Comment</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="comment in customerComments">
                    <td>{{ comment.user.name }}</td>
                    <td>{{ comment.created_at }}</td>
                    <td>{{ comment.content }}</td>
                </tr>
                <tr v-if="customerComments.length == 0">
                    <td colspan="3">No comments...</td>
                </tr>
                </tbody>
            </table>
            <textarea name="content" id="content" class="form-control" @change="$emit('textChanged', $event.target.value)" placeholder="Write your comment here..."></textarea>
        </div>
    </div>
</template>
<style>

</style>
<script type="text/babel">

    export default{
        props:{
            customerComments:{
                required: true
            }
        },
        data(){
            return{
                comment: ""
            }
        },
        // watch: {
        //     comment(value){
        //         this.$emit("textChanged", value);
        //     }
        // },
        components:{

        }
    }
</script>
