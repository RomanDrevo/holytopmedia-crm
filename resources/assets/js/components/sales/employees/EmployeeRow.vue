<template>
    <tr>
        <td>{{ emp.id }}</td>
        <td class="employee_name">
            <input type="text" :data-employee-id="emp.id" name="employee_name" class="form-control emloyee_name_input" :value="emp.name" @change="updateName($event.target.value)">
        </td>
        <td style="display:none;">
            <!--<ul>-->
                <!--<li v-for="goal in emp.goals">-->
                    <!--Table {{ goal.table_id }}: Daily - {{ goal.daily }}, Monthly - {{ goal.monthly }}-->
                <!--</li>-->
            <!--</ul>-->
            <span class="goal_span">Daily: {{ emp.goal ? emp.goal.daily : 0 }}</span>
            <span class="goal_span">Monthly: {{ emp.goal ? emp.goal.monthly : 0 }}</span>
        </td>
        <td>
            <multiselect v-model="emp.table" :options="tables" @input="setTable"
                         placeholder="No table selected" label="name" track-by="name" :multiple="false">
            </multiselect>
        </td>
        <td>{{ emp.active ? "YES" : "NO" }}</td>
        <td style="text-align:right;">
            <div class="buttons-wrapp">
                <button class="btn btn-success btn-xs set_goal" @click="showEmployeeModal" title="Set goals">
                    <i class="fa fa-rocket" aria-hidden="true"></i>
                </button>

                <button class="btn btn-success btn-xs set_goal" @click="openPhotoModal" title="Load picture">
                    <i class="fa fa-file-image-o"></i>
                </button>

                <!--<button class="btn btn-success btn-xs set_goal"  @click="downloadReport">-->
                    <!--Download Monthly-->
                <!--</button>-->
                <form class="form-inline" method="GET" action="employees/csv-employee-deposits/monthly">
                    <input type="hidden" name="csv_employee_id" :value="emp.id">
                    <button class="btn btn-success report" type="submit" data-toggle="tooltip" data-placement="bottom" title="Download this month deposits and withdrawals to excel"><i class="fa fa-calendar" aria-hidden="true"> M</i></button>
                </form>
                <form class="form-inline" method="GET" action="employees/csv-employee-deposits/daily">
                    <input type="hidden" name="csv_employee_id" :value="emp.id">
                    <button class="btn btn-success report" type="submit" data-toggle="tooltip" data-placement="bottom" title="Download this day deposits and withdrawals to excel"><i class="fa fa-calendar" aria-hidden="true"> D</i></button>
                </form>
            </div>
            <img width="70px" height="80px"  :src="photo">
        </td>
    </tr>
</template>
<style>

</style>
<script type="text/babel">
    import Multiselect from 'vue-multiselect';

    export default{
        name: 'employee-row',
        data(){
            return {
                selectedTable: "",
                prevImg: "",
                imgChanged: false
            }
        },

        props: {
            emp: {
                required: true
            },
            tables: {
                type: Array,
                requierd: true
            }
        },
        mounted(){
            if (this.emp.table) {
                this.selectedTable = this.emp.table;
            }

            eventBus.$on("imagePrevChanged", this.changeImage);
        },
        methods: {
            showEmployeeModal(){
                this.$emit('showModal', this.emp);
            },

            openPhotoModal(){
                this.$emit('showPhotoModal', this.emp)
            },

            updateName(value){
                this.emp.name = value;
                this.$http.post('/sales/update-employee-name', {'name': this.emp.name, 'employee_id': this.emp.id})
            },

            setTable(){
                let table_id = this.emp.table ? this.emp.table.id : null;
                this.$http.post('/sales/update-user-table', {'table_id': table_id, 'employee_id': this.emp.id});
            },

            changeImage(payload){

                if (this.emp.id != payload.employee_id){
                    return;
                }
                this.imgChanged = true;


                this.prevImg = payload.image;
                this.emp.image = "";
            },

            downloadReport(){
                this.$http.get('/sales/employees/csv-employee-deposits?csv_employee_id=' + this.emp.id).then(response => {
                    console.log(response);
                });
            }
        },
        computed: {
            photo(){
                if (this.imgChanged) {
                    return this.prevImg;
                }

                //if the employee has image we show it, otherwise we dispkay a default anonymus image.
                if (this.emp.image != null) {
                    return '/app/' + this.emp.image;
                }

                return "/images/person.png";
            }
        },
        components: {
            Multiselect
        }
    }
</script>

<style scoped>
     img{
        right: 0;
        margin-top: 6px;
        margin-bottom: 6px;
        margin-right: -6px;
        border-radius: 8px 0px 0px 8px;
        box-shadow: -2px 1px 10px;
    }

    .buttons-wrapp{
        float: left;
        height: 100%;
        margin-top: 5%;
        margin-left: 10%;
    }

    .set_goal, .report{
        padding: 1px 5px;
        font-size: 24px;
        line-height: 1.5;
        border-radius: 3px;
    }

    .form-inline{
        display: inline-block;
    }

    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: middle;
        text-align: center;
    }

</style>