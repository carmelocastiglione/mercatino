<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Middleware\StaffMiddleware;
use App\Models\BookReservationBatch;
use App\Models\BookReservation;
use App\Models\BookSale;
use App\Events\BookReservationBatchConfirmed;
use App\Events\BookReservationBatchRejected;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class BookReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(StaffMiddleware::class);
    }

    /**
     * Display a listing of all pending book reservation batches.
     */
    public function index(): View
    {
        $query = BookReservationBatch::with([
            'user',
            'bookReservations.bookListing.book',
        ])->orderByDesc('created_at');

        // Filter by school if user is not admin
        if (auth()->user()->role === 'staff') {
            $query->bySchool(auth()->user()->school_id);
        }

        $batches = $query->paginate(15);

        return view('staff.book-reservations.index', compact('batches'));
    }

    /**
     * Display the specified book reservation batch.
     */
    public function show(BookReservationBatch $bookReservationBatch): View
    {
        $this->authorize('view', $bookReservationBatch);

        $bookReservationBatch->load([
            'user',
            'bookReservations.bookListing.book',
        ]);

        return view('staff.book-reservations.show', [
            'batch' => $bookReservationBatch,
        ]);
    }

    /**
     * Confirm the entire book reservation batch and create BookSale records.
     */
    public function confirm(BookReservationBatch $bookReservationBatch): RedirectResponse
    {
        $this->authorize('view', $bookReservationBatch);

        if (!$bookReservationBatch->isPending()) {
            return redirect()->back()
                ->withErrors(['error' => 'Questa prenotazione non è in sospeso.']);
        }

        $staffUser = auth()->user();

        // Update batch status
        $bookReservationBatch->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Process each reservation
        foreach ($bookReservationBatch->bookReservations as $reservation) {
            // Update reservation status
            $reservation->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Create BookSale
            BookSale::create([
                'book_listing_id' => $reservation->book_listing_id,
                'sold_by' => $staffUser->id,
                'buyer_id' => $bookReservationBatch->user_id,
            ]);

            // Update book listing status to sold
            $reservation->bookListing->update(['status' => 'sold']);
        }

        // Dispatch event
        BookReservationBatchConfirmed::dispatch($bookReservationBatch);

        return redirect()->route('staff.book-reservations.show', $bookReservationBatch)
            ->with('success', 'Prenotazione confermata e vendite registrate con successo.');
    }

    /**
     * Reject the entire book reservation batch.
     */
    public function reject(BookReservationBatch $bookReservationBatch): RedirectResponse
    {
        $this->authorize('view', $bookReservationBatch);

        if (!$bookReservationBatch->isPending()) {
            return redirect()->back()
                ->withErrors(['error' => 'Questa prenotazione non è in sospeso.']);
        }

        // Update batch status
        $bookReservationBatch->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        // Reject all reservations and restore book availability
        foreach ($bookReservationBatch->bookReservations as $reservation) {
            $reservation->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);

            // Restore book to available
            $reservation->bookListing->update(['status' => 'available']);
        }

        // Dispatch event
        BookReservationBatchRejected::dispatch($bookReservationBatch);

        return redirect()->route('staff.book-reservations.index')
            ->with('success', 'Prenotazione rifiutata e libri ripristinati come disponibili.');
    }
}
