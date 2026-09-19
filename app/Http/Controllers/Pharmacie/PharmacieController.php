<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacieController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::with(['patient', 'cashier'])
            ->where('module', 'pharmacie')
            ->when($request->q, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'ilike', "%{$search}%")
                        ->orWhere('customer_name', 'ilike', "%{$search}%")
                        ->orWhere('customer_phone', 'ilike', "%{$search}%")
                        ->orWhereHas('patient', fn ($p) => $p->where('first_name', 'ilike', "%{$search}%")
                            ->orWhere('last_name', 'ilike', "%{$search}%"));
                });
            })
            ->when($request->date, fn ($q, $date) => $q->whereDate('created_at', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pharmacie.index', [
            'sales' => $sales,
            'patients' => Patient::orderBy('last_name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'clinic' => ClinicSetting::current(),
            'reopenCancelSale' => $this->reopenCancelSale(),
        ]);
    }

    /**
     * Si une annulation a échoué à la validation, on recharge la vente
     * concernée pour que le modal se rouvre déjà rempli.
     */
    private function reopenCancelSale(): ?array
    {
        if (! session()->hasOldInput('sale_id')) {
            return null;
        }

        if (! session()->get('errors')?->getBag('cancel')->any()) {
            return null;
        }

        $sale = Sale::with(['items', 'cancellations.items'])
            ->where('module', 'pharmacie')
            ->find(old('sale_id'));

        if (! $sale) {
            return null;
        }

        $cancelledItemIds = $sale->cancellations
            ->flatMap(fn ($c) => $c->items ?? [])
            ->pluck('sale_item_id');

        $oldCheckedIds = collect(old('item_ids', []));

        return [
            'id' => $sale->id,
            'reference' => $sale->reference,
            'total' => $sale->total,
            'items' => $sale->items->map(fn ($i) => [
                'id' => $i->id,
                'description' => $i->description,
                'quantity' => $i->quantity,
                'line_total' => $i->line_total,
                'already_cancelled' => $cancelledItemIds->contains($i->id),
                'old_checked' => $oldCheckedIds->contains($i->id),
            ]),
        ];
    }
}