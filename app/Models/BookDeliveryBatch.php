<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookDeliveryBatch extends Model
{
    /**
     * The attributes that are mass assignable.
     * Possible values for 'status': 'pending', 'submitted', 'approved', 'rejected', 'delivered'
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'school_id',
        'status',
        'delivered_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'delivered_date' => 'datetime',
    ];

    /**
     * Get the student who created this batch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the school this batch belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all deliveries in this batch.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(BookDelivery::class, 'batch_id');
    }

    /**
     * Get total number of books in this batch.
     */
    public function getTotalBooksAttribute(): int
    {
        return $this->deliveries()->count();
    }

    /**
     * Get total price of all books in this batch.
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->deliveries()->sum('price');
    }

    /**
     * Get count of approved deliveries in this batch.
     */
    public function getApprovedCountAttribute(): int
    {
        return $this->deliveries()->where('status', 'approved')->count();
    }
}

