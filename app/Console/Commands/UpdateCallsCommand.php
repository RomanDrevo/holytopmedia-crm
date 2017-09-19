<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateCallsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:update-calls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching all the records from our voice center API';

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
        
        //Create Guzzle client for the request
        $requestUrl = "https://api1.voicenter.co.il/hub/cdr";
        $client = new Client([
            'base_uri' => $requestUrl,
            'timeout'  => 60.0,
            'cookies' => true, 
        ]);

        //Set up the range for call dates
        $startDate = Carbon::now()->subHour()->format("Y-m-d\TH:i");
        $endDate = Carbon::now()->format("Y-m-d\TH:i");

        //List of required fields to get from the API
        $params = [
            "code"      =>  env('VOICE_CENTER_CODE'),
            "fromdate"  =>  $startDate,
            "todate"    =>  $endDate,
            "format"    =>  'json',
            "Fields"    =>  [
                "CallerNumber", 
                "DID", 
                "Date", 
                "CallerExtension", 
                "TargetNumber", 
                "TargetExtension", 
                "TargetPrefixName", 
                "Duration", 
                "RepresentativeName", 
                "RepresentativeCode", 
                "CallID", 
                "DialStatus", 
                "DialStatus2"
            ]
        ];

        //Make the request
        $res = $client->get("?" . http_build_query($params));

        //Return if not status 200
        if($res->getStatusCode() != 200){
            \Log::error("Could not connect to voice center (Server error)");
            return false;
        }

        //Parse the calls content
        $callsResponse = json_decode($res->getBody()->getContents(), true);

        //Return if response is not good
        if($callsResponse["ERROR_NUMBER"] != 0){
            \Log::error("Could not connect to voice center (Internal error)");
            return false;
        }

        //Get the calls array from the response
        $calls = $callsResponse["CDR_LIST"];

        //Save calls to db
        foreach ($calls as $call) {
            $lastId = \DB::table("calls")->insert([
                "CallID"                =>  $call["CallID"],
                "Date"                  =>  $call["Date"],
                "DID"                   =>  $call["DID"],
                "CallerNumber"          =>  $call["CallerNumber"],
                "TargetNumber"          =>  $call["TargetNumber"],
                "TargetPrefixName"      =>  $call["TargetPrefixName"],
                "Duration"              =>  $call["Duration"],
                "RepresentativeName"    =>  $call["RepresentativeName"],
                "RepresentativeCode"    =>  $call["RepresentativeCode"],
                "DialStatus"            =>  $call["DialStatus"],
                "DialStatus2"           =>  $call["DialStatus2"]
            ]);

            if(!$lastId){
                \Log::error("Could not save phone recored " . $lastId);
            }
        }

    }
}
