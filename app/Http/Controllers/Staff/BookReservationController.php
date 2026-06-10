<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Middleware\StaffMiddleware;
use App\Models\BookReservationBatch;
use App\Models\BookReservation;
use App\Models\BookSale;
use App\Models\User;
use App\Helpers\PriceHelper;
use App\Services\NotificationService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BookReservationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
        $this->middleware('auth');
        $this->middleware(StaffMiddleware::class);
    }

    /**
     * Display a listing of all book reservation batches with stats.
     */
    public function index(): View
    {
        $query = request()->input('q', '');

        // Totals without filters
        $pendingCount = BookReservationBatch::where('status', 'pending')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $confirmedCount = BookReservationBatch::where('status', 'confirmed')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $cancelledCount = BookReservationBatch::where('status', 'cancelled')
            ->bySchool(auth()->user()->school_id)
            ->count();

        // All batches for table (no status filter)
        $batches = BookReservationBatch::with('user', 'bookReservations.bookListing.book')
            ->bySchool(auth()->user()->school_id)
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($groupQuery) use ($query) {
                    $groupQuery->where('ean13', 'ilike', "%{$query}%")
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery->where('surname', 'ilike', "%{$query}%")
                                ->orWhere('email', 'ilike', "%{$query}%")
                                ->orWhere('code', 'ilike', "%{$query}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.book-reservations.index', [
            'batches' => $batches,
            'pendingCount' => $pendingCount,
            'confirmedCount' => $confirmedCount,
            'cancelledCount' => $cancelledCount,
            'filterQuery' => $query,
        ]);
    }

    /**
     * Display book reservations filtered by batch status.
     */
    public function byStatus($status): View
    {
        $query = request()->input('q', '');

        // Totals without filters
        $pendingCount = BookReservationBatch::where('status', 'pending')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $confirmedCount = BookReservationBatch::where('status', 'confirmed')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $cancelledCount = BookReservationBatch::where('status', 'cancelled')
            ->bySchool(auth()->user()->school_id)
            ->count();

        // Batches filtered by status
        $batches = BookReservationBatch::where('status', $status)
            ->with('user', 'bookReservations.bookListing.book')
            ->bySchool(auth()->user()->school_id)
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($groupQuery) use ($query) {
                    $groupQuery->where('ean13', 'ilike', "%{$query}%")
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery->where('surname', 'ilike', "%{$query}%")
                                ->orWhere('email', 'ilike', "%{$query}%")
                                ->orWhere('code', 'ilike', "%{$query}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusLabels = [
            'pending' => 'In Attesa',
            'confirmed' => 'Valutate',
            'cancelled' => 'Cancellate',
        ];

        return view('staff.book-reservations.index', [
            'batches' => $batches,
            'pendingCount' => $pendingCount,
            'confirmedCount' => $confirmedCount,
            'cancelledCount' => $cancelledCount,
            'statusFilter' => $status,
            'statusLabel' => $statusLabels[$status] ?? $status,
            'filterQuery' => $query,
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

        $school = $student->school;

        // Get all pending batches for this student with their pending reservations
        $batches = BookReservationBatch::where('user_id', $studentId)
            ->where('school_id', $staffSchoolId)
            ->where('status', 'pending')
            ->with(['bookReservations' => function ($query) {
                $query->where('status', 'pending')
                      ->with('bookListing.book', 'bookListing.seller.school');
            }])
            ->latest()
            ->get();

        // Calcola i prezzi per ogni reservazione con la fee di vendita sommata
        $batches->each(function ($batch) use ($school) {
            $batch->bookReservations->each(function ($reservation) use ($school) {
                // Calcola marketplace_price (metà del prezzo originale)
                $originalPrice = $reservation->bookListing->book->original_price;
                $marketplacePrice = floor($originalPrice) / 2;
                
                // Fee di vendita (sommata, non sottratta)
                $fee = $school->sales_fee ?? 0;
                $total = $marketplacePrice + $fee;

                // Salva i dati nel modello per usarli nella blade
                $reservation->price_data = [
                    'original_price' => (float) $originalPrice,
                    'marketplace_price' => (float) $marketplacePrice,
                    'fee' => (float) $fee,
                    'total' => (float) $total,
                ];
            });
        });

        // Count total pending reservations across all batches
        $pendingCount = $batches->sum(function ($batch) {
            return $batch->bookReservations->count();
        });

        // Prepare batchesForJson for JavaScript (includes all reservations data)
        $batchesForJson = $batches->map(function ($batch) {
            return [
                'batch' => ['id' => $batch->id, 'ean13' => $batch->ean13],
                'reservations' => $batch->bookReservations->map(function ($res) {
                    return ['id' => $res->id];
                })->toArray(),
            ];
        })->toArray();

        // Collect all reservations for JavaScript
        $reservations = $batches->flatMap(function ($batch) {
            return $batch->bookReservations;
        });

        return view('staff.book-reservations.student', [
            'student' => $student,
            'batches' => $batches,
            'reservations' => $reservations,
            'batchesForJson' => $batchesForJson,
            'pendingCount' => $pendingCount,
        ]);
    }

    /**
     * Search for students by surname, email, code, or reservation batch code.
     */
    public function searchStudents(): JsonResponse
    {
        $query = request()->input('q');
        $staffSchoolId = auth()->user()->school_id;

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $students = User::where('school_id', $staffSchoolId)
            ->where(function ($q) use ($query) {
                $q->where('surname', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%")
                    ->orWhere('code', 'ilike', "%{$query}%")
                    ->orWhereHas('bookReservationBatches', function ($batchQuery) use ($query) {
                        $batchQuery->where('ean13', 'ilike', "%{$query}%");
                    });
            })
            ->select('id', 'name', 'surname', 'email', 'code')
            ->limit(10)
            ->get();

        return response()->json($students);
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
     * Approve a single book reservation (JSON API endpoint - verification only)
     */
    public function approveSingle(Request $request): JsonResponse
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

        // Just acknowledge, don't save
        return response()->json(['success' => true, 'message' => 'Pronto per approvazione']);
    }

    /**
     * Reject a single book reservation (JSON API endpoint - verification only)
     */
    public function rejectSingle(Request $request): JsonResponse
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

        // Just acknowledge, don't save
        return response()->json(['success' => true, 'message' => 'Pronto per rifiuto']);
    }

    /**
     * Approve multiple book reservations (JSON API endpoint)
     */
    public function approveBulk(Request $request): JsonResponse
    {
        $reservationIds = $request->input('reservation_ids', []);

        if (empty($reservationIds)) {
            return response()->json(['success' => false, 'message' => 'Nessuna prenotazione specificata'], 400);
        }

        $reservations = BookReservation::whereIn('id', $reservationIds)
            ->where('status', 'pending')
            ->with('batch')
            ->get();

        if ($reservations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nessuna prenotazione pending trovata'], 404);
        }

        // Verify all reservations belong to staff's school
        foreach ($reservations as $reservation) {
            if ($reservation->batch->user->school_id !== auth()->user()->school_id) {
                return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
            }
        }

        $approvedCount = 0;
        foreach ($reservations as $reservation) {
            $reservation->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
            $approvedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$approvedCount prenotazioni approvate",
            'approved_count' => $approvedCount
        ]);
    }

    /**
     * Reject multiple book reservations (JSON API endpoint)
     */
    public function rejectMultiple(Request $request): JsonResponse
    {
        $reservationIds = $request->input('reservation_ids', []);

        if (empty($reservationIds)) {
            return response()->json(['success' => false, 'message' => 'Nessuna prenotazione specificata'], 400);
        }

        $reservations = BookReservation::whereIn('id', $reservationIds)
            ->where('status', 'pending')
            ->with('batch', 'bookListing')
            ->get();

        if ($reservations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nessuna prenotazione pending trovata'], 404);
        }

        // Verify all reservations belong to staff's school
        foreach ($reservations as $reservation) {
            if ($reservation->batch->user->school_id !== auth()->user()->school_id) {
                return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
            }
        }

        $rejectedCount = 0;
        foreach ($reservations as $reservation) {
            $reservation->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);
            
            // Restore book listing to available
            $reservation->bookListing->update(['status' => 'available']);
            
            // Notify seller when individual reservation is rejected
            $this->notificationService->notifyBookReservationRejected($reservation->bookListing);
            
            $rejectedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$rejectedCount prenotazioni rifiutate",
            'rejected_count' => $rejectedCount
        ]);
    }

    /**
     * Update batch status (JSON API endpoint)
     */
    public function updateBatchStatus(Request $request): JsonResponse
    {
        $batchIds = $request->input('batch_ids', []);
        $status = $request->input('status', 'confirmed');

        if (empty($batchIds)) {
            return response()->json(['success' => false, 'message' => 'Nessun batch specificato'], 400);
        }

        // Valida lo stato
        $allowedStatuses = ['pending', 'confirmed', 'rejected'];
        if (!in_array($status, $allowedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Stato non valido'], 400);
        }

        $batches = BookReservationBatch::whereIn('id', $batchIds)->get();

        if ($batches->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nessun batch trovato'], 404);
        }

        $staffSchoolId = auth()->user()->school_id;
        $updatedCount = 0;

        foreach ($batches as $batch) {
            // Verifica che il batch appartenga alla scuola dello staff
            if ($batch->user->school_id !== $staffSchoolId) {
                return response()->json(['success' => false, 'message' => 'Non autorizzato ad aggiornare questo batch'], 403);
            }

            $batch->update([
                'status' => $status,
                'confirmed_at' => $status === 'confirmed' ? now() : null,
                'rejected_at' => $status === 'rejected' ? now() : null,
            ]);

            $updatedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$updatedCount batch aggiornati",
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Store approved reservation IDs in session before redirecting to sales
     */
    public function storeSessionApprovals(Request $request): JsonResponse
    {
        $approvedIds = $request->input('approved_reservation_ids', []);
        $studentId = $request->input('student_id');

        if (empty($approvedIds)) {
            return response()->json(['success' => false, 'message' => 'Nessuna prenotazione'], 400);
        }

        // Store in session
        session()->put([
            'approved_reservation_ids' => $approvedIds,
            'student_id' => $studentId
        ]);

        return response()->json(['success' => true]);
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

            // Get approved reservation IDs from session (set by storeSessionApprovals)
            $approvedIds = session()->get('approved_reservation_ids', []);

            if (empty($approvedIds)) {
                return redirect()->back()->withErrors(['error' => 'Nessuna prenotazione approvata in questa sessione']);
            }

            // Get ONLY the confirmed reservations that were approved in this session
            $confirmedReservations = BookReservation::whereIn('id', $approvedIds)
                ->where('status', 'confirmed')
                ->with('batch', 'bookListing.book')
                ->get();

            if ($confirmedReservations->isEmpty()) {
                return redirect()->back()->withErrors(['error' => 'Nessuna prenotazione approvata trovata']);
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
