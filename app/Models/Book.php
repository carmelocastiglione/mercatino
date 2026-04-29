<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'subject',
        'school_class',
        'original_price',
        'cover_image',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all listings for this book.
     */
    public function listings(): HasMany
    {
        return $this->hasMany(BookListing::class);
    }
    public function bookDeliveries(): HasMany
    {
        return $this->hasMany(BookDelivery::class);
    }
    /**
     * Get available listings for this book.
     */
    public function availableListings()
    {
        return $this->listings()->where('status', 'available');
    }
}
