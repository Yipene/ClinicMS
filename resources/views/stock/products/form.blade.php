@php $isEdit = $product->exists; @endphp
<x-clinic-layout>
    <x-slot name="header">{{ $isEdit ? 'Modifier le produit' : 'Nouveau produit' }}</x-slot>

    <form method="POST" action="{{ $isEdit ? route('stock.products.update', $product) : route('stock.products.store') }}"
          class="max-w-2xl space-y-4 rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">SKU *</label>
                <input name="sku" value="{{ old('sku', $product->sku) }}" required class="mt-1 w-full rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Code-barres</label>
                <input name="barcode" value="{{ old('barcode', $product->barcode) }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Nom *</label>
            <input name="name" value="{{ old('name', $product->name) }}" required class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">DCI</label>
            <input name="dci" value="{{ old('dci', $product->dci) }}" class="mt-1 w-full rounded-lg border-slate-200">
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-slate-700">Catégorie</label>
                <select name="category" class="mt-1 w-full rounded-lg border-slate-200">
                    @foreach(['medicament','consommable','materiel'] as $cat)
                        <option value="{{ $cat }}" @selected(old('category', $product->category) === $cat)>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Unité</label>
                <input name="unit" value="{{ old('unit', $product->unit ?? 'unité') }}" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Seuil min.</label>
                <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level ?? 10) }}" min="0" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Prix d'achat (PA) *</label>
                <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" step="1" required class="mt-1 w-full rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Prix de vente (PV) *</label>
                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="1" required class="mt-1 w-full rounded-lg border-slate-200">
            </div>
        </div>
        @if(! $isEdit)
            <div>
                <label class="block text-sm font-medium text-slate-700">Stock initial</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" class="mt-1 w-full rounded-lg border-slate-200">
            </div>
        @endif
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-slate-300 text-teal-600">
            <span class="text-sm">Produit actif</span>
        </label>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-teal-700">Enregistrer</button>
            <x-btn-secondary href="{{ route('stock.products.index') }}">Annuler</x-btn-secondary>
        </div>
    </form>
</x-clinic-layout>
