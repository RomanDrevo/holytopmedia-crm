<template>
    <div class="container">
        <div class="row">
            <div class="x_panel">
                <div class="x_title">
                    <h2 class="alerts-heading">Alerts</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-5 pull-right">

                            <form @submit.prevent="getSearchResults" class="form-inline" id="filter-form">
                                <div class="form-group">
                                    <label for="search">Filter:</label>
                                    <input type="text" class="form-control" name="search" id="search" v-model="query" placeholder="Customer ID">
                                </div>
                                <div class="form-group">
                                    <select class="form-control" v-model="type">
                                        <option value="">ALL</option>
                                        <option value="1">NOT VERIFIED</option>
                                        <option value="2">KEYWORDS</option>
                                        <option value="3">DECLINED</option>
                                        <option value="4">NO CONTACT</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <ul class="alerts-group">
                        <li v-for="(alert, index) in alerts" class="alert-group-item" :class="'alert type' + alert.type">
                            <a :href="'/compliance/customers/' + alert.customer_crm_id">
                                <h5><strong> {{ alert.subject }} </strong></h5>
                                <h5 v-html="alert.content"></h5>
                            </a>
                            <button class="btn btn-danger delete-button" @click.prevent="deleteAlert(index, alert.id)"><i class="fa fa-trash-o"></i></button>
                        </li>
                    </ul>
                </div>
                <pagination :current_page="alertsdata.current_page"
                            :prev_page_url="alertsdata.prev_page_url"
                            :last_page="alertsdata.last_page"
                            :next_page_url="alertsdata.next_page_url"
                            :url_path="url"
                            :total="alertsdata.total"
                            name="alerts"
                            :per_page="alertsdata.per_page"
                            :updateRecords="updateRecords">
                </pagination>

            </div>
        </div>
    </div>

</template>

<script type="text/babel">
    import Pagination from '../../common/Pagination.vue';

    export default{
        created(){
            this.getData('/compliance/alerts/by-type');
        },

        data() {
            return{
                alertsdata: {},
                alerts: [],
                type: '',
                query: ''
            }
        },

        computed: {
            url() {
                let tempUrl = "/compliance/alerts/by-type";
                return this.type ? tempUrl + "?type=" + this.type + "&" : tempUrl + "?";
            }
        },

        methods: {

            getAlertsByType(type) {
                 this.type = type;
                 this.getData('/compliance/alerts/by-type?type=' + type);

            },

            updateRecords(e){
                let httpPath = e.target.getAttribute("href");
                this.getData(httpPath);
                window.scrollTo(0, 10);
            },

            getSearchResults() {
                let tempUrl = '/compliance/alerts/by-type?search=' + this.query;
                console.log(tempUrl);
                if(this.type) {
                    tempUrl += "&type=" + this.type;
                }
                this.getData(tempUrl);


            },

            getData(url) {
                this.$http.get(url).then((response) => {
                    this.alertsdata = response.body;
                    this.alerts = this.alertsdata.data;
                });
            },

            deleteAlert(index, alertId){


                Vue.http.post("/compliance/alerts/" + alertId + "/delete",{alertId: alertId})
                    .then((response) => {
                        this.alerts.splice(index, 1);
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
        },
        components: {
            Pagination
        }

    }


</script>

<style scoped>
    ul {
        padding-left: 0;
    }

    li {
        list-style: none;
    }

    h5{
        color: black;
    }

    .type1 {

            background-color: #ED3034;

     }

    .type2 {
        color: black;
        background-color: #009855;

    }

    .type3 {
        color: black;
        background-color: #FFC310;

    }

    .type4 {
        color: black;
        background-color: #006DBE;

    }
    form {
        margin-bottom: 20px;
    }

    .alert-group-item{
        position: relative;
        border: none;
    }

    .delete-button{
            color: #131313;
           background-color: #ffffff;
           border-color: #ffa500;
           position: absolute;
           right: 0;
           bottom: 0;
           height: 100%;
    }
    .alerts-heading {
        margin-top: 40px;
    }


</style>