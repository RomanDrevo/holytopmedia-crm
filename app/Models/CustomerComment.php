<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CustomerComment extends Model
{
    protected $fillable = ["*"];

    public function customer()
    {
    	return $this->belongsTo(Customer::class);
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
