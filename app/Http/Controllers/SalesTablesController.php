<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\AssignEmployeesRequest;
use App\Http\Requests\SetTableGoalsRequest;
use App\Liantech\Helpers\DownloadTableReports;
use App\Models\Broker;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Table;
use App\User;
use Config;
use Illuminate\Http\Request;


class SalesTablesController extends Controller
{
    /**
     * Show all tables view
     * @return View
     */
    public function index()
    {
        return view('pages.sales.tables');
    }

    public function getAllTables()
    {
        $tables = Table::with("manager")->ByBroker()->get();
        $employees = Employee::ByBroker()->get();
        $users = User::ByBroker()->get();
        $brokers = Broker::all();

        return response([
            "tables" => $tables,
            "employees" => $employees,
            "users" => $users,
            "brokers" => $brokers
        ], 200);
    }

    /**
     * Delete a table based on ID
     * @param  Int $table_id
     * @return Redirect
     */
    public function deactivate(Request $request)
    {
        //get the requested table
        $table = Table::find($request->table_id);
        if (!$table)
            return response(["error" => "Table with that id could not be found."], 403);

        //get all employees connected to this table
        //and reset the table_id column
        foreach ($table->employees as $employee) {
            $employee->table_id = null;
            $employee->save();
        }

        $table->active = false;
        $table->save();

        return response("ok", 200);
    }

    public function activate(Request $request)
    {
        //get the requested table
        $table = Table::find($request->table_id);
        if (!$table)
            return response(["error" => "Table with that id could not be found."], 403);

        $table->active = true;
        $table->save();

        $tables = Table::with("manager")->ByBroker()->get();
        return response([
            "tables" => $tables
        ], 200);
    }

    public function setGoals(SetTableGoalsRequest $request)
    {
        $table = Table::find($request->table_id);
        $table->daily_goal = $request->daily;
        $table->monthly_goal = $request->monthly;
        $table->save();

        return response("ok", 200);
    }

    /**
     * Update the manager name on the table
     * @param  Request $request
     * @return String
     */
    public function assignManager(Request $request)
    {
        if ($request->has("table_id")) {

            $user = $request->user_id ? User::find($request->user_id) : null;
            $table = Table::find($request->table_id);

            if (is_null($table))
                return response("error", 403);

            $table->manager_id = $user ? $user->id : null;
            $table->save();

            return response("ok", 200);
        }

        return response("error", 403);
    }

    public function assignEmployees(AssignEmployeesRequest $request)
    {
        $table = Table::find($request->table_id);

        if (!$table->active)
            return response("Table must be active!", 403);

        $table->employees->each(function ($employee) {
            $employee->table_id = null;
            $employee->save();
        });

        $table->assignEmployees($request->employees);

        return response("ok", 200);
    }

    public function getAssignedEmployees(Request $request)
    {
        if (!$request->table_id)
            return response("error", 403);

        $table = Table::find($request->table_id);

        if (is_null($table))
            return response("error", 403);

        return response([
            "employees" => $table->employees
        ], 200);
    }

    /**
     * Update the table name
     * @param  Request $request
     * @return String
     */
    public function updateName(Request $request)
    {
        if (isset($request->table_id) && isset($request->name)) {

            $table = Table::find($request->table_id);
            if (is_null($table))
                return "error";

            $table->name = $request->name;
            $table->save();

            return "ok";
        }

        return "error";
    }

    /**
     * Create a new table
     * @param  Request $request
     * @return Redirect
     */
    public function create(Request $request)
    {
        if ($request->has("name") && $request->has("type") && $request->has("manager_id")) {

            $table = new Table;
            $table->type = $request->type;
            $table->name = $request->name;
            $table->manager_id = $request->manager_id;
            $table->broker_id = \Auth::user()->broker_id;
            $table->save();

            return response("ok", 200);
        }

        return response("error", 403);
    }

    /**
     * Assign an employee to his table
     * @param  Request $request
     * @return String
     */
    public function updateTableForEmployee(Request $request)
    {
        if (isset($request->employee_id)) {
            $employee = Employee::find($request->employee_id);
            if (is_null($employee))
                return "error";

            $employee->table_id = (!$request->table_id) ? null : $request->table_id;
            $employee->save();

            return "ok";
        }

        return "error";
    }

    public function downloadMonthlyReport(Request $request)
    {
        return (new DownloadTableReports)->monthlyTableDepositsAndWithdrawals($request);
    }

    public function downloadDailyReport(Request $request)
    {
        return (new DownloadTableReports)->dailyTableDepositsAndWithdrawals($request);
    }
}
