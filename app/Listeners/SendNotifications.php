<?php

namespace App\Listeners;

use App\Events\BookSold;
use App\Models\Notification;

class SendNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookSold $event): void
    {
        $bookSale = $event->bookSale;
        $bookListing = $bookSale->bookListing;
        $book = $bookListing->book;
        $seller = $bookListing->seller;

        // Create notification for the seller (book owner)
        Notification::create([
            'user_id' => $seller->id,
            'type' => 'book_sold',
            'data' => [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'book_author' => $book->author,
                'price' => $bookListing->price,
                'sale_id' => $bookSale->id,
            ],
            'title' => 'Libro venduto',
            'description' => "Il tuo libro \"{$book->title}\" di {$book->author} è stato venduto a €" . number_format($bookListing->price, 2),
        ]);
    }
}
