<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {

        // Define permissions
        $permissions = [
            'dashboard' => ['dashboard.view'],
            'role' => ['role.create', 'role.view', 'role.edit', 'role.delete'],
            'user' => ['user.create', 'user.view', 'user.edit', 'user.delete'],
            'profile' => ['profile.view', 'profile.edit']
        ];

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
        }

        // Create roles
        $roles = ['superadmin', 'user'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $rolePermissions = [
            'user' => [
                'dashboard.view',
                'profile.view',
                'profile.edit',
            ]
        ];

        foreach ($rolePermissions as $role => $permissions) {
            $roleInstance = Role::where('name', $role)->first();
            if ($roleInstance) {
                $roleInstance->syncPermissions($permissions);
            }
        }

        // Assign roles to users
        $users = [
            'admin@iqbolshoh.uz' => 'superadmin',
            'user@iqbolshoh.uz' => 'user',
        ];

        foreach ($users as $email => $role) {
            $user = User::where('email', $email)->first();
            if ($user && !$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        $this->command->info('Roles and Permissions created successfully!');
    }
}
