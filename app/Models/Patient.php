<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'first_name', 'last_name', 'phone', 'email',
        'birth_date', 'gender', 'address', 'blood_group',
        'emergency_contact', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'notes' => 'encrypted',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }
}
