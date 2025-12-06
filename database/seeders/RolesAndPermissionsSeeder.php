<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Reset cached roles and permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'create',
            'read',
            'update',
            'delete',
        ];
        $roles = [
            'admin',
            'user',
            'car-owner',
        ];
        $entities = [
            'users',
            'roles',
            'reports',
            'sales',
            'settings',
            'invoices',
            'products',
            'departments',
            'staff',
        ];

        foreach ($permissions as $permission) {
            foreach ($entities as $entity) {
                 Permission::firstOrCreate(['name' => $permission.'-'.$entity ]);
            }

        }

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $role = Role::findByName('admin');
        $role->givePermissionTo(Permission::all());
        $adminRole = User::find(1)->assignRole('admin');

        $roleUser = Role::findByName('user');
        $roleUser->givePermissionTo(Permission::all());
        $userRole = User::find(2)->assignRole('user');

}


        }
