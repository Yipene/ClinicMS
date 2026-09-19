<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacySaleController extends Controller
{
    

    public function store(StoreSaleRequest $request, SaleService $saleService): RedirectResponse
    {
        $data = $request->only([
            'patient_id', 'customer_type', 'customer_name', 'customer_phone', 'discount', 'notes',
        ]);
        if ($data['customer_type'] !== 'patient') {
            $data['patient_id'] = null;
        }
        $data['module'] = 'pharmacie';

        try {
            $sale = $saleService->create(
                $data,
                $request->validated('items'),
                $request->validated('payments'),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()], 'sale');
        }

        return redirect()->route('pharmacie.sales.show', $sale)
            ->with('status', 'Vente pharmaceutique enregistrée.');
    }

    public function show(Sale $sale): View
    {
        abort_unless($sale->module === 'pharmacie', 404);
        $sale->load(['items.product', 'payments', 'patient', 'cashier']);

        return view('pharmacie.sales.show', [
            'sale' => $sale,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function searchProducts(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'ilike', "%{$q}%")
                    ->orWhere('dci', 'ilike', "%{$q}%")
                    ->orWhere('barcode', 'ilike', "%{$q}%")
                    ->orWhere('sku', 'ilike', "%{$q}%");
            })
            ->limit(15)
            ->get(['id', 'name', 'dci', 'barcode', 'sale_price', 'stock_quantity', 'sku']);

        return response()->json($products);
    }
}
