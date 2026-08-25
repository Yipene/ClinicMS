<x-clinic-layout>
    <x-slot name="header">Nouveau bon de commande</x-slot>

    <form method="POST" action="{{ route('stock.purchase-orders.store') }}" x-data="{ lines: [{}, {}], addLine() { this.lines.push({}) } }" class="space-y-6">
        @csrf
        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm max-w-3xl space-y-4">
            <div>
                <label class="block text-sm font-medium">Fournisseur *</label>
                <select name="supplier_id" required class="mt-1 w-full rounded-lg border-slate-200">
                    <option value="">— Choisir —</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Date livraison prévue</label>
                <input type="date" name="expected_at" value="{{ old('expected_at') }}" class="mt-1 rounded-lg border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium">Notes</label>
                <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-slate-200">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Lignes de commande</h3>
                <button type="button" @click="addLine()" class="text-sm text-teal-600 font-medium">+ Ajouter une ligne</button>
            </div>
            <template x-for="(line, index) in lines" :key="index">
                <div class="grid gap-3 sm:grid-cols-4 mb-3 pb-3 border-b border-slate-100">
                    <div class="sm:col-span-2">
                        <select :name="'items['+index+'][product_id]'" required class="w-full rounded-lg border-slate-200 text-sm">
                            <option value="">Produit</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="number" :name="'items['+index+'][quantity]'" placeholder="Qté" min="1" required class="w-full rounded-lg border-slate-200 text-sm">
                    </div>
                    <div>
                        <input type="number" :name="'items['+index+'][unit_price]'" placeholder="PA unitaire" min="0" step="1" required class="w-full rounded-lg border-slate-200 text-sm">
                    </div>
                </div>
            </template>
        </div>

        <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-teal-700">Créer le bon</button>
    </form>
</x-clinic-layout>
