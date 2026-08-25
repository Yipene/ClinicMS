<x-clinic-layout>
    <x-slot name="header">Réservation bloc opératoire</x-slot>
    <form method="POST" action="{{ route('medical.bloc.store') }}" class="max-w-xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf
        <div><label class="text-sm font-medium">Bloc *</label><select name="operating_room_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Patient (optionnel)</label><select name="patient_id" class="mt-1 w-full rounded-lg border-slate-200"><option value="">— Client externe —</option>@foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->full_name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Client externe</label><input name="external_client" class="mt-1 w-full rounded-lg border-slate-200" placeholder="Si pas de patient"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Début *</label><input type="datetime-local" name="starts_at" required class="mt-1 w-full rounded-lg border-slate-200"></div>
            <div><label class="text-sm font-medium">Fin *</label><input type="datetime-local" name="ends_at" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        </div>
        <div><label class="text-sm font-medium">Facturation</label><select name="billing_type" class="mt-1 w-full rounded-lg border-slate-200"><option value="half_day">Demi-journée</option><option value="full_day">Journée complète</option></select></div>
        <div><label class="text-sm font-medium">Convention / notes</label><textarea name="convention_notes" rows="2" class="mt-1 w-full rounded-lg border-slate-200"></textarea></div>
        <label class="flex gap-2 text-sm"><input type="checkbox" name="bill_now" value="1" checked class="rounded text-teal-600"> Facturer (si patient sélectionné)</label>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Réserver</button>
    </form>
</x-clinic-layout>
