<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ["withdrawals_crm_id", "broker_id", "table_id", "customerId", "paymentMethod", "confirmationCode", "clearedBy", "clearingUserID", "receptionEmployeeId", "processEmployeeId", "confirmEmployeeId", "amount", "currency", "rateUSD", "amountUSD", "transactionID", "campaignId", "cancelReason", "swiftCode", "comment", "description", "requestTime", "confirmTime", "type", "status", "cancellationTime", "is_verified", "withdrawal_type", "is_split", "split_id", "waitForDocs"];

    protected $dates = ["confirmTime", "requestTime"];

    public function Employee()
    {
    	return $this->belongsTo(Employee::class, 'receptionEmployeeId', 'employee_crm_id')->ByBroker();
    }

    public function deposits()
    {
    	return $this->hasMany(Deposit::class, "customer_id", "customer_id")->ByBroker();
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerId', 'customer_crm_id')->ByBroker();
    }

    public function splits()
    {
        return $this->hasMany(WithdrawalSplit::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function scopeByBroker($query, $broker_id = 1)
    {
        $broker_id = (\Auth::check()) ? \Auth::user()->broker_id : $broker_id;

        return $query->where('broker_id', $broker_id);
    }

    public function notes()
    {
        return $this->hasMany(WithdrawalNote::class);
    }
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaignId', 'campaign_crm_id')->byBroker();
    }
}
