<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use App\Models\StockMovement;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(): View
    {
        $movements = StockMovement::with(['product', 'user', 'supplier'])
            ->when(request('type'), fn ($q, $type) => $q->where('type', $type))
            ->when(request('product_id'), fn ($q, $id) => $q->where('product_id', $id))
            ->when(request('date'), fn ($q, $date) => $q->whereDate('created_at', $date))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $typeLabels = [
            'purchase' => 'Achat / Réception',
            'sale' => 'Vente',
            'internal' => 'Usage interne',
            'expired' => 'Périmé',
            'adjustment' => 'Ajustement',
            'cancellation_return' => 'Retour annulation',
            'inventory' => 'Inventaire',
        ];

        return view('stock.movements.index', [
            'movements' => $movements,
            'typeLabels' => $typeLabels,
            'clinic' => ClinicSetting::current(),
        ]);
    }
}
