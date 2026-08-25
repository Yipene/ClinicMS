<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Patient;
use App\Models\User;
use App\Services\MedicalBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(): View
    {
        $deliveries = Delivery::with(['patient', 'doctor'])->latest('delivered_at')->paginate(20);

        return view('medical.deliveries.index', compact('deliveries'));
    }

    public function create(): View
    {
        return view('medical.deliveries.form', [
            'delivery' => new Delivery,
            'patients' => Patient::orderBy('last_name')->get(),
            'doctors' => User::role(['medecin', 'administrateur'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MedicalBillingService $billing): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:normal,cesarean'],
            'gestational_weeks' => ['nullable', 'integer', 'min:20', 'max:45'],
            'delivered_at' => ['required', 'date'],
            'newborn_sex' => ['nullable', 'in:M,F'],
            'newborn_weight' => ['nullable', 'numeric', 'min:0'],
            'fee' => ['required', 'numeric', 'min:0'],
            'postnatal_notes' => ['nullable', 'string'],
        ]);

        $patient = Patient::findOrFail($data['patient_id']);
        $label = $data['type'] === 'cesarean' ? 'Accouchement (césarienne)' : 'Accouchement (voie basse)';
        $sale = $data['fee'] > 0 ? $billing->bill($patient, 'accouchement', $label, (float) $data['fee']) : null;

        Delivery::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'sale_id' => $sale?->id,
            'type' => $data['type'],
            'gestational_weeks' => $data['gestational_weeks'] ?? null,
            'delivered_at' => $data['delivered_at'],
            'newborn_info' => array_filter([
                'sex' => $data['newborn_sex'] ?? null,
                'weight_kg' => $data['newborn_weight'] ?? null,
            ]),
            'fee' => $data['fee'],
            'postnatal_notes' => $data['postnatal_notes'] ?? null,
        ]);

        return redirect()->route('medical.deliveries.index')->with('status', 'Accouchement enregistré.');
    }
}
