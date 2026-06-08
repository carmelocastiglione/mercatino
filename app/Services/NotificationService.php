<?php

namespace App\Services;

use App\Models\BookReservationBatch;
use App\Models\BookSale;
use App\Models\User;
use App\Notifications\BookReservationCancelledNotification;
use App\Notifications\BookReservationConfirmedNotification;
use App\Notifications\BookReservationCreatedNotification;
use App\Notifications\BookReservationRejectedNotification;
use App\Notifications\BookReservationRejectedSellerNotification;
use App\Notifications\BookSoldNotification;
use App\Notifications\BookSoldNotificationForSeller;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify sellers when a reservation batch is created
     */
    public function notifyBatchCreated(BookReservationBatch $batch): void
    {
        try {
            $batch->load(['bookReservations' => function ($q) {
                $q->with(['bookListing' => function ($q) {
                    $q->with('book');
                }]);
            }, 'user']);

            // Notify book sellers
            $sellerIds = $batch->bookReservations
                ->pluck('bookListing.seller_id')
                ->filter()
                ->unique();

            foreach ($sellerIds as $sellerId) {
                $seller = User::find($sellerId);
                if (!$seller) continue;

                $sellerReservations = $batch->bookReservations
                    ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

                $books = $sellerReservations
                    ->pluck('bookListing.book.title')
                    ->toArray();

                $seller->notify(new BookReservationCreatedNotification(
                    batch: $batch,
                    books: $books,
                    count: $sellerReservations->count(),
                ));
            }
        } catch (\Exception $e) {
            Log::error('Error in NotificationService::notifyBatchCreated', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Notify student and sellers when a reservation batch is confirmed
     */
    public function notifyBatchConfirmed(BookReservationBatch $batch): void
    {
        $batch->load(['bookReservations' => function ($q) {
            $q->with(['bookListing' => function ($q) {
                $q->with('book');
            }]);
        }, 'user']);

        // Notify student
        $booksList = $batch->bookReservations
            ->pluck('bookListing.book.title')
            ->toArray();

        $batch->user->notify(new BookReservationConfirmedNotification(
            batch: $batch,
            books: $booksList,
        ));

        // Notify sellers that books are sold
        $sellerIds = $batch->bookReservations
            ->pluck('bookListing.seller_id')
            ->filter()
            ->unique();

        foreach ($sellerIds as $sellerId) {
            $seller = User::find($sellerId);
            if (!$seller) continue;

            $sellerReservations = $batch->bookReservations
                ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

            $books = $sellerReservations
                ->pluck('bookListing')
                ->map(fn ($listing) => [
                    'title' => $listing->book->title,
                    'price' => $listing->price,
                ])
                ->toArray();

            $seller->notify(new BookSoldNotificationForSeller(
                batch: $batch,
                books: $books,
                count: $sellerReservations->count(),
                totalPrice: collect($books)->sum('price'),
            ));
        }
    }

    /**
     * Notify student and sellers when a reservation batch is rejected
     */
    public function notifyBatchRejected(BookReservationBatch $batch): void
    {
        $batch->load(['bookReservations' => function ($q) {
            $q->with(['bookListing' => function ($q) {
                $q->with('book');
            }]);
        }, 'user']);

        // Notify student
        $studentBooks = $batch->bookReservations
            ->pluck('bookListing.book.title')
            ->toArray();

        $batch->user->notify(new BookReservationRejectedNotification(
            batch: $batch,
            books: $studentBooks,
        ));

        // Notify sellers
        $sellerIds = $batch->bookReservations
            ->pluck('bookListing.seller_id')
            ->filter()
            ->unique();

        foreach ($sellerIds as $sellerId) {
            $seller = User::find($sellerId);
            if (!$seller) continue;

            $sellerReservations = $batch->bookReservations
                ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

            $books = $sellerReservations
                ->pluck('bookListing.book.title')
                ->toArray();

            $seller->notify(new BookReservationRejectedSellerNotification(
                batch: $batch,
                books: $books,
                count: $sellerReservations->count(),
            ));
        }
    }

    /**
     * Notify sellers when a reservation batch is cancelled
     */
    public function notifyBatchCancelled(BookReservationBatch $batch): void
    {
        $batch->load(['bookReservations' => function ($q) {
            $q->with(['bookListing' => function ($q) {
                $q->with('book');
            }]);
        }, 'user']);

        // Notify sellers
        $sellerIds = $batch->bookReservations
            ->pluck('bookListing.seller_id')
            ->filter()
            ->unique();

        foreach ($sellerIds as $sellerId) {
            $seller = User::find($sellerId);
            if (!$seller) continue;

            $sellerReservations = $batch->bookReservations
                ->filter(fn ($r) => $r->bookListing->seller_id === $sellerId);

            $books = $sellerReservations
                ->pluck('bookListing.book.title')
                ->toArray();

            $seller->notify(new BookReservationCancelledNotification(
                batch: $batch,
                books: $books,
                count: $sellerReservations->count(),
            ));
        }
    }

    /**
     * Notify seller when a book is sold
     */
    public function notifyBookSold(BookSale $bookSale): void
    {
        $seller = $bookSale->bookListing->seller;
        $seller->notify(new BookSoldNotification($bookSale));
    }
}
