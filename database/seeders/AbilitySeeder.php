<?php

namespace Database\Seeders;

use App\Domain\Users\SubDomains\Abilities\Models\Ability;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('abilities')->insert([
            ['name' => 'create-appointment', 'description' => 'This ability grants the user can create a appointment to a doctor.', 'activated' => 1],
            ['name' => 'list-appointment', 'description' => 'This ability grants the user can list all appointment of a doctor.', 'activated' => 1],
            ['name' => 'edit-appointment', 'description' => 'This ability grants the user can edit a appointment to a doctor.', 'activated' => 1],
            ['name' => 'delete-appointment', 'description' => 'This ability grants the user can delete a appointment to a doctor.', 'activated' => 1],
            ['name' => 'create-patient', 'description' => 'This ability grants the user can create a new patient in system.', 'activated' => 1],
            ['name' => 'list-patient', 'description' => 'This ability grants the user can list all patient in system.', 'activated' => 1],
            ['name' => 'edit-patient', 'description' => 'This ability grants the user can edit an existing patient in system.', 'activated' => 1],
            ['name' => 'delete-patient', 'description' => 'This ability grants the user can delete an existing patient in system.', 'activated' => 1],
            ['name' => 'start-attendance', 'description' => 'This ability grants the user can start a new attendance, change status of appointment on system.', 'activated' => 1],
            ['name' => 'end-attendance', 'description' => 'This ability grants the user can end a attendance, change status of appointment on system.', 'activated' => 1],
        ]);
    }
}
