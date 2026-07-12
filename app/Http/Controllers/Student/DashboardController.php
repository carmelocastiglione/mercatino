<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $totalDeliveries = $user->bookDeliveries()->count();

        $totalSales = $user->getTotalSalesAmount();
        $totalSalesCount = $user->bookListings()->whereIn('status', ['sold', 'withdrawn'])->count();
        $totalWithdrawn = $user->getTotalWithdrawnAmount();
        $totalPurchases = $user->purchases()->count();
        $totalReclaims = $user->withdrawals()->count();
        $totalReservations = $user->bookReservationBatches()->count();
        $totalBookListings = $user->bookListings()->count();

        return view('student.dashboard', [
            'totalDeliveries' => $totalDeliveries,
            'totalSales' => $totalSales,
            'totalSalesCount' => $totalSalesCount,
            'totalWithdrawn' => $totalWithdrawn,
            'totalPurchases' => $totalPurchases,
            'totalReclaims' => $totalReclaims,
            'totalReservations' => $totalReservations,
            'totalBookListings' => $totalBookListings,
        ]);
    }
}
