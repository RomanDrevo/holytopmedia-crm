<?php

namespace App\Liantech\Classes\SMSRules;

use Carbon\Carbon;

/**
* 
*/
class RSTSingleDeposit extends SMSMaster implements SMSRule
{   

    public function getPhones($customers)
    {
        //$customers = $this->getCustomers($from);
        return $this->extractValidPhones($customers);
    }

    public function getCustomers(Carbon $from = null)
    {
        $regtime = $from->toDateTimeString();
        $query = "SELECT C.id as customer_id, C.email as customer_email, C.regtime, C.Phone as customer_phone, C.cellphone as customer_cellphone, C.saleStatus as sales_status, C.Country as countryId, CU.name as country_name, COUNT(D.id) as depositsCount,  
                (
                    SELECT CSV.customValueName as customer_sales_status
                    FROM custom_settings_pairing CSP
                    JOIN custom_settings_types CST ON CST.id = CSP.customTypeId
                    JOIN custom_settings_values CSV ON CSV.id = CSP.CustomTypeValueId
                    WHERE CST.id = 1
                    AND CSP.targetEntityId = C.id
                    AND CSP.CustomTypeValueId NOT IN (0, -1)
                    AND CSV.customValueName NOT IN ('Risky', 'No English', 'Sleepy')
                    ORDER BY CSP.id DESC
                    LIMIT 1
                ) custom_sales_status

                FROM customers C
                JOIN users U On C.employeeInChargeId = U.id
                JOIN country CU On C.Country = CU.id
                JOIN customer_deposits D On C.id = D.customerId
                WHERE U.lastName = 'RST'
                AND U.firstName != 'Pool No English'
                AND C.Country IN (225, 224, 206, 205, 192, 187, 174, 98, 103, 72, 96, 150, 160, 129, 124, 122, 80, 58, 153, 197)
                AND C.saleStatus NOT IN ('noCall', 'checkNumber')
                AND C.isBlackList = 0
                AND C.regTime >= :regtime
                GROUP BY D.customerId
                HAVING COUNT(*) < 2";

        return collect(\DB::connection("spot_db_" . \Auth::user()->broker->name)->select(\DB::raw($query), [$regtime]));
    }
}