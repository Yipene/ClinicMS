<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockExportController extends Controller
{
    public function export(): StreamedResponse
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['SKU', 'Nom', 'DCI', 'PA', 'PV', 'Stock', 'Seuil min', 'Marge'], ';');
            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->sku, $p->name, $p->dci,
                    $p->purchase_price, $p->sale_price,
                    $p->stock_quantity, $p->min_stock_level, $p->margin,
                ], ';');
            }
            fclose($handle);
        }, 'stock-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
