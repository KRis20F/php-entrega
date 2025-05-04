<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        User::create([
            'name' => 'kris',
            'email' => 'kris@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id
        ]);
    }
}
