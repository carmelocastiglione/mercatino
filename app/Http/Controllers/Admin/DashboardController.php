<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\School;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalSchools' => School::count(),
            'totalUsers' => User::count(),
            'totalBooks' => Book::count(),
        ]);
    }
}
