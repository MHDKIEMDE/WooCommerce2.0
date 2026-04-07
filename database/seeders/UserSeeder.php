<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super-admin
        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name'      => 'Administrateur',
            'password'  => Hash::make('admin2026!'),
            'role'      => 'super-admin',
            'phone'     => '',
            'is_active' => true,
        ]);

        // Admin secondaire
        User::firstOrCreate(['email' => 'manager@example.com'], [
            'name'      => 'Manager Boutique',
            'password'  => Hash::make('manager2026!'),
            'role'      => 'admin',
            'phone'     => '',
            'is_active' => true,
        ]);

        // Compte démo client
        User::firstOrCreate(['email' => 'client@example.com'], [
            'name'      => 'Client Démo',
            'password'  => Hash::make('client2026!'),
            'phone'     => '',
            'role'      => 'customer',
            'is_active' => true,
        ]);

        // Clients de test (utilise UserFactory)
        User::factory()->count(10)->create();
    }
}
