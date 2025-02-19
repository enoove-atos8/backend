<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcclesiasticalDivisionsGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserir dados nas divisões eclesiásticas
        // Ajuste os IDs conforme os valores reais inseridos na tabela ecclesiastical_divisions
        $divisionIds = DB::table('ecclesiastical_divisions')
            ->pluck('id', 'name')
            ->toArray();

        // Verifique os IDs no seu ambiente e ajuste conforme necessário
        $ministryId = $divisionIds['Ministérios'];
        $departmentId = $divisionIds['Departamentos'];
        $organizationId = $divisionIds['Organizações'];
        $eventId = $divisionIds['Eventos'];

        DB::table('ecclesiastical_divisions_groups')->insert([
            // Área de Louvor
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_group_id' => null,
                'name' => 'Louvor',
                'description' => 'Ministério de louvor e adoração',
                'slug' => 'louvor',
                'financial_transactions_exists' => 1,
                'enabled' => 1,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_group_id' => null,
                'name' => 'Familia',
                'description' => 'Ministério da Familia',
                'slug' => 'familia',
                'financial_transactions_exists' => 1,
                'enabled' => 1,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_group_id' => null,
                'name' => 'Evangelismo e Missoes',
                'description' => 'Evangelismo e Missoes',
                'slug' => 'evangelismo-e-missoes',
                'financial_transactions_exists' => 1,
                'enabled' => 1,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
        ]);
    }
}
