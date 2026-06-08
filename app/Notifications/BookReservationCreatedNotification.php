<?php

namespace App\Notifications;

use App\Models\BookReservationBatch;
use Illuminate\Notifications\Notification;

class BookReservationCreatedNotification extends Notification
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
            'student_name' => $this->batch->user->name . ' ' . $this->batch->user->surname,
            'books' => $this->books,
            'count' => $this->count,
            'title' => 'Libro Prenotato',
            'description' => sprintf(
                'Uno studente ha prenotato %d libro/i da te: %s',
                $this->count,
                implode(', ', $this->books)
            ),
        ];
    }
}
