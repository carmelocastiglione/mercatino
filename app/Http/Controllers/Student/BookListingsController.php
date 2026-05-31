<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BookListingsController extends Controller
{
    /**
     * Display the list of book listings for the authenticated student.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $listings = $user->bookListings()
            ->with('book')
            ->latest('updated_at')
            ->paginate(15);

        $statusStats = [
            'available' => $user->bookListings()->where('status', 'available')->count(),
            'reserved' => $user->bookListings()->where('status', 'reserved')->count(),
            'sold' => $user->bookListings()->where('status', 'sold')->count(),
        ];

        return view('student.book-listings.index', [
            'listings' => $listings,
            'statusStats' => $statusStats,
        ]);
    }
}
