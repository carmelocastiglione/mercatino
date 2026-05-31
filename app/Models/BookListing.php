<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class BookListing extends Model
{
    use SoftDeletes;

    /**
    * The attributes that are mass assignable.
    * Possible values for 'status': 'available', 'reserved', 'sold', 'withdrawn', 'reclaim', 'archived'
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'book_id',
        'seller_id',
        'buyer_id',
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
     * Scope to filter listings by school.
     */
    public function scopeBySchool(Builder $query, ?int $schoolId): Builder
    {
        return $query->whereHas('book', fn($q) => $q->bySchool($schoolId));
    }

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

    /**
     * Get the sales records for this listing.
     */
    public function bookSales(): HasMany
    {
        return $this->hasMany(BookSale::class);
    }

    /**
     * Get the withdrawal record if this book was sold and money was withdrawn.
     */
    public function withdrawal(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the reclaim record if this book was reclaimed.
     */
    public function reclaim(): HasMany
    {
        return $this->hasMany(Reclaim::class);
    }

    /**
     * Get the book reservations for this listing.
     */
    public function bookReservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }
}
