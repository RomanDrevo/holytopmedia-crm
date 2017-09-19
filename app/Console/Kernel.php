<?php

namespace App\Console;

use App\Console\Commands\RiskAlertsBigDepositorNotContacted;
use App\Console\Commands\RiskAlertsDeclinedDeposits;
use App\Console\Commands\RiskAlertsMatchingKeywords;
use App\Console\Commands\RiskAlertsNotVerifiedCustomer;
use App\Console\Commands\UpdateCallsCommand;
use App\Console\Commands\UpdateCampaigns;
use App\Console\Commands\UpdateCustomers;
use App\Console\Commands\UpdateCustomersRecords;
use App\Console\Commands\UpdateDepositsAndWithdrawals;
use App\Console\Commands\UpdateDepositsStatus;
use App\Console\Commands\UpdateEmployees;
use App\Console\Commands\UpdateWithdrawalsStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateDepositsAndWithdrawals::class,
        UpdateEmployees::class,
        UpdateCustomers::class,
        UpdateCustomersRecords::class,
        RiskAlertsMatchingKeywords::class,
        RiskAlertsDeclinedDeposits::class,
        RiskAlertsNotVerifiedCustomer::class,
        RiskAlertsBigDepositorNotContacted::class,
        UpdateCallsCommand::class,
        UpdateCampaigns::class,
        UpdateWithdrawalsStatus::class,
        UpdateDepositsStatus::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Update calls records
        $schedule->command('liantech:update-calls')->hourly()->sendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('liantech:update-customers')->everyMinute()->withoutOverlapping()->sendOutputTo(storage_path('logs/cron.log'));
        $schedule->command('liantech:update-records')->everyMinute()->withoutOverlapping()->sendOutputTo(storage_path('logs/cron.log'));
        $schedule->command('liantech:update-employees')->everyMinute()->withoutOverlapping()->sendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('liantech:update-campaigns')->everyTenMinutes()->withoutOverlapping()->sendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('liantech:update-existing-customers-records')->dailyAt('00:00')->sendOutputTo(storage_path('logs/cron.log'));

        $schedule->command('liantech:update-withdrawals-status')->everyTenMinutes()->withoutOverlapping()->sendOutputTo(storage_path('logs/cron.log'));
        $schedule->command('liantech:update-deposits-status')->everyTenMinutes()->withoutOverlapping()->sendOutputTo(storage_path('logs/cron.log'));

        //alerts
        //$schedule->command('liantech:risk-alert-declined-deposits')->twiceDaily(8, 18)->sendOutputTo(storage_path('logs/cron.log'));
        $schedule->command('liantech:risk-alert-not-verified-customer')->twiceDaily(8, 18)->sendOutputTo(storage_path('logs/cron.log'));
        $schedule->command('liantech:risk-alert-big-depositor-not-contacted')->twiceDaily(8, 18)->sendOutputTo(storage_path('logs/cron.log'));
        $schedule->command('liantech:risk-alert-matching-keywords')->twiceDaily(8, 18)->sendOutputTo(storage_path('logs/cron.log'));
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
