<?php

namespace App\Notifications;

use App\Models\BookSale;
use Illuminate\Notifications\Notification;

class BookSaleCancelledNotification extends Notification
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
            'title' => 'Vendita annullata',
            'description' => "La vendita del tuo libro \"{$book->title}\" è stata annullata. Il libro torna disponibile nel catalogo.",
        ];
    }
}
