<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookDelivery;
use App\Models\BookDeliveryBatch;
use App\Models\BookListing;
use App\Models\User;
use App\Helpers\PriceHelper;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DeliveryController extends Controller
{
    /**
     * Display a listing of delivery batches (filtered by staff's school).
     */
    public function index(): View
    {
        $query = request()->input('q', '');

        // Totals without filters
        $pendingCount = BookDeliveryBatch::where('status', 'pending')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $submittedCount = BookDeliveryBatch::where('status', 'submitted')
            ->bySchool(auth()->user()->school_id)
            ->count();

        // All batches for table (no status filter)
        $batches = BookDeliveryBatch::with('user', 'deliveries.book')
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

        return view('staff.deliveries.index', [
            'batches' => $batches,
            'pendingCount' => $pendingCount,
            'submittedCount' => $submittedCount,
            'filterQuery' => $query,
        ]);
    }

    /**
     * Display deliveries filtered by batch status.
     */
    public function byStatus($status): View
    {
        $query = request()->input('q', '');

        // Totals without filters
        $pendingCount = BookDeliveryBatch::where('status', 'pending')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $submittedCount = BookDeliveryBatch::where('status', 'submitted')
            ->bySchool(auth()->user()->school_id)
            ->count();

        // Batches filtered by status
        $batches = BookDeliveryBatch::where('status', $status)
            ->with('user', 'deliveries.book')
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
            'pending' => 'Da Approvare',
            'submitted' => 'Valutate',
        ];

        return view('staff.deliveries.index', [
            'batches' => $batches,
            'pendingCount' => $pendingCount,
            'submittedCount' => $submittedCount,
            'statusFilter' => $status,
            'statusLabel' => $statusLabels[$status] ?? $status,
            'filterQuery' => $query,
        ]);
    }

    /**
     * Display the specified delivery for review (authorized by school).
     */
    public function show(BookDelivery $delivery): View
    {
        $staffSchoolId = auth()->user()->school_id;
        
        if ($delivery->book->school_id !== $staffSchoolId) {
            abort(403, 'Non puoi accedere a questa consegna');
        }

        return view('staff.deliveries.show', [
            'delivery' => $delivery->load('user', 'book'),
        ]);
    }

    /**
     * Approve the specified delivery (authorized by school).
     */
    public function approve(BookDelivery $delivery): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;
        
        if ($delivery->book->school_id !== $staffSchoolId) {
            return back()->with('error', 'Non puoi accedere a questa consegna');
        }

        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi approvare solo consegne in sospeso');
        }

        // Aggiorna la consegna
        $delivery->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        // Crea una nuova BookListing dal BookDelivery
        BookListing::create([
            'book_id' => $delivery->book_id,
            'seller_id' => $delivery->user_id,
            'condition' => $delivery->condition,
            'price' => $delivery->price,
            'status' => 'available',
        ]);

        return redirect()->route('staff.deliveries.index')
            ->with('success', 'Consegna approvata e aggiunta al catalogo!');
    }

    /**
     * Show the form for rejecting a delivery (authorized by school).
     */
    public function rejectForm(BookDelivery $delivery): View
    {
        $staffSchoolId = auth()->user()->school_id;
        
        if ($delivery->book->school_id !== $staffSchoolId) {
            abort(403, 'Non puoi accedere a questa consegna');
        }

        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi rifiutare solo consegne in sospeso');
        }

        return view('staff.deliveries.reject', [
            'delivery' => $delivery->load('user', 'book'),
        ]);
    }

    /**
     * Reject the specified delivery (authorized by school).
     */
    public function reject(Request $request, BookDelivery $delivery): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;
        
        if ($delivery->book->school_id !== $staffSchoolId) {
            return back()->with('error', 'Non puoi accedere a questa consegna');
        }

        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi rifiutare solo consegne in sospeso');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Specifica il motivo del rifiuto',
            'rejection_reason.max' => 'Il motivo non può superare 500 caratteri',
        ]);

        $delivery->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('staff.deliveries.index')
            ->with('success', 'Consegna rifiutata correttamente.');
    }

    /**
     * Display deliveries for a specific student (authorized by school)
     */
    public function studentDeliveries($studentId): View
    {
        $staffSchoolId = auth()->user()->school_id;
        $student = User::findOrFail($studentId);

        // Verifica che lo studente sia della stessa scuola
        if ($student->school_id !== $staffSchoolId) {
            abort(403, 'Non puoi accedere ai libri di questo studente');
        }

        $school = $student->school;
        $deliveries = BookDelivery::where('user_id', $studentId)
            ->where('status', 'pending')
            ->with('book', 'batch.scheduledDeliveryDate')
            ->bySchool($staffSchoolId)
            ->whereHas('batch', function ($query) {
                $query->where('status', 'pending');
            })
            ->latest()
            ->get();

        // Calcola i prezzi per ogni delivery usando PriceHelper
        $deliveriesWithPrices = $deliveries->map(function ($delivery) use ($school) {
            $priceData = PriceHelper::calculatePrice($delivery->book->original_price, $school, true);
            $delivery->price_data = $priceData;
            return $delivery;
        });

        // Raggruppa i deliveries per batch
        $batches = $deliveriesWithPrices->groupBy('batch_id')->map(function ($deliveries) {
            return [
                'batch' => $deliveries->first()->batch,
                'deliveries' => $deliveries,
            ];
        });

        // Prepara i dati per il JavaScript (array puro, non oggetti Eloquent)
        $batchesForJson = $batches->map(function ($batchData) {
            return [
                'batch' => [
                    'id' => $batchData['batch']->id,
                    'ean13' => $batchData['batch']->ean13,
                ],
                'deliveries' => $batchData['deliveries']->map(function ($delivery) {
                    return ['id' => $delivery->id];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return view('staff.deliveries.student', [
            'student' => $student,
            'batches' => $batches,
            'batchesForJson' => $batchesForJson,
            'deliveries' => $deliveriesWithPrices,
            'pendingCount' => $deliveriesWithPrices->count(),
        ]);
    }

    /**
     * Reject a delivery (JSON API endpoint, authorized by school)
     */
    public function rejectDeliveryJson(Request $request)
    {
        $staffSchoolId = auth()->user()->school_id;
        $deliveryId = $request->query('delivery_id');
        $delivery = BookDelivery::find($deliveryId);

        if (!$delivery) {
            return response()->json(['success' => false, 'message' => 'Consegna non trovata'], 404);
        }

        if ($delivery->book->school_id !== $staffSchoolId) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
        }

        if ($delivery->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Consegna non in sospeso'], 400);
        }

        $delivery->update([
            'status' => 'rejected',
            'rejection_reason' => 'Rifiutata dallo staff',
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Consegna rifiutata']);
    }

    /**
     * Approve multiple deliveries (JSON API endpoint, filtered by staff's school)
     */
    public function approveBulk(Request $request)
    {
        $deliveryIds = $request->input('delivery_ids', []);
        $modifiedDeliveries = $request->input('modified_deliveries', []);

        if (empty($deliveryIds)) {
            return response()->json(['success' => false, 'message' => 'Nessuna consegna specificata'], 400);
        }

        $deliveries = BookDelivery::whereIn('id', $deliveryIds)
            ->where('status', 'pending')
            ->bySchool(auth()->user()->school_id)
            ->get();

        if ($deliveries->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nessuna consegna pending trovata'], 404);
        }

        // Crea una mappa dei dati modificati per accesso veloce
        $modifiedMap = [];
        foreach ($modifiedDeliveries as $mod) {
            $modifiedMap[$mod['id']] = $mod;
        }

        $approvedCount = 0;
        foreach ($deliveries as $delivery) {
            // Applica le modifiche se esistono
            $condition = $delivery->condition;
            $price = $delivery->price;
            
            if (isset($modifiedMap[$delivery->id])) {
                $condition = $modifiedMap[$delivery->id]['condition'] ?? $condition;
                $price = $modifiedMap[$delivery->id]['price'] ?? $price;
            }

            $delivery->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'condition' => $condition,
                'price' => $price,
            ]);

            $approvedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$approvedCount consegne approvate",
            'approved_count' => $approvedCount
        ]);
    }

    /**
     * Update batch status (JSON API endpoint, filtered by staff's school)
     */
    public function updateBatchStatus(Request $request)
    {
        $batchIds = $request->input('batch_ids', []);
        $status = $request->input('status', 'submitted');

        if (empty($batchIds)) {
            return response()->json(['success' => false, 'message' => 'Nessun batch specificato'], 400);
        }

        // Valida lo stato
        $allowedStatuses = ['submitted', 'approved', 'rejected', 'delivered', 'pending'];
        if (!in_array($status, $allowedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Stato non valido'], 400);
        }

        $batches = BookDeliveryBatch::whereIn('id', $batchIds)
            ->with('deliveries.book')
            ->get();

        if ($batches->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nessun batch trovato'], 404);
        }

        $staffSchoolId = auth()->user()->school_id;
        $updatedCount = 0;

        foreach ($batches as $batch) {
            // Verifica che tutti i deliveries del batch appartengano alla scuola dello staff
            $isAuthorized = $batch->deliveries->every(function ($delivery) use ($staffSchoolId) {
                return $delivery->book->school_id === $staffSchoolId;
            });

            if (!$isAuthorized) {
                return response()->json(['success' => false, 'message' => 'Non autorizzato ad aggiornare questo batch'], 403);
            }

            $batch->update(['status' => $status]);
            $updatedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$updatedCount batch aggiornati a '$status'",
            'updated_count' => $updatedCount
        ]);
    }
}
