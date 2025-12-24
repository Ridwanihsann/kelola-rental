<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'actual_return_date',
        'penalty_fee',
        'final_total_price',
    ];

    protected $casts = [
        'actual_return_date' => 'datetime',
        'penalty_fee' => 'integer',
        'final_total_price' => 'integer',
    ];

    /**
     * Relationship: Rental
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Scope: Order by most recent
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('actual_return_date', 'desc');
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $from = null, $to = null)
    {
        if ($from) {
            $query->whereDate('actual_return_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('actual_return_date', '<=', $to);
        }
        return $query;
    }

    /**
     * Check if there was a penalty
     */
    public function hasPenalty(): bool
    {
        return $this->penalty_fee > 0;
    }
}
