<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowChartRequest;
use App\Http\Requests\ShowTableChartRequest;
use App\Liantech\Helpers\ReportsChartsCalculator;
use App\Liantech\Helpers\ScoreboardCalculator;
use App\Liantech\Helpers\TableReportsChartsCalculator;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Deposit;
use App\Models\Withdrawal;

class SalesReportsController extends Controller
{
    public function index()
    {
        $customerByHours = $this->sortCustomersByHours();
        $countries = json_encode(\Config::get('liantech.countries_codes'));
        $campaigns = Campaign::byBroker()->get();

        $left_to_goal = json_encode((new ReportsChartsCalculator)->monthlyGoalGuageChart());
        return view("pages.sales.reports.index", compact('left_to_goal', 'customerByHours', 'countries', 'campaigns'));
    }

    public function sortCustomersByHours(Request $request = null)
    {
        if (isset($request->startDate) && isset($request->endDate)) {
            $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
            $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");
        } else {
            $start = Carbon::now()->subMonth();
            $end = Carbon::now();
        }
        $customers = \DB::connection("spot_db_" . \Auth::user()->broker->name)
            ->table('customers')
            ->select('id', 'regTime', 'Country', 'campaignId')
            ->whereBetween('regTime', [$start, $end])
            ->where('lastDepositDate', '!=', '0000-00-00 00:00:00')
            ->get();

        $hourlyCustomers = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyCustomers[$i] = [];
        }
        foreach ($customers as $customer) {
            $regTime = Carbon::parse($customer->regTime);
            $customer->weekDay = $regTime->dayOfWeek;
            $hourlyCustomers[$regTime->hour][] = $customer;
        }

        return collect($hourlyCustomers);
    }


    public function tableIndex($id)
    {
        $table = Table::find($id);
        $left_to_goal = json_encode((new TableReportsChartsCalculator)->tableMonthlyGoal($table));
        return view("pages.sales.reports.manager", compact('table', 'left_to_goal'));
    }

    public function getDepositsByPaymentMethod(ShowChartRequest $request)
    {
        $data = (new ReportsChartsCalculator)->cCvsWirePieChart($request);
        if (!$data) {
            return response("No relevant data found", 200);
        }
        return response([
            "data" => $data
        ], 200);
    }

    public function getGoalData(ShowChartRequest $request)
    {
        if (isset($request->tableId)) {
            $table = Table::find($request->tableId);
            return (new TableReportsChartsCalculator)->tableMonthlyGoal($table);
        }
        return (new ReportsChartsCalculator)->monthlyGoalGuageChart();
    }

    public function getTotalDepositsAmount()
    {
        return (new ReportsChartsCalculator)->monthlyGoalGuageChart();
    }

    public function getCustomersByCampaigns(ShowChartRequest $request)
    {
        $data = (new ReportsChartsCalculator)->customersByCampaignPieChart($request);
        return response([
            "data" => $data
        ], 200);
    }

    public function getCustomersByContries(ShowChartRequest $request)
    {
        $data = (new ReportsChartsCalculator)->customersByCountriesPieChart($request);
        return response([
            "data" => $data
        ], 200);
    }

    public function getDepositsByCurrencies(ShowChartRequest $request)
    {
        $data = (new ReportsChartsCalculator)->depositsByCurrenciesPieChart($request);
        return response([
            "data" => $data
        ], 200);
    }


    public function getDepositsAndNetByCampaigns(ShowChartRequest $request)
    {
        $data = (new ReportsChartsCalculator)->depositsAndNetByCampaignBarChart($request);
        return response([
            "data" => $data
        ], 200);
    }

    public function getDepositsAndNetByCountries(ShowChartRequest $request)
    {
        $data = (new ReportsChartsCalculator)->depositsAndNetByCountryBarChart($request);
        return response([
            "data" => $data
        ], 200);
    }

    public function getDepositsByEmployeesPie(ShowTableChartRequest $request)
    {
        $table = Table::find($request->tableId);
        if ($table->type == 1) {
            $data = (new TableReportsChartsCalculator)->depositsByEmployeesPieChartFTD($request);
        } else {
            $data = (new TableReportsChartsCalculator)->depositsByEmployeesPieChartRST($request);
        }
        return response([
            "data" => $data
        ], 200);
    }

    public function getDepositsByEmployeesBar(ShowTableChartRequest $request)
    {
        $table = Table::find($request->tableId);
        if ($table->type == 1) {
            $data = (new TableReportsChartsCalculator)->depositsByEmployeesBarChartFTD($request);
        } else {
            $data = (new TableReportsChartsCalculator)->depositsAndNetByEmployeesBarChartRST($request);
        }

        return response([
            "data" => $data
        ], 200);
    }

    public function downloadUpsaleReport(Request $request)
    {
        isset($request->startDate) ? $end = Carbon::parse($request->startDate) : $end = Carbon::now();
        $start = Carbon::parse($end)->subMonths(1)->startOfMonth();
        return (new ReportsChartsCalculator())->getUpsaleData($start, $end);
    }



}


