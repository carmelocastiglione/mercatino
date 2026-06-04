<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookListing;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Display all books in catalog (filtered by staff's school).
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;
        $query = request()->input('q', '');
        
        // Calculate statistics for all books in school
        $totalBooks = Book::bySchool($schoolId)->count();
        
        // Get all books with counts to calculate averages
        $allBooksStats = Book::bySchool($schoolId)
            ->withCount(['listings' => fn($q) => $q->where('status', 'available')])
            ->withCount('listings as total_listings')
            ->get();
        
        $avgAvailableCopies = $totalBooks > 0 
            ? $allBooksStats->sum('listings_count') / $totalBooks 
            : 0;
        
        $avgTotalCopies = $totalBooks > 0 
            ? $allBooksStats->sum('total_listings') / $totalBooks 
            : 0;
        
        // Get filtered/paginated books
        $books = Book::bySchool($schoolId)
            ->withCount(['listings' => fn($q) => $q->where('status', 'available')])
            ->withCount('listings as total_listings')
            ->when($query, function($q) use($query) {
                return $q->where(function($subQuery) use($query) {
                    $subQuery->where('title', 'ilike', "%{$query}%")
                        ->orWhere('author', 'ilike', "%{$query}%")
                        ->orWhere('isbn', 'ilike', "%{$query}%");
                });
            })
            ->orderBy('title', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('staff.books.index', [
            'books' => $books,
            'query' => $query,
            'filterQuery' => $query,
            'totalBooks' => $totalBooks,
            'avgAvailableCopies' => $avgAvailableCopies,
            'avgTotalCopies' => $avgTotalCopies,
        ]);
    }

    /**
     * Show the form to create a new book.
     */
    public function create(): View
    {
        return view('staff.books.create');
    }

    /**
     * Store a new book in catalog.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')
                    ->where('school_id', auth()->user()->school_id)
            ],
            'description' => 'nullable|string|max:1000',
            'subject' => 'nullable|string|max:100',
            'school_class' => 'nullable|string|max:50',
            'original_price' => 'nullable|numeric|min:0|max:999.99',
        ]);

        $validated['school_id'] = auth()->user()->school_id;

        Book::create($validated);

        return redirect()->route('staff.books.index')
            ->with('success', 'Libro aggiunto al catalogo con successo!');
    }

    /**
     * Show the form to edit a book.
     */
    public function edit(Book $book): View
    {
        if (auth()->user()->school_id !== $book->school_id) {
            abort(403, 'Non hai accesso a questo libro');
        }
        
        return view('staff.books.edit', compact('book'));
    }

    /**
     * Update a book in catalog.
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        if (auth()->user()->school_id !== $book->school_id) {
            abort(403, 'Non hai accesso a questo libro');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')
                    ->where('school_id', auth()->user()->school_id)
                    ->ignore($book->id)
            ],
            'description' => 'nullable|string|max:1000',
            'subject' => 'nullable|string|max:100',
            'school_class' => 'nullable|string|max:50',
            'original_price' => 'nullable|numeric|min:0|max:999.99',
        ]);

        $book->update($validated);

        return redirect()->route('staff.books.index')
            ->with('success', 'Libro aggiornato con successo!');
    }

    /**
     * Delete a book from catalog.
     */
    public function destroy(Book $book): RedirectResponse
    {
        if (auth()->user()->school_id !== $book->school_id) {
            abort(403, 'Non hai accesso a questo libro');
        }

        $hasListings = $book->listings()->exists();

        if ($hasListings) {
            return back()->with('error', 'Non puoi eliminare un libro che ha copie in catalogo. Elimina prima le copie.');
        }

        $book->delete();

        return redirect()->route('staff.books.index')
            ->with('success', 'Libro eliminato dal catalogo!');
    }
}
