<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |-------------------------------------------------------------------------- 
        | Define user data
        |-------------------------------------------------------------------------- 
        | This section defines all necessary users for the system
        */
        $users = [
            ['name' => 'Super Admin', 'email' => 'admin@iqbolshoh.uz', 'password' => Hash::make('IQBOLSHOH')],
            ['name' => 'User', 'email' => 'user@iqbolshoh.uz', 'password' => Hash::make('IQBOLSHOH')],
            ['name' => 'Manager', 'email' => 'manager@iqbolshoh.uz', 'password' => Hash::make('IQBOLSHOH')],
        ];

        /*
        |-------------------------------------------------------------------------- 
        | Create users
        |-------------------------------------------------------------------------- 
        | This section creates all necessary users for the system
        */
        collect($users)->each(fn($user) => User::create($user));

        /*
        |-------------------------------------------------------------------------- 
        | Display success message
        |-------------------------------------------------------------------------- 
        | This section displays a confirmation message
        */
        $this->command->info('Users seeded: Admin & User!');
    }
}