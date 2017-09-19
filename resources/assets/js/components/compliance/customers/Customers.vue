<template>
    <div class="container-fluid">
        <div class="row">
            <div class="x_panel">
                <div class="x_title">
                    <h2>All Customers</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-3 pull-left">
                            <!--@if(\Request::has('query') || \Request::has('employee_id'))-->
                            <!--<a href="/compliance/customers" class="btn btn-warning btn-xs">Clean search results</a>-->
                            <!--@endif-->
                        </div>
                        <div class="col-md-4 pull-right" style="margin-bottom: 18px;">
                            <form @submit.prevent="getSearchResults" class="form-inline" id="filter-form">
                                <div class="form-group">
                                    <label for="search">Filter:</label>
                                    <input type="text" class="form-control" name="search" id="search" v-model="query">
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="field" v-model="searchBy">
                                        <option value="customer_crm_id">By ID</option>
                                        <option value="name">By Name</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                                <button v-if="isSearched" class="btn btn-default btn-sm" @click.prevent="reload" >Clear Filters</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">Customer ID</th>
                                <th class="column-title">Name </th>
                                <th class="column-title">Email </th>
                                <th class="column-title">Country </th>
                                <th class="column-title">Phone </th>
                                <th class="column-title">Actions </th>
                            </tr>
                            </thead>

                            <tbody>
                                <customer-row v-for="customer in customers" :cus="customer" :countries="countries"></customer-row>
                            </tbody>
                        </table>

                        <pagination :current_page="customersData.current_page"
                                    :prev_page_url="customersData.prev_page_url"
                                    :last_page="customersData.last_page"
                                    :next_page_url="customers.next_page_url"
                                    :url_path="url"
                                    :total="customersData.total"
                                    name="customers"
                                    :per_page="customers.per_page"
                                    :updateRecords="updateRecords">
                        </pagination>
                        <div class="row text-center" style="margin: 0px auto;">
                            <!--{!! $customers->appends(\Request::except('page'))->render() !!}-->
                            <!--<p class="show_pagination_count">{{ 'Showing customers '.$customers->firstItem().' to '.$customers->lastItem().' out of '.$customers->total() }}</p>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    body{

    }
</style>
<script type="text/babel">
    import CustomerRow from './CustomerRow';
    import Pagination from '../../common/Pagination.vue';
    export default{
        props:{
            countries: {
                requierd: true
            }
        },
        data(){
            return{
                searchBy: "customer_crm_id",
                selectedEmployeeName: "",
                selectedEmployeeID: "",
                customersData: {},
                customers: [],
                query: '',
                isSearched: false
            }
        },
        computed: {
            url() {
                let tempUrl = "/compliance/customers/get-customers";
                return this.type ? tempUrl + "?type=" + this.type + "&" : tempUrl + "?";
            }
        },
        methods: {

            updateRecords(e){
                let httpPath = e.target.getAttribute("href");
                this.getData(httpPath);
            },

            getData(url) {
                this.$http.get(url).then((response) => {
                    this.customersData = response.body;
                    //console.log(this.employeesData);
                    this.customers = this.customersData.data;
                });
            },
            getSearchResults() {
                let tempUrl = '/compliance/customers/get-customers?search=' + this.query + '&by=' + this.searchBy;
                console.log(tempUrl);
                this.getData(tempUrl);
                this.isSearched = true;
            },
            reload(){
                location.reload();
            }
        },

        mounted(){
            this.getData('/compliance/customers/get-customers');
        },
        components:{
            Pagination,
            CustomerRow
        }
    }
</script>
