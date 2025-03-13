<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Rollarni yaratish
        $admin = Role::create(['name' => 'admin']);
        $teacher = Role::create(['name' => 'teacher']);
        $student = Role::create(['name' => 'student']);

        // Ruxsatnomalarni yaratish
        $permissions = [
            'manage users',
            'manage students',
            'create posts',
            'view courses'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Adminga barcha ruxsatlarni berish
        $admin->givePermissionTo(Permission::all());

        // Teacher uchun faqat dars yaratish va ko‘rish
        $teacher->givePermissionTo(['create posts', 'view courses']);

        // Student faqat darslarni ko‘rishi mumkin
        $student->givePermissionTo(['view courses']);
    }
}

