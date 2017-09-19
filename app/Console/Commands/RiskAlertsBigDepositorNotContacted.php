<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Models\Setting;
use App\Models\Broker;
use Carbon\Carbon;

class RiskAlertsBigDepositorNotContacted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:risk-alert-big-depositor-not-contacted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look for big depositor that has not been contacted more then 10 days';

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
        //get data from compliance settings table
        $sinceSetting = Setting::where('option_name', 'not_contacted_since')->first();
        $createCommentDate = new Carbon($sinceSetting->option_value);

        $lastDepositSetting = Setting::where('option_name', 'not_contacted_last_deposit')->first();
        $lastDepositDate = new Carbon($lastDepositSetting->option_value);
        $totalSetting = Setting::where('option_name', 'big_depositor_total')->first();
        $totalDeposits = intval($totalSetting->option_value);


        $brokers = Broker::where("platform", "spot")->get();


        foreach ($brokers as $broker){

            $alertCustomerIds = Alert::where('broker_id', $broker->id)->where("type", 4)->pluck("customer_crm_id")->toArray();

            $subQuery = \DB::connection("spot_db_" . $broker->name)->table("customers")
                ->select("customers.id as customerId", \DB::raw("COUNT(customer_communications.id) as totalComments"))
                ->join("customer_communications", "customers.id", "=", "customer_communications.customerId")
                ->where(function($query) use ($createCommentDate, $lastDepositDate){
                    $query->where("customer_communications.createDate", ">=", $createCommentDate);
                    $query->where("customers.lastDepositDate", ">=", $lastDepositDate);
                })
                ->groupBy("customers.id");



            $notContactedCustomers = \DB::connection("spot_db_" . $broker->name)->table("customers")
                ->select("customers.id as customerId", \DB::raw("SUM(customer_deposits.amount) as totalDeposits"))
                ->join("customer_deposits", "customers.id", "=", "customer_deposits.customerId")
                ->where("customers.lastDepositDate", ">=", Carbon::now()->subMonths(3))
                ->whereNotIn("customer_deposits.paymentMethod", ['Bonus', 'Qiwi', 'AlertPay'])
                ->whereNotIn("customers.id", function($query) use($subQuery){
                    $query->select("customerId")
                        ->from( \DB::raw("({$subQuery->toSql()}) as DD") );
                })
                ->mergeBindings($subQuery)
                ->groupBy("customers.id")
                ->having("totalDeposits", ">=", $totalDeposits)
                ->get();

            foreach ($notContactedCustomers as $customer) {
                if(!in_array($customer->customerId, $alertCustomerIds)) {
                    Alert::create([
                        "customer_crm_id"   =>  $customer->customerId,
                        "broker_id"         =>  $broker->id,
                        "type"              =>  4,
                        "subject"           =>  "Customer has not been contacted!",
                        "content"           =>  "Big depositor has not been contacted! Depositor ID is: "  . $customer->customerId
                    ]);
                }

            }
        }


    }
}
