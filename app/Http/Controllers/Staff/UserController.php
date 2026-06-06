<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display all users in staff's school (filtered by type: students, staff, or all).
     */
    public function index(Request $request): View
    {
        $schoolId = auth()->user()->school_id;
        $type = $request->input('type', ''); // Empty means show all
        $query = $request->input('q', '');
        
        // Get counts for both types
        $studentsCount = User::where('school_id', $schoolId)
            ->where('role', 'studente')
            ->orderBy('surname', 'asc')
            ->orderBy('name', 'asc')
            ->count();
        
        $staffCount = User::where('school_id', $schoolId)
            ->where('role', 'staff')
            ->orderBy('surname', 'asc')
            ->orderBy('name', 'asc')
            ->count();
        
        // Get filtered users
        $usersQuery = User::where('school_id', $schoolId);
        
        // Apply type filter only if specified
        if (!empty($type) && in_array($type, ['studente', 'staff'])) {
            $usersQuery->where('role', $type);
        }
        
        $users = $usersQuery
            ->when($query, function($q) use($query) {
                return $q->where(function($subQuery) use($query) {
                    $subQuery->where('surname', 'ilike', "%{$query}%")
                        ->orWhere('email', 'ilike', "%{$query}%")
                        ->orWhere('code', 'ilike', "%{$query}%")
                        ->orWhere('name', 'ilike', "%{$query}%");
                });
            })
            ->orderBy('surname', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('staff.users.index', [
            'users' => $users,
            'type' => $type,
            'query' => $query,
            'studentsCount' => $studentsCount,
            'staffCount' => $staffCount,
        ]);
    }

    /**
     * Show the form to create a new user.
     */
    public function create(): View
    {
        return view('staff.users.create');
    }

    /**
     * Store a new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
            ],
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['studente', 'staff'])],
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('staff.users.index', ['type' => $validated['role']])
            ->with('success', 'Utente creato con successo!');
    }

    /**
     * Show the form to edit a user.
     */
    public function edit(User $user): View
    {
        // Ensure user belongs to the same school
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        return view('staff.users.edit', ['user' => $user]);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Ensure user belongs to the same school
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['studente', 'staff'])],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('staff.users.index', ['type' => $user->role])
            ->with('success', 'Utente aggiornato con successo!');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort(403, 'Eliminazione non consentita.');
    }
}
