<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Middleware\StaffMiddleware;
use App\Models\BookReservationBatch;
use App\Models\BookReservation;
use App\Models\BookSale;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BookReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(StaffMiddleware::class);
    }

    /**
     * Display a listing of all pending book reservation batches with stats.
     */
    public function index(): View
    {
        $batches = BookReservationBatch::where('status', 'pending')
            ->with('user', 'bookReservations.bookListing.book')
            ->bySchool(auth()->user()->school_id)
            ->latest()
            ->paginate(10);

        $pendingCount = BookReservationBatch::where('status', 'pending')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $confirmedCount = BookReservationBatch::where('status', 'confirmed')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $rejectedCount = BookReservationBatch::where('status', 'rejected')
            ->bySchool(auth()->user()->school_id)
            ->count();

        return view('staff.book-reservations.index', [
            'batches' => $batches,
            'pendingCount' => $pendingCount,
            'confirmedCount' => $confirmedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }

    /**
     * Display all book reservations for a specific student (authorized by school)
     */
    public function studentReservations($studentId): View
    {
        $staffSchoolId = auth()->user()->school_id;
        $student = User::findOrFail($studentId);

        // Verify student is from the same school
        if ($student->school_id !== $staffSchoolId) {
            abort(403, 'Non puoi accedere alle prenotazioni di questo studente');
        }

        // Get all pending batches for this student with their pending reservations
        $batches = BookReservationBatch::where('user_id', $studentId)
            ->where('school_id', $staffSchoolId)
            ->where('status', 'pending')
            ->with(['bookReservations' => function ($query) {
                $query->where('status', 'pending')
                      ->with('bookListing.book');
            }])
            ->latest()
            ->get();

        // Count total pending reservations across all batches
        $pendingCount = $batches->sum(function ($batch) {
            return $batch->bookReservations->count();
        });

        return view('staff.book-reservations.student', [
            'student' => $student,
            'batches' => $batches,
            'pendingCount' => $pendingCount,
        ]);
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
     * Approve a single book reservation (JSON API endpoint)
     */
    public function approveReservation(Request $request): JsonResponse
    {
        $reservationId = $request->query('reservation_id');
        $reservation = BookReservation::find($reservationId);

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Prenotazione non trovata'], 404);
        }

        $batch = $reservation->batch;
        if ($batch->user->school_id !== auth()->user()->school_id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        if ($reservation->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Prenotazione non in sospeso'], 400);
        }

        // Mark as approved but don't change book listing status yet
        $reservation->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Prenotazione approvata']);
    }

    /**
     * Reject a single book reservation (JSON API endpoint)
     */
    public function rejectReservation(Request $request): JsonResponse
    {
        $reservationId = $request->query('reservation_id');
        $reservation = BookReservation::find($reservationId);

        if (!$reservation) {
            return response()->json(['success' => false, 'message' => 'Prenotazione non trovata'], 404);
        }

        $batch = $reservation->batch;
        if ($batch->user->school_id !== auth()->user()->school_id) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        if ($reservation->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Prenotazione non in sospeso'], 400);
        }

        // Mark as rejected and restore book listing to available
        $reservation->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        $reservation->bookListing->update(['status' => 'available']);

        return response()->json(['success' => true, 'message' => 'Prenotazione rifiutata']);
    }

    /**
     * Prepare sales from approved reservations and redirect to sales.create
     */
    public function prepareSales(Request $request): RedirectResponse
    {
        try {
            $studentId = $request->input('student_id');
            $student = User::findOrFail($studentId);

            if ($student->school_id !== auth()->user()->school_id) {
                abort(403, 'Non autorizzato');
            }

            // Get all confirmed reservations for this student from pending batches
            $confirmedReservations = BookReservation::whereHas('batch', function ($query) use ($studentId) {
                $query->where('user_id', $studentId)->where('status', 'pending');
            })->where('status', 'confirmed')
                ->with('batch', 'bookListing.book')
                ->get();

            if ($confirmedReservations->isEmpty()) {
                return redirect()->back()->withErrors(['error' => 'Nessuna prenotazione approvata']);
            }

            // Prepare data for sales creation
            $approvedReservations = $confirmedReservations->map(function ($reservation) {
                return [
                    'book_listing_id' => $reservation->book_listing_id,
                    'book_title' => $reservation->bookListing->book->title,
                    'book_author' => $reservation->bookListing->book->author,
                    'book_isbn' => $reservation->bookListing->book->isbn,
                    'book_price' => $reservation->bookListing->price_sell ?? $reservation->bookListing->price,
                    'book_condition' => $reservation->bookListing->condition,
                    'seller_id' => $reservation->bookListing->seller_id,
                    'seller_name' => $reservation->bookListing->seller->name ?? '',
                    'seller_surname' => $reservation->bookListing->seller->surname ?? '',
                    'seller_code' => $reservation->bookListing->seller->code ?? '',
                ];
            })->toArray();

            // Collect batch IDs and update their status to confirmed
            $batchIds = $confirmedReservations->pluck('book_reservation_batch_id')->unique()->toArray();
            if (!empty($batchIds)) {
                BookReservationBatch::whereIn('id', $batchIds)->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);
            }

            // Store in session using put() instead of with() to persist data
            session()->put([
                'approved_reservations' => $approvedReservations,
                'student_id' => $studentId
            ]);

            return redirect()->route('staff.sales.create');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Errore nella preparazione delle vendite']);
        }
    }

    /**
     * Reject the entire book reservation batch (legacy, kept for reference).
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

        return redirect()->route('staff.book-reservations.index')
            ->with('success', 'Prenotazione rifiutata e libri ripristinati come disponibili.');
    }
}
