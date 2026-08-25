<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperatingRoom extends Model
{
    protected $fillable = [
        'name', 'code', 'half_day_rate', 'full_day_rate', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'half_day_rate' => 'decimal:2',
            'full_day_rate' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(OperatingRoomBooking::class);
    }
}
