<?php

namespace App\Liantech\Queries;

use App\Models\Deposit;
use Carbon\Carbon;

/**
* Get all deposits Query
*/
class DepositsDailyQuery
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

        return Deposit::byBroker()->with('customer', 'employee', 'splits', 'notes', 'table')
            ->where("confirmTime", ">=", Carbon::now()->startOfDay())
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where("receptionEmployeeId", ">", 0)
            ->orderBy("id", "desc");
    }
}