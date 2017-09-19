<template>
    <tr>
        <td>{{ table.id }}</td>
        <td>
            <input type="text" class="name_input form-control" :value="table.name" @change="nameChanged"/>
        </td>
        <td>{{table.type == 1 ? "FTD" : "RST"}}</td>
        <td style="width: 25%;">
            <multiselect v-model="selectedManager" @input="assignManagerToTable" :options="users"
                         placeholder="No worker selected" label="name" track-by="name" :multiple="false">
            </multiselect>
        </td>
        <td>{{ goalForType(table.type, table.daily_goal) }}</td>
        <td>{{ goalForType(table.type, table.monthly_goal) }}</td>
        <td style="text-align: center;">
            <a v-if="table.active == 1"
               class="btn btn-success btn-xs"
               @click.prevent="deactivate"
               data-toggle="tooltip"
               data-placement="bottom"
               title="Deactivate">
                <i class="fa fa-toggle-on" aria-hidden="true"></i>
            </a>

            <a v-else class="btn btn-danger btn-xs"
               @click.prevent="activate"
               data-toggle="tooltip"
               data-placement="bottom"
               title="Activate">
                <i class="fa fa-toggle-off" aria-hidden="true"></i>
            </a>
            <a class="btn btn-success btn-xs"
               @click.prevent="assignEmployees"
               data-toggle="tooltip"
               data-placement="bottom"
               title="Assign Employees">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
            <a class="btn btn-success btn-xs"
               @click.prevent="$emit('goalsRequest', table)"
               data-toggle="tooltip"
               data-placement="bottom"
               title="Set Goals">
                <i class="fa fa-money" aria-hidden="true"></i>
            </a>
            <a target="_blank"
               class="btn btn-primary btn-xs view_table"
               :href="'/screens/scoreboard/' + table.id"
               data-toggle="tooltip"
               data-placement="bottom"
               title="View Table">
                <i class="fa fa-expand" aria-hidden="true"></i>
            </a>
            <form class="form-inline" method="GET" action="tables/monthly-report">
                <input type="hidden" name="csv_table_id" :value="table.id">
                <button class="btn btn-success report" type="submit" data-toggle="tooltip" data-placement="bottom" title="Download this month deposits and withdrawals to excel"><i class="fa fa-calendar" aria-hidden="true"> M</i></button>
            </form>
            <form class="form-inline" method="GET" action="tables/daily-report">
                <input type="hidden" name="csv_table_id" :value="table.id">
                <button class="btn btn-success report" type="submit" data-toggle="tooltip" data-placement="bottom" title="Download this day deposits and withdrawals to excel"><i class="fa fa-calendar" aria-hidden="true"> D</i></button>
            </form>

            <a v-if="is_admin == 1" target="_blank"
               class="btn btn-primary btn-xs view_table"
               :href="'/sales/reports/manager/' + table.id"
               data-toggle="tooltip"
               data-placement="bottom"
               title="View Report">
                <i class="fa fa-line-chart" aria-hidden="true"></i>
            </a>
        </td>
    </tr>
</template>

<script type="text/babel">
	import Multiselect from 'vue-multiselect';
    import {numberFormat} from '../../../helpers/functions';

	export default {
		name: "table-row",
		props: {
			table: {
				required: true
			},
			users: {
				required: true
			},
            is_admin: {
                required: true
            },
		},
		methods: {
			nameChanged(e){

				this.$emit("nameChange", {
					table_id: this.table.id,
					name: e.target.value
				});

			},
			assignManagerToTable(user){
				this.$emit("managerSelect", {
                    user_id: user ? user.id : null,
                    table_id: this.table.id
                });
			},
			assignEmployees(){
                if(!this.table.active){
                    swal("Oops.", "Table must be active to assign new employees.", "error");
                    return;
                }
				this.$emit("assignEmployees", this.table);
			},
			deactivate(e){
                swal({
                    title: 'Are you sure?',
                    text: "This will remove all assigned employees from this table!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Deactivate!'
                }).then(() => {
                    this.$emit("deactivate", this.table.id);
                    // swal('Deleted!','Your file has been deleted.','success');
                });
			},
            activate(e){
                this.$emit("activate", this.table.id);
            },
            goalForType(type, num){
                if(type == 1){
                    return  num ? num : 0;
                }else{
                    return  "$" + numberFormat(num);
                }
            }
		},
		computed: {
			selectedManager(){
                if (!this.table.manager) {
                    return {};
                }

                return this.table.manager;
            },
		},
		components: {
			Multiselect
		}
	}



</script>

<style scoped>
    .form-inline{
        display: inline-block;
    }
    .btn-group-xs>.btn, .btn-xs {
        padding: 1px 5px;
        font-size: 19px;
        line-height: 1.5;
        border-radius: 3px;
    }
</style>