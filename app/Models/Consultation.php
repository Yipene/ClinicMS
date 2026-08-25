<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'sale_id', 'consulted_at',
        'reason', 'diagnosis', 'prescription', 'fee', 'status', 'follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'consulted_at' => 'datetime',
            'follow_up_at' => 'datetime',
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
