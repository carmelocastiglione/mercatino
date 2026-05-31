<?php

namespace App\Listeners;

use App\Events\BookSold;
use App\Notifications\BookSoldNotification;

class SendNotifications
{
    /**
     * Handle the event.
     */
    public function handle(BookSold $event): void
    {
        $bookSale = $event->bookSale;
        $seller = $bookSale->bookListing->seller;

        // Send notification to seller using Laravel's notification system
        $seller->notify(new BookSoldNotification($bookSale));
    }
}
