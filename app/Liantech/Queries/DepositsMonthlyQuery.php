<?php


namespace App\Liantech\Queries;
use App\Models\Deposit;
use Carbon\Carbon;

class DepositsMonthlyQuery
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

        return Deposit::byBroker()->with('customer', 'employee', 'splits', 'notes', 'table')
            ->where("confirmTime", ">=", Carbon::now()->startOfMonth())
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where("receptionEmployeeId", ">", 0)
            ->orderBy("id", "desc");
    }
}