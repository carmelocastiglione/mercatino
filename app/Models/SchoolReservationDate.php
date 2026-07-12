<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class SchoolReservationDate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'scheduled_date',
        'label',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the school this reservation date belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the reservation batches for this date.
     */
    public function reservationBatches(): HasMany
    {
        return $this->hasMany(BookReservationBatch::class, 'scheduled_reservation_date_id');
    }

    /**
     * Scope to filter only active dates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter only future dates.
     */
    public function scopeFuture(Builder $query): Builder
    {
        return $query->where('scheduled_date', '>', now());
    }

    /**
     * Scope to filter by school.
     */
    public function scopeBySchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }
}
