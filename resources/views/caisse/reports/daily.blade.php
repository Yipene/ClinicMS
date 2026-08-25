<x-clinic-layout>
    <x-slot name="header">Recette journalière (24h)</x-slot>
    <x-slot name="subheader">{{ $day->translatedFormat('l d F Y') }}</x-slot>

    <div class="flex flex-wrap gap-3 mb-6">
        <form method="GET" class="flex gap-2">
            <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-slate-200 text-sm">
            <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white">Voir</button>
        </form>
        <x-btn-secondary href="{{ route('caisse.daily.export', ['date' => $date]) }}">Export CSV</x-btn-secondary>
        <x-btn-secondary href="{{ route('caisse.daily.pdf', ['date' => $date]) }}">Télécharger PDF</x-btn-secondary>
        <button onclick="window.print()" class="rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50 print:hidden">Imprimer</button>
    </div>

    @if($anomaly)
        <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
            ⚠ Alerte : recette du jour ({{ format_money($totalRevenue) }}) inférieure au seuil configuré ({{ format_money($threshold) }}).
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        <x-stat-card label="Recette totale" :value="format_money($totalRevenue)" color="teal" />
        <x-stat-card label="Nombre de ventes" :value="$sales->count()" color="blue" />
        <x-stat-card label="Panier moyen" :value="format_money($sales->count() ? $totalRevenue / $sales->count() : 0)" color="amber" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-6">
        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
            <h3 class="font-semibold mb-4">Par module</h3>
            @forelse($byModule as $module => $amount)
                <div class="flex justify-between text-sm py-2 border-b border-slate-50">
                    <span>{{ $moduleLabels[$module] ?? $module }}</span>
                    <span class="font-medium">{{ format_money($amount) }}</span>
                </div>
            @empty
                <p class="text-slate-500 text-sm">Aucune recette.</p>
            @endforelse
        </div>
        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
            <h3 class="font-semibold mb-4">Par mode de paiement</h3>
            @forelse($byPayment as $method => $amount)
                <div class="flex justify-between text-sm py-2 border-b border-slate-50">
                    <span>{{ $paymentLabels[$method] ?? $method }}</span>
                    <span class="font-medium">{{ format_money($amount) }}</span>
                </div>
            @empty
                <p class="text-slate-500 text-sm">—</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3">Réf.</th>
                    <th class="px-4 py-3">Heure</th>
                    <th class="px-4 py-3">Patient</th>
                    <th class="px-4 py-3">Module</th>
                    <th class="px-4 py-3 text-right">Montant</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($sales as $sale)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $sale->reference }}</td>
                        <td class="px-4 py-3">{{ $sale->created_at->format('H:i') }}</td>
                        <td class="px-4 py-3">{{ $sale->patient?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $moduleLabels[$sale->module] ?? $sale->module }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ format_money($sale->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 font-semibold">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right">TOTAL</td>
                    <td class="px-4 py-3 text-right">{{ format_money($totalRevenue) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-clinic-layout>
