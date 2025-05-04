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
        // Configuration settings for permissions, roles, and user-role assignments
        $config = [

            /*
            |--------------------------------------------------------------------------
            | Permissions by Resource
            |--------------------------------------------------------------------------
            |
            | Define available permissions for each resource. Each permission is
            | represented by a pair of resource and action (e.g., 'user.view').
            |
            */

            'permissions' => [
                'dashboard' => ['view'],
                'permission' => ['view', 'create', 'edit', 'delete'],
                'role' => ['view', 'create', 'edit', 'delete'],
                'user' => ['view', 'create', 'edit', 'delete'],
                'session' => ['view', 'delete'],
                'profile' => ['view', 'edit', 'delete'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Roles Definition
            |--------------------------------------------------------------------------
            |
            | Define all roles to be used in the system. Each role can be assigned
            | to one or more users and has specific permissions assigned.
            |
            */

            'roles' => [
                'superadmin', // Full access
                'user',       // Limited access
            ],

            /*
            |--------------------------------------------------------------------------
            | Role-Specific Permissions
            |--------------------------------------------------------------------------
            |
            | Assign specific permissions to each role. Permissions are grouped by
            | resource. Use this to control what each role can or cannot do.
            |
            */

            'permissions_by_role' => [
                'user' => [
                    'profile' => ['view', 'edit'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | User Role Assignments
            |--------------------------------------------------------------------------
            |
            | Assign roles to users by matching their email addresses. Only users
            | that exist in the database will be assigned a role.
            |
            */

            'user_role_assignments' => [
                'admin@iqbolshoh.uz' => 'superadmin',
                'user@iqbolshoh.uz' => 'user',
            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        |
        | Iterate over all defined permissions and create them if they do not exist.
        | Permissions follow the "resource.action" naming convention.
        |
        */
        collect($config['permissions'])->each(
            fn($actions, $resource) =>
            collect($actions)->each(
                fn($action) => Permission::firstOrCreate([
                    'name' => "$resource.$action",
                    'guard_name' => 'web',
                ])
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Create Roles and Assign Permissions
        |--------------------------------------------------------------------------
        |
        | For each defined role, create it and assign the corresponding permissions.
        | Uses syncPermissions to avoid duplicate or outdated assignments.
        |
        */
        collect($config['roles'])->each(
            fn($roleName) =>
            tap(
                Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]),
                fn($role) => $role->syncPermissions(
                    collect($config['role_permissions'][$roleName] ?? [])->flatMap(
                        fn($actions, $resource) =>
                        collect($actions)->map(fn($action) => "$resource.$action")
                    )
                )
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Assign Roles to Users
        |--------------------------------------------------------------------------
        |
        | Check each email from the config and assign the defined role if the user exists.
        | Ensures proper linking between users and their permissions.
        |
        */
        collect($config['user_roles'])->each(
            fn($role, $email) =>
            User::where('email', $email)->first()?->assignRole($role)
        );

        /*
        |--------------------------------------------------------------------------
        | Success Confirmation
        |--------------------------------------------------------------------------
        |
        | Output a success message in the console when all operations are complete.
        |
        */
        $this->command->info('✅ Roles and Permissions created successfully!');
    }
}
