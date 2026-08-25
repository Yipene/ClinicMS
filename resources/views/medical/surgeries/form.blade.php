<x-clinic-layout>
    <x-slot name="header">Planifier une intervention</x-slot>
    <form method="POST" action="{{ route('medical.surgeries.store') }}" class="max-w-xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf
        <div><label class="text-sm font-medium">Patient *</label><select name="patient_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->full_name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Chirurgien *</label><select name="surgeon_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($surgeons as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Bloc</label><select name="operating_room_id" class="mt-1 w-full rounded-lg border-slate-200"><option value="">—</option>@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Date/heure *</label><input type="datetime-local" name="scheduled_at" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Honoraires</label><input type="number" name="fee" value="50000" min="0" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Statut</label><select name="status" class="mt-1 w-full rounded-lg border-slate-200"><option value="planned">Planifiée</option><option value="completed">Terminée</option></select></div>
        <div><label class="text-sm font-medium">Compte-rendu</label><textarea name="operative_report" rows="4" class="mt-1 w-full rounded-lg border-slate-200"></textarea></div>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Enregistrer</button>
    </form>
</x-clinic-layout>
