<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailList extends Model
{
    protected $table = "lists";

    protected $fillable = ["*"];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function customers()
    {
        return $this->hasMany(EmailCustomer::class, 'list_id');
    }
}
