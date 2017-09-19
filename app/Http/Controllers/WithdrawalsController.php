<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateWithdrawalSplitRequest;
use App\Http\Requests\RemoveWithdrawalSplitRequest;
use App\Models\Split;
use App\Models\Withdrawal;
use App\Models\Table;
use App\Models\Employee;
use App\Models\WithdrawalNote;
use App\Models\WithdrawalSplit;
use Illuminate\Http\Request;
use Gate;

class WithdrawalsController extends Controller
{
    /**
     * Each withdrawal has a table associated with it when the
     * employee get assigned to this withdrawal.
     *
     * @param  Request $request
     * @return String
     */
    public function assignWithdrawalToTable(Request $request)
    {
        if (isset($request->withdrawal_id) && isset($request->table_id) && isset($request->employee_id)) {

            $table = Table::find($request->table_id);
            $withdrawal = Withdrawal::find($request->withdrawal_id);
            if (is_null($withdrawal) || !is_numeric($request->employee_id))
                return "error";

            $withdrawal->table_id = $request->table_id;
            $withdrawal->withdrawal_type = $table->type;
            $withdrawal->save();
            if ($withdrawal->table) {
                return $withdrawal;
            }
        }

        return "error";
    }

    /**
     * Create a split for a withdrawal between 2 employees
     *
     * @param  Request $request
     * @return mixed
     */
    public function createNewWithdrawalSplit(CreateWithdrawalSplitRequest $request)
    {
        $withdrawal = Withdrawal::find($request->withdrawal_id);

        $sum = 0;
        if($withdrawal->splits){
            $sum = $withdrawal->splits->sum("amount");
        }

        if(($sum + intval($request->split_amount)) > $withdrawal->amount){
            return response([
                ["Total splits amount is greater then the withdrawal amount!"]
            ], 403);
        }


        $split = WithdrawalSplit::create([
            "withdrawal_id" => $withdrawal->id,
            "to" => $request->split_employee_id,
            "amount" => $request->split_amount
        ]);

        if (!$withdrawal->split) {
            $withdrawal->is_split = true;
            $withdrawal->save();
        }

        return response([
            "split" => $split->load('toEmployee')
        ], 200);
    }

    /**
     * Remove an existing withdrawal split from the DB
     *
     * @param  Request $request
     * @return String
     */
    public function removeWithdrawalSplit(RemoveWithdrawalSplitRequest $request)
    {


        $withdrawal = Withdrawal::find($request->withdrawal_id);
        foreach ($withdrawal->splits as $split) {
            if($split->id == $request->split_id){
                $split->delete();
                break;
            }
        }
        if( !$withdrawal->splits->count()){
            $withdrawal->is_split = false;
            $withdrawal->save();
        }

        $withdrawal->load("splits.toEmployee");
        return response([
            "splits"    => $withdrawal->splits,
            "is_split"  => $withdrawal->is_split
        ], 200);

    }

    /**
     * Assign a new or existing withdrawal to an employee
     * if not already assigned automaticaly
     *
     * @param  Request $request
     * @return String/JSON
     */
    public function assignWithdrawalToEmployee(Request $request)
    {
        if (isset($request->withdrawal_id) && isset($request->employee_id)) {

            if(Gate::allows("sales_edit_withdrawals")){
                $withdrawal = Withdrawal::find($request->withdrawal_id);

                $employee = Employee::find($request->employee_id);
                if (is_null($withdrawal) || is_null($employee))
                    return "error";

                $withdrawal->receptionEmployeeId = $employee->employee_crm_id;
                $withdrawal->save();
                if ($withdrawal->employee) {
                    return $withdrawal;
                }
            }
            else{
                return response([
                    "message" => "You don't have permission to edit withdrawals!"
                ], 403);
            }
        }

        return "error";
    }

    /**
     * Assign the withdrawal to one of the existing types
     * (FTD / RST)
     *
     * @param  Request $request
     * @return String
     */
    public function assignWithdrawalToType(Request $request)
    {
        if (isset($request->withdrawal_id) && isset($request->type)) {

            $withdrawal = Withdrawal::find($request->withdrawal_id);
            if (is_null($withdrawal))
                return "error";

            $withdrawal->withdrawal_type = $request->type;
            $withdrawal->save();

            return $withdrawal;
        }

        return "error";
    }

    /**
     * Update the withdrawal verification status
     * (YES / NO)
     *
     * @param  Request $request
     * @return String
     */
    public function UpdateWithdrawalVerificationStatus(Request $request)
    {
        if (isset($request->withdrawal_id) && isset($request->is_verified)) {

            // get the chosen withdrawal to update
            // the right status
            $withdrawal = Withdrawal::find($request->withdrawal_id);
            if (is_null($withdrawal))
                return "error";

            $withdrawal->is_verified = $request->is_verified;
            $withdrawal->save();

            return "ok";
        }

        return "error";
    }


    public function addWithdrawalNote(Request $request)
    {
        if (isset($request->withdrawal_id) && isset($request->withdrawal_note) && $request->withdrawal_note) {
            $note = WithdrawalNote::create([
                'user_id' => \Auth::user()->id,
                'withdrawal_id' => $request->withdrawal_id,
                'content' => $request->withdrawal_note
            ]);
            return $note;
        }
        return 'Missing parameters';
    }
}
