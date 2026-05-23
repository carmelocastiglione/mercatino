<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
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
     * Scope to filter books by school.
     */
    public function scopeBySchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Get the school this book belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

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
