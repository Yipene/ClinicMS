<x-clinic-layout>
    <x-slot name="header">Ventes — Pharmacie</x-slot>
    <div class="flex gap-3 mb-6">
        <x-btn-primary href="{{ route('pharmacie.sales.create') }}">+ Vente pharmaceutique</x-btn-primary>
        <x-btn-secondary href="{{ route('pharmacie.cancellations.index') }}">Annulations</x-btn-secondary>
    </div>
    <form method="GET" class="mb-4 flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Référence, patient…" class="rounded-lg border-slate-200 text-sm flex-1">
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-slate-200 text-sm">
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white">Filtrer</button>
    </form>
    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr><th class="px-4 py-3">Réf.</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Client</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3 text-right">Total</th><th></th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $sale->reference }}</td>
                        <td class="px-4 py-3">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $sale->patient?->full_name ?? $sale->customer_name ?? 'Comptoir anonyme' }}@if($sale->customer_phone)<span class="block text-xs text-slate-400">{{ $sale->customer_phone }}</span>@endif</td>
                        <td class="px-4 py-3"><span class="text-xs rounded-full bg-slate-100 px-2">{{ $sale->status }}</span></td>
                        <td class="px-4 py-3 text-right font-medium">{{ format_money($sale->total) }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('pharmacie.sales.show', $sale) }}" class="text-teal-600">Reçu</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Aucune vente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
</x-clinic-layout>
