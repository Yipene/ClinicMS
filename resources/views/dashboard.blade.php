<x-clinic-layout>
    <x-slot name="header">Tableau de bord</x-slot>
    <x-slot name="subheader">{{ $clinic->name }} — Vue d'ensemble</x-slot>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-8">
        <x-stat-card
            label="Recette du jour"
            :value="number_format($stats['daily_revenue'], 0, ',', ' ') . ' ' . $clinic->currency"
            hint="Encaissements sur 24h"
            color="teal"
        />
        <x-stat-card
            label="Recette mensuelle"
            :value="number_format($stats['monthly_revenue'], 0, ',', ' ') . ' ' . $clinic->currency"
            hint="Depuis le début du mois"
            color="blue"
        />
        <x-stat-card
            label="Patients enregistrés"
            :value="$stats['patients_count']"
            hint="{{ $stats['consultations_today'] }} consultation(s) aujourd'hui"
            color="amber"
        />
        <x-stat-card
            label="Alertes stock"
            :value="$stats['low_stock_count']"
            hint="Produits sous seuil minimum"
            color="rose"
        />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-900">Recettes par module (24h)</h2>
                </div>
                <div class="p-6">
                    @forelse ($revenueByModule as $module => $amount)
                        @php
                            $labels = [
                                'caisse' => 'Caisse', 'pharmacie' => 'Pharmacie', 'consultation' => 'Consultations',
                                'examen' => 'Examens', 'hospitalisation' => 'Hospitalisations', 'intervention' => 'Interventions',
                                'accouchement' => 'Accouchements', 'bloc' => 'Bloc opératoire',
                            ];
                            $max = max($revenueByModule->max(), 1);
                            $pct = ($amount / $max) * 100;
                        @endphp
                        <div class="mb-4 last:mb-0">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-slate-600">{{ $labels[$module] ?? ucfirst($module) }}</span>
                                <span class="font-medium">{{ number_format($amount, 0, ',', ' ') }} {{ $clinic->currency }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-gradient-to-r from-teal-500 to-emerald-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-8">Aucune recette enregistrée aujourd'hui.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-900">Dernières transactions</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">Référence</th>
                                <th class="px-6 py-3 font-medium">Patient</th>
                                <th class="px-6 py-3 font-medium">Module</th>
                                <th class="px-6 py-3 font-medium text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentSales as $sale)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3 font-mono text-xs">{{ $sale->reference }}</td>
                                    <td class="px-6 py-3">{{ $sale->patient?->full_name ?? '—' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs">{{ $sale->module }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium">{{ number_format($sale->total, 0, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucune vente pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white shadow-lg">
                <h3 class="font-semibold text-lg">Indicateurs rapides</h3>
                <ul class="mt-4 space-y-3 text-sm text-slate-300">
                    <li class="flex justify-between"><span>Ventes aujourd'hui</span><span class="font-semibold text-white">{{ $stats['sales_today'] }}</span></li>
                    <li class="flex justify-between"><span>Lits occupés</span><span class="font-semibold text-white">{{ $stats['occupied_beds'] }}</span></li>
                    <li class="flex justify-between"><span>Produits critiques</span><span class="font-semibold text-amber-300">{{ $stats['low_stock_count'] }}</span></li>
                </ul>
            </div>

            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Stock critique</h3>
                <ul class="space-y-3">
                    @forelse ($criticalProducts as $product)
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-slate-700 truncate pr-2">{{ $product->name }}</span>
                            <span class="shrink-0 rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700">{{ $product->stock_quantity }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Aucune alerte stock.</li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-2xl border border-dashed border-teal-300 bg-teal-50/50 p-5 text-sm text-teal-900 space-y-2">
                <p class="font-semibold">Accès rapide</p>
                <div class="flex flex-wrap gap-x-4 gap-y-1">
                    @can('patients.manage')<a href="{{ route('patients.create') }}" class="text-teal-700 hover:underline">+ Patient</a>@endcan
                    @can('pharmacie.sell')<a href="{{ route('pharmacie.sales.create') }}" class="text-teal-700 hover:underline">Vente pharmacie</a>@endcan
                    @can('caisse.manage')<a href="{{ route('caisse.sales.create') }}" class="text-teal-700 hover:underline">Vente caisse</a>@endcan
                    @can('consultations.manage')<a href="{{ route('medical.consultations.create') }}" class="text-teal-700 hover:underline">Consultation</a>@endcan
                </div>
            </div>
        </div>
    </div>
</x-clinic-layout>
