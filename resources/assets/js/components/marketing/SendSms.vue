<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-comments" aria-hidden="true"></i> Send SMS</div>

                    <div class="panel-body">
                        <loader v-if="loading"></loader>
                        <table id="customers-filter" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th colspan="2">Filter By:</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="filters">Register Date:</td>
                                <td>
                                    <datepicker v-model="sms.from" placeholder="Start Date"></datepicker>
                                </td>
                            </tr>
                            <tr>
                                <td class="filters">Rules</td>
                                <td>
                                    <select v-model="sms.type" class="form-control" @change="getCount">
                                        <option :value="null">Choose a rule...</option>
                                        <option value="rst_single_deposits">RST single deposits</option>
                                        <option value="ftd_no_deposits">FTD no deposits</option>
                                    </select>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <hr/>
                        <div v-if="isSubmit">{{ countMessage }}
                            <form class="form-inline" method="GET" action="/marketing/download/customers" style="display:inline">
                                <input type="hidden" :value="sms.type" name="type" />
                                <input type="hidden" :value="sms.from" name="from" />
                                <button class="btn btn-primary btn-xs"><i class="fa fa-file-excel-o" aria-hidden="true" type="submit"></i> Download</button>
                            </form>
                        </div>
                        <form class="send-sms">
                            <!--<div class="form-group">-->
                                <!--<label for="subject">Subject:</label>-->
                                <!--<input type="text" class="form-control" id="subject" v-model="sms.subject">-->
                            <!--</div>-->
                            <div class="form-group">
                                <label for="sms-body">Body:</label>
                                <textarea id="sms-body" v-model="sms.smsbody" class="form-control"></textarea>
                                <p>*** If the message contains a contact number, do not enter it directly. Use {phone} placeholder instead.<br/>
                                *** Example: Call your account manager now to get $2,500 bonus {phone} </p>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success" @click.prevent="sendSms" v-bind:disabled="disableButton">Send</button>
                                <div class="pull-right" id="test-number">
                                    <input type="text" class="form-control" v-model="sms.testNumber" placeholder="Enter Test Number">
                                    <button class="btn btn-xs btn-primary" @click.prevent="submitTest">Test</button>
                                    <p v-if="notTested" class="testing-message">* Please, make a test before sending to customers</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import moment from 'moment';
    import Datepicker from 'vuejs-datepicker'
    import Multiselect from 'vue-multiselect';
    import Loader from '../common/Loader.vue';
    import {numberFormat} from '../../helpers/functions';

    export default {
        name: 'send-sms',
        props: {},
        data(){
            return {
                loading: false,
                countMessage: '',
                isSubmit: false,
                disableButton: true,
                notTested: true,
                sms: {
                    type: null,
                    subject: '',
                    smsbody: '',
                    from: '',
                    testNumber: ''
                }

            }
        },
        created() {
            this.sms.from = moment().startOf('month').format();

        },
        computed: {
            query() {
                return 'type=' + this.sms.type + '&from=' + this.sms.from;
            }
        },
        methods: {
            getCount() {
                this.loading = true;
                this.isSubmit = false;
                this.countMessage = '';
                Vue.http.post('/marketing/sms/count', this.$data.sms)
                        .then(response => {
                            this.loading = false;
                            this.isSubmit = true;
                            this.countMessage = "Target customers count is: " + response.body;
                        })
                        .catch(error => {
                            this.loading = false;
                            alert(error.body);
                        });
            },
            messageSent() {
                this.sms.type = null;
                this.sms.subject = '';
                this.sms.smsbody = '';
                this.sms.testNumber = '';
            },
            submitTest() {
                this.loading = true;
                Vue.http.post('/marketing/sms/test', this.$data.sms)
                        .then(response => {
                            this.loading = false;
                            this.disableButton = false;
                            this.notTested = false;
                            this.sms.testNumber = '';
                        })
                        .catch(errors => {
                            this.loading = false;
                            let errorMessage = '';
                            _.map(errors.body, error => {
                                errorMessage += error[0] + '<br/>';
                            });
                            swal("Oops...", errorMessage, "error");
                        });
            },

            sendSms() {
                this.loading = true;
                Vue.http.post('/marketing/sms/to', this.$data.sms)
                        .then(response => {
                            this.loading = false;
                            swal('Sended!', 'Your message has been sended.', 'success');
                            this.messageSent();
                        })
                        .catch(errors => {
                            this.loading = false;
                            if(typeof(error == 'object')) {
                                let errorMessage = '';
                                _.map(errors.body, error => {
                                    errorMessage += error[0] + '<br/>';
                                });
                                swal("Oops...", errorMessage, "error");
                            } else {
                                swal("Oops...", error.body, "error");
                            }

                        });
            }
        },
        components: {
            Datepicker,
            Multiselect,
            Loader
        }
    }
</script>
<style scoped>
    #customers-filter {
        margin-top: 60px;
    }
    td.filters {
        font-weight: bold;
    }
    .send-sms {
        margin-top: 60px;
    }
    #test-number {
        width: 40%;
    }
    #test-number input{
        width: 84%;
        display: inline-block;
    }
     #test-number button {
        padding: 5px;
     }
    .testing-message {
        color: red;
        font-size: 12px;
    }
    .phone-note {
        font-size: 12px;
    }





</style>