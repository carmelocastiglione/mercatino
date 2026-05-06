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
                $user->code = self::generateCode($user->name, $user->surname);
            }
        });
    }

    /**
     * Generate a unique code from name and surname.
     * Format: First letter of name + First letter of surname + . + 4 digit number
     * Example: JD.0001, MR.0002
     */
    public static function generateCode(string $name, string $surname): string
    {
        $initials = strtoupper(substr($name, 0, 1) . substr($surname, 0, 1));
        
        // Get the next sequential number for this initials combination
        $count = self::where('code', 'like', "$initials.%")->count();
        $nextNumber = $count + 1;
        
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
     * Get the withdrawals for this user.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get total amount from sales of books sold by this user.
     */
    public function getTotalSalesAmount(): float
    {
        return (float) BookSale::where('sold_by', $this->id)
            ->join('book_listings', 'book_sales.book_listing_id', '=', 'book_listings.id')
            ->sum('book_listings.price');
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
}
