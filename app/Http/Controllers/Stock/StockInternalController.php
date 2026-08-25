<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AuditLogger;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockInternalController extends Controller
{
    public function create(): View
    {
        return view('stock.internal.create', [
            'products' => Product::where('is_active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, StockService $stockService): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $stockService->decrease($product, (int) $data['quantity'], 'internal', notes: $data['notes']);
        AuditLogger::log('stock.internal_use', $product, null, $data);

        return redirect()->route('stock.movements.index')->with('status', 'Sortie interne enregistrée.');
    }
}
