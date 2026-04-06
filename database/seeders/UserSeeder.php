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
        User::firstOrCreate(['email' => 'admin@massaka.ci'], [
            'name'      => 'Administrateur Massaka',
            'password'  => Hash::make('massaka2026!'),
            'role'      => 'super-admin',
            'phone'     => '+225 07 00 00 00 00',
            'is_active' => true,
        ]);

        // Admin secondaire
        User::firstOrCreate(['email' => 'manager@massaka.ci'], [
            'name'      => 'Manager Boutique',
            'password'  => Hash::make('manager2026!'),
            'role'      => 'admin',
            'phone'     => '+225 07 11 22 33 44',
            'is_active' => true,
        ]);

        // Compte démo client
        User::firstOrCreate(['email' => 'client@massaka.ci'], [
            'name'      => 'Koné Aminata',
            'password'  => Hash::make('client2026!'),
            'phone'     => '+225 05 00 00 00 00',
            'role'      => 'customer',
            'is_active' => true,
        ]);

        // Clients de test (utilise UserFactory)
        User::factory()->count(10)->create();
    }
}
