<?php

namespace App\Notifications;

use App\Models\BookSale;
use Illuminate\Notifications\Notification;

class BookSoldNotification extends Notification
{
    /**
     * The book sale instance.
     */
    protected BookSale $bookSale;

    /**
     * Create a new notification instance.
     */
    public function __construct(BookSale $bookSale)
    {
        $this->bookSale = $bookSale;
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
        $bookListing = $this->bookSale->bookListing;
        $book = $bookListing->book;

        return [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'book_author' => $book->author,
            'price_sell' => $bookListing->price_sell,
            'sale_id' => $this->bookSale->id,
            'title' => 'Libro venduto',
            'description' => "Il tuo libro \"{$book->title}\" ({$book->isbn}) è stato venduto a €" . number_format($bookListing->price_sell, 2),
        ];
    }
}
