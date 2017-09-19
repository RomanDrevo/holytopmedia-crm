<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    protected $fillable = ["*"];

    public function goals()
    {
    	return $this->hasMany(Goal::class);
    }

    public function goal()
    {
        return $this->hasOne(Goal::class)->where("table_id", $this->table_id);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'receptionEmployeeId', 'employee_crm_id')->ByBroker($this->broker_id)->where("paymentMethod", "!=", "Bonus");
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'receptionEmployeeId', 'employee_crm_id')->ByBroker($this->broker_id)->where("paymentMethod", "!=", "Bonus");
    }

    public function todayDeposits()
    {
    	$today = Carbon::today();

    	return $this->deposits()->where('confirmTime', '>=', $today->format('Y-m-d H:i:s'));
    }

    public function monthlyDeposits()
    {
        $today = Carbon::today()->startOfMonth();

        return $this->deposits()->where('confirmTime', '>=', $today->format('Y-m-d H:i:s'));
    }

    public function todayWithdrawals()
    {
        $today = Carbon::today();

        return $this->withdrawals()->where('confirmTime', '>=', $today->format('Y-m-d H:i:s'));
    }

    public function monthlyWithdrawals()
    {
        $thisMonth = Carbon::now()->startOfMonth();

        return $this->withdrawals()->where('confirmTime', '>=', $thisMonth->format('Y-m-d H:i:s'));
    }

    public function todaySplits()
    {
        $today = Carbon::today();

        return $this->splitsToMe()->where('created_at', '>=', $today->format('Y-m-d H:i:s'));
    }

    public function monthlySplits()
    {
        $thisMonth = Carbon::now()->startOfMonth();

        return $this->splitsToMe()->where('created_at', '>=', $thisMonth->format('Y-m-d H:i:s'));
    }

    public function todayWithdrawalSplits()
    {
        $today = Carbon::today();

        return $this->withdrawalSplitsToMe()->where('created_at', '>=', $today->format('Y-m-d H:i:s'));
    }

    public function monthlyWithdrawalSplits()
    {
        $thisMonth = Carbon::now()->startOfMonth();

        return $this->withdrawalSplitsToMe()->where('created_at', '>=', $thisMonth->format('Y-m-d H:i:s'));
    }

    public function yesterdayDeposits()
    {
        $yesterday = Carbon::yesterday();
        $today = Carbon::today();

        return $this->deposits()
            ->where('confirm_time', '>=', $yesterday->format('Y-m-d H:i:s'))
            ->where('confirm_time', '<', $today->format('Y-m-d H:i:s'));
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function splitsToMe()
    {
        return $this->hasMany(Split::class, 'to', 'id');
    }

    public function splitsFromMe()
    {
        return $this->hasMany(Split::class, "id", "from_employee_id");
    }

    public function withdrawalSplitsToMe()
    {
        return $this->hasMany(WithdrawalSplit::class);
    }

    public function withdrawalSplitsFromMe()
    {
        return $this->hasMany(WithdrawalSplit::class, "id", "from_employee_id");
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByBroker($query, $broker_id = 1)
    {
        $broker_id = (\Auth::check()) ? \Auth::user()->broker_id : $broker_id;

        return $query->where('broker_id', $broker_id);
    }
}
