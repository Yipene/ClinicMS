<x-clinic-layout>
    <x-slot name="header">Ventes — Caisse</x-slot>
    <x-slot name="subheader">Historique des transactions</x-slot>

    <div class="flex flex-wrap gap-3 mb-6">
        <x-btn-primary href="{{ route('caisse.sales.create') }}">+ Nouvelle vente</x-btn-primary>
        <x-btn-secondary href="{{ route('caisse.daily') }}">Recette du jour</x-btn-secondary>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Référence ou patient…" class="rounded-lg border-slate-200 text-sm">
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-slate-200 text-sm">
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white">Filtrer</button>
    </form>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Référence</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Patient</th>
                    <th class="px-4 py-3">Module</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $sale->reference }}</td>
                        <td class="px-4 py-3">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $sale->patient?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 text-xs">{{ $sale->module }}</span></td>
                        <td class="px-4 py-3 text-right font-semibold">{{ format_money($sale->total) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('caisse.sales.show', $sale) }}" class="text-teal-600 hover:underline">Reçu</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Aucune vente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
</x-clinic-layout>
