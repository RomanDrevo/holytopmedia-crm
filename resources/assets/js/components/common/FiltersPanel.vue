<template>
    <form>

        <table class="table table-striped table-bordered" id="filter-deposits">
            <thead>
            <tr>
                <th colspan="2">Filter By:</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="filter-label">{{ name }} ID</td>
                <td><input type="text" v-model="search.id" class="form-control ids"
                           :placeholder="'Enter ' + name + ' ID'"></td>
            </tr>
            <tr>
                <td class="filter-label">Transaction  ID</td>
                <td><input type="text" v-model="search.transaction_id" class="form-control ids"
                           placeholder="Enter Transaction ID"></td>
            </tr>

            <tr>
                <td class="filter-label">Customer ID</td>
                <td><input type="text" v-model="search.customer_id" class="form-control ids"
                           placeholder="Enter Customer ID"></td>
            </tr>
            <tr>
                <td class="filter-label">Employee</td>
                <td>
                    <multiselect v-model="search.employee" :options="employees"
                                 placeholder="No worker selected" label="name" track-by="name" :multiple="false">
                    </multiselect>
                </td>
            </tr>
            <tr>
                <td class="filter-label">Table</td>
                <td>
                    <multiselect v-model="search.table" :options="tables"
                                 placeholder="No table selected" label="name" track-by="name" :multiple="false">
                    </multiselect>
                </td>
            </tr>
            <!--<tr>-->
                <!--<td class="filter-label">Approved</td>-->
                <!--<td>-->
                    <!--<select v-model="search.approved" class="form-control order-select">-->
                        <!--<option :value="null">Select option</option>-->
                        <!--<option value="1">YES</option>-->
                        <!--<option value="0">NO</option>-->
                    <!--</select>-->
                <!--</td>-->
            <!--</tr>-->
            <tr>
                <td class="filter-label">Status</td>
                <td>
                    <select v-model="search.status" class="form-control order-select">
                        <option value="all">All</option>
                        <option value="approved">Approved</option>
                        <option value="canceled">Canceled</option>
                        <option value="pending">Pending</option>
                        <option value="deleted">Deleted</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="filter-label">Start & End Dates</td>
                <td>
                    <datepicker v-model="search.startDate" placeholder="Start Date"></datepicker>
                    <datepicker v-model="search.endDate" placeholder="End Date"></datepicker>
                </td>
            </tr>
            <tr>
                <td class="filter-label">Amount</td>
                <td>
                    <div class="form-group amount-input">
                        <label for="min-amount">Min amount</label><br/>
                        <input type="text" v-model="search.amountMin" class="form-control"
                               id="min-amount">
                    </div>
                    <div class="form-group amount-input">
                        <label for="max-amount">Max amount</label><br/>
                        <input type="text" v-model="search.amountMax" class="form-control"
                               id="max-amount">
                    </div>

                    <div class="form-group amount-input" v-if="(search.amountMin || search.amountMax)">
                        <label for="currencies">Currencies</label>
                        <select id="currencies" v-model="search.currency" class="form-control">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="filter-label">Order By</td>
                <td>
                    <select v-model="search.orderBy" class="form-control order-select">
                        <option value="id">{{ name }} ID</option>
                        <option value="customerId">Customer ID</option>
                        <option value="amount">Amount</option>
                        <option value="confirmTime">Date</option>
                    </select>

                    <select v-model="search.order" class="form-control order-select">
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
            <button @click.prevent="searchResults" class="btn btn-success btn-sm"><i class="fa fa-search"></i> Search
            </button>
            <button v-if="isSearched" @click.prevent="downloadResults" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i>
                Download Result
            </button>
            <button v-if="isSearched" class="btn btn-default btn-sm" @click.prevent="clearResults" >Clear Filters</button>
        <slot name="additional-buttons"></slot>
    </form>
</template>

<script type="text/babel">
    import Multiselect from 'vue-multiselect';
    import Datepicker from 'vuejs-datepicker'
    import moment from 'moment';

    export default {
        name: 'filters-panel',
        props: {
            name,
            defaultStatus,
            employees: {
                required: true
            },
            tables: {
                required: true
            }
        },
        data() {
            return {
                search: {
                    id: '',
                    transaction_id: '',
                    employee: {},
                    startDate: null,
                    endDate: null,
                    amountMin: null,
                    amountMax: null,
                    currency: 'GBP',
                    customer_id: '',
                    table: {},
                    approved : null,
                    status: this.defaultStatus ? this.defaultStatus : "all",
                    verification: null,
                    orderBy: 'id',
                    order: 'desc'

                },
                isAmountEntered: false,
                isSearched: false
            }
        },

        methods: {

            clearResults() {
                this.search.id = '';
                this.search.transaction_id = '';
                this.search.employee = {};
                this.search.startDate = null;
                this.search.endDate = null;
                this.search.amountMin = null;
                this.search.amountMax = null;
                this.search.currency = 'GBP';
                this.search.customer_id = '';
                this.search.table = {};
                this.search.approved = null;
                this.search.status = this.defaultStatus ? this.defaultStatus : 'all'
                this.search.verification = null;
                this.search.orderBy = 'id';
                this.search.order = 'desc';

                this.isSearched = false;

                this.$emit('fetch')
            },
            searchResults() {
                var data = this.search,
                        start = data.startDate,
                        end = data.endDate;
                if (data.startDate) {
                    data.startDate = moment(data.startDate).format('DD-MM-YYYY HH:mm:ss');
                }
                if (data.endDate) {
                    data.endDate = moment(data.endDate).format('DD-MM-YYYY HH:mm:ss');
                    data.endDate = data.endDate.replace('00:00:00', '23:59:00');
                }
                this.$emit('search', data)
                this.search.startDate = start;
                this.search.endDate = end;
                this.isSearched = true;

            },

            downloadResults() {
                let data = this.search,
                        start = data.startDate,
                        end = data.endDate;
                if (data.startDate) {
                    data.startDate = moment(data.startDate).format('DD-MM-YYYY HH:mm:ss');
                }
                if (data.endDate) {
                    data.endDate = moment(data.endDate).format('DD-MM-YYYY HH:mm:ss');
                    data.endDate = data.endDate.replace('00:00:00', '23:59:00');
                }
                this.$emit('download', data);

                this.search.startDate = start;
                this.search.endDate = end;
            }
        },

        components: {
            Multiselect,
            Datepicker
        }

    }
</script>

<style scoped>
    form {
        width: 50%;
        margin-bottom: 20px;
    }

    form th {
        font-size: 17px;
    }

    .ids {
        width: 60%;
    }
    .amount-input,
     .order-select
     {
        width: 30%;
        display: inline-block;
        margin-right: 15px;
    }

    .filter-label {
        font-weight: bold;
    }




</style>