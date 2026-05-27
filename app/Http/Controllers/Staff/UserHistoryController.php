<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BookDeliveryBatch;
use App\Models\Acquisition;
use App\Models\BookSale;
use App\Models\Withdrawal;
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
        return view('staff.user-history.index');
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

        // Collect all movements
        $movements = [];

        // 1. Delivery Batches (Prenotazioni di consegna)
        $deliveryBatches = BookDeliveryBatch::where('user_id', $user->id)
            ->with('deliveries')
            ->get();

        foreach ($deliveryBatches as $batch) {
            $movements[] = [
                'type' => 'delivery_batch',
                'date' => $batch->created_at,
                'data' => $batch,
                'title' => 'Consegna prenotata in batch',
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

        // 3. Book Sales as Buyer (Libri acquistati)
        $salesAsBuyer = BookSale::where('buyer_id', $user->id)
            ->with('bookListing.book')
            ->get();

        foreach ($salesAsBuyer as $sale) {
            $movements[] = [
                'type' => 'purchase',
                'date' => $sale->created_at,
                'data' => $sale,
                'title' => 'Libro acquistato',
                'description' => $sale->bookListing->book->title ?? 'N/A',
                'icon' => '🛒',
            ];
        }

        // 4. Book Sales as Seller (Libri venduti)
        $salesAsSeller = BookSale::whereHas('bookListing', function ($q) use ($user) {
            $q->where('seller_id', $user->id);
        })
            ->with('bookListing.book')
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

        // 5. Withdrawals (Riscossioni)
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->get();

        foreach ($withdrawals as $withdrawal) {
            $movements[] = [
                'type' => 'withdrawal',
                'date' => $withdrawal->created_at,
                'data' => $withdrawal,
                'title' => 'Riscossione',
                'description' => '€' . number_format($withdrawal->amount, 2),
                'icon' => '💸',
            ];
        }

        // Sort by date descending (newest first)
        usort($movements, function ($a, $b) {
            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        return view('staff.user-history.show', [
            'user' => $user,
            'movements' => $movements,
        ]);
    }
}
