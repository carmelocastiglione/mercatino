<?php

namespace App\Notifications;

use App\Models\BookReservationBatch;
use Illuminate\Notifications\Notification;

class BookReservationConfirmedNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected BookReservationBatch $batch,
        protected array $books,
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
            'count' => $this->batch->total_items,
            'books' => $this->books,
            'title' => 'Prenotazione Confermata',
            'description' => sprintf(
                'La tua prenotazione di %d libro/i è stata confermata: %s',
                $this->batch->total_items,
                implode(', ', $this->books)
            ),
        ];
    }
}
