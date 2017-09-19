<?php

use Illuminate\Database\Seeder;

class TablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("tables")->insert(["broker_id" => 1, "type" => 1, "name"  =>  "FTD Morning", "Manager" => "Unknown"]);
        DB::table("tables")->insert(["broker_id" => 1, "type" => 1, "name"  =>  "FTD Evening", "Manager" => "Unknown"]);
        DB::table("tables")->insert(["broker_id" => 1, "type" => 2, "name"  =>  "RST Morning", "Manager" => "Unknown"]);
        DB::table("tables")->insert(["broker_id" => 1, "type" => 2, "name"  =>  "RST Morning", "Manager" => "Unknown"]);
        DB::table("tables")->insert(["broker_id" => 2, "type" => 1, "name"  =>  "FTD Morning", "Manager" => "Unknown"]);
        DB::table("tables")->insert(["broker_id" => 2, "type" => 1, "name"  =>  "FTD Evening", "Manager" => "Unknown"]);
        DB::table("tables")->insert(["broker_id" => 2, "type" => 2, "name"  =>  "RST Morning", "Manager" => "Unknown"]);
        DB::table("tables")->insert(["broker_id" => 2, "type" => 2, "name"  =>  "RST Morning", "Manager" => "Unknown"]);
    }
}
