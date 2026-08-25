@php
    $productOptions = $products->map(fn ($p) => [
        'id' => $p->id, 'name' => $p->name, 'dci' => $p->dci,
        'price' => (float) $p->sale_price, 'stock' => $p->stock_quantity,
    ]);
@endphp
<x-clinic-layout>
    <x-slot name="header">Vente pharmaceutique</x-slot>
    <x-slot name="subheader">Recherche par nom, DCI ou code — déstockage automatique</x-slot>

    <form method="POST" action="{{ route('pharmacie.sales.store') }}"
          x-data="pharmacySale({{ $productOptions->toJson() }})" @submit="prepareSubmit"
          class="grid gap-6 lg:grid-cols-3">
        @csrf
        <input type="hidden" name="module" value="pharmacie">

        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border p-6 shadow-sm space-y-4">
                <div>
                    <label class="block text-sm font-medium">Patient</label>
                    <select name="patient_id" class="mt-1 w-full rounded-lg border-slate-200">
                        <option value="">— Comptant —</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Recherche médicament</label>
                    <input type="search" x-model="search" @input.debounce.300ms="filterProducts()" placeholder="Nom, DCI, code-barres…" class="w-full rounded-lg border-slate-200">
                </div>
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold">Panier</h3>
                    <button type="button" @click="addLine()" class="text-sm text-teal-600 font-medium">+ Ligne</button>
                </div>
                <template x-for="(line, index) in lines" :key="index">
                    <div class="grid gap-2 sm:grid-cols-12 items-end border-b pb-3">
                        <div class="sm:col-span-4">
                            <select @change="pickProduct(index, $event)" class="w-full rounded-lg border-slate-200 text-sm">
                                <option value="">— Choisir —</option>
                                <template x-for="p in filteredProducts" :key="p.id">
                                    <option :value="p.id" x-text="p.name + (p.dci ? ' ['+p.dci+']' : '') + ' — stock '+p.stock"></option>
                                </template>
                            </select>
                            <input type="hidden" :name="'items['+index+'][product_id]'" x-model="line.product_id">
                        </div>
                        <div class="sm:col-span-4">
                            <input type="text" :name="'items['+index+'][description]'" x-model="line.description" required placeholder="Description" class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="line.quantity" min="1" required class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="line.unit_price" min="0" required class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                    </div>
                </template>
            </div>
            <div class="rounded-2xl bg-white border p-6 shadow-sm">
                <template x-for="(pay, index) in payments" :key="index">
                    <div class="grid sm:grid-cols-3 gap-2 mb-2">
                        <select :name="'payments['+index+'][method]'" x-model="pay.method" class="rounded-lg border-slate-200 text-sm">
                            <option value="cash">Espèces</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="transfer">Virement</option>
                        </select>
                        <input type="number" :name="'payments['+index+'][amount]'" x-model.number="pay.amount" min="0" class="rounded-lg border-slate-200 text-sm">
                        <input type="text" :name="'payments['+index+'][reference]'" x-model="pay.reference" placeholder="Réf." class="rounded-lg border-slate-200 text-sm">
                    </div>
                </template>
            </div>
        </div>
        <div class="rounded-2xl bg-slate-900 text-white p-6 sticky top-24">
            <p class="text-slate-400 text-sm">Total</p>
            <p class="text-3xl font-bold text-teal-400" x-text="formatMoney(total)"></p>
            <input type="number" name="discount" x-model.number="discount" min="0" placeholder="Remise" class="mt-4 w-full rounded-lg bg-slate-800 border-slate-700 text-white">
            <button type="submit" class="mt-6 w-full rounded-xl bg-teal-500 py-3 font-semibold">Valider</button>
        </div>
    </form>
    <script>
        function pharmacySale(products) {
            return {
                products, filteredProducts: products, search: '',
                lines: [{ product_id: '', description: '', quantity: 1, unit_price: 0 }],
                payments: [{ method: 'cash', amount: 0, reference: '' }], discount: 0,
                get subtotal() { return this.lines.reduce((s,l)=>s+l.quantity*l.unit_price,0); },
                get total() { return Math.max(0, this.subtotal - this.discount); },
                addLine() { this.lines.push({ product_id:'', description:'', quantity:1, unit_price:0 }); },
                filterProducts() {
                    const q = this.search.toLowerCase();
                    this.filteredProducts = q ? this.products.filter(p =>
                        p.name.toLowerCase().includes(q) || (p.dci&&p.dci.toLowerCase().includes(q))) : this.products;
                },
                pickProduct(i, e) {
                    const p = this.products.find(x => x.id == e.target.value);
                    if (p) { this.lines[i].product_id=p.id; this.lines[i].description=p.name; this.lines[i].unit_price=p.price; }
                },
                formatMoney(v) { return new Intl.NumberFormat('fr-FR').format(v)+' {{ $clinic->currency }}'; },
                prepareSubmit() { if(!this.payments[0].amount) this.payments[0].amount=this.total; }
            };
        }
    </script>
</x-clinic-layout>
