<x-clinic-layout>
    <x-slot name="header">Sortie interne</x-slot>
    <x-slot name="subheader">Consommation interne (service, bloc, etc.)</x-slot>

    <form method="POST" action="{{ route('stock.internal.store') }}"
          class="max-w-lg space-y-4 rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Produit *</label>
            <select name="product_id" required class="mt-1 w-full rounded-lg border-slate-200">
                <option value="">— Choisir —</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                        {{ $product->name }} (stock: {{ $product->stock_quantity }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Quantité *</label>
            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Motif *</label>
            <textarea name="notes" rows="3" required class="mt-1 w-full rounded-lg border-slate-200">{{ old('notes') }}</textarea>
        </div>
        <x-btn-primary type="submit">Enregistrer la sortie</x-btn-primary>
    </form>
</x-clinic-layout>
