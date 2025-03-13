<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Rollarni yaratish
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $student = Role::firstOrCreate(['name' => 'student']);

        // Ruxsatnomalarni yaratish
        $permissions = [
            'manage users',
            'manage students',
            'create posts',
            'view courses'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Adminga barcha ruxsatlarni berish
        $admin->givePermissionTo(Permission::all());

        // Teacher faqat post yaratishi va kurslarni ko‘rishi mumkin
        $teacher->givePermissionTo(['create posts', 'view courses']);

        // Student faqat kurslarni ko‘rishi mumkin
        $student->givePermissionTo(['view courses']);
    }
}
