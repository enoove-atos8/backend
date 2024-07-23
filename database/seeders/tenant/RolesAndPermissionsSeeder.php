<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        //app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create roles and assign created permissions

        // this can be done as separate statements
        Role::create(
            [
                'name'          => 'admin',
                'guard_name'    => 'web',
                'display_name'  => 'Administrador',
                'description'   => 'Acesso a todas as funcionalidades do sistema',
                'activated'     => 1,
            ]);

        Role::create(
            [
                'name'          => 'pastor',
                'guard_name'    => 'web',
                'display_name'  => 'Pastor',
                'description'   => 'Acesso a todas as funcionalidades do sistema',
                'activated'     => 1,
            ]);

        Role::create(
            [
                'name'          => 'treasury',
                'guard_name'    => 'web',
                'display_name'  => 'Tesouraria',
                'description'   => 'Acesso aos módulos de tesouraria',
                'activated'     => 1,
            ]);

        Role::create(
            [
                'name'          => 'patrimony',
                'guard_name'    => 'web',
                'display_name'  => 'Patrimônio',
                'description'   => 'Acesso aos módulos de patrimônio',
                'activated'     => 1,
            ]);

        Role::create(
            [
                'name'          => 'secretary',
                'guard_name'    => 'web',
                'display_name'  => 'Secretaria',
                'description'   => 'Acesso aos módulos de secretaria.',
                'activated'     => 1,
            ]);
    }
}
