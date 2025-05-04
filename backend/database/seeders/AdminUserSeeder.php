<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        $admin = User::create([
            'name' => 'kris',
            'email' => 'kris@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
            'remember_token' => 'admin_' . Str::random(60),
            'api_token' => hash('sha256', 'admin_special_token_' . Str::random(60))
        ]);

        // Guardar el token en un archivo de log seguro (solo para desarrollo)
        if (app()->environment('local')) {
            File::put(
                storage_path('logs/admin_token.log'),
                "Admin API Token: {$admin->api_token}\n"
            );
        }
    }
}
