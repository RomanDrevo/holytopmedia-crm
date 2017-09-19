<?php

namespace App\Liantech\Classes\SMSRules;

use Carbon\Carbon;

interface SMSRule{

    public function getPhones($customers);

    public function getCustomers(Carbon $from = null);

}