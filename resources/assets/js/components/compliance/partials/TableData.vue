<template>
    <div class="panel panel-primary">
        <div class="panel-heading">{{ customer.FirstName }} {{ customer.LastName }} information</div>
        <div class="panel-body">
            <table class="table table-striped jambo_table bulk_action">
                <tbody>
                    <tr>
                        <td>Name</td>
                        <td>{{ customer.FirstName }} {{ customer.LastName }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ customer.email }}</td>
                        <input type="hidden" name="primary_email" id="primary_email" :value="customer.email" />
                    </tr>
                    <tr>
                        <td>Secondary Email</td>
                        <td>
                            <input type="email" name="secondary_email" id="secondary_email" :data-email="customer.secondary_email" @change="addSecondaryEmail" class="form-control" v-model="secEmail" />
                        </td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>{{ customer.Phone }}</td>
                    </tr>
                    <tr>
                        <td>Secondary Phone</td>
                        <td>
                            <input type="text" name="secondary_phone" id="secondary_phone" @change="$emit('addSecondaryPhone', $event.target.value)" class="form-control" v-model="secPhone" />
                        </td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td>{{ countries[customer.Country] }}</td>
                    </tr>
                    <tr>
                        <td>Verification Status</td>
                        <td>
                            <select name="verification" id="verification" class="form-control" @change="sendVerificationStatus" v-model="verifStatus">
                                <option>None</option>
                                <option>Partial</option>
                                <option>Full</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Campaign ID</td>
                        <td>{{ customer.campaignId }}</td>
                    </tr>
                    <tr>
                        <td>Currency</td>
                        <td>{{ customer.currency }}</td>
                    </tr>
                    <tr>
                        <td>Registration Status</td>
                        <td>{{ customer.regStatus }}</td>
                    </tr>
                    <tr>
                        <td>First Deposit Date</td>
                        <td>{{ customer.firstDepositDate ? customer.firstDepositDate : "No Deposits"}}</td>
                    </tr>
                    <tr>
                        <td>Last Deposit Date</td>
                        <td>{{ customer.lastDepositDate ? customer.lastDepositDate : "No Deposits" }}</td>
                    </tr>
                    <tr>
                        <td>Last Withdrawal Date</td>
                        <td>
                            {{ customer.lastWithdrawalDate ? customer.lastWithdrawalDate : "No Withdrawals" }}
                        </td>
                    </tr>
                    <tr>
                        <td>Last Login Date</td>
                        <td>{{ customer.lastLoginDate ? customer.lastLoginDate : "No data"}}</td>
                    </tr>
                    <tr>
                        <td>Total Deposits</td>
                        <td>{{ customer.currency }} {{ totalDeposits }}</td>
                    </tr>
                    <tr>
                        <td>Total CC Deposits</td>
                        <td>{{ customer.currency }} {{ totalCCdeposits }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script type="text/babel">

    export default{
        props: {
            customer: {
                required: true
            },

            countries: {
                required: true
            },
            customerVerification: {
                required: true
            },
            totalDeposits:{
                required:true
            },
            totalCCdeposits:{
                required: true
            },
            secondaryEmail:{
                required: true
            },
            secondaryPhone:{
                required: true
            }
        },
        data(){
          return {
              verifStatus: this.customer.verification,
              secEmail: this.customer.secondary_email,
              secPhone: this.customer.secondary_phone
          }
        },
        methods: {
            sendVerificationStatus(){
                this.$emit("verificationStatusChanged", this.verifStatus)
            },
            addSecondaryEmail(){
                this.$emit("secondaryEmailChanged", this.secEmail)
            }
        }
    }
</script>
