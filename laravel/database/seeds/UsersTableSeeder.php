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
		   'name' => 'admin',
		   'email' => 'art4biz@mail.ru',
		   'password' => bcrypt('321321321'),
	   ]);
    }
}
