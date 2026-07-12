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
            ->whereIn('status', ['sold', 'withdrawn'])
            ->with(['book', 'bookSales.buyer'])
            ->latest('updated_at')
            ->paginate(15);

        $totalSales = $user->bookListings()->whereIn('status', ['sold', 'withdrawn'])->count();
        $totalEarnings = $user->getTotalSalesAmount();

        return view('student.sales.index', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'totalEarnings' => $totalEarnings,
        ]);
    }
}
