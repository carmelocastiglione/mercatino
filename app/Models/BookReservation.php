<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class BookReservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'book_reservation_batch_id',
        'book_listing_id',
        'status',
        'notes',
        'reserved_at',
        'confirmed_at',
        'rejected_at',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the batch this reservation belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(BookReservationBatch::class, 'book_reservation_batch_id');
    }

    /**
     * Get the book listing being reserved.
     */
    public function bookListing(): BelongsTo
    {
        return $this->belongsTo(BookListing::class);
    }

    /**
     * Get the book sale if this reservation was confirmed.
     */
    public function bookSale(): HasOne
    {
        return $this->hasOne(BookSale::class, 'book_listing_id', 'book_listing_id');
    }

    /**
     * Scope to filter reservations by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending reservations.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter confirmed reservations.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Check if reservation is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if reservation is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if reservation is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
