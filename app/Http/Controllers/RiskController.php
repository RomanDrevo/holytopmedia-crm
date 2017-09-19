<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Alert;
use DB;

class RiskController extends Controller
{
    public function index()
    {
        return view('pages.compliance.alerts');
    }

    public function get(Request $request)
    {
        $alerts = Alert::with("customer")
            ->ByBroker()
            ->where(function ($query) use ($request) {
                if ($request->type) {
                    $query->where('type', $request->type);
                }
                if ($request->search) {
                    $query->where('customer_crm_id', 'LIKE', '%' . $request->search . '%');
                }

            })
            ->paginate(25);

        return $alerts;
    }

    public function destroy($alertId)
    {

        $alert = Alert::find($alertId);

        if (is_null($alert)) {
            return response("Not found", 403);
        }

        $alert->delete();

        return response("OK, alert has been deleted", 200);
    }


    public function settings()
    {

        $settings = Setting::getArrayOfSettings();
        $removeKeys = array('GBP/USD', 'EUR/USD', 'USD/NIS', 'USD/USD', 'monthly_goal');
        foreach ($removeKeys as $key) {
            unset($settings[$key]);
        }
        $settings = json_encode($settings);
        return view("pages.compliance.settings", compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $result = $request->all();

        foreach ($result as $key => $value) {
            $this->findAndSetSetting($key, $value);
        }
        return 'ok';


    }

    private function findAndSetSetting($key, $value)
    {
        if(isset($key)) {
            $setting = Setting::where('option_name', $key)->first();
            $setting->option_value = $value;
            $setting->save();
        }
    }

}
