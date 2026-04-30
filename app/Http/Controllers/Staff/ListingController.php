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
            ->select('id', 'name', 'surname', 'email', 'code')
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
     * Mark a listing as sold.
     */
    public function markAsSold(BookListing $listing): RedirectResponse
    {
        $listing->update(['status' => 'sold']);

        return back()->with('success', 'Libro marcato come venduto!');
    }

    /**
     * Create a new book via AJAX (for staff to add new books).
     */
    public function createBook()
    {
        try {
            $validated = request()->validate([
                'title' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'isbn' => ['required', 'string', 'max:20', 'unique:books'],
                'original_price' => ['required', 'numeric', 'min:0'],
            ]);

            $book = Book::create([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'isbn' => $validated['isbn'],
                'original_price' => $validated['original_price'],
            ]);

            return response()->json([
                'book' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'original_price' => $book->original_price,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store multiple acquisitions in batch.
     */
    public function storeBatch()
    {
        try {
            $validated = request()->validate([
                'acquisitions' => ['required', 'array', 'min:1'],
                'acquisitions.*.seller_id' => ['required', 'exists:users,id'],
                'acquisitions.*.book_id' => ['required', 'exists:books,id'],
                'acquisitions.*.condition' => ['required', 'in:like-new,good,fair,poor'],
                'acquisitions.*.price' => ['required', 'numeric', 'min:0'],
                'acquisitions.*.leave' => ['nullable', 'boolean'],
            ]);

            $leave = request()->input('leave', false);
            $count = 0;
            foreach ($validated['acquisitions'] as $acquisition) {
                BookListing::create([
                    'book_id' => $acquisition['book_id'],
                    'seller_id' => $acquisition['seller_id'],
                    'condition' => $acquisition['condition'],
                    'price' => $acquisition['price'],
                    'status' => 'available',
                    'views' => 0,
                    'favorites' => 0,
                    'leave' => $acquisition['leave'] ?? false,
                ]);
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => $count . ' acquisizione/i completata/e',
                'count' => $count,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }
}
