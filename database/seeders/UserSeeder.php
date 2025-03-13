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
        // Foydalanuvchilarni yaratish va rollarni tayinlash
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@iqbolshoh.uz',
                'password' => Hash::make('IQBOLSHOH'),
                'role' => 'admin',
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@iqbolshoh.uz',
                'password' => Hash::make('IQBOLSHOH'),
                'role' => 'teacher',
            ],
            [
                'name' => 'Student User',
                'email' => 'student@iqbolshoh.uz',
                'password' => Hash::make('IQBOLSHOH'),
                'role' => 'student',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                ['name' => $userData['name'], 'password' => $userData['password']]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
