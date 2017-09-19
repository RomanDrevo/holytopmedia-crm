<?php

namespace App\Http\Controllers;

use App\Liantech\Helpers\DownloadEmployeeReports;
use App\Liantech\Queries\DepositsMonthlyQuery;
use App\Liantech\Queries\DepositsQuery;
use App\Liantech\Queries\FilterDepositQuery;
use App\Liantech\Queries\FilterWithdrawalQuery;
use App\Liantech\Queries\WithdrawalsDailyQuery;
use App\Liantech\Queries\WithdrawalsMonthlyQuery;
use App\Liantech\Queries\WithdrawalsQuery;
use App\Liantech\Repositories\SpotRepository;
use App\Liantech\Repositories\StatsRepository;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Table;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use App\Liantech\Queries\DepositsDailyQuery;

class AdministratorController extends Controller
{
    public function showDeposits(Request $request)
    {

        $employees = Employee::ByBroker()->get();
        $tables = Table::byBroker()->get();
        $currencies = json_encode(Config::get('liantech.currencies_symbols'));

        return view('pages.system.deposits', compact('employees', 'tables', 'currencies'));
    }

    public function getDepositsData(Request $request)
    {
        return DepositsQuery::get();
    }

    public function getFilteredDeposits(Request $request)
    {
        return FilterDepositQuery::get($request);

    }

    public function filteredDepositsForCvs(Request $request)
    {
        $deposits = FilterDepositQuery::noPagination($request);
        return FilterDepositQuery::arrayForCvs($deposits);

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

    public function showWithdrawals(Request $request)
    {
        $employees = Employee::active()->ByBroker()->get();
        $tables = Table::all();
        $currencies = json_encode(Config::get('liantech.currencies_symbols'));

        return view('pages.system.withdrawals', compact('employees', 'tables', 'currencies'));

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

    public function dailyWithdrawalsForCvs(Request $request)
    {
        $withdrawals = WithdrawalsDailyQuery::getNoPagination();
        return FilterWithdrawalQuery::arrayForCvs($withdrawals);
    }

    public function monthlyWithdrawalsForCvs(Request $request)
    {
        $withdrawals = WithdrawalsMonthlyQuery::getNoPagination();
        return FilterWithdrawalQuery::arrayForCvs($withdrawals);
    }

    public function employees(Request $request)
    {
        $q = request()->has('query') ? request('query') : "";

        $employees = Employee::with("deposits", "withdrawals")
            ->whereHas("deposits", function ($query) {
                $query->where("confirmTime", ">=", Carbon::now()->startOfMonth());
            })
            ->ByBroker()
            ->active()
            ->where('name', 'LIKE', "%" . $q . "%")
            ->orderBy('name')->paginate(25);

        return view('pages.system.employees', compact('employees'));
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

    public function downloadEmployeeDeposits(Request $request)
    {
        return (new DownloadEmployeeReports)->downloadEmployeeDeposits($request);
    }

}
