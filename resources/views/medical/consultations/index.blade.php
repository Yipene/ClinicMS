<x-clinic-layout>
    <x-slot name="header">Consultations</x-slot>
    <x-btn-primary href="{{ route('medical.consultations.create') }}" class="mb-6">+ Consultation</x-btn-primary>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500"><tr><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Patient</th><th class="px-4 py-3 text-left">Médecin</th><th class="px-4 py-3 text-right">Honoraires</th><th></th></tr></thead>
            <tbody class="divide-y">
                @foreach($consultations as $c)
                    <tr><td class="px-4 py-3">{{ $c->consulted_at->format('d/m/Y') }}</td><td class="px-4 py-3">{{ $c->patient->full_name }}</td><td class="px-4 py-3">{{ $c->doctor->name }}</td><td class="px-4 py-3 text-right">{{ format_money($c->fee) }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('medical.consultations.show', $c) }}" class="text-teal-600">Voir</a></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $consultations->links() }}</div>
</x-clinic-layout>
