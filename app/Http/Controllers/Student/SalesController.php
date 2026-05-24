<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SalesController extends Controller
{
    /**
     * Display the list of sales for the authenticated student.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $sales = $user->bookListings()
            ->where('status', 'sold')
            ->with(['book', 'bookSales.buyer'])
            ->latest('updated_at')
            ->paginate(15);

        $totalSales = $user->bookListings()->where('status', 'sold')->count();
        $totalEarnings = $user->getTotalSalesAmount();

        return view('student.sales.index', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'totalEarnings' => $totalEarnings,
        ]);
    }

    /**
     * Display a single sale (show details).
     */
    public function show($listingId): View
    {
        $user = auth()->user();
        
        $listing = $user->bookListings()
            ->where('status', 'sold')
            ->with(['book', 'bookSales.buyer'])
            ->findOrFail($listingId);

        return view('student.sales.show', [
            'listing' => $listing,
        ]);
    }
}
