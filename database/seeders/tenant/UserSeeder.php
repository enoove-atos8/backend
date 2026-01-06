<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Cria apenas o usuário de sistema se não existir
        DB::table('users')->updateOrInsert(
            ['id' => 1], // Busca por ID
            [
                'email' => 'system@atos8.com',
                'password' => bcrypt('system@'.uniqid()),
                'activated' => 1,
                'type' => 'system',
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                'updated_at' => now(),
            ]
        );
    }
}
