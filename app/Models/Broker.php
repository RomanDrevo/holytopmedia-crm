<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Broker extends Model
{
    protected $fillable = ["*"];

    public function lists()
    {
        return $this->hasMany(EmailList::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);

    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
