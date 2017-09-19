<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Broker;
use App\Models\Customer;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RiskAlertsMatchingKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:risk-alert-matching-keywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look for a specific keywords and notify if spotted';

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
        //get keywords from settings
        $keywords = Alert::getKeywords();
        $sinceSetting = Setting::where('option_name', 'keywords_since')->first();
        $createDate = new Carbon($sinceSetting->option_value);

        $brokers = Broker::where("platform", "spot")->get();

        foreach ($brokers as $broker) {
            $alertCustomerIds = Alert::where('broker_id', $broker->id)->where("type", 2)->pluck("customer_crm_id")->toArray();

            $comments = \DB::connection("spot_db_" . $broker->name)->table("customer_communications")
                ->where("createDate", ">=", $createDate)
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->orWhere("subject", "LIKE", "% {$keyword} %");
                        $query->orWhere("body", "LIKE", "% {$keyword} %");
                    }
                })
                ->get();


            foreach ($comments as $comment) {


                $customer = Customer::where('customer_crm_id', $comment->customerId)->where("broker_id", $broker->id)->first();
                if(!$customer){
                    continue;
                }

                $foundKeyword = "";

                foreach ($keywords as $keyword) {
                    $foundKeyword = "";
                    $subject = $this->messageProcessing($comment->subject);
                    $body = $this->messageProcessing($comment->body);
                    if (in_array($keyword, $subject) || in_array($keyword, $body) ) {


                        $foundKeyword = $keyword;
                        break;
                    }
                }
                if(!in_array($comment->customerId, $alertCustomerIds)){
                    Alert::create([
                        "customer_crm_id" => $comment->customerId,
                        "broker_id" => $broker->id,
                        "type" => 2,
                        "subject" => "Keyword Match!",
                        "content" => "The keyword <strong>" . $foundKeyword . "</strong> has been found for the customer id: " . $comment->customerId
                    ]);
                }

            }
        }


    }

    private function messageProcessing($subject)
    {
        $subject = strip_tags($subject);
        $subject = str_replace("\n", "", $subject);
        $subject = str_replace('&nbsp;', '', $subject);
        $subject = explode(" ", $subject);
        return array_map('strtolower', $subject);
    }

}
