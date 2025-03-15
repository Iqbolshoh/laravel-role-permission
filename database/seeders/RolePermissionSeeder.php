<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        $permissions = [
            'dashboard' => ['dashboard.view', 'dashboard.edit'],
            'blog' => ['blog.create', 'blog.view', 'blog.edit', 'blog.delete', 'blog.approve'],
            'admin' => ['admin.create', 'admin.view', 'admin.edit', 'admin.delete', 'admin.approve'],
            'role' => ['role.create', 'role.view', 'role.edit', 'role.delete', 'role.approve'],
            'profile' => ['profile.view', 'profile.edit', 'profile.delete', 'profile.update'],
        ];

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                $permission = Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'web',
                ]);

                if (!$roleSuperAdmin->hasPermissionTo($perm)) {
                    $roleSuperAdmin->givePermissionTo($permission);
                }
            }
        }

        $admin = User::where('email', 'admin@iqbolshoh.uz')->first();
        if ($admin && !$admin->hasRole('superadmin')) {
            $admin->assignRole('superadmin');
        }

        $this->command->info('Roles and Permissions created successfully!');
    }
}
