<template>
	<div>
		<div class="row">
			<loader v-if="loading"></loader>
		    <div class="col-lg-12">
		        <div class="panel panel-default">
		            <div class="panel-heading">
		                <i class="fa fa-users"></i> All Tables
		            </div>

                    <modal v-if="goalsModal.showModal" @confirm="updateGoals" @close="goalsModal.showModal = false" width="600">
                        <h3 slot="header">
                            <b>Set Goals For</b> {{ selectedTable.name }}
                        </h3>
                        <div class="form-group" slot="body">
                            <label for="daily_goal">Set Daily Goal:</label>
                            <input id="daily_goal" type="text" class="form-control" v-model="goalsModal.dailyGoal">

                            <label for="monthly_goal">Set Monthly Goal:</label>
                            <input id="monthly_goal" type="text" class="form-control" v-model="goalsModal.monthlyGoal">
                        </div>
                    </modal>
					
					<!--Assign employees modal-->
                    <modal v-if="employeesModal.showModal" :width="600">
                        <h3 slot="header">
                        	<button type="button" class="close" @click="closeEmployeesModal">&times;</button>
                            Assign employees to {{ selectedTable.name }}
                        </h3>

                        <div slot="body">
                            <div class="form-group">
                                <label>Employees</label>
								
								<multiselect 
									v-model="selectedEmployees" 
									label="name" 
									track-by="name" 
									placeholder="Type to search" 
									:options="employees" 
									:multiple="true" 
									:searchable="true" 
									:close-on-select="false">
									<span slot="noResult">Oops! No employees found.</span>
								</multiselect>
                            </div>
                        </div>
                        <div slot="footer">
                            <button type="button" class="btn btn-success" @click="assignEmployeesToTable">
                            	Assign Employees
                            </button>
                        </div>
                    </modal><!--Assign employees modal-->

                    <!--create table modal-->
                    <modal v-if="tableModal.showModal" :width="400">
                        <h3 slot="header">
                            <button type="button" class="close" @click="closeCreateModal">&times;</button>
                            Create new table
                        </h3>

                        <div slot="body">
                        	<div class="form-group">
                                <label for="name">Name </label>
                                <input type="text" class="form-control" v-model="tableModal.name" id="name">
                            </div>
                            <div class="form-group">
                                <label for="type">Type </label>
                                <select v-model="tableModal.type" id="type" class="form-control">
                                	<option value="1">FTD</option>
                                	<option value="2">RST</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Manager</label>
                                <multiselect v-model="tableModal.manager" :options="users"
					                         placeholder="No manager selected" label="name" track-by="name" :multiple="false">
					            </multiselect>
                            </div>
                        </div>
                        <div slot="footer">
                            <p class="split-error" v-show="tableModal.isError">{{ tableModal.errorMessage }}</p>
                            <button type="button" class="btn btn-success" @click="createTable">
                            	Create Table
                            </button>
                        </div>
                    </modal><!--create table modal-->

		            <div class="panel-body">
		                <div id="deposits_wrapper">
		                    <div class="dataTable_wrapper">
		                        <table class="table table-striped table-bordered table-hover" id="dataTables-employees">
		                            <thead>
		                                <tr>
		                                    <th>ID</th>
		                                    <th>Name</th>
		                                    <th>Type</th>
                                            <th>Manager</th>
                                            <th>Daily Goal</th>
		                                    <th>Monthly Goal</th>
		                                    <th>Actions</th>
		                                </tr>
		                            </thead>
		                            <tbody>
										<table-row 
											v-for="table in tables" 
											:table="table"
											:users="users"
											:is_admin="is_admin"
											@nameChange="changeName"
											@deactivate="deactivate"
                                            @activate="activate"
                                            @goalsRequest="openGoalsModal"
											@managerSelect="assignManager"
											@assignEmployees="openEmployeesModal"></table-row>

		                            </tbody>
		                        </table>
		                        <div class="row" style="text-align: center;">
		                            <button class="btn btn-primary" @click="tableModal.showModal = true">CREATE NEW TABLE</button>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
	</div>
</template>

