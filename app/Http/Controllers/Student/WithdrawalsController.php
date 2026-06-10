<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class WithdrawalsController extends Controller
{
    /**
     * Display a listing of the user's withdrawals.
     */
    public function index(): View
    {
        $user = auth()->user();
        $withdrawals = $user->withdrawals()
            ->with(['bookListing.book'])
            ->latest('created_at')
            ->paginate(15);

        $totalToWithdraw = $user->getAvailableBalance();
        $totalWithdrawn = $user->getTotalWithdrawnAmount();

        return view('student.withdrawals.index', compact('withdrawals', 'totalToWithdraw', 'totalWithdrawn'));
    }
}
