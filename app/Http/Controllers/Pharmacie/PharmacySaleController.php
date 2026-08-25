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
    public function index(Request $request): View
    {
        $sales = Sale::with(['patient', 'cashier'])
            ->where('module', 'pharmacie')
            ->when($request->q, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'ilike', "%{$search}%")
                        ->orWhereHas('patient', fn ($p) => $p->where('first_name', 'ilike', "%{$search}%")
                            ->orWhere('last_name', 'ilike', "%{$search}%"));
                });
            })
            ->when($request->date, fn ($q, $date) => $q->whereDate('created_at', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pharmacie.sales.index', [
            'sales' => $sales,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function create(): View
    {
        return view('pharmacie.sales.create', [
            'patients' => Patient::orderBy('last_name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function store(StoreSaleRequest $request, SaleService $saleService): RedirectResponse
    {
        $data = $request->only(['patient_id', 'discount', 'notes']);
        $data['module'] = 'pharmacie';

        try {
            $sale = $saleService->create(
                $data,
                $request->validated('items'),
                $request->validated('payments'),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
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
