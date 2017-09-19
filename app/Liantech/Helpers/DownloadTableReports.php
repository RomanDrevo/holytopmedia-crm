<?php
namespace App\Liantech\Helpers;

use Illuminate\Http\Request;
use App\Liantech\Queries\FilterWithdrawalQuery;
use App\Models\Deposit;
use App\Models\Tab;
use Config;
use App\Models\Withdrawal;
use App\Models\Table;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Liantech\Queries\FilterDepositQuery;


class DownloadTableReports
{

    public function monthlyTableDepositsAndWithdrawals(Request $request)
    {

        if (!isset($request->csv_table_id) || empty($request->csv_table_id))
            return response([
                'Message:' => 'csv_table_id has not been sent to server'
            ], 403);

        $table = Table::where('id', $request->csv_table_id)->first();

        Excel::create(date('d-m-y') . '_table-export', function ($excel) use ($table) {
            $excel->setTitle($table->name . " Deposits");
            $excel->sheet($table->name . " Deposits", function ($sheet) use ($table) {

                $thisMonth = Carbon::now()->startOfMonth();

                $allDeposits = array();

                $deposits = Deposit::with('table', 'splits')
                    ->where("table_id", $table->id)
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


                $sheet->fromArray($allDeposits);

            });
            $excel->sheet($table->name . " Withdrawals", function ($sheet) use ($table) {

                $thisMonth = Carbon::now()->startOfMonth();

                $allWithdrawals = array();

                $withdrawals = Withdrawal::with('table', 'splits')
                    ->where("table_id", $table->id)
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

    public function dailyTableDepositsAndWithdrawals(Request $request)
    {

        if (!isset($request->csv_table_id) || empty($request->csv_table_id))
            return response([
                'Message:' => 'csv_table_id has not been sent to server'
            ], 403);

        $table = Table::where('id', $request->csv_table_id)->first();

        Excel::create(date('d-m-y') . '_table-export', function ($excel) use ($table) {
            $excel->setTitle($table->name . " Deposits");
            $excel->sheet($table->name . " Deposits", function ($sheet) use ($table) {

                $thisDay = Carbon::now()->startOfDay();

                $allDeposits = array();

                $deposits = Deposit::with('table', 'splits')
                    ->where("table_id", $table->id)
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


                $sheet->fromArray($allDeposits);

            });
            $excel->sheet($table->name . " Withdrawals", function ($sheet) use ($table) {

                $thisDay = Carbon::now()->startOfDay();

                $allWithdrawals = array();

                $withdrawals = Withdrawal::with('table', 'splits')
                    ->where("table_id", $table->id)
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

}