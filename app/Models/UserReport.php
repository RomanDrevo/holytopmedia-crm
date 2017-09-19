<?php

namespace App\Models;


use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    protected $fillable = ["user_id", "first_comment_count", "documents_count", "month", "year"];

    public static function addFirstCommentToUser(){
        $reports = (new static)->where('user_id', \Auth::id())->where('month', Carbon::now()->month)->where('year', Carbon::now()->year)->first();
        if(is_null($reports)){
            $reports = (new static)->create([
                "user_id" => \Auth::id(),
                "first_comment_count" => 0,
                "documents_count" => 0,
                "month" => Carbon::now()->month,
                "year" => Carbon::now()->year
            ]);
        }
        $reports->first_comment_count = $reports->first_comment_count + 1;
        $reports->save();

        return $reports;
    }
}
