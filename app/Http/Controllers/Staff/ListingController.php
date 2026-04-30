<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookListing;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ListingController extends Controller
{
    /**
     * Display the list of acquired books.
     */
    public function index(): View
    {
        $listings = BookListing::with('book', 'seller')
            ->where('status', 'available')
            ->latest()
            ->paginate(15);

        return view('staff.listings.index', [
            'listings' => $listings,
        ]);
    }

    /**
     * Search books by title or ISBN.
     */
    public function searchBooks()
    {
        $query = request()->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $books = Book::where('title', 'ilike', "%{$query}%")
            ->orWhere('isbn', 'ilike', "%{$query}%")
            ->orWhere('author', 'ilike', "%{$query}%")
            ->select('id', 'title', 'author', 'isbn', 'original_price')
            ->limit(10)
            ->get();

        return response()->json($books);
    }

    /**
     * Search sellers by name or email.
     */
    public function searchSellers()
    {
        $query = request()->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $users = \App\Models\User::where('name', 'ilike', "%{$query}%")
            ->orWhere('surname', 'ilike', "%{$query}%")
            ->orWhere('email', 'ilike', "%{$query}%")
            ->select('id', 'name', 'surname', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Show the form for creating a new acquisition.
     */
    public function create(): View
    {
        return view('staff.listings.create');
    }

    /**
     * Store a newly created acquisition in storage.
     */
    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'seller_id' => ['required', 'exists:users,id'],
            'book_id' => ['required', 'exists:books,id'],
            'condition' => ['required', 'in:like-new,good,fair,poor'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        BookListing::create([
            'book_id' => $validated['book_id'],
            'seller_id' => $validated['seller_id'],
            'condition' => $validated['condition'],
            'price' => $validated['price'],
            'status' => 'available',
            'views' => 0,
            'favorites' => 0,
        ]);

        return redirect()->route('staff.listings.index')
            ->with('success', 'Libro acquisito con successo!');
    }

    /**
     * Mark a listing as sold.
     */
    public function markAsSold(BookListing $listing): RedirectResponse
    {
        $listing->update(['status' => 'sold']);

        return back()->with('success', 'Libro marcato come venduto!');
    }
}
