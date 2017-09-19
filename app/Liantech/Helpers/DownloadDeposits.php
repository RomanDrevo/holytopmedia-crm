<?php
namespace App\Liantech\Helpers;
use Config;
use App\Http\Requests;
use App\Liantech\Queries\DepositsQuery;
use App\Liantech\Queries\DepositsDailyQuery;
use App\Liantech\Queries\WithdrawalsDailyQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DownloadDeposits
{
    public function downloadDepositsResults(Request $request)
    {


        $deposits = DepositsQuery::getNoPagination();
        Excel::create(date('d-m-y') . '_employee-export', function ($excel) use ($deposits) {
            $excel->setTitle("Search results deposits");
            $excel->sheet("Search results deposits", function ($sheet) use ($deposits) {

                $thisMonth = Carbon::now()->startOfMonth();

                $allDeposits = array();

                foreach ($deposits as $deposit) {

                    $notes = "";

                    if ($deposit->is_split)
                        $notes = "Split for " . number_format($deposit->split->amount) . " with " . $deposit->split->employee->name;

                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customerId,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Amount USD' => number_format($deposit->amountUSD),
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->clearedBy,
                        'Is Verified' => $deposit->is_verified ? "YES" : "NO",
                        'Confirm Time' => $deposit->assigned_at ? $deposit->assigned_at->format('m-d-Y H:i:s') : "",
                        'Notes' => $notes,
                    );
                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);
                }

                $sheet->fromArray($allDeposits);

            });

        })->export('xls');
    }

    public function downloadDepositsDaily()
    {
        $deposits = DepositsDailyQuery::getNoPagination();

        Excel::create(date('d-m-y') . '_deposits', function ($excel) use ($deposits) {
            $excel->setTitle("Daily Deposits " . Carbon::now()->format("d-m-Y"));
            $excel->sheet("Daily Deposits " . Carbon::now()->format("d-m-Y"), function ($sheet) use ($deposits) {

                $thisMonth = Carbon::now()->startOfMonth();


                $allDeposits = array();

                foreach ($deposits as $deposit) {

                    $notes = "";



                    if ($deposit->is_split)
                        $notes = "Split for " . number_format($deposit->split->amount) . " with " . $deposit->split->employee->name;
                    $oneDeposit = array(
                        'ID' => $deposit->id,
                        'Customer ID' => $deposit->customerId,
                        'Amount' => number_format($deposit->amount),
                        'currency' => $deposit->currency,
                        'Payment Method' => $deposit->paymentMethod,
                        'Cleared By' => $deposit->clearedBy,
                        'Employee' => $deposit->employee ? $deposit->employee->name : "unknown",
                        'Table Manager' => ($deposit->employee && $deposit->employee->table) ? $deposit->employee->table->manager : "unknown",
                        'Is Verified' => $deposit->customer ? $deposit->customer->verification : "No customer found",
                        'Confirm Time' => $deposit->assigned_at ? $deposit->assigned_at->format('d-M-Y') : "",
                        'Notes' => $notes,
                    );

                    //push the deposit to the big array
                    array_push($allDeposits, $oneDeposit);
                }

                $sheet->fromArray($allDeposits);

            });

        })->export('xls');
    }

    public function downloadWithdrawalsDaily()
    {
        $withdrawals = WithdrawalsDailyQuery::getNoPagination();

        Excel::create(date('d-m-y') . '_withdrawals', function ($excel) use ($withdrawals) {
            $excel->setTitle("Daily Withdrawals " . Carbon::now()->format("d-m-Y"));
            $excel->sheet("Daily Withdrawals " . Carbon::now()->format("d-m-Y"), function ($sheet) use ($withdrawals) {

                $thisMonth = Carbon::now()->startOfMonth();

                $allWithdrawals = array();

                foreach ($withdrawals as $withdrawal) {

                    $notes = "";

                    //Calculate the amount to take from each employee for this deposit.
                    //
                    // if($withdrawal->is_split)
                    //     $notes = "Split for ". number_format($withdrawal->split->amount) . " with " . $withdrawal->split->employee->name;

                    $oneWithdrawal = array(
                        'ID' => $withdrawal->id,
                        'Customer ID' => $withdrawal->customerId,
                        'Amount' => number_format($withdrawal->amount),
                        'currency' => $withdrawal->currency,
                        'Payment Method' => $withdrawal->paymentMethod,
                        'Cleared By' => $withdrawal->clearedBy,
                        'Employee' => $withdrawal->employee ? $withdrawal->employee->name : "unknown",
                        'Table Manager' => ($withdrawal->employee && $withdrawal->employee->table) ? $withdrawal->employee->table->manager : "unknown",
                        'Is Verified' => $withdrawal->customer ? $withdrawal->customer->verification : "No customer found",
                        'Confirm Time' => $withdrawal->confirmTime ? $withdrawal->confirmTime->format('d-M-Y') : "",
                        'Notes' => $notes,
                    );
                    //push the withdrawals to the big array
                    array_push($allWithdrawals, $oneWithdrawal);
                }

                $sheet->fromArray($allWithdrawals);

            });

        })->export('xls');
    }

}