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
                'route_resource' => 'ministries',
                'name' => 'Ministérios',
                'description' => 'Ministérios da igreja',
                'enabled' => 1,
            ],
            [
                'route_resource' => 'departures',
                'name' => 'Departamentos',
                'description' => 'Departamentos da igreja',
                'enabled' => 1,
            ],
            [
                'route_resource' => 'organizations',
                'name' => 'Organizações',
                'description' => 'Organizações associadas',
                'enabled' => 1,
            ],
            [
                'route_resource' => 'events',
                'name' => 'Eventos',
                'description' => 'Eventos da igreja',
                'enabled' => 1,
            ],
            [
                'route_resource' => 'projects',
                'name' => 'Projetos',
                'description' => 'Projetos sociais ou internos da igreja',
                'enabled' => 1,
            ],
            [
                'route_resource' => 'campaigns',
                'name' => 'Campanhas',
                'description' => 'Campanhas de arrecadação para investimentos, aquisição de bens ou afins',
                'enabled' => 1,
            ],
            [
                'route_resource' => 'education',
                'name' => 'Educação',
                'description' => 'Educação cristã e formação',
                'enabled' => 1,
            ],
        ]);
    }
}
