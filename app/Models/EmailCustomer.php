<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmailCustomer extends Model
{
    protected $fillable = ["*"];

    public function list()
    {
        return $this->belongsTo(EmailList::class);
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('h:i A');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('h:i A');
    }
}
