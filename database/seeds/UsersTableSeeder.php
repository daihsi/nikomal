<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 10; $i <= 49; $i++) {
            DB::table('users')->insert([
                'name' => 'test'. $i,
                'email' => 'test'. $i. '@test'. $i. 'com',
                'password' => $i. $i. $i. $i,
            ]);
        }
    }
}
