<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SchoolDeliveryDate;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SchoolDeliveryDateController extends Controller
{
    /**
     * Display a listing of delivery dates for the staff's school.
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;
        
        $activeDates = SchoolDeliveryDate::bySchool($schoolId)
            ->where('is_active', true)
            ->orderBy('scheduled_date')
            ->get();

        $inactiveDates = SchoolDeliveryDate::bySchool($schoolId)
            ->where('is_active', false)
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return view('staff.school-delivery-dates.index', [
            'activeDates' => $activeDates,
            'inactiveDates' => $inactiveDates,
        ]);
    }

    /**
     * Show the form for creating a new delivery date.
     */
    public function create(): View
    {
        return view('staff.school-delivery-dates.create');
    }

    /**
     * Store a newly created delivery date.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scheduled_date' => 'required|date_format:Y-m-d|after:today',
            'label' => 'nullable|string|max:255',
        ], [
            'scheduled_date.required' => 'La data di consegna è obbligatoria',
            'scheduled_date.date_format' => 'La data deve essere in formato YYYY-MM-DD',
            'scheduled_date.after' => 'La data deve essere nel futuro',
            'label.max' => 'L\'etichetta non può superare 255 caratteri',
        ]);

        $schoolId = auth()->user()->school_id;

        // Converti la data in datetime (mezzanotte)
        $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['scheduled_date'])->startOfDay();

        SchoolDeliveryDate::create([
            'school_id' => $schoolId,
            'scheduled_date' => $dateTime,
            'label' => $validated['label'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('staff.delivery-dates.index')
            ->with('success', 'Data di consegna aggiunta con successo');
    }

    /**
     * Show the form for editing the specified delivery date.
     */
    public function edit(SchoolDeliveryDate $deliveryDate): View
    {
        $this->authorize($deliveryDate);

        return view('staff.school-delivery-dates.edit', [
            'deliveryDate' => $deliveryDate,
        ]);
    }

    /**
     * Update the specified delivery date.
     */
    public function update(Request $request, SchoolDeliveryDate $deliveryDate): RedirectResponse
    {
        $this->authorize($deliveryDate);

        $validated = $request->validate([
            'scheduled_date' => 'required|date_format:Y-m-d',
            'label' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ], [
            'scheduled_date.required' => 'La data di consegna è obbligatoria',
            'scheduled_date.date_format' => 'La data deve essere in formato YYYY-MM-DD',
            'label.max' => 'L\'etichetta non può superare 255 caratteri',
        ]);

        $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['scheduled_date'])->startOfDay();

        $deliveryDate->update([
            'scheduled_date' => $dateTime,
            'label' => $validated['label'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.delivery-dates.index')
            ->with('success', 'Data di consegna aggiornata con successo');
    }

    /**
     * Delete the specified delivery date.
     */
    public function destroy(SchoolDeliveryDate $deliveryDate): RedirectResponse
    {
        $this->authorize($deliveryDate);

        $deliveryDate->delete();

        return redirect()->route('staff.delivery-dates.index')
            ->with('success', 'Data di consegna eliminata con successo');
    }

    /**
     * Toggle active status of a delivery date.
     */
    public function toggle(SchoolDeliveryDate $deliveryDate): RedirectResponse
    {
        $this->authorize($deliveryDate);

        $deliveryDate->update([
            'is_active' => !$deliveryDate->is_active,
        ]);

        $status = $deliveryDate->is_active ? 'attivata' : 'disattivata';

        return back()->with('success', "Data di consegna {$status} con successo");
    }

    /**
     * Authorize the user to manage the delivery date.
     */
    protected function authorize(SchoolDeliveryDate $deliveryDate): void
    {
        if ($deliveryDate->school_id !== auth()->user()->school_id) {
            abort(403, 'Non sei autorizzato a modificare questa data di consegna');
        }
    }
}
