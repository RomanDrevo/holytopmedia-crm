<?php

namespace App\Liantech\Queries;

use App\Models\Withdrawal;
use Carbon\Carbon;

/**
* Get all deposits Query
*/
class WithdrawalsDailyQuery
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
        return Withdrawal::byBroker()->with('customer', 'employee', 'splits', 'notes', 'table')
            ->where("confirmTime", ">=", Carbon::now()->startOfDay())
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where('status', 'approved')
            ->orderBy("id", "desc"); 
	}
}