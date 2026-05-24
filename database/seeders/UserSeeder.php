<?php

namespace Database\Seeders;

use App\Models\School;
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
        // Get Viganò school
        $vigano = School::where('name', 'Viganò')->first();
        // Get or create Liceo Agnesi school
        $agnesi = School::where('name', 'Agnesi')->first();

        // Viganò users
        User::create([
            'name' => 'Marco',
            'surname' => 'Rossi',
            'email' => 'studente@issvigano.org',
            'password' => Hash::make('mercatino'),
            'role' => 'studente',
            'school_id' => $vigano?->id,
            'email_verified_at' => now(),
            'code' => User::generateCode('Marco', 'Rossi'),
        ]);

        User::create([
            'name' => 'Sara',
            'surname' => 'Neri',
            'email' => 'studente2@issvigano.org',
            'password' => Hash::make('mercatino'),
            'role' => 'studente',
            'school_id' => $vigano?->id,
            'email_verified_at' => now(),
            'code' => User::generateCode('Sara', 'Neri'),
        ]);

        User::create([
            'name' => 'Anna',
            'surname' => 'Bianchi',
            'email' => 'staff@issvigano.org',
            'password' => Hash::make('mercatino'),
            'role' => 'staff',
            'school_id' => $vigano?->id,
            'email_verified_at' => now(),
            'code' => User::generateCode('Anna', 'Bianchi'),
        ]);

        // Liceo Agnesi users
        User::create([
            'name' => 'Marco',
            'surname' => 'Rossi',
            'email' => 'studente@liceoagnesi.edu.it',
            'password' => Hash::make('mercatino'),
            'role' => 'studente',
            'school_id' => $agnesi->id,
            'email_verified_at' => now(),
            'code' => User::generateCode('Marco', 'Rossi'),
        ]);

        User::create([
            'name' => 'Sara',
            'surname' => 'Neri',
            'email' => 'studente2@liceoagnesi.edu.it',
            'password' => Hash::make('mercatino'),
            'role' => 'studente',
            'school_id' => $agnesi->id,
            'email_verified_at' => now(),
            'code' => User::generateCode('Sara', 'Neri'),
        ]);

        User::create([
            'name' => 'Anna',
            'surname' => 'Bianchi',
            'email' => 'staff@liceoagnesi.edu.it',
            'password' => Hash::make('mercatino'),
            'role' => 'staff',
            'school_id' => $agnesi->id,
            'email_verified_at' => now(),
            'code' => User::generateCode('Anna', 'Bianchi'),
        ]);

        // Admin user for local login (not associated with a school)
        User::create([
            'name' => 'Giovanni',
            'surname' => 'Verdi',
            'email' => 'admin@issvigano.org',
            'password' => Hash::make('mercatino'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'code' => User::generateCode('Giovanni', 'Verdi'),
        ]);

        // Admin user for Google SSO (not associated with a school)
        User::create([
            'name' => 'Carmelo',
            'surname' => 'Castiglione',
            'email' => 'castiglione.carmelo@issvigano.org',
            'role' => 'admin',
            'email_verified_at' => now(),
            'code' => User::generateCode('Carmelo', 'Castiglione'),
        ]);
    }
}
