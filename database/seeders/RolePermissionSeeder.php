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
        // Rollarni yaratish
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $roleTeacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $roleStudent = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Ruxsatlar ro‘yxati
        $permissions = [
            'dashboard' => ['dashboard.view', 'dashboard.edit'],
            'blog' => ['blog.create', 'blog.view', 'blog.edit', 'blog.delete', 'blog.approve'],
            'admin' => ['admin.create', 'admin.view', 'admin.edit', 'admin.delete', 'admin.approve'],
            'role' => ['role.create', 'role.view', 'role.edit', 'role.delete', 'role.approve'],
            'profile' => ['profile.view', 'profile.edit', 'profile.delete', 'profile.update'],
            'course' => ['course.create', 'course.view', 'course.edit', 'course.delete', 'course.approve'],
            'lesson' => ['lesson.create', 'lesson.view', 'lesson.edit', 'lesson.delete'],
            'test' => ['test.create', 'test.view', 'test.edit', 'test.delete'],
            'certificate' => ['certificate.create', 'certificate.view', 'certificate.edit', 'certificate.delete'],
            'student' => ['student.create', 'student.view', 'student.edit', 'student.delete'],
            'teacher' => ['teacher.create', 'teacher.view', 'teacher.edit', 'teacher.delete'],
            'payment' => ['payment.view', 'payment.process'],
            'report' => ['report.view', 'report.download'],
        ];

        // Ruxsatlarni yaratish va rollarga biriktirish
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                $permission = Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'web',
                ]);

                // Superadmin barcha ruxsatlarni oladi
                if (!$roleSuperAdmin->hasPermissionTo($perm)) {
                    $roleSuperAdmin->givePermissionTo($permission);
                }
            }
        }

        // Teacher va Student uchun maxsus ruxsatlar
        $teacherPermissions = [
            'dashboard.view',
            'blog.view',
            'course.create',
            'course.view',
            'lesson.create',
            'lesson.view',
            'test.create',
            'test.view',
            'certificate.view',
            'student.view'
        ];

        $studentPermissions = [
            'dashboard.view',
            'blog.view',
            'course.view',
            'lesson.view',
            'test.view',
            'certificate.view'
        ];

        foreach ($teacherPermissions as $perm) {
            $permission = Permission::where('name', $perm)->first();
            if ($permission && !$roleTeacher->hasPermissionTo($perm)) {
                $roleTeacher->givePermissionTo($permission);
            }
        }

        foreach ($studentPermissions as $perm) {
            $permission = Permission::where('name', $perm)->first();
            if ($permission && !$roleStudent->hasPermissionTo($perm)) {
                $roleStudent->givePermissionTo($permission);
            }
        }

        // Admin foydalanuvchiga SuperAdmin rolini biriktirish
        $admin = User::where('email', 'admin@iqbolshoh.uz')->first();
        if ($admin && !$admin->hasRole('superadmin')) {
            $admin->assignRole('superadmin');
        }

        // Teacher foydalanuvchiga Teacher rolini biriktirish
        $teacher = User::where('email', 'teacher@iqbolshoh.uz')->first();
        if ($teacher && !$teacher->hasRole('teacher')) {
            $teacher->assignRole('teacher');
        }

        // Student foydalanuvchiga Student rolini biriktirish
        $student = User::where('email', 'student@iqbolshoh.uz')->first();
        if ($student && !$student->hasRole('student')) {
            $student->assignRole('student');
        }

        $this->command->info('Roles and Permissions created successfully!');
    }
}
