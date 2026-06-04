<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookListing;
use Illuminate\View\View;

class BookListingController extends Controller
{
    /**
     * Display a list of available book listings for staff (filtered by staff's school).
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;
        $query = request()->input('q', '');
        
        $listings = BookListing::with('book', 'seller')
            ->where('status', 'available')
            ->bySchool($schoolId)
            ->when($query, function($q) use($query) {
                return $q->whereHas('book', function($bookQuery) use($query) {
                    $bookQuery->where(function($subQuery) use($query) {
                        $subQuery->where('title', 'ilike', "%{$query}%")
                            ->orWhere('author', 'ilike', "%{$query}%")
                            ->orWhere('isbn', 'ilike', "%{$query}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalAvailableBooks = BookListing::where('status', 'available')
            ->bySchool($schoolId)
            ->count();

        $totalAcquisitionAmount = BookListing::where('status', 'available')
            ->bySchool($schoolId)
            ->sum('price');

        $totalSalesAmount = BookListing::where('status', 'available')
            ->bySchool($schoolId)
            ->sum('price_sell');

        return view('staff.book-listings.index', [
            'listings' => $listings,
            'totalAvailableBooks' => $totalAvailableBooks,
            'totalAcquisitionAmount' => $totalAcquisitionAmount,
            'totalSalesAmount' => $totalSalesAmount,
            'filterQuery' => $query,
        ]);
    }
}
