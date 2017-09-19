<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table("settings")->insert(["option_name" => "GBP/USD", "option_value" => "1.3", "pretty_name"  =>  "GBP"]);
//        DB::table("settings")->insert(["option_name" => "EUR/USD", "option_value" => "1.1", "pretty_name"  =>  "EUR"]);;
//        DB::table("settings")->insert(["option_name" => "USD/NIS", "option_value" => "3.859966", "pretty_name"  =>  "NIS"]);
//        DB::table("settings")->insert(["option_name" => "USD/USD", "option_value" => "1", "pretty_name"  =>  "USD"]);
//        DB::table("settings")->insert(["option_name" => "monthly_goal", "option_value" => "320000", "pretty_name"  =>  "Company Monthly Goal"]);
//
//        DB::table("settings")->insert(["option_name" => "alerts_keywords", "option_value" => "financial | withdrawal", "pretty_name"  =>  "Alerts Matching Keywords"]);
//        DB::table("settings")->insert(["option_name" => "keywords_since", "option_value" => Carbon::now()->subDays(3), "pretty_name"  =>  "Matching Keywords Since Date"]);
//
//        DB::table("settings")->insert(["option_name" => "big_depositor_total", "option_value" => 10000, "pretty_name"  =>  "Big Depositor Total Deposits"]);
//        DB::table("settings")->insert(["option_name" => "not_contacted_since", "option_value" => Carbon::now()->subDays(5), "pretty_name"  =>  "Depositor Not Contacted Since"]);
//        DB::table("settings")->insert(["option_name" => "not_contacted_last_deposit", "option_value" => Carbon::now()->subMonths(3), "pretty_name"  =>  "Not Contacted Depositor Last Deposit"]);
//
//        DB::table("settings")->insert(["option_name" => "declined_since", "option_value" => Carbon::now()->subDays(5), "pretty_name"  =>  "Declined Deposit Since"]);
//
//        DB::table("settings")->insert(["option_name" => "not_verified_last_deposit", "option_value" => Carbon::now()->subDays(5), "pretty_name"  =>  "Not Verified Deposit Since"]);
//        DB::table("settings")->insert(["option_name" => "not_verified_total", "option_value" => 10000, "pretty_name"  =>  "Big Depositor Total Deposits"]);


//        DB::table("settings")->insert(["option_name" => "small_deposit", "option_value" => "", "pretty_name"  =>  "Small Deposit Video"]);
//        DB::table("settings")->insert(["option_name" => "big_deposit", "option_value" => "", "pretty_name"  =>  "Big Deposit Video"]);

        DB::table("settings")->insert(["option_name" => "auto_approved_processors", "option_value" => "Fibonatix1 | Inatec", "pretty_name"  =>  "Auto Approved Processors"]);

    }
}
