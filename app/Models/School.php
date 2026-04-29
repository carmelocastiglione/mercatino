<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name',
        'description',
        'city',
        'address',
        'phone',
        'email',
    ];

    /**
     * Get the users (students and staff) for the school.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
