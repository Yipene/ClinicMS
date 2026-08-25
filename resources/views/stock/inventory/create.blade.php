<x-clinic-layout>
    <x-slot name="header">Inventaire physique</x-slot>
    <x-slot name="subheader">Saisissez les quantités comptées — les écarts seront ajustés automatiquement</x-slot>

    <form method="POST" action="{{ route('stock.inventory.store') }}" class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Notes</label>
            <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-slate-200">{{ old('notes') }}</textarea>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-3 py-2">Produit</th>
                    <th class="px-3 py-2">Stock système</th>
                    <th class="px-3 py-2">Quantité comptée</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($products as $i => $product)
                    <tr>
                        <td class="px-3 py-3">
                            {{ $product->name }}
                            <input type="hidden" name="counts[{{ $i }}][product_id]" value="{{ $product->id }}">
                        </td>
                        <td class="px-3 py-3 text-slate-500">{{ $product->stock_quantity }}</td>
                        <td class="px-3 py-3">
                            <input type="number" name="counts[{{ $i }}][counted_quantity]" min="0"
                                   value="{{ old("counts.{$i}.counted_quantity", $product->stock_quantity) }}"
                                   class="w-24 rounded-lg border-slate-200">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="mt-6 rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-teal-700">
            Valider l'inventaire
        </button>
    </form>
</x-clinic-layout>
