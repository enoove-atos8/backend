<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('abilities')->insert([
            [
                'name' => 'all',
                'description' => 'This ability grants the user all access system',
                'activated' => 1
            ],
            [
                'name' => 'finance_access',
                'description' => 'This ability grants the user finance access abilities',
                'activated' => 1
            ],
            [
                'name' => 'finance_consult',
                'description' => 'This ability grants the user finance consult abilities',
                'activated' => 1
            ],
            [
                'name' => 'finance_edit',
                'description' => 'This ability grants the user finance edit abilities',
                'activated' => 1
            ],
            [
                'name' => 'patrimony_access',
                'description' => 'This ability grants the user patrimony access abilities',
                'activated' => 1
            ],
            [
                'name' => 'patrimony_consult',
                'description' => 'This ability grants the user patrimony consult abilities',
                'activated' => 1
            ],
            [
                'name' => 'patrimony_edit',
                'description' => 'This ability grants the user patrimony edit abilities',
                'activated' => 1
            ],
            [
                'name' => 'secretary_access',
                'description' => 'This ability grants the user secretary access abilities',
                'activated' => 1
            ],
            [
                'name' => 'secretary_consult',
                'description' => 'This ability grants the user secretary consult abilities',
                'activated' => 1
            ],
            [
                'name' => 'secretary_insert',
                'description' => 'This ability grants the user secretary insert abilities',
                'activated' => 1
            ],
            [
                'name' => 'secretary_edit',
                'description' => 'This ability grants the user secretary edit abilities',
                'activated' => 1
            ],
            [
                'name' => 'accountant_access',
                'description' => 'This ability grants the user accountant access abilities',
                'activated' => 1
            ],
            [
                'name' => 'accountant_consult',
                'description' => 'This ability grants the user accountant consult abilities',
                'activated' => 1
            ]
        ]);
    }
}
