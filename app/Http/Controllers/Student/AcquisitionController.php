<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Acquisition;
use Illuminate\View\View;

class AcquisitionController extends Controller
{
    /**
     * Display the specified acquisition for the authenticated student (seller).
     */
    public function show(Acquisition $acquisition): View
    {
        // Ensure the authenticated user is the seller of this acquisition
        $this->authorize('view', $acquisition);

        $acquisition->load(['bookListings.book', 'seller']);

        $school = $acquisition->seller->school;
        
        // Carica le date di ritiro della scuola
        $withdrawDates = $school->withdrawDates()
            ->where('is_active', true)
            ->orderBy('scheduled_date')
            ->get();
        
        // Format withdrawal dates
        $activeDates = $withdrawDates->map(fn($date) => $date->scheduled_date->format('d/m/Y'))->toArray();
        $withdrawDatesText = !empty($activeDates) 
            ? implode(', ', $activeDates)
            : 'stabiliti dalla scuola';
        
        $referringName = $school->getSetting('referring_name');

        return view('student.acquisitions.show', [
            'acquisition' => $acquisition,
            'school' => $school->description,
            'referringName' => $referringName,
            'city' => $school->city,
            'withdrawDates' => $withdrawDates,
            'withdrawDatesText' => $withdrawDatesText,
        ]);
    }
}
