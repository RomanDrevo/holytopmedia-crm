<template>
    <div style="margit-top: 140px;" class="employees-wraper">
        <loader v-if="loading"></loader>
        <div class="panel panel-default">
            <div class="row">
                <div class="col-md-12">
                    <form @submit.prevent="getSearchResults" class="form-inline" id="filter-form">
                        <multiselect
                                v-model="search.table"
                                :options="tables"
                                placeholder="No table selected"
                                label="name"
                                track-by="name"
                                :multiple="false">
                        </multiselect>


                        <div class="form-group">
                            <select class="form-control"  v-model="search.activation_status">
                                <option value="">Employee status</option>
                                <option value="1">Active</option>
                                <option value="0">Not Active</option>
                            </select>
                        </div>


                        <input class="form-control"type="text" v-model="search.term">
                        <div class="form-group">
                            <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                        <button v-if="isSearched" class="btn btn-default" @click.prevent="reload">
                            <i class="fa fa-refresh" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>
            <div class="panel-heading">
                <i class="fa fa-users"></i> {{ title | reverseStr |  uppercase}}
            </div>
            <modal v-if="showModal" @confirm="updateGoals" @close="showModal = false" width="600">
                <h3 slot="header">
                    <b>Set Goals For</b> {{ selectedEmployee.name }}
                </h3>
                <div class="form-group" slot="body">
                    <label for="table_select">Select Table:</label>

                    <multiselect
                        v-model="selectedTable"
                        @input="showGoalsInputs"
                        :options="tables"
                        placeholder="No table selected"
                        label="name"
                        track-by="name"
                        :multiple="false">
                    </multiselect>

                    <div v-if="showGoals">
                        <label for="daily_goal">Set Daily Goal:</label>
                        <input id="daily_goal" type="text" class="form-control" v-model="dailyGoal">

                        <label for="monthly_goal">Set Monthly Goal:</label>
                        <input id="monthly_goal" type="text" class="form-control" v-model="monthlyGoal">
                    </div>
                </div>
            </modal>
            <modal v-if="showPictureModal" @confirm="updateImage" @close="showPictureModal = false" width="600">
                <h3 slot="header">
                    <b>Set Picture For</b> {{ selectedEmployee.name }}
                </h3>
                <div class="form-group" slot="body">
                    <input @change="previewThumbnail" type="file">
                    <div class="image-wrapper">
                        <img class="prev-img" :src="imageSrc">
                    </div>
                </div>
            </modal>
            <table class="table table-striped table-bordered table-hover" id="dataTables-employees">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th style="display:none;">Goals</th>
                    <th>Table</th>
                    <th>Active?</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    <employee-row v-for="employee in employees" :imageSrc="imageSrc" :emp="employee" :tables="tables" @showModal="showEmployeeModal" @showPhotoModal="showEmployeePhotoModal"></employee-row>
                </tbody>
            </table>

            <pagination :current_page="employeesData.current_page"
                        :prev_page_url="employeesData.prev_page_url"
                        :last_page="employeesData.last_page"
                        :next_page_url="employeesData.next_page_url"
                        :url_path="url"
                        :total="employeesData.total"
                        name="employees"
                        :per_page="employeesData.per_page"
                        :updateRecords="updateRecords">
            </pagination>

        </div>
    </div>
</template>


