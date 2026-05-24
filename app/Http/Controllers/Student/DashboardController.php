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
        $totalSalesCount = $user->bookListings()->where('status', 'sold')->count();
        $totalWithdrawn = $user->getTotalWithdrawnAmount();

        return view('student.dashboard', [
            'totalDeliveries' => $totalDeliveries,
            'totalSales' => $totalSales,
            'totalSalesCount' => $totalSalesCount,
            'totalWithdrawn' => $totalWithdrawn,
        ]);
    }
}
