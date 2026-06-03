<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Reset Permission Cache
        |--------------------------------------------------------------------------
        */
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        $permissions = [
            'view',
            'create',
            'delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin Role
        |--------------------------------------------------------------------------
        */
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $adminRole->syncPermissions($permissions);

        /*
        |--------------------------------------------------------------------------
        | Companies
        |--------------------------------------------------------------------------
        */
        $vmc = Company::firstOrCreate([
            'name' => 'VMC',
        ]);

        $nuqoosh = Company::firstOrCreate([
            'name' => 'Nuqoosh',
        ]);

        $hobs = Company::firstOrCreate([
            'name' => 'Hobs innovation',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */
        $admin = User::firstOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'name' => 'Admin',
                'password' => bcrypt('123456'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Assign Role
        |--------------------------------------------------------------------------
        */
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        /*
        |--------------------------------------------------------------------------
        | Attach Companies
        |--------------------------------------------------------------------------
        */
        $admin->companies()->syncWithoutDetaching([
            $vmc->id,
            $nuqoosh->id,
            $hobs->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Default Active Company
        |--------------------------------------------------------------------------
        */
        if (!$admin->active_company_id) {
            $admin->update([
                'active_company_id' => $vmc->id,
            ]);
        }
    }
}
