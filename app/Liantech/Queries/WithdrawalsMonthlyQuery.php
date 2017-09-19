<?php
namespace App\Liantech\Queries;

use App\Models\Withdrawal;
use Carbon\Carbon;

class WithdrawalsMonthlyQuery
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
        return Withdrawal::byBroker()->with('customer', 'employee', 'splits', 'notes', 'table')
            ->where("confirmTime", ">=", Carbon::now()->startOfMonth())
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where('status', 'approved')
            ->orderBy("id", "desc");
    }
}