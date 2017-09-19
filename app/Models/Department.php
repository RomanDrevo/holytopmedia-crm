<?php

namespace App\Models;

use App\Permission;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
