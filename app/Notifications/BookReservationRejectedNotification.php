<?php

namespace App\Notifications;

use App\Models\BookListing;
use Illuminate\Notifications\Notification;

class BookReservationRejectedNotification extends Notification
{
    /**
     * The book listing instance.
     */
    protected BookListing $bookListing;

    /**
     * Create a new notification instance.
     */
    public function __construct(BookListing $bookListing)
    {
        $this->bookListing = $bookListing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $book = $this->bookListing->book;

        return [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'book_author' => $book->author,
            'isbn' => $book->isbn,
            'book_listing_id' => $this->bookListing->id,
            'price_sell' => $this->bookListing->price_sell,
            'title' => 'Prenotazione rifiutata',
            'description' => "La prenotazione del tuo libro \"{$book->title}\" ({$book->isbn}) è stata rifiutata",
        ];
    }
}
