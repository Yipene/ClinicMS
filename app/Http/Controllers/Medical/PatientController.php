<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\AuditLogger;
use App\Services\ReferenceGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $patients = Patient::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($query) use ($s) {
                $query->where('first_name', 'ilike', "%{$s}%")
                    ->orWhere('last_name', 'ilike', "%{$s}%")
                    ->orWhere('code', 'ilike', "%{$s}%")
                    ->orWhere('phone', 'ilike', "%{$s}%");
            }))
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('medical.patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('medical.patients.form', ['patient' => new Patient]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:M,F,other'],
            'address' => ['nullable', 'string'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['code'] = ReferenceGenerator::generate('PAT', new Patient, 'code');
        $patient = Patient::create($data);
        AuditLogger::log('patient.created', $patient, null, ['code' => $patient->code]);

        return redirect()->route('patients.show', $patient)->with('status', 'Patient enregistré.');
    }

    public function show(Patient $patient): View
    {
        $patient->load([
            'consultations' => fn ($q) => $q->latest()->limit(5),
            'sales' => fn ($q) => $q->latest()->limit(5),
        ]);

        return view('medical.patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('medical.patients.form', compact('patient'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:M,F,other'],
            'address' => ['nullable', 'string'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $patient->update($data);
        AuditLogger::log('patient.updated', $patient);

        return redirect()->route('patients.show', $patient)->with('status', 'Dossier mis à jour.');
    }
}
