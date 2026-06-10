<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Acquisition;
use App\Models\BookDelivery;
use App\Models\BookDeliveryBatch;
use App\Models\BookListing;
use App\Models\BookReservationBatch;
use App\Models\BookSale;
use App\Models\Reclaim;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard (filtered by staff's school).
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;
        $school = auth()->user()->school;

        // Calculate book status distribution
        $totalCatalogBooks = Book::bySchool($schoolId)->count();
        $availableBooks = BookListing::where('status', 'available')->bySchool($schoolId)->count();
        $reservedBooks = BookListing::where('status', 'reserved')->bySchool($schoolId)->count();
        $soldBooks = BookListing::where('status', 'sold')->bySchool($schoolId)->count();
        $withdrawnBooks = BookListing::where('status', 'withdrawn')->bySchool($schoolId)->count();
        $reclaimedBooks = BookListing::where('status', 'reclaim')->bySchool($schoolId)->count();
        $archivedBooks = BookListing::where('status', 'archived')->bySchool($schoolId)->count();

        // Activity metrics
        $totalSales = BookSale::bySchool($schoolId)->count();
        $totalWithdrawalsCount = Withdrawal::bySchool($schoolId)->count();
        $pendingReclaims = Reclaim::where('status', 'pending')->bySchool($schoolId)->count();
        $pendingDeliveryBatches = BookDeliveryBatch::where('status', 'pending')->bySchool($schoolId)->count();

        // Financial metrics - Totale Ricavi Vendite (sum of price_sell for sold books, excluding reclaimed)
        $totalEarned = BookListing::join('book_sales', 'book_listings.id', '=', 'book_sales.book_listing_id')
            ->join('books', 'book_listings.book_id', '=', 'books.id')
            ->where('books.school_id', $schoolId)
            ->whereNull('book_sales.reclaim_id')  // Escludere i libri resi
            ->sum('book_listings.price_sell');

        // Financial metrics - Totale Riscosso (sum of amounts from withdrawals)
        $totalWithdrawn = Withdrawal::whereHas('user', function ($queryBuilder) use ($schoolId) {
                $queryBuilder->where('school_id', $schoolId);
            })
            ->sum('amount');

        // Financial metrics - Totale Da Riscuotere (sum of price for sold books, excluding reclaimed)
        // Questo è il totale fisso che devono avere gli studenti
        $totalToCollect = BookListing::join('book_sales', 'book_listings.id', '=', 'book_sales.book_listing_id')
            ->join('books', 'book_listings.book_id', '=', 'books.id')
            ->where('books.school_id', $schoolId)
            ->whereNull('book_sales.reclaim_id')  // Escludere i libri resi
            ->sum('book_listings.price');

        // Financial metrics - Ancora da Riscuotere (differenza tra totale da riscuotere e riscosso)
        $stillToCollect = $totalToCollect - $totalWithdrawn;

        // Financial metrics - Guadagno (differenza tra totale ricavi vendite e totale da riscuotere)
        $gain = $totalEarned - $totalToCollect;

        return view('staff.dashboard', [
            'totalBooks' => Book::bySchool($schoolId)->count(),
            'pendingDeliveries' => BookDelivery::where('status', 'pending')->bySchool($schoolId)->count(),
            'pendingDeliveryBatches' => $pendingDeliveryBatches,
            'totalAcquisitions' => BookListing::bySchool($schoolId)->count(),
            'availableBooks' => $availableBooks,
            'totalSales' => $totalSales,
            'totalWithdrawals' => $totalWithdrawalsCount,
            'pendingReclaims' => $pendingReclaims,
            'pendingReservations' => BookReservationBatch::where('status', 'pending')->bySchool($schoolId)->count(),
            'enableOnlineSales' => $school->hasFeatureEnabled('enable_online_sales'),
            'totalStudents' => User::where('school_id', $schoolId)->where('role', 'studente')->count(),
            'totalStaff' => User::where('school_id', $schoolId)->where('role', 'staff')->count(),
            // Chart data - Financial metrics (stacked bar showing: riscosso + ancora da riscuotere + guadagno = totale ricavi)
            'financialData' => [
                'withdrawn' => $totalWithdrawn,        // Riscosso
                'stillToCollect' => $stillToCollect,   // Ancora da riscuotere
                'gain' => $gain,                       // Guadagno
                'totalEarned' => $totalEarned,         // Totale Ricavi Vendite (somma delle tre parti sopra)
                'totalToCollect' => $totalToCollect,   // Totale Da Riscuotere (fisso)
            ],
            'activityData' => [
                'sales' => $totalSales,
                'withdrawals' => $totalWithdrawalsCount,
                'reclaims' => $pendingReclaims,
                'deliveries' => $pendingDeliveryBatches,
            ],
            'bookStatusData' => [
                'available' => $availableBooks,
                'reserved' => $reservedBooks,
                'sold' => $soldBooks,
                'withdrawn' => $withdrawnBooks,
                'reclaim' => $reclaimedBooks,
                'archived' => $archivedBooks,
            ],
        ]);
    }
}
