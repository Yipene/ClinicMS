<x-clinic-layout>
    <x-slot name="header">Inventaires</x-slot>
    <x-slot name="subheader">Historique des inventaires physiques</x-slot>

    <div class="mb-6">
        <x-btn-primary href="{{ route('stock.inventory.create') }}">+ Nouvel inventaire</x-btn-primary>
    </div>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Référence</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Responsable</th>
                    <th class="px-4 py-3">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($inventories as $inv)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $inv->reference }}</td>
                        <td class="px-4 py-3">{{ $inv->completed_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $inv->user->name }}</td>
                        <td class="px-4 py-3">{{ $inv->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Aucun inventaire.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $inventories->links() }}</div>
</x-clinic-layout>
