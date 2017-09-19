<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Liantech\Classes\Pusher;
use App\Liantech\Helpers\DownloadEmployeeReports;
use App\Liantech\Queries\DepositsQuery;
use App\Liantech\Queries\FilterDepositQuery;
use App\Liantech\Queries\FilterWithdrawalQuery;
use App\Liantech\Queries\WithdrawalsQuery;
use App\Http\Requests\GetEmployeesRequest;
use App\Liantech\Repositories\SpotRepository;
use App\Liantech\Repositories\StatsRepository;
use App\Models\Broker;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Split;
use App\Models\Table;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Liantech\Queries\DepositsDailyQuery;
use App\Liantech\Queries\DepositsMonthlyQuery;


class SalesController extends Controller
{

    public function showDeposits(Request $request)
    {
        $employees = Employee::ByBroker()->get();
        $tables = Table::byBroker()->get();
        $currencies = json_encode(Config::get('liantech.currencies_symbols'));
        $hasPermissionsToEdit = (\Auth::user()->hasPermission('admin') || \Auth::user()->hasPermission('sales_edit_deposits')) ? 1 : 0;

        return view('pages.sales.deposits', compact('employees', 'tables', 'currencies', 'hasPermissionsToEdit'));
    }

    public function getDepositsData(Request $request)
    {
        return DepositsQuery::get();
    }

    public function getFilteredDeposits(Request $request)
    {
        if ($request->employee) {
            $deposits = FilterDepositQuery::noPagination($request);

            $splitDeposits = FilterDepositQuery::getDepositsBySplitToEmployee($request);
            $merge = $deposits->merge($splitDeposits)->sortByDesc('id');
            return new LengthAwarePaginator($merge, $merge->count(), 25);
        }
        return FilterDepositQuery::get($request);
    }

    public function filteredDepositsForCvs(Request $request)
    {
        $deposits = FilterDepositQuery::noPagination($request);
        if ($request->employee) {
            $splitDeposits = FilterDepositQuery::getDepositsBySplitToEmployee($request);
            $merge = $deposits->merge($splitDeposits)->sortByDesc('id');
            return FilterDepositQuery::arrayForCvs($merge);
        }
        return FilterDepositQuery::arrayForCvs($deposits);

    }

    public function showWithdrawals(Request $request)
    {
        $employees = Employee::ByBroker()->get();
        $tables = Table::byBroker()->get();
        $currencies = json_encode(Config::get('liantech.currencies_symbols'));
        $hasPermissionsToEdit = (\Auth::user()->hasPermission('admin') || \Auth::user()->hasPermission('sales_edit_deposits')) ? 1 : 0;
        return view('pages.sales.withdrawals', compact('employees', 'tables', 'currencies', 'hasPermissionsToEdit'));
    }

    public function getWithdrawalsData(Request $request)
    {
        return WithdrawalsQuery::get();
    }

    public function getFilteredWithdrawals(Request $request)
    {
        return FilterWithdrawalQuery::get($request);
    }

    public function filteredWithdrawalsForCvs(Request $request)
    {
        $withdrawals = FilterWithdrawalQuery::noPagination($request);
        return FilterWithdrawalQuery::arrayForCvs($withdrawals);
    }


    public function showEmployees()
    {
        $tables = Table::byBroker()->get();
        return view('pages.sales.employees', compact('tables'));
    }

    public function getEmployees(GetEmployeesRequest $request)
    {

        $employees = Employee::byBroker()
            ->with('table', 'goals')
            ->where(function ($query) use ($request) {

                if ($request->has("table_id")) {
                    $query->where('table_id', $request->table_id);
                }

                if ($request->has("term")) {
                    $query->where("name", "LIKE", "%" . $request->term . "%");
                }

                if($request->has("is_active")){
                    $query->where('active', $request->is_active);
                }
            })
            ->orderBy("id", "desc")
            ->paginate(25);

        return $employees;
    }

    public function downloadEmployeesDailyReports(Request $request)
    {
        return (new DownloadEmployeeReports)->dailyEmployeeDepositsAndWithdrawals($request);
    }

    public function downloadEmployeesMonthlyReports(Request $request)
    {
        return (new DownloadEmployeeReports)->monthlyEmployeeDepositsAndWithdrawals($request);
    }


    public function getReports()
    {
        $employees = Employee::ByBroker()->with("deposits")->orderBy("id", "desc")->paginate(15);

        return view('pages.sales.reports', compact('employees'));
    }


    private function createEmployeesSelect($employees)
    {
        $html = '<select data-deposit-id="[deposit_id]" class="deposit-worker-selector nice-select form-control" style="width: 100%">
                    <option value="0">Selfie</option>';

        foreach ($employees as $employee) {
            $html .= '<option value="' . $employee->employee_crm_id . '">' . $employee->name . '</option>';
        }

        $html .= '</select>';

        return $html;

    }

    public function getPlaygroundStats(Request $request)
    {
        if (isset($request->shouldUpdate) && $request->shouldUpdate) {

            $statsRepo = new StatsRepository();

            $data = [
                "daily_ftd" => $statsRepo->dailyFtdCount(),
                "monthly_ftd" => $statsRepo->monthlyFtdCount(),
                "today_deposits" => $statsRepo->todayTotalDeposits(),
                "monthly_deposits" => $statsRepo->monthlyTotalDeposits(),
            ];

            return $data;
        }

        return redirect()->back();
    }


    public function settings()
    {
        $settings = Setting::getArrayOfSettings();
        return view("pages.sales.settings", compact('settings'));
    }


    public function setMonthGoal(Request $request)
    {
        if (isset($request->monthly_goal) && is_numeric($request->monthly_goal)) {
            $setting = Setting::where('option_name', 'monthly_goal')->first();
            $setting->option_value = $request['monthly_goal'];
            $setting->save();
            return redirect('/sales/settings')->with('success', 'Goal successfully updated');
        }
        return redirect('/sales/settings')->with('error', 'The goal has to be numeric');
    }

    public function setAutoApprovedProcessor (Request $request){

        if (isset($request->auto_approved_processor)) {

            $setting = Setting::where('option_name', 'auto_approved_processors')->first();
            $setting->option_value = $request['auto_approved_processor'];
            $setting->save();

            return redirect('/sales/settings')->with('success', 'Auto Approved Processors successfully updated');
        }

        return redirect('/sales/settings')->with('error', 'Choose Processor');
    }

    public function loadEmployeeImage(Request $request)
    {

        if (!$request->file('img')) {
            return response([
                "message" => "image is required!"
            ], 403);
        }

        $path = $request->img->store('employees', 'image');

        $employee = Employee::byBroker()->where("id", $request->employee_id)->first();

        if (is_null($employee))
            return response("error uploading image", 403);

        $employee->image = $path;
        $employee->save();

        return response([
            "employee" => $employee
        ], 200);
    }

    public function dailyDepositsForCvs(Request $request)
    {
        $deposits = DepositsDailyQuery::getNoPagination();
        return FilterDepositQuery::arrayForCvs($deposits);

    }

    public function monthlyDepositsForCvs(Request $request)
    {
        $deposits = DepositsMonthlyQuery::getNoPagination();
        return FilterDepositQuery::arrayForCvs($deposits);

    }
}
