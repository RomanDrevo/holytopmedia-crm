<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Split extends Model
{
	protected $fillable = ["deposit_id", "to", "amount"];

    public function deposit()
    {
    	return $this->belongsTo(Deposit::class);
    }

    public function toEmployee()
    {
    	return $this->belongsTo(Employee::class, 'to', 'id');
    }
}
