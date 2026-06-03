<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BookDeliveryBatch;
use App\Models\BookReservationBatch;
use App\Models\Acquisition;
use App\Models\BookSale;
use App\Models\BookSaleBatch;
use App\Models\Withdrawal;
use App\Models\WithdrawalBatch;
use App\Models\PickupBatch;
use App\Models\Reclaim;
use App\Models\BookListing;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UserHistoryController extends Controller
{
    /**
     * Display user history search page.
     */
    public function index(): View
    {
        $user = null;
        $movements = [];

        return view('staff.user-history.index', compact('user', 'movements'));
    }

    /**
     * Search for users by name, surname, email, or code.
     */
    public function search(): JsonResponse
    {
        $query = request('q', '');
        $staffSchoolId = auth()->user()->school_id;

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('school_id', $staffSchoolId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'ilike', "%{$query}%")
                    ->orWhere('surname', 'ilike', "%{$query}%")
                    ->orWhere('code', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%");
            })
            ->select('id', 'name', 'surname', 'code', 'email')
            ->limit(15)
            ->get();

        return response()->json($users);
    }

    /**
     * Show user history timeline.
     */
    public function show(User $user): View
    {
        // Authorization check
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403, 'Non autorizzato');
        }

        $movements = $this->getUserMovements($user);

        return view('staff.user-history.index', compact('user', 'movements'));
    }

    /**
     * Get all movements for a user.
     */
    private function getUserMovements(User $user): array
    {
        $movements = [];

        // 0. Book Reservation Batches (Acquisti prenotati)
        $reservationBatches = BookReservationBatch::where('user_id', $user->id)
            ->with('bookReservations.bookListing.book')
            ->get();

        foreach ($reservationBatches as $batch) {
            $movements[] = [
                'type' => 'reservation_batch',
                'date' => $batch->created_at,
                'data' => $batch,
                'title' => 'Acquisto prenotato',
                'description' => $batch->total_items . ' libro/i',
                'icon' => '📋',
            ];
        }

        // 1. Delivery Batches (Prenotazioni di consegna)
        $deliveryBatches = BookDeliveryBatch::where('user_id', $user->id)
            ->with('deliveries.book')
            ->get();

        foreach ($deliveryBatches as $batch) {
            $movements[] = [
                'type' => 'delivery_batch',
                'date' => $batch->created_at,
                'data' => $batch,
                'title' => 'Consegna prenotata',
                'description' => $batch->deliveries->count() . ' libro/i',
                'icon' => '📦',
            ];
        }

        // 2. Acquisitions (Consegne acquisite)
        $acquisitions = Acquisition::where('seller_id', $user->id)
            ->with('bookListings.book')
            ->get();

        foreach ($acquisitions as $acq) {
            $movements[] = [
                'type' => 'acquisition',
                'date' => $acq->created_at,
                'data' => $acq,
                'title' => 'Consegna acquisita',
                'description' => $acq->bookListings->count() . ' libro/i - €' . number_format($acq->total_price ?? 0, 2),
                'icon' => '📥',
            ];
        }

        // 3. Book Sales Batches as Buyer (Batch di libri acquistati)
        $saleBatchesAsBuyer = BookSaleBatch::where('buyer_id', $user->id)
            ->with('sales.bookListing.book')
            ->get();

        foreach ($saleBatchesAsBuyer as $batch) {
            $movements[] = [
                'type' => 'purchase_batch',
                'date' => $batch->created_at,
                'data' => $batch,
                'title' => 'Libri acquistati',
                'description' => $batch->sales->count() . ' libro/i - €' . number_format($batch->total_price ?? 0, 2),
                'icon' => '🛒',
            ];
        }

        // 4. Book Sales as Seller (Libri venduti)
        $salesAsSeller = BookSale::whereHas('bookListing', function ($q) use ($user) {
            $q->where('seller_id', $user->id);
        })
            ->with('bookListing.book', 'batch')
            ->get();

        foreach ($salesAsSeller as $sale) {
            $movements[] = [
                'type' => 'sale',
                'date' => $sale->created_at,
                'data' => $sale,
                'title' => 'Libro venduto',
                'description' => $sale->bookListing->book->title ?? 'N/A',
                'icon' => '💰',
            ];
        }

        // 5. Withdrawal Batches (Riscossioni in batch)
        $withdrawalBatches = WithdrawalBatch::where('user_id', $user->id)
            ->with('withdrawals.bookListing.book')
            ->get();

        foreach ($withdrawalBatches as $batch) {
            $movements[] = [
                'type' => 'withdrawal_batch',
                'date' => $batch->created_at,
                'data' => $batch,
                'title' => 'Riscossione',
                'description' => $batch->withdrawals->count() . ' prelievo/i - €' . number_format($batch->total_amount ?? 0, 2),
                'icon' => '💸',
            ];
        }

        // 6. Pickup Batches (Libri ritirati)
        $pickupBatches = PickupBatch::where('user_id', $user->id)
            ->with('pickups.bookListing.book')
            ->get();

        foreach ($pickupBatches as $batch) {
            $movements[] = [
                'type' => 'pickup_batch',
                'date' => $batch->created_at,
                'data' => $batch,
                'title' => 'Libri ritirati',
                'description' => $batch->pickups->count() . ' libro/i',
                'icon' => '📚',
            ];
        }

        // 7. Reclaims as Buyer (Libri che l'utente ha reso)
        $reclamsByBuyer = Reclaim::where('buyer_id', $user->id)
            ->with('bookListing.book', 'user')
            ->get();

        foreach ($reclamsByBuyer as $reclaim) {
            $movements[] = [
                'type' => 'reclaim_by_buyer',
                'date' => $reclaim->created_at,
                'data' => $reclaim,
                'title' => 'Libro reso',
                'description' => $reclaim->bookListing->book->title ?? 'N/A',
                'icon' => '↩️',
            ];
        }

        // 8. Reclaims as Seller (Libri dell'utente che sono stati resi)
        $reclaimsBySeller = Reclaim::where('user_id', $user->id)
            ->with('bookListing.book', 'buyer')
            ->get();

        foreach ($reclaimsBySeller as $reclaim) {
            $movements[] = [
                'type' => 'reclaim_by_seller',
                'date' => $reclaim->created_at,
                'data' => $reclaim,
                'title' => 'Libro del tuo stock reso',
                'description' => $reclaim->bookListing->book->title ?? 'N/A',
                'icon' => '↩️',
            ];
        }

        // Sort by date descending (newest first)
        usort($movements, function ($a, $b) {
            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        return $movements;
    }
}
