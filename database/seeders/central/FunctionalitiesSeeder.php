<?php

namespace Database\Seeders\Central;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FunctionalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('functionalities')->insert([
            ['plan_id' => 1, 'name' => 'common-features', 'display_name' => 'Funcionalidades de gestão eclesiástica', 'descriptions' =>  'Funcionalidades gerais de gestão eclesiástica', 'activated' => true],
            ['plan_id' => 2, 'name' => 'common-features', 'display_name' => 'Funcionalidades de gestão eclesiástica', 'descriptions' =>  'Funcionalidades gerais de gestão eclesiástica', 'activated' => true],
            ['plan_id' => 3, 'name' => 'common-features', 'display_name' => 'Funcionalidades de gestão eclesiástica', 'descriptions' =>  'Funcionalidades gerais de gestão eclesiástica', 'activated' => true],
            ['plan_id' => 4, 'name' => 'common-features', 'display_name' => 'Funcionalidades de gestão eclesiástica', 'descriptions' =>  'Funcionalidades gerais de gestão eclesiástica', 'activated' => true],
            ['plan_id' => 4, 'name' => 'cloud-repository-entries-receipts', 'display_name' => 'Processamento automático de entradas gerais', 'description' =>  'Funcionalidade de processamento automático de comprovantes de entradas gerais', 'activated' => true],
            ['plan_id' => 4, 'name' => 'management-bank-statements', 'display_name' => 'Gestão de extratos bancários', 'description' =>  'Gestão de extratos bancários com atribuição de conta a entradas e saídas gerais', 'activated' => true],
            ['plan_id' => 4, 'name' => 'semiauto-bank-reconciliation', 'display_name' => 'Conciliação bancária semi-automática', 'description' =>  'Conciliação bancária automática com base na disponibilização do extrato bancário.', 'activated' => true],
        ]);
    }
}
