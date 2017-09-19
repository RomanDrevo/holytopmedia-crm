<template>


        <tr class="even pointer">
            <td>{{ cus.customer_crm_id }}</td>
            <td>{{ cus.FirstName + " " + cus.LastName }}</td>
            <td>{{ cus.email }}</td>
            <td>{{ countries[cus.Country] }}</td>
            <td>{{ cus.Phone }}</td>
            <td>
                <a :href=" '/compliance/customers/'+cus.customer_crm_id " type="button" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="bottom" title="Edit">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </a>
            </td>
        </tr>


</template>
<style>
    body{

    }
</style>
<script type="text/babel">

    export default{
        name: "customer-row",
        props: {
            cus:{
                required:true
            },
            countries: {

                requierd: true
            }
        },
        components:{

        }
    }
</script>
