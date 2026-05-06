<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookListing;
use Illuminate\View\View;

class BookListingController extends Controller
{
    /**
     * Display a list of available book listings for staff.
     */
    public function index(): View
    {
        $listings = BookListing::with('book', 'seller')
            ->where('status', 'available')
            ->latest()
            ->paginate(15);

        return view('staff.book-listings.index', [
            'listings' => $listings,
        ]);
    }
}
