<?php

namespace App\Liantech\Repositories;

use App\Deposit;
use App\Employee;
use App\EmployeeStats;
use App\Goal;
use App\Setting;
use App\Withdrawal;
use Carbon\Carbon;
use DB;
/**
* 	Get required statistics for the playground notification
*/
class StatsRepository
{
	public function dailyFtdCount()
    {
        return Deposit::where("confirm_time", ">=", Carbon::today())->where("type", 1)->where("employee_id", "!=", 0)->count();
    }

    public function monthlyFtdCount()
    {
        return Deposit::where("confirm_time", ">=", Carbon::now()->startOfMonth())->where("type", 1)->where("employee_id", "!=", 0)->count();
    }

    public function todayTotalDeposits()
    {
        return Deposit::where("confirm_time", ">=", Carbon::today())->sum('usd_amount');
    }

    public function monthlyTotalDeposits()
    {
        return Deposit::where("confirm_time", ">=", Carbon::now()->startOfMonth())->sum('usd_amount');
    }
}






