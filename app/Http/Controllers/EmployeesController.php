<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\GetGoalForTableRequest;
use App\Http\Requests\UpdateEmployeeGoalRequest;
use App\Liantech\Classes\Pusher;
use App\Models\Employee;
use App\Models\Goal;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{

    /**
     * Update the goal for a specific employee
     * 
     * @param  Request $request 
     * @return Redirect
     */
    public function updateGoal(UpdateEmployeeGoalRequest $request)
    {
        $goal = Goal::where("employee_id", $request->employee_id)->where("table_id", $request->table_id)->first();
        if( is_null($goal)){
            $goal = new Goal;
            $goal->employee_id = $request->employee_id;
            $goal->table_id = $request->table_id;
        }

        $goal->daily = $request->daily;
        $goal->monthly = $request->monthly;
        $goal->save();

        return response("ok", 200);
    }

    public function getGoalForTable(GetGoalForTableRequest $request){
        $goal = Goal::where("employee_id", $request->employee_id)->where("table_id", $request->table_id)->first();

        return response([
            "daily" =>  is_null($goal) ? "" : $goal->daily,
            "monthly" =>  is_null($goal) ? "" : $goal->monthly,
        ], 200);
    }


    /**
     * Update an employee name
     * 
     * @param  Request $request 
     * @return Redirect
     */
    public function updateName(Request $request)
    {
        if( isset($request->name) && isset($request->employee_id) && !empty($request->name)){
            $employee = Employee::find($request->employee_id);
            if( is_null($employee) )
                return response("error", 403);

            $employee->name = $request->name;
            $employee->save();

            return response("ok", 200);
        }
        return response("error", 403);
    }


    /**
     * Notify the scoreboard to get fresh data from the server
     * 
     * @param  boolean $isUpdated
     */
    private function notifyScoreboard($isUpdated = false)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY', ''), 
            env('PUSHER_APP_SECRET', ''), 
            env('PUSHER_APP_ID', '')
        );

        $pusher->trigger('scoreboard_channel', 'needs_to_update', ["is_updated" => $isUpdated] );
    }
}