<script type="text/babel">
	import TableRow from './TableRow.vue';
	import Multiselect from 'vue-multiselect';
	import Loader from '../../common/Loader.vue';

	export default {
		name: "sales-tables",
		// props: {
		// 	is_admin: {
		// 		required: true
		// 	}
        //
		// },
        mounted(){
            Vue.http.get("/sales/tables/all-tables")
            .then(response => {
                this.tables = response.body.tables;
                this.brokers = response.body.brokers;
                this.users = response.body.users;
                this.employees = response.body.employees;
            });
        },
		data(){
			return {
                tables: [],
                brokers: [],
                users: [],
                employees: [],
				tableModal: {
                    showModal: false,
                    name: "",
                    type: "1",
                    manager: {},
                    isError: false,
                    errorMessage: ''
                },
                employeesModal: {
                	showModal: false,
                	query: ""
                },
                goalsModal: {
                    showModal: false,
                    dailyGoal: "",
                    monthlyGoal: ""
                },
                loading: false,
                selectedTable: {},
                selectedEmployees: []
			}
		},

		methods: {
			changeName(data){

				Vue.http.post("/sales/update-table-name", data)
				.then(response => {
					//do something if neccessary...
				}).catch(error => {
					location.reload();
				});
			},
			deactivate(table_id){
				Vue.http.post("/sales/tables/deactivate", {table_id})
				.then(response => {
                    this.tables.map(table => {
                        if(table.id == table_id){
                            table.active = 0;
                        }
                    });
				}).catch(error => {
					location.reload();
				});
			},
            activate(table_id){
                Vue.http.post("/sales/tables/activate", {table_id})
                .then(response => {
                    this.tables = response.body.tables;
                }).catch(error => {
                    location.reload();
                });
            },
			assignManager(data){
				Vue.http.post("/sales/tables/assign-manager", data)
				.then(response => {
					//do something if neccessary
				}).catch(error => {
					location.reload();
				});
			},
            updateGoals(){
                Vue.http.post("/sales/tables/set-goals", {
                    table_id: this.selectedTable.id,
                    daily: this.goalsModal.dailyGoal,
                    monthly: this.goalsModal.monthlyGoal,
                })
                .then(response => {
                    this.tables.map(table => {
                        if(table.id == this.selectedTable.id){
                            table.daily_goal = this.goalsModal.dailyGoal;
                            table.monthly_goal = this.goalsModal.monthlyGoal;
                        }
                    });
                    this.goalsModal.showModal = false;
                    this.goalsModal.dailyGoal = "";
                    this.goalsModal.monthlyGoal = "";
                    swal("Done!", "Table goal has been updated.", "success");
                })
                .catch(errors => {
                    let error = Object.values(errors.body)[0][0];
                    swal("Oops...", error, "error");
                });
            },
            openGoalsModal(table){
                this.selectedTable = table;
                this.goalsModal.showModal = true;
            },
			closeCreateModal(){
				this.tableModal.isError = false;
                this.tableModal.showModal = false;
                this.tableModal.name = "";
                this.tableModal.type = 1;
			},
			createTable(){
				Vue.http.post("/sales/tables/create", {
					name: this.tableModal.name,
					type: this.tableModal.type,
					manager_id: this.tableModal.manager.id
				})
				.then(response => {
					location.reload();
				}).catch(error => {
					this.tableModal.isError = true;
					this.tableModal.errorMessage = "Something went wrong, please try again";
				});
			},
			openEmployeesModal(table){
				this.selectedTable = table;
				this.loading = true;
				Vue.http.get("/sales/tables/assigned-employees?table_id=" + table.id)
				.then(response => {
					this.loading = false;
					this.selectedEmployees = response.data.employees;
					this.employeesModal.showModal = true;
				});
			},
			closeEmployeesModal(){
				this.employeesModal.showModal = false;
				this.selectedTable = {};
			},
			assignEmployeesToTable(){

				this.loading = true;

				let employeesId = [];

				_.map(this.selectedEmployees, employee => {
					employeesId.push(employee.id);
				});

				Vue.http.post("/sales/tables/assign-employees", {
					table_id: this.selectedTable.id,
					employees: employeesId
				})
				.then(response => {
					this.loading = false;
					swal('Done!','All employees assigned successfully.','success');
					this.employeesModal.showModal = false;
				})
				.catch(error => {
					this.loading = false;
					console.log(error);
				});
			}
		},
		computed: {
			selectedManager(){
                if (!this.employee) {
                    return {};
                }

                return {
                    id: this.deposit.receptionEmployeeId,
                    name: this.deposit.employee.name
                };
            }
		},
		components: {
			TableRow, Multiselect, Loader
		}
	}
</script>

<style scoped>
	
</style>