<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookListing;
use App\Models\Reclaim;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WithdrawalBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of all withdrawals (filtered by staff's school).
     */
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;

        // Get all users that have book listings from staff's school
        $sellers = User::whereHas('bookListings.book', function ($query) use ($schoolId) {
                $query->bySchool($schoolId);
            })
            ->where('school_id', $schoolId)
            ->with(['bookListings.book', 'bookListings.bookSales', 'withdrawals'])
            ->latest('updated_at')
            ->paginate(15);

        // Calculate total amounts
        $totalEarned = User::whereHas('bookListings.book', function ($query) use ($schoolId) {
                $query->bySchool($schoolId);
            })
            ->where('school_id', $schoolId)
            ->get()
            ->sum(fn($user) => $user->getTotalSalesAmount());

        $totalWithdrawn = User::whereHas('bookListings.book', function ($query) use ($schoolId) {
                $query->bySchool($schoolId);
            })
            ->where('school_id', $schoolId)
            ->get()
            ->sum(fn($user) => $user->getTotalWithdrawnAmount());

        $totalAvailable = $totalEarned - $totalWithdrawn;

        // Calculate progress: users who have withdrawn vs users who have sold books
        $usersWithSoldBooks = BookListing::join('books', 'book_listings.book_id', '=', 'books.id')
            ->where('books.school_id', $schoolId)
            ->where('book_listings.status', 'sold')
            ->distinct('book_listings.seller_id')
            ->count('book_listings.seller_id');

        $usersWithdrawn = Withdrawal::whereHas('user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->distinct('user_id')
            ->count('user_id');

        $withdrawalProgress = $usersWithSoldBooks > 0 ? ($usersWithdrawn / $usersWithSoldBooks) * 100 : 0;

        return view('staff.withdrawals.index', [
            'sellers' => $sellers,
            'totalEarned' => $totalEarned,
            'totalWithdrawn' => $totalWithdrawn,
            'totalAvailable' => $totalAvailable,
            'usersWithdrawn' => $usersWithdrawn,
            'usersWithSoldBooks' => $usersWithSoldBooks,
            'withdrawalProgress' => $withdrawalProgress,
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
                $q->where('name', 'ilike', "%$query%")
                    ->orWhere('surname', 'ilike', "%$query%")
                    ->orWhere('code', 'ilike', "%$query%")
                    ->orWhere('email', 'ilike', "%$query%");
            })
            ->take(10)
            ->get(['id', 'name', 'surname', 'code', 'email']);

        return response()->json($sellers);
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
        $reclaimedBooks = $bookListings->where('status', 'reclaim')->values();
        $archivedBooks = $bookListings->where('status', 'archived')->values();

        return view('staff.withdrawals.manage-seller', [
            'seller' => $user,
            'soldBooks' => $soldBooks,
            'unsoldBooks' => $unsoldBooks,
            'reclaimedBooks' => $reclaimedBooks,
            'archivedBooks' => $archivedBooks,
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

        return redirect()->route('staff.withdrawals.process-seller', $seller->id)
            ->with('success', "Riscosso {$amount}€ per il libro \"{$listing->book->title}\"");
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
        
        // Create reclaim record
        Reclaim::create([
            'user_id' => $seller->id,
            'book_listing_id' => $listing->id,
            'notes' => "Libro non venduto ritirato: {$bookTitle}",
        ]);
        
        // Mark the listing as reclaimed
        $listing->update(['status' => 'reclaim']);

        return redirect()->back()
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

        // Mark the listing as archived
        $listing->update(['status' => 'archived']);

        return redirect()->back()
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

        // Get all books to withdraw (available/reserved and leave = false)
        $booksToWithdraw = BookListing::where('seller_id', $user->id)
            ->bySchool($staffSchoolId)
            ->whereIn('status', ['available', 'reserved'])
            ->where('leave', false)
            ->get();

        $withdrawCount = 0;
        foreach ($booksToWithdraw as $listing) {
            // Create reclaim record
            Reclaim::create([
                'user_id' => $user->id,
                'book_listing_id' => $listing->id,
                'notes' => "Ritiro massivo libri non venduti: {$listing->book->title}",
            ]);
            
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
            $listing->update(['status' => 'archived']);
            $archiveCount++;
        }

        $message = "{$withdrawCount} libro(i) ritirato(i)";
        if ($archiveCount > 0) {
            $message .= ", {$archiveCount} archiviato(i)";
        }

        return redirect()->back()
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

        return redirect()->back()
            ->with('success', "{$withdrawCount} libro(i) venduto(i) - Soldi ritirati");
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
