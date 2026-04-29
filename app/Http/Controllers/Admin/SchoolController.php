<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    /**
     * Show list of all schools.
     */
    public function index(): View
    {
        $schools = School::latest()->paginate(15);
        
        return view('admin.schools.index', [
            'schools' => $schools,
        ]);
    }

    /**
     * Show the form for creating a new school.
     */
    public function create(): View
    {
        return view('admin.schools.create');
    }

    /**
     * Store a newly created school in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:schools',
            'description' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255',
        ]);

        School::create($validated);

        return redirect()->route('admin.schools.index')->with('success', 'Scuola creata con successo.');
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school): View
    {
        return view('admin.schools.edit', [
            'school' => $school,
        ]);
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:schools,name,' . $school->id,
            'description' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255',
        ]);

        $school->update($validated);

        return redirect()->route('admin.schools.index')->with('success', 'Scuola aggiornata con successo.');
    }

    /**
     * Delete the specified school.
     */
    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return redirect()->route('admin.schools.index')->with('success', 'Scuola eliminata con successo.');
    }
}
