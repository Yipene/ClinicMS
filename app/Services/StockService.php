<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function increase(
        Product $product,
        int $quantity,
        string $type,
        ?float $unitPurchasePrice = null,
        ?float $unitSalePrice = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $supplierId = null,
        ?string $batchNumber = null,
        ?string $expiryDate = null,
        ?string $notes = null,
    ): StockMovement {
        return DB::transaction(function () use (
            $product, $quantity, $type, $unitPurchasePrice, $unitSalePrice,
            $referenceType, $referenceId, $supplierId, $batchNumber, $expiryDate, $notes
        ) {
            $product->increment('stock_quantity', $quantity);

            return StockMovement::create([
                'product_id' => $product->id,
                'supplier_id' => $supplierId,
                'user_id' => auth()->id(),
                'type' => $type,
                'quantity' => abs($quantity),
                'unit_purchase_price' => $unitPurchasePrice ?? $product->purchase_price,
                'unit_sale_price' => $unitSalePrice ?? $product->sale_price,
                'batch_number' => $batchNumber,
                'expiry_date' => $expiryDate,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    public function decrease(
        Product $product,
        int $quantity,
        string $type,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
    ): StockMovement {
        if ($product->stock_quantity < $quantity) {
            throw new \InvalidArgumentException("Stock insuffisant pour {$product->name} (disponible: {$product->stock_quantity}).");
        }

        return DB::transaction(function () use ($product, $quantity, $type, $referenceType, $referenceId, $notes) {
            $product->decrement('stock_quantity', $quantity);

            return StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $type,
                'quantity' => -abs($quantity),
                'unit_purchase_price' => $product->purchase_price,
                'unit_sale_price' => $product->sale_price,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    public function adjust(Product $product, int $newQuantity, ?string $notes = null): StockMovement
    {
        $diff = $newQuantity - $product->stock_quantity;

        if ($diff === 0) {
            throw new \InvalidArgumentException('Aucun écart à enregistrer.');
        }

        if ($diff > 0) {
            return $this->increase($product, $diff, 'inventory', notes: $notes);
        }

        return $this->decrease($product, abs($diff), 'inventory', notes: $notes);
    }
}
