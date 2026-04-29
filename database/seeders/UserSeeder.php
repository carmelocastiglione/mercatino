<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Marco',
            'surname' => 'Rossi',
            'email' => 'studente@issvigano.org',
            'password' => Hash::make('mercatino'),
            'role' => 'studente',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Anna',
            'surname' => 'Bianchi',
            'email' => 'staff@issvigano.org',
            'password' => Hash::make('mercatino'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Giovanni',
            'surname' => 'Verdi',
            'email' => 'admin@issvigano.org',
            'password' => Hash::make('mercatino'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}
