<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        School::create([
            'name' => 'Agnesi',
            'description' => 'Liceo Maria Gaetana Agnesi',
            'city' => 'Merate',
            'address' => 'Via Agnesi, 6',
            'phone' => '+39 02 1234 5678',
            'email' => 'info@issagnesi.it',
        ]);

        School::create([
            'name' => 'Viganò',
            'description' => 'Istituto tecnico Francesco Viganò',
            'city' => 'Merate',
            'address' => 'Via Viganò, 1',
            'phone' => '+39 039 9876 5432',
            'email' => 'info@issvigano.org',
        ]);
    }
}
