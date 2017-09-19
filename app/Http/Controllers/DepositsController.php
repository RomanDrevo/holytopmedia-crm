<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Http\Requests\CreateSplitRequest;
use App\Http\Requests\RemoveSplitRequest;
use App\Liantech\Classes\Pusher;
use App\Models\Deposit;
use App\Models\DepositNote;
use App\Models\Employee;
use App\Models\Split;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gate;

class DepositsController extends Controller
{
    /**
     * Each deposit has a table associated with it when the
     * employee get assigned to this deposit.
     *
     * @param  Request $request
     * @return String
     */
    public function assignDepositToTable(Request $request)
    {
        if (isset($request->deposit_id) && isset($request->table_id) && isset($request->employee_id)) {
            $table = Table::find($request->table_id);
            // get the chosen deposit to update
            // the right type
            $deposit = Deposit::find($request->deposit_id);
            if (is_null($deposit) || !is_numeric($request->employee_id))
                return "error";

            $deposit->table_id = intval($request->table_id);
            $deposit->deposit_type = $table->type;
            $deposit->save();
            if ($deposit->table) {
                return $deposit;
            }

        }

        return "error";
    }

    /**
     * Create a split for a deposit between 2 employees
     * @param  Request $request
     * @return mixed
     *   */
    public function createNewSplit(CreateSplitRequest $request)
    {
        $deposit = Deposit::find($request->deposit_id);

        $sum = 0;
        if($deposit->splits){
            $sum = $deposit->splits->sum("amount");
        }

        if(($sum + intval($request->split_amount)) > $deposit->amount){
            return response([
                ["Total splits amount is greater then the deposit amount!"]
            ], 403);
        }

        $split = Split::create([
            "deposit_id"    =>  $deposit->id,
            "to"            =>  $request->split_employee_id,
            "amount"        =>  $request->split_amount
        ]);

        if(!$deposit->is_split){
            $deposit->is_split = true;
            $deposit->save();
        }

        return response([
            "split"     =>  $split->load('toEmployee')
        ], 200);
    }

    /**
     * Remove an existing split from the DB
     *
     * @param  Request $request
     * @return String
     */
    public function removeSplit(RemoveSplitRequest $request)
    {


        $deposit = Deposit::find($request->deposit_id);
        foreach ($deposit->splits as $split) {
            if($split->id == $request->split_id){
                $split->delete();
                break;
            }
        }

        if( !$deposit->splits->count()){
            $deposit->is_split = false;
            $deposit->save();
        }

        $deposit->load("splits.toEmployee");

        $this->notifyScoreboard(true);
        return response([
            "splits"    => $deposit->splits,
            "is_split"  => $deposit->is_split
        ], 200);
    }

    /**
     * Assign a new or existing deposit to an employee
     * if not already assigned automaticaly
     *
     * @param  Request $request
     * @return String/JSON
     */
    public function assignDepositToEmployee(Request $request)
    {
        if (isset($request->deposit_id) && isset($request->employee_id)) {

            // get the chosen deposit to update
            // the right worker

            if(Gate::allows("sales_edit_deposits")){
                $deposit = Deposit::find($request->deposit_id);
                $employee = Employee::find($request->employee_id);
                if (is_null($deposit) || is_null($employee))
                    return "error";

                $deposit->receptionEmployeeId = $employee->employee_crm_id;

                $table = $deposit->receptionEmployeeId ? $deposit->employee->table : null;

                if ($table) {
                    $deposit->table_id = $table->type;
                } else {
                    $deposit->table_id = null;
                }

                $deposit->save();
                if ($deposit->employee) {
                    return $deposit;
                }
            }else{
                return response([
                    "message" => "You don't have permission to edit deposits!"
                ], 403);

            }
        }

        return "error";
    }

    /**
     * Assign the deposit to one of the existing types
     * (FTD / RST)
     *
     * @param  Request $request
     * @return String
     */
    public function assignDepositToType(Request $request)
    {
        if (isset($request->deposit_id) && isset($request->type)) {

            // get the chosen deposit to update
            // the right type
            $deposit = Deposit::find($request->deposit_id);
            if (is_null($deposit))
                return "error";

            $deposit->deposit_type = $request->type;
            $deposit->save();

            return $deposit;
        }

        return "error";
    }



    public function assignDepositToTime(Request $request)
    {
        $today = Carbon::now()->startOfDay();
        if (isset($request->deposit_id) && isset($request->assigned_at)) {

            $deposit = Deposit::find($request->deposit_id);
            if (is_null($deposit))
                return "error";

            $date = new Carbon($request->assigned_at);
            if($date > Carbon::now()) {
                return response([
                    "status"    => 'error',
                    'message' => "You're trying to assign deposit to future date!"
                ], 500);
            }
            $date->setTimezone('UTC');

            if($date >= $today) {
                $assigned_at = Carbon::now();
            } else {
                $assigned_at = Carbon::parse($date)->format("Y-m-d H:m:s");
                $assigned_at = new Carbon($this->str_replace_first('00:', '10:', $assigned_at));
            }
            $deposit->assigned_at = new Carbon($assigned_at);
            $deposit->save();

            return $assigned_at;

        }

        return "error";
    }
    private function str_replace_first($from, $to, $subject)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $subject, 1);
    }


    /**
     * Update the deposit verification status
     * (YES / NO)
     *
     * @param  Request $request
     * @return String
     */
    public function UpdateDepositVerificationStatus(Request $request)
    {
        if (isset($request->deposit_id) && isset($request->is_verified)) {

            // get the chosen deposit to update
            // the right status
            $deposit = Deposit::find($request->deposit_id);
            if (is_null($deposit))
                return "error";

            $deposit->is_verified = $request->is_verified;
            $deposit->save();

            return "ok";
        }

        return "error";
    }

    public function addDepositNote(Request $request)
    {
        if(isset($request->deposit_id) && isset($request->deposit_note) && $request->deposit_note) {
            $note = DepositNote::create([
                'user_id' => \Auth::user()->id,
                'deposit_id' => $request->deposit_id,
                'content' => $request->deposit_note
            ]);
            return $note;
        }
        return 'Missing parameters';

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

        $pusher->trigger('scoreboard_channel', 'needs_to_update', ["is_updated" => $isUpdated]);
    }

//    public function getDepositInfo($id){
//        $deposit = Deposit::byBroker()->where('id', $id)->first;
//        return $deposit;
//    }
}

