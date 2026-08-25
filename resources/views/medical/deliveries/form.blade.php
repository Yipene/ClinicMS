<x-clinic-layout>
    <x-slot name="header">Enregistrer un accouchement</x-slot>
    <form method="POST" action="{{ route('medical.deliveries.store') }}" class="max-w-xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf
        <div><label class="text-sm font-medium">Mère (patiente) *</label><select name="patient_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->full_name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Médecin *</label><select name="doctor_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($doctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Type</label><select name="type" class="mt-1 w-full rounded-lg border-slate-200"><option value="normal">Voie basse</option><option value="cesarean">Césarienne</option></select></div>
        <div><label class="text-sm font-medium">Terme (semaines)</label><input type="number" name="gestational_weeks" min="20" max="45" class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Date/heure *</label><input type="datetime-local" name="delivered_at" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Sexe NN</label><select name="newborn_sex" class="mt-1 w-full rounded-lg border-slate-200"><option value="">—</option><option value="M">M</option><option value="F">F</option></select></div>
            <div><label class="text-sm font-medium">Poids (kg)</label><input type="number" step="0.01" name="newborn_weight" class="mt-1 w-full rounded-lg border-slate-200"></div>
        </div>
        <div><label class="text-sm font-medium">Honoraires</label><input type="number" name="fee" value="25000" min="0" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Suivi post-natal</label><textarea name="postnatal_notes" rows="2" class="mt-1 w-full rounded-lg border-slate-200"></textarea></div>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Enregistrer</button>
    </form>
</x-clinic-layout>
