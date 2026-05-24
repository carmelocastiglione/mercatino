<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Helpers\PriceHelper;
use App\Models\BookDelivery;
use App\Models\BookDeliveryBatch;
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

        $user = auth()->user();
        $school = $user->school;

        $books = Book::bySchool($user->school_id)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('isbn', 'ilike', "%{$query}%")
                    ->orWhere('author', 'ilike', "%{$query}%");
            })
            ->select('id', 'title', 'author', 'isbn', 'original_price')
            ->limit(10)
            ->get()
            ->map(function ($book) use ($school) {
                $priceDetails = PriceHelper::calculatePrice($book->original_price, $school, true);
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'original_price' => $priceDetails['original_price'],
                    'marketplace_price' => $priceDetails['marketplace_price'],
                    'fee' => $priceDetails['fee'],
                    'price' => $priceDetails['total'],
                ];
            });

        return response()->json($books);
    }

    /**
     * Display a listing of the student's deliveries.
     */
    public function index(): View
    {
        $user = auth()->user();
        $deliveries = $user->bookDeliveries()
            ->with('book')
            ->latest()
            ->paginate(10);

        $pendingDeliveries = $user->bookDeliveries()->where('status', 'pending')->count();
        $approvedDeliveries = $user->bookDeliveries()->where('status', 'approved')->count();
        $rejectedDeliveries = $user->bookDeliveries()->where('status', 'rejected')->count();

        return view('student.deliveries.index', [
            'deliveries' => $deliveries,
            'pendingDeliveries' => $pendingDeliveries,
            'approvedDeliveries' => $approvedDeliveries,
            'rejectedDeliveries' => $rejectedDeliveries,
        ]);
    }

    /**
     * Display deliveries filtered by status.
     */
    public function byStatus($status): View
    {
        $user = auth()->user();
        $deliveries = $user->bookDeliveries()
            ->where('status', $status)
            ->with('book')
            ->latest()
            ->paginate(10);

        $statusLabels = [
            'pending' => 'In Sospeso',
            'approved' => 'Approvate',
            'rejected' => 'Rifiutate',
        ];

        $pendingDeliveries = $user->bookDeliveries()->where('status', 'pending')->count();
        $approvedDeliveries = $user->bookDeliveries()->where('status', 'approved')->count();
        $rejectedDeliveries = $user->bookDeliveries()->where('status', 'rejected')->count();

        return view('student.deliveries.index', [
            'deliveries' => $deliveries,
            'statusFilter' => $status,
            'statusLabel' => $statusLabels[$status] ?? $status,
            'pendingDeliveries' => $pendingDeliveries,
            'approvedDeliveries' => $approvedDeliveries,
            'rejectedDeliveries' => $rejectedDeliveries,
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
     * Add a book to the delivery batch cart (JSON endpoint).
     */
    public function addToCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'condition' => 'required|in:like-new,good,fair,poor',
        ]);

        // Verifica che il libro appartenga alla scuola dell'utente
        $user = auth()->user();
        $book = Book::find($validated['book_id']);
        if ($book->school_id !== $user->school_id) {
            return response()->json(['error' => 'Libro non disponibile'], 403);
        }

        $priceDetails = PriceHelper::calculatePrice($book->original_price, $user->school, true);

        return response()->json([
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'condition' => $validated['condition'],
            'original_price' => $priceDetails['original_price'],
            'marketplace_price' => $priceDetails['marketplace_price'],
            'fee' => $priceDetails['fee'],
            'price' => $priceDetails['total'],
        ]);
    }

    /**
     * Store multiple deliveries as a batch.
     */
    /**
     * Store multiple deliveries as a batch.
     */
    public function storeMultiple(Request $request): RedirectResponse
    {
        // Decodifica il JSON ricevuto dal form
        $itemsJson = $request->input('items');
        $items = json_decode($itemsJson, true);

        // Valida i dati decodificati
        if (!is_array($items) || count($items) === 0) {
            return back()->with('error', 'Seleziona almeno un libro');
        }

        // Valida ogni item
        $validator = \Illuminate\Support\Facades\Validator::make(['items' => $items], [
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.condition' => 'required|in:like-new,good,fair,poor',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = auth()->user();
        $school = $user->school;

        // Crea il batch
        $batch = $user->deliveryBatches()->create([
            'school_id' => $user->school_id,
            'status' => 'pending',
        ]);

        // Aggiungi i libri al batch
        foreach ($items as $item) {
            $book = Book::find($item['book_id']);
            
            // Verifica che il libro appartenga alla scuola dell'utente
            if ($book->school_id !== $user->school_id) {
                $batch->delete();
                return back()->with('error', 'Uno dei libri selezionati non appartiene alla tua scuola');
            }

            $priceDetails = PriceHelper::calculatePrice($book->original_price, $school, true);

            $user->bookDeliveries()->create([
                'batch_id' => $batch->id,
                'book_id' => $book->id,
                'condition' => $item['condition'],
                'price' => $priceDetails['total'],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('student.batches.show', $batch->id);
    }

    /**
     * Show delivery batch summary.
     */
    public function showBatch(BookDeliveryBatch $batch): View
    {
        // Autorizza l'utente a visualizzare solo i propri batch
        if ($batch->user_id !== auth()->id()) {
            abort(403, 'Non sei autorizzato ad accedere a questo batch');
        }

        $batch->load('deliveries.book');

        return view('student.deliveries.batch-show', [
            'batch' => $batch,
        ]);
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
