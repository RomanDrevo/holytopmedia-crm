<?php

namespace App\Console\Commands;
use App\Models\Alert;
use App\Models\Customer;
use App\Models\Broker;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Console\Command;

class RiskAlertsNotVerifiedCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:risk-alert-not-verified-customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look for not verified after 10 days 10.000$ customers';

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
        $lastDepositSetting = Setting::where('option_name', 'not_verified_last_deposit')->first();
        $lastDepositDate = new Carbon($lastDepositSetting->option_value);

        $totalSetting = Setting::where('option_name', 'not_verified_total')->first();
        $totalDeposits = intval($totalSetting->option_value);
        $brokers = Broker::where("platform", "spot")->get();

        foreach($brokers as $broker){
            $alertCustomerIds = Alert::where('broker_id', $broker->id)->where("type", 1)->pluck("customer_crm_id")->toArray();

            $bigDepositors = \DB::connection("spot_db_" . $broker->name)->table("customers")
                ->select("customers.id", \DB::raw("SUM(customer_deposits.id) as totalDeposits"))
                ->join("customer_deposits", "customers.id", "=", "customer_deposits.customerId")
                ->where(function($query) use ($lastDepositDate, $totalDeposits){
                    $query->where("customers.lastDepositDate", ">=", $lastDepositDate);
                    $query->where("customers.verification", "!=", 'Full');
                    $query->where("customer_deposits.amount", '>=', $totalDeposits);
                })
                ->groupBy("customers.id")->get();

            foreach ($bigDepositors as $depositor) {
                if(!in_array($depositor->id, $alertCustomerIds)) {
                    Alert::create([
                        "customer_crm_id"   =>  $depositor->id,
                        "broker_id" => $broker->id,
                        "type"          =>  1,
                        "subject"       =>  "Not verified customer!",
                        "content"       =>  "Big depositor is not verified! Depositor ID is: "  . $depositor->id
                    ]);
                }
            }
        }
    }
}
