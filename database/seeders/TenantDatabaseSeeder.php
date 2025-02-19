<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\EcclesiasticalDivisionsGroupsSeeder;
use Database\Seeders\Tenant\EcclesiasticalDivisionsSeeder;
use Database\Seeders\Tenant\FinancialSettingsSeeder;
use Database\Seeders\tenant\RolesAndPermissionsSeeder;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            EcclesiasticalDivisionsSeeder::class,
            //EcclesiasticalDivisionsGroupsSeeder::class,
            FinancialSettingsSeeder::class
        ]);
    }
}
