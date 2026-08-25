<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'sale_id', 'type', 'gestational_weeks',
        'delivered_at', 'newborn_info', 'fee', 'postnatal_notes',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
            'newborn_info' => 'array',
            'fee' => 'decimal:2',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
