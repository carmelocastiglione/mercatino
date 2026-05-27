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
     * Display the list of sold books (filtered by staff's school).
     */
    public function index(): View
    {
        $sales = BookSale::with('bookListing.book', 'soldBy')
            ->bySchool(auth()->user()->school_id)
            ->latest('created_at')
            ->paginate(15);

        $totalSalesCount = BookSale::bySchool(auth()->user()->school_id)->count();
        
        $totalRevenue = BookSale::bySchool(auth()->user()->school_id)
            ->join('book_listings', 'book_sales.book_listing_id', '=', 'book_listings.id')
            ->sum('book_listings.price');

        return view('staff.sales.index', [
            'sales' => $sales,
            'totalSalesCount' => $totalSalesCount,
            'totalRevenue' => $totalRevenue,
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
     * Search for buyers by name, surname, or code (filtered by staff's school).
     */
    public function searchBuyers(): JsonResponse
    {
        $query = request('q', '');
        $staffSchoolId = auth()->user()->school_id;

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $buyers = User::where('school_id', $staffSchoolId)
            ->where(function ($q) use ($query) {
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
     * Search for available book listings (filtered by staff's school).
     */
    public function searchListings(): JsonResponse
    {
        $query = request('q', '');
        $staffSchoolId = auth()->user()->school_id;

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $listings = BookListing::join('books', 'book_listings.book_id', '=', 'books.id')
            ->join('users', 'book_listings.seller_id', '=', 'users.id')
            ->where('book_listings.status', '=', 'available')
            ->where('books.school_id', $staffSchoolId)
            ->where(function ($q) use ($query) {
                $q->where('books.title', 'ilike', "%{$query}%")
                    ->orWhere('books.author', 'ilike', "%{$query}%")
                    ->orWhere('books.isbn', 'ilike', "%{$query}%")
                    ->orWhere('users.name', 'ilike', "%{$query}%")
                    ->orWhere('users.surname', 'ilike', "%{$query}%")
                    ->orWhere('users.code', 'ilike', "%{$query}%");
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
     * Store batch sales (authorized by school).
     */
    public function storeBatch(): JsonResponse
    {
        try {
            $staffSchoolId = auth()->user()->school_id;

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

                // Verify book belongs to staff's school
                if ($listing->book->school_id !== $staffSchoolId) {
                    return response()->json([
                        'message' => 'Il libro non appartiene alla tua scuola'
                    ], 403);
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
     * Show sale summary/receipt (authorized by school).
     */
    public function show($saleId): View
    {
        $staffSchoolId = auth()->user()->school_id;
        $sale = BookSale::with('bookListing.book', 'soldBy', 'buyer')
            ->findOrFail($saleId);
        
        if ($sale->bookListing->book->school_id !== $staffSchoolId) {
            abort(403, 'Non autorizzato');
        }

        return view('staff.sales.show', [
            'sale' => $sale,
        ]);
    }

    /**
     * Show batch sales summary (authorized by school).
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
            ->bySchool(auth()->user()->school_id)
            ->get();

        return view('staff.sales.show', [
            'sales' => $sales,
            'isBatch' => true,
        ]);
    }

    /**
     * Store a newly created sale in storage (legacy, authorized by school).
     */
    public function store(): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        $validated = request()->validate([
            'book_listing_id' => ['required', 'exists:book_listings,id'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,satispay,paypal'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Verifica che il libro appartiene alla scuola dello staff
        $listing = BookListing::findOrFail($validated['book_listing_id']);
        if ($listing->book->school_id !== $staffSchoolId) {
            return back()->with('error', 'Non autorizzato');
        }

        return redirect()->route('staff.sales.index')
            ->with('success', 'Vendita registrata con successo!');
    }
}
