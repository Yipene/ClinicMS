<x-clinic-layout>
    <x-slot name="header">Interventions chirurgicales</x-slot>
    <x-btn-primary href="{{ route('medical.surgeries.create') }}" class="mb-6">+ Planifier</x-btn-primary>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Patient</th><th class="px-4 py-3">Chirurgien</th><th class="px-4 py-3">Bloc</th><th class="px-4 py-3">Statut</th></tr></thead>
            <tbody class="divide-y">
                @foreach($surgeries as $s)
                    <tr><td class="px-4 py-3">{{ $s->scheduled_at->format('d/m/Y H:i') }}</td><td class="px-4 py-3">{{ $s->patient->full_name }}</td><td class="px-4 py-3">{{ $s->surgeon->name }}</td><td class="px-4 py-3">{{ $s->operatingRoom?->name ?? '—' }}</td><td class="px-4 py-3">{{ $s->status }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $surgeries->links() }}</div>
</x-clinic-layout>
