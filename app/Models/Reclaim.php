<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Builder;

class Reclaim extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'buyer_id',
        'book_listing_id',
        'notes',
        'status',
        'rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to filter reclaims by school.
     */
    public function scopeBySchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->whereHas('bookListing', fn($q) => $q->bySchool($schoolId));
    }

    /**
     * Get the user (seller) who had the book reclaimed.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book listing that was reclaimed.
     */
    public function bookListing(): BelongsTo
    {
        return $this->belongsTo(BookListing::class);
    }

    /**
     * Get the buyer (customer who purchased the book).
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the book sale associated with this reclaim.
     */
    public function bookSale(): BelongsTo
    {
        return $this->belongsTo(BookSale::class);
    }
}
