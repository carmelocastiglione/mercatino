<?php

namespace App\Services;

use App\Models\User;
use App\Models\Book;
use App\Models\BookListing;
use App\Models\BookReservation;
use App\Models\BookSale;
use App\Models\Withdrawal;
use App\Models\Pickup;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Mappa dei tipi esportabili con colonne associate
     */
    private const EXPORT_TYPES = [
        'utenti' => [
            'label' => 'Utenti',
            'query' => 'getUsers',
            'columns' => ['Nome', 'Cognome', 'Email', 'Codice', 'Ruolo', 'Data Creazione'],
            'fields' => ['name', 'surname', 'email', 'code', 'role', 'created_at'],
        ],
        'libri' => [
            'label' => 'Libri in catalogo',
            'query' => 'getBooks',
            'columns' => ['Titolo', 'Autore', 'ISBN', 'Prezzo Originale', 'Data Creazione'],
            'fields' => ['title', 'author', 'isbn', 'original_price', 'created_at'],
        ],
        'libri_acquisiti' => [
            'label' => 'Libri acquisiti (tutti)',
            'query' => 'getAcquiredListings',
            'columns' => ['ISBN', 'Titolo', 'Condizione', 'Prezzo acquisizione', 'Prezzo vendita', 'Lascia', 'Venditore Codice', 'Venditore Nome', 'Venditore Cognome', 'Stato', 'Prenotante Codice', 'Prenotante Nome', 'Prenotante Cognome', 'Acquirente Codice', 'Acquirente Nome', 'Acquirente Cognome', 'Data Creazione'],
            'fields' => ['isbn', 'title', 'condition', 'price', 'price_sell', 'leave', 'seller_code', 'seller_name', 'seller_surname', 'status', 'reserver_code', 'reserver_name', 'reserver_surname', 'buyer_code', 'buyer_name', 'buyer_surname', 'created_at'],
        ],
        'libri_disponibili' => [
            'label' => 'Libri disponibili',
            'query' => 'getAvailableListings',
            'columns' => ['ISBN', 'Titolo', 'Condizione', 'Prezzo acquisizione', 'Prezzo vendita', 'Lascia', 'Venditore Codice', 'Venditore Nome', 'Venditore Cognome', 'Data Creazione'],
            'fields' => ['isbn', 'title', 'condition', 'price', 'price_sell', 'leave', 'seller_code', 'seller_name', 'seller_surname', 'created_at'],
        ],
        'libri_prenotati' => [
            'label' => 'Libri prenotati',
            'query' => 'getReservedListings',
            'columns' => ['ISBN', 'Titolo', 'Condizione', 'Prezzo acquisizione', 'Prezzo vendita', 'Venditore Codice', 'Venditore Nome', 'Venditore Cognome', 'Prenotante Codice', 'Prenotante Nome', 'Prenotante Cognome', 'Data Creazione'],
            'fields' => ['isbn', 'title', 'condition', 'price', 'price_sell', 'seller_code', 'seller_name', 'seller_surname', 'reserver_code', 'reserver_name', 'reserver_surname', 'created_at'],
        ],
        'libri_venduti' => [
            'label' => 'Libri venduti',
            'query' => 'getSoldListings',
            'columns' => ['ISBN', 'Titolo', 'Condizione', 'Prezzo acquisizione', 'Prezzo vendita', 'Venditore Codice', 'Venditore Nome', 'Venditore Cognome', 'Acquirente Codice', 'Acquirente Nome', 'Acquirente Cognome', 'Data Vendita'],
            'fields' => ['isbn', 'title', 'condition', 'price', 'price_sell', 'seller_code', 'seller_name', 'seller_surname', 'buyer_code', 'buyer_name', 'buyer_surname', 'created_at'],
        ],
        'libri_riscossi' => [
            'label' => 'Libri riscossi',
            'query' => 'getWithdrawnListings',
            'columns' => ['ISBN', 'Titolo', 'Condizione', 'Prezzo acquisizione', 'Prezzo vendita', 'Venditore Codice', 'Venditore Nome', 'Venditore Cognome', 'Data Riscossione'],
            'fields' => ['isbn', 'title', 'condition', 'price', 'price_sell', 'seller_code', 'seller_name', 'seller_surname', 'created_at'],
        ],
        'libri_ritirati' => [
            'label' => 'Libri ritirati',
            'query' => 'getClaimedListings',
            'columns' => ['ISBN', 'Titolo', 'Condizione', 'Prezzo acquisizione', 'Prezzo vendita', 'Venditore Codice', 'Venditore Nome', 'Venditore Cognome', 'Data Ritiro'],
            'fields' => ['isbn', 'title', 'condition', 'price', 'price_sell', 'seller_code', 'seller_name', 'seller_surname', 'created_at'],
        ],
        'libri_ceduti' => [
            'label' => 'Libri ceduti',
            'query' => 'getArchivedListings',
            'columns' => ['ISBN', 'Titolo', 'Condizione', 'Prezzo acquisizione', 'Prezzo vendita', 'Venditore Codice', 'Venditore Nome', 'Venditore Cognome', 'Data Cessione'],
            'fields' => ['isbn', 'title', 'condition', 'price', 'price_sell', 'seller_code', 'seller_name', 'seller_surname', 'created_at'],
        ],
    ];

    /**
     * Ottiene la lista dei tipi esportabili
     */
    public static function getAvailableTypes(): array
    {
        return collect(self::EXPORT_TYPES)
            ->map(fn($config, $key) => [
                'key' => $key,
                'label' => $config['label'],
            ])
            ->values()
            ->toArray();
    }

    /**
     * Esporta i dati in CSV
     */
    public static function exportToCsv(string $type, int $schoolId): StreamedResponse
    {
        if (!isset(self::EXPORT_TYPES[$type])) {
            abort(404, 'Tipo esportazione non valido');
        }

        $config = self::EXPORT_TYPES[$type];
        $methodName = $config['query'];
        
        // Recupera i dati
        $data = self::$methodName($schoolId);
        
        // Genera il nome file
        $filename = $type . '_' . now()->format('Ymd_His') . '.csv';
        
        // Crea lo StreamedResponse
        return response()->streamDownload(function () use ($data, $config) {
            $handle = fopen('php://output', 'w');
            
            // Scrivi header
            fputcsv($handle, $config['columns']);
            
            // Scrivi dati
            foreach ($data as $row) {
                $values = [];
                foreach ($config['fields'] as $field) {
                    $values[] = $row[$field] ?? '';
                }
                fputcsv($handle, $values);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Recupera gli utenti della scuola
     */
    private static function getUsers(int $schoolId): Collection
    {
        return User::where('school_id', $schoolId)
            ->orderBy('surname')
            ->orderBy('name')
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'code' => $user->code,
                'role' => $user->role === 'studente' ? 'Studente' : ucfirst($user->role),
                'created_at' => $user->created_at?->format('d/m/Y H:i') ?? '',
            ]);
    }

    /**
     * Recupera i libri del catalogo
     */
    private static function getBooks(int $schoolId): Collection
    {
        return Book::where('school_id', $schoolId)
            ->orderBy('title')
            ->get()
            ->map(fn($book) => [
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'original_price' => number_format($book->original_price, 2, ',', '.'),
                'created_at' => $book->created_at?->format('d/m/Y H:i') ?? '',
            ]);
    }

    /**
     * Recupera TUTTI i libri acquisiti (book_listings con qualsiasi status)
     */
    private static function getAcquiredListings(int $schoolId): Collection
    {
        return BookListing::whereHas('book', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['book', 'seller'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($listing) {
                $reserver = null;
                $buyer = null;
                
                // Se prenotato, recupera il prenotante
                if ($listing->status === 'reserved') {
                    $reservation = BookReservation::where('book_listing_id', $listing->id)->first();
                    if ($reservation && $reservation->batch) {
                        $reserver = $reservation->batch->user;
                    }
                }
                
                // Se venduto O riscosso, recupera l'acquirente
                if ($listing->status === 'sold' || $listing->status === 'withdrawn') {
                    $sale = BookSale::where('book_listing_id', $listing->id)->first();
                    if ($sale && $sale->buyer) {
                        $buyer = $sale->buyer;
                    }
                }
                
                return [
                    'isbn' => $listing->book->isbn,
                    'title' => $listing->book->title,
                    'condition' => self::formatCondition($listing->condition),
                    'price' => number_format($listing->price, 2, ',', '.'),
                    'price_sell' => number_format($listing->price_sell, 2, ',', '.'),
                    'leave' => $listing->leave ? 'Sì' : 'No',
                    'seller_code' => $listing->seller->code,
                    'seller_name' => $listing->seller->name,
                    'seller_surname' => $listing->seller->surname,
                    'status' => self::formatStatus($listing->status),
                    'reserver_code' => $reserver?->code ?? '',
                    'reserver_name' => $reserver?->name ?? '',
                    'reserver_surname' => $reserver?->surname ?? '',
                    'buyer_code' => $buyer?->code ?? '',
                    'buyer_name' => $buyer?->name ?? '',
                    'buyer_surname' => $buyer?->surname ?? '',
                    'created_at' => $listing->created_at?->format('d/m/Y H:i') ?? '',
                ];
            });
    }


    /**
     * Recupera i libri prenotati (book_listings con status='reserved')
     */
    private static function getReservedListings(int $schoolId): Collection
    {
        return BookListing::where('status', 'reserved')
            ->whereHas('book', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['book', 'seller'])
            ->get()
            ->map(function ($listing) {
                // Recupera la prenotazione per questo listing
                $reservation = BookReservation::where('book_listing_id', $listing->id)->first();
                $reserver = null;
                
                if ($reservation && $reservation->batch) {
                    $reserver = $reservation->batch->user;
                }
                
                return [
                    'isbn' => $listing->book->isbn,
                    'title' => $listing->book->title,
                    'condition' => self::formatCondition($listing->condition),
                    'price' => number_format($listing->price, 2, ',', '.'),
                    'price_sell' => number_format($listing->price_sell, 2, ',', '.'),
                    'seller_code' => $listing->seller->code,
                    'seller_name' => $listing->seller->name,
                    'seller_surname' => $listing->seller->surname,
                    'reserver_code' => $reserver?->code ?? '',
                    'reserver_name' => $reserver?->name ?? '',
                    'reserver_surname' => $reserver?->surname ?? '',
                    'created_at' => $reservation?->created_at?->format('d/m/Y H:i') ?? '',
                ];
            });
    }

    private static function getAvailableListings(int $schoolId): Collection
    {
        return BookListing::where('status', 'available')
            ->whereHas('book', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['book', 'seller'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($listing) => [
                'isbn' => $listing->book->isbn,
                'title' => $listing->book->title,
                'condition' => self::formatCondition($listing->condition),
                'price' => number_format($listing->price, 2, ',', '.'),
                'price_sell' => number_format($listing->price_sell, 2, ',', '.'),
                'leave' => $listing->leave ? 'Sì' : 'No',
                'seller_code' => $listing->seller->code,
                'seller_name' => $listing->seller->name,
                'seller_surname' => $listing->seller->surname,
                'created_at' => $listing->created_at?->format('d/m/Y H:i') ?? '',
            ]);
    }

    /**
     * Recupera i libri venduti (book_listings con status='sold')
     */
    private static function getSoldListings(int $schoolId): Collection
    {
        return BookListing::where('status', 'sold')
            ->whereHas('book', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['book', 'seller'])
            ->get()
            ->map(function ($listing) {
                // Recupera la vendita per questo listing
                $sale = BookSale::where('book_listing_id', $listing->id)->first();
                $buyer = null;
                
                if ($sale && $sale->buyer) {
                    $buyer = $sale->buyer;
                }
                
                return [
                    'isbn' => $listing->book->isbn,
                    'title' => $listing->book->title,
                    'condition' => self::formatCondition($listing->condition),
                    'price' => number_format($listing->price, 2, ',', '.'),
                    'price_sell' => number_format($listing->price_sell, 2, ',', '.'),
                    'seller_code' => $listing->seller->code,
                    'seller_name' => $listing->seller->name,
                    'seller_surname' => $listing->seller->surname,
                    'buyer_code' => $buyer?->code ?? '',
                    'buyer_name' => $buyer?->name ?? '',
                    'buyer_surname' => $buyer?->surname ?? '',
                    'created_at' => $sale?->created_at?->format('d/m/Y H:i') ?? '',
                ];
            });
    }

    /**
     * Recupera i libri riscossi (book_listings con status='withdrawn')
     */
    private static function getWithdrawnListings(int $schoolId): Collection
    {
        return BookListing::where('status', 'withdrawn')
            ->whereHas('book', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['book', 'seller'])
            ->get()
            ->map(function ($listing) {
                // Recupera il withdrawal per questo listing
                $withdrawal = Withdrawal::where('book_listing_id', $listing->id)->first();
                $withdrawalUser = null;
                
                if ($withdrawal && $withdrawal->user) {
                    $withdrawalUser = $withdrawal->user;
                }
                
                return [
                    'isbn' => $listing->book->isbn,
                    'title' => $listing->book->title,
                    'condition' => self::formatCondition($listing->condition),
                    'price' => number_format($listing->price, 2, ',', '.'),
                    'price_sell' => number_format($listing->price_sell, 2, ',', '.'),
                    'seller_code' => $listing->seller->code,
                    'seller_name' => $listing->seller->name,
                    'seller_surname' => $listing->seller->surname,
                    'created_at' => $withdrawal?->created_at?->format('d/m/Y H:i') ?? '',
                ];
            });
    }

    /**
     * Recupera i libri ceduti (book_listings con status='archived')
     */
    private static function getArchivedListings(int $schoolId): Collection
    {
        return BookListing::where('status', 'archived')
            ->whereHas('book', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['book', 'seller'])
            ->get()
            ->map(function ($listing) {
                // Recupera il pickup per questo listing
                $pickup = Pickup::where('book_listing_id', $listing->id)->first();
                
                return [
                    'isbn' => $listing->book->isbn,
                    'title' => $listing->book->title,
                    'condition' => self::formatCondition($listing->condition),
                    'price' => number_format($listing->price, 2, ',', '.'),
                    'price_sell' => number_format($listing->price_sell, 2, ',', '.'),
                    'seller_code' => $listing->seller->code,
                    'seller_name' => $listing->seller->name,
                    'seller_surname' => $listing->seller->surname,
                    'created_at' => $pickup?->created_at?->format('d/m/Y H:i') ?? '',
                ];
            });
    }

    /**
     * Recupera i libri ritirati (book_listings con status='reclaim')
     */
    private static function getClaimedListings(int $schoolId): Collection
    {
        return BookListing::where('status', 'reclaim')
            ->whereHas('book', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['book', 'seller'])
            ->get()
            ->map(function ($listing) {
                // Recupera il pickup per questo listing
                $pickup = Pickup::where('book_listing_id', $listing->id)->first();
                
                return [
                    'isbn' => $listing->book->isbn,
                    'title' => $listing->book->title,
                    'condition' => self::formatCondition($listing->condition),
                    'price' => number_format($listing->price, 2, ',', '.'),
                    'price_sell' => number_format($listing->price_sell, 2, ',', '.'),
                    'seller_code' => $listing->seller->code,
                    'seller_name' => $listing->seller->name,
                    'seller_surname' => $listing->seller->surname,
                    'created_at' => $pickup?->created_at?->format('d/m/Y H:i') ?? '',
                ];
            });
    }

    /**
     * Formatta la condizione del libro in modo leggibile
     */
    private static function formatCondition(string $condition): string
    {
        $conditions = [
            'like-new' => 'Come nuovo',
            'good' => 'Buono',
            'fair' => 'Discreto',
            'poor' => 'Usato',
        ];
        return $conditions[$condition] ?? $condition;
    }

    /**
     * Formatta lo stato del libro in italiano
     */
    private static function formatStatus(string $status): string
    {
        $statuses = [
            'available' => 'Disponibile',
            'reserved' => 'Prenotato',
            'sold' => 'Venduto',
            'withdrawn' => 'Riscosso',
            'reclaim' => 'Ritirato',
            'archived' => 'Ceduto',
        ];
        return $statuses[$status] ?? $status;
    }
}
