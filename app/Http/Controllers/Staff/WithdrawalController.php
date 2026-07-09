<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookListing;
use App\Models\Pickup;
use App\Models\PickupBatch;
use App\Models\Reclaim;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WithdrawalBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of all withdrawals (filtered by staff's school).
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;
        $query = request()->input('q', '');

        // Get all users that have book listings from staff's school
        // Using addSelect with subqueries to avoid N+1 queries
        $sellers = User::whereHas('bookListings.book', function ($queryBuilder) use ($schoolId) {
                $queryBuilder->bySchool($schoolId);
            })
            ->where('school_id', $schoolId)
            ->addSelect([
                'books_to_pickup' => BookListing::selectRaw('COUNT(*)')
                    ->whereColumn('seller_id', 'users.id')
                    ->bySchool($schoolId)
                    ->whereIn('status', ['available', 'reserved'])
                    ->where('leave', false)
            ])
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('surname', 'ilike', "%{$query}%")
                        ->orWhere('code', 'ilike', "%{$query}%")
                        ->orWhere('email', 'ilike', "%{$query}%")
                        ->orWhereHas('withdrawalBatches', function ($batchQuery) use ($query) {
                            $batchQuery->where('ean13', 'ilike', "%{$query}%");
                        })
                        ->orWhereHas('pickupBatches', function ($batchQuery) use ($query) {
                            $batchQuery->where('ean13', 'ilike', "%{$query}%");
                        });
                });
            })
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        // Add booksToPickup as an attribute (from subquery result)
        $sellers->getCollection()->transform(function($seller) {
            $seller->booksToPickup = $seller->books_to_pickup ?? 0;
            return $seller;
        });

        // Calculate total amounts - Totale Ricavi Vendite (sum of price_sell for sold books, excluding reclaimed)
        $totalEarned = BookListing::join('book_sales', 'book_listings.id', '=', 'book_sales.book_listing_id')
            ->join('books', 'book_listings.book_id', '=', 'books.id')
            ->where('books.school_id', $schoolId)
            ->whereNull('book_sales.reclaim_id')
            ->sum('book_listings.price_sell');

        // Calculate total amounts - Totale Riscosso (sum of amounts from withdrawals)
        $totalWithdrawn = Withdrawal::whereHas('user', function ($queryBuilder) use ($schoolId) {
                $queryBuilder->where('school_id', $schoolId);
            })
            ->sum('amount');

        // Calculate total amounts - Totale Da Riscuotere (sum of price for sold books, excluding reclaimed)
        $totalAvailable = BookListing::join('book_sales', 'book_listings.id', '=', 'book_sales.book_listing_id')
            ->join('books', 'book_listings.book_id', '=', 'books.id')
            ->where('books.school_id', $schoolId)
            ->whereNull('book_sales.reclaim_id')
            ->sum('book_listings.price');

        // Calculate progress: users who have withdrawn vs users who have sold books
        $usersWithSoldBooks = BookListing::join('book_sales', 'book_listings.id', '=', 'book_sales.book_listing_id')
            ->join('books', 'book_listings.book_id', '=', 'books.id')
            ->where('books.school_id', $schoolId)
            ->distinct('book_listings.seller_id')
            ->count('book_listings.seller_id');

        // Get aggregated data for users in SQL instead of PHP loop
        $usersData = \DB::table('users')
            ->leftJoin('book_listings', 'users.id', '=', 'book_listings.seller_id')
            ->leftJoin('books', 'book_listings.book_id', '=', 'books.id')
            ->leftJoin('book_sales', 'book_listings.id', '=', 'book_sales.book_listing_id')
            ->leftJoin('withdrawals', 'book_listings.id', '=', 'withdrawals.book_listing_id')
            ->where('users.school_id', $schoolId)
            ->where('books.school_id', $schoolId)
            ->groupBy('users.id')
            ->selectRaw('users.id,
                COUNT(DISTINCT CASE WHEN book_sales.id IS NOT NULL THEN book_sales.book_listing_id END) as sold_count,
                COUNT(DISTINCT CASE WHEN withdrawals.id IS NOT NULL THEN withdrawals.id END) as withdrawn_count,
                COUNT(DISTINCT CASE WHEN book_listings.status IN (\'available\', \'reserved\') AND book_listings.leave = false THEN book_listings.id END) as unsold_count,
                COUNT(DISTINCT CASE WHEN book_listings.status NOT IN (\'withdrawn\', \'reclaim\', \'archived\') THEN book_listings.id END) as incomplete_count,
                COUNT(DISTINCT book_listings.id) as total_books')
            ->get()
            ->keyBy('id');

        // Calculate statistics from aggregated data
        $usersCompleted = $usersData->filter(function($user) {
            return $user->total_books > 0 && $user->incomplete_count === 0;
        })->count();

        $usersWithPending = $usersData->filter(function($user) {
            $hasPendingWithdrawal = $user->sold_count > 0 && $user->sold_count > $user->withdrawn_count;
            $hasPendingPickup = $user->unsold_count > 0;
            return $hasPendingWithdrawal || $hasPendingPickup;
        })->count();

        $withdrawalProgress = $usersWithSoldBooks > 0 ? (($usersWithSoldBooks - $usersWithPending) / $usersWithSoldBooks) * 100 : 0;
        $overallProgress = ($usersCompleted + $usersWithPending) > 0 ? ($usersCompleted / ($usersCompleted + $usersWithPending)) * 100 : 0;

        // Count unsold books to pick up (available/reserved books with leave=false)
        $unsoldToPickup = BookListing::bySchool($schoolId)
            ->whereIn('status', ['available', 'reserved'])
            ->where('leave', false)
            ->count();

        return view('staff.withdrawals.index', [
            'sellers' => $sellers,
            'totalEarned' => $totalEarned,
            'totalWithdrawn' => $totalWithdrawn,
            'totalAvailable' => $totalAvailable,
            'usersWithSoldBooks' => $usersWithSoldBooks,
            'withdrawalProgress' => $withdrawalProgress,
            'usersCompleted' => $usersCompleted,
            'usersWithPending' => $usersWithPending,
            'overallProgress' => $overallProgress,
            'unsoldToPickup' => $unsoldToPickup,
            'filterQuery' => $query,
        ]);
    }

    /**
     * Show the form for managing withdrawals by seller.
     */
    public function create(): View
    {
        return view('staff.withdrawals.create');
    }

    /**
     * Search sellers for withdrawal management (filtered by staff's school).
     */
    public function searchSellers(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        $schoolId = auth()->user()->school_id;
        
        // Get sellers (users with book listings from staff's school)
        $sellers = User::where('school_id', $schoolId)
            ->whereHas('bookListings.book', function ($q) use ($schoolId) {
                $q->bySchool($schoolId);
            })
            ->where(function ($q) use ($query) {
                $q->where('surname', 'ilike', "%$query%")
                    ->orWhere('code', 'ilike', "%$query%")
                    ->orWhere('email', 'ilike', "%$query%")
                    ->orWhereHas('withdrawalBatches', function ($batchQuery) use ($query) {
                        $batchQuery->where('ean13', 'ilike', "%$query%");
                    })
                    ->orWhereHas('pickupBatches', function ($batchQuery) use ($query) {
                        $batchQuery->where('ean13', 'ilike', "%$query%");
                    });
            })
            ->take(10)
            ->get(['id', 'name', 'surname', 'code', 'email']);

        return response()->json($sellers);
    }

    /**
     * Display list of students with pending withdrawals.
     */
    public function pendingWithdrawals(): View
    {
        $schoolId = auth()->user()->school_id;

        // Single aggregated query to get all users with their book counts
        $usersData = DB::table('users')
            ->leftJoin('book_listings', 'users.id', '=', 'book_listings.seller_id')
            ->leftJoin('books', 'book_listings.book_id', '=', 'books.id')
            ->leftJoin('book_sales', 'book_listings.id', '=', 'book_sales.book_listing_id')
            ->leftJoin('withdrawals', function($join) {
                $join->on('book_listings.id', '=', 'withdrawals.book_listing_id');
            })
            ->where('users.school_id', $schoolId)
            ->where('books.school_id', $schoolId)
            ->groupBy('users.id', 'users.name', 'users.surname', 'users.email', 'users.code', 'users.school_id')
            ->selectRaw('
                users.id,
                users.name,
                users.surname,
                users.email,
                users.code,
                users.school_id,
                COUNT(DISTINCT CASE WHEN book_sales.id IS NOT NULL THEN book_sales.book_listing_id END) as sold_count,
                COUNT(DISTINCT CASE WHEN withdrawals.id IS NOT NULL THEN withdrawals.id END) as withdrawn_count,
                COUNT(DISTINCT CASE WHEN book_listings.status IN (\'available\', \'reserved\') AND book_listings.leave = false THEN book_listings.id END) as unsold_count
            ')
            ->get();

        // Filter users with pending withdrawals or pickups
        $pendingUsers = $usersData
            ->filter(function($user) {
                $hasPendingWithdrawal = $user->sold_count > $user->withdrawn_count;
                $hasPendingPickup = $user->unsold_count > 0;
                return $hasPendingWithdrawal || $hasPendingPickup;
            })
            ->map(function($user) {
                // Convert to object with pending counts
                $userData = new \stdClass();
                $userData->id = $user->id;
                $userData->name = $user->name;
                $userData->surname = $user->surname;
                $userData->email = $user->email;
                $userData->code = $user->code;
                $userData->school_id = $user->school_id;
                $userData->pendingWithdrawal = max(0, $user->sold_count - $user->withdrawn_count);
                $userData->pendingBooks = $user->unsold_count;
                return $userData;
            })
            ->sortBy(function($user) {
                return $user->surname . ' ' . $user->name;
            });

        return view('staff.withdrawals.pending', [
            'pendingUsers' => $pendingUsers,
        ]);
    }

    /**
     * Display seller's books and withdrawal summary (authorized by school).
     */
    public function processSeller(User $user): View
    {
        $schoolId = auth()->user()->school_id;

        // Verify seller belongs to staff's school
        if ($user->school_id !== $schoolId) {
            abort(403, 'Non autorizzato');
        }

        // Get all book listings from this seller that belong to staff's school
        $bookListings = BookListing::where('seller_id', $user->id)
            ->bySchool($schoolId)
            ->with(['book', 'bookSales'])
            ->get();

        // Group by status
        $soldBooks = $bookListings->where('status', 'sold')->values();
        $unsoldBooks = $bookListings->whereIn('status', ['available', 'reserved'])->values();

        // Get all withdrawal batches with their withdrawals (filtered by school)
        $withdrawalBatches = WithdrawalBatch::where('user_id', $user->id)
            ->whereHas('user', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['withdrawals.bookListing.book'])
            ->latest()
            ->get();

        // Get all pickup batches with their pickups (filtered by school)
        $pickupBatches = PickupBatch::where('user_id', $user->id)
            ->whereHas('user', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['pickups.bookListing.book'])
            ->latest()
            ->get();

        return view('staff.withdrawals.manage-seller', [
            'seller' => $user,
            'soldBooks' => $soldBooks,
            'unsoldBooks' => $unsoldBooks,
            'withdrawalBatches' => $withdrawalBatches,
            'pickupBatches' => $pickupBatches,
        ]);
    }

    /**
     * Withdraw money for a sold book (authorized by school).
     */
    public function withdrawMoney(Request $request, BookListing $listing): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify listing belongs to staff's school
        if ($listing->book->school_id !== $staffSchoolId) {
            return redirect()->back()->with('error', 'Non autorizzato');
        }

        // Verify the listing is sold and get the price
        if ($listing->status !== 'sold') {
            return redirect()->back()->withErrors(['error' => 'Il libro non è venduto']);
        }

        $seller = $listing->seller;
        $amount = $listing->price;
        $notes = $request->input('notes', "Ritiro libro venduto: {$listing->book->title}");

        // Create withdrawal batch
        $batch = WithdrawalBatch::create([
            'user_id' => $seller->id,
            'total_amount' => $amount,
        ]);

        // Create withdrawal record
        Withdrawal::create([
            'user_id' => $seller->id,
            'book_listing_id' => $listing->id,
            'withdrawal_batch_id' => $batch->id,
            'amount' => $amount,
        ]);

        // Mark the listing as withdrawn (payment collected)
        $listing->update(['status' => 'withdrawn']);

        return redirect()->route('staff.withdrawals.show-batch', $batch->id);
    }

    /**
     * Withdraw an unsold book (remove from listings, authorized by school).
     */
    public function withdrawBook(BookListing $listing): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify listing belongs to staff's school
        if ($listing->book->school_id !== $staffSchoolId) {
            return redirect()->back()->with('error', 'Non autorizzato');
        }

        // Verify the listing is not sold
        if ($listing->status === 'sold') {
            return redirect()->back()->withErrors(['error' => 'Il libro è già venduto']);
        }

        $bookTitle = $listing->book->title;
        $seller = $listing->seller;
        
        // Create pickup batch for single book
        $batch = PickupBatch::create([
            'user_id' => $seller->id,
        ]);

        // Create pickup record (leave = false for reclaim)
        Pickup::create([
            'user_id' => $seller->id,
            'book_listing_id' => $listing->id,
            'pickup_batch_id' => $batch->id,
            'leave' => false,
        ]);

        // Create reclaim record (legacy, for backward compatibility)
        /*
        Reclaim::create([
            'user_id' => $seller->id,
            'book_listing_id' => $listing->id,
            'notes' => "Libro non venduto ritirato: {$bookTitle}",
        ]);
        */
        
        // Mark the listing as reclaimed
        $listing->update(['status' => 'reclaim']);

        return redirect()->route('staff.withdrawals.pickup-summary', $batch)
            ->with('success', "Libro \"{$bookTitle}\" ritirato dalla vendita");
    }

    /**
     * Archive a book that is not sold (leave = true).
     */
    public function archiveBook(BookListing $listing): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify listing belongs to staff's school
        if ($listing->book->school_id !== $staffSchoolId) {
            return redirect()->back()->with('error', 'Non autorizzato');
        }

        // Verify the listing is not sold and has leave = true
        if ($listing->status === 'sold') {
            return redirect()->back()->withErrors(['error' => 'Il libro è già venduto']);
        }

        if (!$listing->leave) {
            return redirect()->back()->withErrors(['error' => 'Questo libro non può essere archiviato']);
        }

        $bookTitle = $listing->book->title;
        $seller = $listing->seller;

        // Create pickup batch for single book
        $batch = PickupBatch::create([
            'user_id' => $seller->id,
        ]);

        // Create pickup record (leave = true for archive)
        Pickup::create([
            'user_id' => $seller->id,
            'book_listing_id' => $listing->id,
            'pickup_batch_id' => $batch->id,
            'leave' => true,
        ]);

        // Mark the listing as archived
        $listing->update(['status' => 'archived']);

        return redirect()->route('staff.withdrawals.pickup-summary', $batch)
            ->with('success', "Libro \"{$bookTitle}\" archiviato");
    }

    /**
     * Withdraw all available/reserved books with leave = false and archive books with leave = true for a seller.
     */
    public function withdrawAllBooks(User $user): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify seller belongs to staff's school
        if ($user->school_id !== $staffSchoolId) {
            return redirect()->back()->with('error', 'Non autorizzato');
        }

        // Create a single pickup batch for all operations
        $batch = PickupBatch::create([
            'user_id' => $user->id,
        ]);

        // Get all books to withdraw (available/reserved and leave = false)
        $booksToWithdraw = BookListing::where('seller_id', $user->id)
            ->bySchool($staffSchoolId)
            ->whereIn('status', ['available', 'reserved'])
            ->where('leave', false)
            ->get();

        $withdrawCount = 0;
        foreach ($booksToWithdraw as $listing) {
            // Create pickup record (leave = false for reclaim)
            Pickup::create([
                'user_id' => $user->id,
                'book_listing_id' => $listing->id,
                'pickup_batch_id' => $batch->id,
                'leave' => false,
            ]);

            // Create reclaim record (legacy, for backward compatibility)
            /*
            Reclaim::create([
                'user_id' => $user->id,
                'book_listing_id' => $listing->id,
                'notes' => "Ritiro massivo libri non venduti: {$listing->book->title}",
            ]);
            */
            
            // Mark the listing as reclaimed
            $listing->update(['status' => 'reclaim']);
            $withdrawCount++;
        }

        // Get all books to archive (available/reserved and leave = true)
        $booksToArchive = BookListing::where('seller_id', $user->id)
            ->bySchool($staffSchoolId)
            ->whereIn('status', ['available', 'reserved'])
            ->where('leave', true)
            ->get();

        $archiveCount = 0;
        foreach ($booksToArchive as $listing) {
            // Create pickup record (leave = true for archive)
            Pickup::create([
                'user_id' => $user->id,
                'book_listing_id' => $listing->id,
                'pickup_batch_id' => $batch->id,
                'leave' => true,
            ]);

            $listing->update(['status' => 'archived']);
            $archiveCount++;
        }

        $message = "{$withdrawCount} libro(i) ritirato(i)";
        if ($archiveCount > 0) {
            $message .= ", {$archiveCount} archiviato(i)";
        }

        return redirect()->route('staff.withdrawals.pickup-summary', $batch)
            ->with('success', $message);
    }

    /**
     * Withdraw money for all sold books in one operation.
     */
    public function withdrawAllSoldBooks(User $user): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify seller belongs to staff's school
        if ($user->school_id !== $staffSchoolId) {
            return redirect()->back()->with('error', 'Non autorizzato');
        }

        // Get all sold books
        $soldBooks = BookListing::where('seller_id', $user->id)
            ->bySchool($staffSchoolId)
            ->where('status', 'sold')
            ->get();

        // Create withdrawal batch
        $batch = WithdrawalBatch::create([
            'user_id' => $user->id,
            'total_amount' => 0,
        ]);

        $withdrawCount = 0;
        $totalAmount = 0;
        foreach ($soldBooks as $listing) {
            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'book_listing_id' => $listing->id,
                'withdrawal_batch_id' => $batch->id,
                'amount' => $listing->price,
            ]);
            
            $totalAmount += $withdrawal->amount;
            
            // Mark the listing as withdrawn
            $listing->update(['status' => 'withdrawn']);
            $withdrawCount++;
        }

        // Update batch total amount
        $batch->update(['total_amount' => $totalAmount]);

        return redirect()->route('staff.withdrawals.show-batch', $batch->id);
    }

    /**
     * Store a newly created resource in storage (authorized by school).
     */
    public function store(Request $request): RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify user belongs to staff's school
        $user = User::findOrFail($validated['user_id']);
        if ($user->school_id !== $staffSchoolId) {
            return redirect()->back()->with('error', 'Non autorizzato');
        }

        // Verify user has sufficient balance
        $availableBalance = $user->getAvailableBalance();

        if ($validated['amount'] > $availableBalance) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => "L'importo richiesto ({$validated['amount']}€) supera il saldo disponibile ({$availableBalance}€)"]);
        }

        Withdrawal::create($validated);

        return redirect()->route('staff.withdrawals.index')
            ->with('success', 'Riscossione registrata con successo');
    }

    /**
     * Display the specified resource (authorized by school).
     */
    public function show(Withdrawal $withdrawal): View
    {
        $staffSchoolId = auth()->user()->school_id;
        $withdrawal->load('user', 'bookListing.book');

        // If associated with a book listing, verify it belongs to staff's school
        if ($withdrawal->book_listing_id) {
            if ($withdrawal->bookListing->book->school_id !== $staffSchoolId) {
                abort(403, 'Non autorizzato');
            }
        }

        return view('staff.withdrawals.show', [
            'withdrawal' => $withdrawal,
        ]);
    }

    /**
     * Display withdrawal batch summary (authorized by school).
     */
    public function showBatch(WithdrawalBatch $withdrawalBatch): View
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify seller belongs to staff's school
        if ($withdrawalBatch->user->school_id !== $staffSchoolId) {
            abort(403, 'Non autorizzato');
        }

        // Load withdrawals with book listings
        $withdrawalBatch->load(['withdrawals.bookListing.book', 'user']);

        return view('staff.withdrawals.batch-summary', [
            'batch' => $withdrawalBatch,
        ]);
    }

    /**
     * Show pickup batch summary (riepilogo ritiri/archiviazioni)
     */
    public function showPickupBatch(PickupBatch $pickupBatch): View
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify seller belongs to staff's school
        if ($pickupBatch->user->school_id !== $staffSchoolId) {
            abort(403, 'Non autorizzato');
        }

        // Load pickups with relationships
        $pickupBatch->load(['pickups.bookListing.book', 'user']);

        return view('staff.withdrawals.pickup-summary', [
            'batch' => $pickupBatch,
        ]);
    }

    /**
     * Process all books (sold and unsold) for a seller in one operation.
     */
    public function processComplete(User $user): View|RedirectResponse
    {
        $staffSchoolId = auth()->user()->school_id;

        // Verify seller belongs to staff's school
        if ($user->school_id !== $staffSchoolId) {
            return redirect()->back()->with('error', 'Non autorizzato');
        }

        // Get all sold books
        $soldBooks = BookListing::where('seller_id', $user->id)
            ->bySchool($staffSchoolId)
            ->where('status', 'sold')
            ->get();

        // Get all unsold books (available/reserved)
        $unsoldBooks = BookListing::where('seller_id', $user->id)
            ->bySchool($staffSchoolId)
            ->whereIn('status', ['available', 'reserved'])
            ->get();

        // Create withdrawal batch for sold books
        $withdrawalBatch = null;
        $totalWithdrawn = 0;
        $withdrawCount = 0;

        if ($soldBooks->count() > 0) {
            $withdrawalBatch = WithdrawalBatch::create([
                'user_id' => $user->id,
                'total_amount' => 0,
            ]);

            foreach ($soldBooks as $listing) {
                Withdrawal::create([
                    'user_id' => $user->id,
                    'book_listing_id' => $listing->id,
                    'withdrawal_batch_id' => $withdrawalBatch->id,
                    'amount' => $listing->price,
                ]);
                
                $totalWithdrawn += $listing->price;
                $listing->update(['status' => 'withdrawn']);
                $withdrawCount++;
            }

            $withdrawalBatch->update(['total_amount' => $totalWithdrawn]);
        }

        // Create pickup batch for unsold books
        $pickupBatch = null;
        $archivedCount = 0;

        if ($unsoldBooks->count() > 0) {
            $pickupBatch = PickupBatch::create([
                'user_id' => $user->id,
            ]);

            foreach ($unsoldBooks as $listing) {
                Pickup::create([
                    'user_id' => $user->id,
                    'book_listing_id' => $listing->id,
                    'pickup_batch_id' => $pickupBatch->id,
                    'leave' => $listing->leave,
                ]);

                // Set status based on leave field
                $newStatus = $listing->leave ? 'reclaim' : 'archived';
                $listing->update(['status' => $newStatus]);
                $archivedCount++;
            }
        }

        // Load relationships for display
        if ($withdrawalBatch) {
            $withdrawalBatch->load(['withdrawals.bookListing.book', 'user']);
        }
        if ($pickupBatch) {
            $pickupBatch->load(['pickups.bookListing.book', 'user']);
        }

        return view('staff.withdrawals.complete-summary', [
            'seller' => $user,
            'withdrawalBatch' => $withdrawalBatch,
            'pickupBatch' => $pickupBatch,
            'withdrawCount' => $withdrawCount,
            'totalWithdrawn' => $totalWithdrawn,
            'archivedCount' => $archivedCount,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
