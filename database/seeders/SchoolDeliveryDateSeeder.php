<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolDeliveryDate;
use Illuminate\Database\Seeder;

class SchoolDeliveryDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all schools
        $schools = School::all();

        foreach ($schools as $school) {
            // Crea 3 date di consegna per ogni scuola
            SchoolDeliveryDate::create([
                'school_id' => $school->id,
                'scheduled_date' => now()->addDays(7)->startOfDay(),
                'label' => 'Consegna principale',
                'is_active' => true,
            ]);

            SchoolDeliveryDate::create([
                'school_id' => $school->id,
                'scheduled_date' => now()->addDays(14)->startOfDay(),
                'label' => 'Consegna secondaria',
                'is_active' => true,
            ]);

            SchoolDeliveryDate::create([
                'school_id' => $school->id,
                'scheduled_date' => now()->addDays(21)->startOfDay(),
                'label' => 'Consegna finale',
                'is_active' => true,
            ]);
        }
    }
}
