<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleCancellation;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleCancellationService
{
    public function __construct(
        protected StockService $stockService,
    ) {}

    public function cancel(Sale $sale, string $type, string $reason, ?array $itemIds = null): SaleCancellation
    {
        if (in_array($sale->status, ['cancelled'])) {
            throw new \InvalidArgumentException('Cette vente est déjà annulée.');
        }

        return DB::transaction(function () use ($sale, $type, $reason, $itemIds) {
            $sale->load('items.product');

            $itemsToCancel = $sale->items;
            if ($type === 'partial' && $itemIds) {
                $itemsToCancel = $sale->items->whereIn('id', $itemIds);
                if ($itemsToCancel->isEmpty()) {
                    throw new \InvalidArgumentException('Sélectionnez au moins une ligne à annuler.');
                }
            }

            $amount = $itemsToCancel->sum('line_total');
            $cancelledItems = [];

            foreach ($itemsToCancel as $item) {
                $cancelledItems[] = [
                    'sale_item_id' => $item->id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ];

                if ($item->product_id && $item->product) {
                    $this->stockService->increase(
                        $item->product,
                        (int) $item->quantity,
                        'cancellation_return',
                        referenceType: Sale::class,
                        referenceId: $sale->id,
                        notes: "Annulation {$sale->reference}",
                    );
                }
            }

            $cancellation = SaleCancellation::create([
                'sale_id' => $sale->id,
                'user_id' => auth()->id(),
                'type' => $type,
                'amount' => $amount,
                'reason' => $reason,
                'items' => $cancelledItems,
            ]);

            if ($type === 'full' || $itemsToCancel->count() === $sale->items->count()) {
                $sale->update(['status' => 'cancelled', 'payment_status' => 'unpaid']);
            } else {
                $sale->update(['status' => 'partially_cancelled']);
            }

            AuditLogger::log('sale.cancelled', $sale, null, [
                'type' => $type,
                'amount' => $amount,
                'reference' => $sale->reference,
            ]);

            return $cancellation;
        });
    }
}
