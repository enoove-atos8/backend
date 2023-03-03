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
            ['name' => 'admin', 'type' => 'admin','description' => 'This role grants the user all the abilities of system', 'activated' => 1],
            ['name' => 'doctor', 'type' => 'employee','description' => 'This role grants the user all the abilities of a doctor', 'activated' => 1],
            ['name' => 'receptionist', 'type' => 'employee','description' => 'This role grants the user all the abilities of a receptionist', 'activated' => 1],
            ['name' => 'patient', 'type' => 'patient','description' => 'This role grants the user all the abilities of a patient', 'activated' => 1],
        ]);
    }
}
