<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class BookDelivery extends Model
{
    /**
     * The attributes that are mass assignable.
     * Possible values for 'status': 'pending', 'approved', 'rejected'
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'condition',
        'price',
        'status', 
        'rejection_reason',
        'approved_by',
        'leave',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Scope to filter deliveries by school.
     */
    public function scopeBySchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->whereHas('book', fn($q) => $q->bySchool($schoolId));
    }

    /**
     * Get the student who requested the delivery.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book being delivered.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the staff member who approved the delivery.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
