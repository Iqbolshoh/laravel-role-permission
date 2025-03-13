<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Rollar va ruxsatlarni yaratish
        $this->call(RolePermissionSeeder::class);
        
        // Foydalanuvchilarni yaratish
        $this->call(UserSeeder::class);
    }
}
