<?php

namespace App\Http\Controllers\Staff;

use App\Models\Reclaim;
use App\Models\BookListing;
use App\Models\BookSale;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReclaimController extends Controller
{
    /**
     * Display all pending reclaims
     */
    public function index()
    {
        return view('staff.reclaims.index', [
            'title' => 'Gestione Resi',
            'description' => 'Gestisci i resi dei libri venduti',
        ]);
    }

    /**
     * Search buyers by name, email, or code (filtered by staff's school)
     */
    public function searchBuyers(Request $request)
    {
        $query = $request->query('q', '');
        $staffSchoolId = auth()->user()->school_id;

        if (strlen($query) < 2) {
            return response()->json(['buyers' => []]);
        }

        $buyers = User::where('role', 'studente')
            ->where('school_id', $staffSchoolId)
            ->where(function ($q) use ($query) {
                $q->whereRaw("LOWER(name) ILIKE ?", ["%{$query}%"])
                  ->orWhereRaw("LOWER(surname) ILIKE ?", ["%{$query}%"])
                  ->orWhereRaw("LOWER(email) ILIKE ?", ["%{$query}%"])
                  ->orWhereRaw("LOWER(code) ILIKE ?", ["%{$query}%"]);
            })
            ->limit(10)
            ->get(['id', 'name', 'surname', 'email', 'code']);

        return response()->json(['buyers' => $buyers]);
    }

    /**
     * Get books purchased by a buyer (filtered by staff's school)
     */
    public function getBuyerBooks(Request $request)
    {
        $buyerId = $request->query('buyer_id');

        // Find all books purchased by the buyer (via book_sales) from staff's school
        $books = BookListing::whereIn('id', function ($query) use ($buyerId) {
                $query->select('book_listing_id')
                      ->from('book_sales')
                      ->where('buyer_id', $buyerId);
            })
            ->where('status', 'sold')
            ->bySchool(auth()->user()->school_id)
            ->with('book')
            ->get();

        $reclaims = Reclaim::whereIn('book_listing_id', $books->pluck('id'))
            ->where('status', '!=', 'rejected')
            ->get();

        return response()->json([
            'books' => $books->map(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->book->title,
                    'author' => $b->book->author,
                    'condition' => $b->condition,
                    'price' => $b->price,
                    'status' => $b->status,
                ];
            }),
            'reclaims' => $reclaims,
        ]);
    }

    /**
     * Show form to approve/reject a reclaim (authorized by school)
     */
    public function create(Request $request)
    {
        $bookListingId = $request->query('book_listing_id');
        $bookListing = BookListing::with('book', 'seller')->findOrFail($bookListingId);
        $staffSchoolId = auth()->user()->school_id;

        if ($bookListing->book->school_id !== $staffSchoolId) {
            abort(403, 'Non autorizzato');
        }

        if ($bookListing->status !== 'sold') {
            return back()->withErrors('Il libro non è venduto.');
        }

        return view('staff.reclaims.create', [
            'bookListing' => $bookListing,
            'title' => 'Gestisci Reso',
        ]);
    }

    /**
     * Store a reclaim (approve or reject, authorized by school)
     */
    public function store(Request $request)
    {
        try {
            $bookListingId = $request->input('book_listing_id');
            $action = $request->input('action'); // 'approve' o 'reject'
            $bookListing = BookListing::find($bookListingId);
            $staffSchoolId = auth()->user()->school_id;

            if (!$bookListing || $bookListing->status !== 'sold') {
                return back()->withErrors('Libro non trovato o non venduto');
            }

            if ($bookListing->book->school_id !== $staffSchoolId) {
                return back()->with('error', 'Non autorizzato');
            }

            if ($action === 'approve') {
                // Crea e approva il reso
                $reclaim = Reclaim::create([
                    'user_id' => $bookListing->seller_id,
                    'book_listing_id' => $bookListingId,
                    'status' => 'approved',
                ]);

                // Ripristina il libro a available
                $bookListing->update(['status' => 'available']);
                BookSale::where('book_listing_id', $bookListing->id)->delete();

                return redirect()->route('staff.reclaims.show', $reclaim->id)
                    ->with('success', 'Reso approvato! Il libro è stato rimesso in vendita.');

            } elseif ($action === 'reject') {
                // Crea e rifiuta il reso
                $rejection_reason = $request->input('rejection_reason');

                $reclaim = Reclaim::create([
                    'user_id' => $bookListing->seller_id,
                    'book_listing_id' => $bookListingId,
                    'status' => 'rejected',
                    'rejection_reason' => $rejection_reason,
                ]);

                return redirect()->route('staff.reclaims.show', $reclaim->id)
                    ->with('success', 'Reso rifiutato.');
            }

            return back()->withErrors('Azione non valida');
        } catch (\Exception $e) {
            return back()->withErrors('Errore: ' . $e->getMessage());
        }
    }

    /**
     * Create a new reclaim for a book (legacy - used by index page, authorized by school)
     */
    public function createReclaim(Request $request)
    {
        try {
            $bookListingId = $request->input('book_listing_id');
            $bookListing = BookListing::find($bookListingId);
            $staffSchoolId = auth()->user()->school_id;

            if (!$bookListing || $bookListing->status !== 'sold') {
                return response()->json(['success' => false, 'message' => 'Libro non trovato o non venduto'], 400);
            }

            if ($bookListing->book->school_id !== $staffSchoolId) {
                return response()->json(['success' => false, 'message' => 'Non autorizzato'], 403);
            }

            // Controlla se esiste già un reso non rifiutato per questo libro
            $existingReclaim = Reclaim::where('book_listing_id', $bookListingId)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingReclaim) {
                return response()->json(['success' => false, 'message' => 'Esiste già un reso per questo libro'], 400);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Reindirizzamento...',
                'redirect_url' => route('staff.reclaims.create', ['book_listing_id' => $bookListingId]),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show reclaim details (view only, authorized by school)
     */
    public function show(Reclaim $reclaim)
    {
        $staffSchoolId = auth()->user()->school_id;
        $reclaim->load(['user', 'bookListing.book', 'bookListing.seller']);

        if ($reclaim->bookListing->book->school_id !== $staffSchoolId) {
            abort(403, 'Non autorizzato');
        }

        return view('staff.reclaims.show', [
            'reclaim' => $reclaim,
            'title' => 'Dettagli Reso',
        ]);
    }

    /**
     * Delete a reclaim (undo, authorized by school)
     */
    public function destroy(Reclaim $reclaim)
    {
        try {
            $staffSchoolId = auth()->user()->school_id;
            $reclaim->load('bookListing.book');

            if ($reclaim->bookListing->book->school_id !== $staffSchoolId) {
                return back()->with('error', 'Non autorizzato');
            }

            $reclaim->delete();
            return redirect()->route('staff.reclaims.index')
                ->with('success', 'Reso eliminato.');
        } catch (\Exception $e) {
            return back()->withErrors('Errore: ' . $e->getMessage());
        }
    }

}
