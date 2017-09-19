<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ["*"];

    public function employee()
    {
    	return $this->belongsTo(Employee::class);
    }
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
