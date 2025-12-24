<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'renter_name',
        'renter_phone',
        'renter_ktp',
        'start_date',
        'end_date',
        'total_price',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'integer',
    ];

    /**
     * Relationship: Items (through pivot table)
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'rental_items')
            ->withPivot('daily_price')
            ->withTimestamps();
    }

    /**
     * Relationship: History record
     */
    public function history()
    {
        return $this->hasOne(History::class);
    }

    /**
     * Scope: Active rentals only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Completed rentals
     */
    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    /**
     * Scope: Overdue rentals (past end_date)
     * Telat = hari ini > tanggal kembali
     */
    public function scopeOverdue($query)
    {
        return $query->active()->where('end_date', '<', now()->toDateString());
    }

    /**
     * Scope: Due today (jatuh tempo hari ini)
     */
    public function scopeDueToday($query)
    {
        return $query->active()->whereDate('end_date', now()->toDateString());
    }

    /**
     * Scope: Not yet started (belum diambil)
     * Booking yang start_date nya masih di masa depan
     */
    public function scopeNotStarted($query)
    {
        return $query->active()->where('start_date', '>', now()->toDateString());
    }

    /**
     * Scope: Currently active (sudah diambil, belum dikembalikan)
     * start_date <= today AND end_date >= today
     */
    public function scopeOngoing($query)
    {
        $today = now()->toDateString();
        return $query->active()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    /**
     * Check if rental is overdue
     * Telat jika hari ini sudah melewati end_date
     */
    public function isOverdue(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        return now()->startOfDay()->gt($this->end_date->startOfDay());
    }

    /**
     * Check if rental has not started yet (belum diambil)
     */
    public function isNotStarted(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        return now()->startOfDay()->lt($this->start_date->startOfDay());
    }

    /**
     * Check if due today
     */
    public function isDueToday(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        return now()->startOfDay()->eq($this->end_date->startOfDay());
    }

    /**
     * Get number of days overdue (berapa hari telat)
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->startOfDay()->diffInDays($this->end_date->startOfDay());
    }

    /**
     * Calculate rental duration in days
     */
    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Complete the rental (return items) - tanpa penalty
     */
    public function complete(): History
    {
        // Mark rental as done
        $this->update(['status' => 'done']);

        // Mark all items as available
        foreach ($this->items as $item) {
            $item->markAsAvailable();
        }

        // Create history record (no penalty)
        return History::create([
            'rental_id' => $this->id,
            'actual_return_date' => now(),
            'penalty_fee' => 0, // No penalty
            'final_total_price' => $this->total_price,
        ]);
    }

    /**
     * Calculate and set total price from items
     */
    public function calculateTotalPrice(): int
    {
        $dailyTotal = $this->items->sum('pivot.daily_price');
        $duration = $this->duration;

        return $dailyTotal * $duration;
    }
}
