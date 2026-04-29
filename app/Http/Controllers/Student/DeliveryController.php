<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BookDelivery;
use App\Models\Book;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the student's deliveries.
     */
    public function index(): View
    {
        $deliveries = auth()->user()->bookDeliveries()
            ->with('book')
            ->latest()
            ->paginate(10);

        return view('student.deliveries.index', [
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Show the form for creating a new delivery.
     */
    public function create(): View
    {
        $books = Book::latest()->get();

        return view('student.deliveries.create', [
            'books' => $books,
        ]);
    }

    /**
     * Store a newly created delivery in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'condition' => 'required|in:like-new,good,fair,poor',
        ], [
            'book_id.required' => 'Seleziona un libro dal catalogo',
            'book_id.exists' => 'Il libro selezionato non esiste',
            'condition.required' => 'Specifica le condizioni del libro',
            'condition.in' => 'Condizioni non valide',
        ]);

        // Calcola il prezzo come metà del prezzo originale, arrotondato per difetto all'intero
        $book = Book::find($validated['book_id']);
        $price = floor($book->original_price / 2);

        auth()->user()->bookDeliveries()->create([
            'book_id' => $validated['book_id'],
            'condition' => $validated['condition'],
            'price' => $price,
            'status' => 'pending',
        ]);

        return redirect()->route('student.deliveries.index')
            ->with('success', 'Consegna prenotata con successo! Lo staff la esaminerà presto.');
    }

    /**
     * Show the form for editing the specified delivery.
     */
    public function edit(BookDelivery $delivery): View
    {
        $this->authorizeStudent($delivery);
        
        $books = Book::latest()->get();

        return view('student.deliveries.edit', [
            'delivery' => $delivery,
            'books' => $books,
        ]);
    }

    /**
     * Update the specified delivery in storage.
     */
    public function update(Request $request, BookDelivery $delivery): RedirectResponse
    {
        $this->authorizeStudent($delivery);

        // Solo le consegne pending possono essere modificate
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi modificare solo le consegne in sospeso');
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'condition' => 'required|in:like-new,good,fair,poor',
        ], [
            'book_id.required' => 'Seleziona un libro dal catalogo',
            'book_id.exists' => 'Il libro selezionato non esiste',
            'condition.required' => 'Specifica le condizioni del libro',
            'condition.in' => 'Condizioni non valide',
        ]);

        // Calcola il prezzo come metà del prezzo originale, arrotondato per difetto all'intero
        $book = Book::find($validated['book_id']);
        $validated['price'] = floor($book->original_price / 2);

        $delivery->update($validated);

        return redirect()->route('student.deliveries.index')
            ->with('success', 'Consegna aggiornata con successo!');
    }

    /**
     * Remove the specified delivery from storage.
     */
    public function destroy(BookDelivery $delivery): RedirectResponse
    {
        $this->authorizeStudent($delivery);

        // Solo le consegne pending possono essere eliminate
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi eliminare solo le consegne in sospeso');
        }

        $delivery->delete();

        return redirect()->route('student.deliveries.index')
            ->with('success', 'Consegna annullata con successo!');
    }

    /**
     * Verify that the authenticated user owns the delivery.
     */
    private function authorizeStudent(BookDelivery $delivery): void
    {
        if ($delivery->user_id !== auth()->id()) {
            abort(403, 'Non sei autorizzato ad accedere a questa consegna');
        }
    }
}
