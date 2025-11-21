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
            [
                'name' => 'bronze',
                'description' => 'Plan for churches up to 100 members',
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
                ])
            ],
            [
                'name' => 'silver',
                'description' => 'Plan for churches up to 250 members',
                'price' => 397.00,
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
                ])
            ],
            [
                'name' => 'gold',
                'description' => 'Plan for churches up to 350 members',
                'price' => 597.00,
                'activated' => true,
                'stripe_product_id' => null,
                'stripe_price_id' => null,
                'billing_interval' => 'month',
                'trial_period_days' => 15,
                'features' => json_encode([
                    'members_limit' => 350,
                    'storage_gb' => 100,
                    'basic_reports' => true,
                    'advanced_reports' => true,
                    'priority_support' => true,
                ])
            ],
            [
                'name' => 'max',
                'description' => 'Custom plan for churches with more than 350 members - charged per member',
                'price' => 1.80,
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
                    'unit_price' => 1.80,
                    'minimum_members' => 350,
                ])
            ],
        ]);
    }
}
