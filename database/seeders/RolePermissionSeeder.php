<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |-------------------------------------------------------------------------- 
        | Define permissions and roles
        |-------------------------------------------------------------------------- 
        | This section defines all necessary permissions for the system
        */
        $config = [
            'permissions' => [
                'role' => ['view', 'create', 'edit', 'delete'],
                'user' => ['view', 'create', 'edit', 'delete'],
                'profile' => ['view', 'edit', 'delete']
            ],
            'roles' => ['superadmin', 'user', 'manager'], // Added 'manager' role
            'role_permissions' => [
                'user' => ['user.view', 'profile.view', 'profile.edit'],
                'manager' => ['user.view', 'user.create', 'user.edit'], // Permissions for manager
            ],
            'user_roles' => [
                'admin@iqbolshoh.uz' => 'superadmin',
                'user@iqbolshoh.uz' => 'user',
                'manager@iqbolshoh.uz' => 'manager' // Added manager user
            ]
        ];

        /*
        |-------------------------------------------------------------------------- 
        | Create permissions
        |-------------------------------------------------------------------------- 
        | This section creates all necessary permissions for the system
        */
        collect($config['permissions'])->each(fn($perms, $group) =>
            collect($perms)->each(fn($perm) =>
                Permission::firstOrCreate(['name' => "$group.$perm", 'guard_name' => 'web'])));

        /*
        |-------------------------------------------------------------------------- 
        | Create roles and assign permissions
        |-------------------------------------------------------------------------- 
        | This section defines all necessary permissions for the system
        */
        collect($config['roles'])->each(fn($role) =>
            tap(
                Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']),
                fn($roleInstance) => $config['role_permissions'][$role] ?? [] ?
                $roleInstance->syncPermissions($config['role_permissions'][$role]) : null
            ));

        /*
        |-------------------------------------------------------------------------- 
        | Assign roles to users
        |-------------------------------------------------------------------------- 
        | This section defines all necessary permissions for the system
        */
        collect($config['user_roles'])->each(fn($role, $email) =>
            tap(User::where('email', $email)->first(), fn($user) =>
                $user?->hasRole($role) ?: $user?->assignRole($role)));

        /*
        |-------------------------------------------------------------------------- 
        | Display success message
        |-------------------------------------------------------------------------- 
        | This section defines all necessary permissions for the system
        */
        $this->command->info('Roles and Permissions created successfully!');
    }
}