<script type="text/babel">
    import EmployeeRow from './EmployeeRow.vue';
    import Modal from '../../common/Modal.vue';
    import Multiselect from 'vue-multiselect';
    import Loader from '../../common/Loader.vue';

    export default {
        name: "employees-view",
        props: {
            tables: {
                type: Array,
                requierd: true
            }
        },
        data(){
            return {
                title: "All employees",
                search: {
                    table: '',
                    term: '',
                    activation_status: ''
                },
                searchBy: "",
                showModal: false,
                showPictureModal: false,
                loading: false,
                selectedEmployee: {},
                selectedTable: {},
                employeesData: {},
                employees: [],
                dailyGoal: "",
                monthlyGoal: "",
                query: '',
                isSearched: false,
                showGoals: false,
                image: {},
                imageSrc: "",
                some_value: '',
                some_options: ['Select option', 'options', 'selected', 'mulitple', 'label', 'searchable', 'clearOnSelect', 'hideSelected', 'maxHeight', 'allowEmpty', 'showLabels', 'onChange', 'touched']
            };
        },

        computed: {
            url() {
                let tempUrl = "/sales/employees/get-employees";
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
                    this.employeesData = response.body;
                    this.employees = this.employeesData.data;
                });
            },

            showEmployeeModal(employee){
                this.selectedEmployee = employee;
                this.showModal = true;
            },

            showEmployeePhotoModal(employee){
                this.selectedEmployee = employee;
                this.showPictureModal = true;
            },

            showGoalsInputs(){
                this.loading = true;
                let data = {
                    employee_id: this.selectedEmployee.id,
                    table_id: this.selectedTable.id
                };

                this.$http.post("/sales/get-goal-for-table", data)
                        .then(response => {
                            this.dailyGoal = response.body.daily;
                            this.monthlyGoal = response.body.monthly;
                            this.loading = false;
                            this.showModal = true;
                            this.showGoals = true;
                        });
            },

            updateGoals(){
                this.loading = true;
                let data = {
                    daily: this.dailyGoal,
                    monthly: this.monthlyGoal,
                    employee_id: this.selectedEmployee.id,
                    table_id: this.selectedTable.id
                };

                this.$http.post('/sales/update-goal', data)
                        .then((response) => {
                            swal("Done!", "The goal has been set for this table.", "success");
                            this.dailyGoal = "";
                            this.monthlyGoal = "";
                            this.loading = false;
                            this.showModal = false;
                            this.showGoals = false;
                        })
                        .catch(errors => {
                            let error = Object.values(errors.body)[0][0];
                            swal("Oops...", error, "error");
                        });
                this.selectedTable = {};

            },

            getSearchResults() {
                let table_id = this.search.table ? this.search.table.id : "";
                let term = this.search.term;
                let active_status = this.search.activation_status ? this.search.activation_status : "";

                let tempUrl = '/sales/employees/get-employees?term=' + term + '&table_id=' + table_id + '&is_active=' + active_status;

                this.getData(tempUrl);
                this.isSearched = true;
            },

            reload(){
                this.getData('/sales/employees/get-employees');
                this.isSearched = false;
                this.term = '';
                this.search.table = "";
                this.search.activation_status = "";
            },

            previewThumbnail(event){
                let input = event.target;

                if(input.files && input.files[0]){
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageSrc = e.target.result;
                        // console.log(this.imageSrc);
                        eventBus.$emit("imagePrevChanged", {
                            employee_id: this.selectedEmployee.id,
                            image: this.imageSrc
                        });
                    };
                    reader.readAsDataURL(input.files[0]);
                    this.image = input.files[0];
                }
            },

            updateImage(){
                let form = new FormData();
                form.append('img', this.image);
                form.append('employee_id', this.selectedEmployee.id);
                this.$http.post('/sales/employees/add-employee-image', form)
                        .then(response => {
                            //this.imgSrc =  response.body.employee.image;
                        });
                this.showPictureModal = false;
                this.imageSrc = "";
            }
        },

        filters: {
            uppercase(value){
                return value.toUpperCase();
            },

            reverseStr(str){
                return str.split('').reverse().join('');
            }
        },

        mounted(){
            this.getData('/sales/employees/get-employees');
        },

        components: {
            EmployeeRow, Modal, Multiselect, Loader
        }
    }
</script>

<style scoped>
    #filter-form {
        margin-top: 15px;
        margin-right: 15%;
    }
    #filter-form .form-group {
        margin-right: 5px;
    }
     .multiselect:nth-child(1){
        display: inline-block;
        width:100%;
    }

    .form-inline .form-control{
        display: inline-block;
    }
    .multiselect {
        box-sizing: content-box;
        display: inline-block;
        position: relative;
        width: 21%;
        min-height: 40px;
        text-align: left;
        color: #35495e;
    }

    .multiselect[data-v-6edcb128]:nth-child(1) {
        display: inline-block;
        width: 34%;
    }
</style>

