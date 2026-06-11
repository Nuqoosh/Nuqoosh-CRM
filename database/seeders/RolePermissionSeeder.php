<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles if not exist
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'api'],
            ['name' => 'admin', 'guard_name' => 'api']
        );

        $user = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'api'],
            ['name' => 'user', 'guard_name' => 'api']
        );

        // Create permission if not exist
        $perm = Permission::firstOrCreate(
            ['name' => 'manage templates', 'guard_name' => 'api'],
            ['name' => 'manage templates', 'guard_name' => 'api']
        );

        // Assign permission to admin role (avoid duplicate)
        if (!$admin->hasPermissionTo($perm)) {
            $admin->givePermissionTo($perm);
        }
    }
}