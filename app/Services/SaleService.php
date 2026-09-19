<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected StockService $stockService,
    ) {}

    public function create(array $saleData, array $items, array $payments): Sale
    {
        return DB::transaction(function () use ($saleData, $items, $payments) {
            $subtotal = collect($items)->sum(fn ($item) => $item['quantity'] * $item['unit_price']);
            $discount = (float) ($saleData['discount'] ?? 0);
            $total = max(0, $subtotal - $discount);

            $sale = Sale::create([
                'reference' => ReferenceGenerator::generate('VTE', new Sale),
                'patient_id' => $saleData['patient_id'] ?? null,
                'customer_type' => $saleData['customer_type'] ?? ($saleData['patient_id'] ? 'patient' : 'anonymous'),
                'customer_name' => $saleData['customer_name'] ?? null,
                'customer_phone' => $saleData['customer_phone'] ?? null,
                'cashier_id' => auth()->id(),
                'module' => $saleData['module'] ?? 'caisse',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'status' => 'completed',
                'payment_status' => 'paid',
                'notes' => $saleData['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                ]);

                if (! empty($item['product_id'])) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $this->stockService->decrease(
                        $product,
                        (int) $item['quantity'],
                        'sale',
                        Sale::class,
                        $sale->id,
                    );
                }
            }

            $paidTotal = 0;
            foreach ($payments as $payment) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'amount' => $payment['amount'],
                    'method' => $payment['method'],
                    'reference' => $payment['reference'] ?? null,
                    'paid_at' => now(),
                ]);
                $paidTotal += $payment['amount'];
            }

            if ($paidTotal < $total) {
                $sale->update(['payment_status' => $paidTotal > 0 ? 'partial' : 'unpaid']);
            }

            $sale = $sale->load(['items.product', 'payments', 'patient', 'cashier']);

            AuditLogger::log('sale.created', $sale, null, [
                'reference' => $sale->reference,
                'total' => $sale->total,
                'module' => $sale->module,
            ]);

            return $sale;
        });
    }
}
