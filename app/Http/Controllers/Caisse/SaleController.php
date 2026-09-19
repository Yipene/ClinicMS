<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with(['patient', 'cashier'])
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'ilike', "%{$search}%")
                        ->orWhereHas('patient', fn ($p) => $p->where('first_name', 'ilike', "%{$search}%")
                            ->orWhere('last_name', 'ilike', "%{$search}%"));
                });
            })
            ->when(request('date'), fn ($q, $date) => $q->whereDate('created_at', $date))
            ->when(request('module'), fn ($q, $module) => $q->where('module', $module))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('caisse.sales.index', [
            'sales' => $sales,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function create(): View
    {
        return view('caisse.sales.create', [
            'patients' => Patient::orderBy('last_name')->get(),
            'products' => Product::where('is_active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get(),
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function store(StoreSaleRequest $request, SaleService $saleService): RedirectResponse
    {
        $saleData = $request->only([
            'patient_id', 'customer_type', 'customer_name', 'customer_phone', 'discount', 'notes',
        ]);
        $saleData['module'] = 'caisse';
        if (($saleData['customer_type'] ?? 'anonymous') !== 'patient') {
            $saleData['patient_id'] = null;
        }

        try {
            $sale = $saleService->create(
                $saleData,
                $request->validated('items'),
                $request->validated('payments'),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('caisse.sales.show', $sale)
            ->with('status', 'Vente enregistrée avec succès.');
    }

    public function show(Sale $sale): View
    {
        abort_unless($sale->module === 'caisse', 404);
        $sale->load(['items.product', 'payments', 'patient', 'cashier']);

        return view('caisse.sales.show', [
            'sale' => $sale,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function pdf(Sale $sale)
    {
        abort_unless($sale->module === 'caisse', 404);
        $sale->load(['items.product', 'payments', 'patient', 'cashier']);

        return Pdf::loadView('pdf.receipt', [
            'sale' => $sale,
            'clinic' => ClinicSetting::current(),
        ])->download($sale->reference.'.pdf');
    }
}
