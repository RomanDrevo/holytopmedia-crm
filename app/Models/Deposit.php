<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class Deposit extends Model
{
    protected $fillable = ["broker_id", "deposits_crm_id", "table_id", "customerId", "paymentMethod", "clearedBy", "receptionEmployeeId", "processEmployeeId", "confirmEmployeeId", "amount", "currency", "rateUSD", "amountUSD", "transactionID", "campaignId", "cancelReason", "IPAddress", "requestTime", "confirmTime", "assigned_at", "type", "status", "is_verified", "deposit_type", "is_split", "split_id"];

    protected $dates = ["confirmTime", "assigned_at"];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'receptionEmployeeId', 'employee_crm_id')->ByBroker();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerId', 'customer_crm_id')->ByBroker();
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaignId', 'campaign_crm_id')->byBroker();
    }

    public function splits()
    {
        return $this->hasMany(Split::class);
    }

    public function scopeByBroker($query, $broker_id = 1)
    {
        $broker_id = (\Auth::check()) ? \Auth::user()->broker_id : $broker_id;

        return $query->where('broker_id', $broker_id);
    }

    public function notes()
    {
        return $this->hasMany(DepositNote::class);
    }

    public function scopeDateBetween($query, $start, $end)
    {
        return $query->whereBetween('assigned_at', [$start, $end]);
    }

    public function isSalesApproved($processorsStr = "")
    {
        //Get the processors string from the database
        //and split it by "|"
        if($processorsStr == ""){
            $processorsStr = Setting::where('option_name', 'auto_approved_processors')->pluck("option_value")->first();
        }
        $processors = explode("|", $processorsStr);

        //remove all empty values
        $processors = array_filter($processors);

        //trim spaces
        $processors = array_map('trim', $processors);

        return in_array( $this->clearedBy, $processors );
    }
}
