<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $q = $request->get('q', '');
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('barcode', $q)
                    ->orWhere('sku', 'ilike', $q)
                    ->orWhere('name', 'ilike', "%{$q}%")
                    ->orWhere('dci', 'ilike', "%{$q}%");
            })
            ->limit(20)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'barcode' => $p->barcode,
                'name' => $p->name,
                'dci' => $p->dci,
                'sale_price' => (float) $p->sale_price,
                'purchase_price' => (float) $p->purchase_price,
                'stock_quantity' => $p->stock_quantity,
                'margin' => (float) $p->margin,
                'low_stock' => $p->isLowStock(),
            ]);

        return response()->json($products);
    }

    public function byBarcode(string $barcode): JsonResponse
    {
        $product = Product::where('is_active', true)
            ->where('barcode', $barcode)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Produit introuvable'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'dci' => $product->dci,
            'sale_price' => (float) $product->sale_price,
            'stock_quantity' => $product->stock_quantity,
        ]);
    }
}
