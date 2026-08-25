<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['clinic' => ClinicSetting::current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone_primary' => ['nullable', 'string', 'max:30'],
            'phone_secondary' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'website' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:3'],
            'cash_register_threshold' => ['nullable', 'numeric', 'min:0'],
        ]);

        $clinic = ClinicSetting::current();
        $old = $clinic->only(array_keys($data));
        $clinic->update($data);
        AuditLogger::log('clinic_settings.updated', $clinic, $old, $data);

        return back()->with('status', 'Paramètres de la clinique enregistrés.');
    }
}
