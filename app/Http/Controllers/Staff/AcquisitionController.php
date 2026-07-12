<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Helpers\PriceHelper;
use App\Models\Book;
use App\Models\BookDelivery;
use App\Models\Acquisition;
use App\Models\BookListing;
use App\Models\User;
use App\Models\BookSale;
use App\Models\Reclaim;
use App\Models\BookReservation;
use App\Models\Withdrawal;
use App\Models\Pickup;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class AcquisitionController extends Controller
{
    /**
     * Display the list of acquisitions.
     */
    public function index(): View
    {
        $query = request()->input('q', '');
        
        // Totals without filters
        $totalAcquisitionsCount = Acquisition::whereHas('bookListings.book', fn($q) => $q->bySchool(auth()->user()->school_id))
            ->count();

        $totalBooksCount = BookListing::whereHas('acquisition.bookListings.book', fn($q) => $q->bySchool(auth()->user()->school_id))
            ->whereHas('book', fn($q) => $q->bySchool(auth()->user()->school_id))
            ->count();

        $totalAcquisitionsAmount = Acquisition::whereHas('bookListings.book', fn($q) => $q->bySchool(auth()->user()->school_id))
            ->sum('total_price');

        // Filtered acquisitions for table
        $acquisitions = Acquisition::with('staff', 'seller', 'bookListings.book')
            ->whereHas('bookListings.book', fn($q) => $q->bySchool(auth()->user()->school_id))
            ->when($query, function ($q) use ($query) {
                return $q->where('ean13', 'ilike', "%{$query}%")
                    ->orWhereHas('seller', function ($userQuery) use ($query) {
                        $userQuery->where('surname', 'ilike', "%{$query}%")
                            ->orWhere('email', 'ilike', "%{$query}%")
                            ->orWhere('code', 'ilike', "%{$query}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.acquisitions.index', [
            'acquisitions' => $acquisitions,
            'totalAcquisitionsCount' => $totalAcquisitionsCount,
            'totalBooksCount' => $totalBooksCount,
            'totalAcquisitionsAmount' => $totalAcquisitionsAmount,
            'filterQuery' => $query,
        ]);
    }

    /**
     * Search books by title or ISBN (filtered by staff's school).
     */
    public function searchBooks()
    {
        $query = request()->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $school = auth()->user()->school;

        $books = Book::bySchool(auth()->user()->school_id)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('isbn', 'ilike', "%{$query}%")
                    ->orWhere('author', 'ilike', "%{$query}%");
            })
            ->select('id', 'title', 'author', 'isbn', 'original_price')
            ->limit(10)
            ->get()
            ->map(function ($book) use ($school) {
                $priceDetails = PriceHelper::calculatePrice($book->original_price, $school, true);
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'original_price' => $priceDetails['original_price'],
                    'marketplace_price' => $priceDetails['marketplace_price'],
                    'fee' => $priceDetails['fee'],
                    'price' => $priceDetails['total'],
                ];
            });

        return response()->json($books);
    }

    /**
     * Search sellers by surname, email or code (filtered by staff's school).
     */
    public function searchSellers()
    {
        $query = request()->input('q');
        $staffSchoolId = auth()->user()->school_id;

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('school_id', $staffSchoolId)
            ->where(function ($q) use ($query) {
                $q->where('surname', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%")
                    ->orWhere('code', 'ilike', "%{$query}%");
            })
            ->select('id', 'name', 'surname', 'email', 'code')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Search students by name, email, or code (filtered by staff's school).
     */
    public function searchStudents(): JsonResponse
    {
        $query = request()->input('q');
        $staffSchoolId = auth()->user()->school_id;

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $students = User::where('school_id', $staffSchoolId)
            ->where(function ($q) use ($query) {
                $q->where('surname', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%")
                    ->orWhere('code', 'ilike', "%{$query}%")
                    ->orWhereHas('deliveryBatches', function ($batchQuery) use ($query) {
                        $batchQuery->where('ean13', 'ilike', "%{$query}%");
                    });
            })
            ->select('id', 'name', 'surname', 'email', 'code')
            ->limit(10)
            ->get();

        return response()->json($students);
    }

    /**
     * Get pending deliveries for a student (filtered by staff's school).
     */
    public function getStudentDeliveries(): JsonResponse
    {
        $studentId = request()->input('student_id');

        if (!$studentId) {
            return response()->json(['error' => 'Student ID is required'], 400);
        }

        $deliveries = BookDelivery::where('user_id', $studentId)
            ->where('status', 'pending')
            ->bySchool(auth()->user()->school_id)
            ->with('book')
            ->get();

        return response()->json([
            'deliveries' => $deliveries->map(fn($d) => [
                'id' => $d->id,
                'book' => [
                    'id' => $d->book->id,
                    'title' => $d->book->title,
                    'author' => $d->book->author,
                ],
                'condition' => $d->condition,
                'price' => $d->price,
            ])
        ]);
    }

    /**
     * Show the form for creating a new acquisition.
     */
    public function create(): View
    {
        return view('staff.acquisitions.create', [
            'enableOnlineSales' => auth()->user()->school->hasFeatureEnabled('enable_online_sales'),
        ]);
    }

    /**
     * Create a new book via AJAX (for staff to add new books).
     */
    public function createBook()
    {
        try {
            $validated = request()->validate([
                'title' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'isbn' => ['required', 'string', 'max:20', 'unique:books'],
                'original_price' => ['required', 'numeric', 'min:0'],
            ]);

            $book = Book::create([
                'school_id' => auth()->user()->school_id,
                'title' => $validated['title'],
                'author' => $validated['author'],
                'isbn' => $validated['isbn'],
                'original_price' => $validated['original_price'],
            ]);

            // Calcola i prezzi usando PriceHelper
            $school = auth()->user()->school;
            $priceDetails = PriceHelper::calculatePrice($book->original_price, $school, true);

            return response()->json([
                'book' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'original_price' => $priceDetails['original_price'],
                    'marketplace_price' => $priceDetails['marketplace_price'],
                    'fee' => $priceDetails['fee'],
                    'price' => $priceDetails['total'],
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store multiple acquisitions in batch (create Acquisition + BookListings).
     */
    public function storeBatch()
    {
        try {
            $staffSchoolId = auth()->user()->school_id;
            $school = auth()->user()->school;

            $validated = request()->validate([
                'leave' => ['nullable', 'boolean'],
                'seller_password' => ['nullable', 'string'], // Password del venditore (temporanea)
                'acquisitions' => ['required', 'array', 'min:1'],
                'acquisitions.*.seller_id' => ['required', 'exists:users,id'],
                'acquisitions.*.book_id' => ['required', 'exists:books,id'],
                'acquisitions.*.condition' => ['required', 'in:like-new,good,fair,poor'],
                'acquisitions.*.price' => ['required', 'numeric', 'min:0'],
                'acquisitions.*.leave' => ['nullable', 'boolean'],
            ]);

            // Verify all books and sellers belong to staff's school
            foreach ($validated['acquisitions'] as $item) {
                $book = Book::find($item['book_id']);
                $seller = User::find($item['seller_id']);

                if ($book->school_id !== $staffSchoolId) {
                    return response()->json([
                        'message' => 'Il libro selezionato non appartiene alla tua scuola',
                    ], 403);
                }

                if ($seller->school_id !== $staffSchoolId) {
                    return response()->json([
                        'message' => 'Il venditore selezionato non appartiene alla tua scuola',
                    ], 403);
                }
            }

            // Get all acquisitions to group by seller
            $acquisitionsBySellerAndLeave = [];
            $totalPrice = 0;

            foreach ($validated['acquisitions'] as $item) {
                $key = $item['seller_id'] . '_' . ($item['leave'] ?? false ? '1' : '0');
                if (!isset($acquisitionsBySellerAndLeave[$key])) {
                    $acquisitionsBySellerAndLeave[$key] = [
                        'seller_id' => $item['seller_id'],
                        'leave' => $item['leave'] ?? false,
                        'items' => [],
                        'total' => 0,
                    ];
                }
                $acquisitionsBySellerAndLeave[$key]['items'][] = $item;
                $acquisitionsBySellerAndLeave[$key]['total'] += $item['price'];
                $totalPrice += $item['price'];
            }

            $createdCount = 0;
            $booksCount = 0;
            $firstAcquisitionId = null;

            // Create an Acquisition for each seller/leave combination
            foreach ($acquisitionsBySellerAndLeave as $group) {
                $acquisition = Acquisition::create([
                    'staff_id' => auth()->id(),
                    'seller_id' => $group['seller_id'],
                    'status' => 'completed',
                    'total_price' => $group['total'],
                    'notes' => null,
                ]);

                if (!$firstAcquisitionId) {
                    $firstAcquisitionId = $acquisition->id;
                }

                // Create BookListings for each item
                foreach ($group['items'] as $item) {
                    // Calculate selling price = acquisition price + purchase_fee + sales_fee
                    $priceSell = PriceHelper::calculateSellingPrice($item['price'], $school);

                    BookListing::create([
                        'acquisition_id' => $acquisition->id,
                        'book_id' => $item['book_id'],
                        'seller_id' => $item['seller_id'],
                        'condition' => $item['condition'],
                        'price' => $item['price'],
                        'price_sell' => $priceSell,
                        'status' => 'available',
                        'views' => 0,
                        'favorites' => 0,
                        'leave' => $item['leave'] ?? false,
                    ]);
                    $booksCount++;
                }

                $createdCount++;
            }

            // Se online sales è abilitato e c'è una password, salva in session temporaneamente
            if ($school->getSetting('enable_online_sales') && !empty($validated['seller_password'])) {
                session()->put('seller_login_credentials', [
                    'password' => $validated['seller_password'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $createdCount . ' acquisizion' . ($createdCount !== 1 ? 'i' : 'e') . ' completata/e con ' . $booksCount . ' libr' . ($booksCount !== 1 ? 'i' : 'o'),
                'count' => $booksCount,
                'acquisition_id' => $firstAcquisitionId,
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
     * Show acquisition summary/receipt.
     */
    public function show(Acquisition $acquisition): View
    {
        $acquisition->load('staff', 'seller', 'bookListings.book');
        
        $school = $acquisition->staff->school;
        
        // Carica le date di ritiro della scuola
        $withdrawDates = $school->withdrawDates()
            ->where('is_active', true)
            ->orderBy('scheduled_date')
            ->get();
        
        // Format withdrawal dates
        $activeDates = $withdrawDates->map(fn($date) => $date->scheduled_date->format('d/m/Y'))->toArray();
        $withdrawDatesText = !empty($activeDates) 
            ? implode(', ', $activeDates)
            : 'stabiliti dalla scuola';
        
        $referringName = $school->getSetting('referring_name');
        
        return view('staff.acquisitions.show', [
            'acquisition' => $acquisition,
            'school' => $school->description,
            'referringName' => $referringName,
            'city' => $school->city,
            'withdrawDates' => $withdrawDates,
            'withdrawDatesText' => $withdrawDatesText,
        ]);
    }

    /**
     * Mark a listing as sold.
     */
    public function markAsSold(BookListing $listing): RedirectResponse
    {
        $listing->update(['status' => 'sold']);

        return back()->with('success', 'Libro marcato come venduto!');
    }

    /**
     * Destroy an acquisition (hard delete with dependency checks)
     * 
     * Logica di eliminazione:
     * - BookSale: BLOCCA (devi prima eliminare la vendita)
     * - Reclaim: ELIMINA DIRETTAMENTE
     * - BookReservation: BLOCCA (devi prima vendere il libro)
     * - Withdrawal: BLOCCA (devi prima eliminare la riscossione)
     * - Pickup: BLOCCA (devi prima eliminare il ritiro)
     */
    public function destroyAcquisition(Acquisition $acquisition): RedirectResponse
    {
        try {
            $staffSchoolId = auth()->user()->school_id;

            // Verify acquisition belongs to staff's school
            if ($acquisition->seller->school_id !== $staffSchoolId) {
                abort(403, 'Non autorizzato');
            }

            // Load all book listings with their dependencies
            $acquisition->load([
                'bookListings.bookSales',
                'bookListings.reclaim',
                'bookListings.bookReservations',
                'bookListings.withdrawal',
                'bookListings.pickups',
            ]);

            // Check for dependencies
            $blockedByDependency = false;
            $blockedReasons = [];

            foreach ($acquisition->bookListings as $listing) {
                // 1. BookSale - BLOCCA
                if ($listing->bookSales->count() > 0) {
                    $blockedByDependency = true;
                    $blockedReasons[] = "Libro \"{$listing->book->title}\" ha vendite registrate. Elimina prima la vendita.";
                }

                // 2. Reclaim - ELIMINA DIRETTAMENTE (verrà gestito dopo)
                // (non blocca)

                // 3. BookReservation - BLOCCA
                if ($listing->bookReservations->count() > 0) {
                    $blockedByDependency = true;
                    $blockedReasons[] = "Libro \"{$listing->book->title}\" ha prenotazioni. Vendi il libro o rifiuta le prenotazioni prima.";
                }

                // 4. Withdrawal - BLOCCA
                if ($listing->withdrawal->count() > 0) {
                    $blockedByDependency = true;
                    $blockedReasons[] = "Libro \"{$listing->book->title}\" ha riscossioni registrate. Elimina la riscossione prima.";
                }

                // 5. Pickup - BLOCCA
                if ($listing->pickups->count() > 0) {
                    $blockedByDependency = true;
                    $blockedReasons[] = "Libro \"{$listing->book->title}\" è nel ritiro invenduti. Elimina il ritiro prima.";
                }
            }

            // If blocked, return error
            if ($blockedByDependency) {
                return back()->withErrors([
                    'error' => 'Impossibile eliminare l\'acquisizione:' . "\n" . implode("\n", $blockedReasons)
                ]);
            }

            // Delete Reclaims directly (no blocking)
            foreach ($acquisition->bookListings as $listing) {
                Reclaim::where('book_listing_id', $listing->id)->delete();
            }

            // Delete BookListings (cascade will handle Pickups due to cascadeOnDelete)
            foreach ($acquisition->bookListings as $listing) {
                $listing->forceDelete(); // Hard delete, bypassing SoftDeletes
            }

            // Delete the acquisition itself
            $acquisition->delete();

            return redirect()->route('staff.acquisitions.index')
                ->with('success', 'Acquisizione eliminata con successo.');

        } catch (\Exception $e) {
            return back()->with('error', 'Errore durante la cancellazione: ' . $e->getMessage());
        }
    }
}
