<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = [
        'name', 'legal_name', 'address', 'city',
        'phone_primary', 'phone_secondary', 'email', 'website',
        'logo_path', 'currency', 'cash_register_threshold',
    ];

    protected function casts(): array
    {
        return [
            'cash_register_threshold' => 'decimal:2',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'name' => config('app.name', 'Clinic MS'),
            'address' => null,
            'phone_primary' => null,
            'phone_secondary' => null,
            'email' => null,
            'website' => null,
            'currency' => 'XOF',
        ]);
    }
}
