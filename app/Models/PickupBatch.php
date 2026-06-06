<?php

namespace App\Models;

use App\Helpers\EAN13Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickupBatch extends Model
{
    protected $fillable = ['user_id', 'ean13'];
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pickups(): HasMany
    {
        return $this->hasMany(Pickup::class);
    }
}
