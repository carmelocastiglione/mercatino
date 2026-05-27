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
        // Get Viganò school only
        $vigano = School::where('name', 'Viganò')->first();

        if ($vigano) {
            // Crea 2 date di consegna per la scuola Viganò
            SchoolDeliveryDate::create([
                'school_id' => $vigano->id,
                // Data di consegna principale: 16 Giugno 2026
                'scheduled_date' => \Carbon\Carbon::create(2026, 6, 16)->startOfDay(),
                'label' => 'Consegna principale',
                'is_active' => true,
            ]);

            SchoolDeliveryDate::create([
                'school_id' => $vigano->id,
                // Data di consegna secondaria: 16 Settembre 2026
                'scheduled_date' => \Carbon\Carbon::create(2026, 9, 16)->startOfDay(),
                'label' => 'Consegna secondaria',
                'is_active' => true,
            ]);
        }
    }
}
