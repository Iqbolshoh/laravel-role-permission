<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use App\Models\User;

/**
 * Class RolePermissionSeeder
 *
 * This seeder sets up role-based access control (RBAC) using the Spatie Permission package.
 * It creates permissions, roles, and users, and assigns roles to users based on a configuration array.
 */
class RolePermissionSeeder extends Seeder
{
    /*
    |---------------------------------------------------------------
    | run
    |---------------------------------------------------------------
    | Executes the database seeding process for roles, permissions, and users.
    |
    */
    public function run(): void
    {
        /*
        |---------------------------------------------------------------
        | config
        |---------------------------------------------------------------
        | Defines permissions, roles, and users for seeding.
        |
        */
        $config = [
            /*
            |---------------------------------------------------------------
            | permissions
            |---------------------------------------------------------------
            | Lists resources and their allowed actions.
            |
            */
            'permissions' => [
                'user' => ['view', 'create', 'edit', 'delete'],
                'profile' => ['view', 'edit'],
                'session' => ['view', 'delete'],
            ],

            /*
            |---------------------------------------------------------------
            | roles
            |---------------------------------------------------------------
            | Maps roles to their specific permissions.
            |
            */
            'roles' => [
                'superadmin' => [], // Automatically assigned all permissions
                'manager' => [
                    'user' => ['view', 'create', 'edit', 'delete'],
                    'profile' => ['view', 'edit'],
                    'session' => ['view', 'delete'],
                ],
                'user' => [
                    'profile' => ['view', 'edit'],
                    'session' => ['view', 'delete'],
                ],
            ],

            /*
            |---------------------------------------------------------------
            | users_by_role
            |---------------------------------------------------------------
            | Groups users by role with their credentials.
            |
            */
            'users_by_role' => [
                'superadmin' => [
                    ['name' => 'Super Admin', 'email' => 'admin@iqbolshoh.uz', 'password' => bcrypt('IQBOLSHOH')],
                ],
                'manager' => [
                    ['name' => 'Manager User', 'email' => 'manager@iqbolshoh.uz', 'password' => bcrypt('IQBOLSHOH')],
                ],
                'user' => [
                    ['name' => 'Regular User', 'email' => 'user@iqbolshoh.uz', 'password' => bcrypt('IQBOLSHOH')],
                ],
            ],
        ];

        /*
        |---------------------------------------------------------------
        | createPermissions
        |---------------------------------------------------------------
        | Creates permissions for each resource and action.
        |
        */
        collect($config['permissions'])->each(
            fn($actions, $resource) =>
            collect($actions)->each(
                fn($action) =>
                Permission::firstOrCreate(['name' => "$resource.$action", 'guard_name' => 'web'])
            )
        );

        /*
        |---------------------------------------------------------------
        | createRolesAndAssignPermissions
        |---------------------------------------------------------------
        | Creates roles and assigns permissions to them.
        |
        */
        collect($config['roles'])->each(function ($permissions, $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $perms = $roleName === 'superadmin' ? Permission::pluck('name') : collect($permissions)
                ->flatMap(fn($actions, $resource) => collect($actions)->map(fn($action) => "$resource.$action"));
            $role->syncPermissions($perms);
        });

        /*
        |---------------------------------------------------------------
        | createUsersAndAssignRoles
        |---------------------------------------------------------------
        | Creates users and assigns roles to them.
        |
        */
        collect($config['users_by_role'])->each(
            fn($users, $roleName) =>
            collect($users)->each(fn($userData) => User::firstOrCreate(
                ['email' => $userData['email']],
                ['name' => $userData['name'], 'password' => $userData['password']]
            )->syncRoles($roleName))
        );

        /*
        |---------------------------------------------------------------
        | outputSuccessMessage
        |---------------------------------------------------------------
        | Outputs a success message to the console.
        |
        */
        $this->command->info('Permissions, roles, and users seeded successfully!');
    }
}
