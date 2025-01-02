<?php

namespace Database\Seeders\Central;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            ['name' => 'trial', 'description' => 'Plan with limitation of 15 days', 'price' => 0, 'activated' =>  true],
            ['name' => 'bronze', 'description' => 'Plan without limitation of until 100 members', 'price' => 199, 'activated' =>  true],
            ['name' => 'silver', 'description' => 'Plan without limitation of until 200 members', 'price' => 299, 'activated' =>  true],
            ['name' => 'gold', 'description' => 'Plan without limitation above 300 members', 'price' => 399, 'activated' =>  true],
        ]);
    }
}
