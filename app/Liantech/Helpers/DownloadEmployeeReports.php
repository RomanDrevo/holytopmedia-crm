<?php

namespace App\Liantech\Helpers;

use Illuminate\Http\Request;
use App\Liantech\Queries\FilterWithdrawalQuery;
use App\Models\Deposit;
use App\Models\Employee;
use Config;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Liantech\Queries\FilterDepositQuery;

class DownloadEmployeeReports
{
    public $currencies = null;

    public function __construct()
    {
        return $this->currencies = Config::get('liantech.currencies_symbols');
    }

    public function monthlyEmployeeDepositsAndWithdrawals(Request $request)
    {
        if (!isset($request->csv_employee_id) || empty($request->csv_employee_id))
            return redirect()->back()->with('error', 'No employee selected');

        $employee = Employee::where('id', $request->csv_employee_id)->first();

        Excel::create(date('d-m-y') . '_employee-export', function ($excel) use ($employee) {
            $excel->setTitle($employee->name . " Deposits");
            $excel->sheet($employee->name . " Deposits", function ($sheet) use ($employee) {

                $thisMonth = Carbon::now()->startOfMonth();

                $allDeposits = array();

                $deposits = Deposit::with('employee', 'splits')
                    ->where("receptionEmployeeId", $employee->employee_crm_id)
                    ->where("assigned_at", ">=", $thisMonth)
                    ->orderBy("id", "desc")
                    ->get();

                foreach ($deposits as $deposit) {
                    $notes = (new FilterDepositQuery)->buildDepositNotesList($deposit);

                    $assign_time = is_null($deposit->assigned_at) ? '' : $deposit->assigned_at;
                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customerId,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Amount USD' => number_format($deposit->amountUSD),
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->clearedBy,
                        'Is Verified' => $deposit->is_verified ? "YES" : "NO",
                        'Confirm Time' => is_string($assign_time) ? $assign_time : $assign_time->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);
                }

                $notes = '';
                $index = 0;
                foreach ($employee->monthlySplits as $split) {
                    ++$index;
                    $deposit = $split->deposit;
                    $notes .= $index . ". Split for " . $this->currencies[$deposit->currency] . number_format($split->amount) . " from " . $split->deposit->employee->name . " ";

                    $assign_time = is_null($deposit->assigned_at) ? '' : $deposit->assigned_at;
                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customer_id,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Amount USD' => number_format($deposit->amountUSD),
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->cleared_by,
                        'Is Verified' => $deposit->is_verified ? "YES" : "NO",
                        'Confirm Time' => is_string($assign_time) ? $assign_time : $assign_time->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);

                }

                $sheet->fromArray($allDeposits);

            });
            $excel->sheet($employee->name . " Withdrawals", function ($sheet) use ($employee) {

                $thisMonth = Carbon::now()->startOfMonth();

                $allWithdrawals = array();

                $withdrawals = Withdrawal::with('employee', 'splits')
                    ->where("receptionEmployeeId", $employee->id)
                    ->where('approved', true)
                    ->where("confirmTime", ">=", $thisMonth)
                    ->orderBy("id", "desc")
                    ->get();

                foreach ($withdrawals as $withdrawal) {

                    $notes = (new FilterWithdrawalQuery)->buildWithdrawalNotesList($withdrawal);
                    $oneWithdrawal = array(
                        'ID' => $withdrawal->id,
                        'Customer ID' => $withdrawal->customerId,
                        'Amount' => number_format($withdrawal->amount),
                        'currency' => $withdrawal->currency,
                        'Amount USD' => number_format($withdrawal->amountUSD),
                        'Payment Method' => $withdrawal->paymentMethod,
                        'Cleared By' => $withdrawal->clearedBy,
                        'Is Verified' => $withdrawal->is_verified ? "YES" : "NO",
                        'Confirm Time' => $withdrawal->confirmTime->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allWithdrawals, $oneWithdrawal);
                }

                $sheet->fromArray($allWithdrawals);

            });


        })->export('xls');
    }

    public function dailyEmployeeDepositsAndWithdrawals(Request $request)
    {
        if (!isset($request->csv_employee_id) || empty($request->csv_employee_id))
            return redirect()->back()->with('error', 'No employee selected');

        $employee = Employee::where('id', $request->csv_employee_id)->first();

        Excel::create(date('d-m-y') . '_employee-export', function ($excel) use ($employee) {
            $excel->setTitle($employee->name . " Deposits");
            $excel->sheet($employee->name . " Deposits", function ($sheet) use ($employee) {

                $thisDay = Carbon::now()->startOfDay();

                $allDeposits = array();

                $deposits = Deposit::with('employee', 'splits')
                    ->where("receptionEmployeeId", $employee->employee_crm_id)
                    ->where("assigned_at", ">=", $thisDay)
                    ->orderBy("id", "desc")
                    ->get();

                foreach ($deposits as $deposit) {

                    $notes = (new FilterDepositQuery)->buildDepositNotesList($deposit);
                    $assign_time = is_null($deposit->assigned_at) ? '' : $deposit->assigned_at;
                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customerId,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Amount USD' => number_format($deposit->amountUSD),
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->clearedBy,
                        'Is Verified' => $deposit->is_verified ? "YES" : "NO",
                        'Confirm Time' => is_string($assign_time) ? $assign_time : $assign_time->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);
                }

                $notes = '';
                $index = 0;
                foreach ($employee->monthlySplits as $split) {
                    ++$index;
                    $deposit = $split->deposit;

                    $notes .= $index . ". Split for " . $this->currencies[$deposit->currency] . number_format($split->amount) . " from " . $split->deposit->employee->name . " ";

                    $assign_time = is_null($deposit->assigned_at) ? '' : $deposit->assigned_at;
                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customer_id,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Amount USD' => number_format($deposit->amountUSD),
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->cleared_by,
                        'Is Verified' => $deposit->is_verified ? "YES" : "NO",
                        'Confirm Time' => is_string($assign_time) ? $assign_time : $assign_time->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);

                }

                $sheet->fromArray($allDeposits);

            });
            $excel->sheet($employee->name . " Withdrawals", function ($sheet) use ($employee) {

                $thisDay = Carbon::now()->startOfDay();

                $allWithdrawals = array();

                $withdrawals = Withdrawal::with('employee', 'splits')
                    ->where("receptionEmployeeId", $employee->id)
                    ->where('approved', true)
                    ->where("confirmTime", ">=", $thisDay)
                    ->orderBy("id", "desc")
                    ->get();

                foreach ($withdrawals as $withdrawal) {
                    $notes = (new FilterWithdrawalQuery)->buildWithdrawalNotesList($withdrawal);
                    $oneWithdrawal = array(
                        'ID' => $withdrawal->id,
                        'Customer ID' => $withdrawal->customerId,
                        'Amount' => number_format($withdrawal->amount),
                        'currency' => $withdrawal->currency,
                        'Amount USD' => number_format($withdrawal->amountUSD),
                        'Payment Method' => $withdrawal->paymentMethod,
                        'Cleared By' => $withdrawal->clearedBy,
                        'Is Verified' => $withdrawal->is_verified ? "YES" : "NO",
                        'Confirm Time' => $withdrawal->confirmTime->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allWithdrawals, $oneWithdrawal);
                }

                $sheet->fromArray($allWithdrawals);

            });


        })->export('xls');
    }


    public function downloadEmployeeDeposits(Request $request)
    {
        if (!isset($request->csv_employee_id) || empty($request->csv_employee_id))
            return redirect()->back()->with('error', 'No employee selected');


        $employee = Employee::where('employee_crm_id', $request->csv_employee_id)->first();
        Excel::create(date('d-m-y') . '_employee-export', function ($excel) use ($employee) {
            $excel->setTitle($employee->name . " Deposits");
            $excel->sheet($employee->name . " Deposits", function ($sheet) use ($employee) {

                $thisMonth = Carbon::now()->startOfMonth();

                $allDeposits = array();

                $deposits = Deposit::with('employee', 'splits')
                    ->where("receptionEmployeeId", $employee->employee_crm_id)
                    ->where("assigned_at", ">=", $thisMonth)
                    ->orderBy("id", "desc")
                    ->get();

                foreach ($deposits as $deposit) {

                    $notes = (new FilterDepositQuery)->buildDepositNotesList($deposit);

                    $assign_time = is_null($deposit->assigned_at) ? '' : $deposit->assigned_at;
                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customerId,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Amount USD' => number_format($deposit->amountUSD),
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->clearedBy,
                        'Is Verified' => $deposit->is_verified ? "YES" : "NO",
                        'Confirm Time' => is_string($assign_time) ? $assign_time : $assign_time->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);
                }

                $notes = '';
                $index = 0;
                foreach ($employee->monthlySplits as $split) {
                    ++$index;
                    $deposit = $split->deposit;

                    $notes .= $index . ". Split for " . $this->currencies[$deposit->currency] . number_format($split->amount) . " from " . $split->deposit->employee->name . " ";

                    $assign_time = is_null($deposit->assigned_at) ? '' : $deposit->assigned_at;
                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customer_id,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Amount USD' => number_format($deposit->amountUSD),
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->cleared_by,
                        'Is Verified' => $deposit->is_verified ? "YES" : "NO",
                        'Confirm Time' => is_string($assign_time) ? $assign_time : $assign_time->format('m-d-Y H:i:s'),
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);

                }

                $sheet->fromArray($allDeposits);

            });

        })->export('xls');
    }
}