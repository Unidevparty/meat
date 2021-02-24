<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(['name' => 'admin']);
        DB::table('roles')->insert(['name' => 'user']);
        DB::table('roles')->insert(['name' => 'manager']);

        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 1,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 2,
        ]);
    }
}
