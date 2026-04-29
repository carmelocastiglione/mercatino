<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\School;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'totalSchools' => School::count(),
            'totalUsers' => User::count(),
            'totalBooks' => Book::count(),
        ]);
    }

    /**
     * Show list of all schools.
     */
    public function schools(): View
    {
        $schools = School::latest()->paginate(15);
        
        return view('admin.schools.index', [
            'schools' => $schools,
        ]);
    }

    /**
     * Show the form for creating a new school.
     */
    public function createSchool(): View
    {
        return view('admin.schools.create');
    }

    /**
     * Store a newly created school in storage.
     */
    public function storeSchool(Request $request): RedirectResponse
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

        return redirect()->route('admin.schools')->with('success', 'Scuola creata con successo.');
    }

    /**
     * Show the form for editing the specified school.
     */
    public function editSchool(School $school): View
    {
        return view('admin.schools.edit', [
            'school' => $school,
        ]);
    }

    /**
     * Update the specified school in storage.
     */
    public function updateSchool(Request $request, School $school): RedirectResponse
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

        return redirect()->route('admin.schools')->with('success', 'Scuola aggiornata con successo.');
    }

    /**
     * Delete the specified school.
     */
    public function deleteSchool(School $school): RedirectResponse
    {
        $school->delete();

        return redirect()->route('admin.schools')->with('success', 'Scuola eliminata con successo.');
    }

    /**
     * Show list of all users.
     */
    public function users(): View
    {
        $users = User::latest()->paginate(15);
        
        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function createUser(): View
    {
        $schools = School::latest()->get();
        
        return view('admin.users.create', [
            'schools' => $schools,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:studente,staff,admin',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('admin.users')->with('success', 'Utente creato con successo.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function editUser(User $user): View
    {
        $schools = School::latest()->get();
        
        return view('admin.users.edit', [
            'user' => $user,
            'schools' => $schools,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:studente,staff,admin',
            'school_id' => 'nullable|exists:schools,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'Utente aggiornato con successo.');
    }

    /**
     * Delete the specified user.
     */
    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Non puoi eliminare il tuo account.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Utente eliminato con successo.');
    }

    /**
     * Show list of all books.
     */
    public function books(): View
    {
        $books = Book::with('seller')->latest()->paginate(15);
        
        return view('admin.books.index', [
            'books' => $books,
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function createBook(): View
    {
        $users = User::where('role', 'studente')->latest()->get();
        
        return view('admin.books.create', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created book in storage.
     */
    public function storeBook(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'school_class' => 'required|string|max:255',
            'condition' => 'required|in:like-new,good,fair,poor',
            'price' => 'required|numeric|min:0.01',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,reserved,sold,archived',
        ]);

        Book::create($validated);

        return redirect()->route('admin.books')->with('success', 'Libro creato con successo.');
    }

    /**
     * Show the form for editing the specified book.
     */
    public function editBook(Book $book): View
    {
        $users = User::where('role', 'studente')->latest()->get();
        
        return view('admin.books.edit', [
            'book' => $book,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified book in storage.
     */
    public function updateBook(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'school_class' => 'required|string|max:255',
            'condition' => 'required|in:like-new,good,fair,poor',
            'price' => 'required|numeric|min:0.01',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,reserved,sold,archived',
        ]);

        $book->update($validated);

        return redirect()->route('admin.books')->with('success', 'Libro aggiornato con successo.');
    }

    /**
     * Delete the specified book.
     */
    public function deleteBook(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('admin.books')->with('success', 'Libro eliminato con successo.');
    }
}


