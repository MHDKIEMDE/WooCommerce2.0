<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(['email' => 'admin@agri-shop.fr'], [
            'name'      => 'Administrateur',
            'password'  => Hash::make('admin1234'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Compte démo client
        User::firstOrCreate(['email' => 'client@agri-shop.fr'], [
            'name'      => 'Jean Dupont',
            'password'  => Hash::make('client1234'),
            'phone'     => '0612345678',
            'role'      => 'customer',
            'is_active' => true,
        ]);

        // Clients de test
        User::factory()->count(10)->create();
    }
}
