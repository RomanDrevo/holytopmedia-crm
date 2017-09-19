<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Setting;
use App\Models\Broker;
use Carbon\Carbon;
use App\Models\Alert;
use Illuminate\Console\Command;
use Config;

class RiskAlertsDeclinedDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:risk-alert-declined-deposits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look for declined deposits';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currencies = Config::get('liantech.currencies_symbols');

        $sinceSetting = Setting::where('option_name', 'declined_since')->first();
        $sinceDate = new Carbon($sinceSetting->option_value);
        $deposits = Deposit::where("status", "!=", "approved")->where('confirmTime', '>=', $sinceDate)->get();



        $brokers = Broker::where("platform", "spot")->get();

        foreach ($brokers as $broker){

            $alertCustomerIds = Alert::where('broker_id', $broker->id)->where("type", 3)->pluck("customer_crm_id")->toArray();

            foreach ($deposits as $deposit) {

                if(!in_array($deposit->customerId, $alertCustomerIds)){

                    Alert::create([
                        "customer_crm_id"   =>  $deposit->customerId,
                        "broker_id" => $broker->id,
                        "type"          =>  3,
                        "subject"       =>  "Declined deposit!",
                        "content"       =>  "Declined deposit has been found! Customer ID is: "
                            . $deposit->customerId . ", amount is " . $currencies[$deposit->currency] . number_format($deposit->amount)
                    ]);
                }

            }
        }



    }
}
