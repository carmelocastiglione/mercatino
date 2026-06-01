<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Acquisition;
use App\Models\BookDelivery;
use App\Models\BookDeliveryBatch;
use App\Models\BookListing;
use App\Models\BookReservationBatch;
use App\Models\BookSale;
use App\Models\Reclaim;
use App\Models\Withdrawal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard (filtered by staff's school).
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;

        return view('staff.dashboard', [
            'totalBooks' => Book::bySchool($schoolId)->count(),
            'pendingDeliveries' => BookDelivery::where('status', 'pending')->bySchool($schoolId)->count(),
            'pendingDeliveryBatches' => BookDeliveryBatch::where('status', 'pending')->bySchool($schoolId)->count(),
            'totalAcquisitions' => BookListing::bySchool($schoolId)->count(),
            'availableBooks' => BookListing::where('status', 'available')->bySchool($schoolId)->count(),
            'totalSales' => BookSale::bySchool($schoolId)->count(),
            'totalWithdrawals' => Withdrawal::bySchool($schoolId)->count(),
            'pendingReclaims' => Reclaim::where('status', 'pending')->bySchool($schoolId)->count(),
            'pendingReservations' => BookReservationBatch::where('status', 'pending')->bySchool($schoolId)->count(),
        ]);
    }
}
