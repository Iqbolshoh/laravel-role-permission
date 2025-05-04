<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use App\Models\User;

/**
 * Class RolePermissionSeeder
 *
 * This seeder manages the creation of roles, permissions, and their assignment to users.
 * It leverages the Spatie Permission package to implement role-based access control (RBAC).
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeding process.
     *
     * Creates all necessary permissions, roles, and assigns them to users
     * based on a predefined configuration array.
     *
     * @return void
     */
    public function run(): void
    {
        /*
        |---------------------------------------------------------------
        | Configuration Array
        |---------------------------------------------------------------
        |
        | Defines permissions, roles with their associated permissions,
        | and users grouped by role.
        |
        */
        $config = [
            /*
            |---------------------------------------------------------------
            | Resource Permissions
            |---------------------------------------------------------------
            |
            | List of permissions for each resource.
            | Example actions: 'view', 'create', 'edit', 'delete'.
            |
            */
            'permissions' => [
                'role' => ['view', 'create', 'edit', 'delete'],
                'user' => ['view', 'create', 'edit', 'delete'],
                'profile' => ['view', 'edit', 'delete'],
                'session' => ['view', 'delete'],
            ],

            /*
            |---------------------------------------------------------------
            | Roles and Their Permissions
            |---------------------------------------------------------------
            |
            | Assigns permissions to specific user roles.
            | The 'superadmin' role automatically receives all permissions.
            |
            */
            'roles' => [
                'superadmin' => [
                    'permissions' => [], // Automatically assigned all permissions
                ],
                'user' => [
                    'permissions' => [
                        'profile' => ['view', 'edit'],
                        'session' => ['view', 'delete'],
                    ],
                ],
            ],

            /*
            |---------------------------------------------------------------
            | Users Grouped by Role
            |---------------------------------------------------------------
            |
            | Predefined users for each role with their credentials.
            | Used for seeding or demo/testing purposes.
            |
            */
            'users_by_role' => [
                'superadmin' => [
                    [
                        'name' => 'Super Admin',
                        'email' => 'admin@iqbolshoh.uz',
                        'password' => bcrypt('IQBOLSHOH'),
                        'role' => 'superadmin',
                    ],
                ],
                'user' => [
                    [
                        'name' => 'Regular User',
                        'email' => 'user@iqbolshoh.uz',
                        'password' => bcrypt('IQBOLSHOH'),
                        'role' => 'user',
                    ],
                ],
            ],
        ];

        /*
        |---------------------------------------------------------------
        | Create Permissions
        |---------------------------------------------------------------
        |
        | Creates permissions in the "resource.action" format.
        | Example: "role.view", "user.create".
        |
        */
        collect($config['permissions'])->each(
            fn($actions, $resource) => collect($actions)->each(
                fn($action) => Permission::firstOrCreate(['name' => "$resource.$action", 'guard_name' => 'web'])
            )
        );

        /*
        |---------------------------------------------------------------
        | Create Roles and Assign Permissions
        |---------------------------------------------------------------
        |
        | Creates roles and assigns the corresponding permissions.
        | The 'superadmin' role is granted all permissions.
        |
        */
        foreach ($config['roles'] as $roleName => $roleConfig) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $permissions = $roleName === 'superadmin'
                ? Permission::all()->pluck('name')
                : collect($roleConfig['permissions'])->flatMap(
                    fn($actions, $resource) => collect($actions)->map(fn($action) => "$resource.$action")
                );
            $role->syncPermissions($permissions);
        }

        /*
        |---------------------------------------------------------------
        | Create Users and Assign Roles
        |---------------------------------------------------------------
        |
        | Creates users and assigns the appropriate roles to them.
        |
        */
        foreach ($config['users_by_role'] as $users) {
            foreach ($users as $userData) {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    ['name' => $userData['name'], 'password' => $userData['password']]
                );
                $user->assignRole($userData['role']);
            }
        }

        /*
        |---------------------------------------------------------------
        | Success Confirmation
        |---------------------------------------------------------------
        |
        | Outputs a success message to the console indicating that
        | roles, permissions, and users have been seeded successfully.
        |
        */
        $this->command->info('Permissions, Roles, and users seeded successfully!');
    }
}