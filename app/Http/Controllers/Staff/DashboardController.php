<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Acquisition;
use App\Models\BookDelivery;
use App\Models\BookListing;
use App\Models\BookSale;
use App\Models\Withdrawal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function index(): View
    {
        $pendingDeliveries = BookDelivery::where('status', 'pending')->count();
        $totalAcquisitions = BookListing::count();
        $availableBooks = BookListing::where('status', 'available')->count();
        $totalSales = BookSale::count();
        $totalWithdrawals = Withdrawal::count();

        return view('staff.dashboard', [
            'pendingDeliveries' => $pendingDeliveries,
            'totalAcquisitions' => $totalAcquisitions,
            'availableBooks' => $availableBooks,
            'totalSales' => $totalSales,
            'totalWithdrawals' => $totalWithdrawals,
        ]);
    }
}
