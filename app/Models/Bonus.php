<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $fillable = ["*"];
	
    protected $dates = ["confirm_time"];

    public function customer()
    {
    	return $this->belongsTo(Customer::class);
    }
}
