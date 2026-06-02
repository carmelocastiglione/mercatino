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
        $listings = BookListing::with('book', 'seller')
            ->where('status', 'available')
            ->bySchool(auth()->user()->school_id)
            ->latest()
            ->paginate(15);

        $totalAvailableBooks = BookListing::where('status', 'available')
            ->bySchool(auth()->user()->school_id)
            ->count();

        $totalAcquisitionAmount = BookListing::where('status', 'available')
            ->bySchool(auth()->user()->school_id)
            ->sum('price');

        $totalSalesAmount = BookListing::where('status', 'available')
            ->bySchool(auth()->user()->school_id)
            ->sum('price_sell');

        return view('staff.book-listings.index', [
            'listings' => $listings,
            'totalAvailableBooks' => $totalAvailableBooks,
            'totalAcquisitionAmount' => $totalAcquisitionAmount,
            'totalSalesAmount' => $totalSalesAmount,
        ]);
    }
}
