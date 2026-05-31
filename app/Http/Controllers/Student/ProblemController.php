<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProblemController extends Controller
{
    /**
     * Show the form for creating a new problem report.
     */
    public function create(): View
    {
        return view('student.problems.create');
    }

    /**
     * Store a newly created problem in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'description.required' => 'La descrizione è obbligatoria.',
            'description.min' => 'La descrizione deve contenere almeno 10 caratteri.',
            'description.max' => 'La descrizione non può superare 2000 caratteri.',
        ]);

        Problem::create([
            'user_id' => auth()->id(),
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Problema segnalato con successo. Grazie per il feedback!');
    }
}
