<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\ClinicSetting;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->when(request('q'), fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('name', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%")
                    ->orWhere('dci', 'ilike', "%{$search}%")
                    ->orWhere('barcode', 'ilike', "%{$search}%");
            }))
            ->when(request('filter') === 'low', fn ($q) => $q->whereColumn('stock_quantity', '<=', 'min_stock_level'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Product::where('is_active', true)->count(),
            'low_stock' => Product::where('is_active', true)->whereColumn('stock_quantity', '<=', 'min_stock_level')->count(),
            'stock_value' => Product::where('is_active', true)->sum(DB::raw('stock_quantity * purchase_price')),
        ];

        return view('stock.products.index', [
            'products' => $products,
            'stats' => $stats,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function create(): View
    {
        return view('stock.products.form', ['product' => new Product]);
    }

    public function store(StoreProductRequest $request, StockService $stockService): RedirectResponse
    {
        $data = $request->validated();
        $initialStock = (int) ($data['stock_quantity'] ?? 0);
        unset($data['stock_quantity']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['stock_quantity'] = 0;

        $product = Product::create($data);

        if ($initialStock > 0) {
            $stockService->increase($product, $initialStock, 'adjustment', notes: 'Stock initial');
        }

        return redirect()->route('stock.products.index')->with('status', 'Produit créé avec succès.');
    }

    public function edit(Product $product): View
    {
        return view('stock.products.form', compact('product'));
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        unset($data['stock_quantity']);
        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        return redirect()->route('stock.products.index')->with('status', 'Produit mis à jour.');
    }
}
