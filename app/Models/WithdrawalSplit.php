<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalSplit extends Model
{
    protected $fillable = ["withdrawal_id", "to", "amount"];

    protected $table = "withdrawal_splits";

    public function withdrawal()
    {
    	return $this->hasOne(Withdrawal::class);
    }

    public function toEmployee()
    {
        return $this->belongsTo(Employee::class, 'to', 'id');
    }

}
