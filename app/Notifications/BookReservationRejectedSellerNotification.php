<?php

namespace App\Notifications;

use App\Models\BookReservationBatch;
use Illuminate\Notifications\Notification;

class BookReservationRejectedSellerNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected BookReservationBatch $batch,
        protected array $books,
        protected int $count,
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
            'title' => 'Prenotazione Rifiutata',
            'description' => sprintf(
                'La prenotazione di %d libro/i è stata rifiutata. I libri sono di nuovo disponibili: %s',
                $this->count,
                implode(', ', $this->books)
            ),
        ];
    }
}
