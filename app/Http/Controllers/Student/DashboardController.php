<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(): View
    {
        $pendingDeliveries = auth()->user()->bookDeliveries()
            ->where('status', 'pending')
            ->count();

        $approvedDeliveries = auth()->user()->bookDeliveries()
            ->where('status', 'approved')
            ->count();

        $rejectedDeliveries = auth()->user()->bookDeliveries()
            ->where('status', 'rejected')
            ->count();

        return view('student.dashboard', [
            'pendingDeliveries' => $pendingDeliveries,
            'approvedDeliveries' => $approvedDeliveries,
            'rejectedDeliveries' => $rejectedDeliveries,
        ]);
    }
}
