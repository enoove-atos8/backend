<?php

namespace Database\Seeders\Central;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'bronze',
                'description' => 'Plano para igrejas com até 100 membros',
                'price' => 197.00,
                'activated' => true,
                'stripe_product_id' => null,
                'stripe_price_id' => null,
                'billing_interval' => 'month',
                'trial_period_days' => 15,
                'features' => json_encode([
                    'members_limit' => 100,
                    'storage_gb' => 10,
                    'basic_reports' => true,
                    'advanced_reports' => false,
                    'priority_support' => false,
                ]),
            ],
            [
                'name' => 'silver',
                'description' => 'Plano para igrejas com até 250 membros',
                'price' => 347.00,
                'activated' => true,
                'stripe_product_id' => null,
                'stripe_price_id' => null,
                'billing_interval' => 'month',
                'trial_period_days' => 15,
                'features' => json_encode([
                    'members_limit' => 250,
                    'storage_gb' => 50,
                    'basic_reports' => true,
                    'advanced_reports' => true,
                    'priority_support' => false,
                ]),
            ],
            [
                'name' => 'gold',
                'description' => 'Plano para igrejas com até 399 membros',
                'price' => 497.00,
                'activated' => true,
                'stripe_product_id' => null,
                'stripe_price_id' => null,
                'billing_interval' => 'month',
                'trial_period_days' => 15,
                'features' => json_encode([
                    'members_limit' => 399,
                    'storage_gb' => 100,
                    'basic_reports' => true,
                    'advanced_reports' => true,
                    'priority_support' => true,
                ]),
            ],
            [
                'name' => 'diamond',
                'description' => 'Plano ilimitado - cobrança por membro',
                'price' => 2.00,
                'activated' => true,
                'stripe_product_id' => null,
                'stripe_price_id' => null,
                'billing_interval' => 'month',
                'trial_period_days' => 15,
                'features' => json_encode([
                    'members_limit' => null,
                    'storage_gb' => 500,
                    'basic_reports' => true,
                    'advanced_reports' => true,
                    'priority_support' => true,
                    'metered_billing' => true,
                    'unit_price' => 2.00,
                    'minimum_members' => 400,
                ]),
            ],
        ]);
    }
}
