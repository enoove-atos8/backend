<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('financial_settings')->insert([
            ['monthly_budget_tithes' => 40000, 'budget_activated' => true],
        ]);
    }
}
