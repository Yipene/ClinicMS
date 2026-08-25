<x-clinic-layout>
    <x-slot name="header">{{ $patient->full_name }}</x-slot>
    <x-slot name="subheader">{{ $patient->code }}</x-slot>
    <div class="flex gap-3 mb-6">
        <x-btn-secondary href="{{ route('patients.edit', $patient) }}">Modifier</x-btn-secondary>
        <x-btn-primary href="{{ route('medical.consultations.create', ['patient_id' => $patient->id]) }}">+ Consultation</x-btn-primary>
    </div>
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border p-6 shadow-sm text-sm space-y-2">
            <p><span class="text-slate-500">Tél.</span> {{ $patient->phone ?? '—' }}</p>
            <p><span class="text-slate-500">Naissance</span> {{ $patient->birth_date?->format('d/m/Y') ?? '—' }}</p>
            <p><span class="text-slate-500">Groupe sanguin</span> {{ $patient->blood_group ?? '—' }}</p>
            <p class="text-slate-600">{{ $patient->notes }}</p>
        </div>
        <div class="rounded-2xl bg-white border p-6 shadow-sm">
            <h3 class="font-semibold mb-3">Dernières consultations</h3>
            @forelse($patient->consultations as $c)
                <a href="{{ route('medical.consultations.show', $c) }}" class="block py-2 border-b text-sm hover:text-teal-600">{{ $c->consulted_at->format('d/m/Y') }} — {{ Str::limit($c->reason, 40) }}</a>
            @empty
                <p class="text-slate-500 text-sm">Aucune consultation.</p>
            @endforelse
        </div>
    </div>
</x-clinic-layout>
