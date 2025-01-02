<?php

namespace Database\Seeders;

use Database\Seeders\Central\FunctionalitiesSeeder;
use Database\Seeders\Central\PlanSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            FunctionalitiesSeeder::class,
        ]);
    }
}
