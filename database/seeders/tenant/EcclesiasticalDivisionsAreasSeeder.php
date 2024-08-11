<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcclesiasticalDivisionsAreasSeeder extends Seeder
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

        DB::table('ecclesiastical_divisions_areas')->insert([
            // Área de Louvor
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_area_id' => null,
                'financial_transactions_exists' => 1,
                'departments_exists' => 1,
                'events_exists' => 1,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Louvor',
                'description' => 'Ministério de louvor e adoração',
                'ministry_linked' => 0,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_area_id' => null,
                'financial_transactions_exists' => 1,
                'departments_exists' => 1,
                'events_exists' => 1,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Família',
                'description' => 'Ministério da família',
                'ministry_linked' => 0,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_area_id' => null,
                'financial_transactions_exists' => 1,
                'departments_exists' => 1,
                'events_exists' => 1,
                'organizations_exists' => 1,
                'enabled' => 1,
                'name' => 'Evangelismo e Missões',
                'description' => 'Ministério de Evangelismo e Missões',
                'ministry_linked' => 0,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_area_id' => null,
                'financial_transactions_exists' => 1,
                'departments_exists' => 1,
                'events_exists' => 1,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Ação Social',
                'description' => 'Ministério de Ação Social',
                'ministry_linked' => 0,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_area_id' => null,
                'financial_transactions_exists' => 1,
                'departments_exists' => 0,
                'events_exists' => 1,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Libras',
                'description' => 'Ministério de Libras',
                'ministry_linked' => 0,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_area_id' => null,
                'financial_transactions_exists' => 1,
                'departments_exists' => 1,
                'events_exists' => 1,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Educação Cristã',
                'description' => 'Ministério de Educação Cristã',
                'ministry_linked' => 0,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $ministryId,
                'parent_area_id' => null,
                'financial_transactions_exists' => 1,
                'departments_exists' => 1,
                'events_exists' => 0,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Comunicação Criativa',
                'description' => 'Ministério de Comunicação Criativa',
                'ministry_linked' => 0,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            // Exemplo de área com subárea
            [
                'ecclesiastical_division_id' => $departmentId,
                'parent_area_id' => 6,
                'financial_transactions_exists' => 1,
                'departments_exists' => 1,
                'events_exists' => 0,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Departamento Infantil',
                'description' => 'Departamento Infantil',
                'ministry_linked' => 1,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
            [
                'ecclesiastical_division_id' => $departmentId,
                'parent_area_id' => 7,
                'financial_transactions_exists' => 1,
                'departments_exists' => 0,
                'events_exists' => 0,
                'organizations_exists' => 0,
                'enabled' => 1,
                'name' => 'Departamento de Mídia',
                'description' => 'Departamento de Mídia',
                'ministry_linked' => 1,
                'temporary_event' => null,
                'start_date' => null,
                'end_date' => null,
            ],
        ]);
    }
}
