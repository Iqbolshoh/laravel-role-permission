<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin foydalanuvchisi
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@iqbolshoh.uz',
            'password' => Hash::make('password')
        ]);
        $admin->assignRole('admin');

        // Teacher foydalanuvchisi
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@iqbolshoh.uz',
            'password' => Hash::make('password')
        ]);
        $teacher->assignRole('teacher');

        // Student foydalanuvchisi
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@iqbolshoh.uz',
            'password' => Hash::make('password')
        ]);
        $student->assignRole('student');
    }
}
