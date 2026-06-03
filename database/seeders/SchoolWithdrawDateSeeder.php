<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolWithdrawDate;
use Illuminate\Database\Seeder;

class SchoolWithdrawDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get both schools
        $vigano = School::where('name', 'Viganò')->first();
        $agnesi = School::where('name', 'Agnesi')->first();

        if ($vigano) {
            // Crea 2 date di ritiro per la scuola Viganò
            SchoolWithdrawDate::create([
                'school_id' => $vigano->id,
                'scheduled_date' => \Carbon\Carbon::create(2026, 7, 22)->startOfDay(),
                'label' => 'Ritiro principale',
                'is_active' => true,
            ]);

            SchoolWithdrawDate::create([
                'school_id' => $vigano->id,
                'scheduled_date' => \Carbon\Carbon::create(2026, 7, 23)->startOfDay(),
                'label' => 'Ritiro secondario',
                'is_active' => true,
            ]);
        }

        if ($agnesi) {
            // Crea 2 date di ritiro per la scuola Agnesi
            SchoolWithdrawDate::create([
                'school_id' => $agnesi->id,
                'scheduled_date' => \Carbon\Carbon::create(2026, 7, 22)->startOfDay(),
                'label' => 'Ritiro principale',
                'is_active' => true,
            ]);

            SchoolWithdrawDate::create([
                'school_id' => $agnesi->id,
                'scheduled_date' => \Carbon\Carbon::create(2026, 7, 23)->startOfDay(),
                'label' => 'Ritiro secondario',
                'is_active' => true,
            ]);
        }
    }
}
