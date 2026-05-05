<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookSale;
use App\Models\BookListing;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
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
        return view('staff.sales.create');
    }

    /**
     * Search for buyers by name, surname, or code.
     */
    public function searchBuyers(): JsonResponse
    {
        $query = request('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $buyers = User::where(function ($q) use ($query) {
            $q->whereRaw("CONCAT(name, ' ', surname) ILIKE ?", ["%$query%"])
                ->orWhere('code', 'ILIKE', "%$query%")
                ->orWhere('email', 'ILIKE', "%$query%");
        })
            ->where('role', 'student')
            ->limit(10)
            ->get(['id', 'name', 'surname', 'code', 'email'])
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'code' => $user->code,
                'email' => $user->email,
                'display' => "{$user->name} {$user->surname} ({$user->code})",
            ]);

        return response()->json($buyers);
    }

    /**
     * Search for available book listings.
     */
    public function searchListings(): JsonResponse
    {
        $query = request('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $listings = BookListing::with('book')
            ->where('status', 'available')
            ->whereHas('book', function ($q) use ($query) {
                $q->where('title', 'ILIKE', "%$query%")
                    ->orWhere('author', 'ILIKE', "%$query%")
                    ->orWhere('isbn', 'ILIKE', "%$query%");
            })
            ->limit(10)
            ->get()
            ->map(fn($listing) => [
                'id' => $listing->id,
                'title' => $listing->book->title,
                'author' => $listing->book->author,
                'isbn' => $listing->book->isbn,
                'condition' => $listing->condition,
                'price' => $listing->price,
                'display' => "{$listing->book->title} - {$listing->book->author}",
            ]);

        return response()->json($listings);
    }

    /**
     * Store batch sales.
     */
    public function storeBatch(): JsonResponse
    {
        try {
            $validated = request()->validate([
                'sales' => ['required', 'array', 'min:1'],
                'sales.*.buyer_id' => ['required', 'exists:users,id'],
                'sales.*.book_listing_id' => ['required', 'exists:book_listings,id'],
                'sales.*.payment_method' => ['required', 'in:cash,card,bank_transfer,satispay,paypal'],
            ]);

            $salesToCreate = [];
            $totalAmount = 0;
            $firstSaleId = null;

            foreach ($validated['sales'] as $sale) {
                // Verify book listing exists and is available
                $listing = BookListing::findOrFail($sale['book_listing_id']);
                
                if ($listing->status !== 'available') {
                    continue;
                }

                $bookSale = BookSale::create([
                    'book_listing_id' => $listing->id,
                    'sold_by' => auth()->id(),
                    'buyer_id' => $sale['buyer_id'],
                    'payment_method' => $sale['payment_method'],
                ]);

                if (!$firstSaleId) {
                    $firstSaleId = $bookSale->id;
                }

                // Update listing status
                $listing->update(['status' => 'sold']);

                $totalAmount += $listing->price;
            }

            return response()->json([
                'success' => true,
                'message' => count($validated['sales']) . ' vendita/e registrata/e con successo',
                'count' => count($validated['sales']),
                'total' => $totalAmount,
                'sale_id' => $firstSaleId,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show sale summary/receipt.
     */
    public function show($saleId): View
    {
        $sale = BookSale::with('bookListing.book', 'soldBy', 'buyer')
            ->findOrFail($saleId);
        
        return view('staff.sales.show', [
            'sale' => $sale,
        ]);
    }

    /**
     * Store a newly created sale in storage (legacy).
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
