<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcclesiasticalDivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ecclesiastical_divisions')->insert([
            [
                'slug' => 'ministries',
                'name' => 'Ministérios',
                'description' => 'Ministérios da igreja',
                'enabled' => 1,
            ],
            [
                'slug' => 'departures',
                'name' => 'Departamentos',
                'description' => 'Departamentos da igreja',
                'enabled' => 1,
            ],
            [
                'slug' => 'organizations',
                'name' => 'Organizações',
                'description' => 'Organizações associadas',
                'enabled' => 1,
            ],
            [
                'slug' => 'events',
                'name' => 'Eventos',
                'description' => 'Eventos da igreja',
                'enabled' => 1,
            ],
            [
                'slug' => 'projects',
                'name' => 'Projetos',
                'description' => 'Projetos sociais ou internos da igreja',
                'enabled' => 1,
            ],
            [
                'slug' => 'campaigns',
                'name' => 'Campanhas',
                'description' => 'Campanhas de arrecadação para investimentos, aquisição de bens ou afins',
                'enabled' => 1,
            ],
        ]);
    }
}
