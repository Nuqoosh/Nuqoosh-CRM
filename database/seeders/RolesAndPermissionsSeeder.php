<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * guard_name = 'web' is correct even for Sanctum API apps.
     *
     * Spatie resolves guards from config/auth.php guard keys ('web', 'api'),
     * NOT from Sanctum's driver name ('sanctum'). Using 'web' is the
     * standard pattern for Spatie + Sanctum — Sanctum handles authentication,
     * Spatie uses 'web' as the permission guard independently.
     */
    private const GUARD = 'web';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'documents.view', 'documents.generate', 'documents.delete',
            'templates.view', 'templates.create', 'templates.edit', 'templates.delete',
            'analytics.view',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'companies.switch', 'companies.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => self::GUARD,
            ]);
        }

        // super-admin — full access, matches User::isAdmin()
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => self::GUARD]);
        $superAdmin->syncPermissions(Permission::where('guard_name', self::GUARD)->get());

        // admin — matches existing role:admin middleware on routes
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => self::GUARD]);
        $admin->syncPermissions([
            'documents.view', 'documents.generate', 'documents.delete',
            'templates.view', 'templates.create', 'templates.edit', 'templates.delete',
            'analytics.view',
            'users.view', 'users.create', 'users.edit',
            'companies.switch',
        ]);

        // hr-manager
        $hrManager = Role::firstOrCreate(['name' => 'hr-manager', 'guard_name' => self::GUARD]);
        $hrManager->syncPermissions([
            'documents.view',
            'analytics.view',
            'users.view', 'users.create', 'users.edit',
        ]);

        // office-manager
        $officeManager = Role::firstOrCreate(['name' => 'office-manager', 'guard_name' => self::GUARD]);
        $officeManager->syncPermissions([
            'documents.view', 'documents.generate',
            'templates.view', 'templates.create', 'templates.edit', 'templates.delete',
            'analytics.view',
            'companies.switch',
        ]);

        // employee
        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => self::GUARD]);
        $employee->syncPermissions([
            'documents.view', 'documents.generate',
        ]);

        $this->command->info('✅ Roles & permissions seeded (guard: web)');
        $this->command->info('   Roles: super-admin, admin, hr-manager, office-manager, employee');
        $this->command->info('   Permissions: ' . count($permissions));
    }
}