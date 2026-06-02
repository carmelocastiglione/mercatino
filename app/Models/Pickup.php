<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pickup extends Model
{
    protected $fillable = ['user_id', 'book_listing_id', 'pickup_batch_id', 'notes', 'leave'];

    protected $casts = [
        'leave' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookListing(): BelongsTo
    {
        return $this->belongsTo(BookListing::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PickupBatch::class, 'pickup_batch_id');
    }
}
