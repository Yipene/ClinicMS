<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\AuditLogger;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpiredStockController extends Controller
{
    public function index(): View
    {
        $expiring = StockMovement::with('product')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get()
            ->unique('product_id');

        return view('stock.expired.index', compact('expiring'));
    }

    public function destroy(Request $request, Product $product, StockService $stockService): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $qty = min($data['quantity'], $product->stock_quantity);
        $stockService->decrease($product, $qty, 'expired', notes: $data['notes'] ?? 'Destruction produit périmé');
        AuditLogger::log('stock.expired_destroyed', $product, null, ['quantity' => $qty]);

        return back()->with('status', 'Produit périmé retiré du stock.');
    }
}
