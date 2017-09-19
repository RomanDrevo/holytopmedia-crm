<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table("permissions")->insert(["name" => "sales", "label" => "Sales", "department_id" => 6]);
//        DB::table("permissions")->insert(["name" => "sales_show_deposits", "label" => "Show Deposits", "department_id" => 6]);
//        DB::table("permissions")->insert(["name" => "sales_edit_deposits", "label" => "Edit Deposits", "department_id" => 6]);
//        DB::table("permissions")->insert(["name" => "sales_show_withdrawals", "label" => "Show Withdrawals", "department_id" => 6]);
//        DB::table("permissions")->insert(["name" => "sales_edit_withdrawals", "label" => "Edit Withdrawals", "department_id" => 6]);
//
//        DB::table("permissions")->insert(["name" => "marketing", "label" => "Marketing", "department_id" => 7]);
//
//        DB::table("permissions")->insert(["name" => "compliance", "label" => "Compliance", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_approve_deposit", "label" => "Approve Deposit", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_approve_verification", "label" => "Approve Verification", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_show_reports", "label" => "Show Reports", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_edit_reports", "label" => "Edit Reports", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_show_customers", "label" => "Show Customers", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_show_pending", "label" => "Show Pending", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_show_alerts", "label" => "Show Alerts", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_approve_deposit", "label" => "Approve Deposit", "department_id" => 2]);
//        DB::table("permissions")->insert(["name" => "compliance_show_settings", "label" => "Show Settings", "department_id" => 2]);

        DB::table("permissions")->insert(["name" => "compliance", "label" => "Compliance", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_approve_deposit", "label" => "Approve Deposit", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_approve_verification", "label" => "Approve Verification", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_show_reports", "label" => "Show Reports", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_edit_reports", "label" => "Edit Reports", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_show_customers", "label" => "Show Customers", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_show_pending", "label" => "Show Pending", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_show_alerts", "label" => "Show Alerts", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_approve_deposit", "label" => "Approve Deposit", "department_id" => 3]);
        DB::table("permissions")->insert(["name" => "compliance_show_settings", "label" => "Show Settings", "department_id" => 3]);


//        DB::table("permissions")->insert(["name" => "management", "label" => "Management", "department_id" => 4]);
//        DB::table("permissions")->insert(["name" => "management_show_deposits", "label" => "Show Deposits", "department_id" => 4]);
//        DB::table("permissions")->insert(["name" => "management_edit_deposits", "label" => "Edit Deposits", "department_id" => 4]);
//        DB::table("permissions")->insert(["name" => "management_show_withdrawals", "label" => "Show Withdrawals", "department_id" => 4]);
//        DB::table("permissions")->insert(["name" => "management_edit_withdrawals", "label" => "Edit Withdrawals", "department_id" => 4]);
//        DB::table("permissions")->insert(["name" => "management_export", "label" => "Export Reports", "department_id" => 4]);
//
//        DB::table("permissions")->insert(["name" => "support", "label" => "Support", "department_id" => 5]);
//        DB::table("permissions")->insert(["name" => "admin", "label" => "Admin", "department_id" => 1]);
//
//        DB::table("permissions")->insert(["name" => "jackpot", "label" => "Jackpot", "department_id" => 9]);
//        DB::table("permissions")->insert(["name" => "scoreboard", "label" => "Scoreboard", "department_id" => 9]);
    }
}
