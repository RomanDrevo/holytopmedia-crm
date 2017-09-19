<?php
namespace App\Liantech\Queries;

use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Split;
use App\Models\Setting;
use Carbon\Carbon;
use Config;
use Illuminate\Database\Eloquent\Collection;

class FilterDepositQuery
{
    public static function buildQuery($request)
    {
        $start = ($request->startDate) ? Carbon::parse($request->startDate)->startOfDay() : null;
        $end = ($request->endDate) ? Carbon::parse($request->endDate)->endOfDay() : null;

        $depositsQuery = Deposit::with('customer', 'employee', 'splits.toEmployee', 'notes', 'table')
            ->byBroker()
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where(function ($query) use ($request, $start, $end) {
                if ($request->id) {
                    $query->where('id', "LIKE", "%$request->id%"); //search by deposit id
                }
                if ($request->transaction_id) {
                    $query->where('transactionID', "LIKE", "%$request->transaction_id%");
                }
                if ($request->customer_id) {
                    $query->where('customerId', "LIKE", "%$request->customer_id%"); //search by customer id
                }

                if ($request->employee) {//search by employee id
                    $query->where("receptionEmployeeId", intval($request->employee['employee_crm_id']));
                }

                if ($request->table) {//search by table id
                    $query->where("table_id", intval($request->table['id']));
                }

                if ($request->status && $request->status != 'all') {
                    $query->where("status", $request->status);
                }

                if ($start && $end) {//search by date(dates)
                    $query->whereBetween("assigned_at", [$start, $end]);
                } else if ($start) {
                    $query->where("assigned_at", ">=", $start);
                } else if ($end) {
                    $query->where("assigned_at", "<=", $end);
                }


                if ($request->amountMin && $request->amountMax) {//search by amount & currency
                    $query->whereBetween("amount", [$request->amountMin, $request->amountMax])->where("currency", $request->currency);
                } else if ($request->amountMin) {
                    $query->where("amount", ">=", $request->amountMin)->where("currency", $request->currency);;
                } else if ($request->amountMax) {
                    $query->where("amount", "<=", $request->amountMax)->where("currency", $request->currency);;
                }

            })
            ->orderBy($request->orderBy, $request->order);

        return $depositsQuery;
    }

    public static function getDepositsBySplitToEmployee($request)
    {
        $start = ($request->startDate) ? Carbon::parse($request->startDate)->startOfDay() : null;
        $end = ($request->endDate) ? Carbon::parse($request->endDate)->endOfDay() : null;


        return Deposit::with('customer', 'employee', 'splits.toEmployee', 'notes', 'table')
            ->whereHas('splits', function ($query) use ($request) {
                $query->where("to", $request->employee['id']);
            })
            ->where(function ($query) use ($request, $start, $end) {
                if ($request->startDate && $request->endDate) {//search by date(dates)
                    $query->whereBetween("assigned_at", [$start, $end]);
                } else if ($request->startDate) {
                    $query->where("assigned_at", ">=", $start);
                } else if ($request->endDate) {
                    $query->where("assigned_at", "<=", $end);
                }

                if ($request->amountMin && $request->amountMax) {//search by amount & currency
                    $query->whereBetween("amount", [$request->amountMin, $request->amountMax])->where("currency", $request->currency);
                } else if ($request->amountMin) {
                    $query->where("amount", ">=", $request->amountMin)->where("currency", $request->currency);
                } else if ($request->amountMax) {
                    $query->where("amount", "<=", $request->amountMax)->where("currency", $request->currency);
                }

            })->get();
    }

    public static function get($request)
    {
        return self::buildQuery($request)->paginate(25);
    }

    public static function noPagination($request)
    {
        return self::buildQuery($request)->get();
    }

    /**
     * @param Collection $deposits
     * @return array
     */
    public static function arrayForCvs($deposits)
    {
        $processorsStr = Setting::where('option_name', 'auto_approved_processors')->pluck("option_value")->first();

        $result = array();
        foreach ($deposits as $deposit) {
            $depositData = self::buildDepositRow($deposit, $processorsStr);
            array_push($result, array_values($depositData));
            //create cvs row for each deposit split with same values besides employee name and amount
            if ($deposit->is_split) {
                foreach ($deposit->splits as $split) {
                    $splitDepositData = self::buildSplitRow($deposit, $split);
                    $splitDepositData = array_merge($depositData, $splitDepositData);
                    array_push($result, array_values($splitDepositData));
                }
            }
        }
        return $result;

    }


    public static function buildDepositRow(Deposit $deposit, $processorsStr)
    {
        $amount = $deposit->amount;
        $amountUSD = $deposit->amount * $deposit->rateUSD;
        if ($deposit->is_split) {
            foreach ($deposit->splits as $split) {
                $amount -= $split->amount;
                $amountUSD -= $split->amount * $deposit->rateUSD;
            }
        }
        $type = 'Unknown';
        if ($deposit->deposit_type == 1) {
            $type = 'FTD';
        } else if ($deposit->deposit_type == 2) {
            $type = 'Deposit';
        }
        return [
            "id" => $deposit->id,
            "customerId" => $deposit->customerId,
            "customerName" => $deposit->customer ? $deposit->customer->name() : 'N/A',
            "amount" => number_format($amount),
            "currency" => $deposit->currency,
            "amountUSD" => number_format($amountUSD),
            "transactionID" => $deposit->transactionID,
            "paymentMethod" => $deposit->paymentMethod,
            "clearedBy" => $deposit->clearedBy,
            "verification" => $deposit->customer ? $deposit->customer->verification : 'N/A',
            "is_approved" => $deposit->approved || $deposit->isSalesApproved($processorsStr) ? "YES" : "NO",
            "employee" => $deposit->employee ? $deposit->employee->name : 'Unknown',
            "type" => $type,
            "table" => $deposit->table ? $deposit->table->name : 'Unknown',
            "confirmTime" => $deposit->confirmTime ? $deposit->confirmTime->format('d-m-Y') : '',
            "notes" => self::buildDepositNotesList($deposit)
        ];
    }


    public static function buildSplitRow(Deposit $deposit, Split $split)
    {
        return [
            "employee" => $split->toEmployee->name,
            "table" => $split->toEmployee->table ? $split->toEmployee->table->name : 'Unknown',
            "amount" => number_format($split->amount),
            "amountUSD" => number_format($split->amount * $deposit->rateUSD),
            "notes" => 'This is split for deposit ID: ' . $deposit->id
        ];
    }


    /*
     * @param Deposit $deposit
     */
    public static function buildDepositNotesList($deposit)
    {
        $currencies = Config::get('liantech.currencies_symbols');
        $notesList = [];
        $index = 0;
        if (count($deposit->splits)) {
            foreach ($deposit->splits as $split) {
                ++$index;
                $notesList[] = $index . ". Split for " . number_format($split->amount) . $deposit->currency . " with " . $split->toEmployee->name;
            }
        }
        if ($deposit->notes) {
            foreach ($deposit->notes as $note) {
                ++$index;
                $notesList[] = $index . ". " . $note->content;
            }
        }

        return trim(implode(",   ", $notesList));
    }
}