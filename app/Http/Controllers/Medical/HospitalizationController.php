<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Hospitalization;
use App\Models\Patient;
use App\Models\Room;
use App\Models\User;
use App\Services\MedicalBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HospitalizationController extends Controller
{
    public function index(): View
    {
        $hospitalizations = Hospitalization::with(['patient', 'doctor', 'room'])
            ->latest('admitted_at')
            ->paginate(20);

        $occupiedBeds = Hospitalization::where('status', 'admitted')->count();
        $availableRooms = Room::where('is_available', true)->count();

        return view('medical.hospitalizations.index', compact('hospitalizations', 'occupiedBeds', 'availableRooms'));
    }

    public function create(): View
    {
        return view('medical.hospitalizations.form', [
            'hospitalization' => new Hospitalization,
            'patients' => Patient::orderBy('last_name')->get(),
            'doctors' => User::role(['medecin', 'administrateur'])->orderBy('name')->get(),
            'rooms' => Room::where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MedicalBillingService $billing): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'admitted_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'admission_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        $patient = Patient::findOrFail($data['patient_id']);
        $fee = (float) ($data['admission_fee'] ?? 0);
        $sale = $fee > 0 ? $billing->bill($patient, 'hospitalisation', 'Frais admission', $fee) : null;

        if (! empty($data['room_id'])) {
            Room::where('id', $data['room_id'])->update(['is_available' => false]);
        }

        Hospitalization::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'room_id' => $data['room_id'] ?? null,
            'sale_id' => $sale?->id,
            'admitted_at' => $data['admitted_at'],
            'notes' => $data['notes'] ?? null,
            'status' => 'admitted',
        ]);

        return redirect()->route('medical.hospitalizations.index')->with('status', 'Patient admis.');
    }

    public function discharge(Request $request, Hospitalization $hospitalization): RedirectResponse
    {
        $data = $request->validate([
            'discharge_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($hospitalization->room_id) {
            Room::where('id', $hospitalization->room_id)->update(['is_available' => true]);
        }

        if (! empty($data['discharge_fee']) && $data['discharge_fee'] > 0) {
            app(MedicalBillingService::class)->bill(
                $hospitalization->patient,
                'hospitalisation',
                'Frais de sortie / séjour',
                (float) $data['discharge_fee'],
            );
        }

        $hospitalization->update([
            'status' => 'discharged',
            'discharged_at' => now(),
        ]);

        return back()->with('status', 'Sortie enregistrée.');
    }
}
