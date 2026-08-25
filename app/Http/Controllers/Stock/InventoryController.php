<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\ClinicSetting;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\ReferenceGenerator;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $inventories = Inventory::with('user')->latest()->paginate(10);

        return view('stock.inventory.index', [
            'inventories' => $inventories,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function create(): View
    {
        return view('stock.inventory.create', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreInventoryRequest $request, StockService $stockService): RedirectResponse
    {
        DB::transaction(function () use ($request, $stockService) {
            $inventory = Inventory::create([
                'reference' => ReferenceGenerator::generate('INV', new Inventory),
                'user_id' => auth()->id(),
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->notes,
            ]);

            foreach ($request->validated('counts') as $count) {
                $product = Product::lockForUpdate()->findOrFail($count['product_id']);
                $expected = $product->stock_quantity;
                $counted = (int) $count['counted_quantity'];
                $variance = $counted - $expected;

                $inventory->items()->create([
                    'product_id' => $product->id,
                    'expected_quantity' => $expected,
                    'counted_quantity' => $counted,
                    'variance' => $variance,
                ]);

                if ($variance !== 0) {
                    $stockService->adjust($product, $counted, "Inventaire {$inventory->reference}");
                }
            }
        });

        return redirect()->route('stock.inventory.index')
            ->with('status', 'Inventaire enregistré et stock ajusté.');
    }
}
