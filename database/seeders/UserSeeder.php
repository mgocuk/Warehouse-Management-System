<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Depo Sorumlusu')->first();
        $viewerRole = Role::where('name', 'Görüntüleme')->first();

        User::firstOrCreate(
            ['email' => 'admin@bilimmerkezi.org'],
            [
                'name' => 'Mustafa Göcük (Admin)',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'depo@bilimmerkezi.org'],
            [
                'name' => 'Ahmet Yılmaz (Depo Sorumlusu)',
                'password' => Hash::make('password123'),
                'role_id' => $managerRole->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'izleyici@bilimmerkezi.org'],
            [
                'name' => 'Ayşe Kaya (Görüntüleyici)',
                'password' => Hash::make('password123'),
                'role_id' => $viewerRole->id,
                'is_active' => true,
            ]
        );
    }
}
