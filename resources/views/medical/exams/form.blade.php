<x-clinic-layout>
    <x-slot name="header">Prescrire un examen</x-slot>
    <form method="POST" action="{{ route('medical.exams.store') }}" class="max-w-xl space-y-4 rounded-2xl bg-white border p-6 shadow-sm">
        @csrf
        <div><label class="text-sm font-medium">Patient *</label><select name="patient_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($patients as $p)<option value="{{ $p->id }}" @selected(($selectedPatient?->id)==$p->id)>{{ $p->full_name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Médecin *</label><select name="doctor_id" required class="mt-1 w-full rounded-lg border-slate-200">@foreach($doctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Type</label><select name="type" class="mt-1 w-full rounded-lg border-slate-200"><option value="biology">Biologie</option><option value="imaging">Imagerie</option><option value="other">Autre</option></select></div>
        <div><label class="text-sm font-medium">Libellé *</label><input name="label" required class="mt-1 w-full rounded-lg border-slate-200" placeholder="Ex: NFS, Échographie…"></div>
        <div><label class="text-sm font-medium">Date prescription</label><input type="date" name="prescribed_at" value="{{ date('Y-m-d') }}" required class="mt-1 w-full rounded-lg border-slate-200"></div>
        <div><label class="text-sm font-medium">Tarif</label><input type="number" name="fee" value="10000" min="0" class="mt-1 w-full rounded-lg border-slate-200"></div>
        <label class="flex gap-2 text-sm"><input type="checkbox" name="bill_now" value="1" checked class="rounded text-teal-600"> Facturer</label>
        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm text-white">Prescrire</button>
    </form>
</x-clinic-layout>
