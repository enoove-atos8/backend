<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\EcclesiasticalDivisionsAreasSeeder;
use Database\Seeders\Tenant\EcclesiasticalDivisionsSeeder;
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
            EcclesiasticalDivisionsAreasSeeder::class,
        ]);
    }
}
