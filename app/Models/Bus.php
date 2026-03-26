<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    protected $fillable = [
        'bus_number',
        'from_location',
        'to_location',
        'journey_time',
        'journey_date',
        'total_seats',
        'available_seats',
        'price_per_seat',
        'bus_type',
        'created_at',
        'updated_at',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
