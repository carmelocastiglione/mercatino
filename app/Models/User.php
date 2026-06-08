<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'surname', 'email', 'password', 'role', 'school_id', 'code', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->code) {
                $user->code = self::generateCode($user->name, $user->surname, $user->school_id);
            }
        });
    }

    /**
     * Generate a unique code from name and surname, unique per school.
     * Format: First letter of name + First letter of surname + . + 4 digit number
     * Example: JD.0001, MR.0002
     * The code is unique within each school.
     */
    public static function generateCode(string $name, string $surname, ?int $schoolId = null): string
    {
        $initials = strtoupper(substr($name, 0, 1) . substr($surname, 0, 1));
        
        // Get the highest number assigned for this initials combination within the school
        // This prevents collisions if users are deleted (gaps won't cause duplicate codes)
        $query = self::where('code', 'like', "$initials.%");
        
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        
        // Extract the maximum number from existing codes
        $maxNumber = $query->pluck('code')
            ->map(fn($code) => (int) substr($code, strlen($initials) + 1))
            ->max() ?? 0;
        
        $nextNumber = $maxNumber + 1;
        
        return $initials . '.' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the school that the user belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the book listings from this user (seller).
     */
    public function bookListings(): HasMany
    {
        return $this->hasMany(BookListing::class, 'seller_id');
    }

    public function bookDeliveries(): HasMany
    {
        return $this->hasMany(BookDelivery::class);
    }

    public function deliveryBatches(): HasMany
    {
        return $this->hasMany(BookDeliveryBatch::class);
    }

    public function approvedDeliveries(): HasMany
    {
        return $this->hasMany(BookDelivery::class, 'approved_by');
    }

    /**
     * Get the sales made by this user (seller).
     */
    public function sales(): HasMany
    {
        return $this->hasMany(BookSale::class, 'sold_by');
    }

    /**
     * Get the books purchased by this user (buyer).
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(BookSale::class, 'buyer_id');
    }

    /**
     * Get the withdrawals for this user.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the withdrawal batches for this user.
     */
    public function withdrawalBatches(): HasMany
    {
        return $this->hasMany(WithdrawalBatch::class);
    }

    /**
     * Get the pickups for this user.
     */
    public function pickups(): HasMany
    {
        return $this->hasMany(Pickup::class);
    }

    /**
     * Get the pickup batches for this user.
     */
    public function pickupBatches(): HasMany
    {
        return $this->hasMany(PickupBatch::class);
    }

    /**
     * Get the reclaimed books for this user.
     */
    public function reclaims(): HasMany
    {
        return $this->hasMany(Reclaim::class);
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Get total amount from sales of books sold by this user (seller).
     */
    public function getTotalSalesAmount(): float
    {
        return (float) BookListing::where('seller_id', $this->id)
            ->whereIn('status', ['sold', 'withdrawn'])
            ->sum('price');
    }

    /**
     * Get total amount already withdrawn by this user.
     */
    public function getTotalWithdrawnAmount(): float
    {
        return (float) $this->withdrawals()->sum('amount');
    }

    /**
     * Get the available balance (total sales - total withdrawn).
     */
    public function getAvailableBalance(): float
    {
        return $this->getTotalSalesAmount() - $this->getTotalWithdrawnAmount();
    }

    /**
     * Get the book reservation batches for this user (student).
     */
    public function bookReservationBatches(): HasMany
    {
        return $this->hasMany(BookReservationBatch::class);
    }
}
