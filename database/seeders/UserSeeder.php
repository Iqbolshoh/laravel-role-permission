<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@iqbolshoh.uz',
                'password' => Hash::make('IQBOLSHOH')
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@iqbolshoh.uz',
                'password' => Hash::make('IQBOLSHOH')
            ],
            [
                'name' => 'Student User',
                'email' => 'student@iqbolshoh.uz',
                'password' => Hash::make('IQBOLSHOH')
            ],
        ];

        foreach ($users as $data) {
            User::create($data);
        }

        $this->command->info('Users table seeded with Admin, Teacher, and Student!');
    }
}
