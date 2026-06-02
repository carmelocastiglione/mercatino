<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BookListing;
use App\Models\BookReservationBatch;
use App\Models\BookReservation;
use App\Models\BookSale;
use App\Events\BookReservationBatchCreated;
use App\Events\BookReservationBatchCancelled;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BookReservationController extends Controller
{
    /**
     * Display a listing of the student's book reservations.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $batches = BookReservationBatch::where('user_id', $user->id)
            ->with(['bookReservations.bookListing.book'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('student.book-reservations.index', compact('batches'));
    }

    /**
     * Show the form for creating a new book reservation.
     */
    public function create(): View
    {
        return view('student.book-reservations.create');
    }

    /**
     * Search available books acquired by staff (JSON endpoint).
     */
    public function searchAcquisitionBooks(): JsonResponse
    {
        $query = request()->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();

        $books = BookListing::where('acquisition_id', '!=', null)
            ->where('status', 'available')
            ->bySchool($user->school_id)
            ->with('book')
            ->whereHas('book', function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('isbn', 'ilike', "%{$query}%")
                    ->orWhere('author', 'ilike', "%{$query}%");
            })
            ->select('id', 'book_id', 'price', 'price_sell', 'condition', 'status')
            ->limit(10)
            ->get()
            ->map(function ($listing) {
                return [
                    'id' => $listing->id,
                    'title' => $listing->book->title,
                    'author' => $listing->book->author,
                    'isbn' => $listing->book->isbn,
                    'price' => $listing->price_sell ?? $listing->price,
                    'condition' => $listing->condition,
                ];
            });

        return response()->json($books);
    }

    /**
     * Check if books are still available.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $bookListingIds = $request->input('book_listing_ids', []);
        
        if (empty($bookListingIds)) {
            return response()->json(['available' => false, 'message' => 'Nessun libro selezionato']);
        }

        $unavailableBooks = BookListing::whereIn('id', $bookListingIds)
            ->where('status', '!=', 'available')
            ->with('book')
            ->get();

        if ($unavailableBooks->isNotEmpty()) {
            $titles = $unavailableBooks->map(fn ($book) => $book->book->title)->join(', ');
            return response()->json([
                'available' => false,
                'message' => 'I seguenti libri non sono più disponibili: ' . $titles
            ]);
        }

        return response()->json(['available' => true]);
    }

    /**
     * Store a newly created book reservation batch in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'book_listing_ids' => 'required|array|min:1',
            'book_listing_ids.*' => 'required|exists:book_listings,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify all books are available and belong to the same school
        $bookListings = BookListing::whereIn('id', $validated['book_listing_ids'])
            ->where('status', 'available')
            ->bySchool($user->school_id)
            ->get();

        if ($bookListings->count() !== count($validated['book_listing_ids'])) {
            return redirect()->back()
                ->withErrors(['error' => 'Uno o più libri non sono più disponibili. Riprova.']);
        }

        // Create the batch
        $batch = BookReservationBatch::create([
            'user_id' => $user->id,
            'school_id' => $user->school_id,
            'status' => 'pending',
            'total_items' => $bookListings->count(),
            'notes' => $validated['notes'] ?? null,
            'reserved_at' => now(),
        ]);

        // Create individual reservations and mark books as reserved
        foreach ($bookListings as $listing) {
            BookReservation::create([
                'book_reservation_batch_id' => $batch->id,
                'book_listing_id' => $listing->id,
                'status' => 'pending',
                'reserved_at' => now(),
            ]);

            // Mark the book as reserved
            $listing->update(['status' => 'reserved']);
        }

        // Dispatch event
        BookReservationBatchCreated::dispatch($batch);

        return redirect()->route('student.book-reservations.show', $batch)
            ->with('success', 'Prenotazione creata con successo. Lo staff esaminerà la tua richiesta.');
    }

    /**
     * Display the specified book reservation batch.
     */
    public function show(BookReservationBatch $bookReservationBatch): View
    {
        $this->authorize('view', $bookReservationBatch);

        $bookReservationBatch->load([
            'bookReservations.bookListing.book',
            'user',
        ]);

        return view('student.book-reservations.show', [
            'batch' => $bookReservationBatch,
        ]);
    }

    /**
     * Cancel the specified book reservation batch (only if pending).
     */
    public function destroy(BookReservationBatch $bookReservationBatch): RedirectResponse
    {
        $this->authorize('delete', $bookReservationBatch);

        if (!$bookReservationBatch->isPending()) {
            return redirect()->back()
                ->withErrors(['error' => 'Puoi cancellare solo prenotazioni in sospeso.']);
        }

        // Mark batch as cancelled
        $bookReservationBatch->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Cancel all reservations and restore book availability
        foreach ($bookReservationBatch->bookReservations as $reservation) {
            $reservation->update(['status' => 'cancelled']);
            $reservation->bookListing->update(['status' => 'available']);
        }

        // Dispatch event
        BookReservationBatchCancelled::dispatch($bookReservationBatch);

        return redirect()->route('student.book-reservations.index')
            ->with('success', 'Prenotazione cancellata con successo.');
    }
}
