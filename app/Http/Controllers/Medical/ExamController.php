<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\User;
use App\Services\MedicalBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(): View
    {
        $exams = Exam::with(['patient', 'doctor'])->latest('prescribed_at')->paginate(20);

        return view('medical.exams.index', compact('exams'));
    }

    public function create(Request $request): View
    {
        return view('medical.exams.form', [
            'exam' => new Exam,
            'patients' => Patient::orderBy('last_name')->get(),
            'doctors' => User::clinicians()->orderBy('name')->get(),
            'selectedPatient' => $request->patient_id ? Patient::find($request->patient_id) : null,
        ]);
    }

    public function store(Request $request, MedicalBillingService $billing): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:biology,imaging,other'],
            'label' => ['required', 'string', 'max:255'],
            'prescribed_at' => ['required', 'date'],
            'fee' => ['required', 'numeric', 'min:0'],
            'bill_now' => ['boolean'],
        ]);
        abort_unless(User::clinicians()->whereKey($data['doctor_id'])->exists(), 422, 'Le médecin sélectionné n’est pas habilité.');

        $patient = Patient::findOrFail($data['patient_id']);
        $sale = ($data['bill_now'] ?? true) && $data['fee'] > 0
            ? $billing->bill($patient, 'examen', $data['label'], (float) $data['fee'])
            : null;

        Exam::create([
            ...collect($data)->only(['patient_id', 'doctor_id', 'type', 'label', 'prescribed_at', 'fee'])->all(),
            'sale_id' => $sale?->id,
            'status' => 'prescribed',
        ]);

        return redirect()->route('medical.exams.index')->with('status', 'Examen prescrit.');
    }

    public function edit(Exam $exam): View
    {
        $exam->load(['patient', 'doctor']);

        return view('medical.exams.edit', [
            'exam' => $exam,
            'doctors' => User::clinicians()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:prescribed,in_progress,completed,cancelled'],
            'result' => ['nullable', 'string'],
            'result_at' => ['nullable', 'date'],
        ]);

        if ($data['status'] === 'completed' && empty($data['result_at'])) {
            $data['result_at'] = now();
        }

        $exam->update($data);

        return redirect()->route('medical.exams.index')->with('status', 'Examen mis à jour.');
    }
}
