<template>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Settings</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default alerts-settings">
                    <div class="panel-heading">
                        <i class="fa fa-users"></i> Alerts Settings
                    </div>
                    <div class="panel-body">

                        <form @submit.prevent="saveSettings">
                            <h4>Big Depositor Not Contacted Alerts</h4>
                            <div class="form-group">
                                <label for="total_deposits">Sum of total deposits:</label>
                                <input type="text" class="form-control" v-model="settings.big_depositor_total"
                                       id="total_deposits">
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Not contacted since day: </label>
                                <datepicker v-model="settings.not_contacted_since"
                                            placeholder="Since Date"></datepicker>
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Last deposit date</label>
                                <datepicker v-model="settings.not_contacted_last_deposit"
                                            placeholder="Choose Date"></datepicker>
                            </div>
                            <hr class="sett-hr"/>
                            <h4>Matching Keywords Alerts</h4>
                            <div class="form-group">
                                <label for="alerts_keywords">Keywords:</label>
                                <input type="text" class="form-control" v-model="settings.alerts_keywords"
                                       id="alerts_keywords">
                                <p style="padding:10px 0;"><b>Separate words with | sign.(Example: customer |
                                    verification | financial)</b></p>
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Look in comments since:</label>
                                <datepicker v-model="settings.keywords_since" placeholder="Choose Date"></datepicker>
                            </div>
                            <hr class="sett-hr"/>
                            <h4>Declined deposits alerts</h4>
                            <div class="form-group">
                                <label class="settings-label">Since date:</label>
                                <datepicker v-model="settings.declined_since" placeholder="Choose Date"></datepicker>
                            </div>
                            <hr class="sett-hr"/>
                            <h4>Not Verified Deposit alerts</h4>
                            <div class="form-group">
                                <label class="settings-label">Last Deposit date:</label>
                                <datepicker v-model="settings.not_verified_last_deposit" placeholder="Choose Date"></datepicker>
                            </div>
                            <div class="form-group">
                                <label class="settings-label">Sum of total deposits:</label>
                                <input type="text" class="form-control" v-model="settings.not_verified_total">
                            </div>

                            <div class="form-group">
                                <button class="btn btn-success pull-right" type="submit"><i class="fa fa-floppy-o"></i>
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/babel">
    import Datepicker from 'vuejs-datepicker';
    import moment from 'moment';

    export default {

        name: 'complience-settings',
        props: {
            settings: {
                required: true
            }
        },
        data() {
            return {
            }
        },
        methods: {
            saveSettings() {
                console.log(this.settings);
                Vue.http.post('/compliance/settings/save-settings', {
                    big_depositor_total: this.settings.big_depositor_total,
                    not_contacted_since: moment(this.settings.not_contacted_since).format('YYYY-MM-DD HH:mm:ss'),
                    not_contacted_last_deposit: moment(this.settings.not_contacted_last_deposit).format('YYYY-MM-DD HH:mm:ss'),
                    alerts_keywords: this.settings.alerts_keywords,
                    keywords_since: moment(this.settings.keywords_since).format('YYYY-MM-DD HH:mm:ss'),
                    declined_since: moment(this.settings.declined_since).format('YYYY-MM-DD HH:mm:ss'),
                    not_verified_last_deposit: moment(this.settings.not_verified_last_deposit).format('YYYY-MM-DD HH:mm:ss'),
                    not_verified_total: this.settings.not_verified_total

                }).then((response) => {
                    swal('Updated!', 'Settings have been updated.', 'success');

                }).catch(errors => {
                    let error = Object.values(errors.body)[0][0];
                    swal("Oops...", error, "error");
                });
            }
        },

        components: {
            Datepicker
        }
    }
</script>
<style scoped>
    .alerts-settings {
        margin-top: 40px;
        margin-bottom: 40px;
    }
    .alerts-settings .panel-body {
        margin-bottom: 60px;
    }
    .alerts-setting h4 {
        margin-bottom: 20px;
    }
    .alerts-settings input[type=text] {
        width: 65%;
    }
    hr.sett-hr {
        border-top: 1px solid #ccc;
    }
    .settings-label {
        display: block;
    }

</style>