<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SchoolWithdrawDate;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SchoolWithdrawDateController extends Controller
{
    /**
     * Display a listing of withdraw dates for the staff's school.
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;
        
        $activeDates = SchoolWithdrawDate::bySchool($schoolId)
            ->where('is_active', true)
            ->orderBy('scheduled_date')
            ->get();

        $inactiveDates = SchoolWithdrawDate::bySchool($schoolId)
            ->where('is_active', false)
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return view('staff.school-withdraw-dates.index', [
            'activeDates' => $activeDates,
            'inactiveDates' => $inactiveDates,
        ]);
    }

    /**
     * Show the form for creating a new withdraw date.
     */
    public function create(): View
    {
        return view('staff.school-withdraw-dates.create');
    }

    /**
     * Store a newly created withdraw date.
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

        SchoolWithdrawDate::create([
            'school_id' => $schoolId,
            'scheduled_date' => $dateTime,
            'label' => $validated['label'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('staff.withdraw-dates.index')
            ->with('success', 'Data di ritiro aggiunta con successo');
    }

    /**
     * Show the form for editing the specified withdraw date.
     */
    public function edit(SchoolWithdrawDate $withdrawDate): View
    {
        $this->authorizeWithdrawDate($withdrawDate);

        return view('staff.school-withdraw-dates.edit', [
            'withdrawDate' => $withdrawDate,
        ]);
    }

    /**
     * Update the specified withdraw date.
     */
    public function update(Request $request, SchoolWithdrawDate $withdrawDate): RedirectResponse
    {
        $this->authorizeWithdrawDate($withdrawDate);

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

        $withdrawDate->update([
            'scheduled_date' => $dateTime,
            'label' => $validated['label'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.withdraw-dates.index')
            ->with('success', 'Data di ritiro aggiornata con successo');
    }

    /**
     * Delete the specified withdraw date.
     */
    public function destroy(SchoolWithdrawDate $withdrawDate): RedirectResponse
    {
        $this->authorizeWithdrawDate($withdrawDate);

        $withdrawDate->delete();

        return redirect()->route('staff.withdraw-dates.index')
            ->with('success', 'Data di ritiro eliminata con successo');
    }

    /**
     * Toggle active status of a withdraw date.
     */
    public function toggle(SchoolWithdrawDate $withdrawDate): RedirectResponse
    {
        $this->authorizeWithdrawDate($withdrawDate);

        $withdrawDate->update([
            'is_active' => !$withdrawDate->is_active,
        ]);

        $status = $withdrawDate->is_active ? 'attivata' : 'disattivata';

        return back()->with('success', "Data di ritiro {$status} con successo");
    }

    /**
     * Authorize the user to manage the withdraw date.
     */
    public function authorizeWithdrawDate(SchoolWithdrawDate $withdrawDate): void
    {
        if ($withdrawDate->school_id !== auth()->user()->school_id) {
            abort(403, 'Non sei autorizzato a modificare questa data di ritiro');
        }
    }
}
