<?php

namespace App\Liantech\Classes\SMSRules;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
/**
* 
*/
abstract class SMSMaster{

    public function extractValidPhones($customers = [])
    {

        $phones = array();
        $phoneUtil =  PhoneNumberUtil::getInstance();

        foreach ($customers as $customer) {
            try{
                $phone = "+" . (ltrim($customer->customer_phone, "+"));
                $phoneObj = $phoneUtil->parse($phone, 'CH');
                if($phoneUtil->isValidNumber($phoneObj)) {
                    $phones[] = $phoneUtil->format($phoneObj, PhoneNumberFormat::E164);
                }
            }catch(\Exception $e){
                continue;
            }
        }

        return $phones;   
    }

}