<template>
    <div class="container" style="margin-bottom: 70px; margin-top: 100px;">
        <div class="row">
            <div class="panel panel-primary" style="width: 60%; margin: 60px auto;">
                <div class="panel-heading">
                    <h4>Create a new user</h4>
                </div>

                <div class="panel-body">
                    <form @submit.prevent="createUser">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" v-model="name">
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" v-model="password">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" v-model="email">
                        </div>

                        <div class="form-group">
                            <label for="broker">Broker:</label>
                            <select id="broker" name="broker" class="form-control" v-model="broker_id">
                                <option :value="null">Choose a broker...</option>
                                <option v-for="broker in brokers" :value="broker.id">{{ broker.name }}</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="department">Department:</label>
                            <select @change="selectPermissionsOnChange" id="department" name="department" class="form-control"
                                    v-model="department_name">
                                <option :value="null">Choose a department...</option>
                                <option v-for="department in departments" :value="department.name">{{ department.name
                                    }}
                                </option>
                            </select>
                        </div>

                        <div class="form-check">

                            <div class="col-sm-4 departments" v-for="department in departments">
                                <h4 class="dep-name">{{ department.name.replace("_", " ") }}</h4>

                                <label v-for="permission in department.permissions">
                                    <input type="checkbox" :checked="allPermissions[department.name][permission.id]"
                                           class="form-check-input" @change="changeValue"
                                           name="permissions[]" :value="permission.id">{{
                                    permission.label }}
                                </label>
                            </div>
                            <hr>

                        </div>

                        <div class="pull-right" style="clear: both;">
                            <input type="submit" value="Save" class="btn btn-primary"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>


<script type="text/babel">

    export default{

        props: ["departments", "brokers", "permissions"],

        data(){
            return {
                allPermissions: {},
                name: '',
                password: '',
                email: '',
                broker_id: null,
                department_name: null

            }
        },
        created(){
            this.allPermissionsObject();
        },

        methods: {
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

            selectPermissionsOnChange(e) {
                this.allPermissionsObject();
                for (var d in this.allPermissions) {
                    for (var p in this.allPermissions[d]) {
                        if(d == e.target.value) {
                            this.allPermissions[d][p] = true;
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

            createUser() {
                let permissons = [];
                for (var d in this.allPermissions) {
                    for (var p in this.allPermissions[d]) {
                        if (this.allPermissions[d][p]) {
                            permissons.push(p);
                        }
                    }
                }
                Vue.http.post('/admin/users/store', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    department_name: this.department_name,
                    broker_id : this.broker_id,
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

    h4.dep-name {
        text-transform: capitalize;
    }
    .form-check label {
        display: block;
    }

    hr {
        border-top: 1px solid #ccc;
    }
    .departments {
        min-height: 220px;
    }


</style>