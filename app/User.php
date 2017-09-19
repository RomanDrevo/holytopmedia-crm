<?php

namespace App;

use App\Liantech\Helpers\ComplianceEmployeesCalculator;
use App\Models\CustomerComment;
use App\Models\Department;
use App\Models\Table;
use App\Models\UserReport;
use App\Models\WithdrawalNote;
use Carbon\Carbon;
use App\Models\Broker;
use App\Models\DepositNote;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'department_id', 'broker_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function comments()
    {
        return $this->hasMany(CustomerComment::class);
    }

    public function monthlyReports()
    {
        return $this->hasOne(UserReport::class)->where('month', Carbon::now()->month)->where('year', Carbon::now()->year);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function scopeByBroker($query)
    {
        return $query->where('broker_id', \Auth::user()->broker_id);
    }

    public function complianceCalc()
    {
        return new ComplianceEmployeesCalculator($this);
    }

    public function notes()
    {
        return $this->hasMany(DepositNote::class);
    }

    public function withdrawal_note()
    {
        return $this->hasMany(WithdrawalNote::class);
    }

    public function ownTable()
    {
        return $this->hasOne(Table::class, 'manager_id', 'id');
    }
}
