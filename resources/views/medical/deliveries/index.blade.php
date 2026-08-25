<x-clinic-layout>
    <x-slot name="header">Accouchements</x-slot>
    <x-btn-primary href="{{ route('medical.deliveries.create') }}" class="mb-6">+ Enregistrer</x-btn-primary>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Mère</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Nouveau-né</th></tr></thead>
            <tbody class="divide-y">
                @foreach($deliveries as $d)
                    <tr><td class="px-4 py-3">{{ $d->delivered_at->format('d/m/Y H:i') }}</td><td class="px-4 py-3">{{ $d->patient->full_name }}</td><td class="px-4 py-3">{{ $d->type }}</td>
                        <td class="px-4 py-3 text-xs">{{ $d->newborn_info ? json_encode($d->newborn_info) : '—' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $deliveries->links() }}</div>
</x-clinic-layout>
