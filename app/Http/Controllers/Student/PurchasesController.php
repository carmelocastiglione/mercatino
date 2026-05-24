<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the user's purchases.
     */
    public function index(): View
    {
        $user = auth()->user();
        $purchases = $user->purchases()
            ->with(['bookListing.book', 'soldBy'])
            ->latest('created_at')
            ->paginate(15);

        $totalPurchases = $user->purchases()->count();
        $totalSpent = $purchases->sum(fn($p) => $p->bookListing->price);

        return view('student.purchases.index', compact('purchases', 'totalPurchases', 'totalSpent'));
    }

    /**
     * Display a single purchase detail.
     */
    public function show($purchaseId): View
    {
        $user = auth()->user();
        
        $purchase = $user->purchases()
            ->with(['bookListing.book', 'soldBy'])
            ->findOrFail($purchaseId);

        return view('student.purchases.show', compact('purchase'));
    }
}
