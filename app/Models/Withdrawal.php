<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Withdrawal extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'book_listing_id',
        'withdrawal_batch_id',
        'amount',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to filter withdrawals by school.
     */
    public function scopeBySchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->whereHas('bookListing', fn($q) => $q->bySchool($schoolId));
    }

    /**
     * Get the user who made the withdrawal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book listing associated with this withdrawal.
     */
    public function bookListing(): BelongsTo
    {
        return $this->belongsTo(BookListing::class);
    }

    /**
     * Get the withdrawal batch (if this withdrawal is part of a batch).
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(WithdrawalBatch::class, 'withdrawal_batch_id');
    }
}
