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
        DB::table('users')->insert([
            'name' => 'Syaiful Shah Zinan',
            'username' => 'syaifulsz',
            'email' => 'syaifulsz@ragnaro.kz',
            'active' => 1,
            'password' => bcrypt('syaifulsz'),
        ]);
    }

    public function drop()
    {
        DB::table('users')->delete();
    }
}
