<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookDelivery;
use App\Models\BookListing;
use App\Models\BookSale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function index(): View
    {
        $pendingDeliveries = BookDelivery::where('status', 'pending')->count();
        $availableBooks = BookListing::where('status', 'available')->count();
        $totalSales = BookSale::count();

        return view('staff.dashboard', [
            'pendingDeliveries' => $pendingDeliveries,
            'availableBooks' => $availableBooks,
            'totalSales' => $totalSales,
        ]);
    }
}
