<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use App\Models\User;

/**
 * Class RolePermissionSeeder
 *
 * This seeder handles the creation of roles, permissions, and their assignment to users.
 * It utilizes the Spatie Permission package for managing role-based access control.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates all necessary permissions, roles, and assigns them to users
     * according to the predefined configuration array.
     *
     * @return void
     */
    public function run(): void
    {
        /* ----- Configuration Array ----- */
        /* Defines permissions, roles with their permissions, and users by role. */
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
            | Assign permissions to specific user roles.
            | Superadmin has all permissions by default.
            |
            */
            'roles' => [
                'superadmin' => [
                    'permissions' => [], // Will automatically get all permissions
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
            | Predefined users for each role with credentials.
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

        /* ----- Create Permissions ----- */
        /* Iterate over permissions and create them in "resource.action" format. */
        collect($config['permissions'])->each(
            fn($actions, $resource) => collect($actions)->each(
                fn($action) => Permission::firstOrCreate([
                    'name' => "$resource.$action",
                    'guard_name' => 'web',
                ])
            )
        );

        /* ----- Create Roles and Assign Permissions ----- */
        /* Create roles and assign permissions; superadmin gets all permissions. */
        collect($config['roles'])->each(
            fn($roleConfig, $roleName) =>
            tap(
                Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]),
                fn($role) => $role->syncPermissions(
                    $roleName === 'superadmin'
                    ? Permission::all()->pluck('name') // Superadmin gets all
                    : collect($roleConfig['permissions'])->flatMap(
                        fn($actions, $resource) => collect($actions)->map(fn($action) => "$resource.$action")
                    )
                )
            )
        );

        /* ----- Create Users and Assign Roles ----- */
        /* Seed users with credentials and assign their roles. */
        collect($config['users_by_role'])->each(
            fn($users, $role) =>
            collect($users)->each(
                fn($userData) =>
                tap(
                    User::firstOrCreate(
                        ['email' => $userData['email']],
                        [
                            'name' => $userData['name'],
                            'password' => $userData['password'],
                        ]
                    ),
                    fn($user) => $user->assignRole($userData['role'])
                )
            )
        );

        /* ----- Success Confirmation ----- */
        /* Output success message to console. */
        $this->command->info('Roles, Permissions, and Users seeded successfully!');
    }
}