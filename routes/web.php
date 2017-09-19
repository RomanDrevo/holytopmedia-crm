<?php

use Illuminate\Support\Facades\Auth;
use App\Liantech\Classes\Pusher;
use Intercom\IntercomClient;



Auth::routes();
Route::get('/', function () { return redirect('/login'); });



Route::group(["middleware" => "auth"], function () {
    //DASHBOARD
    Route::get('dashboard', 'MainController@index');


    //Upload Customers to spot
    Route::group(["prefix" => "upload"], function(){
        Route::get('/add-customers', 'CustomersUploaderController@createCustomers');
        Route::post('/add-customers/proccess-file', 'CustomersUploaderController@createAndUpload');

        Route::get('/edit-customers', 'CustomersUploaderController@editCustomers');
        Route::post('/edit-customers/proccess-file', 'CustomersUploaderController@editAndUpload');
    });


    //Screens
    Route::group(["prefix" => "screens"], function(){

        //jackpot
        Route::get('/jackpot', 'ScreensController@jackpot');
        Route::post('/get-stats', 'ScreensController@getStats');
        Route::post('/add-winner-video', 'ScreensController@storeVideo');

        //Scoreboard
        Route::get('/scoreboard/{table}', 'ScoreboardController@getScoreboard');
        Route::get('/scoreboard/get-stats/{table}', 'ScoreboardController@getStatsByTable');
        Route::post('/get-scoreboard-updates', 'ScoreboardController@getScoreboardUpdates');

    });
    
    //SALES
    Route::group(["prefix" => "sales", "middleware" => "permission:sales"], function(){
        //pages

        Route::get('/employees', 'SalesController@showEmployees');
        Route::get('/employees/get-employees', 'SalesController@getEmployees');
        Route::post('/employees/add-employee-image', 'SalesController@loadEmployeeImage');

        //deposits
        Route::get('/deposits', 'SalesController@showDeposits');
        Route::get('/deposits/get-data', 'SalesController@getDepositsData');
        Route::get('/deposits/search', 'SalesController@getFilteredDeposits');
        Route::get('/deposits/download-results', 'SalesController@filteredDepositsForCvs');

        Route::post('/assign-deposit-to-employee', 'DepositsController@assignDepositToEmployee');
        Route::post('/assign-deposit-to-type', 'DepositsController@assignDepositToType');
        Route::post('/assign-deposit-to-time', 'DepositsController@assignDepositToTime');
        Route::post('/update-deposit-verification-status', 'DepositsController@UpdateDepositVerificationStatus');
        Route::post('/assign-deposit-to-table', 'DepositsController@assignDepositToTable');
        Route::post('/create-new-split', 'DepositsController@createNewSplit');
        Route::post('/remove-split', 'DepositsController@removeSplit');

        Route::get('/deposits/daily', 'SalesController@dailyDepositsForCvs');
        Route::get('/deposits/monthly', 'SalesController@monthlyDepositsForCvs');
//        Route::get('/deposits/monthly', 'AdministratorController@monthlyDepositsForCvs');

        //notes
        Route::post('/add-note', 'DepositsController@addDepositNote');


        //reports
        Route::get("/reports", "SalesReportsController@index");
        Route::post("/reports/goal-data", "SalesReportsController@getGoalData");
        Route::post("/reports/upsale", "SalesReportsController@downloadUpsaleReport");
        Route::post("/reports/payment-method", "SalesReportsController@getDepositsByPaymentMethod");
        Route::post("/reports/campaigns", "SalesReportsController@getCustomersByCampaigns");
        Route::post("/reports/countries", "SalesReportsController@getCustomersByContries");
        Route::post("/reports/currencies", "SalesReportsController@getDepositsByCurrencies");

        Route::post("/reports/deposits-and-net/campaign", "SalesReportsController@getDepositsAndNetByCampaigns");
        Route::post("/reports/deposits-and-net/country", "SalesReportsController@getDepositsAndNetByCountries");

        Route::post("/reports/customers-per-hours", "SalesReportsController@sortCustomersByHours");

        Route::get("reports/manager/{table_id}", "SalesReportsController@tableIndex");
        Route::post("reports/mananger/employees-pie", "SalesReportsController@getDepositsByEmployeesPie");
        Route::post("reports/mananger/employees-bar", "SalesReportsController@getDepositsByEmployeesBar");

        //withdrawals
        Route::get('/withdrawals', 'SalesController@showWithdrawals');
        Route::get('/withdrawals/get-data', 'SalesController@getWithdrawalsData');
        Route::get('/withdrawals/search', 'SalesController@getFilteredWithdrawals');
        Route::get('/withdrawals/download-results', 'SalesController@filteredWithdrawalsForCvs');

        Route::post('/assign-withdrawal-to-employee', 'WithdrawalsController@assignWithdrawalToEmployee');
        Route::post('/assign-withdrawal-to-type', 'WithdrawalsController@assignWithdrawalToType');
        Route::post('/update-withdrawal-verification-status', 'WithdrawalsController@UpdateWithdrawalVerificationStatus');
        Route::post('/assign-withdrawal-to-table', 'WithdrawalsController@assignWithdrawalToTable');
        Route::post('/create-new-withdrawal-split', 'WithdrawalsController@createNewWithdrawalSplit');
        Route::post('/remove-withdrawal-split', 'WithdrawalsController@removeWithdrawalSplit');

        //withdrawal notes
        Route::post('/add-withdrawal-note', 'WithdrawalsController@addWithdrawalNote');

        //employees
        Route::post('/update-goal', 'EmployeesController@updateGoal');
        Route::post('/get-goal-for-table', 'EmployeesController@getGoalForTable');
        Route::post('/get-goals', 'EmployeesController@getGoals');
        Route::post('/update-employee-name', 'EmployeesController@updateName');
        Route::get('/csv-employee-deposits', 'ReportsSettingsController@downloadDeposits');
        Route::get('/employees/csv-employee-deposits/monthly', 'SalesController@downloadEmployeesMonthlyReports');
        Route::get('/employees/csv-employee-deposits/daily', 'SalesController@downloadEmployeesDailyReports');

        //Tables
        Route::get('/tables', 'SalesTablesController@index');
        Route::get('/tables/all-tables', 'SalesTablesController@getAllTables');
        Route::post('/tables/deactivate', 'SalesTablesController@deactivate');
        Route::post('/tables/activate', 'SalesTablesController@activate');
        Route::post('/tables/assign-manager', 'SalesTablesController@assignManager');
        Route::post('/tables/set-goals', 'SalesTablesController@setGoals');
        Route::post('/tables/assign-employees', 'SalesTablesController@assignEmployees');
        Route::get('/tables/assigned-employees', 'SalesTablesController@getAssignedEmployees');

        //Tables Reports
        Route::get('/tables/monthly-report', 'SalesTablesController@downloadMonthlyReport');
        Route::get('/tables/daily-report', 'SalesTablesController@downloadDailyReport');


        Route::post('/update-table-manager', 'SalesTablesController@updateManager');
        Route::post('/update-table-name', 'SalesTablesController@updateName');
        Route::post('/tables/create', 'SalesTablesController@create');
        Route::post('/update-user-table', 'SalesTablesController@updateTableForEmployee');

        //general
        Route::post('/get-scoreboard-updates', 'HomeController@getScoreboardUpdates');

        //settings
        Route::get('settings', "SalesController@settings");
        Route::post('settings/set-month-goal', "SalesController@setMonthGoal");
        Route::post('settings/set-auto-approved-processor', "SalesController@setAutoApprovedProcessor");


    });

    //MARKETING
    Route::group(["prefix" => "marketing", "middleware" => "permission:marketing"], function(){
        //LISTS
        Route::get('/lists', 'EmailsListsController@index');
        Route::get('/lists/create', 'EmailsListsController@create');
        Route::post('/lists/create', 'EmailsListsController@store');
        Route::get('/lists/{id}', 'EmailsListsController@show');
        Route::get('/lists/{id}/customers', 'EmailsListsController@getCustomers');
        Route::get('/lists/{list_id}/customers/{customer_id}', 'EmailsListsController@customerClicked');

        //REPORTS
        Route::get('/reports', 'EmailsReportsController@index');

        //CAMPAIGNS
        Route::get('/campaigns', 'MarketingController@index');
        Route::post('/get-campaign-info', 'MarketingController@getCampaign');



       Route::get('/test', 'MarketingController@verifyEmails');


        Route::get('/sms', 'MarketingController@sendSms');
        Route::post('/sms/count', 'MarketingController@checkPhonesCount');
        Route::post('/sms/to', 'MarketingController@sendTo');
        Route::post('/sms/test', 'MarketingController@sendTestSms');

        Route::get('/download/customers', 'MarketingController@downloadToSVC');

    });

    //COMPLIANCE
    Route::group(["prefix" => "compliance", "middleware" => "permission:compliance"], function(){
        //customers
        Route::get('/customers', 'CustomersController@index')->middleware("permission:compliance_show_customers");
        Route::get('/customers/get-customers', 'CustomersController@getCustomers');
        Route::get('/customers/{id}', 'CustomersController@show');
        Route::get('/customers/{id}/get-customer-info', 'CustomersController@getCustomerInfo');
        Route::post('/customers/{id}/update', 'CustomersController@update');

        Route::post('/customers/{id}/add-comment', 'CustomersController@addCommentToCustomer');

        Route::post('/customers/{id}/edit-comments', 'CustomersController@editFileComments');
        Route::post('/get-deposits', 'ComplianceAjaxController@getDeposits');
        Route::post("/customer/toggle-deposit", 'ComplianceAjaxController@toggleDeposit');

        Route::post('/approve-deny-file', 'ComplianceAjaxController@approveDenyFile');
        Route::post('/get-comments-for-file', 'ComplianceAjaxController@getFileComments');
        
        Route::get('/pending', 'ComplianceController@showPending')->middleware("permission:compliance_show_pending");
        Route::get('/pending/get-data', 'ComplianceController@getPendingDeposits');
        Route::get('/pending/search', 'ComplianceController@getSearchResults');

        Route::get('/alerts', 'RiskController@index')->middleware("permission:compliance_show_alerts");
        Route::get('/alerts/by-type', 'RiskController@get');
        Route::get('/reports', 'ComplianceController@getReports')->middleware("permission:compliance_show_reports");

        Route::post('/alerts/{alertId}/delete', 'RiskController@destroy');

        //settings
        Route::get('settings', "RiskController@settings");
        Route::post('settings/save-settings', "RiskController@saveSettings");

    });

    //MANAGEMENT
    Route::group(["prefix" => "system", "middleware" => "permission:management"], function(){

        Route::get('/test', function (){
            $client = new IntercomClient("dG9rOmU0ZGNiMWZiXzhhMThfNDBlOF84OGNkXzAwNThjNmRmNDA5ZDoxOjA=", null);
        });

        //deposits
        Route::get('/deposits', 'AdministratorController@showDeposits');
        Route::get('/deposits/get-data', 'AdministratorController@getDepositsData');
        Route::get('/deposits/search', 'AdministratorController@getFilteredDeposits');
        Route::get('/deposits/download-results', 'AdministratorController@filteredDepositsForCvs');
        Route::get('/deposits/daily', 'AdministratorController@dailyDepositsForCvs');
        Route::get('/deposits/monthly', 'AdministratorController@monthlyDepositsForCvs');

        //withdrawals
        Route::get('/withdrawals', 'AdministratorController@showWithdrawals');
        Route::get('/withdrawals/get-data', 'AdministratorController@getWithdrawalsData');
        Route::get('/withdrawals/search', 'AdministratorController@getFilteredWithdrawals');
        Route::get('/withdrawals/download-results', 'AdministratorController@filteredWithdrawalsForCvs');
        Route::get('/withdrawals/daily', 'AdministratorController@dailyWithdrawalsForCvs');
        Route::get('/withdrawals/monthly', 'AdministratorController@monthlyWithdrawalsForCvs');

        //employees
        Route::get('/employees', 'AdministratorController@employees');
        Route::get('/csv-employee-deposits', 'AdministratorController@downloadEmployeeDeposits');

        //settings
        Route::get('settings', "ReportsSettingsController@index");
        Route::post('settings/change-currency', "ReportsSettingsController@store");
        Route::post('settings/update-currency-per-month', "ReportsSettingsController@updateCurrencyPerMonth");
    });

    //Application Manager
    Route::group(["prefix" => "admin", "middleware" => "permission:admin"], function(){

        Route::get('/users', 'AppUserController@index');
        Route::get('/users/create', 'AppUserController@create');

        Route::get('/users/departments', 'AppUserController@getDepartments');
        Route::get('/users/brokers', 'AppUserController@getBrokers');
        Route::post('/users/store', 'AppUserController@store');

        Route::post('/users/update', 'AppUserController@update');
        Route::get('users/{id}', 'AppUserController@show');

        Route::delete('/users/{id}/delete/', 'AppUserController@destroy');
    });
    
});


Route::get("/test-all-employees", function(){
    return App\Models\Employee::where("broker_id", 1)->get();
});

//Track the user click
Route::get('/marketing/emails/track-user-click', 'TrackingController@recordClick');



