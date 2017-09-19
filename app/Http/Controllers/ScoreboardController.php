<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Setting;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Table;
use App\Liantech\Helpers\ScoreboardCalculator;
use Carbon\Carbon;

class ScoreboardController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:scoreboard");
    }


    public function getScoreboard($table_id)
    {

        $videos_src = json_encode([
            "small_deposit" =>  Setting::where("option_name", "small_deposit")->first(),
            "big_deposit"   =>  Setting::where("option_name", "big_deposit")->first()
        ]);

        $table = Table::with('manager')->byBroker()->find($table_id);
        if (!$table) {
            return 'error';
        }
        $stats = (new ScoreboardCalculator)->getEmployeesStatsByTable($table);
        switch ($table->type) {
            case 1:
                $employees = collect($stats['employees']);
                $table = $stats['table'];
                return view('pages.sales.ftd-scoreboard', compact('employees', 'table', 'videos_src'));
                break;

            case 2:
                $employees = collect($stats['employees']);
                $table = $stats['table'];
                return view('pages.sales.rst-scoreboard', compact('employees', 'table', 'videos_src'));
                break;

            default:
                return redirect()->back()->with("error", "No matching table type!!");
                break;
        }
    }

    public function getStatsByTable($table_id)
    {
        $table = Table::with('manager')->byBroker()->find($table_id);
        if (!$table) {
            return 'error';
        }
        return (new ScoreboardCalculator)->getEmployeesStatsByTable($table);
    }


}
