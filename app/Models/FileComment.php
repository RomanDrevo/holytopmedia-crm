<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileComment extends Model
{
    protected $fillable = ["*"];

    public function file()
    {
    	return $this->belongsTo(File::class);
    }

    public function user()
    {
    	return belongsTo(User::class);
    }
}
