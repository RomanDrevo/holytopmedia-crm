<template>
    <div class="container-fluid">
        <div class="row">
            <div class="x_panel">
                <div class="x_title">
                    <h2 style="font-size: 26px;">{{ customer.FirstName }} {{ customer.LastName }} ID: <a href="">{{
                        customer.customer_crm_id }}</a></h2>
                    <div class="clearfix"></div>
                </div>
                <form action="/compliance/customers/' + this.customer.customer_crm_id + '/update" method="POST" enctype="multipart/form-data">


                    <input type="hidden" name="_token" :value="csrf">

                    <input type="hidden" name="customer_id" :value="customer.id" id="customer_id"/>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-md-6 table-responsive customer_data">

                                <table-data @verificationStatusChanged="updateVerificationStatus"
                                            @secondaryEmailChanged="updateSecondaryEmail"
                                            @addSecondaryPhone="updateSecondaryPhone"
                                            :totalDeposits="totalDeposits" :totalCCdeposits="totalCCdeposits"
                                            :customer="customer" :countries="countries"
                                            :customerVerification="customerVerification"
                                            :secondaryEmail="secondaryEmail"
                                            :secondaryPhone="secondaryPhone"
                                ></table-data>

                                <comments :customerComments="customerComments" @textChanged="updateComment"></comments>

                            </div>

                            <div class="col-md-6">
                                <alerts v-if="customerAlerts.length > 0" :customerAlerts="customerAlerts"></alerts>

                                <deposits @onStatusChange="updateDepositStatus" :customer="customer"
                                          :totalDeposits="totalDeposits" :totalCCdeposits="totalCCdeposits"
                                          :allCustomerDeposits="allCustomerDeposits"></deposits>

                                <bonuses :customerBonuses="customerBonuses"></bonuses>

                                <withdrawals :withdrawals="withdrawals"></withdrawals>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="action_buttons pull-right">
                                <a href="/compliance/customers" class="btn btn-default">Back to customers</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import TableData from '../partials/TableData.vue';
    import Alerts from '../partials/Alerts.vue';
    import Bonuses from '../partials/Bonuses.vue';
    import Comments from '../partials/Comments.vue';
    import Deposits from '../partials/Deposits.vue';
    import Withdrawals from '../partials/Withdrawals.vue';

    export default{
        name: "customer-info",
        props: {
            customer: {
                required: true
            }
        },
        data(){
            return {
                data: [],
                countries: {},
                totalCCdeposits: "",
                totalDeposits: "",
                allCustomerDeposits: {},
                customerComments: {},
                customerAlerts: {},
                customerBonuses: {},
                withdrawals: {},
                customerVerification: "",
                secondaryEmail: "",
                secondaryPhone: "",
                updates:{
                    content: "",
                    verification: "",
                    secondary_email: "",
                    secondary_phone: ""
                },
                csrf: Laravel.csrfToken
            }
        },
        methods: {
            getCustomerInfo(url) {
                this.$http.get(url).then((response) => {
                    this.data = response.body.customerInfo;
                    this.countries = this.data.countries;
                    this.totalCCdeposits = this.data.totalCCdeposits;
                    this.totalDeposits = this.data.totalDeposits;
                    this.allCustomerDeposits = this.data.customer.deposits;
                    this.customerComments = this.data.customer.comments;
                    this.customerAlerts = this.data.customer.alerts;
                    this.customerBonuses = this.data.bonuses;
                    this.withdrawals = this.data.customer.withdrawals;
                    //this.secondaryEmail = this.data.customer.secondary_email;
                    //this.secondaryPhone= this.data.customer.secondary_phone;
                    //console.log(this.secondaryEmail);
                });
            },
            updateComment(value){
                this.updates.content = value;
            },
            updateVerificationStatus(data){
                this.updates.verification = data;
            },
            updateSecondaryEmail(data){
                this.updates.secondary_email = data;
            },
            updateSecondaryPhone(value){
                this.updates.secondary_phone = value;
            },
            updateDepositStatus(deposit_id){
                this.$http.post('/compliance/customer/toggle-deposit', {
                    deposit_id: deposit_id
                }).then(response => {
                    _.map(this.allCustomerDeposits, deposit => {
                        if (deposit.id == deposit_id) {
                            deposit.approved = response.body.deposit.approved;
                        }
                    });
                }).catch(errors => {
                    let error = Object.values(errors.body)[0];
                    swal("Oops...", error, "error");
                });

            },
            updateCustomer(){
                this.$http.post('/compliance/customers/' + this.customer.customer_crm_id + '/update', this.updates).then(response => {
                    console.log(response.status)
                    location.reload();
                }).catch(errors => {
                    console.log(errors);
                });

            }
        },
        mounted(){
            let tempUrl = "/compliance/customers/" + this.customer.customer_crm_id + "/get-customer-info";
            this.getCustomerInfo(tempUrl);
            this.customerVerification = this.customer.verification;
            this.updates.verification = this.customer.verification;
            this.secondaryEmail = this.customer.secondary_email;
            this.secondaryPhone = this.customer.secondary_phone;
            console.log(this.customer)
        },
        components: {
            TableData,
            Bonuses,
            Comments,
            Deposits,
            Withdrawals,
            Alerts
        }
    }
</script>
