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
            'password'  => Hash::make('
            '),
            'role'      => 'admin',
            'phone'     => '',
            'is_active' => true,
        ]);

        // Vendeurs démo (5)
        $sellers = [
            ['email' => 'vendeur@example.com',  'name' => 'Kofi Mensah',     'phone' => '+2250101010101'],
            ['email' => 'vendeur2@example.com', 'name' => 'Aminata Diallo',  'phone' => '+2250202020202'],
            ['email' => 'vendeur3@example.com', 'name' => 'Jean-Baptiste N\'Goran', 'phone' => '+2250303030303'],
            ['email' => 'vendeur4@example.com', 'name' => 'Fatou Coulibaly', 'phone' => '+2250404040404'],
            ['email' => 'vendeur5@example.com', 'name' => 'Sékou Traoré',    'phone' => '+2250505050505'],
        ];

        foreach ($sellers as $seller) {
            User::firstOrCreate(['email' => $seller['email']], [
                'name'      => $seller['name'],
                'password'  => Hash::make('vendeur2026!'),
                'role'      => 'seller',
                'phone'     => $seller['phone'],
                'is_active' => true,
            ]);
        }

        // Acheteurs démo (10)
        $buyers = [
            ['email' => 'client@example.com',   'name' => 'Marie Kouassi',   'phone' => '+2250601010101'],
            ['email' => 'client2@example.com',  'name' => 'Paul Yao',        'phone' => '+2250602020202'],
            ['email' => 'client3@example.com',  'name' => 'Sylvie Bamba',    'phone' => '+2250603030303'],
            ['email' => 'client4@example.com',  'name' => 'Ibrahim Touré',   'phone' => '+2250604040404'],
            ['email' => 'client5@example.com',  'name' => 'Abla Fofana',     'phone' => '+2250605050505'],
            ['email' => 'client6@example.com',  'name' => 'Rodrigue Aka',    'phone' => '+2250606060606'],
            ['email' => 'client7@example.com',  'name' => 'Mariam Sanogo',   'phone' => '+2250607070707'],
            ['email' => 'client8@example.com',  'name' => 'Christophe Koffi','phone' => '+2250608080808'],
            ['email' => 'client9@example.com',  'name' => 'Awa Diomandé',    'phone' => '+2250609090909'],
            ['email' => 'client10@example.com', 'name' => 'Narcisse Brou',   'phone' => '+2250610101010'],
        ];

        foreach ($buyers as $buyer) {
            User::firstOrCreate(['email' => $buyer['email']], [
                'name'      => $buyer['name'],
                'password'  => Hash::make('client2026!'),
                'role'      => 'buyer',
                'phone'     => $buyer['phone'],
                'is_active' => true,
            ]);
        }
    }
}
