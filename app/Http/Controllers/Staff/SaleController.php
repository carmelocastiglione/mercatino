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
                $q->where('name', 'ilike', "%{$query}%")
                    ->orWhere('surname', 'ilike', "%{$query}%")
                    ->orWhere('code', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%");
            })
            ->select('id', 'name', 'surname', 'code', 'email')
            ->limit(10)
            ->get();

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

        $listings = BookListing::join('books', 'book_listings.book_id', '=', 'books.id')
            ->join('users', 'book_listings.seller_id', '=', 'users.id')
            ->where('book_listings.status', '=', 'available')
            ->where(function ($q) use ($query) {
                $q->where('books.title', 'ilike', "%{$query}%")
                    ->orWhere('books.author', 'ilike', "%{$query}%")
                    ->orWhere('books.isbn', 'ilike', "%{$query}%");
            })
            ->select('book_listings.*', 'books.title', 'books.author', 'books.isbn', 'users.name as seller_name', 'users.surname as seller_surname', 'users.code as seller_code')
            ->limit(10)
            ->get()
            ->map(fn($listing) => [
                'id' => $listing->id,
                'title' => $listing->title,
                'author' => $listing->author,
                'isbn' => $listing->isbn,
                'condition' => $listing->condition,
                'price' => $listing->price,
                'seller_name' => $listing->seller_name,
                'seller_surname' => $listing->seller_surname,
                'seller_code' => $listing->seller_code,
                'display' => "{$listing->title} - {$listing->author}",
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
            ]);

            $totalAmount = 0;
            $count = 0;
            $saleIds = [];

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
                ]);

                $saleIds[] = $bookSale->id;

                // Update listing status
                $listing->update(['status' => 'sold']);

                $totalAmount += $listing->price;
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => $count . ' vendita/e registrata/e con successo',
                'count' => $count,
                'total' => $totalAmount,
                'redirect' => route('staff.sales.batch-summary', ['ids' => implode(',', $saleIds)]),
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
     * Show batch sales summary.
     */
    public function batchSummary(): View
    {
        $ids = request()->query('ids', '');
        $saleIds = array_filter(explode(',', $ids));

        if (empty($saleIds)) {
            return redirect()->route('staff.sales.create');
        }

        $sales = BookSale::with('bookListing.book', 'soldBy', 'buyer')
            ->whereIn('id', $saleIds)
            ->get();

        return view('staff.sales.show', [
            'sales' => $sales,
            'isBatch' => true,
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
