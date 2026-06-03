<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Viganò school
        $vigano = School::where('name', 'Viganò')->first();
        // Get Liceo Agnesi school
        $agnesi = School::where('name', 'Agnesi')->first();

        // Viganò settings
        if ($vigano) {
            $vigano->setSetting('enable_online_sales', 'true');
            $vigano->setSetting('referring_name', 'COMITATO GENITORI ISTITUTO TECNICO VIGANO\' DI MERATE (LC)');
        }

        // Agnesi settings
        if ($agnesi) {
            $agnesi->setSetting('enable_online_sales', 'false');
            $agnesi->setSetting('referring_name', 'COMITATO GENITORI LICEO AGNESI DI MERATE (LC)');
        }
    }
}

