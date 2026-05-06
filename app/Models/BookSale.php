<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookSale extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'book_listing_id',
        'sold_by',
        'buyer_id',
        'notes',
    ];

    /**
     * Get the book listing that was sold.
     */
    public function bookListing(): BelongsTo
    {
        return $this->belongsTo(BookListing::class);
    }

    /**
     * Get the staff member who sold the book.
     */
    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    /**
     * Get the buyer who purchased the book.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
