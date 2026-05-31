<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class BookReservationBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id',
        'status',
        'total_items',
        'notes',
        'reserved_at',
        'confirmed_at',
        'rejected_at',
        'cancelled_at',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the student who made this reservation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the school this reservation belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the book reservations in this batch.
     */
    public function bookReservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }

    /**
     * Get the book listings through reservations.
     */
    public function bookListings()
    {
        return $this->hasManyThrough(
            BookListing::class,
            BookReservation::class,
            'book_reservation_batch_id', // Foreign key on BookReservation to this model
            'id',                        // Local key on BookReservation
            'id',                        // Local key on this model
            'book_listing_id'            // Foreign key on BookReservation to BookListing
        );
    }

    /**
     * Scope to filter batches by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending batches.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter batches by school.
     */
    public function scopeBySchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Check if batch is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if batch is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if batch is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if batch is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get total price of all books in batch.
     */
    public function getTotalPrice(): float
    {
        return $this->bookReservations()
            ->join('book_listings', 'book_reservations.book_listing_id', '=', 'book_listings.id')
            ->sum('book_listings.price');
    }
}
