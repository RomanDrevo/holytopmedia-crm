<?php

namespace App\Providers;

use App\Liantech\Classes\SMSRules\NewDepositors;
use App\Liantech\Classes\SMSRules\RSTSingleDeposit;
use Illuminate\Support\ServiceProvider;

class SMSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('rst_single_deposits', function ($app) {
            return new RSTSingleDeposit();
        });

        $this->app->bind('ftd_no_deposits', function ($app) {
            return new NewDepositors();
        });
    }
}
