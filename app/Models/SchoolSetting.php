<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = ['school_id', 'key', 'value'];

    /**
     * Get the school that owns this setting
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}

