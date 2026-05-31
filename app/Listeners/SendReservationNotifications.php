<?php

namespace App\Listeners;

use App\Events\BookReservationBatchCreated;
use App\Events\BookReservationBatchConfirmed;
use App\Events\BookReservationBatchRejected;
use App\Events\BookReservationBatchCancelled;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class SendReservationNotifications
{
    /**
     * Handle booking batch created event.
     */
    public function onBatchCreated(BookReservationBatchCreated $event): void
    {
        try {
            $batch = $event->batch;
            
            $batch->load(['bookReservations' => function ($q) {
                $q->with(['bookListing' => function ($q) {
                    $q->with('book');
                }]);
            }, 'user']);

            // Create notifications for book sellers
            $sellerIds = $batch->bookReservations
                ->pluck('bookListing.seller_id')
                ->filter()
                ->unique();

            foreach ($sellerIds as $sellerId) {
                $sellerReservations = $batch->bookReservations
                    ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

                $sellerBooks = $sellerReservations->count();
                $books = $sellerReservations
                    ->pluck('bookListing.book.title')
                    ->toArray();

                Notification::create([
                    'user_id' => $sellerId,
                    'type' => 'book_reservation_created',
                    'data' => [
                        'batch_id' => $batch->id,
                        'student_name' => $batch->user->name . ' ' . $batch->user->surname,
                        'books' => $books,
                        'count' => $sellerBooks,
                    ],
                    'title' => 'Libro Prenotato',
                    'description' => sprintf(
                        'Uno studente ha prenotato %d libro/i da te: %s',
                        $sellerBooks,
                        implode(', ', $books)
                    ),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in SendReservationNotifications::onBatchCreated', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle booking batch confirmed event.
     */
    public function onBatchConfirmed(BookReservationBatchConfirmed $event): void
    {
        $batch = $event->batch;
        $batch->load(['bookReservations' => function ($q) {
            $q->with(['bookListing' => function ($q) {
                $q->with('book');
            }]);
        }, 'user']);

        // Create notification for student
        $booksList = $batch->bookReservations
            ->pluck('bookListing.book.title')
            ->toArray();

        Notification::create([
            'user_id' => $batch->user_id,
            'type' => 'reservation_confirmed',
            'data' => [
                'batch_id' => $batch->id,
                'count' => $batch->total_items,
                'books' => $booksList,
            ],
            'title' => 'Prenotazione Confermata',
            'description' => sprintf(
                'La tua prenotazione di %d libro/i è stata confermata: %s',
                $batch->total_items,
                implode(', ', $booksList)
            ),
        ]);

        // Create notifications for book sellers
        $sellerIds = $batch->bookReservations
            ->pluck('bookListing.seller_id')
            ->filter()
            ->unique();

        foreach ($sellerIds as $sellerId) {
            // Get seller's books from this batch
            $sellerReservations = $batch->bookReservations
                ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

            $sellerBooks = $sellerReservations->count();

            $books = $sellerReservations
                ->pluck('bookListing')
                ->map(fn ($listing) => [
                    'title' => $listing->book->title,
                    'price' => $listing->price,
                ])
                ->toArray();

            $bookTitles = $sellerReservations
                ->pluck('bookListing.book.title')
                ->toArray();

            Notification::create([
                'user_id' => $sellerId,
                'type' => 'book_sold',
                'data' => [
                    'batch_id' => $batch->id,
                    'books' => $books,
                    'count' => $sellerBooks,
                    'total_price' => collect($books)->sum('price'),
                ],
                'title' => 'Libro Venduto',
                'description' => sprintf(
                    '%d libro/i è stato/i venduto/i: %s',
                    $sellerBooks,
                    implode(', ', $bookTitles)
                ),
            ]);
        }
    }

    /**
     * Handle booking batch rejected event.
     */
    public function onBatchRejected(BookReservationBatchRejected $event): void
    {
        $batch = $event->batch;
        $batch->load(['bookReservations' => function ($q) {
            $q->with(['bookListing' => function ($q) {
                $q->with('book');
            }]);
        }, 'user']);

        // Create notification for student
        $studentBooks = $batch->bookReservations
            ->pluck('bookListing.book.title')
            ->toArray();

        Notification::create([
            'user_id' => $batch->user_id,
            'type' => 'reservation_rejected',
            'data' => [
                'batch_id' => $batch->id,
                'count' => $batch->total_items,
                'books' => $studentBooks,
            ],
            'title' => 'Prenotazione Rifiutata',
            'description' => sprintf(
                'La tua prenotazione di %d libro/i è stata rifiutata. I libri sono ora disponibili per altri studenti: %s',
                $batch->total_items,
                implode(', ', $studentBooks)
            ),
        ]);

        // Create notifications for book sellers
        $sellerIds = $batch->bookReservations
            ->pluck('bookListing.seller_id')
            ->filter()
            ->unique();

        foreach ($sellerIds as $sellerId) {
            // Get seller's books from this batch
            $sellerReservations = $batch->bookReservations
                ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

            $sellerBooks = $sellerReservations->count();

            $books = $sellerReservations
                ->pluck('bookListing.book.title')
                ->toArray();

            Notification::create([
                'user_id' => $sellerId,
                'type' => 'reservation_rejected_seller',
                'data' => [
                    'batch_id' => $batch->id,
                    'books' => $books,
                    'count' => $sellerBooks,
                ],
                'title' => 'Prenotazione Rifiutata',
                'description' => sprintf(
                    'La prenotazione di %d libro/i è stata rifiutata. I libri sono di nuovo disponibili: %s',
                    $sellerBooks,
                    implode(', ', $books)
                ),
            ]);
        }
    }

    /**
     * Handle booking batch cancelled event.
     */
    public function onBatchCancelled(BookReservationBatchCancelled $event): void
    {
        $batch = $event->batch;
        $batch->load(['bookReservations' => function ($q) {
            $q->with(['bookListing' => function ($q) {
                $q->with('book');
            }]);
        }, 'user']);

        // Create notifications for book sellers
        $sellerIds = $batch->bookReservations
            ->pluck('bookListing.seller_id')
            ->filter()
            ->unique();

        foreach ($sellerIds as $sellerId) {
            // Get seller's books from this batch
            $sellerReservations = $batch->bookReservations
                ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

            $sellerBooks = $sellerReservations->count();

            $books = $sellerReservations
                ->pluck('bookListing.book.title')
                ->toArray();

            Notification::create([
                'user_id' => $sellerId,
                'type' => 'reservation_cancelled_seller',
                'data' => [
                    'batch_id' => $batch->id,
                    'books' => $books,
                    'count' => $sellerBooks,
                ],
                'title' => 'Prenotazione Cancellata',
                'description' => sprintf(
                    'Una prenotazione di %d libro/i è stata cancellata. I libri sono di nuovo disponibili: %s',
                    $sellerBooks,
                    implode(', ', $books)
                ),
            ]);
        }
    }
}
