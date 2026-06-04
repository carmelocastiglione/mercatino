<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookSale;
use App\Models\BookSaleBatch;
use App\Models\BookListing;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SaleController extends Controller
{
    /**
     * Display the list of sale batches (filtered by staff's school).
     */
    public function index(): View
    {
        $query = request()->input('q', '');

        // Totals without filters
        $totalBatchesCount = BookSaleBatch::bySchool(auth()->user()->school_id)->count();
        
        $totalRevenue = BookSaleBatch::bySchool(auth()->user()->school_id)
            ->sum('total_price');

        // Filtered batches for table
        $batches = BookSaleBatch::with(['creator', 'buyer', 'sales'])
            ->bySchool(auth()->user()->school_id)
            ->when($query, function ($q) use ($query) {
                return $q->whereHas('buyer', function ($buyerQuery) use ($query) {
                    $buyerQuery->where('name', 'ilike', "%{$query}%")
                        ->orWhere('surname', 'ilike', "%{$query}%")
                        ->orWhere('email', 'ilike', "%{$query}%")
                        ->orWhere('code', 'ilike', "%{$query}%");
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('staff.sales.index', [
            'batches' => $batches,
            'totalBatchesCount' => $totalBatchesCount,
            'totalRevenue' => $totalRevenue,
            'filterQuery' => $query,
        ]);
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create(): View
    {
        // Check if there are approved reservations from session
        $approvedReservations = session('approved_reservations', []);
        $studentId = session('student_id', null);

        return view('staff.sales.create', [
            'approvedReservations' => $approvedReservations,
            'studentId' => $studentId,
            'enableOnlineSales' => auth()->user()->school->hasFeatureEnabled('enable_online_sales'),
        ]);
    }

    /**
     * Search for buyers by name, surname, code, email, or ID (filtered by staff's school).
     */
    public function searchBuyers(): JsonResponse
    {
        $query = request('q', '');
        $staffSchoolId = auth()->user()->school_id;

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        // Check if query is a numeric ID
        if (is_numeric($query)) {
            $buyer = User::where('school_id', $staffSchoolId)
                ->where('id', intval($query))
                ->select('id', 'name', 'surname', 'code', 'email')
                ->first();

            if ($buyer) {
                return response()->json([$buyer]);
            }
        }

        // Search by text fields if query is at least 2 characters
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
     * Search for available book listings with separate filters (filtered by staff's school).
     */
    public function searchListings(): JsonResponse
    {
        $titleQuery = request('title', '');
        $sellerCodeQuery = request('seller_code', '');
        $staffSchoolId = auth()->user()->school_id;

        // If both are empty, return empty
        if (strlen($titleQuery) < 1 && strlen($sellerCodeQuery) < 1) {
            return response()->json([]);
        }

        $listings = BookListing::join('books', 'book_listings.book_id', '=', 'books.id')
            ->join('users', 'book_listings.seller_id', '=', 'users.id')
            ->where('book_listings.status', '=', 'available')
            ->where('books.school_id', $staffSchoolId);

        // Apply title filter if provided (searches title, author, isbn)
        if (strlen($titleQuery) >= 1) {
            $listings->where(function ($q) use ($titleQuery) {
                $q->where('books.title', 'ilike', "%{$titleQuery}%")
                    ->orWhere('books.author', 'ilike', "%{$titleQuery}%")
                    ->orWhere('books.isbn', 'ilike', "%{$titleQuery}%");
            });
        }

        // Apply seller code filter if provided
        if (strlen($sellerCodeQuery) >= 1) {
            $listings->where('users.code', 'ilike', "%{$sellerCodeQuery}%");
        }

        $results = $listings
            ->select('book_listings.*', 'books.title', 'books.author', 'books.isbn', 'users.name as seller_name', 'users.surname as seller_surname', 'users.code as seller_code')
            ->limit(50)
            ->get()
            ->map(fn($listing) => [
                'id' => $listing->id,
                'title' => $listing->title,
                'author' => $listing->author,
                'isbn' => $listing->isbn,
                'condition' => $listing->condition,
                'price' => $listing->price_sell ?? $listing->price,
                'seller_name' => $listing->seller_name,
                'seller_surname' => $listing->seller_surname,
                'seller_code' => $listing->seller_code,
            ]);

        return response()->json($results);
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

            // Get buyer_id from first sale (all sales should have same buyer)
            $buyerId = $validated['sales'][0]['buyer_id'];

            // Create the batch first
            $batch = BookSaleBatch::create([
                'school_id' => $staffSchoolId,
                'created_by' => auth()->id(),
                'buyer_id' => $buyerId,
                'total_price' => 0, // Will be updated after calculating total
            ]);

            $totalAmount = 0;
            $count = 0;

            foreach ($validated['sales'] as $index => $sale) {
                // Verify book listing exists and is available
                $listing = BookListing::findOrFail($sale['book_listing_id']);

                // Accept both 'available' and 'reserved' statuses
                if (!in_array($listing->status, ['available', 'reserved'])) {
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
                    'book_sale_batch_id' => $batch->id,
                ]);

                // Update listing status
                $listing->update(['status' => 'sold']);

                $totalAmount += $listing->price_sell ?? $listing->price;
                $count++;
            }

            // Update batch with total price
            $batch->update(['total_price' => $totalAmount]);

            // Clear session data after processing
            session()->forget(['approved_reservations', 'student_id']);

            $redirectUrl = route('staff.sales.show', ['batch' => $batch->id]);

            return response()->json([
                'success' => true,
                'message' => $count . ' vendita/e registrata/e con successo',
                'count' => $count,
                'total' => $totalAmount,
                'redirect' => $redirectUrl,
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
     * Show batch sales summary (authorized by school).
     */
    public function show($batchId): View|RedirectResponse
    {
        $batch = BookSaleBatch::with(['sales.bookListing.book', 'sales.buyer', 'creator', 'buyer'])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($batchId);

        return view('staff.sales.show', [
            'batch' => $batch,
            'sales' => $batch->sales,
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
