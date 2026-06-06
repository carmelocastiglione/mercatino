<?php

namespace App\Models;

use App\Helpers\EAN13Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Acquisition extends Model
{
    protected $fillable = [
        'staff_id',
        'seller_id',
        'status',
        'total_price',
        'notes',
        'ean13',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($acquisition) {
            $acquisition->ean13 = EAN13Helper::generate();
        });
    }

    /**
     * Get the staff member who made this acquisition.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the seller from whom this acquisition was made.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the book listings in this acquisition.
     */
    public function bookListings(): HasMany
    {
        return $this->hasMany(BookListing::class);
    }
}
