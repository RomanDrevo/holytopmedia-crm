<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Email list: {{ listdata.name }} (Broker: {{ listdata.broker.name }})</div>

                    <div class="panel-body">
                        <h3>All users clicks</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Assigned Employee</th>
                                    <th>Clicked</th>
                                    <th>Last active</th>
                                    <th>Link to platform</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="customer in customers">
                                    <td>{{ customer.customer_id }}</td>
                                    <td>{{ customer.name }}</td>
                                    <td>{{ customer.employee_name }}</td>
                                    <td>{{ customer.clicked ? "YES" : "NO" }}</td>
                                    <td>{{ customer.created_at }}</td>
                                    <td><a target="_blank" :customer-id="customer.customer_id" :href="'https://spotcrm.' + listdata.broker.url_name + '.com/crm/customers/page/' + customer.customer_id" @click="customerClicked">{{ customer.name }}</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['listdata', 'listid'],

        created(){
            this.fetchUsers();
            this.registerToPusher();
        },

        data(){
            return {
                customers: [],
                pusher: ''
            }
        },

        methods: {
            fetchUsers(){
                this.$http.get('/marketing/lists/' + this.listid + '/customers').then((response) => {
                    this.customers = response.body;
                });
            },
            registerToPusher(){
                this.pusher = new Pusher('a55901f4fcbdd8c002f0', { encrypted: true });
                let channel = this.pusher.subscribe('list_' + this.listid);
                channel.bind('customer_clicked', function(data) {
                    console.log(data);
                    if(data.success){ 
                        if (Notification.permission !== "granted")
                            Notification.requestPermission();
                        else {
                            var notification = new Notification(data.username + " just clicked the email", {
                              icon: 'http://www.job4work.com/files/pictures/profile_pic.png',
                              body: "This guy just clicked on your email and added to the list: " + data.listname,
                            });

                            notification.onclick = function () {
                                window.focus();      
                            };
                        }
                        this.fetchUsers(); 
                    }
                }.bind(this));
            },
            customerClicked(e){
                const customerId = e.target.getAttribute('customer-id');
                this.$http.get('/marketing/lists/' + this.listid + '/customers/' + customerId).then((response) => {
                    if(response.body == "ok"){ this.fetchUsers(); }
                });
            }
        }
    }
</script>
