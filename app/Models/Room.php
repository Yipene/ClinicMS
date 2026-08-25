<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'code', 'name', 'department', 'type', 'daily_rate', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'daily_rate' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function hospitalizations(): HasMany
    {
        return $this->hasMany(Hospitalization::class);
    }
}
