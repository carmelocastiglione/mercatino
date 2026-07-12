<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SchoolReservationDate;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SchoolReservationDateController extends Controller
{
    /**
     * Display a listing of reservation dates for the staff's school.
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;
        
        $activeDates = SchoolReservationDate::bySchool($schoolId)
            ->where('is_active', true)
            ->orderBy('scheduled_date')
            ->get();

        $inactiveDates = SchoolReservationDate::bySchool($schoolId)
            ->where('is_active', false)
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return view('staff.school-reservation-dates.index', [
            'activeDates' => $activeDates,
            'inactiveDates' => $inactiveDates,
        ]);
    }

    /**
     * Show the form for creating a new reservation date.
     */
    public function create(): View
    {
        return view('staff.school-reservation-dates.create');
    }

    /**
     * Store a newly created reservation date.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scheduled_date' => 'required|date_format:Y-m-d|after:today',
            'label' => 'nullable|string|max:255',
        ], [
            'scheduled_date.required' => 'La data di ritiro è obbligatoria',
            'scheduled_date.date_format' => 'La data deve essere in formato YYYY-MM-DD',
            'scheduled_date.after' => 'La data deve essere nel futuro',
            'label.max' => 'L\'etichetta non può superare 255 caratteri',
        ]);

        $schoolId = auth()->user()->school_id;

        // Converti la data in datetime (mezzanotte)
        $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['scheduled_date'])->startOfDay();

        SchoolReservationDate::create([
            'school_id' => $schoolId,
            'scheduled_date' => $dateTime,
            'label' => $validated['label'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('staff.reservation-dates.index')
            ->with('success', 'Data di ritiro prenotazioni aggiunta con successo');
    }

    /**
     * Show the form for editing the specified reservation date.
     */
    public function edit(SchoolReservationDate $reservationDate): View
    {
        $this->authorizeReservationDate($reservationDate);

        return view('staff.school-reservation-dates.edit', [
            'reservationDate' => $reservationDate,
        ]);
    }

    /**
     * Update the specified reservation date.
     */
    public function update(Request $request, SchoolReservationDate $reservationDate): RedirectResponse
    {
        $this->authorizeReservationDate($reservationDate);

        $validated = $request->validate([
            'scheduled_date' => 'required|date_format:Y-m-d',
            'label' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ], [
            'scheduled_date.required' => 'La data di ritiro è obbligatoria',
            'scheduled_date.date_format' => 'La data deve essere in formato YYYY-MM-DD',
            'label.max' => 'L\'etichetta non può superare 255 caratteri',
        ]);

        $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['scheduled_date'])->startOfDay();

        $reservationDate->update([
            'scheduled_date' => $dateTime,
            'label' => $validated['label'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.reservation-dates.index')
            ->with('success', 'Data di ritiro prenotazioni aggiornata con successo');
    }

    /**
     * Delete the specified reservation date.
     */
    public function destroy(SchoolReservationDate $reservationDate): RedirectResponse
    {
        $this->authorizeReservationDate($reservationDate);

        $reservationDate->delete();

        return redirect()->route('staff.reservation-dates.index')
            ->with('success', 'Data di ritiro prenotazioni eliminata con successo');
    }

    /**
     * Toggle active status of a reservation date.
     */
    public function toggle(SchoolReservationDate $reservationDate): RedirectResponse
    {
        $this->authorizeReservationDate($reservationDate);

        $reservationDate->update([
            'is_active' => !$reservationDate->is_active,
        ]);

        $status = $reservationDate->is_active ? 'attivata' : 'disattivata';

        return back()->with('success', "Data di ritiro prenotazioni {$status} con successo");
    }

    /**
     * Authorize the user to manage the reservation date.
     */
    public function authorizeReservationDate(SchoolReservationDate $reservationDate): void
    {
        if ($reservationDate->school_id !== auth()->user()->school_id) {
            abort(403, 'Non sei autorizzato a modificare questa data di ritiro prenotazioni');
        }
    }
}
