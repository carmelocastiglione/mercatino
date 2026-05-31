<?php

namespace App\Notifications;

use App\Models\BookSale;
use App\Models\Notification;
use Illuminate\Notifications\Notification as BaseNotification;

class BookSoldNotification extends BaseNotification
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
        return ['custom'];
    }

    /**
     * Get the custom representation of the notification.
     */
    public function toCustom(object $notifiable): void
    {
        $bookListing = $this->bookSale->bookListing;
        $book = $bookListing->book;

        Notification::create([
            'user_id' => $notifiable->id,
            'type' => 'book_sold',
            'data' => [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'book_author' => $book->author,
                'price' => $bookListing->price,
                'sale_id' => $this->bookSale->id,
            ],
            'title' => 'Libro venduto',
            'description' => "Il tuo libro \"{$book->title}\" di {$book->author} è stato venduto a €" . number_format($bookListing->price, 2),
        ]);
    }
}
