<?php

namespace App\Liantech\Queries;

use App\Models\Withdrawal;

/**
* Get all deposits Query
*/
class WithdrawalsQuery
{
    public static function get()
    {
        return self::buildQuery()->paginate(25);
    }

    public static function getNoPagination()
    {
        return self::buildQuery()->get();
    }

	public static function buildQuery()
	{
        $employee_id = ( \Request::has("employee_id") && is_numeric(\Request::get("employee_id")) ) ? \Request::get("employee_id") : "";
        return Withdrawal::with('customer', 'employee', 'splits.toEmployee', 'notes', 'table')
            ->ByBroker()
            ->where("status", "approved")
            ->where(function($table) use ($employee_id){
                if(!empty($employee_id)) {
                    $table->where("receptionEmployeeId", $employee_id);
                }
            })
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->orderBy("id", "desc");  
	}
}