<?php
namespace App\Liantech\Helpers;

use App\Models\Department;
use App\User;


/**
* Calculate and generate all the reports for compliance and risk departments
*/
class ComplianceRiskReports
{
    public function getUsersWithStats()
    {

        $departmentsIds = Department::where("name", "compliance")->orWhere("name", "development")->pluck("id")->toArray();

        $users = User::with("department", "comments")->get();

        $users = $users->filter(function($user) use ($departmentsIds){
            return in_array($user->department->id, $departmentsIds);
        });

//        dd($users);
        return $users;
    }
}