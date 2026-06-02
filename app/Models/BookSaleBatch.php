<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class BookSaleBatch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'created_by',
        'buyer_id',
        'total_price',
        'notes',
    ];

    /**
     * Scope to filter batches by school.
     */
    public function scopeBySchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Get the school that owns the batch.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the user who created the batch.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the buyer of this batch.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get all sales in this batch.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(BookSale::class);
    }

    /**
     * Get the total revenue from this batch (calculated from sales with price_sell).
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->sales->sum(fn($sale) => $sale->bookListing->price_sell ?? $sale->bookListing->price);
    }
}

