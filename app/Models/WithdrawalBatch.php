<?php

namespace App\Models;

use App\Helpers\EAN13Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WithdrawalBatch extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'notes',
        'ean13',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Boot method to generate EAN13.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $batch) {
            $batch->ean13 = EAN13Helper::generate();
        });
    }

    /**
     * Get the user (seller) that owns this withdrawal batch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the withdrawals in this batch.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
