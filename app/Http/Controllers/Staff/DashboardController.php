<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookDelivery;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function index(): View
    {
        $pendingDeliveries = BookDelivery::where('status', 'pending')->count();
        $approvedDeliveries = BookDelivery::where('status', 'approved')->count();
        $rejectedDeliveries = BookDelivery::where('status', 'rejected')->count();
        $totalProcessed = $approvedDeliveries + $rejectedDeliveries;

        return view('staff.dashboard', [
            'pendingDeliveries' => $pendingDeliveries,
            'approvedDeliveries' => $approvedDeliveries,
            'rejectedDeliveries' => $rejectedDeliveries,
            'totalProcessed' => $totalProcessed,
        ]);
    }
}
