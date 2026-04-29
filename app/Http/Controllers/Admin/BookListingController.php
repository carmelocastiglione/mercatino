<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookListing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookListingController extends Controller
{
    /**
     * Show list of all book listings (copies for sale).
     */
    public function index(): View
    {
        $listings = BookListing::with('book', 'seller')->latest()->paginate(15);
        
        return view('admin.listings.index', [
            'listings' => $listings,
        ]);
    }

    /**
     * Show the form for creating a new book listing.
     */
    public function create(): View
    {
        $books = Book::latest()->get();
        $users = User::where('role', 'studente')->latest()->get();
        
        return view('admin.listings.create', [
            'books' => $books,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created book listing.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'seller_id' => 'required|exists:users,id',
            'condition' => 'required|in:like-new,good,fair,poor',
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|in:available,reserved,sold,archived',
        ]);

        BookListing::create($validated);

        return redirect()->route('admin.listings.index')->with('success', 'Annuncio creato con successo.');
    }

    /**
     * Show the form for editing the specified listing.
     */
    public function edit(BookListing $listing): View
    {
        $books = Book::latest()->get();
        $users = User::where('role', 'studente')->latest()->get();
        
        return view('admin.listings.edit', [
            'listing' => $listing,
            'books' => $books,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified listing.
     */
    public function update(Request $request, BookListing $listing): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'seller_id' => 'required|exists:users,id',
            'condition' => 'required|in:like-new,good,fair,poor',
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|in:available,reserved,sold,archived',
        ]);

        $listing->update($validated);

        return redirect()->route('admin.listings.index')->with('success', 'Annuncio aggiornato con successo.');
    }

    /**
     * Delete the specified listing.
     */
    public function destroy(BookListing $listing): RedirectResponse
    {
        $listing->delete();

        return redirect()->route('admin.listings.index')->with('success', 'Annuncio eliminato con successo.');
    }
}
