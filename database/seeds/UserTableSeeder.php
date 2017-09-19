<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Polina',
            'email' => 'polinas@liantechltd.com',
            'password' => bcrypt('polina1234'),
        ]);
        DB::table('users')->insert([
            'name' => 'Afik Deri',
            'email' => 'afikd@liantechltd.com',
            'password' => bcrypt('Aa123123!'),
        ]);
        DB::table('users')->insert([
            'name' => 'Roman S',
            'email' => 'romans@liantechltd.com',
            'password' => bcrypt('roman1234'),
        ]);
    }
}
