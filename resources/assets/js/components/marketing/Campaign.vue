<template>
    <div>
        <form class="form-inline" id="filter-form">
            <multiselect

            v-model="search.campaign"
            :options="campaigns"
            placeholder="No campaign selected"
            label="name"
            track-by="name"
            :multiple="false">
            </multiselect>
            <div class="datepick">
                <datepicker v-model="search.startDate" placeholder="Start Date"></datepicker>
                <datepicker v-model="search.endDate" placeholder="End Date"></datepicker>
            </div>
            <button @click.prevent="getCampaignInfo" class="btn btn-success btn-sm">Get Campaign Statistics</button>
        </form>

        <campaign-info v-if="showCampaignInfo" :campaignInfo="campaignInfo"></campaign-info>
    </div>
</template>

<script type="text/babel">
    import Multiselect from 'vue-multiselect';
    import CampaignInfo from './CampaignInfo.vue';
    import Datepicker from 'vuejs-datepicker';
    export default{
        props:{
            campaigns:{
                required: true
            }
        },
        data(){
            return {
                search:{
                    startDate: null,
                    endDate: null
                },
                isSearched: false,
                campaignID: "",
                campaignInfo: {},
                showCampaignInfo: false
            }
        },
        methods:{
            reload(){
                this.getData('/sales/employees/get-employees');
                this.isSearched = false;
                this.term = '';
            },
            getCampaignInfo(){
                this.$http.post('/marketing/get-campaign-info', {"campaignID": this.search.campaign.id, "start_date":this.search.startDate, "end_date":this.search.endDate })
                        .then(response => {
                            if(response.data.data.length != 0){
                                this.campaignInfo = response.data.data;
                                this.showCampaignInfo = true;
                            }else{
                                swal(response.body.message);
                            }
                        })
                        .catch(errors => {
                            this.showCampaignInfo = false;
                            let error = Object.values(errors.body)[0];
                            swal("Error", error, "error");
                        });
            }
        },
        components: {
            Multiselect, CampaignInfo, Datepicker
        }
    }
</script>


<style scoped>
    .datepicker {
        margin-top: 15px;
        position: relative;
    }
    .btn-group-sm>.btn, .btn-sm {
        margin-top: 9px;
        padding: 5px 10px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }
</style>

