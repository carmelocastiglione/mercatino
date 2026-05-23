<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BookDelivery;
use App\Models\Book;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class DeliveryController extends Controller
{
    /**
     * Search books by title, author or ISBN (filtered by user's school).
     */
    public function searchBooks(): JsonResponse
    {
        $query = request()->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $books = Book::bySchool(auth()->user()->school_id)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('isbn', 'ilike', "%{$query}%")
                    ->orWhere('author', 'ilike', "%{$query}%");
            })
            ->select('id', 'title', 'author', 'isbn', 'original_price')
            ->limit(10)
            ->get();

        return response()->json($books);
    }

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
     * Display deliveries filtered by status.
     */
    public function byStatus($status): View
    {
        $deliveries = auth()->user()->bookDeliveries()
            ->where('status', $status)
            ->with('book')
            ->latest()
            ->paginate(10);

        $statusLabels = [
            'pending' => 'In Sospeso',
            'approved' => 'Approvate',
            'rejected' => 'Rifiutate',
        ];

        return view('student.deliveries.index', [
            'deliveries' => $deliveries,
            'statusFilter' => $status,
            'statusLabel' => $statusLabels[$status] ?? $status,
        ]);
    }

    /**
     * Show the form for creating a new delivery.
     */
    public function create(): View
    {
        return view('student.deliveries.create');
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

        // Verifica che il libro appartenga alla scuola dell'utente
        $book = Book::find($validated['book_id']);
        if ($book->school_id !== auth()->user()->school_id) {
            return back()->with('error', 'Non puoi consegnare un libro che non appartiene alla tua scuola');
        }

        // Calcola il prezzo come metà del prezzo originale, arrotondato per difetto all'intero
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

        return view('student.deliveries.edit', [
            'delivery' => $delivery,
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

        // Verifica che il libro appartenga alla scuola dell'utente
        $book = Book::find($validated['book_id']);
        if ($book->school_id !== auth()->user()->school_id) {
            return back()->with('error', 'Non puoi consegnare un libro che non appartiene alla tua scuola');
        }

        // Calcola il prezzo come metà del prezzo originale, arrotondato per difetto all'intero
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
