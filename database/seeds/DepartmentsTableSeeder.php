<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("departments")->insert(["name" => "development"]);
        DB::table("departments")->insert(["name" => "compliance"]);
        DB::table("departments")->insert(["name" => "risk"]);
        DB::table("departments")->insert(["name" => "management"]);
        DB::table("departments")->insert(["name" => "customers_support"]);
        DB::table("departments")->insert(["name" => "sales"]);
        DB::table("departments")->insert(["name" => "marketing"]);
        DB::table("departments")->insert(["name" => "hr"]);
        DB::table("departments")->insert(["name" => "screens"]);
    }
}
