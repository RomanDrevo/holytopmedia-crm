<?php

namespace App\Liantech\Helpers;

use App\User;


class ComplianceEmployeesCalculator
{

    protected $user;

    function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getComments(){
        return 4;
    }


}