<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ReclaimsController extends Controller
{
    /**
     * Display a listing of the user's reclaims.
     */
    public function index(): View
    {
        $user = auth()->user();
        $reclaims = $user->reclaims()
            ->with(['bookListing.book'])
            ->latest('created_at')
            ->paginate(15);

        $totalReclaims = $user->reclaims()->count();

        return view('student.reclaims.index', compact('reclaims', 'totalReclaims'));
    }

    /**
     * Display a single reclaim detail.
     */
    public function show($reclaimId): View
    {
        $user = auth()->user();
        
        $reclaim = $user->reclaims()
            ->with(['bookListing.book'])
            ->findOrFail($reclaimId);

        return view('student.reclaims.show', compact('reclaim'));
    }
}
