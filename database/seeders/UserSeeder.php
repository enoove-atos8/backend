<?php

namespace Database\Seeders;

use Domain\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['email' => 'admin@odonto.solutions', 'password' => bcrypt('123456'), 'activated' => '1'],
            ['email' => 'qjohnson@example.org', 'password' => bcrypt('123456'), 'activated' => '1'],
            ['email' => 'ansel.greenfelder@example.net', 'password' => bcrypt('123456'), 'activated' => '1'],
            ['email' => 'weissnat.casey@example.org', 'password' => bcrypt('123456'), 'activated' => '1'],
            ['email' => 'olockman@example.org', 'password' => bcrypt('123456'), 'activated' => '1'],
        ]);
    }
}
