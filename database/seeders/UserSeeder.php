<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name'      => 'Administrateur',
            'password'  => Hash::make('admin2026!'),
            'role'      => 'admin',
            'phone'     => '',
            'is_active' => true,
        ]);

        // Vendeur démo
        User::firstOrCreate(['email' => 'vendeur@example.com'], [
            'name'      => 'Vendeur Démo',
            'password'  => Hash::make('vendeur2026!'),
            'role'      => 'seller',
            'phone'     => '',
            'is_active' => true,
        ]);

        // Acheteur démo
        User::firstOrCreate(['email' => 'client@example.com'], [
            'name'      => 'Client Démo',
            'password'  => Hash::make('client2026!'),
            'phone'     => '',
            'role'      => 'buyer',
            'is_active' => true,
        ]);
    }
}
