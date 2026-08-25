<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surgery extends Model
{
    protected $fillable = [
        'patient_id', 'surgeon_id', 'anesthesiologist_id', 'operating_room_id',
        'sale_id', 'scheduled_at', 'operative_report', 'status', 'fee',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'fee' => 'decimal:2',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function surgeon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surgeon_id');
    }

    public function anesthesiologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anesthesiologist_id');
    }

    public function operatingRoom(): BelongsTo
    {
        return $this->belongsTo(OperatingRoom::class);
    }
}
