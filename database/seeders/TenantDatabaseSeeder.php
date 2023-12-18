<?php

namespace Database\Seeders;

use Database\Seeders\tenant\RolesAndPermissionsSeeder;
use Database\Seeders\tenant\UserSeeder;
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
            //UserSeeder::class,
            RolesAndPermissionsSeeder::class
        ]);
    }
}
