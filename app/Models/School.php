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

    /**
     * Get the delivery dates configured for the school.
     */
    public function deliveryDates(): HasMany
    {
        return $this->hasMany(SchoolDeliveryDate::class);
    }

    /**
     * Get the withdraw dates configured for the school.
     */
    public function withdrawDates(): HasMany
    {
        return $this->hasMany(SchoolWithdrawDate::class);
    }

    /**
     * Get the settings for the school.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(SchoolSetting::class);
    }

    /**
     * Get a specific setting value
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->settings()->where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    /**
     * Set a specific setting value
     */
    public function setSetting(string $key, $value): SchoolSetting
    {
        return $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Check if a setting exists and is truthy
     */
    public function hasFeatureEnabled(string $key): bool
    {
        $value = $this->getSetting($key, false);
        return (bool) $value && $value !== 'false' && $value !== '0';
    }

    /**
     * Delete a specific setting
     */
    public function deleteSetting(string $key): bool
    {
        return (bool) $this->settings()->where('key', $key)->delete();
    }
}
