<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'daily_price',
        'status',
        'image',
    ];

    protected $casts = [
        'daily_price' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate unique code when creating
        static::creating(function ($item) {
            if (empty($item->code)) {
                $item->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique item code
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'ITM-' . strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Relationship: Rentals (through pivot table)
     */
    public function rentals()
    {
        return $this->belongsToMany(Rental::class, 'rental_items')
            ->withPivot('daily_price')
            ->withTimestamps();
    }

    /**
     * Get active rental for this item
     */
    public function activeRental()
    {
        return $this->rentals()->where('status', 'active')->first();
    }

    /**
     * Scope: Available items only
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope: Rented items only
     */
    public function scopeRented($query)
    {
        return $query->where('status', 'rented');
    }

    /**
     * Check if item is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Mark item as rented
     */
    public function markAsRented(): void
    {
        $this->update(['status' => 'rented']);
    }

    /**
     * Mark item as available
     */
    public function markAsAvailable(): void
    {
        $this->update(['status' => 'available']);
    }

    /**
     * Get total rental count
     */
    public function getRentalsCountAttribute(): int
    {
        return $this->rentals()->count();
    }

    /**
     * Get total revenue from this item
     */
    public function getTotalRevenueAttribute(): int
    {
        return $this->rentals()
            ->whereHas('history')
            ->get()
            ->sum(function ($rental) {
                $days = $rental->start_date->diffInDays($rental->end_date) + 1;
                return $rental->pivot->daily_price * $days;
            });
    }
}
