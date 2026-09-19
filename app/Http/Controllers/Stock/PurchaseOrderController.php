<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\ClinicSetting;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(): View
    {
        $orders = PurchaseOrder::with(['supplier', 'creator'])
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock.purchase-orders.index', [
            'orders' => $orders,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function storeAndReceive(StorePurchaseOrderRequest $request, PurchaseOrderService $service): RedirectResponse
    {
            $order = $service->create(
                $request->only(['supplier_id', 'expected_at', 'notes']),
                $request->validated('items'),);


    $quantities = $order->items->pluck('quantity', 'id')->all();

    $service->receive($order, $quantities);

    return redirect()->route('pharmacie.index')->with('status', 'Approvisionnement enregistré — stock mis à jour.');
        }

    public function create(): View
    {
        return view('stock.purchase-orders.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StorePurchaseOrderRequest $request, PurchaseOrderService $service): RedirectResponse
    {
        $order = $service->create(
            $request->only(['supplier_id', 'expected_at', 'notes']),
            $request->validated('items'),
        );

        return redirect()->route('stock.purchase-orders.show', $order)
            ->with('status', 'Bon de commande créé.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['items.product', 'supplier', 'creator']);

        return view('stock.purchase-orders.show', [
            'order' => $purchaseOrder,
            'clinic' => ClinicSetting::current(),
        ]);
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder, PurchaseOrderService $service): RedirectResponse
    {
        $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['integer', 'min:0'],
        ]);

        $service->receive($purchaseOrder, $request->input('quantities', []));

        return redirect()->route('stock.purchase-orders.show', $purchaseOrder)
            ->with('status', 'Réception enregistrée — stock mis à jour.');
    }
}
