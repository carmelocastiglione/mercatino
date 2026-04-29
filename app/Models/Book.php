<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = [
        'seller_id',
        'title',
        'author',
        'isbn',
        'description',
        'subject',
        'school_class',
        'condition',
        'price',
        'original_price',
        'status',
        'images',
        'cover_image',
        'views',
        'favorites',
    ];

    protected $casts = [
        'images' => 'json',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the seller of the book.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
