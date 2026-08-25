<x-clinic-layout>
    <x-slot name="header">Produits & stock</x-slot>
    <x-slot name="subheader">Gestion du catalogue et des niveaux de stock</x-slot>

    <div class="flex flex-wrap gap-3 mb-6">
        <x-btn-primary href="{{ route('stock.products.create') }}">+ Nouveau produit</x-btn-primary>
        <x-btn-secondary href="{{ route('stock.products.index', ['filter' => 'low']) }}">Alertes stock ({{ $stats['low_stock'] }})</x-btn-secondary>
        <x-btn-secondary href="{{ route('stock.export') }}">Export CSV</x-btn-secondary>
    </div>

    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        <x-stat-card label="Produits actifs" :value="$stats['total']" color="teal" />
        <x-stat-card label="Sous seuil" :value="$stats['low_stock']" color="rose" />
        <x-stat-card label="Valeur stock (PA)" :value="format_money($stats['stock_value'])" color="blue" />
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher nom, SKU, DCI…"
               class="flex-1 rounded-xl border-slate-200 shadow-sm focus:border-teal-500 focus:ring-teal-500">
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white">Filtrer</button>
    </form>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">SKU</th>
                    <th class="px-4 py-3">Produit</th>
                    <th class="px-4 py-3">PA / PV</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Marge</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-slate-50 {{ $product->isLowStock() ? 'bg-rose-50/50' : '' }}">
                        <td class="px-4 py-3 font-mono text-xs">{{ $product->sku }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $product->name }}</div>
                            @if($product->dci)<div class="text-xs text-slate-500">{{ $product->dci }}</div>@endif
                        </td>
                        <td class="px-4 py-3">{{ format_money($product->purchase_price) }} / {{ format_money($product->sale_price) }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $product->isLowStock() ? 'text-rose-600 font-semibold' : '' }}">{{ $product->stock_quantity }}</span>
                            <span class="text-slate-400 text-xs">/ min {{ $product->min_stock_level }}</span>
                        </td>
                        <td class="px-4 py-3 text-emerald-600">{{ format_money($product->margin) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('stock.products.edit', $product) }}" class="text-teal-600 hover:underline">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Aucun produit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</x-clinic-layout>
