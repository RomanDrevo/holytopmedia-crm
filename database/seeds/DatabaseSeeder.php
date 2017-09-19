<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UserTableSeeder::class);
//        $this->call(DepartmentsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
//        $this->call(PermissionsUsersTableSeeder::class);
//        $this->call(BrokersTableSeeder::class);
//        $this->call(TablesTableSeeder::class);
//        $this->call(SettingsTableSeeder::class);
//        $this->call(DepositsAssignAtColumnSeeder::class);
    }

}
