<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Show list of all books in the catalog.
     */
    public function index(): View
    {
        $books = Book::latest()->paginate(15);
        
        return view('admin.books.index', [
            'books' => $books,
        ]);
    }

    /**
     * Show the form for creating a new book in the catalog.
     */
    public function create(): View
    {
        return view('admin.books.create');
    }

    /**
     * Store a newly created book in the catalog.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books',
            'description' => 'nullable|string',
            'subject' => 'nullable|string|max:255',
            'school_class' => 'nullable|string|max:255',
            'original_price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|string|max:255',
        ]);

        Book::create($validated);

        return redirect()->route('admin.books.index')->with('success', 'Libro aggiunto al catalogo con successo.');
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book): View
    {
        return view('admin.books.edit', [
            'book' => $book,
        ]);
    }

    /**
     * Update the specified book in the catalog.
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'description' => 'nullable|string',
            'subject' => 'nullable|string|max:255',
            'school_class' => 'nullable|string|max:255',
            'original_price' => 'nullable|numeric|min:0',
            'cover_image' => 'nullable|string|max:255',
        ]);

        $book->update($validated);

        return redirect()->route('admin.books.index')->with('success', 'Libro aggiornato con successo.');
    }

    /**
     * Delete the specified book.
     */
    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Libro eliminato dal catalogo.');
    }
}
