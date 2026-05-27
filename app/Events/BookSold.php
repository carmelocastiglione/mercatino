<?php

namespace App\Events;

use App\Models\BookSale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookSold
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public BookSale $bookSale;

    /**
     * Create a new event instance.
     */
    public function __construct(BookSale $bookSale)
    {
        $this->bookSale = $bookSale;
    }
}
