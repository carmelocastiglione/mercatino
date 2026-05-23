<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookListing;
use App\Models\Reclaim;
use App\Models\User;
use App\Models\Withdrawal;
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

        return view('staff.withdrawals.index', [
            'sellers' => $sellers,
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
        $unsoldBooks = $bookListings->where('status', 'available')->values();

        return view('staff.withdrawals.manage-seller', [
            'seller' => $user,
            'soldBooks' => $soldBooks,
            'unsoldBooks' => $unsoldBooks,
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

        // Create withdrawal record
        Withdrawal::create([
            'user_id' => $seller->id,
            'book_listing_id' => $listing->id,
            'amount' => $amount,
            'notes' => $notes,
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
