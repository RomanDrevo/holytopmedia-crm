<?php
namespace App\Liantech\Helpers;

use App\Models\Table;
use App\Models\Withdrawal;
use Carbon\Carbon;
use App\Models\Deposit;
use App\Models\Setting;


class TableReportsChartsCalculator
{
    protected $settings;
    protected $countries_codes;

    public function __construct()
    {
        $this->settings = $this->sortSettings();
        $this->countries_codes = \Config::get('liantech.countries_codes');
    }



    public function tableMonthlyGoal(Table $table)
    {
        $res = array();
        $startOfMonth = Carbon::now()->startOfMonth();
        $res['goal'] = $table->monthly_goal;
        if ($table->type == 1) {            //ftd table
            $monthlyDeposits = Deposit::byBroker()
                ->where('assigned_at', '>=', $startOfMonth)
                ->where('paymentMethod', '!=', "Bonus")
                ->where('deposit_type', 1)
                ->where('table_id', $table->id)
                ->get();
            $res['total'] = $monthlyDeposits->count('amount');
        } else {
            //rst table
            $monthlyDeposits = Deposit::byBroker()
                ->where('assigned_at', '>=', $startOfMonth)
                ->where('paymentMethod', '!=', "Bonus")
                ->where('deposit_type', 2)
                ->where('table_id', $table->id)
                ->get();
            $res['total'] = $monthlyDeposits->sum('amount');
        }
        return $res;

    }

    public function depositsWithEmployees($request)
    {

        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");
        return Deposit::byBroker()->with('employee')
            ->where('paymentMethod', '!=', 'Bonus')
            ->whereBetween("deposits.assigned_at", [$start, $end])
            ->where('table_id', $request->tableId)
            ->get();
    }

    public function depositsByEmployeesPieChartFTD($request)
    {

        $deposits = $this->depositsWithEmployees($request);
        $graphTotal = array();
        foreach ($deposits as $deposit) {
            $employee = $deposit->employee;
            if (is_null($employee))
                continue;

            $employee_id = $employee->id;
            if (isset($graphTotal[$employee_id])) {
                $graphTotal[$employee_id]['count_deposits']++;

            } else {
                $graphTotal[$employee_id]['count_deposits'] = 1;
                $graphTotal[$employee_id]['employee_name'] = $employee->name;
            }

        }
        $series = array();
        $series['name'] = 'Count';
        $series['type'] = 'pie';
        $deposits = collect($graphTotal);
        $total = $deposits->sum('count_deposits');

        foreach ($deposits as $deposit) {
            $percents = round(($deposit['count_deposits'] / $total) * 100, 2);
            $series['data'][] = array(
                'name' => $deposit['employee_name'],
                'y' => $percents,
                'count' => $deposit['count_deposits'] . ' deposits'
            );
        }
        return [
            'title' => 'Monthly FTD',
            'series' => $series
        ];
    }


    public function depositsByEmployeesBarChartFTD($request)
    {
        $deposits = $this->depositsWithEmployees($request);

        $graphTotal = array();
        foreach ($deposits as $deposit) {
            $employee = $deposit->employee;
            if (is_null($employee))
                continue;

            $employee_id = $employee->id;
            if (isset($graphTotal[$employee_id])) {
                $graphTotal[$employee_id]['count_deposits']++;

            } else {
                $graphTotal[$employee_id]['count_deposits'] = 1;
                $graphTotal[$employee_id]['employee_name'] = $employee->name;
            }

        }
        $series = array();
        $series['name'] = 'Count';
        $series['type'] = 'pie';
        $deposits = collect($graphTotal);

        $title = 'Monthly FTD per employee ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y");
        return array(
            'categories' => $deposits->pluck('employee_name'),
            'title' => $title,
            'series' => array(['name' => 'Deposits', 'data' => $deposits->pluck('count_deposits')])
        );
    }


    public function depositsByEmployeesPieChartRST($request)
    {

        $deposits = $this->depositsWithEmployees($request);
        $graphTotal = [];
        foreach ($deposits as $deposit) {
            $employee = $deposit->employee;
            if (is_null($employee))
                continue;

            $employee_id = $employee->id;
            if (isset($graphTotal[$employee_id])) {
                $graphTotal[$employee_id]['total_deposits'] += $deposit["amount"] * floatval($this->settings[$deposit->currency]);

            } else {
                $graphTotal[$employee_id]['total_deposits'] = $deposit["amount"] * floatval($this->settings[$deposit->currency]);
                $graphTotal[$employee_id]['employee_name'] = $employee->name;
            }

        }
        $deposits = collect($graphTotal);
        $series = array();
        $series['name'] = 'Count';
        $series['type'] = 'pie';
        $total = $deposits->sum('total_deposits');

        foreach ($deposits as $deposit) {
            $percents = round(($deposit['total_deposits'] / $total) * 100, 2);
            $series['data'][] = array(
                'name' => $deposit['employee_name'],
                'y' => $percents,
                'count' => number_format($deposit['total_deposits']) . '$'
            );
        }
        return [
            'title' => 'Monthly RST',
            'series' => $series
        ];
    }

    public function depositsAndNetByEmployeesBarChartRST($request)
    {
        $deposits = $this->depositsWithEmployees($request);
        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");
        $withdrawals = Withdrawal::with('employee')->byBroker()
            ->where('paymentMethod', '!=', 'Bonus')
            ->whereBetween('confirmTime', [$start, $end])
            ->where('table_id', $request->tableId)
            ->get();
        $graphTotal = [];

        foreach ($deposits as $deposit) {
            $employee = $deposit->employee;
            if (is_null($employee))
                continue;

            $employee_id = $employee->id;
            if (isset($graphTotal[$employee_id])) {
                $graphTotal[$employee_id]['total_deposits'] += $deposit["amount"] * floatval($this->settings[$deposit->currency]);

            } else {
                $graphTotal[$employee_id]['total_deposits'] = $deposit["amount"] * floatval($this->settings[$deposit->currency]);
                $graphTotal[$employee_id]['employee_name'] = $employee->name;
            }
            $graphTotal[$employee_id]['net'] = $graphTotal[$employee_id]['total_deposits'];
        }

        foreach ($withdrawals as $withdrawal) {
            $employee = $withdrawal->employee;
            if (is_null($employee))
                continue;

            $employee_id = $employee->id;
            if (isset($graphTotal[$withdrawal->emploeee->id]) && $employee_id) {
                $graphTotal[$employee_id]['net'] -= $withdrawal["amount"] * floatval($this->settings[$withdrawal->currency]);
            }
        }
        $total = (collect($graphTotal));
        return array(
            'categories' => $total->pluck('employee_name'),
            'title' => 'Monthly RST Deposit/WD/Net per employee',
            'series' => array(
                ['name' => 'Total Deposits', 'data' => $total->pluck('total_deposits')],
                ['name' => 'Net', 'data' => $total->pluck('net')]
            )
        );
    }

    private static function sortSettings()
    {
        $allSettings = Setting::all();
        $settings = array();

        foreach ($allSettings as $singleSetting) {
            $settings[$singleSetting->pretty_name] = $singleSetting->option_value;
        }
        return $settings;
    }

}