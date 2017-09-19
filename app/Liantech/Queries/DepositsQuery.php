<?php

namespace App\Liantech\Queries;

use App\Models\Deposit;

/**
* Get all deposits Query
*/
class DepositsQuery
{

	public static function get($field = 'id')
	{
		return self::buildQuery($field)->paginate(25);
	}

    public static function getNoPagination($field = 'id')
    {
        return self::buildQuery($field)->get();
    }

    public static function buildQuery($field = 'id')
    {
        $q = \Request::has("query") ? \Request::get("query") : "";

        $field = \Request::has("field") ? \Request::get("field") : "id";

        $employee_id = ( \Request::has("employee_id") && is_numeric(\Request::get("employee_id")) ) ? \Request::get("employee_id") : "";
        return Deposit::with('customer', 'employee', 'splits.toEmployee', 'notes', 'table')
            ->ByBroker()
            ->where(function($table) use ($employee_id){
                if(!empty($employee_id))
                    $table->where("receptionEmployeeId", $employee_id);
            })
            ->where(function($table) use ($field, $q){
                $table->where($field, "LIKE", "%$q%");
            })
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])

            ->orderBy("id", "desc");
    }
}