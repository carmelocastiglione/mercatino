<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookSale;
use App\Models\BookListing;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SaleController extends Controller
{
    /**
     * Display the list of sold books.
     */
    public function index(): View
    {
        $sales = BookSale::with('bookListing.book', 'soldBy')
            ->latest('created_at')
            ->paginate(15);

        $totalSales = BookSale::join('book_listings', 'book_sales.book_listing_id', '=', 'book_listings.id')
            ->sum('book_listings.price');
        $todaySales = BookSale::whereDate('created_at', today())->count();

        return view('staff.sales.index', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'todaySales' => $todaySales,
        ]);
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create(): View
    {
        // Libri disponibili (status = available)
        $availableListings = BookListing::with('book')
            ->where('status', 'available')
            ->get();

        return view('staff.sales.create', [
            'availableListings' => $availableListings,
        ]);
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'book_listing_id' => ['required', 'exists:book_listings,id'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,satispay,paypal'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Crea la vendita
        BookSale::create([
            'book_listing_id' => $validated['book_listing_id'],
            'sold_by' => auth()->id(),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Marca il listing come sold
        BookListing::findOrFail($validated['book_listing_id'])
            ->update(['status' => 'sold']);

        return redirect()->route('staff.sales.index')
            ->with('success', 'Vendita registrata con successo!');
    }
}
