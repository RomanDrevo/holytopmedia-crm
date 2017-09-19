<template>
    <div class="container user-page">
        <div class="x_content">
            <div class="row">

                <div class="panel panel-primary" style="width: 60%; margin: 80px auto;">

                    <div class="panel-heading">
                        <h4>Edit {{ user.name }} information</h4>
                    </div>
                    <div class="panel-body">
                        <form @submit.prevent="updateUser" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="id">ID</label>
                                <input type="text" v-model="user.id" class="form-control" id="id"
                                       readonly disabled>
                            </div>

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" v-model="user.name" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="email" v-model="user.password">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" v-model="user.email">
                            </div>

                            <div class="form-group">
                                <label for="broker">Broker:</label>
                                <select id="broker" name="broker" class="form-control" v-model="user.broker.id">
                                    <option v-for="broker in brokers" :value="broker.id">{{ broker.name }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="department">Department:</label>
                                <select @change="selectPermissionsOnChange" id="department" name="department"
                                        class="form-control"
                                        v-model="user.department.name">
                                    <option v-for="department in departments" :value="department.name">{{ department.name }}</option>
                                </select>
                            </div>

                            <div class="form-check">
                                <div class="col-sm-4 departments" v-for="department in departments">
                                    <h4 class="dep-name">{{ department.name.replace("_", " ") }}</h4>

                                    <label v-for="permission in department.permissions" class="dept-label">
                                        <input type="checkbox"
                                               :checked="allPermissions[department.name][permission.id]"
                                               class="form-check-input"
                                               name="permissions[]" :value="permission.id" @change="changeValue">{{ permission.label }}
                                    </label>
                                </div>
                                <hr>

                                <div class="pull-right" style="clear: both;">
                                    <input type="submit" value="Save" class="btn btn-primary"/>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>


            </div>

        </div>
    </div>
</template>


<style>


</style>


<script type="text/babel">

    export default{

        props: ["user", "permissions", "departments", "brokers"],

        data(){
            return {
                allPermissions: {}

            }
        },

        created(){
            this.allPermissionsObject();
            this.setInitialPermissions();
        },

        methods: {

            //build an permissions object [department_name => [permission_name => boolean] and initialise all key as false
            allPermissionsObject() {
                var ap = this.allPermissions;
                this.departments.forEach(function (d) {
                    Vue.set(ap, d.name, {});
                    d.permissions.forEach(function (p) {
                        Vue.set(ap[d.name], p.id, false);

                    });
                });
                this.allPermissions = ap;
            },

            //get user permissions array from data and based on it update permissions object. set relevant permission names as true
            setInitialPermissions(){
                var ap = this.allPermissions;

                var departments = this.departments;
                var permissions = this.permissions;
                departments.map(function (d) {
                    permissions.map(function (p) {
                        if (d.id == p.department_id) {
                            ap[d.name][p.id] = true;
                        }
                    });

                });
                this.allPermissions = ap;
            },

            //on select change get selected value and update permissions object. set permission names related to chosen department as true
            selectPermissionsOnChange() {
                this.allPermissionsObject();
                for (var d in this.allPermissions) {
                    for (var p in this.allPermissions[d]) {
                        if (d == this.user.department.name) {
                            this.allPermissions[d][p] = true;
                        } else {
                            this.allPermissions[d][p] = false;
                        }


                    }

                }
            },

            changeValue(e) {
                for (var d in this.allPermissions) {
                    for (var p in this.allPermissions[d]) {
                        if(e.target.value == p) {
                            this.allPermissions[d][p] = !this.allPermissions[d][p];
                        }
                    }
                }

            },
            updateUser() {
                let permissons = [];
                for (var d in this.allPermissions) {
                    for (var p in this.allPermissions[d]) {
                        if (this.allPermissions[d][p]) {
                            permissons.push(p);
                        }
                    }
                }
                Vue.http.post('/admin/users/update', {
                    id: this.user.id,
                    name: this.user.name,
                    email: this.user.email,
                    password: this.user.password,
                    department_name: this.user.department.name,
                    broker_id : this.user.broker.id,
                    permissions: permissons
                }).then((response) => {
                    if(response.body == 'ok') {
                        window.location = '/admin/users';
                    } else {
                        alert(response.body);
                    }

                });

            }
        }
    }

</script>
<style scoped>
    .departments {
        min-height: 220px;
    }
    .dept-label {
        display: block;
    }
</style>