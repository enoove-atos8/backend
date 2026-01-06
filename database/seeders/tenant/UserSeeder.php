<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'email' => 'system@atos8.com',
                'password' => bcrypt('system@'.uniqid()),
                'activated' => 1,
                'type' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'admin@atos8.com',
                'password' => bcrypt('123456'),
                'activated' => 1,
                'type' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
