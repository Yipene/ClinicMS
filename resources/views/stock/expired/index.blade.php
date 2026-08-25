<x-clinic-layout>
    <x-slot name="header">Produits à péremption</x-slot>
    <x-slot name="subheader">Lots expirant dans les 90 prochains jours</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-teal-50 border border-teal-200 px-4 py-3 text-sm text-teal-900">{{ session('status') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Produit</th>
                    <th class="px-4 py-3">Expiration</th>
                    <th class="px-4 py-3">Stock actuel</th>
                    <th class="px-4 py-3">Retrait</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($expiring as $movement)
                    @php $product = $movement->product; @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3 {{ $movement->expiry_date->isPast() ? 'text-rose-600 font-medium' : '' }}">
                            {{ $movement->expiry_date->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('stock.expired.destroy', $product) }}" class="flex gap-2 items-center">
                                @csrf
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-20 rounded-lg border-slate-200 text-sm">
                                <button type="submit" class="text-sm text-rose-600 hover:underline">Retirer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Aucun lot proche de la péremption.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-clinic-layout>
