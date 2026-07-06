<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * guard_name = 'api' — matches the 'api' guard in config/auth.php
     * whose driver is sanctum. Routes authenticate via auth:sanctum,
     * and permission middleware uses permission:<name>,api so checks
     * resolve against the same guard the roles were created with.
     *
     * NOT 'sanctum' (a driver name, not a guard — Spatie rejects it).
     * NOT 'web' (session guard — Sanctum token users are null there).
     */
    private const GUARD = 'api';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'documents.view', 'documents.generate', 'documents.delete',
            'templates.view', 'templates.create', 'templates.edit', 'templates.delete',
            'analytics.view',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'companies.switch', 'companies.manage',
            'tasks.view.all', 'tasks.view.own', 'tasks.create',
            'tasks.update.status', 'tasks.delete',
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
            'tasks.view.all', 'tasks.view.own', 'tasks.create',
            'tasks.update.status', 'tasks.delete',
        ]);

        // hr-manager
        $hrManager = Role::firstOrCreate(['name' => 'hr-manager', 'guard_name' => self::GUARD]);
        $hrManager->syncPermissions([
            'documents.view',
            'analytics.view',
            'users.view', 'users.create', 'users.edit',
            'tasks.view.all', 'tasks.view.own', 'tasks.create', 'tasks.update.status',
        ]);

        // office-manager
        $officeManager = Role::firstOrCreate(['name' => 'office-manager', 'guard_name' => self::GUARD]);
        $officeManager->syncPermissions([
            'documents.view', 'documents.generate',
            'templates.view', 'templates.create', 'templates.edit', 'templates.delete',
            'analytics.view',
            'companies.switch',
            'tasks.view.all', 'tasks.view.own', 'tasks.create', 'tasks.update.status',
        ]);

        // employee
        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => self::GUARD]);
        $employee->syncPermissions([
            'documents.view', 'documents.generate',
            'templates.view', // needed to pick a template when generating documents
            'tasks.view.own', 'tasks.update.status',
        ]);

        $this->command->info('✅ Roles & permissions seeded (guard: api)');
        $this->command->info('   Roles: super-admin, admin, hr-manager, office-manager, employee');
        $this->command->info('   Permissions: ' . count($permissions));
    }
}