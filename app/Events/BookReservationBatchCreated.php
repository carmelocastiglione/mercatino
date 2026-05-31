<?php

namespace App\Events;

use App\Models\BookReservationBatch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookReservationBatchCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public BookReservationBatch $batch)
    {
    }
}
