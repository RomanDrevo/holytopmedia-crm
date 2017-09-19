<?php

use Illuminate\Database\Seeder;
use App\Models\Deposit;

class DepositsAssignAtColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Deposit::whereNull("assigned_at")->update([ "assigned_at" => \DB::raw('confirmTime') ]);
    }
}
