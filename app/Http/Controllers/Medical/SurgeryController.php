<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\OperatingRoom;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\User;
use App\Services\MedicalBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurgeryController extends Controller
{
    public function index(): View
    {
        $surgeries = Surgery::with(['patient', 'surgeon', 'operatingRoom'])
            ->latest('scheduled_at')
            ->paginate(20);

        return view('medical.surgeries.index', compact('surgeries'));
    }

    public function create(): View
    {
        return view('medical.surgeries.form', [
            'surgery' => new Surgery,
            'patients' => Patient::orderBy('last_name')->get(),
            'surgeons' => User::clinicians()->orderBy('name')->get(),
            'rooms' => OperatingRoom::where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MedicalBillingService $billing): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'surgeon_id' => ['required', 'exists:users,id'],
            'operating_room_id' => ['nullable', 'exists:operating_rooms,id'],
            'scheduled_at' => ['required', 'date'],
            'operative_report' => ['nullable', 'string'],
            'fee' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:planned,in_progress,completed,cancelled'],
        ]);
        abort_unless(User::clinicians()->whereKey($data['surgeon_id'])->exists(), 422, 'Le chirurgien sélectionné n’est pas habilité.');

        $patient = Patient::findOrFail($data['patient_id']);
        $sale = $data['fee'] > 0
            ? $billing->bill($patient, 'intervention', 'Intervention chirurgicale', (float) $data['fee'])
            : null;

        Surgery::create([
            ...collect($data)->except(['operative_report'])->all(),
            'operative_report' => $data['operative_report'] ?? null,
            'sale_id' => $sale?->id,
        ]);

        return redirect()->route('medical.surgeries.index')->with('status', 'Intervention planifiée.');
    }
}
