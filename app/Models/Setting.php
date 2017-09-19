<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function getArrayOfSettings()
    {
        $allSettings = Setting::all();

        $settings = array();

        foreach ($allSettings as $singleSetting) {
            $settings[$singleSetting->option_name] = $singleSetting->option_value;
        }
        return $settings;
    }
}
