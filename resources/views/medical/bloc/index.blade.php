<x-clinic-layout>
    <x-slot name="header">Bloc opératoire — Location</x-slot>
    <x-btn-primary href="{{ route('medical.bloc.create') }}" class="mb-6">+ Réservation</x-btn-primary>
    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        @foreach($rooms as $room)
            <div class="rounded-xl border p-4 {{ $room->is_available ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50' }}">
                <p class="font-semibold">{{ $room->name }}</p>
                <p class="text-sm text-slate-600">1/2 journée : {{ format_money($room->half_day_rate) }}</p>
                <p class="text-sm text-slate-600">Journée : {{ format_money($room->full_day_rate) }}</p>
            </div>
        @endforeach
    </div>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-4 py-3">Bloc</th><th class="px-4 py-3">Période</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Montant</th></tr></thead>
            <tbody class="divide-y">
                @foreach($bookings as $b)
                    <tr><td class="px-4 py-3">{{ $b->operatingRoom->name }}</td><td class="px-4 py-3">{{ $b->starts_at->format('d/m/Y') }} → {{ $b->ends_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $b->patient?->full_name ?? $b->external_client ?? '—' }}</td><td class="px-4 py-3">{{ format_money($b->amount) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>
</x-clinic-layout>
