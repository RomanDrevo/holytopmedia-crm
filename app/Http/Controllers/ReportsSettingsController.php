<?php

namespace App\Http\Controllers;
use Config;
use App\Http\Requests;
use App\Liantech\Queries\DepositsQuery;
use App\Liantech\Queries\DepositsDailyQuery;
use App\Liantech\Queries\FilterWithdrawalQuery;
use App\Liantech\Queries\WithdrawalsDailyQuery;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Table;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Liantech\Queries\FilterDepositQuery;

class ReportsSettingsController extends Controller
{

    public function index()
    {
        $settings = Setting::getArrayOfSettings();
        return view("pages.system.settings", compact('settings'));
    }

    public function store(Request $request)
    {
        foreach ($request->all() as $option_name => $option_value) {
            if ($option_name == "_token") continue;

            Setting::where("option_name", $option_name)->update(["option_value" => $option_value]);
        }

        return redirect('system/settings')->with("success", "All settings has been updated successfully");
    }

    public function updateCurrencyPerMonth(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'GBP_USD' => 'required|numeric',
            'EUR_USD' => 'required|numeric',
            'month_year' => 'required|date_format:"m-Y"'
        ]);

        if (!$validator->passes())
            return redirect()->back()->with("error", "Please note, all fields are required to set the month currency.");

        if ($request->GBP_USD <= 0 || $request->EUR_USD <= 0)
            return redirect()->back()->with("error", "The convertion rate cannot be zero.");
        $gbp = $request->GBP_USD;
        $eur = $request->EUR_USD;

        $date = Carbon::createFromFormat("m-Y", $request->month_year);

        $deposits = Deposit::whereBetween("assigned_at", [$date->startOfMonth()->format("Y-m-d H:i:s"), $date->endOfMonth()->format("Y-m-d H:i:s")])->get();

        foreach ($deposits as $deposit) {

            if ($deposit->currency == "USD") continue;

            $deposit->amountUSD = $deposit->currency == "GBP" ? $deposit->amount * $gbp : $deposit->amount * $eur;

            $deposit->save();
        }
        return redirect('system/settings')->with("success", count($deposits) . " rates for " . $date->format("F Y") . " has been updated successfully");
    }
}