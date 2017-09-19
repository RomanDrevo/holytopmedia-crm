<?php

use Illuminate\Database\Seeder;

class BrokersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brokers')->insert([
            'name' => 'ivory_option',
            'platform' => 'spot',
            'url_name' => 'https://api-spotplatform.ivoryoption.com/Api',
        ]);
        DB::table('brokers')->insert([
            'name' => '72_option',
            'platform' => 'spot',
            'url_name' => 'https://api-spotplatform.72option.com/Api'
        ]);
        DB::table('brokers')->insert([
            'name' => 'roiteks',
            'platform' => 'panda',
            'url_name' => 'https://no.pandats-api.com',
        ]);
    }
}
