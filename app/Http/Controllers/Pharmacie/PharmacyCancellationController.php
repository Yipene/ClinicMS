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
    

    public function store(Request $request, SaleCancellationService $service): RedirectResponse
{
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    'sale_id' => ['required', 'exists:sales,id'],
    'type' => ['required', 'in:partial,full'],
    'reason' => ['required', 'string', 'max:1000'],
    'item_ids' => ['required_if:type,partial', 'array'],
    'item_ids.*' => ['integer', 'exists:sale_items,id'],
]);

if ($validator->fails()) {
    return back()->withInput()->withErrors($validator, 'cancel');
}
$validated = $validator->validated();

    $sale = Sale::where('module', 'pharmacie')->findOrFail($validated['sale_id']);

    try {
        $service->cancel(
            $sale,
            $validated['type'],
            $validated['reason'],
            $validated['type'] === 'partial' ? $validated['item_ids'] : null,
        );
    } catch (\InvalidArgumentException $e) {
        return back()->withInput()->withErrors(['reason' => $e->getMessage()], 'cancel');
    }

    return redirect()->route('pharmacie.index')
        ->with('status', 'Annulation enregistrée — stock restitué.');
}

}
