<template>
    <div class="container">

        <div class="panel uploader">
            <div class="panel-heading">
                <h3>Add Customers</h3>
                <p>Upload a .csv file of customers</p>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2">Please make sure the customer.csv file is in the following format:</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>first_name</td>
                        <td>The customer's first name</td>
                    </tr>
                    <tr>
                        <td>last_name</td>
                        <td>The customer's last name</td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td>The customer's email address</td>
                    </tr>
                    <tr>
                        <td>phone</td>
                        <td>The phone number including the country code without the plus (+) sign. (example:
                            447400123456 )
                        </td>
                    </tr>
                    <tr>
                        <td>country_id</td>
                        <td>The ID of the customer's country (you can download the list of ID below this table)</td>
                    </tr>
                    <tr>
                        <td>campaign_id</td>
                        <td>Must be a valid campaign id in the CRM</td>
                    </tr>
                    <tr>
                        <td>subcampaign_name</td>
                        <td>Any name could work here.</td>
                    </tr>
                    </tbody>
                </table>

                <p>
                    <a href="/downloads/customers_example.csv" class="btn btn-xs btn-primary">customer.csv example
                        file</a>
                    <a href="/downloads/countries.csv" class="btn btn-xs btn-primary">List of country ID's</a>
                </p>
                <form enctype="multipart/form-data" @submit.prevent="uploadCustomers"
                      style="margin-top: 60px;">

                    <div class="form-group">
                        <label for="customers_file">Customers .CSV file</label>
                        <input type="file" name="customers_file" id="customers_file" class="form-control">
                    </div>

                    <div class="pull-right">
                        <input type="submit" name="submit" class="btn btn-success" value="Upload Document">
                    </div>

                </form>
                <div v-if="proccessing">
                    <div class="text-center">
                        <h3>{{ loadingMessage }}</h3>
                        <div class="progress">
                            <div class="progress-bar progress-bar-info" role="progressbar" :aria-valuenow="progress"
                                 aria-valuemin="0" :aria-valuemax="100" :style="'width: ' + progress + '%'">
                                {{ customersCount }} customers
                            </div>
                        </div>
                    </div>
                    <br/>

                    <div class="alert alert-danger upload-errors" v-if="errors" style="max-height: 200px;overflow-y: auto;">
                        <strong>Warning!</strong> <br/>
                        <div v-html="errors"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script type="text/babel">

    export default {
        name: 'create-customers',
        props: {
            user_id: {
                required: true
            },
            app_key: {
                required: true
            }

        },
        data() {
            return {
                loadingMessage: "",
                proccessing: false,
                customersCount: 0,
                customersCreated: 1,
                errors: "",
                completed: 0,
                pusher: "",
                channel: ""
            }
        }
        ,
        created(){
            this.pusher = new Pusher(this.app_key, {encrypted: true});
            this.channel = this.pusher.subscribe('add_updates_channel_' + this.user_id);
            this.registerToPusher();
        },
        computed: {
            progress(){
                return this.customersCount == 0 ? 0 : parseInt((this.customersCreated / this.customersCount) * 100);
            }
        },
        methods: {
            uploadCustomers(e){
                var files = e.target.customers_file.files;

                var formData = new FormData();
                formData.append('customers_file', files[0]);

                this.$http.post('/upload/add-customers/proccess-file', formData).then((response) => {
                    // console.log(response);
                });
            },
            registerToPusher(){
                this.channel.bind('update_recieved',  (data) => {
                   console.log(data);
                    switch (data.action) {
                        case "file_recieved":
                            this.proccessing = true;

                            this.loadingMessage = "Please Wait, Proccessing " + data.payload.customers_count + " customers...";
                            this.customersCount = data.payload.customers_count;
                            break;

                        case "customers_uploaded":
                            this.customersCreated += 1;
                            if (data.payload.error != "") {
                                this.errors += "<br/>" + data.payload.error + " on line " + data.payload.line;
                            } else {
                                this.completed++;
                            }
                            break;

                        case "upload_complete":
                            if(this.completed > 0) {
                                this.loadingMessage = "Done! (" + this.completed + " customers created)";
                            } else {
                                this.loadingMessage = "Alert! (" + this.completed + " customers created)";
                            }

                            break;
                    }
                });
            },
        }
    }
</script>
<style scoped>

    .uploader {
        margin-top: 100px;
    }
    .upload-errors {
        position: static !important;
    }

</style>