<x-clinic-layout>
    <x-slot name="header">Admission patient</x-slot>
    <form method="POST" action="{{ route('medical.hospitalizations.store') }}" class="max-w-xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf
        <div><label class="text-sm font-medium">Patient *</label><select name="patient_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->full_name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Médecin traitant *</label><select name="doctor_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($doctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Chambre</label><select name="room_id" class="mt-1 w-full rounded-lg border-slate-200"><option value="">— Non assignée —</option>@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->name }} ({{ format_money($r->daily_rate) }}/j)</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Date admission *</label><input type="datetime-local" name="admitted_at" value="{{ now()->format('Y-m-d\TH:i') }}" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Frais d'admission</label><input type="number" name="admission_fee" min="0" class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Notes</label><textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-slate-200"></textarea></div>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Admettre</button>
    </form>
</x-clinic-layout>
