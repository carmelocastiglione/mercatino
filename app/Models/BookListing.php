<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'book_id',
        'seller_id',
        'acquisition_id',
        'condition',
        'price',
        'status',
        'images',
        'views',
        'favorites',
        'leave',
    ];

    protected $casts = [
        'images' => 'json',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the book in the catalog.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the seller of this listing.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the acquisition this listing belongs to.
     */
    public function acquisition(): BelongsTo
    {
        return $this->belongsTo(Acquisition::class);
    }
}
