<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ["broker_id", "customer_crm_id", "FirstName", "LastName", "email", "secondary_email", "Phone", "secondary_phone", "Country", "registrationCountry", "campaignId", "subCampaignId", "regTime", "currency", "employeeInChargeId", "regStatus", "firstDepositDate", "verification", "lastLoginDate", "lastDepositDate", "lastWithdrawalDate"];

    protected $dates = ["regTime", "firstDepositDate", "lastLoginDate", "lastDepositDate", "lastWithdrawalDate"];

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'customerId', 'customer_crm_id')->ByBroker($this->broker_id)->where('paymentMethod', '!=', 'Bonus');
    }

    public function scopeDepositor()
    {
        return $this->firstDepositDate != null;
    }

    public function comments()
    {
        return $this->hasMany(CustomerComment::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'customerId', 'withdrawals_crm_id')->ByBroker($this->broker_id);
    }

    public function bonuses()
    {
        return $this->hasMany(Deposit::class, 'customerId', 'deposits_crm_id')->ByBroker($this->broker_id)->where('paymentMethod', 'Bonus');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function name()
    {
        return $this->FirstName . ' ' . $this->LastName;
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'customer_crm_id', 'customer_crm_id');
    }

    public function scopeByBroker($query, $broker_id = 1)
    {
        $broker_id = (\Auth::check()) ? \Auth::user()->broker_id : $broker_id;

        return $query->where('broker_id', $broker_id);
    }
}
