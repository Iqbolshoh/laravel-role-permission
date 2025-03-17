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
        // Create roles
        $roles = [
            'superadmin',
            'teacher',
            'student'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Define permissions
        $permissions = [
            'dashboard' => ['dashboard.view', 'dashboard.edit'],
            'role' => ['role.create', 'role.view', 'role.edit', 'role.delete'],
            'user' => ['user.create', 'user.view', 'user.edit', 'user.delete'],
            'profile' => ['profile.view', 'profile.edit'],
            'course' => ['course.create', 'course.view', 'course.edit', 'course.delete'],
            'lesson' => ['lesson.create', 'lesson.view', 'lesson.edit', 'lesson.delete'],
            'test' => ['test.create', 'test.view', 'test.edit', 'test.delete'],
            'certificate' => ['certificate.create', 'certificate.view', 'certificate.edit', 'certificate.delete'],
            'payment' => ['payment.view', 'payment.process', 'payment.refund'],
        ];

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
        }

        // Assign permissions to roles
        $rolePermissions = [
            'teacher' => [
                'dashboard.view',
                'profile.view',
                'profile.edit',
                'course.create',
                'course.view',
                'course.edit',
                'course.delete',
                'lesson.create',
                'lesson.view',
                'lesson.edit',
                'lesson.delete',
                'test.create',
                'test.view',
                'test.edit',
                'test.delete',
                'certificate.view',
            ],
            'student' => [
                'dashboard.view',
                'profile.view',
                'profile.edit',
                'course.view',
                'lesson.view',
                'test.view',
                'certificate.view',
                'payment.view',
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
            'teacher@iqbolshoh.uz' => 'teacher',
            'student@iqbolshoh.uz' => 'student',
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
