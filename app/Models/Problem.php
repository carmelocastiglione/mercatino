<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Problem extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'status',
    ];

    /**
     * Get the user that reported the problem.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
