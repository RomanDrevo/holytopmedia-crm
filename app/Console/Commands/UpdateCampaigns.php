<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use Illuminate\Console\Command;
use App\Models\Broker;

class UpdateCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:update-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all the campaigns from the Replica CRM';

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
        $brokers = Broker::where("platform", "spot")->get();
        foreach ($brokers as $broker) {
            try {
                $remoteCampaigns = \DB::connection('spot_db_' . $broker->name)->table("campaigns")->get();
                foreach ($remoteCampaigns as $camp) {
                    $localCampaign = Campaign::where("broker_id", $broker->id)->where('campaign_crm_id', $camp->id)->first();
                    if (is_null($localCampaign)) {
                        //Create a new campaign
                        $localCampaign = new Campaign();

                        $localCampaign->campaign_crm_id = $camp->id;
                        $localCampaign->broker_id = $broker->id;
                        $localCampaign->name = $camp->name;
                        $localCampaign->total_deposits = $camp->totalDeposits;

                        if ($camp->createDate == '0000-00-00 00:00:00') {
                            $localCampaign->create_date = null;
                        } else {
                            $localCampaign->create_date = $camp->createDate;
                        }
                        $localCampaign->save();
                    }
                }

            } catch (\Exception $e) {
                \Log::error("Log from cron message: " . $e->getMessage());
            }

        }
        return true;
    }

}
