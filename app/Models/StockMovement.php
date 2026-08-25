<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'supplier_id', 'user_id', 'type', 'quantity',
        'unit_purchase_price', 'unit_sale_price', 'batch_number',
        'expiry_date', 'reference_type', 'reference_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_purchase_price' => 'decimal:2',
            'unit_sale_price' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
