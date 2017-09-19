<?php
namespace App\Liantech\Queries;

use App\Models\Withdrawal;
use Carbon\Carbon;
use Config;


class FilterWithdrawalQuery
{
    public static function buildQuery($request)
    {

        $start = ($request->startDate) ? Carbon::parse($request->startDate)->startOfDay() : null;
        $end = ($request->endDate) ? Carbon::parse($request->endDate)->endOfDay() : null;
        return Withdrawal::with('customer', 'employee', 'splits.toEmployee', 'notes', 'table')
            ->byBroker()
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where(function ($query) use ($request, $start, $end) {
                if ($request->id) {
                    $query->where('id', "LIKE", "%$request->id%");
                }
                if ($request->transaction_id) {
                    $query->where('transactionID', "LIKE", "%$request->transaction_id%");
                }
                if ($request->customer_id) {
                    $query->where('customerId', "LIKE", "%$request->customer_id%");
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
                    $query->whereBetween("confirmTime", [$start, $end]);
                } else if ($start) {
                    $query->where("confirmTime", ">=", $start);
                } else if ($end) {
                    $query->where("confirmTime", "<=", $end);
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
    }

    public static function get($request)
    {
        return self::buildQuery($request)->paginate(25);
    }

    public static function noPagination($request)
    {
        return self::buildQuery($request)->get();
    }


    public static function arrayForCvs($withdrawals)
    {
        $result = array();
        foreach ($withdrawals as $withdrawal) {

            $withdrawalData = self::buildWithdrawalRow($withdrawal);
            array_push($result, array_values($withdrawalData));

        }
        return $result;

    }

    public static function buildWithdrawalRow(Withdrawal $withdrawal)
    {
        $type = 'Unknown';
        if ($withdrawal->withdrawal_type == 1) {
            $type = 'FTD';
        } else if ($withdrawal->withdrawal_type == 2) {
            $type = 'RST';
        }
        return [
            "id" => $withdrawal->id,
            "customerId" => $withdrawal->customerId,
            "customerName" => $withdrawal->customer ? $withdrawal->customer->name() : 'N/A',
            "amount" => number_format($withdrawal->amount),
            "currency" => $withdrawal->currency,
            "amountUSD" => number_format($withdrawal->amount * $withdrawal->rateUSD),
            "transactionID" => $withdrawal->transactionID,
            "paymentMethod" => $withdrawal->paymentMethod,
            "clearedBy" => $withdrawal->clearedBy,
            "verification" => $withdrawal->customer ? $withdrawal->customer->verification : 'N/A',
            "status" => $withdrawal->status,
            "employee" => $withdrawal->employee ? $withdrawal->employee->name : 'Unknown',
            "type" => $type,
            "table" => $withdrawal->table ? $withdrawal->table->name : 'Unknown',
            "confirmTime" => $withdrawal->confirmTime ? $withdrawal->confirmTime->format('d-m-Y') : '',
            "notes" => self::buildWithdrawalNotesList($withdrawal)
        ];
    }

    /*
     * @param Withdrawal
     */
    public static function buildWithdrawalNotesList($withdrawal)
    {
        $currencies = Config::get('liantech.currencies_symbols');
        $notesList = '';
        $index = 0;
        if (count($withdrawal->splits)) {
            foreach ($withdrawal->splits as $split) {
                ++$index;
                $notesList .= $index . ". Split for " . $currencies[$withdrawal->currency] . number_format($split->amount) . " with " . $split->toEmployee->name . "\n";
            }
        }
        if ($withdrawal->notes) {
            foreach ($withdrawal->notes as $note) {
                ++$index;
                $notesList .= "$index. $note->content \n";
            }
        }

        return $notesList;
    }


}