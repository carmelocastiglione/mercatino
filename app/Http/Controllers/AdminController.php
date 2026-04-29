<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
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
        // Get statistics
        $totalUsers = User::count();
        $studentCount = User::where('role', 'studente')->count();
        $totalBooks = Book::count();
        $totalTransactions = 0; // Will update when Transaction model is ready

        // Get recent users (last 5)
        $recentUsers = User::latest()->take(5)->get();

        // Get recent books
        $recentBooks = Book::with('seller')->latest()->take(5)->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'studentCount' => $studentCount,
            'totalBooks' => $totalBooks,
            'totalTransactions' => $totalTransactions,
            'recentUsers' => $recentUsers,
            'recentBooks' => $recentBooks,
        ]);
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
        return view('admin.users.create');
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
        return view('admin.users.edit', [
            'user' => $user,
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


