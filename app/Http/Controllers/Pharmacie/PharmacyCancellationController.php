<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use App\Models\Sale;
use App\Models\SaleCancellation;
use App\Services\SaleCancellationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacyCancellationController extends Controller
{
    public function index(): View
    {
        $cancellations = SaleCancellation::with(['sale.patient', 'user'])
            ->whereHas('sale', fn ($q) => $q->where('module', 'pharmacie'))
            ->latest()
            ->paginate(20);

        return view('pharmacie.cancellations.index', [
            'cancellations' => $cancellations,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function create(Request $request): View
    {
        $sale = null;
        if ($request->filled('sale_id')) {
            $sale = Sale::with('items.product')
                ->where('module', 'pharmacie')
                ->whereNotIn('status', ['cancelled'])
                ->findOrFail($request->sale_id);
        }

        $recentSales = Sale::where('module', 'pharmacie')
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->limit(20)
            ->get();

        return view('pharmacie.cancellations.create', compact('sale', 'recentSales'));
    }

    public function store(Request $request, SaleCancellationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'type' => ['required', 'in:partial,full'],
            'reason' => ['required', 'string', 'max:1000'],
            'item_ids' => ['nullable', 'array'],
            'item_ids.*' => ['integer', 'exists:sale_items,id'],
        ]);

        $sale = Sale::where('module', 'pharmacie')->findOrFail($validated['sale_id']);

        try {
            $service->cancel(
                $sale,
                $validated['type'],
                $validated['reason'],
                $validated['type'] === 'partial' ? $validated['item_ids'] : null,
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['reason' => $e->getMessage()]);
        }

        return redirect()->route('pharmacie.cancellations.index')
            ->with('status', 'Annulation enregistrée — stock restitué.');
    }
}
