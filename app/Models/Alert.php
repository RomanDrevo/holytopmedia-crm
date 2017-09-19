<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Alert extends Model
{
    protected $fillable = ["type", "subject", "content", "broker_id", "customer_crm_id"];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function scopeByBroker($query)
    {
        return $query->where('broker_id', \Auth::user()->broker_id);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_crm_id', 'customer_crm_id');
    }

    public static function getKeywords()
    {
        $keywords = DB::table('settings')->select('option_value')->where('option_name', 'alerts_keywords')->first();
        $keywords = explode('|', $keywords->option_value);
        foreach ($keywords as &$keyword) {
           $keyword = trim($keyword);
        }
        return $keywords;
    }
}
