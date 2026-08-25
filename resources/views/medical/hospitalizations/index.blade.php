<x-clinic-layout>
    <x-slot name="header">Hospitalisations</x-slot>
    <x-slot name="subheader">{{ $occupiedBeds }} lit(s) occupé(s) — {{ $availableRooms }} chambre(s) libre(s)</x-slot>
    <x-btn-primary href="{{ route('medical.hospitalizations.create') }}" class="mb-6">+ Admission</x-btn-primary>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-4 py-3">Patient</th><th class="px-4 py-3">Chambre</th><th class="px-4 py-3">Entrée</th><th class="px-4 py-3">Statut</th><th></th></tr></thead>
            <tbody class="divide-y">
                @foreach($hospitalizations as $h)
                    <tr>
                        <td class="px-4 py-3">{{ $h->patient->full_name }}</td>
                        <td class="px-4 py-3">{{ $h->room?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $h->admitted_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $h->status }}</td>
                        <td class="px-4 py-3">
                            @if($h->status === 'admitted')
                                <form method="POST" action="{{ route('medical.hospitalizations.discharge', $h) }}" class="inline flex gap-2 items-center">
                                    @csrf
                                    <input type="number" name="discharge_fee" placeholder="Frais sortie" class="w-24 rounded border-slate-200 text-xs">
                                    <button type="submit" class="text-rose-600 text-xs font-medium">Sortie</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $hospitalizations->links() }}</div>
</x-clinic-layout>
