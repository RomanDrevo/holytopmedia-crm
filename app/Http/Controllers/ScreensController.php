<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadVideoRequest;
use App\Liantech\Repositories\SpotRepository;
use App\Models\Broker;
use App\Models\Deposit;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScreensController extends Controller
{

    public function __construct()
    {
        $this->middleware("permission:jackpot");
    }

    public function jackpot()
    {
        $lastWinners = collect([]);
        $currencies_symbols = \Config::get("liantech.currencies_symbols");

        $deposits = Deposit::byBroker()->where("receptionEmployeeId", "!=", 0)->with('employee','table')
            ->where('paymentMethod', '!=', 'Bonus')
            ->latest()->take(4)->get()->sortByDesc("id");
        $deposits->each(function($deposit) use($lastWinners, $currencies_symbols){
            $assigned_at = new Carbon($deposit->assigned_at);
            $lastWinners->push([
                "name" => $deposit->employee->name,
                "desk" => $deposit->employee->table ? $deposit->employee->table->name : "Unknown",
                "deskType" => $deposit->employee->table ? $deposit->employee->table->type : "Unknown",
                "time" => $assigned_at->setTimezone("Asia/Jerusalem")->format("H:i"),
                "amount" => number_format(intval($deposit->amount)),
                "amountType" => $currencies_symbols[$deposit->currency],
                "amountUSD" => number_format(intval($deposit->amount * $deposit->rateUSD)),
                "intAmount" => intval($deposit->amount),
                "animation" => "animated tada",
                "photo" =>  $deposit->employee->image
            ]);

        });
        $videos_src = json_encode([
            "small_deposit" =>  Setting::where("option_name", "small_deposit")->first(),
            "big_deposit"   =>  Setting::where("option_name", "big_deposit")->first()
        ]);

        return view('pages.sales.jackpot', compact('lastWinners', 'videos_src'));
    }

    /**
     * Using the SpotRepository to generate stats
     * 
     * @return Array
     */
    public function getStats()
    {
        $broker = Broker::where("name", request("broker"))->first();
        return (new SpotRepository)->getStats($broker);
    }

    /**
     * Once a video has been uploaded we store it's
     * name in our database and the path in the
     * videos directory
     * 
     * @param  Request $request 
     * @return Response
     */
    public function storeVideo(Request $request){

        $videoUploaded = false;

        $videoNames = [
            "small_video"   =>  "small_deposit", 
            "big_video"     =>  "big_deposit"
        ];

        foreach ($videoNames as $fieldName => $settingName) {
            if($request->file($fieldName) && $request->$fieldName->getClientOriginalExtension() == "mp4"){
                $videoUploaded = true;
                $path = $request->$fieldName->store('winner-videos', 'video');
                $setting = Setting::where('option_name', $settingName)->first();
                $setting->option_value = $path;
                $setting->save();
            }
        }

        if(!$videoUploaded)
            return response([["No video has been uploaded."]], 401);

        return response("ok", 200);
    }
}
