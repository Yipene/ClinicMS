<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(
        protected StockService $stockService,
    ) {}

    public function create(array $data, array $items): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $items) {
            $order = PurchaseOrder::create([
                'reference' => ReferenceGenerator::generate('BC', new PurchaseOrder),
                'supplier_id' => $data['supplier_id'],
                'created_by' => auth()->id(),
                'status' => $data['status'] ?? 'ordered',
                'ordered_at' => $data['ordered_at'] ?? now(),
                'expected_at' => $data['expected_at'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_price' => $item['unit_price'],
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);
            }

            return $order->load(['items.product', 'supplier']);
        });
    }

    public function receive(PurchaseOrder $order, array $receivedQuantities): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $receivedQuantities) {
            $order->load('items.product');

            foreach ($order->items as $item) {
                $qty = (int) ($receivedQuantities[$item->id] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $remaining = $item->quantity_ordered - $item->quantity_received;
                $qty = min($qty, $remaining);

                if ($qty <= 0) {
                    continue;
                }

                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $product->update([
                    'purchase_price' => $item->unit_price,
                ]);

                $this->stockService->increase(
                    $product,
                    $qty,
                    'purchase',
                    (float) $item->unit_price,
                    (float) $product->sale_price,
                    PurchaseOrder::class,
                    $order->id,
                    $order->supplier_id,
                    expiryDate: $item->expiry_date?->format('Y-m-d'),
                    notes: "Réception {$order->reference}",
                );

                $item->increment('quantity_received', $qty);
            }

            $allReceived = $order->items()->get()->every(
                fn ($item) => $item->quantity_received >= $item->quantity_ordered
            );

            $anyReceived = $order->items()->get()->some(fn ($item) => $item->quantity_received > 0);

            $order->update([
                'status' => $allReceived ? 'received' : ($anyReceived ? 'partial' : $order->status),
                'received_at' => $anyReceived ? now() : null,
            ]);

            return $order->fresh(['items.product', 'supplier']);
        });
    }
}
