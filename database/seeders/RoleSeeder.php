<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'type' => 'admin',
                'description' => 'This role grants the user all the abilities of system',
                'activated' => 1
            ],
            [
                'name' => 'shepherd',
                'type' => 'shepherd',
                'description' => 'This role grants the user all skills related to the Shepherd profile',
                'activated' => 1
            ],
            [
                'name' => 'patrimony_minister',
                'type' => 'minister',
                'description' => 'This role grants the user all skills related to the minister profile',
                'activated' => 1
            ],
            [
                'name' => 'secretary',
                'type' => 'secretary',
                'description' => 'This role grants the user all skills related to the secretary profile',
                'activated' => 1
            ],
            [
                'name' => 'accountant',
                'type' => 'accountant',
                'description' => 'This role grants the user all skills related to the accountant profile',
                'activated' => 1
            ],
        ]);
    }
}
