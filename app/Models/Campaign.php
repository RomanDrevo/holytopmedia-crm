<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $fillable = ["name", "total_deposits", "broker_id", "campaign_crm_id", "create_date"];

    public $timestamps = false;

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function scopeByBroker($query)
    {
        return $query->where('broker_id', \Auth::user()->broker_id);
    }
}
