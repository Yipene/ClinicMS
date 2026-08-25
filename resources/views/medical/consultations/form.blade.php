<x-clinic-layout>
    <x-slot name="header">Nouvelle consultation</x-slot>
    <form method="POST" action="{{ route('medical.consultations.store') }}" class="max-w-2xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf
        <div><label class="text-sm font-medium">Patient *</label>
            <select name="patient_id" required class="mt-1 w-full rounded-lg border-slate-200">
                @foreach($patients as $p)<option value="{{ $p->id }}" @selected(($selectedPatient?->id ?? old('patient_id')) == $p->id)>{{ $p->full_name }}</option>@endforeach
            </select></div>
        <div><label class="text-sm font-medium">Médecin *</label>
            <select name="doctor_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($doctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Date *</label><input type="datetime-local" name="consulted_at" value="{{ old('consulted_at', now()->format('Y-m-d\TH:i')) }}" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Motif</label><textarea name="reason" rows="2" class="mt-1 w-full rounded-lg border-slate-200">{{ old('reason') }}</textarea></div>
        <div><label class="text-sm font-medium">Diagnostic</label><textarea name="diagnosis" rows="2" class="mt-1 w-full rounded-lg border-slate-200">{{ old('diagnosis') }}</textarea></div>
        <div><label class="text-sm font-medium">Prescription</label><textarea name="prescription" rows="3" class="mt-1 w-full rounded-lg border-slate-200">{{ old('prescription') }}</textarea></div>
        <div><label class="text-sm font-medium">Honoraires (XOF) *</label><input type="number" name="fee" value="{{ old('fee', 5000) }}" min="0" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <label class="flex gap-2 text-sm"><input type="checkbox" name="bill_now" value="1" checked class="rounded text-teal-600"> Facturer immédiatement à la caisse</label>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Enregistrer</button>
    </form>
</x-clinic-layout>
