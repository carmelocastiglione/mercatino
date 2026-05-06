<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookListing;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of all withdrawals.
     */
    public function index(): View
    {
        // Get all users that have book listings (sellers)
        $sellers = User::whereHas('bookListings')->with(['bookListings.book', 'bookListings.bookSales', 'withdrawals'])
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
     * Search sellers for withdrawal management.
     */
    public function searchSellers(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        
        // Get sellers (users with book listings)
        $sellers = User::whereHas('bookListings')
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
     * Display seller's books and withdrawal summary.
     */
    public function processSeller(User $user): View
    {
        // Get all book listings from this seller
        $bookListings = BookListing::where('seller_id', $user->id)
            ->with(['book', 'bookSales'])
            ->get();

        // Group by status
        $soldBooks = $bookListings->where('status', 'sold')->values();
        $unsoldBooks = $bookListings->where('status', '!=', 'sold')->values();

        return view('staff.withdrawals.manage-seller', [
            'seller' => $user,
            'soldBooks' => $soldBooks,
            'unsoldBooks' => $unsoldBooks,
        ]);
    }

    /**
     * Withdraw money for a sold book.
     */
    public function withdrawMoney(Request $request, BookListing $listing): RedirectResponse
    {
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
            'amount' => $amount,
            'notes' => $notes,
        ]);

        // Mark the listing as withdrawn (payment collected)
        $listing->update(['status' => 'withdrawn']);

        return redirect()->route('staff.withdrawals.process-seller', $seller->id)
            ->with('success', "Riscosso {$amount}€ per il libro \"{$listing->book->title}\"");
    }

    /**
     * Withdraw an unsold book (remove from listings).
     */
    public function withdrawBook(BookListing $listing): RedirectResponse
    {
        // Verify the listing is not sold
        if ($listing->status === 'sold') {
            return redirect()->back()->withErrors(['error' => 'Il libro è già venduto']);
        }

        $bookTitle = $listing->book->title;
        $listing->update(['status' => 'withdrawn']);

        return redirect()->back()
            ->with('success', "Libro \"{$bookTitle}\" ritirato dalla vendita");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify user has sufficient balance
        $user = User::findOrFail($validated['user_id']);
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
     * Display the specified resource.
     */
    public function show(Withdrawal $withdrawal): View
    {
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
