<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'sale_id', 'type', 'label', 'status',
        'prescribed_at', 'result_at', 'result', 'fee',
    ];

    protected function casts(): array
    {
        return [
            'prescribed_at' => 'datetime',
            'result_at' => 'datetime',
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

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
