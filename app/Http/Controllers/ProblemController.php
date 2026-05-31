<?php

namespace App\Http\Controllers;

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
        return view('problems.create');
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

    /**
     * Display a listing of problems for admin.
     */
    public function adminIndex(): View
    {
        $this->authorize('admin');

        $problems = Problem::with('user')
            ->where('status', '!=', 'deleted')
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.problems.index', [
            'problems' => $problems,
        ]);
    }

    /**
     * Mark a problem as resolved.
     */
    public function resolve(Problem $problem): RedirectResponse
    {
        $this->authorize('admin');

        $problem->update(['status' => 'resolved']);

        return redirect()->back()->with('success', 'Problema marcato come corretto.');
    }

    /**
     * Mark a problem as deleted.
     */
    public function delete(Problem $problem): RedirectResponse
    {
        $this->authorize('admin');

        $problem->update(['status' => 'deleted']);

        return redirect()->back()->with('success', 'Problema eliminato.');
    }
}
