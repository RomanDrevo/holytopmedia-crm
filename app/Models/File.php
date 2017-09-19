<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ["*"];

    public function customer()
    {
    	return $this->belongsTo(Customer::class);
    }

    public function comments()
    {
    	return $this->hasMany(FileComment::class);
    }
}
