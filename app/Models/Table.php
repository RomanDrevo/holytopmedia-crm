<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Table extends Model
{
    protected $fillable = ["*"];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function scopeByBroker($query)
    {
        return $query->where('broker_id', \Auth::user()->broker_id);
    }

    public function manager()
    {
        return $this->hasOne(User::class, 'id', 'manager_id');
    }

    public function assignEmployees($employees = [])
    {
        foreach ($employees as $employeeId) {

            $employee = Employee::find($employeeId);

            $employee->table_id = $this->id;
            $employee->save();
        }
    }

}
