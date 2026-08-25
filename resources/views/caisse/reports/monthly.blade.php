<x-clinic-layout>
    <x-slot name="header">Recette mensuelle</x-slot>
    <x-slot name="subheader">{{ $start->translatedFormat('F Y') }}</x-slot>

    <div class="flex flex-wrap gap-3 mb-6">
        <form method="GET" class="flex gap-2">
            <input type="month" name="month" value="{{ $month }}" class="rounded-lg border-slate-200 text-sm">
            <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white">Voir</button>
        </form>
        <x-btn-secondary href="{{ route('caisse.monthly.export', ['month' => $month]) }}">Export CSV</x-btn-secondary>
        <x-btn-secondary href="{{ route('caisse.monthly.pdf', ['month' => $month]) }}">Télécharger PDF</x-btn-secondary>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
        <x-stat-card label="Chiffre d'affaires" :value="format_money($currentTotal)" color="teal" />
        <x-stat-card label="Mois précédent" :value="format_money($previousTotal)" color="blue" />
        <x-stat-card
            label="Évolution"
            :value="$trend !== null ? ($trend >= 0 ? '+' : '') . $trend . ' %' : 'N/A'"
            :hint="$trend !== null ? 'vs mois précédent' : null"
            :color="$trend >= 0 ? 'teal' : 'rose'"
        />
        <x-stat-card label="Bénéfice net estimé" :value="format_money($netBenefit)" hint="CA − achats stock" color="amber" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-6">
        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
            <h3 class="font-semibold mb-4">Évolution quotidienne</h3>
            @php $maxDay = $dailyChart->max('revenue') ?: 1; @endphp
            @forelse($dailyChart as $row)
                @php $pct = ($row->revenue / $maxDay) * 100; @endphp
                <div class="mb-2">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>{{ \Carbon\Carbon::parse($row->day)->format('d/m') }}</span>
                        <span>{{ format_money($row->revenue) }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100">
                        <div class="h-2 rounded-full bg-teal-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 text-sm">Aucune donnée.</p>
            @endforelse
        </div>
        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
            <h3 class="font-semibold mb-4">Répartition par module</h3>
            @foreach($byModule as $row)
                <div class="flex justify-between text-sm py-2 border-b border-slate-50">
                    <span>{{ $moduleLabels[$row->module] ?? $row->module }} ({{ $row->count }})</span>
                    <span class="font-medium">{{ format_money($row->revenue) }}</span>
                </div>
            @endforeach
            <div class="mt-4 pt-4 border-t text-sm flex justify-between">
                <span class="text-slate-500">Coût achats (stock)</span>
                <span class="text-rose-600">{{ format_money($purchaseCost) }}</span>
            </div>
        </div>
    </div>
</x-clinic-layout>
