<?php

namespace App\Services;

use App\Models\BookListing;
use App\Models\BookSale;
use App\Notifications\BookReservationCancelledNotification;
use App\Notifications\BookReservationRejectedNotification;
use App\Notifications\BookReservedNotification;
use App\Notifications\BookSoldNotification;

class NotificationService
{
    /**
     * Notify seller when a book is reserved
     */
    public function notifyBookReserved(BookListing $bookListing): void
    {
        $seller = $bookListing->seller;
        if ($seller) {
            $seller->notify(new BookReservedNotification($bookListing));
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

    /**
     * Notify seller when a book reservation is cancelled
     */
    public function notifyBookReservationCancelled(BookListing $bookListing): void
    {
        $seller = $bookListing->seller;
        if ($seller) {
            $seller->notify(new BookReservationCancelledNotification($bookListing));
        }
    }

    /**
     * Notify seller when a book reservation is rejected
     */
    public function notifyBookReservationRejected(BookListing $bookListing): void
    {
        $seller = $bookListing->seller;
        if ($seller) {
            $seller->notify(new BookReservationRejectedNotification($bookListing));
        }
    }
}