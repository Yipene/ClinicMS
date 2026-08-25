<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use App\Services\MedicalBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        $consultations = Consultation::with(['patient', 'doctor'])
            ->when($request->date, fn ($q, $d) => $q->whereDate('consulted_at', $d))
            ->latest('consulted_at')
            ->paginate(20)
            ->withQueryString();

        return view('medical.consultations.index', compact('consultations'));
    }

    public function create(Request $request): View
    {
        return view('medical.consultations.form', [
            'consultation' => new Consultation,
            'patients' => Patient::orderBy('last_name')->get(),
            'doctors' => User::role(['medecin', 'administrateur'])->orderBy('name')->get(),
            'selectedPatient' => $request->patient_id ? Patient::find($request->patient_id) : null,
        ]);
    }

    public function store(Request $request, MedicalBillingService $billing): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'consulted_at' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'prescription' => ['nullable', 'string'],
            'fee' => ['required', 'numeric', 'min:0'],
            'follow_up_at' => ['nullable', 'date'],
            'bill_now' => ['boolean'],
        ]);

        $patient = Patient::findOrFail($data['patient_id']);
        $sale = ($data['bill_now'] ?? true) && $data['fee'] > 0
            ? $billing->bill($patient, 'consultation', 'Consultation médicale', (float) $data['fee'])
            : null;

        $consultation = Consultation::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'sale_id' => $sale?->id,
            'consulted_at' => $data['consulted_at'],
            'reason' => $data['reason'] ?? null,
            'diagnosis' => $data['diagnosis'] ?? null,
            'prescription' => $data['prescription'] ?? null,
            'fee' => $data['fee'],
            'status' => 'completed',
            'follow_up_at' => $data['follow_up_at'] ?? null,
        ]);

        return redirect()->route('medical.consultations.show', $consultation)
            ->with('status', 'Consultation enregistrée.');
    }

    public function show(Consultation $consultation): View
    {
        $consultation->load(['patient', 'doctor', 'sale']);

        return view('medical.consultations.show', compact('consultation'));
    }
}
