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
            'address' => 'Via del Lodovichi, 10',
            'phone' => '+39 02 1234 5678',
            'email' => 'info@liceoagnesi.edu.it',
            'purchase_fee' => 1.00,
            'sales_fee' => 1.00,
        ]);

        School::create([
            'name' => 'Viganò',
            'description' => 'Istituto Tecnico Francesco Viganò',
            'city' => 'Merate',
            'address' => 'Via dei Lodovichi, 2',
            'phone' => '+39 039 9876 5432',
            'email' => 'info@issvigano.org',
            'purchase_fee' => 0.50,
            'sales_fee' => 0.50,
        ]);
    }
}
