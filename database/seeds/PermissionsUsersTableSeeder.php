<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Permission;

class PermissionsUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::all();

        foreach (User::all() as $user) {
           foreach ($permissions as $permission) {
                $user->assignPermission($permission->name);
           }
        }

    }
}
