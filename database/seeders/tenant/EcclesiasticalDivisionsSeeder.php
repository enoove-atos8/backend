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
                'name' => 'Ministérios',
                'description' => 'Ministérios da igreja',
                'enabled' => 1,
            ],
            [
                'name' => 'Departamentos',
                'description' => 'Departamentos da igreja',
                'enabled' => 1,
            ],
            [
                'name' => 'Organizações',
                'description' => 'Organizações associadas',
                'enabled' => 1,
            ],
            [
                'name' => 'Eventos',
                'description' => 'Eventos da igreja',
                'enabled' => 1,
            ],
        ]);
    }
}
