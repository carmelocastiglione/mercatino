<?php

namespace App\Notifications;

use App\Models\BookReservationBatch;
use Illuminate\Notifications\Notification;

class BookSoldNotificationForSeller extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected BookReservationBatch $batch,
        protected array $books,
        protected int $count,
        protected float $totalPrice,
    ) {}

    /**
     * Get the notification's delivery channels.
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
        return [
            'batch_id' => $this->batch->id,
            'books' => $this->books,
            'count' => $this->count,
            'total_price' => $this->totalPrice,
            'title' => 'Libro Venduto',
            'description' => sprintf(
                '%d libro/i è stato/i venduto/i: %s',
                $this->count,
                implode(', ', array_column($this->books, 'title'))
            ),
        ];
    }
}
