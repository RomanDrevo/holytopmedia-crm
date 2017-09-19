<?php
namespace App\Liantech\Helpers;

use App\Models\Setting;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Split;
use App\Models\Table;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Calculate and generate all the reports for compliance and risk departments
 */
class ScoreboardCalculator
{
    protected $settings;

    public function __construct()
    {
        $this->settings = $this->sortSettings();
    }

    public function getEmployeesStatsByTable(Table $table)
    {
        $thisMonth = Carbon::today()->startOfMonth();
        //get all the employees for specific table
        $allTableEmployees = Employee::with('goals')->ByBroker()->where('table_id', $table->id)->get();
        //init the employees with the goal to send back to the component
        $employeesArr = [];

        //new row for each employee
        foreach ($allTableEmployees as $employee) {
            $employeesArr[$employee->employee_crm_id] = $this->createRowForEmployee($employee, $table);
        }

        $deposits = Deposit::ByBroker()
            ->where('table_id', $table->id)
            ->where('assigned_at', '>=', $thisMonth->format('Y-m-d H:i:s'))
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->get();

        if ($table->type == 1) {
            return $this->getStatsForFtd($employeesArr, $deposits, $table);
        } else {
            return $this->getStatsForRst($employeesArr, $deposits, $table);
        }

    }


    public function getStatsForFtd($employeesArr, $deposits, $table)
    {
        $table->dailyTotal = 0;
        $table->monthlyTotal = 0;
        $depositsCustomersCount = $this->getDepositCountArray();

        $today = Carbon::today()->startOfDay();
        foreach ($deposits as $deposit) {

            //Create a new employee row if not exists
            if (!array_key_exists($deposit->receptionEmployeeId, $employeesArr)) {
                $employeesArr[$deposit->receptionEmployeeId] = $this->createRowForEmployee($deposit->employee, $table);
            }

            //Add all deposits to the employee monthly total
            $employeesArr[$deposit->receptionEmployeeId]['monthly']++;
            $table->monthlyTotal++;

            //Add the count of this deposit to "Today deposit" if the date is
            //freater then today.
            if ($deposit->assigned_at >= $today->format('Y-m-d H:i:s')) {
                $employeesArr[$deposit->receptionEmployeeId]['daily']++;
                $table->dailyTotal++;
            }

            //Determine if this deposit is an upsale and count it in the stats
            if ($this->isUpsale($deposit, $depositsCustomersCount)) {
                $employeesArr[$deposit->receptionEmployeeId]['upsale']++;
            }

        }
        return ['employees' => $employeesArr, 'table' => $table];
    }

    public function getStatsForRst($employeesArr, $deposits, $table)
    {
        $table->dailyTotal = 0;
        $table->monthlyTotal = 0;
        $today = Carbon::today()->startOfDay();

        foreach ($deposits as $deposit) {
            $amount = $deposit->amount * floatval($this->settings[$deposit->currency]);  // * $deposit->rateUSD;
            //create the employee row if not exists
            if (!array_key_exists($deposit->receptionEmployeeId, $employeesArr)) {
                $employeesArr[$deposit->receptionEmployeeId] = $this->createRowForEmployee($deposit->employee, $table);
            }

            //add monthly deposit amount to employee/table stats
            $employeesArr[$deposit->receptionEmployeeId]['monthly'] += $amount;
            $table->monthlyTotal += $amount;

            //add today deposits amount to employee/table stats
            if ($deposit->assigned_at >= $today->format('Y-m-d H:i:s')) {
                $employeesArr[$deposit->receptionEmployeeId]['daily'] += $amount;
                $table->dailyTotal += $amount;
            }


            //if deposit is split remove split amounts from employee/table stats
            if ($deposit->splits->count()) {
                $this->RemoveSplitFromTable($employeesArr, $table, $deposit, $today);
            }
        }

        $this->addSplitToTable($employeesArr, $table, $today);

        return ['employees' => $employeesArr, 'table' => $table];

    }

    public function createRowForEmployee(Employee $employee, Table $table)
    {
        $goal = Goal::where('employee_id', $employee->id)->where('table_id', $table->id)->first();
        return [
            'employee' => $employee,
            'daily' => 0,
            'monthly' => 0,
            'upsale' => 0,
            'daily_goal' => $goal ? $goal->daily : 0,
            'monthly_goal' => $goal ? $goal->monthly : 0
        ];
    }

    public function addSplitToTable(&$employeesArr, &$table, $today)
    {
        $thisMonth = Carbon::today()->startOfMonth();
        $crmIds = array();
        //get the database Primary Key for employee
        foreach ($employeesArr as $employee) {
            $crmIds[] = array_get($employee, 'employee.id');
        }

        //Get all splits that belongs to this table's employees
        $splits = Split::whereIn('to', $crmIds)->where('created_at', '>=', $thisMonth->format('Y-m-d H:i:s'))->get();

        //For each split, add the amount to the employee and the table.
        //Also add to the daily total if happen today
        foreach ($splits as $split) {
            $deposit = $split->deposit;
            $toEmployeeId = $split->toEmployee->employee_crm_id;
            $employeesArr[$toEmployeeId]['monthly'] += $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;
            $table->monthlyTotal += $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;

            if ($split->created_at >= $today) {
                $employeesArr[$toEmployeeId]['daily'] += $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;
                $table->dailyTotal += $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;
            }
        }
    }


    public function RemoveSplitFromTable(&$employeesArr, &$table, $deposit, $today)
    {
        foreach ($deposit->splits as $split) {
            $splitEmployeeId = $split->toEmployee->employee_crm_id;

            //subtract split amount from reception employee id
            $employeesArr[$deposit->receptionEmployeeId]['monthly'] -= $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;

            if ($deposit->assigned_at >= $today) {
                $employeesArr[$deposit->receptionEmployeeId]['daily'] -= $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;
            }

            //Remove the split amount from this table
            $table->monthlyTotal -= $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;
            if ($split->created_at >= $today) {
                $table->dailyTotal -= $split->amount * floatval($this->settings[$deposit->currency]); // * $deposit->rateUSD;
            }
        }
    }


    public function isUpsale(Deposit $deposit, $depositsCustomersCount)
    {
        //if the customer deposited more then once since
        //the beginning of last month the employee will get the upsale.
        return !!($depositsCustomersCount[$deposit->customerId] > 1);
    }

    public function getDepositCountArray(Carbon $date = null)
    {
        if (is_null($date)) {
            $date = Carbon::now()->subMonths(1)->startOfMonth();
        }
        $deposits = Deposit::byBroker()
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where("assigned_at", ">=", $date)
            ->select('customerId', \DB::raw('COUNT(amount) as depositsCount'))
            ->groupBy("customerId")
            // ->having("depositsCount", ">=", 2)
            ->get();

        $depositCustomers = [];

        foreach ($deposits as $deposit)
            $depositCustomers[$deposit->customerId] = $deposit->depositsCount;

        return $depositCustomers;
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